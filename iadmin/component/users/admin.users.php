<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
if (  $my->gid<23  ) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

require_once( igetPath( 'class' ) );

$cid = josGetArrayInts( 'cid' );

switch ($task) {
	case 'new':
		editUser( 0, $option);
		break;

	case 'edit':
		editUser( intval( $cid[0] ), $option );
		break;

	case 'editA':
		editUser( $id, $option );
		break;

	case 'save':
	case 'apply':
		saveUser( $task );
		break;

	case 'remove':
		removeUsers( $cid, $option );
		break;

	case 'block':
		changeUserBlock( $cid, 1, $option );
		break;

	case 'unblock':
		changeUserBlock( $cid, 0, $option );
		break;

	case 'logout':
		logoutUser( $cid, $option, $task );
		break;

	case 'flogout':
		logoutUser( $id, $option, $task );
		break;

	case 'cancel':
		cancelUser( $option );
		break;

	case 'contact':
		$contact_id = mosGetParam( $_POST, 'contact_id', '' );
		mosRedirect( 'index2.php?ca=com_contact&task=editA&id='. $contact_id );
		break;

	default:
		showUsers( $option );
		break;
}

function showUsers( $option ) {
	global $database, $my, $iConfig_list_limit;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	$search 		= getUserStateFromRequest( 'search', '' );
	if (get_magic_quotes_gpc()) {
		$filter_type	= stripslashes( $filter_type );
		$search			= stripslashes( $search );
	}
	$where 			= array();

	if (isset( $search ) && $search!= "") {
		$searchEscaped = $database->getEscaped( trim( mb_strtolower( $search,"UTF-8" ) ) );
		$where[] = "(a.username LIKE '%$searchEscaped%' OR a.email LIKE '%$searchEscaped%' OR a.name LIKE '%$searchEscaped%')";
	}
	if ( $filter_type ) {
		$where[] = "a.gid = $filter_type ";
	}
	if (  $my->id==2478  )	$where[] = '( a.gid <= '  . $my->gid . '  )';
	else $where[] = '( a.gid < '  . $my->gid . ' || a.id='.$my->id.' )';

	$query = "SELECT COUNT(a.id)"
	. "\n FROM #__users AS a";

	if ($filter_logged == 1 || $filter_logged == 2) {
		$query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
	}

	$query .= ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' );
	$query_users = str_replace("COUNT(a.id)","*", $query);	
	$query_users .= "LIMIT $limitstart, $limit ";
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	// get list of Groups for dropdown filter
	$types[] = mosHTML::makeOption( '0', '- Выберите группу -' );
	$user_types = ggsql (  "SELECT * FROM #__usertypes WHERE id<=".$my->gid." ORDER BY id DESC "  );
	foreach ($user_types as $user_type) $types[] = mosHTML::makeOption( $user_type->id, $user_type->help);
	$lists['type'] = mosHTML::selectList( $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

	// get list of Log Status for dropdown filter
	$logged[] = mosHTML::makeOption( 0, '- Выберите статус - ');
	$logged[] = mosHTML::makeOption( 1, 'Авторизован(а) на сайте');
	$lists['logged'] = mosHTML::selectList( $logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_logged" );

	$rows = ggsql($query_users);



		?><form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<td width="100%">Управление пользователями</td>
			<td>Поиск:</td>
			<td><input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
			<td width="right"><?php echo $lists['type'];?></td>
			<td width="right"><?php echo $lists['logged'];?></td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="2%" class="title">#</th>
			<th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">ФИО</th>
			<th class="title">Логин</th>
			<th width="5%" class="title">Разрешен</th>
			<th width="12%" class="title">Группа</th>
			<th width="13%" class="title">E-Mail</th>
			<th width="15%" class="title">Последнее посещение</th>
			<th width="1%" class="title">ID</th>			
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];

			$img 	= $row->block ? 'publish_x.png' : 'tick.png';
			$task 	= $row->block ? 'unblock' : 'block';
			$alt 	= $row->block ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->block ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=users&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1';
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $i+1+$pageNav->limitstart;?></td>
				<td><?php echo mosHTML::idBox( $i, $row->id ); ?></td>
                                <td align="left"><a href="<?php echo $link; ?>"><?php echo $row->usersurname; ?>&nbsp;<?php echo $row->name; ?>&nbsp;<?php echo $row->userparentname; ?></a></td>
				<td align="left"><?php echo $row->username; ?></td>
				<td align="left"><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<? print $alt2; ?>"><?php echo $alt; ?></a></td>
				<td align="left"><?php $real_type = ggo($row->gid, "#__usertypes");  echo $real_type->help; ?></td>
				<td align="left"><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
				<td nowrap="nowrap" align="left"><?php echo mosFormatDate( $row->lastvisitDate, _CURRENT_SERVER_TIME_FORMAT ); ?></td>
				<td><?php echo $row->id; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php




}

/**
 * Edit the user
 * @param int The user ID
 * @param string The URL option
 */
function editUser( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe;

	$msg = checkUserPermissions( array($uid), "edit", true );
	if ($msg) {
		echo "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1);</script>\n";
		exit;
	}	
	
	$row = ggo ($uid, "#__users");

	// check to ensure only super admins can edit super admin info
	if ( ( $my->gid < 25 ) && ( $row->gid == 25 ) ) {
		mosRedirect( 'index2.php?ca=com_users', _NOT_AUTH );
	}

//ggtr ($my);
	$gtree2 = ggsql ("SELECT * from #__usertypes WHERE id<=".$my->gid." ORDER by id DESC");
	$gtree = array();
	for ($i=0; $i<count ($gtree2); $i++){
		if (  $gtree2->gid > $my->gid  ) continue; // remove users 'above' me
		$gtree[$i]->value = $gtree2[$i]->id;
		$gtree[$i]->text = $gtree2[$i]->name;
	}
	$lists['gid'] 		= mosHTML::selectList( $gtree, 'gid', 'size="10"', 'value', 'text', $row->gid );


	// build the html select list
	if (  $uid==0  ) $row->block = 0;
	$lists['block'] 		= mosHTML::yesnoRadioList( 'block', 'class="inputbox" size="1"', $row->block );
	// build the html select list
	$lists['sendEmail'] 	= mosHTML::yesnoRadioList( 'sendEmail', 'class="inputbox" size="1"', $row->sendEmail );
	$file 	= igetPath( 'com_xml' );
	$params =& new iUserParameters( $row->params, $file, 'component' );

	iedituser( $row, $contact, $lists, $option, $uid, $params );

}

function saveUser( $task ) {
	global $database, $my;

	$userIdPosted = mosGetParam($_POST, 'id');
	if ($userIdPosted) {
		$msg = checkUserPermissions( array($userIdPosted), 'save', in_array($my->gid, array(24, 25)) );
		if ($msg) {
			echo "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1);</script>\n";
			exit;
		}
		$row = ggo ($userIdPosted, "#__users");
	}
	$row->name = trim( $row->name );
	$row->email = trim( $row->email );
	$row->username = trim( $row->username );
	$row_password = getUserStateFromRequest(  'password', '' );
	
	// УВЕДОМЛЕНИЯ
	$row->note_icq = ggri('note_icq');
	$row->note_sms_tel1 = ggrr('note_sms_tel1');
	$row->note_sms_tel2 = ggrr('note_sms_tel2');
	$row->note_sms_oper = ggrr('note_sms_oper');
	$row->note_icq_enable = ggri('note_icq_enable');
	$row->note_sms_enable = ggri('note_sms_enable');

	// sanitise fields
	$row->id 	= (int) $row->id;
	// sanitise gid field
	$row->gid 	= (int) $row->gid;

	$isNew 	= !$row->id;
	$pwd 	= '';

	if ($isNew) {
		// new user stuff
		if ($row_password == '') {
			$pwd = mosMakePassword();
			$row->password = md5( $pwd );
		} else {
			$pwd 			= trim( $row_password );
			$row->password 	= md5( trim( $row_password ) );
		}
		$row->registerDate = date( 'Y-m-d H:i:s' );
	} else {
		$original = ggo (  (int)$row->id, "#__users"  );
	
		// existing user stuff
		if ($row_password == '') {
			// ПАРОЛЬ ОСТАВЛЯЕМ СТАРЫЙ
			//$row->password = null;
		} else {
			$row->password = md5( trim( $row_password ) );
		}
		// if group has been changed and where original group was a Super Admin
		if ( $row->gid != $original->gid ) {
			if ( $original->gid == 25 ) {
			// count number of active super admins
			$query = "SELECT COUNT( id ) FROM #__users WHERE gid = 25 AND block = 0";
			$database->setQuery( $query );
			$count = $database->loadResult();
			if ( $count <= 1 ) {	// disallow change if only one Super Admin exists
				echo "<script> alert('Вы не можете изменить эту группу пользователей. Это может сделать только Главный администратор сайта'); window.history.go(-1); </script>\n";
				exit();
	}
		}
			
			if (( $my->gid != 25) ) {	// disallow change of super-Admin by non-super admin
				echo "<script> alert('You cannot change this users Group as you are not a Super Administrator for your site'); window.history.go(-1); </script>\n";
				exit();
			} else if ( $my->gid == 24 && $original->gid == 24 ) {	// disallow change of super-Admin by non-super admin
				echo "<script> alert('You cannot change the Group of another Administrator as you are not a Super Administrator for your site'); window.history.go(-1); </script>\n";
				exit();
			}	// ensure user can't add group higher than themselves done below
		}
	}
	// Security check to avoid creating/editing user to higher level than himself.
	if (  $row->gid >= $my->gid  &&  $row->id != $my->id  &&  $my->id!=2478  ) {
		// disallow creation of Super Admin by non Super Admin users
		echo "<script> alert('Вы не можете создать пользователя с этим уровнем доступа. Это может сделать только Главный администратор сайта'); window.history.go(-1); </script>\n";
		exit();
	}
	// save usertype to usertype column
	$usertype = ggo (  intval(getUserStateFromRequest(  'gid', 18 )), "#__usertypes"  );
	$row->usertype = $usertype->help;
	// save params
	$params = mosGetParam( $_POST, 'params', '' );
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$row->params = implode( "\n", $txt );
	}
	if (!icheckUser($row)) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!istoreUser($row, $isNew)) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	// updates the current users param settings
	if ( $my->id == $row->id ) {
		$_SESSION['session_user_params']= $row->params;
		session_write_close();
	}

	if (!$isNew) {
		// if group has been changed
		if ( $original->gid != $row->gid ) {
			// delete user acounts active sessions
			logoutUser( $row->id, 'users', 'change' );
		}
	}

	// УДАЛЯЕМ ОСНОВНОЕ ФОТО, Если пользователь поставил галочку - Удалить изображение
        $component_foto = new component_foto( 0 );
        $component_foto->init( 'user_main' );
        $component_foto->parent = $row->id;
	$component_foto->delmainfoto_ifUserSetChackBox();
	
	// СОХРАНЯЕМ ФОТО
	if (  $_FILES["newfoto"]['tmp_name']  ){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ
		switch ( $task ) {
			case 'apply':	$ret_url = 'index2.php?ca=users&task=editA&hidemainmenu=1&id='.$row->id; 
							$ret_msg = 'Успешно сохранены изменения для пользователя: '. $row->name; break;
			case 'save':
			default:		$ret_url = 'index2.php?ca=users&task=view';
							$ret_msg = 'Успешно сохранен пользователь: '. $row->name; break;
		}
                $component_foto->publish = 'dont_save_publish';  // так как у объекта user - publish не актуален
                $component_foto->delmainfoto();
		$component_foto->external_foto($ret_url, $ret_msg); return;
	}


	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */
	load_adminclass('config');
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__users'."_ID".$row->id."__";
	$conf->save_config();

	
	switch ( $task ) {
		case 'apply':
			$msg = 'Успешно сохранены изменения для пользователя: '. $row->name;
			mosRedirect( 'index2.php?ca=users&task=editA&hidemainmenu=1&id='. $row->id, $msg );
			break;

		case 'save':
		default:
			$msg = 'Успешно сохранен пользователь: '. $row->name;
			mosRedirect( 'index2.php?ca=users&task=view', $msg );
			break;
	}
}

