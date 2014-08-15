<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<!-- ТЕГИ TITLE META И FAVICON -->
                <? verstka::insite_header(); ?>

		<? css("/theme/start/css/carcass.css") ?>
		<? css("/theme/start/css/content.css") ?>

                <!-- ЗАГРУЖАЕМ ОБЯЗАТЕЛЬНЫЕ JAVASCRIPT БИБЛИОТЕКИ И СТИЛИ INSITE
                     В САМОМ КОНЦЕ ФУНКЦИИ ЗАГРУЖАЕМ СТИЛИ, JAVASCRIPT БИБЛИОТЕКИ КОМПОНЕНТА (их описание лежит в файле: head.php) -->
                <? verstka::insite_main_styles_jscript(); ?>

		<? 
			js("/includes/js/swfobject.js");
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				window.print();
			});
		</script>
    </head>
	<body style="margin:10px 15px; ">
			<? ib(); ?>
	</body>
</html>