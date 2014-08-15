<?php

/**
 * sitelog
 */
class sitelog {
	var $f;
	var $fwhere = array();
	var $desc;
	var $i24r;
	var $pio;
	
	function __construct(){
		$this->f = array();
		$this->f[0] = "";
		$this->f[1] = "";
		$this->f[2] = "";
		$this->f[3] = "";
		$this->f[4] = "";
		$this->f[5] = "";
		$this->f[6] = "";
		$this->f[7] = "";
	}
	function get_description($thisfotocat, $table_name, $parent_field, $start_url, $start_name, $pre_description){
			global $reg;
			$icatway = get_pathway_array($thisfotocat, $table_name, $parent_field, $start_url, $start_name, 0);
			$iret = $pre_description;
			for ($iii=0; $iii<( count($icatway) ); $iii++ ){
				$iret .=  desafelySqlStr($icatway[$iii]->name);
				if (  $iii<(count($icatway)-1)  ) $iret .=  $reg['global_static_delimiter'];
			}
			return $iret;
	}
	
	function get_fwehere (){
		$fwehere = array();
		for ($i=0; $i<count($this->f); $i++  ){	// ggtr01 ($this->f[$i]);
			if (  strcmp($this->f[$i],"")!=0  )
				$fwehere[] = " f$i='".$this->f[$i]."' "; // ggtr01($fwehere);
		}
		$this->fwhere = $fwehere;
	}
	function fill_fvalues(){
		if (  strcmp($this->f[0],"")!=0  ) $this->i24r->f0=$this->f[0];
		if (  strcmp($this->f[1],"")!=0  ) $this->i24r->f1=$this->f[1];
		if (  strcmp($this->f[2],"")!=0  ) $this->i24r->f2=$this->f[2];
		if (  strcmp($this->f[3],"")!=0  ) $this->i24r->f3=$this->f[3];
		if (  strcmp($this->f[4],"")!=0  ) $this->i24r->f4=$this->f[4];
		if (  strcmp($this->f[5],"")!=0  ) $this->i24r->f5=$this->f[5];
		if (  strcmp($this->f[6],"")!=0  ) $this->i24r->f6=$this->f[6];
		if (  strcmp($this->f[7],"")!=0  ) $this->i24r->f7=$this->f[7];
	}
	

	function isnewlog(){
		global $reg;
		$this->get_fwehere();																									if (  $reg['sitelog_debug']==1  ) { ggtr01 ('fwehere'); ggtr1 (  $this->fwhere  ); }
		$sql_stat = "SELECT * FROM #__stat ".(count($this->fwhere)? " WHERE " . implode( ' AND ', $this->fwhere ) : "" );		if (  $reg['sitelog_debug']==1  ) { ggtr01 ('sql_stat'); ggtr (  $sql_stat  ); }
		$pio = ggsql (  $sql_stat  );
		$this->pio = $pio[0]; 																									if (  $reg['sitelog_debug']==1  ) { ggtr01 ('pio'); ggtr2 (  $this->pio  ); }
		if (  isset($this->pio->id)  )  	return false;
		else return true;
	}	

	
	function savelog(){
		global $reg;	
		$this->i24r = new mosDBTable( "#__stat", "id", $reg['db'] );
		$this->i24r->ip = $_SERVER['REMOTE_ADDR']; 
		$this->i24r->ctime = time();
		if (  isset($this->pio->id)  )  	$this->i24r->id = $this->pio->id;			else $this->i24r->id = 0;
		if (  isset($this->pio->cnt)  )  	$this->i24r->cnt = $this->pio->cnt+1;		else $this->i24r->cnt = 1;
		if (  !isset($this->pio->id)  )  	{
			$this->i24r->desc = $this->desc;
			$this->fill_fvalues();
			$this->i24r->url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		if (!$this->i24r->check()) { echo "<script> alert('".$this->i24r->getError()."'); window.history.go(-1); </script>\n"; } else $this->i24r->store();
		if (  $reg['sitelog_debug']==1  ) { ggtr01 ('i24r after store()'); ggtr2 (  $this->i24r  ); }
		return;
	}	
}
?>
