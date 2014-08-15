<?php
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

/**
* @package Joomla RE
* @subpackage Content
*/
class TOOLBAR_FrontPage {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::archiveList();
		mosMenuBar::spacer();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::custom('remove','delete.png','delete_f2.png','Удалить', true);
		mosMenuBar::endTable();
	}
}
?>