<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Submission extends App_Model {

    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = 'id';

    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'submissions';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Submission';
    
    protected $_dependentTables = array('SubmissionAttach','SubmissionGrade');
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = null;
    
	protected $_csvColumns = array('ID','Submit Date','Grade','Anything else?');
	
	/**
     * Receives an array of data that needs to be saved
     * into the database. If the primary key is contained in
     * this array, it will do an update, otherwise, it will do
     * an insert
     *
     * It returns the primary key of the inserted / updated row
     *
     * @param array $data 
     * @access public
     * @return int
     */
    public function save(array $data){
        
        $id = parent::save($data);
        
        $attachModel = new SubmissionAttachment();
        
        $files = array();
        foreach ($_FILES as $form => $content) {
                foreach ($content as $param => $file) {
                    foreach ($file as $number => $target) {
                        $files[$form . '_' . $number . '_'][$param]      = $target;
                    }
                }
        }
        if(is_array($data['attachment'])){
            foreach($data['attachment'] as $field => $filename){
                $attachModel->save(
                    array(
                        'submission_id'	        => $id,
                        'submission_file_name'	    => $files[$field]['name'],
                        'submission_file_unique'	=> $filename,
                        'submission_file_path'		=> UPLOAD_PATH.DIRECTORY_SEPARATOR.$filename,
                        'submission_file_mime'		=> $files[$field]['type']
                    )
                );
            }
        }elseif(is_string($data['attachment'])){
            foreach($files as $fileInfo){
                if(!empty($fileInfo['name'])){
                    $attachModel->save(
                        array(
                            'submission_id'	        => $id,
                            'submission_file_name'	    => $fileInfo['name'],
                            'submission_file_unique'	=> $data['attachment'],
                            'submission_file_path'		=> UPLOAD_PATH.DIRECTORY_SEPARATOR.$data['attachment'],
                        	'submission_file_mime'		=> $fileInfo['type']
                        )
                    );
                    break;
                }
            }
            
        }
        return $id;
    }
    
    public function findByProblemId($problemId){
        $select = $this->_getSelect();
        $select->where('s.problem_id = ?',$problemId);
        return $this->_db->fetchRow($select);
    }
    
    public function findByProblemClass( $problemId, $classId, $page = 1, $paginate = NULL ){
        $select = $this->_getSelect();
        $select->joinLeft(
            array(
                'sa'	=> 'submission_attach'
            ),
            'sa.submission_id = s.id',
            'COUNT(sa.id) AS attachment_count'
        );
        $select->group('s.id');
        $select->where('s.problem_id = ?',$problemId);
        $select->where('u.class_id = ?',$classId);
        $select->order('m.grade ASC');
        return $this->_paginate($this->_prepareContent($select), $page, $paginate);
        
    }
    //assignmentId is added for security reasons
    public function canAnswer($problemId,$classId,$assignmentId){
        $select = new Zend_Db_Select($this->_db);
        $select->from(
            array(
                'p'	=> 'problems'
            )
        );
        $select->join(
            array(
                'a'	=> 'assignments'
            ),
            'p.assignment_id = a.id'
        );
        $select->join(
            array(
                'ca'	=> 'assignment_class'            
            ),
            'ca.assignment_id = a.id'
        );
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(
            array('a.*','ca.*')
        );
        $select->where('ca.class_id = ?',$classId);
        $select->where('p.id = ?',$problemId);
        $select->where('ca.assignment_id = ?',$assignmentId);
        
        $row = $this->_db->fetchRow($select);
        if($row == null){
            return false;
        }
        
        $date = new App_Date($row['assignment_end_at']);
        if($date->isLater(time())){
            return true;
        }
        return false;
    }
    
    public function deleteById($id){
        $attachModel = new SubmissionAttachment();
        $attachModel->delete('submission_id = '.$id);
        return $this->delete('id = '.$id);
    }
    
    public function deleteByIdUser($submissionId,$userId){
        $submissionId = $this->_db->quote($submissionId,Zend_Db::INT_TYPE);
        $userId = $this->_db->quote($userId,Zend_Db::INT_TYPE);
        $attachModel = new SubmissionAttachment();
        $attachModel->delete('submission_id = '.$submissionId);
        return $this->delete(' id = ' . $submissionId . ' AND user_id = '.$userId);
    }
    
    public function batchGrade($fileName,$userId){
        $fileHandle = fopen($fileName,'r');
        if($fileHandle === false){
            return false;
        }
        //reads column data. ignore them.
        fgetcsv($fileHandle,20000,",");
        
        $submissionGradeModel = new SubmissionGrade();
        $notSavedIds = array();
        
        while(($data = fgetcsv($fileHandle,2000,",")) !== FALSE){
            while(count($data) < count($this->_csvColumns)){
                array_push($data, '');
            }
            $row = array(
                'submission_id'	=> $data[0],
                'admin_user_id'	=> $userId,
                'grade'			=> $data[2],
                'grade_desc'	=> $data[3]
            );
            
            if(!$submissionGradeModel->autoGrade($row)){
                $notSavedIds[] = $data[0];
            }
        }
        return $notSavedIds;
    }
    
    public function archiveByProblemId($problemId, $classId){
        
        $zip = new ZipArchive();
        $viewPartial = new Zend_View_Helper_Partial();
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewPartial->setView($viewRenderer->view);
        
        $submissions = $this->findByProblemClass($problemId, $classId,1,false);
        if(count($submissions) > 0){
            $fileName = '/tmp/problem_submissions.zip';
            $csvFileName = '/tmp/submission.csv';
            $zip->open($fileName,ZipArchive::CREATE);
            $csvFile = fopen($csvFileName, 'w');
            fputcsv($csvFile,$this->_csvColumns,',','"');
            $descs = array();
            $attachments = array();
            $date = new App_Date();
            foreach($submissions as $submission){
                $attachments[$submission['id']] = $this->archiveAttachments($zip, $submission['id']);
                $date->set($submission['submission_at']);
                fputcsv($csvFile,
                        array(
                            $submission['id'],
                            $date->toJalali('d M Y، ساعت H:i')
                        ),
                        ',',
                        '"'
                );
                
                $descs[] = array(
                    'id'	=> $submission['id'],
                    'submission_desc'	=> $submission['submission_desc']
                );
            }
            fclose($csvFile);
            
            $content = $viewPartial->partial('partials/print-submission.phtml',
                array(
                    'submissions'	=> $descs,
                    'attachments'	=> $attachments
                )
            );
            $zip->addFromString('submissions.html', $content);
            $zip->addFile($csvFileName,'submissions.csv');
            $zip->close();
            return $fileName;
        }else{
            return false;
        }
    }
    
    public function archive($submissionId){
        
        $zip = new ZipArchive();
        $viewPartial = new Zend_View_Helper_Partial();
        
        $submission = $this->findById($submissionId);
        if(is_array($submission)){
            $fileName = '/tmp/submission_'.$submissionId.'.zip';
            $zip->open($fileName,ZipArchive::CREATE);
            
            $attachments[$submissionId] = $this->archiveAttachments($zip, $submissionId);
            
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $viewPartial->setView($viewRenderer->view);
            $content = $viewPartial->partial('partials/print-submission.phtml',
                array(
                    'submissions'	=> array($submission),
                    'attachments'	=> $attachments
                )
            );
            $zip->addFromString('submission.html', $content);
            $zip->close();
            
            return $fileName;
        }else{
            return false;
        }
    }
    
    public function archiveAttachments(&$zip,$submissionId){
        $attachmentModel = new SubmissionAttachment();
        $attachments = $attachmentModel->findBySubmissionId($submissionId);
        
        foreach($attachments as $attachment){
            $zip->addFile(APPLICATION_PATH.DIRECTORY_SEPARATOR.$attachment['submission_file_path'],$submissionId.'/'.$attachment['submission_file_name']);
        }
        return $attachments;
    }
    
    private function _csvCompatible($str){
        if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
            $str = "'$str";
        }
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
        return $str;
    }
    
    private function _prepareContent($select){
        
        $rows = $this->_db->fetchAll($select);
        $dom = new DOMDocument();
        
        for($i = 0,$len = count($rows);$i < $len;$i++){
            $tmpText = strip_tags($rows[$i]['submission_desc']);
            if(strlen($tmpText) > 260){
                $tmpText .= ' ';
                $tmpText = substr($tmpText,0,strpos($tmpText,' ',260));
                $tmpText .= '...';
            }
            $rows[$i]['submission_desc'] = $tmpText;
        }
        return $rows;
    }
    
    protected function _select(){

        $select = new Zend_Db_Select($this->_db);
        $select->from(
            array(
            	's' => $this->_name,
            )
        );
        $select->joinLeft(
            array(
            	'm' => 'submission_grade'
            ),
            's.id = m.submission_id'
        );
        $select->join(
            array(
                'u'	=> 'users'
            ),
            'u.id = s.user_id'
        );
        $select->order(array('s.submission_at ASC'));
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(
            array(
            	's.*', 'm.*','u.username'
            )
        );
        
        return $select;
    }
}
