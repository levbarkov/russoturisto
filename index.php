<?php

define( "_VALID_INSITE", 1 );
define ('DIRSEP', DIRECTORY_SEPARATOR);
setlocale(LC_ALL, 'ru_RU.UTF-8');
$iseoname = "[\w.-]";


$base = explode("?", $_SERVER['REQUEST_URI']);
if(count($base) > 1){
	$basevars = explode ("&", $base[1]);
	foreach ($basevars as $basevar) {
		list($k, $v) = explode ("=", $basevar);
		if (! isset($v) || strlen($v) == 0) continue;
		$k = urldecode($k);
		$v = urldecode($v);
		if (strpos($k, '[]') !== false) {
			$k = substr($k, 0, strlen($k) - 2);
			if (! isset($_REQUEST[$k]))
				$_REQUEST[$k] = array();
			$_REQUEST[$k][] = $v;
		}
		elseif (strlen($k) > 0)
			$_REQUEST[$k] = $v;
	}
}

$files_loaded = array();

require_once 'iconfig.php';
require_once 'i24.php';
require_once 'idb.php';

$tstart = getmicrotime();
$reg = new registry();
$reg['db'] = new database($DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix);
$database = &$reg['db'];
$tstart = getmicrotime();
$page_time = array('start' => $tstart, 'timer' => $tstart);
$reg['db']->_debug = $reg['sql_log'];

require_once 'external_functions.php';
require_once 'imail.php';
require_once 'isef.php';
require_once 'icore.php';
require_once 'table_parser.php';
require_once 'seo.php';

Api::init();

$reg['promo'] = new promo ();
ilog::vlog("######## АДРЕС ЗАГРУЖАЕМОЙ СТРАНИЦЫ: {$sefname1} #######");


if(!isset($reg['filter_level']))
    $reg['filter_level'] = 'MAX';

$filter = new filter($reg['filter_level']);
$_REQUEST = $filter->go($_REQUEST);
$_POST    = $filter->go($_POST);
$_GET     = $filter->go($_GET);

$mainframe = new mosMainFrame($database, '', '.'); //занимает много времени
$mainframe->initSession();
$reg['my'] = $mainframe->getUser();
$my = &$reg['my'];
$pi = get_pi();
$icom = get_icom($pi);


$site_statistics = new site_statistics();
$site_statistics->SearchEngineStatisticSave();
$site_statistics->DayStatisticSave();

session_start();

foreach ($_REQUEST as $ireq => $ireqvalue){
	$iprefix = substr($ireq, 0, 8 ); // icsmart_
	if (strcmp($iprefix, "icsmart_") == 0){
		setcookie("c_".$ireq, $ireqvalue);
		$_SESSION["c_".$ireq] = $ireqvalue;
	}
}


if (isset($_REQUEST['4ajax'])) {
    $found = false;
    if (! isset($server_aliases) || sizeof($server_aliases) == 0)
        $server_aliases = array(substr(site_url, 7));

    foreach ($server_aliases as $alias) {
        if (strpos($_SERVER['HTTP_REFERER'], $alias) !== false) {
            $found = true;
            break;
        }
    }

    if ($found)
        $reg['4ajax'] = 1;
    else {
        $reg['4ajax'] = 0;
        unset($_REQUEST['4ajax']);
    }
}
else
    $reg['4ajax'] = 0;

$mycart = new mycart();
$mycart->load();	//ggd ($mycart->mycart);
$mycart->maketask( mb_strtolower(ggrr('mycart_task'), "UTF-8") ); 

$mylist = new mylist();
$mylist->maketask( mb_strtolower(ggpr('mylist_task'), "UTF-8") );

if (strcmp($icom, "in") == 0){
	if (isset($_REQUEST['4ajax_login'])  ){	
		$mainframe->ilogin();
		return;
	} else 	{
		$mainframe->ilogin();
		mosRedirect( $_REQUEST['return'], "" );
	}
}
else if (strcmp($icom, "out") == 0){
	if (isset($_REQUEST['4ajax'])){	
		$mainframe->ilogout();
		return; 
	} else {
		$mainframe->ilogout();
		mosRedirect( $_REQUEST['return'], ''); 
	}
}

//  section for coalition
$reg['pi'] = $Itemid = $pi;
$mosConfig_absolute_path = site_path;
$mosConfig_dbprefix = $DBPrefix;
$mosConfig_list_limit = $iConfig_list_limit;
$mosConfig_live_site = site_url;
$reg['c'] = $option = $icom;
$reg['task'] = $task = strval( mosGetParam( $_REQUEST, 'task', '' ) );
$reg['iadmin'] = 0;
$mosConfig_lang = $ilang;
//  section for coalition


$ilang_file = site_path."/ilang/".$ilang.".php";
require_once( $ilang_file );

$doeditor = false;

require_once( site_path . '/editor/editor.php' );
if ($doeditor){
	$ieditor_loaded = 0;
	iLoadEditor('onInitEditor_for_site_users');
}



get_mainobj();


$itheme = get_theme();		
$itheme_file = site_path."/theme/".$itheme->theme."/index.php";
if (isset($_REQUEST['4ajax'])) {
	ib();
}
elseif (isset($_REQUEST['4print'])){
	$itheme_file = site_path."/theme/print/index.php";
	require_once( $itheme_file );
}
elseif (isset($_REQUEST['4ajax_module'])){
	ims(ggrr('4ajax_module'));
}
else{
	require_once( $itheme_file );
	require_once("shadow_cron.php");
}
//	do_ipstat();
//	do_access();
show_debug_info(); 