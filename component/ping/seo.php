<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!='ping'  )	return;

$_REQUEST['c']='ping'; 
$seoresult=true; 
rewrite_option();
?>