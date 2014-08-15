<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;

preg_match("/([\w-]+)$/",dirname(__FILE__), $matches);

if (  $sefname1!=$matches[1]  )	return;
$_REQUEST['c']=$matches[1]; $seoresult=true; rewrite_option();
?>