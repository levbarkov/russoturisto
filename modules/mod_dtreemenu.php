<?php /* $Id: mod_dtreemenu.php */
/**
* dTree Main menu
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @ Created by Winfred van Kuijk <winfred@vankuijk.net>
* @ Uses dTree Javascript: http://www.destroydrop.com/javascripts/tree/
* @ version $Revision: 1.11 $
**/

$debug=0;
$xdebug=0;
global $database;
// testing
// ----------------------------------------------
//$params->useLines = 0;
//$params->useIcons = 0;
//$params->openAll = 1;
//

//$params->menutype = "mainmenu,doesnotexist, \"usermenu\", First MyMenu";
//$params->menutype = "mainmenu";
//$params->menutype = "usermenu";
//$params->menutype = "First MyMenu";

//$params->base = "first";
//$params->base = "menu";
//$params->base = "module";
//$params->base = "site";
//$params->base = "text";
//$params->basetext = "Testing...";

//$params->separator = "<HR>";

// ----------------------------------------------


//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// read module parameters (dTree API), use default values if not set

$params->def = mosParseParams( $module->params );

$autoGenCategories = $params->def('generate_category_items', '');
$useSelection =   $params->def('useSelection', 1);
$useLines =       $params->def('useLines', 1);
$useIcons =       $params->def('useIcons', 1);
$useStatusText =  $params->def('useStatusText', 0);
$closeSameLevel = $params->def('closeSameLevel', 0);

// if all folders should be open, we will ignore the closeSameLevel
$openAll = $params->def('openAll', 0);
if ($debug) {echo "openall: $openAll<br>\n";}
if ( $openAll ) { $closeSameLevel = 0; }

// what menu's to read? Parameter "menutype" contains 0, 1 or more menu's
// Default value: only mainmenu
$menus =  explode(",",$params->def('menutype',array("mainmenu")));
$count_menus = count($menus); 

if ($debug) {echo "# of menus: $count_menus<br>\n";}
if ($debug) {print_r($menus)."<br>\n";}

// After the first menu has been displayed we can print the separator
// At the end of the main loop (=successful print of menu), we'll set it to 1
$first_menu_has_been_printed=0;

$content = "";

