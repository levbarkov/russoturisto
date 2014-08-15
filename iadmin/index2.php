<?php


// Установка флага, что этот файл - родительский
define( '_VALID_INSITE', 1 );
define ('DIRSEP', DIRECTORY_SEPARATOR);

require_once( '../iconfig.php' );
require_once( '../i24.php' );
require_once( '../external_functions.php' );
require_once( '../idb.php' );
require_once( '../isef.php' );
require_once( '../icore.php' );
require_once( '../imail.php' );
require_once( '../table_parser.php' );
// ggd($_REQUEST);
$reg['filter_level'] = "MIN";
$filter = new filter($reg['filter_level']);
$_REQUEST = $filter->go($_REQUEST);
$_GET = $filter->go($_GET);
$_POST = $filter->go($_POST);

//  section for coalition
$mosConfig_absolute_path = site_path;
$mosConfig_dbprefix = $DBPrefix;
$mosConfig_list_limit = $iConfig_list_limit;
//  section for coalition

$reg = new registry();
$reg['db'] = new database( $DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix );
$database = &$reg['db'];
$tstart = getmicrotime(); $page_time = array( 'start'=>$tstart, 'timer'=>$tstart );  $reg['db']->_debug = $reg['sql_log'];
$mainframe = new mosMainFrame( $database, "" , '.');
iflush::init();

Api::init();

// ЗАГРУЗКА ЯЗЫКОВОГО ФАЙЛА
$ilang_file = site_path."/ilang/".$ilang.".php";
require_once( $ilang_file );

// must start the session before we create the mainframe object
session_name( md5( site_url.$sitename ) );
session_start(); 
$reg['iadmin'] = 1;
$reg['option'] = $reg['ca']	= strval( strtolower( mosGetParam( $_REQUEST, 'ca', '' ) ) );
$reg['task'] 				= strval( mosGetParam( $_REQUEST, 'task', '' ) );
$task = $reg['task'];	$option = $reg['ca'];

// БЫСТРОЕ ПЕРЕКЛЮЧЕНИЕ РЕЖИМА HTML/WYSIWYG
if (  isset($_REQUEST['change_mode'])  ){
	$params = new mosParameters( $_SESSION['session_user_params'], '', 'component' );       $newparams="";      $txt = array();       $last_change_mode=ggrr('change_mode');
	if (is_object( $params->_params )) {
		foreach ( $params->_params as $k=>$v) {  //ggtr01 ($k); ggtr01 ($v);
			if (  $k=='editor'){ if (  $_REQUEST['change_mode']=='wysiwyg'  ) $v = 'tinymce';	else $v = 'none'; }
			$txt[] = "$k=$v";
		}
		$newparams = $row->params = implode( "\n", $txt ); // ggd ($newparams);
	}
	if (  $newparams!=''  ){
		$i24r = new mosDBTable( "#__users", "id", $reg['db'] );
		$i24r->id = $_SESSION['session_user_id'];
		$_SESSION['session_user_params'] = $i24r->params = $newparams;
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store(); 
		$_SERVER['QUERY_STRING'] = str_replace("change_mode=$last_change_mode&","",$_SERVER['QUERY_STRING']);
	}
}

// admin session handling
$my = initSessionAdmin( $option, $task );
$reg['my'] = $my;

// обработка данных текущей страницы
if (  isset($_REQUEST['limit'])  ){
	setcookie("c_icsmart_".$option."_limit",  ggri('limit')  );
	$_SESSION["c_icsmart_".$option."_limit"] = ggri('limit');
}
if (  isset($_REQUEST['limitstart'])  ){
	setcookie("c_icsmart_".$option."_limitstart",  ggri('limitstart')  );
	$_SESSION["c_icsmart_".$option."_limitstart"] = ggri('limitstart');
}
// здесь необходимо сохранить все объекты в сессии
foreach ($_REQUEST as $ireq=>$ireqvalue){
	$iprefix = substr($ireq, 0, 8 ); // icsmart_
	if (  strcmp($iprefix, "icsmart_")==0  ){
//		ggtr ($ireqvalue);
		setcookie("c_".$ireq, $ireqvalue);
		$_SESSION["c_".$ireq] = $ireqvalue;
	}
}

// initialise some common request directives
$act 		= strtolower( mosGetParam( $_REQUEST, 'act', '' ) );
$section 	= mosGetParam( $_REQUEST, 'section', '' );
$no_html 		= intval( mosGetParam( $_REQUEST, 'no_html', 0 ) );
$id         	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );

// default admin homepage
if ($option == '') {
	if (  $my->gid==23)	$option = 'shopmanager';
	else $option = 'admin';
}


// инициализация автосохранения по ctrl+Enter
$ctrlEnter = new ctrlEnter();
$ctrlEnter->go();

// precapture the output of the component
require_once( site_path . '/editor/editor.php' );
$ieditor_loaded = 0;
//ob_start();
//$_MOS_OPTION['buffer'] = ob_get_contents();
//ob_end_clean();
//initGzip();

// ЗАГРУЗКА ШАБЛОНА
if (  isset($_REQUEST['4ajax'])  ) 	{  iMainBody_Admin();	}
else 								{  $itheme_file = site_path."/iadmin/theme/".$adminTheme."/index.php";	require_once( $itheme_file );	}



//doGzip();

// if task action is 'save' or 'apply' redo session check
if ( $task == 'save' || $task == 'apply' ) {
	initSessionAdmin( $option, '' );
}
?>