<?php
// если необходимо чтобы был форматирован создаваемый код вставьте \n в код в строке 300
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

function JSCookMenu_counter( ) {
  static $JSCM_count = 0;
  return (++$JSCM_count);
}

class JSCookMenu {

  var $aMain = array();
  var $aSub = array();
  var $bItems = true;
  var $bNameAsStatus = false;
  var $bUseImages = false;
  var $bProduceHtml = false;
  var $sMenuType;
  var $sModulePath;
  var $sOptionImagePath;
  var $sOptionImagePathRel;
  var $sMenuImagePath;
  var $sMenuImagePathRel;
  var $sTheme;
  var $sThemePath;
  var $sOrientation;
  var $sScript;
  var $sHtml;
  var $sFolderImageMain;
  var $sFolderImageSub;
  var $iMenuNumber;
  var $iLeadingImageWidth = false;
  var $iLeadingImageHeight = false;
  
  var $bBufferedBody = false;

  /* Constructor
  * @param array
  * @return void
  */
  function JSCookMenu($menu, $menuType = 'mainmenu') {
	  global $mosConfig_live_site;

    $this->sFolderImageMain = $this->sFolderImageSub = '';
    $this->sMenuType = $menuType;
    $this->sModulePath = $mosConfig_live_site . '/modules/jsCookMenu2/';
	  $this->sOptionImagePathRel = 'images/insite/';
    $this->sMenuImagePathRel = 'modules/jsCookMenu2/images/';
	  $this->sOptionImagePath = $mosConfig_live_site . '/' . $this->sOptionImagePathRel;
	  $this->sMenuImagePath = $mosConfig_live_site . '/' . $this->sMenuImagePathRel;
    $this->setTheme();
    $this->setOrientation();
    $this->iMenuNumber = JSCookMenu_counter();
    $this->sScript = $this->sHtml = '';
    if(!$menu || !is_array($menu) || count($menu) == 0) {
      $this->bItems = false;
    } else {
  	  foreach ($menu as $row) {
	    	if ($row->parent) {
  	  		if (!array_key_exists( $row->parent, $this->aSub )) {	$this->aSub[$row->parent] = array(); }
			    $this->aSub[$row->parent][] = $row;
    		} else {
          $this->aMain[] = $row;
        }
      }
	  }
  } // end method JSCookMenu

/********************/
/*  PUBLIC Methods  */
/********************/

// SETTERS ...

  /* Set theme
  * @param string
  * @return void
  */
  function setTheme($theme = 'ThemeIE') {
    $this->sTheme = $theme;
    $this->sThemePath = $this->sModulePath . $this->sTheme;
  } // end method setTheme

  /* Set orientation
  * @param string
  * @return void
  */
  function setOrientation($orientation = 'vbr') {
    $this->sOrientation = $orientation;
  } // end method setTheme

  /* Set boolean indcating use of images
  * @param boolean
  * @return void
  */
  function setUseImages($useImages = true) {
    $this->bUseImages = (bool)$useImages;
  } // end method setUseImages
  
  /* Set boolean indicating use of name in status line rather than url
  * @param boolean
  * @return void
  */
  function setNameAsStatus($nameAsStatus = true) {
    $this->bNameAsStatus = (bool)$nameAsStatus;
  } // end method setNameAsStatus

  /* Set boolean indicating (hidden) HTML unordered list should also be produced
  * @param boolean
  * @return void
  */
  function setProduceHtml($produceHtml = true) {
    $this->bProduceHtml = (bool)$produceHtml;
  } // end method setProduceHtml

  /* Set boolean indcating template buffers main body HTML before calling mosShowHead (see throwMenu())
  * @param boolean
  * @return void
  */
  function setBufferedBody($bufferedBody = true) {
    $this->bBufferedBody = (bool)$bufferedBody;
  } // end method setUseImages

  /* Sets the main and sub menu folder images
  * @param array
  * @return void
  */
  function setFolderImages($images) {
    $this->sFolderImageMain = $this->sFolderImageSub = '';
    if(is_array($images) && isset($images['main'][0]) && isset($images['main'][1]) && isset($images['sub'][0]) && isset($images['sub'][1])){
      if($images['main'][0] != '' || $images['main'][1] != '') {
        $this->sFolderImageMain = $this->_setFolderImage($images['main'][0], $images['main'][1]);
      }
      if($images['sub'][0] != '' || $images['sub'][1] != '') {
        $this->sFolderImageSub = $this->_setFolderImage($images['sub'][0], $images['sub'][1]);
      }
    }
  }

  /* Sets the width and height resizing values for leading menu images
  * @param integer
  * @param integer
  * @return void
  */
  function setImageResize($width, $height) {
    if($width > 0 && $height > 0){
      $this->iLeadingImageWidth = $width;
      $this->iLeadingImageHeight = $height;
    }
  } // end method setImageResize
  