// start main loop, process each of the menu's
foreach ($menus as $current_menu_key => $menu) {
$menu = trim($menu, "\" ");

// use prettier names than "mainmenu" or "usermenu"
// for the MyMenu's: just use the official name
switch ($menu) {
	case "mainmenu":
		$menuname = "Main Menu";
		break;
	case "usermenu":
		$menuname = "User Menu";
		break;
	default:
		$menuname = $menu;
}

if ($debug) {echo "====================<br>\n";}
if ($debug) {echo "menu: [$menu]<br>\n";}
if ($debug) {echo "current_menu_key: ".$current_menu_key."/".$count_menus."<br>\n";}

// read remaining (non-dtree API) parameters

// what should be used as the base of the tree?
// ( could be *first* menu item, *site* name, *module*, *menu* name or *text* )
$base = $params->def('base',"menu");
// in my case, for the main menu I always want "Home" to be the base node
$base = (!@$params->def('base') && $menu == "mainmenu") ? "first" : $base;
// in case *text* should be the base node, what text should be displayed?
$basetext = $params->def('basetext', $menuname);
// if there's more than one menu, what should be used as separator?
// the rule used: if not specified it will be moduleheading
// (only if module->showtitle is on)
/*
if (!@$params->def('separator') && $module->showtitle!=0) {
	$title = $module->title;
	$separator = <<<EOT
<P><table cellspacing="0" cellpadding="1" align="center" class="moduletable">
<tr><th valign="top">$menuname</th></tr></table>

EOT;
} else {
	//$separator =  $params->def('separator', "<P>");
	$separator =  $params->def('separator', "[css: moduleheading]");
	$separator = trim($separator, "\" ");
}
*/

// what item is selected?
$Itemid = mosgetParam( $_REQUEST, 'pi', 0 );

if ($debug) { echo "gid: ".$my->gid."<br>\n"; }

// select menu items from database
/*
$res=ggsqlr( "SELECT m.* FROM #__menu AS m"
	. "\nWHERE menutype='$menu' AND published=1 AND access <= $my->gid"
	. "\nORDER BY parent,ordering,sublevel"
        );
*/

$res=ggsqlr( "SELECT m.* FROM #__menu AS m"
. "\nWHERE menutype='$menu' AND published=1"
. "\nORDER BY parent,ordering,sublevel"
);

$res = $database->query();
//ggtr ($res);
// how many menu items in this menu?
//$row=count($res);
$row = $database->getNumRows($res);
// if there are no menu items: skip to the next menu in the list
if ($debug) { echo "Number of rows: $row<br>\n"; }
if ($row == 0) { continue; } 

if ($first_menu_has_been_printed && $params->def('use_separator',1)) {
	$content .= $separator;
}

$rows = $database->loadObjectList();
//$rows = $database->loadObjectList( 'id' );

echo $database->getErrorMsg();

if ($xdebug) { echo $database->explain(); }

$first_row = $rows[0];

// create a unique tree identifier, in case multiple dtrees are used 
// (max one per module)
$tree = "d".$module->id."_".$first_row->id;

if ($debug) {echo "tree: $tree<br>\n";}



// start creating the content
// create left aligned table, load the CSS stylesheet and dTree code
$content .= "<table xwvk border=0 cellspacing=0 cellpadding=0 width=\"100%\"><TR><TD align=left>\n";
$content .= "<link rel=\"StyleSheet\" href=\"".site_url."/modules/dtree/dtree.css\" type=\"text/css\" />\n";
$content .= "<script type=\"text/javascript\" src=\"".site_url."/modules/dtree/dtree.js\"></script>\n";
$content .= "<script type=\"text/javascript\">\n";

// create the tree, using the unique name
// pass the live_site parameter on so dTree can find the icons
$content .= "$tree = new dTree('$tree',\"".site_url."\");\n";

// would think the tree is in order, but testing seems 
// to think otherwise, so leave out for now
//$content .= "$tree.config.inOrder = true\n";


// pass on the dTree API parameters
$content .= "$tree.config.useSelection=".$useSelection.";\n";
$content .= "$tree.config.useLines=".$useLines.";\n";
$content .= "$tree.config.useIcons=".$useIcons.";\n";
$content .= "$tree.config.useStatusText=".$useStatusText.";\n";
$content .= "$tree.config.closeSameLevel=".$closeSameLevel.";\n";

//ggtr($content);
// what should the name of the first (=base) node be?
// depends on the module parameter, default is that
// first menu item (often: "Home") is the base node
switch ($base) {
	case "module":
                $basename = $module->title;
                break;
        case "menu":
                $basename = $menuname;
                break;
        case "text":
                $basename = $basetext;
                break;
        case "site":
                $basename = site_url;
                break;
        case "first":
        default:
                $basename = $first_row->name;
}

// what is the ID of this node?
$baseid = $first_row->parent;
// create the link (if not a menu item, no link [could be: to entry page of site])
//$baselink = $base == "first" ? $first_row->link : '';
$baselink = $base == "first" ? site_url : '';

// remember which item is open, normally $Itemid
// except when we want the first item (e.g. Home) to be the base;
// in that case we have to pretend all remaining items belong to "Home"
$openid = ($base == "first" && $Itemid == $first_row->id) ? $first_row->parent : $Itemid;

// it could be that we are displaying e.g. mainmenu in this dtree, 
// but item in usermenu is selected, 
// so: for the rest of this module track if this menu contains the selected item
// Default value: first node (=baseid), but not selected
$opento = $baseid;
$opento_selected = 0;
// what do you know... the first node was selected
if ($baseid == $openid) { $opento_selected = 1; }

// TODO: following adds "Item" parameter to baselink (=unwanted), so disabled for now
//list($baselink,$target) = check_url_target($baselink, $first_row);

// debug
if ($debug) {
echo "menu: $menu<BR>\n";
echo "menuname: $menuname<BR>\n";
echo "base: $baseid - $basename <BR>\n";
echo "link: $baselink <BR>\n";
echo "itemid: $Itemid<BR>\n";
echo "open: $openid<BR>\n";

echo "#   $baseid -1 - $basename<BR>\n";
}

// create the first node, parent is always -1
$content .= "$tree.add($baseid,-1,\"$basename\",\"$baselink\",\"\",\"$target\")\n";
// process each of the nodes
foreach ($rows as $row) { 

	// first row should only be processed if base <> first
	if ( ($base == "first") && ($row->id == $first_row->id) ) { continue; } 

	// get name and link (just to save space in the code later on)
	$name = $row->name;
	$url = $row->link;

	list($url,$target) = check_url_target($url,$row);

if ($debug) {
echo "#   $row->id $row->parent - $row->name<BR>\n";
}
	$content .= "$tree.add($row->id,$row->parent,
		    \"$name\",\"$url\",\"\",\"$target\")\n";

	// if this node is the selected node
	if ($row->id == $openid) 
		{ $opento = $openid; $opento_selected = 1; }
				
	// Check for auto-generation of Category sub-menu's
	if ($autoGenCategories != "") {
   	
   	// Only process 'Content Category' type menu items
   	if ($row->type == "content_category") {
  
  		// Set up the query to return 'Content Items' for all 'Content Category' type menu items
  		if ($autoGenCategories == "All") {
  			
  			$database->setQuery("SELECT m.* FROM #__content AS m "
  				. "WHERE catid = $row->componentid "
  				. "AND state=1 ORDER BY ordering");
  		} else {
  			
  			// Set up the query to return 'Content Items' only for 'Content Category' type
  			// menu items whose target Category ID's appear in the $autoGenCategories list
  			$database->setQuery("SELECT m.* FROM #__content AS m "
  				. "WHERE catid = $row->componentid "
  				. "AND catid IN($autoGenCategories) "
  				. "AND state=1 ORDER BY ordering");
  		}	
  		
  		// Setup the query
  		$rescat = $database->query();
  		$rowcat = $database->getNumRows($rescat); 	
  
  		// 'Content Items' exist
  		if($rowcat > 0 ) {
  			$catrows = $database->loadObjectList();
  			echo $database->getErrorMsg();
  
  			// Create sub-menu entries
  			foreach ($catrows as $catrow) {
    			$caturl = "index.php?c=content&task=view&id=$catrow->id";		
    			$genId = ($row->id * 100000) + $catrow->id;
    			list($caturl,$target) = check_url_target_auto($caturl, $genId, $target);
    			$content .= "$tree.add($genId,$row->id,\"$catrow->title\",\"$caturl\",\"\",\"$target\")\n";
    			
    		  // if this node is the selected node
					if ($genId == $openid) {
						$opento = $openid;
						$opento_selected = 1;
					}
   			}
  		}
  	}
  }

// end of nodes loop (foreach nodes)
}


if ($debug) {
echo "opento: $opento - $opento_selected<BR>\n";
}



// --------------------------catalog output
$excat_tree='';
$excats=ggsql('SELECT * from #__excat WHERE `publish` AND `parent`=0 ');
//ggtr($excats);
$xx=500;

	foreach ($excats as $excat){
	$excat_link="d20_1.add($xx,0,\n		    \"$excat->name\",\"index.php?c=ex&task=excat&id=$excat->id&pi=300\",\"\",\"\")\n"; 
	$cat_xx=$xx; 
	$xx++; 
	$excat_tree=$excat_tree.$excat_link;		
	
	$par_id=$excat->id;
	$subcats=ggsql("SELECT * from #__excat WHERE `publish` AND `parent`=$par_id ");
		foreach ($subcats as $subcat){
		$subcat_link="d20_1.add($xx,$cat_xx,\n		    \"$subcat->name\",\"index.php?c=ex&task=excat&id=$subcat->id&pi=300\",\"\",\"\")\n"; $xx++;
		$excat_tree=$excat_tree.$subcat_link;		
		}

	//d11_1.add(1,0,   "Главная","/index.php?c=frontpage&pi=1","","")
	}
//----------------------- catalog end
//ggtr ($content, 50);
$content .= $excat_tree;
$content .= "document.write($tree);\n";
$content .= $openAll ? "$tree.openAll();\n" : "$tree.closeAll();\n";
$content .= "$tree.openTo($opento,$opento_selected);\n";
$content .= "</script>\n";
$content .= "</td></tr></table>\n";

$first_menu_has_been_printed=1;
// end of main loop (foreach menu)

echo($content);
}


