<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

# КАБИНЕТ ПОЛЬЗОВАТЕЛЯ

class viewCabUser {
	private $_task;
	
	public function __construct(){
		$this->_task = Api::$request->getParam('task', 'str', '');
	}
	
	public function route(){
		$task = $this->_task;
		
		$method_name = 'action' . mb_ucfirst($task);
		if(method_exists($this, $method_name))
			$this->$method_name();
		else
			$this->actionEdit();
	}
	
	private function actionEdit(){
		global $database, $reg;
		
		if(!Api::$user->id){
			echo 'Для доступа в данный раздел необходимо авторизоваться';
			return;
		}
		
		$user_info = ggo(Api::$user->id, "#__users");
		
		$lists['block'] 	= mosHTML::yesnoRadioList('block', 'class="inputbox" size="1"', $user_info->block);
		$lists['sendEmail']	= mosHTML::yesnoRadioList('sendEmail', 'class="inputbox" size="1"', $user_info->sendEmail);

		$tabs = new iTabs(0);
		$canBlockUser 	= 1;
		$canEmailEvents = 1;
		
		# Avatar
		$component_foto = new component_foto(0);
		$component_foto->init('user_main');

		$user = array(
			'username'			=> desafelysqlstr($user_info->username),
			'name'				=> desafelysqlstr($user_info->name),
			'usersurname'		=> desafelysqlstr($user_info->usersurname),
			'userparentname'	=> desafelysqlstr($user_info->userparentname),
			'photo'				=> $component_foto->createPreviewFotoLink('small', 'org', $user_info, $reg['uinoimage'], ' class="highslide fancy" '),
		);
		
		include_once(__DIR__ . '/tpl/editUser.html');
	}
	
