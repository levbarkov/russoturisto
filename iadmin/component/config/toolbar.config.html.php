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
   
		function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
                mosMenuBar::deleteList();
		mosMenuBar::spacer();		
		mosMenuBar::endTable();
	}
}
?>
