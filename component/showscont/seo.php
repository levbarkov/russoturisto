<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database;

$icontent = ggsql ("select id from #__content where sefname='$sefname1'  and catid = 0");
if (  count($icontent)>0  ){
	$_REQUEST['c']='showscont';
	$_REQUEST['task']='view';
	$_REQUEST['id']=$icontent[0]->id;
	rewrite_option();
    $seoresult=true;
}
?>