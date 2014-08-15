<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
switch ($task) {
	case 'getdata':	getdata( $option );
					break;
}
function cron_print_data($strspacei, $dataname, $dataval){
	return "<br />".str_repeat("&nbsp;", $strspacei).$dataname." = ".$dataval;
}
function getdata( $option ) {
	$_REQUEST['donow']=1;
	require_once(site_path."/shadow_cron.php");
	return;
	global $database, $my, $iConfig_list_limit;
	$now=time(); $table_id = 1;
	$i24 = ggo($table_id, "#__crondata");
	if (  ($now-$i24->ctime)>3500  ){	// необходимо обновить информацию о курсе валюты
		// СБОР ВАЛЮТЫ
		$xmlcount = file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp");
		$xml_arrcount = ixml2array($xmlcount);
		$dollar_kurs = str_replace(",",".",$xml_arrcount['ValCurs'][0]['Valute'][4]['Value']);
		$evro_kurs = str_replace(",",".",$xml_arrcount['ValCurs'][0]['Valute'][5]['Value']);
			
		// СОХРАНЕНИЕ РЕЗУЛЬТАТА
		$i24r = new mosDBTable( "#__crondata", "id", $database );
		$i24r->id = $table_id;
		$i24r->ctime = time();
		$i24r->dollar_kurs = safelySqlFloat( $dollar_kurs );
		$i24r->evro_kurs = safelySqlFloat( $evro_kurs );
		if (!$i24r->check()) {
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		} else $i24r->store();
			
		// ВЫВОД РЕЗУЛЬТАТА
		print cron_print_data(8, "dollar_kurs", $i24r->dollar_kurs);
		print cron_print_data(8, "evro_kurs", $i24r->evro_kurs);
	}
}
?>