	private function actionSave(){
		global $database, $reg;
		  
		$userIdPosted = Api::$request->getParam('id', 'int', 0);
		$row = ggo($userIdPosted, "#__users");
		$row->name 				= trim($row->name);
		$row->usersurname 		= trim($row->usersurname);
		$row->userparentname 	= trim($row->userparentname);
		$row->email 			= trim($row->email);
		$row->username 			= trim($row->username);
		$row_password 			= Api::$request->getParam('password', 'str', '');
		$row->id 				= intval($row->id);
		$row->gid 				= intval($row->gid);
		$row->uinfo 			= Api::$request->getParam('uinfo', 'str', '');
		$isNew  				= !$row->id;
		$pwd 					= '';
	
		$original = ggo ($row->id, '#__users');
		if($row_password != '')
			$row->password = md5(trim($row_password));
			
		$gid = Api::$request->getParam('gid', 'int', 18);	
		$usertype = ggo($gid, '#__usertypes');
		
		$row->usertype = $usertype->help;
		
		if(!icheckUser($row)){
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if(!istoreUser($row, $isNew)){
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$msg = 'Успешно сохранен пользователь: ' . $row->name;
		mosRedirect('/' . $reg['cab_seoname'] . '/', $msg);		
	}
		
	public function actionNewfoto(){
		global $database, $my, $isgal, $fparent, $reg;
		if (Api::$user->id){
			if ($_FILES["newfoto"]['tmp_name']){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ				
				$component_foto = new component_foto(0);
				$component_foto->init('user_main');
				$component_foto->parent = Api::$user->id;
				$component_foto->publish = 'dont_save_publish';  // так как у объекта user - publish не актуален
				$component_foto->delmainfoto();
				$component_foto->external_foto('/cab/', 'Фото сохранено');
				
				return;
			}
		}
	}
}

$controller = new viewCabUser();
$controller->route();


return;



global $my, $task, $id;
$task 			= strval( mosGetParam( $_REQUEST, 'task', '' ) );
$cid = josGetArrayInts( 'cid' );
//ggtr ($_REQUEST); ggtr ($task); die();
switch ($task) {
	case 'newfoto':		newfotocab_club( $task );
						break;
	case 'newfoto_store': newfotocab_club_store( $task );
						  break;

	case 'save':		saveuser( $task );
						break;
	default:
	case 'edit':		edituser( $id, $option );
						break;
}
function newfotocab_club( $task ) {
	global $database, $my, $isgal, $fparent, $reg;
	if (  $my->id  ){
		if (  $_FILES["newfoto"]['tmp_name']  ){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ
			switch ( $task ) {
				case 'newfoto':
				default:		$ret_url = '/cab';
							$ret_msg = 'Фото сохранено'; break;
			}
			$component_foto = new component_foto( 0 );
                        //$component_foto->default_init();
			$component_foto->init( 'user_main' );
			$component_foto->parent = $my->id;
                        $component_foto->publish = 'dont_save_publish';  // так как у объекта user - publish не актуален
                        $component_foto->delmainfoto();
			$component_foto->external_foto($ret_url, $ret_msg); return;
		}
	}

	  

		
/*	// delete old logo
	$fmy = ggo ( $my->id, "#__users");
	delfile ( site_path."/images/cab/logo/".$fmy->logo );
	delfile ( site_path."/images/cab/logo/".$fmy->logosmall );

	$iexfototype = "jpg";
	$iexuni = md5(uniqid("exsalon"));
	$_FILES["newfoto"]['name'] = str_replace(" ", "_", $_FILES["newfoto"]['name']);
	$ilogoexname = $_FILES["newfoto"]['name']."_logo___".$iexuni.".".$iexfototype;
	$ismalllogoexname = $_FILES["newfoto"]['name']."_smalllogo___".$iexuni.".".$iexfototype;

	$i24makesmallfoto_func = $reg['uilogo_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
	$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path."/images/cab/logo/".$ilogoexname,
							$reg['uilogowidth'],	$reg['uilogoheight'],	$reg['uilogo_tag']);

	$i24makesmallfoto_func = $reg['uilogosmall_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
	$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path."/images/cab/logo/".$ismalllogoexname,
							$reg['uilogosmallwidth'],	$reg['uilogosmallheight'],	$reg['uilogosmall_tag']);

		
	$i24r = new mosDBTable( "#__users", "id", $database );
	$i24r->id = $my->id;
    $i24r->logo = $ilogoexname;
	$i24r->logosmall = $ismalllogoexname;
	
	if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else $i24r->store();
	//ggr ($database, 20); die();
	$msg = 'Новое фото сохранено';
	mosRedirect( '/'.$reg['cab_seoname'], $msg );
	*/
}
function saveuser(){
	global $database, $my, $reg;
	  
	$userIdPosted = mosGetParam($_POST, 'id');
	$row = ggo ($userIdPosted, "#__users");
	$row->name = trim( $row->name );
	$row->usersurname = trim( $row->usersurname );
	$row->userparentname = trim( $row->userparentname );
	$row->email = trim( $row->email );
	$row->username = trim( $row->username );
	$row_password = getUserStateFromRequest(  'password', '' );
	$row->id 	= (int) $row->id;
	$row->gid 	= (int) $row->gid;
	$row->uinfo 	= ggrr('uinfo');
	$isNew 	= !$row->id;
	$pwd 	= '';

	$original = ggo (  (int)$row->id, "#__users"  );
	if ($row_password == '') {
		;// ПАРОЛЬ ОСТАВЛЯЕМ СТАРЫЙ
	} else { $row->password = md5( trim( $row_password ) ); }
		// save usertype to usertype column
	$usertype = ggo (  intval(getUserStateFromRequest(  'gid', 18 )), "#__usertypes"  );
	$row->usertype = $usertype->help;
	
//	ggd ($row, 50);
	if (!icheckUser($row)) 			{ echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; exit(); }
//	ggd ($row);
	if (!istoreUser($row, $isNew)) 	{ echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";  exit(); }
//	ggd ($database, 50);
	$msg = 'Успешно сохранен пользователь: '. $row->name; mosRedirect( '/'.$reg['cab_seoname'], $msg ); break;
}
function edituser(){
	global $database, $my, $acl, $mainframe, $reg;

	if (  !$my->id  ){
		print 'Для доступа в данный раздел необходимо авторизоваться';
		return;
	}

	$uid = $my->id;
	$row = ggo ($my->id, "#__users");

	// build the html select list
	if (  $uid==0  ) $row->block = 0;
	$lists['block'] 		= mosHTML::yesnoRadioList( 'block', 'class="inputbox" size="1"', $row->block );
	// build the html select list
	$lists['sendEmail'] 	= mosHTML::yesnoRadioList( 'sendEmail', 'class="inputbox" size="1"', $row->sendEmail );
//	iedituser( $row, $contact, $lists, $option, $uid, $params );
		$tabs = new iTabs( 0 );
//		mosCommonHTML::loadOverlib();
		$canBlockUser 	= 1;
		$canEmailEvents = 1;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton() {
			var form = document.adminFormUser;
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
			// do field validation
			if (trim(form.name.value) == "") {
				alert( "Вы должны ввести имя." );
			} else if (form.username.value == "") {
				alert( "Вы должны ввести логин для входа на сайт." );
			} else if (r.exec(form.username.value) || form.username.value.length < 3) {
				alert( "Ваше имя для входа содержит неправильные символы или слишкрм короткое." );
			} else if (trim(form.email.value) == "") {
				alert( "Вы должны ввести адрес email." );
			} else if (trim(form.password.value) != "" && form.password.value != form.password2.value){
				alert( "Пароль неправильный." );
			} else {
				//submitform(  );
				document.adminFormUser.submit();
			}
		}
		function gotocontact( id ) {
			var form = document.adminForm;
			form.contact_id.value = id;
			submitform( 'contact' );
		}
		</script>
		
		<table class="adminheading" width="50%">
		<tr height="10">
			</td></td>
		</tr>

		<tr>
			<th style="padding-left:26px;" id="td14bold" nowrap="nowrap" class="user">Личные данные</th>
			<th nowrap="nowrap" align="right"><a href="javascript: submitbutton(); ">Сохранить</a>&nbsp;&nbsp;&nbsp;<?
				?><a href="<?=$reg['cab_seoname']; ?>">Отменить</a>&nbsp;&nbsp;&nbsp;<?
				?><a href="javascript: ins_ajax_logout(); void(0);">Выход</a>
			</th>
		</tr>
		</table>
		<hr style="margin-left:30px;" />
		<table width="100%" border="0"  style="padding-left:20px;">
		<tr>
			<td width="70%" valign="top">
				<table class="adminform" border="0" cellpadding="2">
				<tr>
					<td width="15%" style="padding-left:20px; vertical-align:top; padding-top:20px;" valign="top" nowrap="nowrap">Фото:</td>
					<td width="85%"><?
						?><table width="100%" border="0" >
							<tr>
								<td valign="top"><?
									$fmy = ggo ( $my->id, "#__users");
									$component_foto = new component_foto( 0 );
									$component_foto->init( 'user_main' );
									print $component_foto->createPreviewFotoLink ( 'small', 'org', $fmy, $reg['uinoimage'], ' class="highslide fancy" '  );
								?></td>
								<td>&nbsp;&nbsp;&nbsp;</td>
								<td width="100%"><?
								// form to add LOGO
								
									?><form action="" method="post"  id="fotofo" name="newfotoForm" enctype="multipart/form-data">	
									<table class="adminheading" width="100%">
									<tr>
										<td nowrap="nowrap" align="left" valign="top" style="text-align:left; vertical-align:top;">Загрузить</td>
										<td>&nbsp;</td>
										<td class="small" align="left" valign="top" style="vertical-align:top; ">(Название файла должно содержать только латинские символы или цифры. Допускаются форматы изображения jpg / gif )</td>
									</tr>
									</table>
									<table class="adminheading" border="0">
									<tr>
										<td><input type="file" name="newfoto" size="35" /></td>
										<td><input type="button" value="Закачать" onclick="document.getElementById('fotofo').submit();" /></td>
									</tr>
									</table>
									<input type="hidden" name="task" value="newfoto" />
								</form>
								<form action="" method="post"  id="adminFormUser" name="adminFormUser">
								</td>
							</tr>
						</table><?
					?></td>
				</tr>
				<form action="index.php" method="post" name="adminFormUser">
				<tr>
					<td style="padding-left:20px;">Логин:</td>
					<td><input type="text" name="username" class="inputbox" size="40" value="<?php echo $row->username; ?>" maxlength="25" /></td>
				</tr>
				<tr>
					<td style="padding-left:20px;">ФИО:</td>
					<td>
						<table cellpadding="0" cellspacing="0" width="300" ><tr>
						<td width="30%"><input type="text" name="usersurname" class="inputbox" style="width:170px; " size="40" value="<?php echo $row->usersurname; ?>" maxlength="50" /></td>
						<td width="40%"><input type="text" name="name" class="inputbox" size="40" style="width:90px;" value="<?php echo $row->name; ?>" maxlength="50" /></td>
						<td width="40%"><input type="text" name="userparentname" class="inputbox" style="width:140px;" size="40" value="<?php echo $row->userparentname; ?>" maxlength="50" /></td>
						</tr></table>
					</td>
				</tr>
				<tr>
					<td style="padding-left:20px;">E-mail:</td>
					<td><input class="inputbox" type="text" name="email" size="40" value="<?php echo $row->email; ?>" /></td>
				</tr>
				<tr>
					<td style="padding-left:20px;">Новый пароль: 
						<div class="iii"><i>если хотите изменить</i></div></td>
					<td><input class="inputbox" type="password" name="password" size="40" value="" /></td>
				</tr>
				<tr>
					<td style="padding-left:20px;">Проверка пароля:</td>
					<td><input class="inputbox" type="password" name="password2" size="40" value="" /></td>
				</tr>
				<?
				if ($canEmailEvents) {
					?>
					<tr>
						<td style="padding-left:20px;">Получать системные сообщения на e-mail</td>
						<td><?php echo $lists['sendEmail']; ?></td>
					</tr>
					<?php
				}
				if( $my->id ) {
					?>
					<tr>
						<td style="padding-left:20px;">Дата регистрации</td>
						<td><?php echo $row->registerDate;?></td>
					</tr>
				<tr>
					<td style="padding-left:20px;">Дата последнего посещения</td>
					<td><?php echo $row->lastvisitDate;?></td>
				</tr>
				<tr>
					<td style="padding-left:20px;">Информация пользователя</td>
					<td><?php editorArea( 'editor1',  $row->uinfo , 'uinfo', '100%;', '200', '75', '20' ) ; ?></td>
				</tr>

					<?php
				}
				?>
				</table>
			</td>
			<td width="30%" valign="top"></td>
		</table>
		<input type="hidden" name="pi" value="200" />
		<input type="hidden" name="block" value="<?=$my->block; ?>" />
		<input type="hidden" name="id" value="<?php echo $my->id; ?>" />
		<input type="hidden" name="gid" value="<?php echo $my->gid; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php
		if (!$canEmailEvents) {
			?>
			<input type="hidden" name="sendEmail" value="0" />
			<?php
		}
		?></form>
		<hr style="margin-left:30px;" />
		<table class="adminheading" width="50%">
		<tr>
			<th width="50%" nowrap="nowrap" align="left" style="text-align:left; padding-left:50;">Мои заказы</th>
			<th width="50%" nowrap="nowrap" align="left"><a href="/cab_orders">Смотреть</a></th>
		</tr>
		</table>
		<hr style="margin-left:30px;" />		
		<table class="adminheading" width="50%">
		<tr>
			<th width="50%" nowrap="nowrap" align="left" style="text-align:left; padding-left:50;">Новости</th>
			<th width="50%" nowrap="nowrap" align="left"><a href="index.php?c=cab_news">Редактировать</a></th>
		</tr>
		</table>
		<hr style="margin-left:30px;" />
		<table class="adminheading" width="50%">
		<tr>
			<th width="50%" nowrap="nowrap" align="left" style="text-align:left; padding-left:50;">Фотогалерея</th>
			<th width="50%" nowrap="nowrap" align="left"><a href="index.php?c=cab_news&gal=1">Редактировать</a></th>
		</tr>
		</table>
		<hr style="margin-left:30px;" />
		<? 
} 
?>