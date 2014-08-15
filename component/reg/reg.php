<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

global $mosConfig_frontend_login, $mainframe, $option;

require_once( site_path."/component/reg/reg.html.php" );

if ( $mosConfig_frontend_login != NULL && ($mosConfig_frontend_login === 0 || $mosConfig_frontend_login === '0')) {
	echo _NOT_AUTH;
	return;
}
$task 			= mosGetParam( $_REQUEST, 'task', "" );			safelySqlStr ($task);
$c 				= mosGetParam( $_REQUEST, 'c', "" );			safelySqlStr ($c);
$option=$c;

switch( $task ) {
	case 'lostPassword':
		lostPassForm($c);
		break;

	case 'sendNewPass':
		sendNewPass( $c );
		break;

	case 'register':
		registerForm($c, 1);
		break;

	case 'saveRegistration':
		saveRegistration();
		break;

	case 'activate':
		activate( $option );
		break;
}

function lostPassForm( $option ) {
	global $mainframe;
	HTML_registration::lostPassForm($option);
}

function sendNewPass( $option ) {
	global $database;
	global $mosConfig_live_site, $mosConfig_sitename;
	global $mosConfig_mailfrom, $mosConfig_fromname, $sitename;
	  
	josSpoofCheck();
	
	$_live_site = $mosConfig_live_site;
	$_sitename 	= $mosConfig_sitename;
	$checkusername	= stripslashes( mosGetParam( $_POST, 'checkusername', '' ) );
	$confirmEmail	= stripslashes( mosGetParam( $_POST, 'confirmEmail', '') );

	$query = "SELECT id FROM #__users WHERE username = " .$database->Quote( $checkusername ). " AND email = " .$database->Quote( $confirmEmail );
	$database->setQuery( $query );
	$user_id = $database->loadResult();
	if (  isset($_REQUEST['4ajax'])  ){
		if(  !$user_id  ){	?> $("#insite_register_server_answer").html('— Пользователь с таким e-mail не зарегистрирован').jTypeWriter({duration:1.5}); <?	return;	}
	}
	else if (!$user_id || !$checkusername || !$confirmEmail) {	mosRedirect( "index.php?c=reg&task=lostPassword&mosmsg="._ERROR_PASS );		}

	$newpass = mosMakePassword();
	$message = _NEWPASS_MSG;
	eval ("\$message = \"$message\";");
	$subject = _NEWPASS_SUB;
	eval ("\$subject = \"$subject\";");

	global $MAILmailfrom, $MAILmailname;
	$mymail = new mymail();
	$mymail->add_address ( $confirmEmail );
	$mymail->set_subject ( $subject );
	$mymail->set_body	 ( str_replace("\n", "<br />", $message) );
	$mymail->send ();
	//mosMail($MAILmailfrom, $MAILmailname, $confirmEmail, $subject, $message);
	$newpass = md5( $newpass );
	$sql = "UPDATE #__users SET password = " . $database->Quote( $newpass ). " WHERE id = " . (int) $user_id;
	$database->setQuery( $sql );	if (!$database->query()) {		die("SQL error" . $database->stderr(true));	}
	if (  isset($_REQUEST['4ajax'])  ){
		?> $("#insite_register_server_answer").html('— <? print _NEWPASS_SENT ?>').jTypeWriter({duration:1.5}); <?	return;	
	}
	else mosRedirect( 'index.php?c=reg&pi=14&mosmsg='. _NEWPASS_SENT );
}

function registerForm( $option, $useractivation ) {
	global $mainframe;
	HTML_registration::registerForm($option, 1);
}