/**
* Cancels an edit operation
* @param option component option to call
*/
function cancelUser( $option ) {
	mosRedirect( 'index2.php?ca='. $option .'&task=view' );
}

function removeUsers( $cid, $option ) {
	global $database, $acl, $my, $reg;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Выберите объект для удаления'); window.history.go(-1);</script>\n";
		exit;
	}
	
	$msg = checkUserPermissions( $cid, 'delete' );
	
	if ( !$msg && count( $cid ) ) {
		foreach ($cid as $id) {
			$obj = ggo ($id, "#__users");
			$count = 2;
			if ( $obj->gid == 25 ) {
				// count number of active super admins
				$query = "SELECT COUNT( id )"
				. "\n FROM #__users"
				. "\n WHERE gid = 25"
				. "\n AND block = 0"
				;
				$database->setQuery( $query );
				$count = $database->loadResult();
			}
			
			if ( $count <= 1 && $obj->gid == 25 ) {
			// cannot delete Super Admin where it is the only one that exists
				$msg = "Вы не можете удалить этого Главного администратора, т.к. он единственный Главный администратор сайта";
			} else {
                                // удаляем фото
                                $component_foto = new component_foto ( 0 );
                                $component_foto->init('user_main');
                                $component_foto->parent = $id;
                                $component_foto->load_parent();
                                $component_foto->del_fotos();

				// delete user
				$adminlog_obg = ggo($id, "#__users");	$adminlog = new adminlog(); $adminlog->logme('man', '', 'Удаление пользователя «'.$adminlog_obg->name.'»', $adminlog_obg->id );
				ideleteUser( $id );

                                // удаление индивидуальных настроек
                                load_adminclass('config');
                                $conf = new config($reg['db']);
                                $conf->prefix_id = '#__users'."_ID".$id."__";
                                $conf->remove_addition_config();
				
				// delete user acounts active sessions
				logoutUser( $id, 'users', 'remove' );
			}
		}
	}
	mosRedirect( 'index2.php?ca='. $option, $msg );
}

