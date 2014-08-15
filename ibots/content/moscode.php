<?php
/**
* @version $Id: moscode.php 2411 2006-02-16 17:23:32Z stingrey $
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

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosCode' );

/**
* Мамбот подсветки кода
*
* <b>Использование:</b>
* <code>{moscode}...какой-нибудь код...{/moscode}</code>
*/
function botMosCode( $published, &$row, &$params, $page=0 ) {
	// определение правильного выражения для бота
	if ( strpos( $row->text, 'moscode' ) === false ) {
		return true;
	}
	
	// define the regular expression for the bot
	$regex = "#{moscode}(.*?){/moscode}#s";

	// check whether mambot has been unpublished
	if (!$published) {
		$row->text = preg_replace( $regex, '', $row->text );
		return true;
	}

	// выполнение замены
	$row->text = preg_replace_callback( $regex, 'botMosCode_replacer', $row->text );

	return true;
}
/**
* Замена совпадающих тэгов an image
* @param array - Массив соответствий (см. - preg_match_all)
* @return string
*/
function botMosCode_replacer( &$matches ) {
	$html_entities_match = array("#<#", "#>#");
	$html_entities_replace = array("&lt;", "&gt;");

	$text = $matches[1];

	$text = preg_replace($html_entities_match, $html_entities_replace, $text );

	// Замена 2 пробелов "&nbsp; " так,  чтобы выравнивался нетабулированный код, при этом не создавая огромных длинных строк.
	$text = str_replace("  ", "&nbsp; ", $text);
	// немедленная замена 2 пробелами с " &nbsp;" выявленным нечетным количеством пробелов.
	$text = str_replace("  ", " &nbsp;", $text);

	// Замена табуляций "&nbsp; &nbsp;" так, что код с символами табуляции выравнивается по правому краю, не создавая слишком длинных строк.
	$text = str_replace("\t", "&nbsp; &nbsp;", $text);

	$text = str_replace('&lt;', '<', $text);
	$text = str_replace('&gt;', '>', $text);

	$text = highlight_string( $text, 1 );

	$text = str_replace('&amp;nbsp;', '&nbsp;', $text);
	$text = str_replace('&lt;br/&gt;', '<br />', $text);
	$text = str_replace('<font color="#007700">&lt;</font><font color="#0000BB">br</font><font color="#007700">/&gt;','<br />', $text);
	$text = str_replace('&amp;</font><font color="#0000CC">nbsp</font><font color="#006600">;', '&nbsp;', $text);
	$text = str_replace('&amp;</font><font color="#0000BB">nbsp</font><font color="#007700">;', '&nbsp;', $text);
	$text = str_replace('<font color="#007700">;&lt;</font><font color="#0000BB">br</font><font color="#007700">/&gt;','<br />', $text);

	return $text;
}
?>