  /* Creates the menu structure
  * @return void
  */
  function buildMenu( ) {
    $this->sScript = $this->sHtml = '';
    if($this->bItems) {
      $this->_recurseMenu($this->aMain, true);
      $this->sScript =  ltrim($this->sScript,"\n, ");
      $this->sScript = "\nvar jsCookMenu{$this->iMenuNumber} = [\n  {$this->sScript}\n];";
      $this->sHtml =  $this->bProduceHtml ? "\n<ul style='display:none;'>{$this->sHtml}\n</ul>" : '';
    }
    $this->sHtml = "\n<div id='jsCookMenuID{$this->iMenuNumber}'>{$this->sHtml}\n</div>";
  } // end method buildMenu

// GETTERS ...

  /* Output menu
  * @return void
  */
  function throwMenu( ) {
    global $mainframe;
    /* Ideally, all the css and script (with the exception of tha 'cmDraw()' function call) should go inside the <head></head> tags,
       using something like $mainframe->addCustomHeadTag(). The problem is that the default rhuk template (on which others are based)
       calls mosShowHead() in the natural flow order, not at the end of processing all other modules/components and using output
       buffers, so anything added to the head using this method never gets a chance to be used. So, we have to put script in-line,
       and use more script to get the css into the head. */
    /* I've added a switch in this class (see setBufferedBody()) which will enable the use of mainframe->addCustomHeadTag(). */

  	echo "\n<!-- START module jsCookMenu ({$this->sMenuType}) -->";
    echo $this->sHtml;
    if($this->bBufferedBody){
      ob_start();
    }
    if($this->bItems) { /* this section could/should go inside the <head></head> tags */
      if(!$this->bBufferedBody || $this->iMenuNumber == 1) { // this restricts a multiple-menu page to just one copy of the script
        echo "\n<script type='text/javascript' src='{$this->sModulePath}JSCookMenu.js'></script>";
      }
      if($this->bBufferedBody) {
        echo "\n<link type='text/css' rel='stylesheet' href='{$this->sThemePath}/theme.css' />";
        echo "\n<script type='text/javascript'>\n<!--\n";
      } else {
        echo "\n<script type='text/javascript'>\n<!--\n";
        echo "var link = document.createElement('link');";
        echo "\nlink.setAttribute('href', '{$this->sThemePath}/theme.css');";
        echo "\nlink.setAttribute('rel', 'stylesheet');";
        echo "\nlink.setAttribute('type', 'text/css');";
        echo "\nvar head = document.getElementsByTagName('head').item(0);";
        echo "\nhead.appendChild(link);\n";
      }
      include ($this->sTheme . '/theme.js.php');
      echo $this->sScript;
      if($this->bBufferedBody) {
        echo "\n//-->\n</script>";
      }
    }
    if($this->bBufferedBody){
      $html = ob_get_clean();
      $mainframe->addCustomHeadTag($html);
    }
    if($this->bItems) {
      if($this->bBufferedBody) {
        echo "\n<script type='text/javascript'>\n<!--";
      }
      echo "\ncmDraw ( 'jsCookMenuID{$this->iMenuNumber}', jsCookMenu{$this->iMenuNumber}, '{$this->sOrientation}', cm{$this->sTheme}{$this->iMenuNumber}, '{$this->sTheme}' );";
      echo "\n//-->\n</script>";
    }
    echo "\n<!-- END module jsCookMenu ({$this->sMenuType}) -->\n";
  } // end method throwMenu

/*********************/
/*  PRIVATE Methods  */
/*********************/
  /* Gets the image for the menu option (if there is one)
  * @param string
  * @return string
  */
  function _getImage($params){
    global $mosConfig_live_site;
//    $menu_params = new stdClass();
		$menu_params = new mosParameters( $params );
		$menu_image = $menu_params->def( 'menu_image', -1 );
		if ( $menu_image && $menu_image != '-1' ) {
      $attr = ($this->iLeadingImageWidth !== false) ? $this->_resizeOptionImage($menu_image) : '';
			return "'<img src=\"{$this->sOptionImagePath}$menu_image\" border=\"0\" alt=\"\" $attr />'";
	  } else {
      return 'null';
	  }
  } // end method _getImage

