<?php

// Установка флага родительского файла
define( "_VALID_INSITE", 1 );
define ('DIRSEP', DIRECTORY_SEPARATOR);

require_once( '../iconfig.php' );
require_once( '../i24.php' );
require_once( '../external_functions.php' );
require_once( '../imail.php' );
require_once( '../idb.php' );	$reg = new registry();	$reg['db'] = new database( $DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix );	$database = &$reg['db'];
require_once( '../icore.php' );

// делаем автоматический редирект на ссылку без www
if (  strpos($_SERVER['HTTP_HOST'],"www") === false  ){  } 
else{	?><script language="javascript"> location.href = "<? print site_url;	?>/iadmin/";  </script><?	}

// ЗАГРУЗКА ЯЗЫКОВОГО ФАЙЛА
$ilang_file = site_path."/ilang/".$ilang.".php";
require_once( $ilang_file );

if (isset( $_POST['submit'] )) {
	/** escape and trim to minimise injection of malicious sql */
	$usrname 	= stripslashes( mosGetParam( $_POST, 'usrname', NULL ) );
	$pass 		= stripslashes( mosGetParam( $_POST, 'pass', NULL ) );
	

	if($pass == NULL) {
		echo "<script>alert('Пожалуйста, введите пароль'); document.location.href='index.php?query_url=".codeurl_admin($_REQUEST['query_url'])."&mosmsg=Пожалуйста, введите пароль'</script>\n";
		exit();
	} else { $pass = md5( $pass ); }

	$query = "SELECT COUNT(*) FROM #__users WHERE gid in (23,24,25)";
	$database->setQuery( $query );
	$count = intval( $database->loadResult() );
	if ($count < 1) {
		mosErrorAlert( _LOGIN_NOADMINS );
	}

	$my = null;
	$query = "SELECT u.*"
	. "\n FROM #__users AS u"
	. "\n WHERE u.username = " . $database->Quote( $usrname )
	. "\n AND u.password = " . $database->Quote( $pass )."AND u.gid>=23" // bug fixed
	. "\n AND u.block = 0"
	;
	$database->setQuery( $query );
	$database->loadObject( $my );
	
	/** find the user group (or groups in the future) */
	if ($my->gid) {

		if ( $my->gid<23 ) {
			mosErrorAlert("Неверные имя пользователя, пароль, или уровень доступа.  Пожалуйста, повторите снова", "document.location.href='index.php'");
		}

		session_name( md5( site_url.$sitename ) );
		session_start();

		// construct Session ID
		$logintime 	= time();
		$session_id = md5( $my->id . $my->username . $my->usertype . $logintime );
		
		// add Session ID entry to DB
		$query = "INSERT INTO #__session"
		. "\n SET time = " . $database->Quote( $logintime ) . ", session_id = " . $database->Quote( $session_id ) . ", userid = " . (int) $my->id . ", usertype = " . $database->Quote( $my->usertype) . ", username = " . $database->Quote( $my->username )
		;
		$database->debug=1;
		$database->setQuery( $query );
//		ggd ($database);
		if (!$database->query()) {
			echo $database->stderr();
		}
		//ggr ($_VERSION->SITE);  ggdd ();
		// check if site designated as a production site 
		// for a demo site allow multiple logins with same user account
		if ( $_VERSION->SITE == 1 ) {
			// delete other open admin sessions for same account
			$query = "DELETE FROM #__session"
			. "\n WHERE userid = " . (int) $my->id
			. "\n AND username = " . $database->Quote( $my->username )
			. "\n AND usertype = " . $database->Quote( $my->usertype )
			. "\n AND session_id != " . $database->Quote( $session_id )
			// this ensures that frontend sessions are not purged
			. "\n AND guest = 1"
			. "\n AND gid = 0"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo $database->stderr();
			}
		}
		$_SESSION['session_id'] 		= $session_id;
		$_SESSION['session_user_id'] 	= $my->id;
		$_SESSION['session_username'] 	= $my->username;
		$_SESSION['session_usertype'] 	= $my->usertype;
		$_SESSION['session_gid'] 		= $my->gid;
		$_SESSION['session_logintime'] 	= $logintime;
		$_SESSION['session_user_params']= $my->params;
		$_SESSION['session_userstate'] 	= array();

		session_write_close();
		
		$expired = 'index2.php';
		
		// check if site designated as a production site 
		// for a demo site disallow expired page functionality
		if ( $_VERSION->SITE == 1 && @$mosConfig_admin_expired === '1' ) {
			$file 	= igetPath( 'com_xml', 'users' );
			$params = new mosParameters( $my->params, $file, 'component' );
			
			$now 	= time();

			// expired page functionality handling
			$expired 		= $params->def( 'expired', '' );
			$expired_time 	= $params->def( 'expired_time', '' );
	
			// if now expired link set or expired time is more than half the admin session life set, simply load normal admin homepage 	
			$checktime = ( $isession_life_admin ? $isession_life_admin : 1800 ) / 2;	
			if (!$expired || ( ( $now - $expired_time ) > $checktime ) ) {
				$expired = 'index2.php';
			}
			// link must also be a Joomla link to stop malicious redirection			
			if ( strpos( $expired, 'index2.php?option=com_' ) !== 0 ) {
				$expired = 'index2.php';
			}

			// clear any existing expired page data
			$params->set( 'expired', '' );
			$params->set( 'expired_time', '' );
			
			/*
			 * ЕСЛИ СЕССИЯ КОНЧИЛАСЬ - ТО ВОССТАНАВЛИВАЕМ ССЫЛКУ
			 */
			if (  isset($_REQUEST['query_url'])  ){
				$query_deurl = str_replace(  '_amp_', '&', $_REQUEST['query_url']  );
				$query_url = str_replace(  '_ravno_', '=', $query_url  );
				$expired = 'index2.php?'.$query_deurl;
			}
	
			// param handling
			if (is_array( $params->toArray() )) {
				$txt = array();
				foreach ( $params->toArray() as $k=>$v) {
					$txt[] = "$k=$v";
				}
				$saveparams = implode( "\n", $txt );
			}
	
			// save cleared expired page info to user data
			$query = "UPDATE #__users"
			. "\n SET params = " . $database->Quote( $saveparams )
			. "\n WHERE id = " . (int) $my->id
			. "\n AND username = " . $database->Quote( $my->username )
			. "\n AND usertype = " . $database->Quote( $my->usertype )
			;
			$database->setQuery( $query );
			$database->query();
		}
		
		/** cannot using mosredirect as this stuffs up the cookie in IIS */
		// redirects page to admin homepage by default or expired page
		/*
		 * ЕСЛИ СЕССИЯ КОНЧИЛАСЬ - ТО ВОССТАНАВЛИВАЕМ ССЫЛКУ
		 */
		if (  isset($_REQUEST['query_url'])  ){
			$query_deurl = str_replace(  '_amp_', '&', $_REQUEST['query_url']  );
			$query_deurl = str_replace(  '_ravno_', '=', $query_deurl  );
			$expired = 'index2.php?'.$query_deurl;
		}
		
		echo "<script>document.location.href='$expired';</script>\n";
		exit();
	} else { mosRedirect("index.php?query_url=".codeurl_admin($_REQUEST['query_url']), "Неверные имя пользователя или пароль. Пожалуйста, повторите снова."); }
} else {
	initGzip();
	$path = site_path . '/iadmin/theme/' . $adminTheme . '/login.php';
	require_once( $path );
	doGzip();
}
?>