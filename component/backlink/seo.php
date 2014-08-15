<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!=$reg['backlink_seoname']  )	return;

$_REQUEST['c']='backlink'; $seoresult=true; rewrite_option();
?>