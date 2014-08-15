<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

require_once( site_path. '/includes/phpInputFilter/class.inputfilter.php' );
require_once( site_path. '/ihtml.php' );
require_once( site_path. '/itabs.php' );
require_once( site_path. '/icommonhtml.php' );
require_once( site_path. '/iuser.php' );
require_once( site_path. '/icontent.php' );
require_once( site_path . '/insite.xml.php' );
require_once( site_path . '/iservice.php' );
require_once( site_path . '/isection.php' );

function get_theme(){
	// var_dump ($_REQUEST);
	global $reg;
	$component = $reg['c'];
	$themes = ggsql ("select * from #__theme_config order by `ordering`");
	foreach ($themes as $theme){
		if ($theme->type == 0){
			
			if ($theme->val == $component){
				if ($theme->ext_file != ''){
					if (file_exists(site_path . "/theme/theme_extfiles/" . $theme->ext_file)){
						$ext_fileresult = false;
						require_once(site_path . "/theme/theme_extfiles/" . $theme->ext_file);
						if ($ext_fileresult == true)
							return $theme;
						else
							continue;
					}
				}
				else
					return $theme;
			}
		}
		elseif($theme->type == 1){
			
			$theme->val = html_entity_decode($theme->val);
			if (preg_match("~{$theme->val}~Usi", $_SERVER['REQUEST_URI'])){
				if ($theme->ext_file != ''){
					if (file_exists(site_path . "/theme/theme_extfiles/" . $theme->ext_file)){
						$ext_fileresult = false;
						require_once(site_path . "/theme/theme_extfiles/" . $theme->ext_file);
						if ($ext_fileresult == true)
							return $theme;
						else
							continue;
					}
				}
				else
					return $theme;
			}
		}
		elseif ($theme->type == 2){
			if ($theme->ext_file != ''){
				if (file_exists(site_path."/theme/theme_extfiles/".$theme->ext_file)){
					$ext_fileresult = false;
					require_once(site_path . "/theme/theme_extfiles/" . $theme->ext_file);
					if ($ext_fileresult == true)
						return $theme;
					else
						continue;
				}
			}
			else
				return $theme;
		}
	}
}

function get_pi(){
	$pi = isset ($_REQUEST['pi']) ? $_REQUEST['pi'] : 0;
	$post_is_empty = true;
	foreach ($_POST as $postrow => $postval){
		if(strcmp($postrow, 'c') == 0){
			$post_is_empty = false;
			break;
		}
	}
	if (  $post_is_empty && strcmp($_SERVER['QUERY_STRING'],"")==0  &&  $pi==0  ) $pi=1;			// ГЛАВНАЯ СТРАНИЦА
	return $pi;
}

function get_icom($pi){
	if (  $pi==1  ){	// ГЛАВНАЯ СТРАНИЦА  ГРУЗИМ ЕЕ КОМПОНЕНТ
		return "frontpage";
	}
	$icomp_file = site_path."/component/".ggrr('c')."/".ggrr('c').".php";
	// ПРОВЕРКА СТОИТ ЛИ ДАНЫЙ КОМПОНЕНТ ЗАПУСКАТЬ
	// ggtr ($_REQUEST['c']); ggtr ($icomp_file);
	if (  $_REQUEST['c']=='seo'  ){ 
		return "nopage"; 
	}
	else if (  isset($_REQUEST['c'])  ){
		
		if(  !file_exists(  $icomp_file  )  )	return "nopage";
	}
	$icom = isset ($_REQUEST['c']) ? $_REQUEST['c'] : "nopage";
	return $icom;
}

function im($pos, &$params=0){	// ЗАГРУЗКА МОДУЛЯ
	$modules = ggsql("SELECT * FROM #__modules WHERE position='$pos' AND published=1 ORDER BY ordering");
	foreach ($modules as $module){
		$imodule_file = site_path."/modules/".$module->module.".php";
		// ПРОВЕРКА СТОИТ ЛИ ДАНЫЙ МОДУЛЬ ЗАПУСКАТЬ
		$params = new mosParameters( $module->params );
//		ggtr ($module->imodparam);
		if ( strlen($module->module)>0)require_once( $imodule_file );
		else print desafelysqlstr($module->content);		
	}
}

function getIm($pos, &$params = 0){
	$modules = ggsql("select * from #__modules where `position`='{$pos}' AND published = 1 order by `ordering`");
	foreach ($modules as $module){
		$imodule_file = site_path . "/modules/" . $module->module . ".php";
		// ПРОВЕРКА СТОИТ ЛИ ДАНЫЙ МОДУЛЬ ЗАПУСКАТЬ
		$params = new mosParameters( $module->params );
		if (strlen($module->module) > 0)
			require_once($imodule_file);
		else
			return desafelysqlstr($module->content);		
	}	
}

