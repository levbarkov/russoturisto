<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!=$reg['cab_seoname']  )	return;

$_REQUEST['c']='cab'; $seoresult=true; rewrite_option();
?>