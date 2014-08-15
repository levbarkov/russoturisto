<?php
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
//'93.88.164.223', 
'94.75.247.240',
'94.127.144.35',
'194.67.18.241',
'x-end-x'
);
////DATABASE CONFIG SECTION////
$DBhostname = "localhost";
$DBuserName = "insite";
$DBpassword = "insite123";
$DBname  	= "XXXXXXX";
$DBPrefix  	= "ins_";

////MAIN CONFIG SECTION////
$sitename  	= "СМС - Инсайт";
define ('site_url', "http://XXXXXXX.krasinsite.ru");
define ('site_path', "/var/www/XXXXXXX.dev/www");

/*
Если у сайта есть много алиасов, то их прописываем сюда для того, чтобы флаг 4ajax не срезался
Пример использования (русские домены вставлять в punicode!): 
$server_aliases = array(
    'ledvizor.ru',
    'ledvisor.ru',
    'xn--b1adckeo0aq.xn--p1ai',
);*/
$server_aliases = array();

////FILE UPLOAD CONFIG SECTION////
ini_set('max_upload_filesize', '30M');
ini_set('post_max_size', '30M');

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
$iConfig_offset = '-7';
$MAILuseractivation = 1;

////MAIL CONFIG SECTION////
$MAILmailer = 'mail';
$MAILmailfrom = 'info@krasinsite.ru';
$MAILmailname = 'КрасИнсайт';
$MailcharSet = "utf-8";
?>
