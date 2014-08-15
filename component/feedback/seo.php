<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!=$reg['feedback_seoname']  )	return;

$_REQUEST['c']='feedback'; $seoresult=true; rewrite_option();
?>