function getIms($pos){		
	if (is_int($pos)){
		$imodule = ggo ($pos, "#__modules");
		return desafelysqlstr($imodule->content);
	} else {
		if(!file_exists(site_path . "/modules/" . $pos . ".php"))
			return '';
		
		$imodule_file = site_path . "/modules/" . $pos . ".php";
		ob_start();
		require_once($imodule_file);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

/**
 * вызов модуля по его названию или имени файла
 * $pos - название модуля или имя файла ( /modules/имя_файла.php )
 * $params - массив с дополнительными параметрами, таким образом мы можем передавать неограниченное количество параметров
 * поскольку передаем указатель, то модуль может изменять или добавлять новые значения в массив $params
 */
function ims($pos, &$params=NULL){	// ЗАГРУЗКА МОДУЛЯ
        ilog::vlog('{ модуль '.$pos);
        ilog::commentlog('START модуль::'.$pos);
	if (  is_int($pos)  ){
		$imodule = ggo ($pos, "#__modules");
		print desafelysqlstr($imodule->content);
                //ВЫВОД ССЫЛКИ НА РЕДАКТИРОВАНИЕ
                $params  = new mosParameters($imodule->params);
                $show_editme =  $params->def('editme', '1');
                if(  $show_editme==1  ){
                    editme( 'module', array('id'=>$imodule->id), 'small' );
                }
	} else {
		$imodule_file = site_path."/modules/".$pos.".php";
		include( $imodule_file );
	}
        ilog::vlog('модуль '.$pos.' }');
        ilog::commentlog('END модуль::'.$pos);
}
/**
 * ЗАГРУЗКА СТИЛЕЙ, JAVASCRIPT-БИБЛИОТЕК И ИНИЦИАЛИЗАЦИЯ КОМПОНЕНТА
 * Смотрит наличие файла head.php в директории компонента и запускает его
 */
function ib_header(){
	global $option;
	$header_path = "/component/".$option."/head.php";
	if (  $option=='seo'  ) return;
	if (  file_exists( site_path.$header_path )  ){
            ?><!-- <?=$option ?> HEAD START --><?php
            require_once( site_path.$header_path );
            ?><!-- <?=$option ?> HEAD END --><?php
        }
}
/**
 *  ЗАГРУЗКА РАБОЧЕЙ ОБЛАСТИ
 */
function ib(){	
global  $icom, $reg;
        ilog::commentlog('START компонент::'.$reg['c']);
        ilog::vlog('{ компонент '.$reg['c']);
        ilog::vlog('task='.$reg['task']);
	$imodule_file = site_path."/component/$icom/$icom.php";
	// var_dump($imodule_file);
                                                                /*
                                                                 * КЛАСС mceContentBody - обязательно должен быть, относительно этого класа делается файл стилей,
                                                                 * чтобы добиться одинакового представления в админке и в клиентской частях сайта
                                                                 */
	if (  !isset($_REQUEST['4ajax'])  ){ ?><!--i24_ib_start-->
	
	<div class="mceContentBody"<?php/* если удалил класс mceContentBody - добавь его в верстке */ ?> id="div_mceContentBody"><?php }
		require_once( $imodule_file );
	if (  !isset($_REQUEST['4ajax'])  ){ ?></div><!--i24_ib_end--><?php }
        ilog::vlog('компонент '.$reg['c'].' }');
        ilog::commentlog('END компонент::'.$reg['c']);
	
}
function ipathway(){	// ЗАГРУЗКА ПУТИ
global  $icom;
	$imodule_file = site_path."/component/$icom/pathway.php";
	if(  file_exists($imodule_file  )) require_once( $imodule_file );
}


/*
 *
 * ФУНКЦИЯ js - выводит тег <script type="text/javascript" src="javascript.js"></script>
 * при повторном вызове одного и того-же скрипта тег не генерируется
 *
 * ДЛЯ ИСКЛЮЧЕНИЯ ДВОЙНОГО ВЫЗОВА ОДНИХ И ТЕХ ЖЕ СКРИПТОВ - ВСЕ ВЫЗОВЫ javascript'ов — ЧЕРЕЗ js("javascript.js");
 *
 */
function js($fullname){
    global $files_loaded;
	$file = $fullname;
	if (!isset($files_loaded['javascript_loaded'][$file]) || $files_loaded['javascript_loaded'][$file] == 0){
	    $files_loaded['javascript_loaded'][$file] = 1;
	    ?><script type="text/javascript" src="<?=$fullname ?>"></script><?php echo "\n";
	}
}
/*
 *
 * ФУНКЦИЯ css - выводит тег <link href="file.css" rel="stylesheet" type="text/css"/>
 * при повторном вызове одного и того-же файла css-тег не генерируется
 *
 * ДЛЯ ИСКЛЮЧЕНИЯ ДВОЙНОГО ВЫЗОВА ОДНИХ И ТЕХ ЖЕ ФАЙЛОВ СТИЛЕЙ - ВСЕ ВЫЗОВЫ .css — ЧЕРЕЗ css("file.css");
 *
 */

function css($file){
    global $files_loaded;
    if (!isset($files_loaded['css_loaded'][$file]) || $files_loaded['css_loaded'][$file] == 0) {
        $files_loaded['css_loaded'][$file] = 1;
        print '<link href="' . $file . '" rel="stylesheet" type="text/css"/>'."\n";
    }
}

function scss($fullname) {
    global $files_loaded;

    $file = preg_replace('/scss/', 'css', $fullname);
    if ($files_loaded['css_loaded'][$file] == 0) {
        $generate = false;
        if (! file_exists(site_path . $file))
            $generate = true;
        else {
            clearstatcache();
            if (filemtime(site_path . $fullname) > filemtime(site_path . $file))
                $generate = true;
        }

        if ($generate) {
            require_once ('includes/PHamlP_3.2/sass/SassParser.php');
            $sass = new SassParser(array(
                'cache_location' => site_path . '/theme/scss/cache',
                'css_location' => site_path . '/theme/css'
            ));
            $css = $sass->toCss(site_path . $fullname);
            $f = fopen(site_path . $file, 'w');
            fputs($f, $css);
            fclose($f);
            chmod(site_path . $file, 0777);
        }
        $files_loaded['css_loaded'][$file] = 1;
        print '<link href="' . $file . '" rel="stylesheet" type="text/css"/>';
    }
}
/**
* Initialise GZIP
*/
function initGzip() {
        global $mosConfig_gzip, $do_gzip_compress;
	
        $do_gzip_compress = FALSE;
        if ($mosConfig_gzip == 1) {
                $phpver = phpversion();
                $useragent = mosGetParam( $_SERVER, 'HTTP_USER_AGENT', '' );
                $canZip = mosGetParam( $_SERVER, 'HTTP_ACCEPT_ENCODING', '' );

		$gzip_check 	= 0;
		$zlib_check 	= 0;
		$gz_check		= 0;
		$zlibO_check	= 0;
		$sid_check		= 0;
		if ( strpos( $canZip, 'gzip' ) !== false) {
			$gzip_check = 1;
		}		
		if ( extension_loaded( 'zlib' ) ) {
			$zlib_check = 1;
		}		
		if ( function_exists('ob_gzhandler') ) {
			$gz_check = 1;
		}
		if ( ini_get('zlib.output_compression') ) {
			$zlibO_check = 1;
		}
		if ( ini_get('session.use_trans_sid') ) {
			$sid_check = 1;
		}

		if ( $phpver >= '4.0.4pl1' && ( strpos($useragent,'compatible') !== false || strpos($useragent,'Gecko')	!== false ) ) {
			// Check for gzip header or northon internet securities or session.use_trans_sid
			if ( ( $gzip_check || isset( $_SERVER['---------------']) ) && $zlib_check && $gz_check && !$zlibO_check && !$sid_check ) {
                                // You cannot specify additional output handlers if
                                // zlib.output_compression is activated here
				ob_start( 'ob_gzhandler' );
                                return;
                        }
                } else if ( $phpver > '4.0' ) {
			if ( $gzip_check ) {
				if ( $zlib_check ) {
                                        $do_gzip_compress = TRUE;
                                        ob_start();
                                        ob_implicit_flush(0);

                                        header( 'Content-Encoding: gzip' );
                                        return;
                                }
                        }
                }
        }
        ob_start();
}

/**
* Perform GZIP
*/
function doGzip() {
        global $do_gzip_compress;
        if ( $do_gzip_compress ) {
                /**
                *Borrowed from php.net!
                */
                $gzip_contents = ob_get_contents();
                ob_end_clean();

                $gzip_size = strlen($gzip_contents);
                $gzip_crc = crc32($gzip_contents);

                $gzip_contents = gzcompress($gzip_contents, 9);
                $gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

                echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
                echo $gzip_contents;
                echo pack('V', $gzip_crc);
                echo pack('V', $gzip_size);
        } else {
                ob_end_flush();
        }
}



/**
* Random password generator
* @return password
*/
function mosMakePassword($length=8) {
        $salt                 = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $makepass        = '';
        mt_srand(10000000*(double)microtime());
	for ($i = 0; $i < $length; $i++)
		$makepass .= $salt[mt_rand(0,61)];
        return $makepass;
}

if (!function_exists('html_entity_decode')) {
        /**
        * html_entity_decode function for backward compatability in PHP
        * @param string
        * @param string
        */
        function html_entity_decode ($string, $opt = ENT_COMPAT) {

                $trans_tbl = get_html_translation_table (HTML_ENTITIES);
                $trans_tbl = array_flip ($trans_tbl);

                if ($opt & 1) { // Translating single quotes
                        // Add single quote to translation table;
                        // doesn't appear to be there by default
                        $trans_tbl["&apos;"] = "'";
                }

                if (!($opt & 2)) { // Not translating double quotes
                        // Remove double quote from translation table
                        unset($trans_tbl["&quot;"]);
                }

                return strtr ($string, $trans_tbl);
        }
}

/**
 * Utility function to return a value from a named array or a specified default
 * @param array A named array
 * @param string The key to search for
 * @param mixed The default value to give if no key found
 * @param int An options mask: _MOS_NOTRIM prevents trim, _MOS_ALLOWHTML allows safe html, _MOS_ALLOWRAW allows raw input
 */
define( "_MOS_NOTRIM", 0x0001 );
define( "_MOS_ALLOWHTML", 0x0002 );
define( "_MOS_ALLOWRAW", 0x0004 );
function mosGetParam( &$arr, $name, $def=null, $mask=0 ) {
        static $noHtmlFilter = null;
        static $safeHtmlFilter = null;

        $return = null;
        if (isset( $arr[$name] )) {
		$return = $arr[$name];
		
		if (is_string( $return )) {
			// trim data
                        if (!($mask&_MOS_NOTRIM)) {
				$return = trim( $return );
                        }

                        if ($mask&_MOS_ALLOWRAW) {
                                // do nothing
                        } else if ($mask&_MOS_ALLOWHTML) {
                                // do nothing - compatibility mode
                        } else {
				// send to inputfilter
                                if (is_null( $noHtmlFilter )) {
                                        $noHtmlFilter = new InputFilter( /* $tags, $attr, $tag_method, $attr_method, $xss_auto */ );
                                }
				$return = $noHtmlFilter->process( $return );
				
				if (empty($return) && is_numeric($def)) {
				// if value is defined and default value is numeric set variable type to integer
					$return = intval($return);
				}				
                        }
			
			// account for magic quotes setting
                        if (!get_magic_quotes_gpc()) {
				$return = addslashes( $return );
			}
		}
		
		return $return;
        } else {
                return $def;
        }
}

function mosErrorAlert( $text, $action='window.history.go(-1);', $mode=1 ) {
        $text = nl2br( $text );
        $text = addslashes( $text );
        $text = strip_tags( $text );

        switch ( $mode ) {
                case 2:
                        echo "<script>$action</script> \n";
                        break;

                case 1:
                default:
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; "._ISO."\" />";
                        echo "<script>alert('$text'); $action</script> \n";
                        //echo '<noscript>';
                        //mosRedirect( @$_SERVER['HTTP_REFERER'], $text );
                        //echo '</noscript>';
                        break;
        }

        exit;
}


	/*
	* Function used to conduct admin session duties
	* Added as of 1.0.8
	* Deperciated 1.1
	*/
	function initSessionAdmin($option, $task) {	
		global $_VERSION, $mosConfig_admin_expired, $database;
		
		// check if session name corresponds to correct format
		$site = site_url.$GLOBALS['sitename'];
		if ( session_name() != md5( $site ) ) {
			echo "<script>document.location.href='index.php'</script>\n";
			exit();
		}
		
		$my = new stdClass();

		// restore some session variables
		$my->id 		= intval( mosGetParam( $_SESSION, 'session_user_id', '' ) );
		$my->username 	= strval( mosGetParam( $_SESSION, 'session_username', '' ) );
		$my->usertype 	= strval( mosGetParam( $_SESSION, 'session_usertype', '' ) );
		$my->gid 		= intval( mosGetParam( $_SESSION, 'session_gid', '' ) );
		$my->params		= mosGetParam( $_SESSION, 'session_user_params', '' );

		$session_id 	= mosGetParam( $_SESSION, 'session_id', '' );
		$logintime 		= mosGetParam( $_SESSION, 'session_logintime', '' );
		// check to see if session id corresponds with correct format
		if ( $session_id == md5( $my->id . $my->username . $my->usertype . $logintime ) ) {
			// if task action is to `save` or `apply` complete action before doing session checks.
			if ($task != 'save' && $task != 'apply') {
				// test for session_life_admin
				if ( @$GLOBALS['isession_life_admin'] ) {
					$session_life_admin = $GLOBALS['isession_life_admin'];
				} else {
					$session_life_admin = 1800;
				}
				
				// purge expired admin sessions only
				$past = time() - $session_life_admin;
				$query = "DELETE FROM #__session"
				. "\n WHERE time < '" . (int) $past . "'"
				. "\n AND guest = 1"
				. "\n AND gid = 0"
				. "\n AND userid <> 0"
				;
				ggsqlq( $query );
				
				// update session timestamp
				$current_time = time();
				$query = "UPDATE #__session"
						. "\n SET time = " . $database->Quote( $current_time )
						. "\n WHERE session_id = " . $database->Quote( $session_id )
				;
				ggsqlq( $query );
		
				// set garbage cleaning timeout
				setSessionGarbageClean();
				
				// check against db record of session
				$query = "SELECT COUNT( session_id )"
				. "\n FROM #__session"
				. "\n WHERE session_id = " . $database->Quote( $session_id )
				. "\n AND username = ". $database->Quote( $my->username )
				. "\n AND userid = ". intval( $my->id )
				;
				$count = ggsqlr( $query );
				
				// if no entry in session table that corresponds boot from admin area
				if ( $count == 0 ) {
					$link 	= NULL;
					
					if ($_SERVER['QUERY_STRING']) {
						$link = 'index2.php?'. $_SERVER['QUERY_STRING'];
					}
					
					// check if site designated as a production site 
					// for a demo site disallow expired page functionality
					// link must also be a Joomla link to stop malicious redirection
					if ( $link && strpos( $link, 'index2.php?option=com_' ) === 0 && $_VERSION->SITE == 1 && @$mosConfig_admin_expired === '1' ) {
						$now 	= time();
						
						$file 	= $this->getPath( 'com_xml', 'com_users' );
						$params = new mosParameters( $my->params, $file, 'component' );
						
						// return to expired page functionality
						$params->set( 'expired', 		$link );
						$params->set( 'expired_time', 	$now );

						// param handling
						if (is_array( $params->toArray() )) {
							$txt = array();
							foreach ( $params->toArray() as $k=>$v) {
								$txt[] = "$k=$v";
							}
							$saveparams = implode( "\n", $txt );
						}
						
						// save expired page info to user data
						$query = "UPDATE #__users"
						. "\n SET params = ". $this->_db->Quote( $saveparams )
						. "\n WHERE id = " . (int) $my->id
						. "\n AND username = ". $this->_db->Quote( $my->username )
						. "\n AND usertype = ". $this->_db->Quote( $my->usertype )
						;
						$this->_db->setQuery( $query );
						$this->_db->query();	
					}
					// СЕССИЯ КОНЧИЛАСЬ - СОХРАНЯЕМ ССЫЛКУ ДЛЯ ДАЛЬНЕЙШЕГО ВОССТАНОВЛЕНИЯ
					echo "<script>document.location.href='index.php?mosmsg=Сессия администратора закончилась&query_url=".codeurl_admin($_SERVER['QUERY_STRING'])."'</script>\n";
					exit();
				} else {
					// load variables into session, used to help secure /popups/ functionality
					$_SESSION['ca'] = $option;
					$_SESSION['task'] 	= $task;
				}
			}
		} else if ($session_id == '') {
			// no session_id as user has not attempted to login, or session.auto_start is switched on
			if (ini_get( 'session.auto_start' ) || !ini_get( 'session.use_cookies' )) {
				echo "<script>document.location.href='index.php?mosmsg=Вам необходимо авторизоваться. Если включен параметр PHP session.auto_start или выключен параметр session.use_cookies setting, то сначала вы должны их исправить перед тем, как сможете войти.'</script>\n";
			} else {
				// СЕССИЯ КОНЧИЛАСЬ - СОХРАНЯЕМ ССЫЛКУ ДЛЯ ДАЛЬНЕЙШЕГО ВОССТАНОВЛЕНИЯ
				echo "<script>document.location.href='index.php?mosmsg=Вам необходимо авторизоваться&query_url=".codeurl_admin($_SERVER['QUERY_STRING']  )."'</script>\n";
			}
			exit();
		} else {
			// session id does not correspond to required session format
			echo "<script>document.location.href='index.php?mosmsg=Неправильная сессия'</script>\n";
			exit();
		}

		return $my;
	}
	
	/* 	Function used to set Session Garbage Cleaning
		garbage cleaning set at configured session time + 3600 seconds */
	function setSessionGarbageClean() {
		/** ensure that funciton is only called once */
		if (!defined( '_JOS_GARBAGECLEAN' )) {
			define( '_JOS_GARBAGECLEAN', 1 );
			$garbage_timeout = 86400 + 3600;
			@ini_set('session.gc_maxlifetime', $garbage_timeout);
        }
    }

	/**
	 * @param string Key search for
	 * @param mixed Default value if not set
	 * @return mixed
	 */
	function get( $key, $default=null ) {
		return mosGetParam( $_SESSION, $key, $default );
	}

	/**
	 * @param string Key to set
	 * @param mixed Value to set
	 * @return mixed The new value
	 */
	function set( $key, $value ) {
		$_SESSION[$key] = $value;
		return $value;
	}	
	
	
	
/**
* Loads admin modules via module position
* @param string The position
* @param int 0 = no style, 1 = tabbed
*/
function mosLoadAdminModules( $position='left', $style=0 ) {
	global $database, $acl, $my;

	//$cache =& mosCache::getCache( 'com_content' );

	$query = "SELECT id, title, module, position, content, showtitle, params"
	. "\n FROM #__modules AS m"
	. "\n WHERE m.published = 1"
	. "\n AND m.position = " . $database->Quote( $position )
	. "\n AND m.client_id = 1"
	. "\n ORDER BY m.ordering"
	;
	$database->setQuery( $query );
	$modules = $database->loadObjectList();
	if($database->getErrorNum()) {
		echo "MA ".$database->stderr(true);
		return;
	}

	switch ($style) {
		case 1:
			// Tabs
			$tabs = new mosTabs(1);
			$tabs->startPane( 'modules-' . $position );
			foreach ($modules as $module) {
				$params = new mosParameters( $module->params );
				$editAllComponents 	= $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' );
				// special handling for components module
				if ( $module->module != 'mod_components' || ( $module->module == 'mod_components' && $editAllComponents ) ) {
					$tabs->startTab( $module->title, 'module' . $module->id );
					if ( $module->module == '' ) {
						mosLoadCustomModule( $module, $params );
					} else {
						mosLoadAdminModule( substr( $module->module, 4 ), $params );
					}
					$tabs->endTab();
				}
			}
			$tabs->endPane();
			break;

		case 2:
			// Div'd
			foreach ($modules as $module) {
				$params = new mosParameters( $module->params );
				echo '<div>';
				if ( $module->module == '' ) {
					mosLoadCustomModule( $module, $params );
				} else {
					mosLoadAdminModule( substr( $module->module, 4 ), $params );
				}
				echo '</div>';
			}
			break;

		case 0:
		default:
			foreach ($modules as $module) {
				$params = new mosParameters( $module->params );
				if ( $module->module == '' ) {
					mosLoadCustomModule( $module, $params );
				} else {
					mosLoadAdminModule( substr( $module->module, 4 ), $params );
				}
			}
			break;
	}
}	
/**
* Loads an admin module
*/
function mosLoadAdminModule( $name, $params=NULL ) {
	global  $task;
	global $database, $my;

	$name = str_replace( '/', '', $name );
	$name = str_replace( '\\', '', $name );
	$path = site_path."/iadmin/modules/ima$name.php";
	if (file_exists( $path )) {
		require $path;
	}
}	
	
	
require_once( 'iadmin/icoreadmin.php' );

/**
 * ПОЛУЧАЕМ МАССИВ - ТИПОВ МЕНЮ
 * Пример: Array ( [0] => Основное меню сайта, [1] => еще одно меню сайта )
 * @return <array>
 */
function menutypes() {
		$query = "SELECT menutype FROM #__menu          GROUP BY menutype ORDER BY menutype";
		$menuMenus = ggsql( $query );

		$menuTypes = '';
		foreach ( $menuMenus as $menuMenu ) $menuTypes[] = $menuMenu->menutype;
		asort( $menuTypes );
		return $menuTypes;
}

function mosParseParams( $txt ) {
        return parse( $txt );
}
function parse( $txt, $process_sections = false, $asArray = false ) {
		if (is_string( $txt )) {
				$lines = explode( "\n", $txt );
		} else if (is_array( $txt )) {
				$lines = $txt;
		} else {
				$lines = array();
		}
		$obj = $asArray ? array() : new stdClass();

		$sec_name = '';
		$unparsed = 0;
		if (!$lines) {
				return $obj;
		}
		foreach ($lines as $line) {
				// ignore comments
				if ($line && $line[0] == ';') {
						continue;
				}
				$line = trim( $line );

				if ($line == '') {
						continue;
				}
				if ($line && $line[0] == '[' && $line[strlen($line) - 1] == ']') {
						$sec_name = substr( $line, 1, strlen($line) - 2 );
						if ($process_sections) {
								if ($asArray) {
										$obj[$sec_name] = array();
								} else {
										$obj->$sec_name = new stdClass();
								}
						}
				} else {
						if ($pos = strpos( $line, '=' )) {
								$property = trim( substr( $line, 0, $pos ) );

								if (substr($property, 0, 1) == '"' && substr($property, -1) == '"') {
										$property = stripcslashes(substr($property,1,count($property) - 2));
								}
								$value = trim( substr( $line, $pos + 1 ) );
								if ($value == 'false') {
										$value = false;
								}
								if ($value == 'true') {
										$value = true;
								}
								if (substr( $value, 0, 1 ) == '"' && substr( $value, -1 ) == '"') {
										$value = stripcslashes( substr( $value, 1, count( $value ) - 2 ) );
								}

								if ($process_sections) {
										$value = str_replace( '\n', "\n", $value );
										if ($sec_name != '') {
												if ($asArray) {
														$obj[$sec_name][$property] = $value;
												} else {
														$obj->$sec_name->$property = $value;
												}
										} else {
												if ($asArray) {
														$obj[$property] = $value;
												} else {
														$obj->$property = $value;
												}
										}
								} else {
										$value = str_replace( '\n', "\n", $value );
										if ($asArray) {
												$obj[$property] = $value;
										} else {
												$obj->$property = $value;
										}
								}
						} else {
								if ($line && trim($line[0]) == ';') {
										continue;
								}
								if ($process_sections) {
										$property = '__invalid' . $unparsed++ . '__';
										if ($process_sections) {
												if ($sec_name != '') {
														if ($asArray) {
																$obj[$sec_name][$property] = trim($line);
														} else {
																$obj->$sec_name->$property = trim($line);
														}
												} else {
														if ($asArray) {
																$obj[$property] = trim($line);
														} else {
																$obj->$property = trim($line);
														}
												}
										} else {
												if ($asArray) {
														$obj[$property] = trim($line);
												} else {
														$obj->$property = trim($line);
												}
										}
								}
						}
				}
		}
		return $obj;
}
	
function igetPath( $varname, $option2='' ) {
		global  $option;
		if (  $option2==''  )  $option2 = $option;

		if ($option2) {
			//$pre = substr($option2,0,3);
			if (  !(strpos($_SERVER['REQUEST_URI'], "/iadmin/")===false)  ){	// КОМПОНЕНТ АДМИНКИ
				$result_dir = site_path."/iadmin/component/$option2";	
			}
			else if (  strcmp($pre, "ima")==0  ){	// МОДУЛЬ АДМИНКИ
				$result_dir = site_path."/iadmin/modules";
			}
			else{
				$result_dir = site_path."/component/$option2";	// КОМПОНЕНТ САЙТА
			}
		}
		$result = null;
		switch ($varname) {
				case 'admin_html':
						$result = $result_dir."/admin.$option2.html.php";
						break;
				case 'class':
						$result = $result_dir."/$option2.class.php";
						break;
				case 'toolbar':
						$result_dir = site_path."/iadmin/component/$option2";
						$path = $result_dir."/toolbar.$option2.php";
						if (file_exists( $path )) $result = $path;						
						break;
				case 'toolbar_html':
						$result_dir = site_path."/iadmin/component/$option2";
						$path = $result_dir."/toolbar.$option2.html.php";
						if (file_exists( $path )) $result = $path;
						break;
				case 'com_xml':
						$path = $result_dir."/$option2.xml";
						if (file_exists( $path )) $result = $path;
						break;						
				case 'menu_xml':
						$path = $result_dir."/$option2.xml";
						if (file_exists( $path )) $result = $path;
						break;	
				case 'mod0_xml':
						$path = site_path."/modules/$option2.xml";
						if (file_exists( $path )) $result = $path;
						break;
		}
		return $result;
}

function igetPath_admintoolbar( $varname, $option2='' ) {
		global  $option;
		if (  $option2==''  )  $option2 = $option;
		if ($option2) { $result_dir = site_path."/iadmin/component/$option2"; }
		$result = null;
		switch ($varname) {
				case 'toolbar':
						$path = $result_dir."/toolbar.$option2.php";
						if (file_exists( $path )) $result = $path;
						break;
				case 'toolbar_html':
						$path = $result_dir."/toolbar.$option2.html.php";
						if (file_exists( $path )) $result = $path;
						break;
		}		return $result;
}
	
function mosRedirect( $url, $msg='' ) {

   global $mainframe;
    // specific filters
        if (trim( $msg )) {
                 if (strpos( $url, '?' )) {
                        $url .= '&mosmsg=' . urlencode( $msg );
                } else {
                        $url .= '?mosmsg=' . urlencode( $msg );
                }
        }
        if (headers_sent()) {
                echo "<script>document.location.href='$url';</script>\n";
        } else {
                @ob_end_clean(); // clear output buffer
                header( 'HTTP/1.1 301 Moved Permanently' );
                header( "Location: ". $url );
        }
        exit();
}
	
/**
 * Function to convert array to integer values
 * @param array
 * @param int A default value to assign if $array is not an array
 * @return array
 */
function mosArrayToInts( &$array, $default=null ) {
        if (is_array( $array )) {
		foreach( $array as $key => $value ) {
			$array[$key] = (int) $value;
                }
        } else {
                if (is_null( $default )) {
			$array = array();
			return array(); // Kept for backwards compatibility
                } else {
			$array = array( (int) $default );
			return array( $default ); // Kept for backwards compatibility
                }
        }
}
/*
* Function to handle an array of integers
*/
function josGetArrayInts( $name, $type=NULL ) {
	if ( $type == NULL ) {
		$type = $_POST;
	}
	
	$array = mosGetParam( $type, $name, array(0) );
	mosArrayToInts( $array );
	
	if (!is_array( $array )) {
		$array = array(0);
	}
	
	return $array;
}
/**
* Gets the value of a user state variable
* @param string The name of the user state variable
* @param string The name of the variable passed in a request
* @param string The default value for the variable if not found
*/
function getUserStateFromRequest( $req_name, $var_default=null ) {
				if (isset( $_REQUEST[$req_name] )) {
						$returnState = $_REQUEST[$req_name];
				} else {
						$returnState = $var_default;
				}
	
	// filter input
	$iFilter = new InputFilter();			
	$returnState = $iFilter->process( $returnState );
	return $returnState;
}

function mosFormatDate($date, $format = '', $offset = NULL){
	global $reg;
	if(empty($format)) 
		$format = '%Y-%m-%d %H:%M:%S';
		
	if(is_null($offset)){
		$offset = $reg['iServerTimeOffset'];
	}
	
	if ($date && preg_match("~([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})~", $date, $regs)){
		$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		$date = $date > -1 ? strftime($format, $date + ($offset)) : '-';
	}
	return $date;
}

function iToolTip( $tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='#', $link=1 ) {
	return $title;
}


function mosReadDirectory( $path, $filter='.', $recurse=false, $fullpath=false  ) {
        $arr = array();
        if (!@is_dir( $path )) {
                return $arr;
        }
        $handle = opendir( $path );

        while ($file = readdir($handle)) {
                $dir = mosPathName( $path.'/'.$file, false );
                $isDir = is_dir( $dir );
                if (($file != ".") && ($file != "..")) {
                        if (preg_match( "/$filter/", $file )) {
                                if ($fullpath) {
                                        $arr[] = trim( mosPathName( $path.'/'.$file, false ) );
                                } else {
                                        $arr[] = trim( $file );
                                }
                        }
                        if ($recurse && $isDir) {
                                $arr2 = mosReadDirectory( $dir, $filter, $recurse, $fullpath );
                                $arr = array_merge( $arr, $arr2 );
                        }
                }
        }
        closedir($handle);
        asort($arr);
        return $arr;
}

function ampReplace( $text ) {
	$text = str_replace( '&&', '*--*', $text );
        $text = str_replace( '&#', '*-*', $text );
	$text = str_replace( '&amp;', '&', $text );
        $text = preg_replace( '|&(?![\w]+;)|', '&amp;', $text );
        $text = str_replace( '*-*', '&#', $text );
	$text = str_replace( '*--*', '&&', $text );

        return $text;
}
function SortArrayObjects_cmp( &$a, &$b ) {
        global $csort_cmp;

        if ( $a->$csort_cmp['key'] > $b->$csort_cmp['key'] ) {
                return $csort_cmp['direction'];
        }

        if ( $a->$csort_cmp['key'] < $b->$csort_cmp['key'] ) {
                return -1 * $csort_cmp['direction'];
        }

        return 0;
}
function SortArrayObjects( &$a, $k, $sort_direction=1 ) {
        global $csort_cmp;

        $csort_cmp = array(
                'key'                  => $k,
                'direction'        => $sort_direction
        );

        usort( $a, 'SortArrayObjects_cmp' );

        unset( $csort_cmp );
}
function iLoadEditor( $init_func_name='onInitEditor' ){
	global $ieditor_loaded;
	if (  $ieditor_loaded==0  ){
		initEditor( $init_func_name );
		$ieditor_loaded = 1;
	}
}
/**
 * Strip slashes from strings or arrays of strings
 * @param mixed The input string or array
 * @return mixed String or array stripped of slashes
 */
function mosStripslashes( &$value ) {
        $ret = '';
        if (is_string( $value )) {
                $ret = stripslashes( $value );
        } else {
                if (is_array( $value )) {
                        $ret = array();
                        foreach ($value as $key => $val) {
                                $ret[$key] = mosStripslashes( $val );
                        }
                } else {
                        $ret = $value;
                }
        }
        return $ret;
}
/**
* Copy the named array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
*/
function mosBindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
        if (!is_array( $array ) || !is_object( $obj )) {
                return (false);
        }

	$ignore = ' ' . $ignore . ' ';
        foreach (get_object_vars($obj) as $k => $v) {
                if( substr( $k, 0, 1 ) != '_' ) {                        // internal attributes of an object are ignored
			if (strpos( $ignore, ' ' . $k . ' ') === false) {
                                if ($prefix) {
                                        $ak = $prefix . $k;
                                } else {
                                        $ak = $k;
                                }
                                if (isset($array[$ak])) {
					$obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? mosStripslashes( $array[$ak] ) : $array[$ak];
                                }
                        }
                }
        }

        return true;
}
/**
* Class to support function caching
*/
class mosCache {
        /**
        * @return object A function cache object
        */
        function &getCache(  $group=''  ) {
                global  $mosConfig_caching, $mosConfig_cachepath, $mosConfig_cachetime;

		require_once( site_path . '/includes/joomla.cache.php' );

                $options = array(
                        'cacheDir'                 => $mosConfig_cachepath . '/',
                        'caching'                 => $mosConfig_caching,
                        'defaultGroup'         => $group,
                        'lifeTime'                 => $mosConfig_cachetime
                );
		$cache = new JCache_Lite_Function( $options );
                return $cache;
        }
        /**
        * Cleans the cache
        */
        function cleanCache( $group=false ) {
                global $mosConfig_caching;
                if ($mosConfig_caching) {
                        $cache =& mosCache::getCache( $group );
                        $cache->clean( $group );
                }
        }
}
/**
* @param string SQL with ordering As value and 'name field' AS text
* @param integer The length of the truncated headline
*/
function mosGetOrderingList($sql, $chop = 30) {
        global $database;
        $order = array();
        $database->setQuery( $sql );
        if (!($orders = $database->loadObjectList())) {
                if ($database->getErrorNum()) {
                        echo $database->stderr();
                        return false;
                } else {
                        $order[] = mosHTML::makeOption( 1, 'Первый' );
                        return $order;
                }
        }
        $order[] = mosHTML::makeOption( 0, '0 Первый' );
        for ($i=0, $n = count( $orders ); $i < $n; $i++) {

                if (mb_strlen($orders[$i]->text, 'utf-8') > $chop) {
                        $text = mb_substr($orders[$i]->text, 0, $chop, 'utf-8')."...";
                } else {
                        $text = $orders[$i]->text;
                }

                $order[] = mosHTML::makeOption( $orders[$i]->value, $orders[$i]->value.' ('.$text.')' );
        }
        $order[] = mosHTML::makeOption($orders[$i-1]->value + 1, ($orders[$i-1]->value + 1).' Последний' );

        return $order;
}
class mosModule extends mosDBTable {
        /** @var int Primary key */
        var $id                                        = null;
        /** @var string */
        var $title                                = null;
        /** @var string */
        var $showtitle                        = null;
        /** @var int */
        var $content                        = null;
        /** @var int */
        var $ordering                        = null;
        /** @var string */
        var $position                        = null;
        /** @var boolean */
        var $checked_out                = null;
        /** @var time */
        var $checked_out_time        = null;
        /** @var boolean */
        var $published                        = null;
        /** @var string */
        var $module                                = null;
        /** @var int */
        var $numnews                        = null;
        /** @var int */
        var $access                                = null;
        /** @var string */
        var $params                                = null;
        /** @var string */
        var $iscore                                = null;
        /** @var string */
        var $client_id                        = null;

        /**
        * @param database A database connector object
        */
        function mosModule( &$db ) {
                $this->mosDBTable( '#__modules', 'id', $db );
        }
        // overloaded check function
        function check() {
                // check for valid name
                if (trim( $this->title ) == '') {
                        $this->_error = "Ваш модуль должен содержать заголовок.";
                        return false;
                }

                return true;
        }
}
class mosCategory extends mosDBTable {
        var $id               = null;
        var $parent_id        = null;
        var $title            = null;
        var $name             = null;
        var $image            = null;
        var $section          = null;
        var $image_position   = null;
        var $description      = null;
        var $published        = null;
        var $checked_out      = null;
        var $checked_out_time = null;
        var $ordering         = null;
        var $access           = null;
        var $params           = null;

        function mosCategory( &$db ) {
                $this->mosDBTable( '#__categories', 'id', $db );
        }
        // overloaded check function
        function check() {
                // check for valid name
                if (trim( $this->title ) == '') {
                        $this->_error = "Your Category must contain a title.";
                        return false;
                }
                if (trim( $this->name ) == '') {
                        $this->_error = "Your Category must have a name.";
                        return false;
                }

                // check for existing name
                $query = "SELECT id"
                . "\n FROM #__categories "
		. "\n WHERE name = " . $this->_db->Quote( $this->name )
		. "\n AND section = " . $this->_db->Quote( $this->section )
                ;
                $this->_db->setQuery( $query );

                $xid = intval( $this->_db->loadResult() );
                if ($xid && $xid != intval( $this->id )) {
                        $this->_error = "Категория с таким названием уже существует. Повторите снова.";
                        return false;
                }
                return true;
        }
}
function mosToolTip( $tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='#', $link=1 ) {
        global $mosConfig_live_site;

        if ( $width ) { $width = ', WIDTH, '.$width .' '; }
        if ( $title ) { $title = ', TITLE, \''.$title .'\''; }
        if ( !$text ) {
                $image         = $mosConfig_live_site . '/includes/js/ThemeOffice/'. $image;
                $text         = '<img src="'. $image .'" border="0" align="absmiddle" />';
        }
        $style = 'style="text-decoration: none; color: #333;"';
        if ( $href ) { $style = ''; } 
		else { $href = '#';}

        $mousover = 'return Tip(\''. $tooltip .'\''. $title . $width .');';
        $tip = "";
        if ( $link ) {
                $tip .= '<a href="'. $href .'" onmouseover="'. $mousover .'" '. $style .'>'. $text .'</a>';
        } else {
                $tip .= '<span onmouseover="'. $mousover .'"  '. $style .'>'. $text .'</span>';
        }

        return $tip;
}
function mosTreeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 ) {

        if (@$children[$id] && $level <= $maxlevel) {
                foreach ($children[$id] as $v) {
                        $id = $v->id;

                        if ( $type ) {
                                $pre         = '<sup>L</sup>&nbsp;';
                                $spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        } else {
                                $pre         = '- ';
                                $spacer = '&nbsp;&nbsp;';
                        }

                        if ( $v->parent == 0 ) {
                                $txt         = $v->name;
                        } else {
                                $txt         = $pre . $v->name;
                        }
                        $pt = $v->parent;
                        $list[$id] = $v;
                        $list[$id]->treename = "$indent$txt";
                        $list[$id]->children = count( @$children[$id] );

                        $list = mosTreeRecurse( $id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type );
                }
        }
        return $list;
}
class mosMenu extends mosDBTable {
        var $id                                        = null;
        var $menutype                        = null;
        var $name                                = null;
        var $link                                = null;
        var $type                                = null;
        var $published                        = null;
        var $componentid                = null;
        var $parent                                = null;
        var $sublevel                        = null;
        var $ordering                        = null;
        var $checked_out                = null;
        var $checked_out_time        = null;
        var $pollid                                = null;
        var $browserNav                        = null;
        var $access                                = null;
        var $utaccess                        = null;
        var $params                                = null;
        function mosMenu( &$db ) {
                $this->mosDBTable( '#__menu', 'id', $db );
        }

