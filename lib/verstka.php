<?php

/**
 * Класс содержащий основный елементы для правильной верстки сайта
 *
 * @author George
 */


class verstka {
	var $row;
	function __construct(&$row = ""){
		
	}

	public static function pageNavigation($total, $limitstart, $limit){
		require_once( site_path . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		echo $pageNav->getListFooter(); 
	}

	public static function insite_header()
	{
		?>
		<!DOCTYPE html>
		<!--[if IE 8 ]>		<html lang="ru" class="ie ie8"> <![endif]-->
		<!--[if IE 9 ]>		<html lang="ru" class="ie ie9"> <![endif]-->
		<!--[if (gt IE 9)|!(IE)]><!--> <html lang="ru"> <!--<![endif]-->
		<head>
		<title><?=ititle(); ?></title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="description" content="<?=imeta_description(); ?>" />
		<meta name="keywords" content="<?=imeta_keywords(); ?>" />
		<link href="/favicon.ico" rel="shortcut icon" />
		<link href="/favicon.png" rel="icon" type="image/png" />
		<?
			search_systems_meta_validation();
			css("/includes/css/style.css");
			css("/includes/css/animate.css");
			css("/includes/css/suite.css");
			css("/includes/css/suite-settings.css");
			css("/includes/bower_components/fancybox/source/jquery.fancybox.css");
			// js("http://code.jquery.com/jquery-1.10.2.js");
			js("http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js");
			js("/includes/js/jquery.colorbox.js");
			js("/includes/js/script.js");
			js("/includes/bower_components/jquery-backstretch/src/jquery.backstretch.js");
			js("/includes/bower_components/fancybox/source/jquery.fancybox.js");
			ib_header();
		?>
		</head>
		<?
	}
        
	/**
	 * Формируем код статистика сайта и размещаем в конце тега body
	 */
	public static function insite_footer(){
		global $reg;
		print desafelySqlStr(  $reg['promo']->data->ext_stat_script  );
	}

	public static function insite_main_styles_jscript(){
		/*
		 * ФУНКЦИЯ search_systems_meta_validation - выводит теги подтверждение прав на сайт для поисковых систем: Yandex, Mail, Google, Aport, Rambler и других.
		 */
		search_systems_meta_validation();
		/*
		 *
		 * ФУНКЦИЯ css - выводит тег <link href="file.css" rel="stylesheet" type="text/css"/>
		 * при повторном вызове одного и того-же файла css-тег не генерируется
		 *
		 * ДЛЯ ИСКЛЮЧЕНИЯ ДВОЙНОГО ВЫЗОВА ОДНИХ И ТЕХ ЖЕ ФАЙЛОВ СТИЛЕЙ - ВСЕ ВЫЗОВЫ .css — ЧЕРЕЗ css("file.css");
		 *
		 */
		css("/theme/theme_extfiles/css/insite.css");
		/*
		 *
		 * ФУНКЦИЯ js - выводит тег <script type="text/javascript" src="javascript.js"></script>
		 * при повторном вызове одного и того-же скрипта тег не генерируется
		 *
		 * ДЛЯ ИСКЛЮЧЕНИЯ ДВОЙНОГО ВЫЗОВА ОДНИХ И ТЕХ ЖЕ СКРИПТОВ - ВСЕ ВЫЗОВЫ javascript'ов — ЧЕРЕЗ js("javascript.js");
		 *
		 */
		js("/includes/js/jquery-1.7.1.min.js");
		js("/includes/js/jquery.form.js");      // для выполнения форм в ajax (ajaxForm, ajaxSubmit)
		js("/includes/js/jTypeWriter.js");      // для эффекта печатания сиволов ( используется в формах при написании ошибки )
		js("/includes/js/insite.js");           // основные js-функции cms insite

		// ЕСЛИ ИСПОЛЬЗУЕТСЯ ЛИЧНЫЙ КАБИНЕТ С ВОЗМОЖНОСТЬЮ ЗАГРУЗКИ ФОТО - ТО НЕОБХОДИМО ПОДКЛЮЧИТЬ JQUERY PLUGIN imgAreaSelect
		// $imgareaselect = new imgareaselect(); $imgareaselect->java_init();
		// ЕСЛИ ИСПОЛЬЗУЕМ ВСПЛЫВАЮЩИЕ ОКНА - ТО НЕОБХОДИМО ПОДКЛЮЧИТЬ colorbox
		$colorbox = new colorbox(); 		$colorbox->java_init();
		// ЕСЛИ ИСПОЛЬЗУЕМ FANCYBOX - ТО НЕОБХОДИМО ПОДКЛЮЧИТЬ fancybox
		$fancybox = new fancybox(); 		$fancybox->java_init();

		/*
		 * ГРУЗИМ СТИЛИ, JAVASCRIPT БИБЛИОТЕКИ КОМПОНЕНТА
		 * ib_header - смотрит наличие файла head.php в директории компонента и запускает его
		 * в файле head.php должны быть определены стили и js-скрипты
		 */
		ib_header();
	}

	/**
	 * Секретная кнопка входа/выхода
	 *
	 * @param <string> $txt текст который будет секретной кнопкой
	 * @param <string> $freecode_login код для вставки в тег span вход
	 * @param <string> $freecode_logout код для вставки в тег a ссылки выхода
	 */
	public static function secret_login($txt, $freecode_login="", $freecode_logout=""){
		global $my;
		?><span <?=$freecode_login ?> onclick="javascript: ins_ajax_open('/?4ajax_module=login', 400, 280); void(0);"><?=$txt ?></span><?
		if (  $my->id  ) {?>&nbsp;<a <?=$freecode_logout ?> href="javascript: ins_ajax_logout(); void(0);">Выход</a><?}
	}

}

