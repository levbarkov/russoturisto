<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if($sefname1 != 'test')
    return;

$_REQUEST['c'] = 'test';
$seoresult = true;
rewrite_option();