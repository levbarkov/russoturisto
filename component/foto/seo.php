<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database, $iseoname;
if (  $sefname1!='foto'  )	return;

$tsefname = $sefname;

$icontent[0]->id = 0;
//определяем следующую директорию
$iregul = "/^[\/]*$iseoname+[\/]+($iseoname+)/";
$iregadd = "$iseoname+[\/]+";
for($i=0; $i<10; $i++){
	$curid = $icontent[0]->id;
	preg_match($iregul,$tsefname, $matches); // ggtr ($matches,2);
	if (  $matches[1]==''  ) break;
	$icontent = ggsql ("select id from #__exfoto where sefname='$matches[1]' and parent=$curid");
	$iregul = str_replace($iregadd, $iregadd."$iseoname+[\/]+", $iregul); $iregadd .= "$iseoname+[\/]+";
}
//$sefname1 = $matches[1];
$_REQUEST['c']='foto';
$_REQUEST['id']=$icontent[0]->id;
rewrite_option();
$seoresult=true;

?>