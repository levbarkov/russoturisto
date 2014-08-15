<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

/**
* @package Joomla RE
* @subpackage Admin
*/
class TOOLBAR_admin {
	function _SYSINFO() {
//		mosMenuBar::startTable();
//		mosMenuBar::help( 'screen.system.info' );
//		mosMenuBar::endTable();
	}
	/**
	* Draws the menu for a New category
	*/
	function _CPANEL() {
		mosMenuBar::startTable();
		mosMenuBar::custom( "showme", "", "", "Смотреть&nbsp;сайт");
		mosMenuBar::endTable();
	}
	/**
	* Draws the menu for a New category
	*/
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::custom( "showme", "", "", "Смотреть&nbsp;сайт");
		mosMenuBar::endTable();
	}
}
?>