	function check() {
		$this->id = (int) $this->id;
		$this->params = (string) trim( $this->params . ' ' );
		return true;
	}
}
class mosComponent extends mosDBTable {
        var $id                                        = null;
        var $name                                = null;
        var $link                                = null;
        var $menuid                                = null;
        var $parent                                = null;
        var $admin_menu_link        = null;
        var $admin_menu_alt                = null;
        var $option                                = null;
        var $ordering                        = null;
        var $admin_menu_img                = null;
        var $iscore                                = null;
        var $params                                = null;
        function mosComponent( &$db ) {
                $this->mosDBTable( '#__components', 'id', $db );
        }
}
function mosPathName($p_path,$p_addtrailingslash = true) {
        $retval = "";

        $isWin = (substr(PHP_OS, 0, 3) == 'WIN');

        if ($isWin)        {
                $retval = str_replace( '/', '\\', $p_path );
                if ($p_addtrailingslash) {
                        if (substr( $retval, -1 ) != '\\') {
                                $retval .= '\\';
                        }
                }

		// Check if UNC path
		$unc = substr($retval,0,2) == '\\\\' ? 1 : 0;

                // Remove double \\
                $retval = str_replace( '\\\\', '\\', $retval );

		// If UNC path, we have to add one \ in front or everything breaks!
		if ( $unc == 1 ) {
			$retval = '\\'.$retval;
		}
        } else {
                $retval = str_replace( '\\', '/', $p_path );
                if ($p_addtrailingslash) {
                        if (substr( $retval, -1 ) != '/') {
                                $retval .= '/';
                        }
                }

		// Check if UNC path
		$unc = substr($retval,0,2) == '//' ? 1 : 0;

                // Remove double //
                $retval = str_replace('//','/',$retval);

		// If UNC path, we have to add one / in front or everything breaks!
		if ( $unc == 1 ) {
			$retval = '/'.$retval;
		}
        }

        return $retval;
}

