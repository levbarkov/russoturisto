<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
//if (  $sefname1!=$reg['backlink_seoname']  )	return;
if (  $sefname1!='icq'  )	return;

$_REQUEST['c']='icq'; $seoresult=true; rewrite_option();
?>