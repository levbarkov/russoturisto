<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
date_default_timezone_set('Asia/Krasnoyarsk');
ini_set('short_open_tag', 'on');


$ip_ban = array(
    '66.249.66.210',
    '66.249.68.141',
    '66.249.71.244',
    '72.30.78.240',
    '74.6.22.101',
    '74.6.22.172',
    '81.19.66.77',
    '87.250.214.226', 
    '92.241.182.23',
    '94.75.247.240',
    '94.127.144.35',
    '194.67.18.241',
    'x-end-x'
);

////DATABASE CONFIG SECTION////
$DBhostname = "localhost";
$DBuserName = "root";
$DBname  	= "russo";
$DBpassword = "";
$DBPrefix  	= "sln_";


////MAIN CONFIG SECTION////
$sitename  	= "Руссо туристо";
define ('site_path', $_SERVER['DOCUMENT_ROOT']);

/*
Если у сайта есть много алиасов, то их прописываем сюда для того, чтобы флаг 4ajax не срезался
Пример использования (русские домены вставлять в punicode!): 
$server_aliases = array(
    'ledvizor.ru',
    'ledvisor.ru',
    'xn--b1adckeo0aq.xn--p1ai',
);*/

$server_aliases = array('russo', 'russo');

foreach($server_aliases as $alias){
    if($alias == $_SERVER['HTTP_HOST']){
        define ('site_url', "http://{$alias}");
        break;
    }
}

if(!defined('site_url'))
    define ('site_url', "http://{$server_aliases[0]}");

////FILE UPLOAD CONFIG SECTION////
ini_set('max_upload_filesize', '30M');
ini_set('post_max_size', '30M');

$_VERSION = new stdClass();

$iversion = "2529";
$adminEmail = "krasinsite@mail.ru";
$ilang = "russian";
$adminTheme = "admin";
$_VERSION->SITE=1;
$session_life_admin = 3600;
$iConfig_list_limit = 30;
$iuniquemail = 1;	//ТРЕБОВАТЬ ОБЯЗАТЕЛЬНОГО НАЛИЧИЯ УНИКАЛЬНОГО EMAIL
$mosConfig_admin_expired = 0; // ПРИНУДИТЕЛЬНОЕ ЗАВЕРШЕНИЕ СЕАНСА АДМИНА САЙТА, В СТАДИИ РАЗРАБОТКИ
$isession_life_admin = 7200;
$iConfig_editor = 'tinymce';
$iConfig_secret = "insiteruul";
$mosConfig_lifetime = '14400';	// секунд живет сессия в базе данных, функция icore/purge
$iConfig_offset = null;
$MAILuseractivation = 1;

////MAIL CONFIG SECTION////
$MAILmailer = 'mail';
$MAILmailfrom = 'info@krasinsite.ru';
$MAILmailname = 'КрасИнсайт';
$MailcharSet = "utf-8";
?>
