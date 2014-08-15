<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

/**
* @package Joomla RE
* @subpackage Users
*/
class TOOLBAR_users {
	/**
	* Draws the menu to edit a user
	*/
	function _EDIT() {
		global $id;

		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::apply();
		mosMenuBar::spacer();
		mosMenuBar::custom( "showme", "", "", "Смотреть&nbsp;на&nbsp;сайте");
		mosMenuBar::spacer();
		if ( $id ) {
			mosMenuBar::cancel( 'cancel', 'Закрыть' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::endTable();
	}

	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::custom( "unblock", "", "", "Разрешить");
		mosMenuBar::spacer();
		mosMenuBar::custom( "block", "", "", "Блокировать");
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::endTable();
	}
}
?>