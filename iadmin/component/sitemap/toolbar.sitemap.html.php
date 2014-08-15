<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

/**
* @package Joomla RE
* @subpackage Users
*/
class TOOLBAR_users {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::save('savecfg');
                mosMenuBar::spacer();
		mosMenuBar::save('makemap', 'Обновить&nbsp;xml');
		mosMenuBar::endTable();
	}
}
?>