class mosMainFrame {
        /** @var database Internal database class pointer */
        var $_db                                = null;
        /** @var object An object of configuration variables */
        var $_config                        = null;
        /** @var object An object of path variables */
        var $_path                                = null;
        /** @var mosSession The current session */
        var $_session                        = null;
        /** @var string The current template */
        var $_template                        = null;
        /** @var array An array to hold global user state within a session */
        var $_userstate                        = null;
        /** @var array An array of page meta information */
        var $_head                                = null;
        /** @var string Custom html string to append to the pathway */
        var $_custom_pathway        = null;
        /** @var boolean True if in the admin client */
        var $_isAdmin                         = false;
			
        function mosMainFrame( &$db, $option, $basePath, $isAdmin=false ) {
			$this->_db =& $db;

			// load the configuration values
			//$this->_setTemplate( $isAdmin );
			//$this->_setAdminPaths( $option, $this->getCfg( 'absolute_path' ) );
			if (isset( $_SESSION['session_userstate'] )) {
					$this->_userstate = & $_SESSION['session_userstate'];
			} else {
					$this->_userstate = null;
			}
			$this->_head = array();
			$this->_head['title']         = isset($GLOBALS['mosConfig_sitename']) ? $GLOBALS['mosConfig_sitename'] : array();
			$this->_head['meta']       	  = array();
			$this->_head['custom']        = array();

			//set the admin check
			//$this->_isAdmin                 = (boolean) $isAdmin;
	
			$now = date( 'Y-m-d H:i:s', time());
			$this->set( 'now', $now );
        }
        function getUser() {
                global $database;
                $user = new mosUser( $this->_db );
                $user->id               = intval( $this->_session->userid );
                $user->username         = $this->_session->username;
                $user->usertype         = $this->_session->usertype;
                $user->gid              = intval( $this->_session->gid );

                if ($user->id) {
					$query = "SELECT id, name, usersurname, userparentname, email, block, sendEmail, registerDate, lastvisitDate, activation, params, uinfo, small, mid, org, full, tel, address FROM #__users WHERE id = " . (int) $user->id ;
					$database->setQuery( $query );			$database->loadObject( $my );
					
					$user->params 		= $my->params;
					$user->name		= $my->name;
					$user->usersurname	= $my->usersurname;
					$user->userparentname	= $my->userparentname;
					$user->email		= $my->email;
					$user->block		= $my->block;
					$user->sendEmail	= $my->sendEmail;
					$user->registerDate	= $my->registerDate;
					$user->lastvisitDate	= $my->lastvisitDate;
					$user->activation	= $my->activation;
					$user->uinfo		= $my->uinfo;
					$user->small		= $my->small;
					$user->mid		= $my->mid;
					$user->org		= $my->org;
					$user->full		= $my->full;
                                        $user->tel		= $my->tel;
                                        $user->address		= $my->address;

                }
                return $user;
        }
        function set( $property, $value=null ) {
                $this->$property = $value;
        }

