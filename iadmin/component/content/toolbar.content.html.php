<?php

// no direct access
defined( '_VALID_INSITE' ) or die( 'Restricted access' );

/**
* @package Joomla RE
* @subpackage Content
*/
class TOOLBAR_content {
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
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel( 'cancel', 'Закрыть' );
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::endTable();
	}

	function _ARCHIVE() {
		mosMenuBar::startTable();
		mosMenuBar::deleteList();		
		mosMenuBar::spacer();		
		mosMenuBar::unarchiveList();
		mosMenuBar::endTable();
	}

	function _MOVE() {
		mosMenuBar::startTable();
		mosMenuBar::custom( 'movesectsave', '', '', 'Сохранить', false );
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _COPY() {
		mosMenuBar::startTable();
		mosMenuBar::custom( 'copysave', '', '', 'Сохранить', false );
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::deleteList();		
		mosMenuBar::spacer();		
		mosMenuBar::archiveList();
		mosMenuBar::spacer();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::customX( 'movesect', '', '', 'Перенести' );
		mosMenuBar::spacer();
		mosMenuBar::customX( 'copy', '', '', 'Копия' );
		mosMenuBar::spacer();
		mosMenuBar::editListX( 'editA' );
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::endTable();
	}
}
?>