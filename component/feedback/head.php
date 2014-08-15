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
js("/includes/js/jTypeWriter.js");
?>