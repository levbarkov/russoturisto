<?php
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );
if (  isset($_REQUEST['func'])  ){
	if (  $_REQUEST['func']=='sign'){
		$iway[0]->name="Задать вопрос";
		$iway[0]->url="";
		i24pwprint ($iway);
	}
} else {
	$iway[0]->name="Вопрос / Ответ";
	$iway[0]->url="";
	i24pwprint ($iway); 
}