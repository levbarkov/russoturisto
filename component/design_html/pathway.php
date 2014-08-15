<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

$iway[0]->name='пример заголовка';
$iway[0]->url="/";
$iway[1]->name='пример подзаголовка';
$iway[1]->url="/";
$iway[2]->name='пример подзаголовка без ссылки';
$iway[2]->url="";

i24pwprint ($iway);
?>