        function get($property, $default=null) {
                if(isset($this->$property)) {
                        return $this->$property;
                } else { return $default; }
        }
		function getPath( $varname, $option='' ) {
			return igetPath( $varname, $option );
		}
		function getUserStateFromRequest( $var_name, $req_name, $var_default=null ) {
			return getUserStateFromRequest( $req_name, $var_default );
		}         
        function getCfg( $varname ) {	//(from configuration.php)
			$varname = 'mosConfig_' . $varname;
			if (isset( $GLOBALS[$varname] )) return $GLOBALS[$varname]; 
			else return null;
        }
		function sessionCookieName() {
			global $mainframe;	 
	
			return md5( 'site' . $mainframe->getCfg( 'live_site' ) );		
		}
		function sessionCookieValue( $id=null ) {
			global $mainframe;		
			$type = $mainframe->getCfg( 'session_type' );
			$browser = @$_SERVER['HTTP_USER_AGENT'];
			switch ($type) {
				case 2:
					$value 			= md5( $id . $_SERVER['REMOTE_ADDR'] );
					break;
				case 1:	// slightly reduced security - 3rd level IP authentication for those behind IP Proxy 
					$remote_addr 	= explode('.',$_SERVER['REMOTE_ADDR']);
					$ip				= $remote_addr[0] .'.'. $remote_addr[1] .'.'. $remote_addr[2];
					$value 			= mosHash( $id . $ip . $browser );
					break;
				default:	// Highest security level - new default for 1.0.8 and beyond
					$ip				= $_SERVER['REMOTE_ADDR'];
					$value 			= mosHash( $id . $ip . $browser );
					break;
			}		
			return $value;
		}
		function remCookieName_User() {
			$value = mosHash( 'remembermecookieusername'. mosMainFrame::sessionCookieName() );
			return $value;
		}
		/* Static Function used to generate the Rememeber Me Cookie Name for Password information */
		function remCookieName_Pass() {
			$value = mosHash( 'remembermecookiepassword'. mosMainFrame::sessionCookieName() );
			return $value;
		}
		
