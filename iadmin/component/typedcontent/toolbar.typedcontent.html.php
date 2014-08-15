<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* @package Joomla RE
* @subpackage Content
*/
class TOOLBAR_typedcontent {
	function _EDIT( ) {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::apply();
		mosMenuBar::spacer();
		mosMenuBar::custom( "showme", "", "", "Смотреть&nbsp;на&nbsp;сайте");
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::customX( 'copysave', '', '', 'Копировать' );
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::editListX( 'editA' );
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::endTable();
	}
}
?>