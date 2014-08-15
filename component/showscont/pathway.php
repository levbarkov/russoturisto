<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
// ПОКАЗЫВАЕМ СТАТИЧНОЕ СОДЕРЖИМОЕ
// id - идентификатор содержимого

$idstat = isset ($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$statcont = ggo ($idstat, "#__content");
if (  $idstat==true  ){
	$iway[0]->name=$statcont->title;
	$iway[0]->url="";
	i24pwprint ($iway);
}