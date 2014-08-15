<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

$iway[0]->name='Личный кабинет';
$iway[0]->url="/";
$iway[1]->name='Заказ № 01778';
$iway[1]->url="";

i24pwprint ($iway);
?>