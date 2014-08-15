<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

$xml = file_get_contents("http://j-as.ru/insite_help_utf.html");
print $xml;
?>