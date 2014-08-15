<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!='gallery'  )	return;

$_REQUEST['c']='gallery'; $seoresult=true; rewrite_option();
?>