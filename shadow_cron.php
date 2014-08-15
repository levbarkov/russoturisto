<?php

// ЗАКОМЕНТИРУЙТЕ СЛЕДУЮЩУЮ СТРОКУ, ЧТОБЫ CRON ЗАРАБОТАЛ
return;


global $database;
$renew = 86400;
$h = date("G");
if(  ($h > 4 && $h < 14)  or  isset($_REQUEST['donow'])  ){
	$database->setQuery("SELECT * FROM #__crondata WHERE id = 1");	$database->query();		list($obj) = $database->loadObjectList();
	if (  isset($_REQUEST['donow'])  ) $obj->ctime =0;	
	if($obj->ctime == "") $obj->ctime =0;	
	if(  (time()-$obj->ctime)  >  $renew  ){
		$_start = microtime();
		$cron_result = do_cron();
		$_end = microtime();
		$time = $_end - $_start;
		$i24r = new mosDBTable( "#__crondata", "id", $database );
		$i24r->id = 1;
		$i24r->ctime = time();
		$i24r->exe_time = $time;
		
		$i24r->dollar_kurs_prev = $obj->dollar_kurs;
		$i24r->evro_kurs_prev = $obj->evro_kurs;
		$i24r->dollar_kurs = safelySqlFloat( $cron_result->dollar_kurs );
		$i24r->evro_kurs = safelySqlFloat( $cron_result->evro_kurs );
		
		if (!$i24r->check()) {			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";		} else $i24r->store();

	}
}
/* То, что выполняется */
function do_cron() {
	global $database, $reg;
	
	// Тэги
	$tag = new tags('', $database, $reg);
	$tag->recalcAll();
	$tag->generateXML(site_path."/includes/tags.xml");	
	// /Тэги
	  
	// СБОР ВАЛЮТЫ
	$xmlcount = file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp");
	$xml_arrcount = ixml2array($xmlcount);
	$cron_result->dollar_kurs = str_replace(",",".",$xml_arrcount['ValCurs'][0]['Valute'][9]['Value']);
	$cron_result->evro_kurs = str_replace(",",".",$xml_arrcount['ValCurs'][0]['Valute'][10]['Value']);
	
	return $cron_result;
}  
?>