		/* Static Function used to generate the Remember Me Cookie Value for Username information */
		function remCookieValue_User( $username ) {
			$value = md5( $username . mosHash( @$_SERVER['HTTP_USER_AGENT'] ) );
			return $value;
		}
		
		/* Static Function used to generate the Remember Me Cookie Value for Password information */
		function remCookieValue_Pass( $passwd ) {
			$value 	= md5( $passwd . mosHash( @$_SERVER['HTTP_USER_AGENT'] ) );
			return $value;
		}	
		
		
		
        function ilogout() {
			global $database;
			mosCache::cleanCache();
			$session 			=& $this->_session;
			$session->guest     = 1;
			$session->username  = '';
			$session->userid    = 0;
			$session->usertype  = '';
			$session->gid 		= 0;
			$session->update();
			// kill remember me cookie
			$lifetime 		= time() - 86400;
			$remCookieName 	= mosMainFrame::remCookieName_User();
			setcookie( $remCookieName, ' ', $lifetime, '/' );
			@session_destroy();
			if (  isset($_REQUEST['4ajax'])  ){	// ajax_out
				?> location.reload(true); <?php	
			}
        }		
		
		function ilogin( $username=null,$passwd=null, $remember=0, $userid=NULL ) {
			global $acl, $_VERSION;  $bypost = 0;
                        $remember = 1;
                        if (  isset($_REQUEST['4ajax_login'])  ){
                            if (   ggrr('username')==''  ) 			{ ?>$("#insite_login_server_answer").html('— Заполните поле «Логин»').jTypeWriter({duration:1.5}); <?php return; }
                            if (   ggrr('passwd')==''  ) 			{ ?>$("#insite_login_server_answer").html('— Введите пароль').jTypeWriter({duration:1.5}); <?php return; }
                        }
			// ggtr ($remember,1); ggtr ($username,1); ggtr($passwd,1); ggtr ($_REQUEST); die();
			// if no username and password passed from function, then function is being called from login module/component
			if (!$username || !$passwd) {
				$username 	= stripslashes( strval( mosGetParam( $_POST, 'username', '' ) ) );
				$passwd 	= stripslashes( strval( mosGetParam( $_POST, 'passwd', '' ) ) );
							$passwd         = md5( $passwd );
							$bypost         = 1;
				// extra check to ensure that sessioncookie exists
				//ggd($this->_session);
				if (!$this->_session->session_id) { mosErrorAlert( _ALERT_ENABLED ); return; }
				josSpoofCheck(NULL,1);
			}
			$row = null;
			if (!$username || !$passwd) { mosErrorAlert( _LOGIN_INCOMPLETE ); exit(); }
			else {
				if ( $remember && strlen($username) == 32 && strlen($passwd) == 32 && $userid ) {
				// query used for remember me cookie
					$harden = mosHash( @$_SERVER['HTTP_USER_AGENT'] );
					$query = "SELECT id, name, username, password, usertype, block, gid"
							. "\n FROM #__users"
					. "\n WHERE id = " . (int) $userid ;
					$this->_db->setQuery( $query ); $this->_db->loadObject($user);
					$check_username = md5( $user->username . $harden );
					$check_password = md5( $user->password . $harden );
	
					if ( $check_username == $username && $check_password == $passwd ) {
						$row = $user;
					}				
				} else {
				// query used for login via login module
					$query = "SELECT id, name, username, password, usertype, block, gid"
					. "\n FROM #__users"
					. "\n WHERE username = ". $this->_db->Quote( $username )
					. "\n AND password = ". $this->_db->Quote( $passwd ) ;
					$this->_db->setQuery( $query ); $this->_db->loadObject( $row );
				}
	
				if (is_object($row)) {
						// user blocked from login
						if ($row->block == 1) { 
							if (  isset($_REQUEST['4ajax_login'])  ){	// ajax_reg_complete
								?> $("#insite_login_server_answer").html('Учетная запись не активирована.').jTypeWriter({duration:1.5}); <?php
								return;
							} else mosErrorAlert(_LOGIN_BLOCKED); 
						}
						// initialize session data
						$session =& $this->_session;
						$session->guest 	= 0;
						$session->username 	= $row->username;
						$session->userid	= intval( $row->id );
						$session->usertype	= $row->usertype;
						$session->gid   	= intval( $row->gid );
						$session->update();
						// check to see if site is a production site
						// allows multiple logins with same user for a demo site
						if ( $_VERSION->SITE ) {
							// delete any old front sessions to stop duplicate sessions
							$query = "DELETE FROM #__session"
							. "\n WHERE session_id != ". $this->_db->Quote( $session->session_id )
							. "\n AND username = ". $this->_db->Quote( $row->username )
							. "\n AND userid = " . (int) $row->id
							. "\n AND gid = " . (int) $row->gid
							. "\n AND guest = 0"
							;
							$this->_db->setQuery( $query );
							$this->_db->query();	
						}
						// update user visit data
						$currentDate = date("Y-m-d\TH:i:s");
						$query = "UPDATE #__users"
						. "\n SET lastvisitDate = ". $this->_db->Quote( $currentDate )
						. "\n WHERE id = " . (int) $session->userid;
						$this->_db->setQuery($query);
						if (!$this->_db->query()) { die($this->_db->stderr(true)); }
		
						// set remember me cookie if selected
						//$remember = strval( mosGetParam( $_POST, 'remember', '' ) );
						////if ( $remember == 'yes' ) {
							// cookie lifetime of 4*365 days
							$lifetime = time() + 4*365*24*60*60;
							$remCookieName 	= mosMainFrame::remCookieName_User();
							$remCookieValue = mosMainFrame::remCookieValue_User( $row->username ) . mosMainFrame::remCookieValue_Pass( $row->password ) . $row->id;
							setcookie( $remCookieName, $remCookieValue, $lifetime, '/' );
						////}
						mosCache::cleanCache();
						if (  isset($_REQUEST['4ajax_login'])  ){	// ajax_reg_complete
							// $("#insite_login_server_answer").html('ajax_reg_complete'); 
							?> location.reload(true); <?php	
						}
					} else {
						if ( $bypost ) { 
							if (  isset($_REQUEST['4ajax_login'])  ){	// ajax_reg_complete
								 ?> $("#insite_login_server_answer").html('— Неправильный логин или пароль').jTypeWriter({duration:1.5}); <?php
							} else mosErrorAlert(_LOGIN_INCORRECT);
						}
						else {	$this->ilogout();	mosRedirect('index.php');	}
						exit();
					}
				}
		}
		
        function initSession() {
		// КАКИЕ КУКИ ФОРМИРУЮТСЯ
		//	sessionCookieName:$sessionValueCheck 	- временный куки хранят зашифрованный session_id, живут пока открыт браузер - они указывают на БД
		// 	$remCookieName:$remCookieValue 			- постоянные куки хранятся 4 года при использовании функции "ЗАПОМНИТЬ МЕНЯ"
		// 	PHPSESSID:unique_value 					- стандартные для PHP сессии и к авторизации никакого отношения не имеет
		
		// initailize session variables
        $session =& $this->_session;
        $session = new mosSession( $this->_db );
		// purge expired sessions
		$session->purge('core');	// очищаем из базы старые записи
		// sessionCookieName - она всегда одинакова для любого компьютера запустившего данный сайт, содержит зашифрованный session_id
		$sessionCookieName 	= mosMainFrame::sessionCookieName();	// генерация md5 названия куки return md5( 'site' . $mainframe->getCfg( 'live_site' ) );
		//ggd ("sessionCookieName $sessionCookieName");
		// Get Session Cookie `value`
		$sessioncookie 		= strval( mosGetParam( $_COOKIE, $sessionCookieName, null ) );
		//ggd ("sessioncookie $sessioncookie");
		// $sessionValueCheck - это session_id  в базе ins_session
		$sessionValueCheck 		= mosMainFrame::sessionCookieValue( $sessioncookie ); //генерация md5 значения куки на основе $sessioncookie.$ip.$browser
		// ggd ("sessionValueCheck $sessionValueCheck");
		// Check if existing session exists in db corresponding to Session cookie `value` 
		// extra check added in 1.0.8 to test sessioncookie value is of correct length
		if ( $sessioncookie && strlen($sessioncookie) == 32 && $sessioncookie != '-' && $session->load($sessionValueCheck) ) {
           $session->time = time(); $session->update(); // update time in session table
        } else {	// в базе ins_session не обнаружено записи для данного session_id=$sessionValueCheck или нет временной куки $sessionCookieName:$sessioncookie
			// Remember Me Cookie `name`
			$remCookieName = mosMainFrame::remCookieName_User(); // mosHash( 'remembermecookieusername'. mosMainFrame::sessionCookieName() );
			//$remCookieName - значение постоянного куки, который всегда хранится в системе 4 года и создается при использовании функции "ЗАПОМНИТЬ МЕНЯ"
			$cookie_found = false; // test if cookie found
			if ( isset($_COOKIE[$sessionCookieName]) || isset($_COOKIE[$remCookieName]) || isset($_POST['force_session']) ) { 
				$cookie_found = true; }
			// check if neither remembermecookie or sessioncookie found
			if (!$cookie_found) {
				// create sessioncookie and set it to a test value set to expire on session end
				setcookie( $sessionCookieName, '-', false, '/' );				
			} else {
			// otherwise, sessioncookie was found, but set to test val or the session expired, prepare for session registration and register the session
				$url = strval( mosGetParam( $_SERVER, 'REQUEST_URI', null ) );
				// stop sessions being created for requests to syndicated feeds
				$session->guest		= 1;
				$session->username 	= '';
				$session->time 		= time();
				$session->gid  		= 0;
				$session->generateId();
				// после generateId() иммем следующее:	_session_cookie - зашифрованный session_id для куки;
				//									   	session_id - session_id в БД ins_session;
				if (!$session->insert()) { die( $session->getError() ); }
	
				// create Session Tracking Cookie set to expire on session end
				setcookie( $sessionCookieName, $session->getCookie(), false, '/' );
				//$session->getCookie() - возвращает _session_cookie (зашифрованный session_id для куки) 
			}
			// Cookie used by Remember me functionality
			$remCookieValue	= strval( mosGetParam( $_COOKIE, $remCookieName, null ) );
			// test if cookie is correct length
			if ( strlen($remCookieValue) > 64 ) {
				// Separate Values from Remember Me Cookie
				$remUser	= substr( $remCookieValue, 0, 32 );
				$remPass	= substr( $remCookieValue, 32, 32 );
				$remID		= intval( substr( $remCookieValue, 64  ) );
				// check if Remember me cookie exists. Login with usercookie info.
				if ( strlen($remUser) == 32 && strlen($remPass) == 32 ) { $this->ilogin( $remUser, $remPass, 1, $remID ); }
			}
		}
	}
	
	
	

}

