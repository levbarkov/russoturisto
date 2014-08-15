<?php
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

class TOOLBAR_menumanager {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::customX( 'copyconfirm', 'copy.png', 'copy_f2.png', 'Копия', true );
		mosMenuBar::spacer();
		mosMenuBar::customX( 'deleteconfirm', 'delete.png', 'delete_f2.png', 'Удалить', true );
		mosMenuBar::spacer();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::endTable();
	}

	function _DELETE() {
		mosMenuBar::startTable();
		mosMenuBar::cancel( );
		mosMenuBar::endTable();
	}

	function _NEWMENU()	{
		mosMenuBar::startTable();
		mosMenuBar::custom( 'savemenu', 'save.png', 'save_f2.png', 'Сохранить', false );
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.menumanager.new' );
		mosMenuBar::endTable();
	}

	function _COPYMENU()	{
		mosMenuBar::startTable();
		mosMenuBar::custom( 'copymenu', 'copy.png', 'copy_f2.png', 'Копия', false );
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
}
?>