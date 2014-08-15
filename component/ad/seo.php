<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database, $reg, $iseoname;
if (  $sefname1!=$reg['ad_seoname']  )	return;

$tsefname = $sefname;
preg_match("/^.*[\/](.*)\.html$/",$sefname, $matches); // ggtr ($matches);
if (  $matches[1]!=''  ){	// необходимо вывести содержимое объявления
	$icontent[0]->id = 0;
	//определяем следующую директорию
	$iregul = "/^[\/]*$iseoname+[\/]+($iseoname+)/";
	$iregadd = "$iseoname+[\/]+";
	for($i=0; $i<10; $i++){
		$curid = $icontent[0]->id;
		preg_match($iregul,$tsefname, $matches); // ggtr ($matches,5);
		if (  strpos($matches[1],'.html')==true  ) break;
		$icontent = ggsql ("select id from #__adcat where sefname='$matches[1]' and parent=$curid");
		$iregul = str_replace($iregadd, $iregadd."$iseoname+[\/]+", $iregul); $iregadd .= "$iseoname+[\/]+";
	}
	if (  $matches[1]=='spisok_sravneniya.html' ){
		$_REQUEST['c']='ad';
		$_REQUEST['task']='adcomp';
		$_REQUEST['id']=$icontent[0]->id;
		rewrite_option();
		$seoresult=true;
		return;
	}
	if (  $matches[1]=='add.html' ){
		$_REQUEST['c']='ad';
		$_REQUEST['task']='new';
		$_REQUEST['id']=$icontent[0]->id;
		rewrite_option();
		$seoresult=true;
		return;
	}

	$goodsefname = substr($matches[1], 0, (strlen($matches[1])-5) );
	$icontent = ggsql ("select id from #__adgood where sefname='$goodsefname' and parent=".$icontent[0]->id."");
	if (  count($icontent)>0  ){
		$_REQUEST['c']='ad';
		$_REQUEST['task']='view';
		$_REQUEST['id']=$icontent[0]->id;
		rewrite_option();
		$seoresult=true;
	}
	return;
}

$icontent[0]->id = 0;
//определяем следующую директорию
$iregul = "/^[\/]*$iseoname+[\/]+($iseoname+)/";
$iregadd = "$iseoname+[\/]+";
for($i=0; $i<10; $i++){
	$curid = $icontent[0]->id;
	preg_match($iregul,$tsefname, $matches); // ggtr ($matches,5);
	if (  $matches[1]==''  ) break;
	$icontent = ggsql ("select id from #__adcat where sefname='$matches[1]' and parent=$curid");
	$iregul = str_replace($iregadd, $iregadd."$iseoname+[\/]+", $iregul); $iregadd .= "$iseoname+[\/]+";
}
//$sefname1 = $matches[1];
$_REQUEST['c']='ad';
$_REQUEST['task']='adcat';
$_REQUEST['id']=$icontent[0]->id;
rewrite_option();
$seoresult=true;
?>