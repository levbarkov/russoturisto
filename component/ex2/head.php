<?php
/* 
 * ДАННЫЙ ФАЙЛ ЗАГРУЖАЕТСЯ В ОБЛАСТИ <HEAD>...</HEAD>
 *
 * Как правило содержит вызов CSS стилей и загрузку JAVASCRIPT библиотек компонента
 */
global $reg;
$component_path = '/component/'.$reg['c'].'/';

/*
 *
 * ФУНКЦИЯ css - выводит тег <link href="file.css" rel="stylesheet" type="text/css"/>
 * при повторном вызове одного и того-же файла css-тег не генерируется
 *
 * ДЛЯ ИСКЛЮЧЕНИЯ ДВОЙНОГО ВЫЗОВА ОДНИХ И ТЕХ ЖЕ ФАЙЛОВ СТИЛЕЙ - ВСЕ ВЫЗОВЫ .css — ЧЕРЕЗ css("file.css");
 *
 */
css($component_path.'style.css');
/*
 *
 * ФУНКЦИЯ js - выводит тег <script type="text/javascript" src="javascript.js"></script>
 * при повторном вызове одного и того-же скрипта тег не генерируется
 *
 * ДЛЯ ИСКЛЮЧЕНИЯ ДВОЙНОГО ВЫЗОВА ОДНИХ И ТЕХ ЖЕ СКРИПТОВ - ВСЕ ВЫЗОВЫ javascript'ов — ЧЕРЕЗ js("javascript.js");
 *
 */
#js($component_path.'jquery_scrollto.js');  // для добавления в избранное с эффектом
js($component_path.'js.js');  // для добавления в избранное с эффектом
?>