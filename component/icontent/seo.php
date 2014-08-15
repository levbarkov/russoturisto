<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database;
$tsefname = $sefname;

//$_REQUEST['clean'] = 1;
preg_match("/(^.*[\/])(.*)\.html$/",$sefname, $matches);
// var_dump ($matches);
if (isset($matches[2]) && $matches[2] != ''){	// необходимо вывести содержимое статьи
	if ( preg_match("/^(.*)[\/](.*)\//",$matches[1], $m) )
	{
		$parent = $m[2];
		$subparent = $m[1];
	}
	else { $parent = substr($matches[1],0, strlen($matches[1])-1); }

	$parent = ggsqlr("select id from #__icat where sefname='".$parent."' and sefnamefull='".($subparent?'/'.$subparent:'')."'; ");
	if ($parent === null) return;
	$icontent = ggsql ("select id from #__content where `state` = 1 and `sefname` = '{$matches[2]}' and `catid` = {$parent}");
	if (  count($icontent)>0  ){
		$_REQUEST['c']='icontent';
		$_REQUEST['task']='view';
		$_REQUEST['id']=$icontent[0]->id;
		rewrite_option();
		$seoresult=true;
	}
	return;
}


$icontent = ggsql ("select id from #__icat where `sefname` = '{$sefname1}' and `parent` = 0");
// var_dump ($icontent);
// var_dump ($sefname1);
if (count($icontent) > 0){
	//определяем следующую директорию
	$iregul = "/^[\/]*\w+[\/]+(\w+)/";
	$iregadd = "\w+[\/]+";
	for($i = 0; $i < 10; $i++){
		$curid = $icontent[0]->id;
		preg_match($iregul,$tsefname, $matches);
		if ($matches[1] == '')
			break;
		
		$icontent = ggsql ("select id from #__icat where sefname = '{$matches[1]}' and `parent` = {$curid}");
		$iregul = str_replace($iregadd, $iregadd."\w+[\/]+", $iregul); $iregadd .= "\w+[\/]+";
	}
	//$sefname1 = $matches[1];
	
	$_REQUEST['c'] = 'icontent';
	$_REQUEST['task'] = 'icat';
	$_REQUEST['id'] = $icontent[0]->id;
	rewrite_option();
    $seoresult = true;
}
