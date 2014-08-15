<?php

/**
 * Класс для работы с пользователями
 *
 * @author George
 */


class user {
	var $vars;
	function __construct(&$row=""){ 
		if (  $row  ){
			if (  is_numeric($row)  ) $this->vars = ggo ($row, "#__users");
			else $this->vars = $row;
		}
	}

	function get_smalllogo(){
		$logo = '/images/cab/nologo.jpg';
		
		if ($this->vars->small != '' and file_exists(site_path . '/images/cab/logo/' . $this->vars->small))
			$logo = '/images/cab/logo/' . $this->vars->small;
			
		return $logo;
	}
	
	function getGentleName(){
		$GentleName = $this->vars->name;
		if (  $this->vars->userparentname!=''  ) $GentleName .= ' '.$this->vars->userparentname;
		
		return desafelySqlStr( $GentleName );
	}

	
	function saveContactDataFromOrder( &$user=NULL ){
		global $reg;
		if (  $user!=NULL  &&  $user->id  ){
			$i24r = new mosDBTable( "#__users", "id", $reg['db'] );
			$i24r->id = $user->id;
			$i24r->tel = ggrr('u_tel');
                        $i24r->address = ggrr('u_address');
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		}
	}

}
