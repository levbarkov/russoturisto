<?php
/**
* @version $Id: legacybots.php 2695 2006-03-07 20:26:09Z stingrey $
* @localized Авторские права (C) 2005 Joom.Ru - Русский дом Joomla!
* @copyright Авторские права (C) 2005 Open Source Matters. Все права защищены.
* @license Лицензия http://www.gnu.org/copyleft/gpl.html GNU/GPL, смотрите LICENSE.php
* Joomla! - свободное программное обеспечение. Эта версия может быть изменена
* в соответствии с Генеральной Общественной Лицензией GNU, поэтому возможно
* её дальнейшее распространение в составе результата работы, лицензированного
* согласно Генеральной Общественной Лицензией GNU или других лицензий свободных 
* программ или программ с открытым исходным кодом.
* Для просмотра подробностей и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
* 
* @translator Oleg A. Myasnikov aka Sourpuss (sourpuss@mamboteam.ru)
*/

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botLegacyBots' );

/**
* Обработка любых унаследованных ботов в каталоге /mambots
*
* ЭТОТ ФАЙЛ МОЖЕТ БЫТЬ **БЕЗОПАСНО УДАЛЕН ** ЕСЛИ ВЫ НЕТ НАСЛЕДОВАНИЯ МАМБОТОВ
* @param object - объект содержимого
* @param int - Побитовая маска параметров
* @param int - Номер страницы
*/
function botLegacyBots( $published, &$row, &$params, $page=0 ) {
	

	// проверка, опубликован ли мамбот
	if ( !$published ) {
		return true;
	}

	// Процесс наследования ботов
	$bots = mosReadDirectory( site_path."/mambots", "\.php$" );
	sort( $bots );
	foreach ($bots as $bot) {
		require site_path ."/mambots/$bot";
	}
}
?>