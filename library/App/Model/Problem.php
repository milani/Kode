<?php

/**
 * Manages the user groups in the application
 *
 * @package admin_models
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class Problem extends App_Model {

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
    protected $_name = 'problems';

    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Problem';
    
    
    protected $_dependentTables = array('ProblemClass');
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var string
     * @access protected
     */
    protected $_displayColumn = 'problem_number';
    
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
        
        $preId = (isset($data['id']))?$data['id']:null;
        
        $problemAttachModel = new ProblemAttachment();
        
        if(isset($data['fork']) && $data['fork']){
            //force model to create new problem
            unset($data['id']);
            $id = parent::save($data);
            
            //copy attachments for copied problem
            $problemAttachModel->copy($preId,$id);
            
        }else{
            $id = parent::save($data);
            //save new attachments
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
                    $problemAttachModel->save(
                        array(
                            'problem_id'	=> $id,
                            'file_name'	    => $files[$field]['name'],
                            'file_unique'	=> $filename,
                            'file_path'		=> UPLOAD_PATH.DIRECTORY_SEPARATOR.$filename,
                            'file_mime'		=> $files[$field]['type']
                        )
                    );
                }
            }elseif(is_string($data['attachment'])){
                foreach($files as $fileInfo){
                    if(!empty($fileInfo['name'])){
                        $problemAttachModel->save(
                            array(
                                'problem_id'	=> $id,
                                'file_name'	    => $fileInfo['name'],
                                'file_unique'	=> $data['attachment'],
                                'file_path'		=> UPLOAD_PATH.DIRECTORY_SEPARATOR.$data['attachment'],
                            	'file_mime'		=> $fileInfo['type']
                            )
                        );
                        break;
                    }
                }
            }
            
        }
        
        if( ! is_array($data['class_id'])){
            $data['class_id'] = array($data['class_id']);
        }
        
        $problemClassModel = new ProblemClass();
        $problemClassModel->saveForProblem($data,$id,$preId);
        
        return $id;
    }
    
    public function deleteById($id,$classId){
        $problemClassModel = new ProblemClass();
        $problemClassModel->deleteForProblem($id,$classId);
        if( count($problemClassModel->findByProblem($id)) == 0){
            //delete attachments
            $problemAttach = new ProblemAttachment();
            $problemAttach->deleteByProblemId($id);
            //delete problem
            parent::deleteById($id);
        }
    }
    
    public function deleteByAssignmentClass($assignmentId,$classId){
        $rows = $this->findByAssignmentClass($assignmentId, $classId);
        foreach($rows as $row){
            $this->deleteById($row['id'],$classId);
        }
    }
    
    public function copy($data){
        $classes = $data['class_id'];
        $id = $data['id'];
        $assignmentClassModel = new AssignmentClass();
        $row = $assignmentClassModel->findById($id);
        foreach($classes as $classId){
            $row['class_id'] = $classId;
            $assignmentClassModel->insert($row);
        }
        
    }
    
    public function copyToAssignment($fromId,$toId,$fromClass,$toClass){
        $select = $this->_getSelect();
        $select->where('p.assignment_id = ?',$fromId);
        $select->where('c.class_id = ?',$fromClass);
        $rows = $this->_db->fetchAll($select);
        foreach($rows as $row){
            $row['assignment_id'] = $toId;
            $row['class_id'] = $toClass;
            $row['fork'] = 1;
            $this->save($row);
        }
    }
    
    public function findByAssignmentClass($assignmentId,$classId,$page = 1, $paginate = NULL){

        $select = $this->_getSelect(false);
        $select->joinLeft(
            array(
                's'	=>    'submissions'
            ),
            's.problem_id = p.id',
            'COUNT(s.id) AS submission_count'
        );
        $select->joinLeft(
            array(
                'sg'	=> 'submission_grade'
            ),
            'sg.submission_id = s.id',
            'COUNT(sg.submission_id) AS submission_grade_count'
        );
        $select->group('p.id');
        $select->where('p.assignment_id = ?',$assignmentId);
        $select->where('c.class_id = ?',$classId);
        
        return $this->_paginate($this->_prepareContent($select), $page, $paginate);
    }
    
    public function findByAnswered($userId,$page = 1,$paginate = NULL){
        
        $select = $this->_getSelect(false);
        $select->joinLeft(
            array(
                's' =>'submissions'
            ) , 's.problem_id = p.id'
        );
        $select->where('s.user_id = ?',$userId);
        
        return $this->_paginate($this->_prepareContent($select), $page, $paginate);
    }
    
    public function findByIdClass($id,$classId){
        $select = $this->_getSelect(false);
        $select->where('p.id = ?',$id);
        $select->where('c.class_id = ?',$classId);
        return $this->_db->fetchRow($select);
    }
    
    public function findRelatedClasses($id){
        $select = $this->_getSelect();
        $select->joinLeft(
            array(
                'cl' => 'class',
            ),
            'c.class_id = cl.id'
        );
        $select->joinLeft(
            array(
                'co' => 'course',
            ),
            'cl.course_id = co.id'
        );
        $select->where('p.id = ?',$id);
        $select->columns(
            array(
            	'p.*', 'c.*','CONCAT(cl.class_name," - ",co.course_name) AS class_name'
            )
        );
        
        return $this->_db->fetchAll($select);
    }
    
    public function archive($problemId,$classId){
        
        $zip = new ZipArchive();
        $assignmentModel = new Assignment();
        $viewPartial = new Zend_View_Helper_Partial();
        
        $problem = $this->findByIdClass($problemId, $classId);
        $assignment = $assignmentModel->findByIdClass($problem['assignment_id'], $classId);
        
        $zip->open('/tmp/problem_'.$problem['problem_number'].'.zip',ZipArchive::CREATE);
        
        $attachments[$problemId] = $this->archiveAttachments($zip, $problemId, $problem['problem_number']);
        
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewPartial->setView($viewRenderer->view);
        $content = $viewPartial->partial('partials/print-problem.phtml',
            array(
                'problems'	    => array($problem),
                'assignment'	=> $assignment,
                'attachments'	=> $attachments
            )
        );
        
        $zip->addFromString('problem.html', $content);
        $zip->close();
        
        return '/tmp/problem_'.$problem['problem_number'].'.zip';
    }
    
    public function archiveAttachments(&$zip,$problemId,$problemNumber){
        $attachmentModel = new ProblemAttachment();
        $attachments = $attachmentModel->findByProblemId($problemId);
        
        foreach($attachments as $attachment){
            $zip->addFile(APPLICATION_PATH.DIRECTORY_SEPARATOR.$attachment['file_path'],$problemNumber.'/'.$attachment['file_name']);
        }
        return $attachments;
    }
    
    private function _prepareContent($select){
        
        $rows = $this->_db->fetchAll($select);
        $dom = new DOMDocument();
        
        for($i = 0,$len = count($rows);$i < $len;$i++){
            $tmpText = strip_tags($rows[$i]['problem_desc']);
            if(strlen($tmpText) > 360){
                $tmpText .= ' ';
                $tmpText = substr($tmpText,0,strpos($tmpText,' ',360));
                $tmpText .= '...';
            }
            $rows[$i]['problem_desc'] = $tmpText;
        }
        return $rows;
    }
    
    protected function _select(){

        $select = new Zend_Db_Select($this->_db);
        $select->from(
            array(
            	'p' => $this->_name,
            )
        );
        $select->where('c.problem_id = p.id');
        $select->join(
            array(
            	'c' => 'problem_class'
            ),
            'p.id = c.problem_id'
        );
        $select->order(array('p.id ASC'));
        $select->reset(Zend_Db_Table::COLUMNS);
        //SELECT `p`.*,c.class_id, (@num := @num +1) AS `problem_number` FROM `problems` AS `p`,(SELECT @num:=0) as `d`, problem_class AS c WHERE c.problem_id = p.id AND p.assignment_id = 2;
        $select->columns(
            array(
            	'p.*', 'c.*','(SELECT COUNT(*) FROM problems p2 where p2.id <= p.id AND p2.assignment_id = p.assignment_id AND (p.id = c.problem_id OR p2.id = c.problem_id)) AS problem_number'
            )
        );
        
        return $select;
    }
}
