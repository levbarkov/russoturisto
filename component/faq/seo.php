<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

global $reg;
if ($sefname1 != 'faq')
    return;

$_REQUEST['c'] = 'faq';
$seoresult = true;
rewrite_option();