function check_url_target($url, $row) {
	switch ($row->type) {
		case "separator":
			$url = "";
		case "url":
			if (eregi( "index.php\?", $url )) {
				//$url = "&Returnid=".$row->id;
				if (!eregi( "Itemid=", $url )) {
					$url .= "&Itemid=".$row->id;
				}
			}
			break;
		default:
			$url .= "&pi=".$row->id;
			break;
	}

        $url = str_replace( '&', '&amp;', $url );

        if (strcasecmp(substr($url,0,4),"http")) {
                $url = sefRelToAbs($url);
        }


	//$url = $row->type <> separator ? sefRelToAbs($url) : "";

        switch ($row->browserNav) {
                case 1: // open in a new window
                case 2: // open in a popup (not supported by dTree)
                        $target = "_window";
                        break;
                case 3: // don't link it
                        $url = "";
                default: // open in same window
                        $target = "";
                        break;
	}
	return array($url,$target);
}

// --------------------
// Mangle auto-generated Category Item link URL's
// --------------------
function check_url_target_auto($url, $genId, $target) {
	$url .= "&Itemid=".$genId;

	$url = str_replace( '&', '&amp;', $url );

	if (strcasecmp(substr($url,0,4),"http")) {
  	$url = sefRelToAbs($url);
    }

	return array($url,$target);
}


?>