function mosPrepareSearchContent( $text, $length=200, $searchword ) {
		
        // strips tags won't remove the actual jscript
        $text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
        $text = preg_replace( '/{.+?}/', '', $text);
        //$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is','\2', $text );
        // replace line breaking tags with whitespace
        $text = preg_replace( "'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text );

	$text = mosSmartSubstr( strip_tags( $text ), $length, $searchword ); 

	return $text;
}
function mosSmartSubstr($text, $length=200, $searchword) {
	// ggd ($text);
  $wordpos = mb_strpos	(   
  							mb_strtolower($text,"UTF-8"), 
							mb_strtolower($searchword,"UTF-8"),  
							0, 
							"UTF-8" 
						);
  $halfside = intval($wordpos - $length/2 - mb_strlen ($searchword, 'UTF-8'));
  if ($wordpos && $halfside > 0) {
	return '...' . mb_substr($text, $halfside, $length, 'UTF-8') . '...';
  } else {
        return mb_substr( $text, 0, $length, 'UTF-8');
  }
}
function josSpoofValue($alt=NULL) {
	global $mainframe;
	
	if ($alt) {
		if ( $alt == 1 ) {
		$random		= date( 'Ymd' );
	} else {		
			$random		= $alt . date( 'Ymd' );
		}
	} else {		
		$random		= date( 'dmY' );
	}
	// the prefix ensures that the hash is non-numeric
	// otherwise it will be intercepted by globals.php
	$validate 	= 'j' . mosHash( $mainframe->getCfg( 'db' ) . $random );
	
	return $validate;
}


function mosHash( $seed ) {
global $iConfig_secret;
        return md5( $iConfig_secret . md5( $seed ) );
}

/**
* Session database table class
* @package Joomla
*/
class mosSession extends mosDBTable {
        /** @var int Primary key */
        var $session_id                        = null;
        /** @var string */
        var $time                                = null;
        /** @var string */
        var $userid                                = null;
        /** @var string */
        var $usertype                        = null;
        /** @var string */
        var $username                        = null;
        /** @var time */
        var $gid                                = null;
        /** @var int */
        var $guest                                = null;
        /** @var string */
        var $_session_cookie        = null;

        /**
        * @param database A database connector object
        */
        function mosSession( &$db ) {
                $this->mosDBTable( '#__session', 'session_id', $db );
        }

		/**
		 * @param string Key search for
		 * @param mixed Default value if not set
		 * @return mixed
		 */
		function get( $key, $default=null ) {
			return mosGetParam( $_SESSION, $key, $default );
		}

		/**
		 * @param string Key to set
		 * @param mixed Value to set
		 * @return mixed The new value
		 */
		function set( $key, $value ) {
			$_SESSION[$key] = $value;
			return $value;
		}

		/**
		 * Sets a key from a REQUEST variable, otherwise uses the default
		 * @param string The variable key
		 * @param string The REQUEST variable name
		 * @param mixed The default value
		 * @return mixed
		 */
		function setFromRequest( $key, $varName, $default=null ) {
			if (isset( $_REQUEST[$varName] )) {
				return mosSession::set( $key, $_REQUEST[$varName] );
			} else if (isset( $_SESSION[$key] )) {
				return $_SESSION[$key];
			} else {
				return mosSession::set( $key, $default );
			}
		}

		/**
		 * Insert a new row
		 * @return boolean
		 */
			function insert() {
					$ret = $this->_db->insertObject( $this->_tbl, $this );
	
					if( !$ret ) {
							$this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->stderr();
							return false;
					} else {
							return true;
					}
			}

		 /**
		 * Update an existing row
		 * @return boolean
		 */
			function update( $updateNulls=false ) {
				global $database;
				//ggr ($this);
					$ret = $database->updateObject( $this->_tbl, $this, 'session_id', $updateNulls );
	
					if( !$ret ) {
							$this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->stderr();
							return false;
					} else {
							return true;
					}
			}
			function iupdatereset( ) {
				global $database;
				
				$i24r = new mosDBTable( "#__session", "session_id", $database );
				$i24r->session_id = $this->session_id;
				$i24r->guest     = 1;
				$i24r->username  = '';
				$i24r->userid    = 0;
				$i24r->usertype  = '';
				$i24r->gid 		= 0;
				ggtr ($i24r);

				if (!$i24r->check()) {
					echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
//					return false;
				} else { $i24r->store(); /*return true;*/ }
				ggtr ($database);
				die();
			}
		/**
		 * Generate a unique session id
		 * @return string
		 */
			function generateId() {
				global $database;
					$failsafe = 20;
					$randnum = 0;
			
					while ($failsafe--) {
							$randnum = md5( uniqid( microtime(), 1 ) );
				$new_session_id = mosMainFrame::sessionCookieValue( $randnum );
				
				if ($randnum != '') {
									$query = "SELECT $this->_tbl_key"
									. "\n FROM $this->_tbl"
					. "\n WHERE $this->_tbl_key = " . $database->Quote( $new_session_id )
									;
									$database->setQuery( $query );
									if(!$result = $database->query()) {
											die( $database->stderr( true ));
									}
					
									if ($database->getNumRows($result) == 0) {
											break;
									}
							}
					}
	
					$this->_session_cookie = $randnum;
			$this->session_id 		= $new_session_id;
			}
	
		/**
		 * @return string The name of the session cookie
		 */
			function getCookie() {
	
					return $this->_session_cookie;
			}
	
		/**
		 * Purge lapsed sessions
		 * @return boolean
		 */
		function purge( $inc=1800, $and='' ) {
			global $mainframe, $database;
			
			if ($inc == 'core') {
				$past_logged 	= time() - $mainframe->getCfg( 'lifetime' );
				$past_guest 	= time() - $mainframe->getCfg( 'lifetime' );
	
				$query = "DELETE FROM $this->_tbl"
				. "\n WHERE ("
				// purging expired logged sessions
				. "\n ( time < '" . (int) $past_logged . "' )"
				. "\n AND guest = 0"
				. "\n AND gid > 0"
				. "\n ) OR ("
				// purging expired guest sessions
				. "\n ( time < '" . (int) $past_guest . "' )"
				. "\n AND guest = 1"
				. "\n AND userid = 0"
				. "\n )" ;
			} else {
			// kept for backward compatability
					$past = time() - $inc;
					$query = "DELETE FROM $this->_tbl WHERE ( time < '" . (int) $past . "' )"
					. $and ;
			}
			$database->setQuery($query); return $database->query();
		}
}
function josSpoofCheck( $header=NULL, $alt=NULL ) {	
//	ggtr (josSpoofValue(1));
	$validate 	= mosGetParam( $_POST, josSpoofValue($alt), 0 );
	
	// probably a spoofing attack
	if (!$validate) {
		header( 'HTTP/1.0 403 Forbidden' );		mosErrorAlert( "НЕ АВТОРИЗОВАН 1" ); 	return;
	}
	// First, make sure the form was posted from a browser.
	// For basic web-forms, we don't care about anything
	// other than requests from a browser:   
	if (!isset( $_SERVER['HTTP_USER_AGENT'] )) {
		header( 'HTTP/1.0 403 Forbidden' );	mosErrorAlert( "НЕ АВТОРИЗОВАН 2" );	return;
	}
	
	// Make sure the form was indeed POST'ed:
	//  (requires your html form to use: action="post")
	if (!$_SERVER['REQUEST_METHOD'] == 'POST' ) {
		header( 'HTTP/1.0 403 Forbidden' );	mosErrorAlert( "НЕ АВТОРИЗОВАН 3" );	return;
	}
	
	if ($header) {
	// Attempt to defend against header injections:
		$badStrings = array(
			'Content-Type:',
			'MIME-Version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:'
		);
		
		// Loop through each POST'ed value and test if it contains
		// one of the $badStrings:
		_josSpoofCheck( $_POST, $badStrings );
	}
}

function _josSpoofCheck( $array, $badStrings ) {
	// Loop through each $array value and test if it contains
	// one of the $badStrings
	foreach( $array as $v ) {
		if (is_array( $v )) {
			_josSpoofCheck( $v, $badStrings );
		} else {
			foreach ($badStrings as $v2) {
				if ( stripos( $v, $v2 ) !== false ) {
					header( 'HTTP/1.0 403 Forbidden' );
					mosErrorAlert( "НЕ АВТОРИЗОВАН 4" );
					exit(); // mosErrorAlert dies anyway, double check just to make sure
				}
			}
		}
	}
}







class mosUser extends mosDBTable {
        /** @var int Unique id*/
        var $id                                = null;
        /** @var string The users real name (or nickname)*/
        var $name                        = null;
        /** @var string The login name*/
        var $username                = null;
        /** @var string email*/
        var $email                        = null;
        /** @var string MD5 encrypted password*/
        var $password                = null;
        /** @var string */
        var $usertype                = null;
        /** @var int */
        var $block                        = null;
        /** @var int */
        var $sendEmail                = null;
        /** @var int The group id number */
        var $gid                        = null;
        /** @var datetime */
        var $registerDate        = null;
        /** @var datetime */
        var $lastvisitDate        = null;
        /** @var string activation hash*/
        var $activation                = null;
        /** @var string */
        var $params                        = null;

        /**
        * @param database A database connector object
        */
        function mosUser( &$database ) {
                $this->mosDBTable( '#__users', 'id', $database );
        }

        /**
         * Validation and filtering
         * @return boolean True is satisfactory
         */
        function check() {
                global $mosConfig_uniquemail;

                // Validate user information
                if (trim( $this->name ) == '') {
			$this->_error = addslashes( _REGWARN_NAME );
                        return false;
                }

                if (trim( $this->username ) == '') {
			$this->_error = addslashes( _REGWARN_UNAME );
                        return false;
                }

		// check that username is not greater than 25 characters
		$username = $this->username;
		if ( strlen($username) > 25 ) {
			$this->username = substr( $username, 0, 25 ); 
		}
		
		// check that password is not greater than 50 characters
		$password = $this->password;
		if ( strlen($password) > 50 ) {
			$this->password = substr( $password, 0, 50 ); 
		}
		
                if (eregi( "[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", $this->username) || strlen( $this->username ) < 3) {
			$this->_error = sprintf( addslashes( _VALID_AZ09 ), addslashes( _PROMPT_UNAME ), 2 );
                        return false;
                }

                if ((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email )==false)) {
			$this->_error = addslashes( _REGWARN_MAIL );
                        return false;
                }

                // check for existing username
                $query = "SELECT id"
                . "\n FROM #__users "
		. "\n WHERE username = " . $this->_db->Quote( $this->username )
		. "\n AND id != " . (int)$this->id
                ;
                $this->_db->setQuery( $query );
                $xid = intval( $this->_db->loadResult() );
                if ($xid && $xid != intval( $this->id )) {
			$this->_error = addslashes( _REGWARN_INUSE );
                        return false;
                }

                if ($mosConfig_uniquemail) {
                        // check for existing email
                        $query = "SELECT id"
                        . "\n FROM #__users "
			. "\n WHERE email = " . $this->_db->Quote( $this->email )
			. "\n AND id != " . (int)$this->id
                        ;
                        $this->_db->setQuery( $query );
                        $xid = intval( $this->_db->loadResult() );
                        if ($xid && $xid != intval( $this->id )) {
				$this->_error = addslashes( _REGWARN_EMAIL_INUSE );
                                return false;
                        }
                }

                return true;
        }

        function store( $updateNulls=false ) {
                global $acl, $migrate;
                $section_value = 'users';

                $k = $this->_tbl_key;
                $key =  $this->$k;
                if( $key && !$migrate) {
                        // existing record
                        $ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
                } else {
                        // new record
                        $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
                }
                if( !$ret ) {
                        $this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->getErrorMsg();
                        return false;
                } else {
                        return true;
                }
        }

        function delete( $oid=null ) {
                global $acl;

                $k = $this->_tbl_key;
                if ($oid) {
                        $this->$k = intval( $oid );
                }

                $query = "DELETE FROM $this->_tbl"
				. "\n WHERE $this->_tbl_key = " . (int) $this->$k
                ;
                $this->_db->setQuery( $query );

                if ($this->_db->query()) {
				// cleanup related data

				// :: private messaging
				$query = "DELETE FROM #__messages_cfg WHERE user_id = " . (int) $this->$k
				;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) {
						$this->_error = $this->_db->getErrorMsg(); return false;
				}
				$query = "DELETE FROM #__messages WHERE user_id_to = " . (int) $this->$k ;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) {
						$this->_error = $this->_db->getErrorMsg(); return false;
				}
				return true;
                } else {
                        $this->_error = $this->_db->getErrorMsg(); return false;
                }
        }

        function getUserListFromGroup( $value, $name, $recurse='NO_RECURSE', $order='name' ) {
                global $acl;

                // Change back in
                //$group_id = $acl->get_group_id( $value, $name, $group_type = 'ARO');
                $group_id = $acl->get_group_id( $name, $group_type = 'ARO');
                $objects = $acl->get_group_objects( $group_id, 'ARO', 'RECURSE');

                if (isset( $objects['users'] )) {
			mosArrayToInts( $objects['users'] );
                        $gWhere = '(id =' . implode( ' OR id =', $objects['users'] ) . ')';

                        $query = "SELECT id AS value, name AS text"
                        . "\n FROM #__users"
                        . "\n WHERE block = '0'"
                        . "\n AND " . $gWhere
                        . "\n ORDER BY ". $order
                        ;
                        $this->_db->setQuery( $query );
                        $options = $this->_db->loadObjectList();
                        return $options;
                } else {
                        return array();
                }
        }
}

