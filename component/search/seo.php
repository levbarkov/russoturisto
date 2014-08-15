<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!='search'  )	return;

$_REQUEST['c']='search'; $seoresult=true; rewrite_option();
?>