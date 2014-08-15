<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
class exfoto{ 
	var $id;
	var $row;
	function __construct ($id=0){
		if (  $id>0  )  $this->load ($id);
	}
	function load ($id){
		$this->row = ggo ($id, "#__exfoto");
	}
	function update_goods (){
		global $database;
		$i24r = new mosDBTable( "#__exfoto", "id", $database );
		$i24r->id = $this->id;
		$i24r->goods = ggsqlr ( "select count(id) from #__exfoto_foto where exfoto_id=$this->id " );
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		return ;
	}

	
}
class exfoto_foto{ 
	var $id;
	var $row;
	function __construct ($id=0){
		if (  $id>0  )  $this->load ($id);
	}
	function load ($id){
		$this->row = ggo ($id, "#__exfoto_foto");
	}
}
?>