  /* Set the script for one option or placeholder; return false if its a separator
  * @param array
  * @param boolean
  * @return booean true if option, false if separator
  */
  function _getOption(&$row, $bHasSub){
    global $mainframe;

    $return = true;
		if ( !$bHasSub && $row->type == 'separator' && str_replace(array(' ','-'), '', $row->name) == '' ) {
      // its an unnamed separator with no sub-menu ...
      $this->sScript .= "\n, _cmSplit";
      $return = false;
    } else {
 			$name = addslashes( $row->name );
 			$alt = $this->bNameAsStatus ? "'" . addslashes( html_entity_decode($row->name) ) . "'" : 'null';
 			$image = $this->bUseImages ? $this->_getImage($row->params) : 'null';
	  	$link = $row->link ? $row->link : 'null';
      // Add Itemid if needed ...
      if($link != 'null' && !eregi('pi=', $link)) {
        // this (partially) emulates mod_mainmenu - whether it should or not remains to be seen!
    		switch ($row->type) {
    			case 'separator': // I don't think this should ever get here, but just in case
    			case 'url': // mod_mainmenu adds Itemid if link contains 'index.php?' and no Itemid, which means you could be screwing up a valid external URL! So I'm going to leave it alone
		    	case 'component_item_link': // ?? should this be left alone, or have an Itemid appended? Not sure so follow mod_mainmenu's lead for now
      			break;
          case 'content_item_link':
          case 'content_typed':
            $menuparams = new mosParameters( $row->params, $mainframe->getPath( 'menu_xml', $row->type ), 'menu' );
              $link .= '&pi='. $row->id;
            break;
/*.../Wizzud v1.3*/
    			default:
		      	$link .= '&pi='. $row->id;
			      break;
    		}
 	  	}
 	  	if($link == 'null') {
        $link_sef = $link_sef_html = $link;
      } else {
/*Wizzud v1.2: don't sef if link begins 'http'. Replaced...
        $link_sef = sefRelToAbs($link);
...with...*/
        $link_sef = preg_match('/^http/i',$link) > 0 ? $link : sefRelToAbs($link);
/*.../Wizzud v1.2*/
/*Wizzud v1.3: on the HTML version, remove slashes from escaped characters. Replaced...
        $link_sef_html = "'" . ampReplace($link_sef) . "'";
...with...*/
        $link_sef_html = "'" . stripslashes(ampReplace($link_sef)) . "'";
/*.../Wizzud v1.3*/
        $link_sef = "'" . $link_sef . "'";
      }
  		$target = ($link == 'null') ? $link : ( ($row->browserNav) ? "'_blank'" : "'_self'" );
 			$this->sScript .= ",[$image, '$name', $link_sef, $target, $alt";
      $this->sHtml .= "<li>" . ($link_sef_html == 'null' ? '' : "<a href=$link_sef_html>") . $name . ($link_sef_html == 'null' ? '' : '</a>');
 	  }
 	  return $return;
  } // end method _getOption

  /* Goes through each menu/submenu evaluating the options
  * @param array
  * @param boolean
  * @return void
  */
  function _recurseMenu(&$thisMenu, $top = false) {
	  foreach ($thisMenu as $row) {
      if (!$top || ($top && $row->parent == 0)) {
        $bHasSub = array_key_exists($row->id, $this->aSub);
        if($this->_getOption($row, $bHasSub)) {
  	  		if ($bHasSub) {
            $this->sHtml .= "\n<ul>";
            $this->_recurseMenu($this->aSub[$row->id]);
            $this->sHtml .= "\n</ul>";
          }
          $this->sScript .= ']';
          $this->sHtml .= '</li>';
        }
      }
    }
  } // end method _recurseMenu

  /* Sets the folder image HTML
  * @param string
  * @param string
  * @return string
  */
  function _setFolderImage($up, $over) {
    if($up == $over) {
      $return = '<img alt="" src="' . $this->sMenuImagePath . $up . '" />';
    } else {
      $attr1 = $attr2 = '';
      if($up == '') {
        $up = 'blank.gif';
        $attr1 = $this->_getImageAttributes($over);
      } elseif($over == '') {
        $over = 'blank.gif';
        $attr2 = $this->_getImageAttributes($up);
      }
      $return  = '<img class="seq1" alt="" src="' . $this->sMenuImagePath . $up . '" ' . $attr1 . ' />';
      $return .= '<img class="seq2" alt="" src="' . $this->sMenuImagePath . $over . '" ' . $attr2 . ' />';
    }
    return $return;
  } // end method _setFolderImage

  /* Gets image attributes
  * @param string
  * @return string
  */
  function _getImageAttributes($match) {
    if(function_exists('getimagesize') && ($size = @getimagesize($this->sMenuImagePathRel . $match)) !== false) {
      return $size[3];
    } else {
      return '';
    }
  } // nd method _getImageAttributes

  /* Provides attribute string for resizing - if neccessary - the option's leading image
  * @param string
  * @return string
  */
  function _resizeOptionImage($image) {
    $return = '';
    if(function_exists('getimagesize') && ($size = @getimagesize($this->sOptionImagePathRel . $image)) !== false) {
      list($w, $h) = $size;
      if($w > 0 && $h > 0) {
        $ratio = min( ($this->iLeadingImageWidth / $w), ($this->iLeadingImageHeight / $h) );
        $newWidth = floor($w * $ratio);
        $newHeight = floor($h * $ratio);
        $return = 'width="' . $newWidth .'" height="' . $newHeight . '"';
      }
    }
    return $return;
  } // end method _resizeOptionImage

}	// end class JSCookMenu
?>