$_MAMBOTS = new iService();

function icsmart($var, $defvalue=""){
	if (  isset($_SESSION["c_".$var])  ) return $_SESSION["c_".$var];
	else return $defvalue;
}
function icsmarti($var, $defvalue=0){
	if (  isset($_SESSION["c_".$var])  ) return $_SESSION["c_".$var];
	else return $defvalue;
}
function do_excatlist($parent, &$vcats, $excatlev, $ignored_id=0){
	$ivcats = ggsql ( "SELECT * FROM #__excat WHERE parent=".$parent." ORDER BY #__excat.order ASC " );
	foreach ($ivcats as $ivcat){
		if (  $ignored_id!=0  and  $ignored_id==$ivcat->id  ) continue;
		$name_prefix = "";  for ($j=0; $j<$excatlev; $j++) $name_prefix .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$vcats[] = mosHTML::makeOption( $ivcat->id, stripslashes($name_prefix.$ivcat->name) );
		$ivakc = ggsqlr ( "SELECT count(id) FROM #__excat WHERE parent=".$ivcat->id );
		if (  $ivakc>0  )  do_excatlist($ivcat->id, $vcats, ($excatlev+1), $ignored_id );
	}
}
function do_adcatlist($parent, &$vcats, $adcatlev, $ignored_id=0){
	$ivcats = ggsql ( "SELECT * FROM #__adcat WHERE parent=".$parent." ORDER BY #__adcat.order ASC " );
	foreach ($ivcats as $ivcat){
		if (  $ignored_id!=0  and  $ignored_id==$ivcat->id  ) continue;
		$name_prefix = "";  for ($j=0; $j<$adcatlev; $j++) $name_prefix .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$vcats[] = mosHTML::makeOption( $ivcat->id, stripslashes($name_prefix.$ivcat->name) );
		$ivakc = ggsqlr ( "SELECT count(id) FROM #__adcat WHERE parent=".$ivcat->id );
		if (  $ivakc>0  )  do_adcatlist($ivcat->id, $vcats, ($adcatlev+1), $ignored_id );
	}
}
function do_icatlist($parent, &$vcats, $excatlev, $ignored_id=0){
	$ivcats = ggsql ( "SELECT * FROM #__icat WHERE parent=".$parent." ORDER BY #__icat.order ASC " );
	foreach ($ivcats as $ivcat){
		if (  $ignored_id!=0  and  $ignored_id==$ivcat->id  ) continue;
		$name_prefix = "";  for ($j=0; $j<$excatlev; $j++) $name_prefix .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$vcats[] = mosHTML::makeOption( $ivcat->id, stripslashes($name_prefix.$ivcat->name) );
		$ivakc = ggsqlr ( "SELECT count(id) FROM #__icat WHERE parent=".$ivcat->id );
		if (  $ivakc>0  )  do_icatlist($ivcat->id, $vcats, ($excatlev+1), $ignored_id );
	}
}
function do_exfotolist($parent, &$vcats, $excatlev, $ignored_id=0){
	$ivcats = ggsql ( "SELECT * FROM #__exfoto WHERE parent=".$parent." ORDER BY #__exfoto.order ASC " );
	foreach ($ivcats as $ivcat){
		if (  $ignored_id!=0  and  $ignored_id==$ivcat->id  ) continue;
		$name_prefix = "";  for ($j=0; $j<$excatlev; $j++) $name_prefix .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$vcats[] = mosHTML::makeOption( $ivcat->id, stripslashes($name_prefix.$ivcat->name) );
		$ivakc = ggsqlr ( "SELECT count(id) FROM #__exfoto WHERE parent=".$ivcat->id );
		if (  $ivakc>0  )  do_exfotolist($ivcat->id, $vcats, ($excatlev+1), $ignored_id );
	}
}
function get_pi_name($pi){
switch ($pi) {
	case  0:	return "Системные вызовы (0)"; break;
	case  1:	return "Главная страница"; break;
	case  10:	return "Поиск"; break;
	case  11:	return "Страница входа / регистрации"; break;
	case  12:	return "Страница восстановления пароля"; break;
	case  20:	return "Форма обратной связи"; break;
	case  21:	return "Форма обратной связи со вложениями"; break;
	case  50:	return "Вопрос / ответ"; break;
	case  51:	return "Задать вопрос / ответ"; break;
	case  100:	return "Статичное содержимое"; break;
	case  200:	return "Новости / статьи"; break;
	case  300:	return "Каталог"; break;
	case  400:	return "Галлерея"; break;
//	default:	return "Системные ($pi)"; break;
	}
	$imenus = ggsql ("SELECT name, link FROM #__menu");
	foreach ($imenus as $imenu){
//		ggtr ($imenu);
		$strpi1 = strpos($imenu->link, "&pi");
		if (  !($strpi1 === false)  ){
			// ggtr ($imenu);
			$strpi = substr($imenu->link, $strpi1+4);
			$strpi2 = strpos($strpi, "&");
			if (  !($strpi2 === false)  ){
				$strpi = substr($strpi, 0, $strpi2);
			}
			// ggtr ($strpi,1);
			if (  $strpi*1.0==$pi  ) return $imenu->name;
		}
	}
	return "Системные ($pi)"; 
}

function clear_allf(){
	$f = array();
	$f[0] = "";
	$f[1] = "";
	$f[2] = "";
	$f[3] = "";
	$f[4] = "";
	$f[5] = "";
	$f[6] = "";
	$f[7] = "";
	return $f;
}
function get_real_fwehere($f){
	$fwehere = array();
	for ($i=0; $i<count($f); $i++  ){
		if (  strcmp($f[$i],"")!=0  )
			$fwehere[] = " f$i='".$f[$i]."' ";
	}
	return $fwehere;
}
function stat_fill_values(&$i24r, $f){
	if (  strcmp($f[0],"")!=0  ) $i24r->f0=$f[0];
	if (  strcmp($f[1],"")!=0  ) $i24r->f1=$f[1];
	if (  strcmp($f[2],"")!=0  ) $i24r->f2=$f[2];
	if (  strcmp($f[3],"")!=0  ) $i24r->f3=$f[3];
	if (  strcmp($f[4],"")!=0  ) $i24r->f4=$f[4];
	if (  strcmp($f[5],"")!=0  ) $i24r->f5=$f[5];
	if (  strcmp($f[6],"")!=0  ) $i24r->f6=$f[6];
	if (  strcmp($f[7],"")!=0  ) $i24r->f7=$f[7];
}
function uheck(){
	$msg = "Вы не авторизованы";
	mosRedirect( 'index.php?c=login', $msg );
}