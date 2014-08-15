<?php
defined( '_VALID_INSITE' ) or die( 'Restricted access' );

global $database, $my, $mainframe, $mosConfig_absolute_path;

$jscmDirectoryPath = $mosConfig_absolute_path . "/modules/jsCookMenu2/";

$bufferedBody = defined('_JSCOOKMENU_BUFFEREDBODY'); 

// Load class and functions...
include_once( $jscmDirectoryPath . 'JSCookMenu.class.php' );

// Parameters ...
$theme = $params->def('theme','ThemeIE');
$orientation = $params->def('orientation','hbr');
$menu = $params->def('menu','mainmenu');
$order = $params->def('ordering','ordering');
$img = array('main'=>array(), 'sub'=>array());
$img['main'][0] = $params->def('img_main','');
$img['main'][1] = $params->def('img_main_hover','');
$img['sub'][0] = $params->def('img_sub','');
$img['sub'][1] = $params->def('img_sub_hover','');
$useImages = ( $params->def('menu_images','n') == 'y' );
$statusName = ( $params->def('status_text','y') == 'y' );
$produceHtml = ( $params->def('html_list','y') == 'y' );
$imageResize = $params->def('image_resize','');

// Error Checking of parameter values, incorrect values loads default values
// Check theme ...
$validThemes = mosReadDirectory($jscmDirectoryPath, '^Theme');
if(!in_array($theme, $validThemes)) $theme = 'ThemeIE';
// Check order ...
if(!in_array($order, array('ordering', 'name', 'id', 'type'))) $order = 'ordering';
// Check orientation ...
if(!in_array($orientation, array('hbr', 'hur', 'hbl', 'hul', 'vbr', 'vur', 'vbl', 'vul'))) $orientation = 'vbr';
// Check image resize ...
if($useImages && preg_match('/^([\d]+)x([\d]+)$/',$imageResize,$match) > 0 && (int)$match[1] > 0 && (int)$match[2] > 0) {
  $imageResize = true;
  $resizeWidth = (int)$match[1];
  $resizeHeight = (int)$match[2];
} else {
  $imageResize = false;
}

// Load menu items ...
$sql = "SELECT m.*"
     . "\n FROM #__menu AS m"
     . "\n WHERE menutype = '$menu'"
     . " AND published = '1'"
     . "\n ORDER BY $order";
$database->setQuery( $sql );
$menus = $database->loadObjectList( );

// Output ...
$oJSCM = new JSCookMenu($menus, $menu);
$oJSCM->setTheme($theme);
$oJSCM->setOrientation($orientation);
$oJSCM->setNameAsStatus($statusName);
$oJSCM->setUseImages($useImages);
if($imageResize) $oJSCM->setImageResize($resizeWidth, $resizeHeight);
$oJSCM->setProduceHtml($produceHtml);
$oJSCM->setBufferedBody($bufferedBody);
$oJSCM->setFolderImages($img);
$oJSCM->buildMenu();
$oJSCM->throwMenu();
/* ------------------- End of Module ------------------- */
?>
