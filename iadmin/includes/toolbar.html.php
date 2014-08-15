<?php


defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );


class MENU_Default {

	function MENU_Default() {
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::addNew();
		mosMenuBar::editList();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
}
?>