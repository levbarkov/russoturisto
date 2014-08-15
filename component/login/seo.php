<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database;
if (  $sefname1!='login'  )	return;

$_REQUEST['c']='login'; $seoresult=true; rewrite_option(); 
?>