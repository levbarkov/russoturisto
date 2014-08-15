<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!='bigform'  )	return;

$_REQUEST['c']='bigform'; $seoresult=true; rewrite_option();
?>