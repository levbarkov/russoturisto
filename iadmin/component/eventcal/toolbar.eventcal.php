<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );


require_once( 'toolbar.eventcal.html.php' );

switch ($task) {

	case "edit":
		TOOLBAR_eventcal::_EDIT();
		break;
  case "new":  
		TOOLBAR_eventcal::_EDIT();
		break;
	case "config":
	case "storeconfig":
	  TOOLBAR_eventcal::_CONFIG();
	  break;	
	case "categories":	
  case "colorcategories":
	case "apply":  
		TOOLBAR_eventcal::_CATEGORIES();
		break;		
	default:
		TOOLBAR_eventcal::_DEFAULT();
		break;
}
?>