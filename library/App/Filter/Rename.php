<?php
class App_Filter_Rename extends Zend_Filter_File_Rename{
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

        if (!isset($rename['target']) or ($rename['target'] == '*')) {
            $rename['target'] = $rename['source'];
        }
        $fileName = basename($rename['source']);
        $name = md5(uniqid().uniqid());
        $rename['target'] = $name;
        return $rename;
    }
}