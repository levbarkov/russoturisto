<?php
defined( '_VALID_INSITE' ) or die( 'Restricted access' );
global $menu;

$menu = ggo(  safelySqlInt($_REQUEST['mid']),  "#__menu");
$params = new mosParameters( $menu->params );	//ggtr ($params, 24);
 
?>
<iframe
		id="blockrandom"
		name="<? print $params->def( 'page_title', "iframe" ); ?>"
		src="<? print $params->def( 'url', "http://insite.krasinsite.ru" ); ?>"
		width="<? print $params->def( 'width', "100%" ); ?>"
		height="<? print $params->def( 'height', 500 ); ?>"
		scrolling="<? print $params->def( 'scrolling', "auto" ); ?>"
		align="top"
		frameborder="0"
		class="wrapper<? print $params->def( 'pageclass_sfx', "" ); ?>">
		Эта страница будет отображена некорректно. Ваш браузер не поддерживает вложенные фреймы (IFrame)</iframe><?  