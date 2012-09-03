<?php
class App_Filter_Rename extends Zend_Filter_File_Rename{

    /**
     * Returns only the new filename without moving it
     * But existing files will be erased when the overwrite option is true
     *
     * @param  string  $value  Full path of file to change
     * @param  boolean $source Return internal informations
     * @return string The new filename which has been set
     */
    public function getNewName($value, $source = false)
    {
        $file = $this->_getFileName($value);

        if ($file['source'] == $file['target']) {
            return $value;
        }

        if (($file['overwrite'] == true) && (file_exists($file['target']))) {
            unlink($file['target']);
        }

        if (file_exists($file['target'])) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception(sprintf("File '%s' could not be renamed. It already exists.", $value));
        }

        if ($source) {
            return $file;
        }

        return $file['target'];
    }

    protected function _getFileName($file)
    {

        $rename = array();
        foreach ($this->_files as $value) {
            if ($value['source'] == '*') {
                if (!isset($rename['source'])) {
                    $rename           = $value;
                    $rename['source'] = $file;
                }
            }

            if ($value['source'] == $file) {
                $rename = $value;
            }

        }

        if (!isset($rename['source'])) {
            return $file;
        }

        $fileName = basename($rename['source']);
        $name = md5(uniqid().uniqid());
        $rename['target'] = $name;

        return $rename;
    }
}
