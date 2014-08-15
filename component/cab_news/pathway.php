<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $task, $id, $option, $isgal;
$id	= intval( getUserStateFromRequest(  'id', 0 ) );
$isgal = ggri('gal');
switch ($task) {
	case 'edit':		editcab_news_pathway( $id, $option );
						break;
	case 'new':			editcab_news_pathway( 0, $option );
						break;
	default:			showcab_news_pathway( $option );
						break;
}
function showcab_news_pathway( $option ) {
	$iway[0]->name="Личный кабинет";
	$iway[0]->url="index.php?c=cab";
	$iway[1]->name=ggri('gal')? "Фотогалерея" : "Новости";
	$iway[1]->url="";
	i24pwprint ($iway); 
}

function editcab_news_pathway( $id, $option ) {
	if (  $id>0  ) $row = ggo($id, "#__cab_news");

	$iway[0]->name="Личный кабинет";
	$iway[0]->url="index.php?c=cab";
	$iway[1]->name=ggri('gal')? "Фотогалерея" : "Новости";
	$iway[1]->url= ggri('gal')? "index.php?c=cab_news&task=view&gal=1" : "index.php?c=cab_news&task=view";
	if (  $id>0  ) $iway[2]->name=stripslashes($row->title);
	else $iway[2]->name="Новая";
	$iway[2]->url="";
	i24pwprint ($iway); 
}
?>