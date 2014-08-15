<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );
// ggtr ($task);
switch ( $task ) {
	case 'move':
		mosMenuBar::startTable();
		mosMenuBar::customG( "Перенести", "javascript: if (document.adminForm.parent.value == 0){ alert('Пожалуйста, сделайте выбор из списка');}else{hideMainMenu();submitbutton('movesave')}");
		mosMenuBar::spacer();
		mosMenuBar::cancel( 'cancel', 'Отмена' );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
		break;
	case 'copy':
		mosMenuBar::startTable();
		mosMenuBar::customG( "Копировать", "javascript: if (document.adminForm.parent.value == 0){ alert('Пожалуйста, сделайте выбор из списка');}else{hideMainMenu();submitbutton('copysave')}");
		mosMenuBar::spacer();
		mosMenuBar::cancel( 'cancel', 'Отмена' );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
		break;
	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_users::_EDIT();
		break;
		
	case 'save':
	case 'apply':
		break;

	case 'cancel':
	case 'view':
	case '':
		TOOLBAR_users::_DEFAULT();
		break;
}
?>