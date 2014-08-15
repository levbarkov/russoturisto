<?php
define( "_VALID_INSITE", 1 );
define ('DIRSEP', DIRECTORY_SEPARATOR);
setlocale(LC_ALL, 'ru_RU.UTF-8');
$iseoname = "[\w.-]";

// необходимо восстановить значения  для $_REQUEST, из-за SEO они теряются
$base = explode("?", $_SERVER['REQUEST_URI']); $basevars = array(); parse_str($base[1],$basevars);
foreach ($basevars as $var_name=>$basevar){    $_REQUEST[$var_name] = $basevar;   }
$files_loaded = array(); // Специальный массив для исключения загрузки повторяющихся переменных

require_once( 'i24.php' );
require_once( 'iconfig.php' );
require_once( 'idb.php' );	$reg = new registry(); $reg['db'] = new database( $DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix ); $database = &$reg['db'];     $tstart = getmicrotime(); $page_time = array( 'start'=>$tstart, 'timer'=>$tstart );  $reg['db']->_debug = $reg['sql_log'];
require_once( 'external_functions.php' );
require_once( 'imail.php' );
require_once( 'isef.php' );
require_once( 'icore.php' );
require_once( 'table_parser.php' );
require_once( 'seo.php' );

// ГЛОБАЛЬНАЯ ЗАЩИТА ВСЕХ ВХОДНЫХ ДАННЫХ
if(!isset($reg['filter_level']))  $reg['filter_level'] = "MAX";
$filter = new filter($reg['filter_level']);
$_REQUEST = $filter->go($_REQUEST);
$_POST = 	$filter->go($_POST);
$_GET = 	$filter->go($_GET);

$mainframe = new mosMainFrame( $database, "" , '.');
$mainframe->initSession();
$reg['my'] = $mainframe->getUser();     $my = &$reg['my'];

?>