<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!='foto_full_screen_gallery'  and  $sefname1!='foto_full_screen_gallery_iteml'  )	return;

if (  $sefname1=='foto_full_screen_gallery_iteml'  ){
	$_REQUEST['c']='foto_full_screen_gallery'; 
	$_REQUEST['task']='items_xml'; 
	$_REQUEST['4ajax']=1; 
	$seoresult=true; rewrite_option();
}
$_REQUEST['c']='foto_full_screen_gallery'; $seoresult=true; rewrite_option();
?>