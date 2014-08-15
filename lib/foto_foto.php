<?php
/* 
 * используется для работы с фотографией
 */

/**
 * Description of filter
 */
class foto_foto {
	var $row;
	var $table_name;
	
	function __construct ($id=0, $table_foto=""){
		if (  $id>0  and  $table_foto!=''  )  $this->load ($id, $table_foto);
	}
	function load ($id, $table_foto){
		$this->row = ggo ($id, $table_foto);
		$this->table_name = $table_foto;
	}
	function get_foto_dir(){
		if 	 	(  $this->table_name=='#__exgood_foto'  )		return "/images/ex/good";
		else if	(  $this->table_name=='#__excat_foto'  ) 		return "/images/ex/cat";
		else if	(  $this->table_name=='#__content_foto'  ) 	return "/images/icat/icont";
		else if	(  $this->table_name=='#__icat_foto'  ) 		return "/images/icat/icat";
		else if	(  $this->table_name=='#__exfoto_foto'  )		return "/images/foto";
	}
	function get_parent_name(){
		if 	 	(  $this->table_name=='#__exgood_foto'  )		return "exgood_id";
		else if	(  $this->table_name=='#__excat_foto'  ) 		return "excat_id";
		else if	(  $this->table_name=='#__content_foto'  ) 		return "content_id";
		else if	(  $this->table_name=='#__icat_foto'  ) 		return "icat_id";
		else if	(  $this->table_name=='#__exfoto_foto'  )		return "exfoto_id";
	}
	// функция походу больше нигде не используется
	function makecopy ( $new_parent ){
		global $reg;
		$new_foto_types = "";
		$foto_types = array("small", "org", "full"); 
		foreach (  $foto_types as $foto_type){
			if(  $this->row->$foto_type!=''  ){
				$file = site_path.$this->get_foto_dir()."/".$this->row->$foto_type;	// ggtr ($file);
				if(  file_exists($file)  ){
					$path_parts = pathinfo( $file );
					$file_ext = $path_parts['extension'];
					$newfile = site_path.$this->get_foto_dir()."/".$this->row->$foto_type.".".$file_ext;	// ggtr ($newfile);
					if (!copy($file, $newfile)) ggd ( "ОШИБКА! Не удалось скопировать $file..." );
					$new_foto_types->$foto_type = $this->row->$foto_type.".".$file_ext;
				}
			}
		}
		$parent_name = $this->get_parent_name();
		$i24r = new mosDBTable( $this->table_name, "id", $reg['db'] );
		$i24r->id = 0;
		$i24r->name = $this->row->name;
		$i24r->order = $this->row->order;
		$i24r->$parent_name = $new_parent;
		foreach (  $foto_types as $foto_type){
			if(  $this->row->$foto_type!=''  ) $i24r->$foto_type = $new_foto_types->$foto_type;
		}
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		return $i24r;
	}
	
	

}
?>
