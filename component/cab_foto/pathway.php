<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $option, $isgal, $fparent;
$cid = josGetArrayInts( 'cid' );
$id	= intval( getUserStateFromRequest(  'id', 0 ) );
$fparent = ggo(ggri('parent'), "#__cab_news");  $isgal = $fparent->type;
switch ($task) {
	case 'edit':		editcab_foto_pathway( $id, $option );
						break;
	default:			showcab_foto_pathway( $option );
						break;
}

function showcab_foto_pathway( $option ) {
	global $database, $my, $iConfig_list_limit, $iConfig_list_limit, $isgal, $fparent;
	$excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	$iway[0]->name="Личный кабинет";
	$iway[0]->url="index.php?c=cab";
	$iway[1]->name=$isgal? "Фотогалерея" : "Новости";
	$iway[1]->url= $isgal? "index.php?c=cab_news&task=view&gal=1" : "index.php?c=cab_news&task=view";
	$iway[2]->name= stripslashes($fparent->title);
	$iway[2]->url= $isgal? "index.php?c=cab_news&task=edit&id=".$fparent->id."&gal=1" : "index.php?c=cab_news&task=edit&id=".$fparent->id;

	i24pwprint ($iway); 
}
function editcab_foto_pathway( $id, $option ) {
	global $database, $fparent, $isgal;

	$ithisfoto = ggo($id, "#__cab_foto");
	$iway[0]->name="Личный кабинет";
	$iway[0]->url="index.php?c=cab";
	$iway[1]->name=$isgal? "Фотогалерея" : "Новости";
	$iway[1]->url= $isgal? "index.php?c=cab_news&task=view&gal=1" : "index.php?c=cab_news&task=view";
	$iway[2]->name= stripslashes($fparent->title);
	$iway[2]->url= $isgal? "index.php?c=cab_news&task=edit&id=&".$fparent->id."&gal=1" : "index.php?c=cab_news&task=edit&id=&".$fparent->id;
	$iway[3]->name="Фото&nbsp;№&nbsp;".$id;
	$iway[3]->url="";
	i24pwprint ($iway); 
}
?>