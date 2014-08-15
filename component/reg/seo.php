<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database;
if (  $sefname1!='reg'  )	return;

// опрнднляем второе поле - task
$iregul = "/^[\/]*\w+[\/]+([\w.-]+)/";
$iregadd = "\w+[\/]+";
preg_match($iregul,$tsefname, $matches); // ggtr ($matches,5);
$_REQUEST['task']=$matches[1];

$_REQUEST['c']='reg'; $seoresult=true; rewrite_option(); 
?>