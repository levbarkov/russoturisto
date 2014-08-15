<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

switch ( $task ) {
	default:
		iviewSearch_pathway();
		break;
}

function iviewSearch_pathway() {
	$iway[0]->name="Поиск";
	$iway[0]->url="";
	i24pwprint ($iway);
}
?>