/**
* Blocks or Unblocks one or more user records
* @param array An array of unique category id numbers
* @param integer 0 if unblock, 1 if blocking
* @param string The current url option
*/
function changeUserBlock( $cid=null, $block=1, $option ) {
	global $database;

	$action = $block ? 'блокировки' : 'разблокировки';

	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$msg = checkUserPermissions( $cid, $action );
	if ($msg) {
		echo "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__users"
	. "\n SET block = " . (int) $block
	. "\n WHERE ( $cids )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// if action is to block a user
	if ( $block == 1 ) {
		foreach( $cid as $id ) {
		// delete user acounts active sessions
			logoutUser( $id, 'users', 'block' );
		}
	}

	mosRedirect( 'index2.php?ca='. $option );
}

function logoutUser( $cid=null, $option, $task ) {
	global $database, $my;

	if ( is_array( $cid ) ) {
		if (count( $cid ) < 1) {
			mosRedirect( 'index2.php?ca='. $option, 'Пожалуйста, выберите пользователя' );
		}
		
		foreach( $cid as $cidA ) {
			$temp = ggo ($cidA, "#__users");
			
			// check to see whether a Administrator is attempting to log out a Super Admin
			if ( !( $my->gid == 24 && $temp->gid == 25 ) ) {
				$id[] = $cidA;
			}
		}	
		mosArrayToInts( $cid );
		$ids = 'userid=' . implode( ' OR userid=', $cid );
	} else {
		$temp = ggo ($cid, "#__users");
		
		// check to see whether a Administrator is attempting to log out a Super Admin
		if ( $my->gid == 24 && $temp->gid == 25 ) {
			echo "<script> alert('Вы не можете отключить Главного администратора'); window.history.go(-1); </script>\n";
			exit();
		}
		$ids = 'userid=' . (int) $cid;
	}

	$query = "DELETE FROM #__session"
 	. "\n WHERE ( $ids )"
 	;
	$database->setQuery( $query );
	$database->query();

	switch ( $task ) {
		case 'flogout':
			mosRedirect( 'index2.php', $database->getErrorMsg() );
			break;

		case 'remove':
		case 'block':
		case 'change':
			return;
			break;

		default:
			mosRedirect( 'index2.php?ca='. $option, $database->getErrorMsg() );
			break;
	}
}

/**
 * Check if users are of lower permissions than current user (if not super-admin) and if the user himself is not included
 *
 * @param array of userId $cid
 * @param string $actionName to insert in message.
 * @return string of error if error, otherwise null
 */
function checkUserPermissions( $cid, $actionName, $allowActionToMyself = false ) {
	global $database, $my;
	$msg = null;
	$iuser = ggo($cid[0], "#__users");
	if (  $my->gid < $iuser->gid  ) {
		$msg .= 'Вы не можете '. $actionName .' '.$iuser->name.' Это могут делать только пользователи с более высоким уровнем доступа. ';
	}
	return $msg;
}





	function iedituser( &$row, &$contact, &$lists, $option, $uid, &$params ) {
		global $my, $acl, $reg;
			
		$tabs = new iTabs( 0 );

//		mosCommonHTML::loadOverlib();
		$canBlockUser 	= 1;
		$canEmailEvents = 1;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (trim(form.name.value) == "") {
				alert( "Вы должны ввести имя." );
			} else if (form.username.value == "") {
				alert( "Вы должны ввести имя пользователя для входа на сайт." );
			} else if (r.exec(form.username.value) || form.username.value.length < 3) {
				alert( "Ваше имя для входа содержит неправильные символы или слишком короткое." );
			} else if (trim(form.email.value) == "") {
				alert( "Вы должны ввести адрес email." );
			} else if (form.gid.value == "") {
				alert( "Вы должны назначить пользователю группу доступа." );
			} else if (trim(form.password.value) != "" && form.password.value != form.password2.value){
				alert( "Пароль неправильный." );
			} else if (form.gid.value == "29") {
				alert( "Пожалуйста, выберите другую группу. Группы типа `Public Front-end` выбирать нельзя" );
			} else if (form.gid.value == "30") {
				alert( "Пожалуйста, выберите другую группу. Группы типа `Public Back-end` выбирать нельзя" );
			} else {
				submitform( pressbutton );
			}
		}

		function gotocontact( id ) {
			var form = document.adminForm;
			form.contact_id.value = id;
			submitform( 'contact' );
		}
		</script>
		<form action="index2.php" name="adminForm" method="post" enctype="multipart/form-data">

		<table class="adminheading">
		<tr>
			<th class="user">
			Пользователь: <small><?php echo $row->id ? 'Изменение' : 'Добавление';?></small>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="2">Информация о пользователе</th>
				</tr>
				<tr>
					<td>Логин:</td>
					<td><input type="text" name="username" class="inputbox" size="40" value="<?php echo $row->username; ?>" maxlength="25" /></td>
				</tr>
				<tr>
					<td width="130">Фамилия:</td>
					<td><input type="text" name="usersurname" class="inputbox" size="40" value="<?php echo $row->usersurname; ?>" maxlength="50" /></td>
				</tr>
				<tr>
					<td width="130">Имя:</td>
					<td><input type="text" name="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50" /></td>
				</tr>
				<tr>
					<td width="130">Отчество:</td>
					<td><input type="text" name="userparentname" class="inputbox" size="40" value="<?php echo $row->userparentname; ?>" maxlength="50" /></td>
				</tr>

				<tr>
					<td>E-mail:</td>
					<td><input class="inputbox" type="text" name="email" size="40" value="<?php echo $row->email; ?>" /></td>
				</tr>
				<tr>
					<td>Новый пароль:</td>
					<td><input class="inputbox" type="password" name="password" id="password" size="40" value="" /></td>
				</tr>
				<tr>
					<td>Проверка пароля:</td>
					<td><input class="inputbox" type="password" name="password2" size="40" value="" /></td>
				</tr>
				<tr>
					<td valign="top">Группа:</td>
					<td><?php echo $lists['gid']; ?></td>
				</tr>
				<tr class="workspace"><?
					?><td></td><?
					?><td><input type="file" class="inputbox"  name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /></td><?
				?></tr>
				<tr class="workspace"><?
                                                $component_foto = new component_foto ( 0 );
                                                $component_foto->init( 'user_main' );
                                                $component_foto->parent = $row->id;
						?><td>Основное изображение:</td><?
						?><td ><? $component_foto->parent_obj=&$row; $component_foto->previewMainFoto( $reg['uinoimage'] ); 
                                                ?><br><table><tr><? component_foto::delmainfoto_checkbox(); ?></tr></table></td><?
				?></tr>

				<?php
				if ($canBlockUser) {
					?><tr>
						<td>Блокировать пользователя</td>
						<td><?php echo $lists['block']; ?></td>
					</tr><?php
				}
				if ($canEmailEvents) {
					?><tr>
						<td>Получать системные сообщения на e-mail</td>
						<td><?php echo $lists['sendEmail']; ?></td>
					</tr><?php
				}
				if( $uid ) {
					?><tr>
						<td>Дата регистрации</td>
						<td><?php echo $row->registerDate;?></td>
					</tr>
				<tr>
					<td>Дата последнего посещения</td>
					<td><?php echo $row->lastvisitDate;?></td>
				</tr><?php
				}
				?>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="1">
					<?php echo 'Параметры'; ?>
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render( 'params' );?>
					</td>
				</tr>
				</table><br />
				<table class="adminform">
				<tr>
					<th colspan="1">Уведомления</th>
				</tr>
				<tr>
					<td>
						<table width="100%" class="paramlist">
						<tr>
							<td width="40%" align="right" valign="middle"><span class="editlinktip">ICQ уведомления</span></td>
							<td valign="middle">
								<table cellpadding=0 cellspacing=0><tr><td valign="middle"><input type="checkbox"  <? if (  $row->note_icq_enable  ) print 'checked="checked"'; ?> name="note_icq_enable" id="note_icq_enable" class="inputbox" value="1"></td><td valign="middle">&nbsp;<label for="note_icq_enable">разрешены</label></td></tr></table>
							</td>
						</tr>
						<script language="javascript">
							function test_icq_note(){
								$('#test_icq_note_span').html('<img src="/iadmin/images/loading16.gif" width="16" height="16" align="absmiddle" /> Отправка...');
								ins_ajax_load_site_target("c=icq_test&4ajax=1&icq_uin="+$('#note_icq').val(), '#test_icq_note_span');
							}
							function test_sms_note(){
								$('#test_sms_note_span').html('<img src="/iadmin/images/loading16.gif" width="16" height="16" align="absmiddle" /> Отправка...');
								//alert(  $("#note_sms_oper option:selected").val()   );
								ins_ajax_load_site_target("c=mail2sms_test&4ajax=1&operator="+$("#note_sms_oper option:selected").val()+"&sms_tel1="+$('#note_sms_tel1').val()+"&sms_tel2="+$('#note_sms_tel2').val(), '#test_sms_note_span');
							}
						</script>
						<tr>
							<td width="40%" align="right" valign="middle"><span class="editlinktip">UID для получения сообщений</span></td>
							<td><input type="text" name="note_icq" id="note_icq" class="inputbox" value="<?=$row->note_icq; ?>">&nbsp;<span id="test_icq_note_span"><a href="javascript: test_icq_note(); void(0);">Проверить</a></span></td>
						</tr>
						<tr>
							<td colspan="2" ></td>
						</tr>
						

						<tr>
							<td width="40%" align="right" valign="middle"><span class="editlinktip">СМС уведомления</span></td>
							<td valign="middle">
								<table cellpadding=0 cellspacing=0><tr><td valign="middle"><input type="checkbox" <? if (  $row->note_sms_enable  ) print 'checked="checked"'; ?> name="note_sms_enable" id="note_sms_enable" class="inputbox" value="1"></td><td valign="middle">&nbsp;<label for="note_sms_enable">разрешены</label></td></tr></table>
							</td>
						</tr>
						<tr>
							<td width="40%" align="right" valign="middle"><span class="editlinktip">Телефон для получения сообщений</span></td>
							<td><?
								?><table cellpadding="0" cellspacing="0" width="240px">
									<tr>
										<td width="40"><input class="inputbox" type="text" maxlength="3" value="<?=$row->note_sms_tel1 ?>"  name="note_sms_tel1" id="note_sms_tel1" style="width:100%; background:url(/theme/start/img/white1x1.png);" /></td>
										<td width="90"><input  class="inputbox" type="text" maxlength="8" value="<?=$row->note_sms_tel2 ?>" name="note_sms_tel2" id="note_sms_tel2" style="width:100%; background:url(/theme/start/img/white1x1.png);" /></td>
										<td width="110">&nbsp;<span id="test_sms_note_span"><a href="javascript: test_sms_note(); void(0);">Проверить</a></span></td>
									</tr>
									<tr>
										<td colspan="3" style="text-align:left;" align="left"><?
											$opers = array();
											$opers[] = mosHTML::makeOption( "etk", "ЕТК");
											$opers[] = mosHTML::makeOption( "megafon_sibir", "Megafon-Сибирь");
											$opers[] = mosHTML::makeOption( "beeline", "Beeline");
											$opers[] = mosHTML::makeOption( "mts", "MTC");
											print mosHTML::selectList( $opers, 'note_sms_oper', 'class="inputbox" style="width:120px; " size="1" id="note_sms_oper" ', 'value', 'text', $row->note_sms_oper );
										?></td>
									</tr>
									<tr>
										<td colspan="3">Для получения СМС-уведомлений необходимо подключить бесплатную услугу mail2sms. <a class="bright" href="http://man.krasinsite.ru/mail2sms?4ajax" target="_blank">Инструкция по подключению</a></td>
									</tr>

								</table><?
							?></td>
						</tr>

						</table>
					</td>
				</tr>
				</table>

                                

			</td>
		</tr>
                <tr>
                    <td colspan="2" >
                    <?
                            /*
                             * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
                             * например индивидуальные параметры для фотографий
                             */
                            load_adminclass('config');	$conf = new config($reg['db']);
                            $conf->prefix_id = '#__users'."_ID".( $row->id==''? 0 : $row->id )."__";
                            $conf->returnme('index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$row->id );
                            $conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
                    ?>
                    </td>
                </tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="ca" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="contact_id" value="" />
		<?php
		if (!$canEmailEvents) {
			?>
			<input type="hidden" name="sendEmail" value="0" />
			<?php
		}
		?>
		</form>
		<?php
	}
?>