function saveRegistration() {
	global $database, $acl, $mosConfig_sitename, $mosConfig_live_site, $MAILuseractivation, $mosConfig_allowUserRegistration;
	global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_fromname;
	global $reg;
	$script_start=""; $script_end="";
	/*
	 * ЕСЛИ МЫ JAVASCRIPT СНАЧАЛА ЗАПИСЫВАЕМ В DIV И ЕГО УЖЕ ОТТУДА ВЫПОЛНЯЕТ БРАУЗЕР
	 */
	if (  $_REQUEST['script_mode']=='html2script'  ){
		$script_start='<script language="javascript">'; $script_end='</script>';
	}
	
	if (  isset($_REQUEST['4ajax'])  ){
		$captcha = new captcha();
		$captcha->codeid_id="insite_login_register_codeid";

		if(  ggrr('username')==''  ){ print $script_start; ?> $("#insite_register_server_answer").html('— Заполните поле «Логин»').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }
                if(  ggsqlr (  "select count(id) from  #__users WHERE username='".ggrr('username')."' "  )>0  ) { print $script_start; ?> $("#insite_register_server_answer").html('— Логин "<?=ggrr('username') ?>" уже занят').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }
                if(  preg_match("/[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]/", ggrr('username'))  )  { print $script_start; ?> $("#insite_register_server_answer").html('— Логин "<?=ggrr('username') ?>" содержит неправильные символы').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }
                if(  mb_strlen(ggrr('username'), 'utf-8')<3  )  { print $script_start; ?> $("#insite_register_server_answer").html('— Логин "<?=ggrr('username') ?>" слишком короткий, не менее 3 символов ').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }

		if(  ggrr('email')==''  ){ print $script_start; ?> $("#insite_register_server_answer").html('— Заполните поле «E-mail»').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }
		if(  ggrr('password')==''  ){ print $script_start; ?> $("#insite_register_server_answer").html('— Введите пароль').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }
		if(  ggrr('password')!=ggrr('password2')  ){ print $script_start; ?> $("#insite_register_server_answer").html('— Пароли не совпадают').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }
		if(  !$captcha->check_me()  ){ print $script_start; ?> $("#insite_register_server_answer").html('— Введено неверный код безопасности').jTypeWriter({duration:1.5}); <?	print $script_end; return;  }
	}
	josSpoofCheck();

	$row = new mosDBTable( "#__users", "Id", $database ); //new mosUser( $database );
	
	$row->name = $_POST['name'];
	$row->usersurname = $_POST['usersurname'];
	$row->userparentname = $_POST['userparentname'];
	
	$row->username = $_POST['uname'];
	$row->email = $_POST['email'];
	$row->password = $_POST['passwd'];
	if (!$row->bind( $_POST, 'usertype' )) {		mosErrorAlert( $row->getError() );	}
	$row->name		= trim( $row->name );
	$row->email		= trim( $row->email );
	$row->username	= trim( $row->username );
	$row->password	= trim( $row->password );
	$row->id = 0;
	$row->usertype = '';
	$row->gid = 18;
	$row->activation = md5( mosMakePassword() );
	if (  $reg['mail_useractivation']==0)	$row->block = '0'; else $row->block = '1';
	$pwd 				= $row->password;
	$row->password 		= md5( $row->password );
	$row->registerDate 	= date('Y-m-d H:i:s');
	$row->params = "1";
	if (!$row->check()) {		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";	} else $row->store();	
	$new_user_id=$row->Id;
	
	$name 		= trim($row->name);
	$email 		= trim($row->email);
	$username 	= trim($row->username);
	$subject 	= sprintf (  _SEND_SUB, $name, short_surl()  );
	$subject 	= html_entity_decode($subject, ENT_QUOTES);

	$activation_link = site_url."/reg/activate?activation=".$row->activation;	
	if (  $reg['mail_useractivation'] == 1){		$message = sprintf (_USEND_MSG_ACTIVATE, $name, short_surl(), $activation_link, $activation_link, site_url, $username, $pwd);	}
	else {	$message = sprintf (_USEND_MSG, $name, short_surl(), $mosConfig_live_site);	}
	$message = html_entity_decode($message, ENT_QUOTES);
	
	// check if Global Config `mailfrom` and `fromname` values exist
	if ($mosConfig_mailfrom != '' && $mosConfig_fromname != '') {
		$adminName2 = $mosConfig_fromname;
		$adminEmail2 = $mosConfig_mailfrom;
	} else {
	// use email address and name of first superadmin for use in email sent to user
		$query = "SELECT name, email FROM #__users WHERE LOWER( usertype ) = 'superadministrator' OR LOWER( usertype ) = 'super administrator' OR LOWER( usertype ) = 'супер администратор'";
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		$row2 			= $rows[0];
		$adminName2 	= $row2->name;
		$adminEmail2 	= $row2->email;
	}
	// Send email to user
	$backlinkgfg = ggo (1, "#__backlinkcfg");
	$mymail = new mymail();
	$mymail->add_address ( $email );
	$mymail->set_subject ( $subject );
	$mymail->set_body	 ( str_replace("\r\n", "<br />", $message) );
	$mymail->send ();
	//mosMail($backlinkgfg->order_mail_from, $backlinkgfg->order_mail_from_name, $email, $subject, $message);

	// Send notification to all administrators
	$subject2 = sprintf (_SEND_SUB, $name, short_surl());
	$message2 = sprintf (_ASEND_MSG, $reg['surl'], $mosConfig_sitename, $row->name, $email, $username);
	$subject2 = html_entity_decode($subject2, ENT_QUOTES);
	$message2 = html_entity_decode($message2, ENT_QUOTES);

	// get email addresses of all admins and superadmins set to recieve system emails
	$query = "SELECT email, sendEmail FROM #__users WHERE ( gid = 24 OR gid = 25 ) AND sendEmail = 1 AND block = 0";
	$database->setQuery( $query );	$admins = $database->loadObjectList();
	
	foreach ( $admins as $admin ) {	// send email to admin & super admin set to recieve system emails
//		$mymail->clear_addresses();
//		$mymail->add_address ( $admin->email );
//		$mymail->set_subject ( $subject2 );
//		$mymail->set_body	 ( $message2 );
//		$mymail->send ();
		//mosMail($adminEmail2, $adminName2, $admin->email, $subject2, $message2);
	}
	if (  isset($_REQUEST['4ajax'])  ){	
		/*
		 * РЕГИСТРАЦИЯ ПРОИСХОДИТ ИЗ КОРЗИНЫ ПРИ ОФОРМЛЕНИИ ЗАКАЗА
		 */
		if (  ggri('register_from_order')==1  ){
			if ( $reg['mail_useractivation'] == 1 ){ $_REQUEST['register_ok']=1; print $script_start; ?> $('#insite_register_main_span').fadeOut(300, function(){
																	$("#insite_register_server_answer").hide();
																	$("#insite_register_server_answer").html('— <? echo _REG_COMPLETE_ACTIVATE ?>');
																	$("#insite_register_server_answer").fadeIn(700);
																  }); <? print $script_end; } 
			else {	$_REQUEST['register_ok']=1; print $script_start; ?> $('#ex_order_register_table').fadeOut(300, function(){
																	$("#ex_order_register_table").html('<tr height=150><td valign="middle" style="vertical-align:middle; padding-left:17px;">— <em>Регистрация завершена!</em><br />После оформления заказа Вы можете посмотреть статус и историю его обработки онлайн в личном кабинете<br /><br /><?
																	?><a href="javascript: ins_ajax_open(\'/<?=$reg['ex_seoname'] ?>/shop.html?4ajax=1&show_order=order_contact_form&floating=1&show_order_register_me=1&uid=<?=$new_user_id ?>\', 0, 0); void(0);">Далее</a></td></tr>');
																	$("#ex_order_register_table").fadeIn(700);
																  }); <? print $script_end; }
		}
		/*
		 * ОБЫЧНАЯ РЕГИСТРАЦИЯ В СПЛЫВАЮЩЕМ ОКНЕ
		 */
		else {
			if ( $reg['mail_useractivation'] == 1 ){ $_REQUEST['register_ok']=1; print $script_start; ?> $('#insite_register_main_span').fadeOut(300, function(){
																	$("#insite_register_server_answer").hide();
																	$("#insite_register_server_answer").html('— <? echo _REG_COMPLETE_ACTIVATE ?>');
																	$("#insite_register_server_answer").fadeIn(700);
																  }); <? print $script_end; } 
			else {	$_REQUEST['register_ok']=1; print $script_start; ?> $('#insite_register_main_span').fadeOut(300, function(){
																	$("#insite_register_server_answer").hide();
																	$("#insite_register_server_answer").html('— <? echo _REG_COMPLETE ?>');
																	$("#insite_register_server_answer").fadeIn(700);
																  }); <? print $script_end; }
		}
	}
	else{
		if ( $reg['mail_useractivation'] == 1 ){ echo _REG_COMPLETE_ACTIVATE; } 
		else {	echo _REG_COMPLETE;	}
	}
	 
}

function activate( $c ) {
	global $database, $my;
	global $MAILuseractivation, $mosConfig_allowUserRegistration;
	
	$option=$c;
	if($my->id) {
		// They're already logged in, so redirect them to the home page
		mosRedirect( 'index.php' );
	}
		

	if ($mosConfig_allowUserRegistration == '0' || $MAILuseractivation == '0') {
		mosNotAuth();
		return;
	}

	$activation = stripslashes( mosGetParam( $_REQUEST, 'activation', '' ) );

	if (empty( $activation )) {
		echo _REG_ACTIVATE_NOT_FOUND;
		return;
	}

	$query = "SELECT id"
	. "\n FROM #__users"
	. "\n WHERE activation = " . $database->Quote( $activation )
	. "\n AND block = 1"
	;
	$database->setQuery( $query );
	$result = $database->loadResult();

	if ($result) {
		$query = "UPDATE #__users"
		. "\n SET block = 0, activation = ''"
		. "\n WHERE activation = " . $database->Quote( $activation )
		. "\n AND block = 1"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			if(!defined(_REG_ACTIVATE_FAILURE)) {
				DEFINE('_REG_ACTIVATE_FAILURE', '<div class="componentheading">Ошибка активации!</div><br />Активация вашей учетной записи невозможна. Пожалуйста, обратитесь к администратору.');
		}
			echo _REG_ACTIVATE_FAILURE;
		} else {
		echo _REG_ACTIVATE_COMPLETE;
		}
	} else {
		echo _REG_ACTIVATE_NOT_FOUND;
	}
	 
}
?>
