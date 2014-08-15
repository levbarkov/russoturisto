<?php
class slinks{ // КЛАСС ДЛЯ РАБОТЫ С МАССИВОМ ССЫЛОК - ДОБАВЛЕНИЕ, ПРОВЕРКА НА НАЛИЧИЕ И  Т.Д..
	var $links = array();
	function add_link($url){
		if (   !$this->find($url)  )	$this->links[] = $url;
	}
	function find($url){ //find link in array of links
		foreach (  $this->links as $link  ){
			if (  $this->check_url_for_c($link, $url)  ) { return true; break; }
		}
		return false;
		//if($this->set) $this->save(0);
	}
	function check_url_for_c($url, $icomp){	//ПРОВЕРЯЕМ  ДВЕ ССЫЛКИ - ОДНИ И ТЕЖЕ ИЛИ НЕТ
		$url 	= str_replace (site_url, "", $url);		$url 	= str_replace ("/", "", $url);	// ggtr01 ($url);
		$icomp = str_replace (site_url, "", $icomp); 	$icomp 	= str_replace ("/", "", $icomp);// ggtr01 ($icomp);
		if 		(  $url==$icomp  ) return true;	else return false;
	}

}


function is_icat($sefname){
	$url 	= str_replace (site_url, "", $sefname);		$url 	= str_replace ("/", "", $sefname);	// ggtr01 ($url);
	$icat = ggsqlr ( "select count(id) from #__icat where sefname = '".$url."' " ); 
	return $icat>0 ? true : false;
}
?>