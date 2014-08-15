<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
//if (  $sefname1!=$reg['backlink_seoname']  )	return;
if (  $sefname1!='mail2sms'  )	return;

$_REQUEST['c']='mail2sms'; $seoresult=true; rewrite_option();
?>