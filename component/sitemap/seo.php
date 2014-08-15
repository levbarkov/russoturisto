<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!=$reg['sitemap_seoname']  )	return;

$_REQUEST['c']='sitemap'; $seoresult=true; rewrite_option();
?>