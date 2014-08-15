<?php
/*
 * ОТПРАВКА СООБЩЕНИЙ ICQ, ВСЯ ПРОБЛЕМА В ТОМ ЧТО ПРИ ЧАСТОЙ РАССЫЛКЕ СООБЩЕНИЙ ID БЛОКИРУЕТ САМ ICQ
 * БЛОКИРОВКУ МОЖНО ОБОЙТИ ПРИ ИСПОЛЬЗОВАНИИ БОТА ICQ
 */

global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

require_once(site_path.'/includes/webicq/webicqlite.class.php');
$icq = new WebIcqLite();
if (!$icq->connect($reg['icq_uin'], $reg['icq_pwd']) )
{
    echo $icq->error;
    exit();
}
$icq->send_message(   ggri('icq_uin'),   mb_convert_encoding(   urldecode( ggrr('icq_text') ), 'cp1251', 'UTF-8'   )   );
$icq->disconnect();
?>