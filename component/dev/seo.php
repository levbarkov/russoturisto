<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!='dev'  )	return;

//определяем следующий параметр
$tsefname = $sefname;
$iregul = "/^[\/]*\w+[\/]+(\w+)/";
$iregadd = "\w+[\/]+";
preg_match($iregul,$tsefname, $matches);
if (  $matches[1]!=''  ) $_REQUEST['task'] = $matches[1];

$_REQUEST['c']='dev'; $seoresult=true; rewrite_option();
?>