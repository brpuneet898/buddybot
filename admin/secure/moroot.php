<?php

namespace MetagaussOpenAI\Admin\Secure;

class MoRoot extends \MetagaussOpenAI\Admin\MoRoot
{
    protected $errors = array();
    protected $sql;
    
    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $file_path = $this->config->getRootPath() . 'admin/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\MetagaussOpenAI\Admin\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance(); 
        }
    }
}