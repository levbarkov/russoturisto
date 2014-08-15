<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if ($sefname1 != 'cab_orders')
    return;

$_REQUEST['c'] = 'cab_orders';
$seoresult = true;
rewrite_option();
