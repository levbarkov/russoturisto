<?php

defined( '_VALID_INSITE' ) or die( 'Direkter Zugriff ist nicht erlaubt.' );
global $mainframe;
require_once( igetPath( 'toolbar_html' ) );
require_once( site_path.'/iadmin/includes/toolbar.html.php' );



switch ($task) {

  case "new":

    menueasybook::NEW_MENU();

    break;



  case "overview":

    break;



  case "edit":

    menueasybook::EDIT_MENU();

    break;



  case "config":

    menueasybook::CONFIG_MENU();

    break;



  case "about":

    menueasybook::ABOUT_MENU();

    break;



  case "convert":

    menueasybook::CONVERT_MENU();

    break;



  case "convert3.42":

    menueasybook::CONVERT_MENU();

    break;



  case "convertyah":

    menueasybook::CONVERT_MENU();

    break;



  case "language";

    menueasybook::LANG_MENU();

    break;



case "words";

    menueasybook::WORD_MENU();

    break;



case "view";

      $obj = new MENU_Default();

    break;



  default:

   break;

}

?>

