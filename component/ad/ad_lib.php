<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
define( 'ICSAD_LIB', 1 );
$ex_value_print = "руб.";

function ad_get_real_f($fval){
	if (  $fval==''  )	return "&nbsp;";
	else 				return $fval;
}

function adrecalc_req($sefurl, $adcatid, $realgoods) {
global $database;
	$adcatgoods = ggsqlr ( "select count(id) from #__adgood where parent=$adcatid " );
	if (  $realgoods!=$adcatgoods  and  $adcatid>0  ){
		$i24r = new mosDBTable( "#__adcat", "id", $database );	
		$i24r->id = $adcatid; $i24r->goods = $adcatgoods; 
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	if (  $adcatgoods>0  and  $adcatid>0  ){
		$adgoods = ggsql ( "select * from #__adgood where parent=$adcatid " ); //ggtr ($adgoods);
		foreach ($adgoods as $adgood){
			if (  $adgood->sefnamefullcat!=$sefurl  ){	
				$i24r = new mosDBTable( "#__adgood", "id", $database );
				$i24r->id = $adgood->id; 	$i24r->sefnamefullcat = $sefurl;
				if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
			}
		}
	}	
	$adcats = ggsql ( "select * from #__adcat where parent=$adcatid " ); // ggtr($adcats);
	if (  count($adcats)>0  )
		foreach ($adcats as $adcat){	//ggtr ($adcat->id,1);
			// обновляем sefurlfull для категории
			$i24r = new mosDBTable( "#__adcat", "id", $database );
			$i24r->id = $adcat->id;
			$i24r->sefnamefull = $sefurl;  $adcat->sefnamefull = $sefurl; // ggtr01 ($sefurl);
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
			// обновляем sefurlcat для объявлений
			adrecalc_req ($sefurl."/".$adcat->sefname, $adcat->id, $adcat->goods);
		}
}

function adcat_update_goods ($idcat){
	global $database;
	$i24r = new mosDBTable( "#__adcat", "id", $database );
	$i24r->id = $idcat;
	$i24r->goods = ggsqlr ( "select count(id) from #__adgood where parent=$idcat " );
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	return ;
}
function adcat_get__sefnamefull($idcat){
	global $reg;
	$thisfotocat = ggo($idcat, "#__adcat");
	$icatway = array(); $iii = 0;
	$icatway[0]->name = ($thisfotocat->name); $icatway[0]->parent = $thisfotocat->parent; $icatway[0]->sefname = $thisfotocat->sefname;
	if (  $thisfotocat->id==0  ) return "";
	while ($icatway[$iii]->parent!=0){
		$icur_catfoto = ggo($icatway[$iii]->parent, "#__adcat");
		$iii++;
		$icatway[$iii]->name = ($icur_catfoto->name); $icatway[$iii]->parent = $icur_catfoto->parent; $icatway[$iii]->sefname = $icur_catfoto->sefname;
	}
	$icatway = invert_array($icatway); $strret = ""; $maxcnt=count ($icatway);
	foreach ($icatway as $iii=>$icatway1){  if (  $iii==($maxcnt-1)  ) break; $strret .= $icatway1->sefname."/"; }
	return '/'.$reg['ad_seoname'].'/'.substr(  $strret, 0, (strlen($strret)-1)  );
}
function adgood_get_sefnamefullcat($idcat){
	global $reg;
	$thisadcat = ggo($idcat, "#__adcat");
	$icatway = array(); $iii = 0;
	$icatway[0]->name = ($thisadcat->name);
	$icatway[0]->parent = $thisadcat->parent;
	$icatway[0]->sefname = $thisadcat->sefname;
	if (  $thisadcat->id==0  ) return "";
	while ($icatway[$iii]->parent!=0){
		$icur_catfoto = ggo($icatway[$iii]->parent, "#__adcat");
		$iii++;
		$icatway[$iii]->name = ($icur_catfoto->name);
		$icatway[$iii]->parent = $icur_catfoto->parent;
		$icatway[$iii]->sefname = $icur_catfoto->sefname;
	}
	
	$icatway = invert_array($icatway); $strret = "";
	foreach ($icatway as $icatway1){
		$strret .= $icatway1->sefname."/";
	}
	return "/".$reg['ad_seoname']."/".$strret;
}

?>