<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
class icat{ 
	var $id;
	var $row;
	function __construct ($id=0){
		if (  $id>0  )  $this->load ($id);
	}
	function load ($id){
		$this->row = ggo ($id, "#__icat");
	}
	function sefnamefull (){
		$thisfotocat = ggo($this->row->parent, "#__icat"); // ggtr ($thisfotocat);
		$icatway = array(); $iii = 0;
		$icatway[0]->name = ($thisfotocat->name); $icatway[0]->parent = $thisfotocat->parent; $icatway[0]->sefname = $thisfotocat->sefname;
		if (  $thisfotocat->id==0  ) return "";
		while (  $icatway[$iii]->parent!=0  ){
			$icur_catfoto = ggo($icatway[$iii]->parent, "#__icat"); $iii++;
			$icatway[$iii]->name = ($icur_catfoto->name); $icatway[$iii]->parent = $icur_catfoto->parent; $icatway[$iii]->sefname = $icur_catfoto->sefname;
		}
		$icatway = invert_array($icatway); $strret = ""; $maxcnt=count ($icatway);
		foreach ($icatway as $icatway1){  $strret .= $icatway1->sefname."/"; }
		return '/'.substr(  $strret, 0, (strlen($strret)-1)  );
	}
	function update_goods (){
		global $database;
		$i24r = new mosDBTable( "#__icat", "id", $database );
		$i24r->id = $this->id;
		$i24r->goods = ggsqlr ( "select count(id) from #__content where catid=$this->id " );
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		return ;
	}

	
}

?>