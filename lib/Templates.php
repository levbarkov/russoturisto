<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class Templates {
    private $path;
    
    public function __construct($path){
        $this->path = $path;
    }
        
    public function getTemplate($tpl){
        if(!file_exists($this->path . $tpl)){
            echo 'Шаблон не найден';
            return false;
        }
        
        include_once($this->path . $tpl);
    } 
}
