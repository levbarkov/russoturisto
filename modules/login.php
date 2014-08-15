<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my; 
$task = ggrr('task');
switch ( $task ) {
	case 'register':	login_registerform( $id );
						break;
	case 'lost_pass':	login_lost_passform( $id );
						break;
	case 'viewtrush':
	default:			login_loginform( $id );
						break;
}
if (  !isset($_REQUEST['4ajax_in'])  ){	?></div><? }

function login_registerform(){
	$captcha = new captcha();    $captcha->img_id="insite_login_register_code"; 	$captcha->codeid_id="insite_login_register_codeid";		$captcha->init();
	if (  1 || !isset($_REQUEST['4ajax_in'])  ){	?><div id="wrapper_insite_login" class="wrapper_insite_ajax" style=" width:630px; height:370"><? }
	$validate = josSpoofValue();
	?><form action="" method="post" name="insite_register" onsubmit="ins_ajax_register_validate(this); return false;">
	<table width="550" border="0" cellspacing="0" cellpadding="0" align="center"  class="insite_ajax_form_table">
		<tr height="5"><th></th></tr>
		<tr height="20"><th style=" text-align:left" align="left">Регистрация</th></tr>
		<tr height="20"><td style="font-size:8px"><div id="insite_register_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 450px; height:20px;" >&nbsp;</div></td></tr>
	</table>
	<table width="550" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_register_main_span" class="insite_ajax_form_table">
		<tr height="8"><th width="35%" style=" text-align:left" align="left">&nbsp;</th><th width="65%"></th></tr>
		<tr>
			<td>Логин&nbsp;</td>
			<td><input type="text" name="username" id="insite_register_username" size="40" value="" class="input_ajax input_width2 input_light" maxlength="25" /></td>
		</tr>
		<tr>
			<td width="30%">Фамилия,&nbsp;Имя,&nbsp;Отчество&nbsp;&nbsp;</td>
			<td><?
				?><table cellpadding="0" cellspacing="0" width="90%"><tr>
					<td width="30%"><input type="text" name="usersurname" id="insite_register_usersurname" size="30" value="" class="input_ajax input_light" style="width:100%;" maxlength="50" /></td>
					<td width="40%"><input type="text" name="name" id="insite_register_name" size="30" value="" class="input_ajax input_light" style="width:100%;" maxlength="50" /></td>
					<td width="40%"><input type="text" name="userparentname" id="insite_register_userparentname" size="30" value="" class="input_ajax input_light" style="width:100%;" maxlength="50" /></td>
				</tr></table><?
			?></td>

		</tr>
		<tr>
			<td><?php echo _REGISTER_EMAIL; ?>&nbsp;</td>
			<td><input type="text" name="email" id="insite_register_email" size="40" value="" class="input_ajax input_width2 input_light" maxlength="100" /></td>
		</tr>
		<tr>
			<td><?php echo _REGISTER_PASS; ?>&nbsp;</td>
			<td><input type="password" name="password" id="insite_register_password" class="input_ajax input_width2 input_light" size="40" value="" /></td>
		</tr>
		<tr>
			<td>Подтверждение&nbsp;пароля:&nbsp;</td>
			<td><input type="password" name="password2" id="insite_register_password2" class="input_ajax input_width2 input_light" size="40" value="" /></td>
		</tr>
		<tr>
			<td>Код&nbsp;безопасности:&nbsp;</td>
			<td style="padding-left:2px;"><table cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" style="vertical-align:middle;"><? $captcha->codeid_input(); $captcha->show_captcha() ?></td>
				<td valign="middle" style="vertical-align:middle; font-size:22px; font-weight:normal; font-style:normal; font-family:Arial, Helvetica, sans-serif; ">&nbsp;&rarr;&nbsp;</td>
				<td valign="middle" style="vertical-align:middle; "><input type='text' name='gbcode'  id="insite_register_gbcode" maxlength='5' style='width:60px;vertical-align:middle;' class='insite_login_register' title='Введите показанный код' /></td>
				<td valign="middle" style="vertical-align:middle; ">&nbsp;&nbsp;&nbsp;<a href="javascript:spamfixreload('insite_login_register_code', '<?=$captcha->codeid ?>')" >не&nbsp;вижу</a></td>
			</tr></table></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2" style="text-align:right" align="right"><input type="submit" value="Регистрировать" class="button" /></td></tr>
	</table>
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="gid" value="0" />
	<input type="hidden" name="useractivation" value="<?php echo $useractivation;?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="saveRegistration" />
	<input type="hidden" name="<?php echo $validate; ?>" id="insite_register_validate" value="1" />
	<input type="hidden" name="4ajax_in" value="1" />
	</form><?php
}
function login_lost_passform(){
	if (  1 || !isset($_REQUEST['4ajax_in'])  ){	?><div id="wrapper_insite_login" class="wrapper_insite_ajax"><? }
	$validate = josSpoofValue();
	?><form action="" method="post" name="insite_lost_pass" onsubmit="ins_ajax_register_lost_pass(this); return false;" >
	<table width="450" border="0" cellspacing="0" cellpadding="0" align="center"  class="insite_ajax_form_table">
		<tr height="5"><th></th></tr>
		<tr height="20"><th style=" text-align:left" align="left">Восстановление пароля</th></tr>
		<tr><td colspan="2"><?php echo _NEW_PASS_DESC; ?></td></tr>
		<tr height="20"><td style="font-size:8px"><div id="insite_register_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 450px; height:20px;" >&nbsp;</div></td></tr>
	</table>

	<table width="450" border="0" cellspacing="0" cellpadding="0" align="center"  class="insite_ajax_form_table">
		<tr height="8"><th width="30%" style=" text-align:left" align="left"></th><th width="70%"></th></tr>
		<tr height="8"><td></td><td style="font-size:8px">&nbsp;</td><td></td></tr>

		<tr>
			<td>Логин:</td>
			<td style="text-align:right;"><input name="checkusername" id="insite_login_checkusername" type="text" class="input_ajax input_width2 input_light" /></td>
		</tr>
		<tr>
			<td>Адрес e-mail:</td>
			<td style="text-align:right;"><input type="text" id="insite_login_confirmEmail" name="confirmEmail" class="input_ajax input_width2 input_light" size="10" /></td>
		</tr>
		<tr>
			<td colspan="2" align="right" style="text-align:right; padding-right:12px;"><input type="submit" name="Submit" class="button" value="Изменить пароль" /></td>
		</tr>
		<tr height="8"><td style="font-size:8px">&nbsp;</td><td></td></tr>
		<tr>
			<td><a href="javascript: ins_ajax_open('/?4ajax_module=login&4ajax_in=1', 400, 280); void(0);" class="log1">Войти</a></td>
			<td style="text-align:right; padding-right:12px;"><a href="javascript: ins_ajax_open('/?4ajax_module=login&task=register&4ajax_in=1', 680, 400); void(0);" class="log1">Регистрация</a></td>
		</tr>
		<tr height="8"><td colspan="2" style="font-size:8px">&nbsp;</td></tr>
	</table>
	<input type="hidden" id="insite_login_validate" name="<?php echo $validate; ?>" value="1" />
	</form>
	<?php
}
function login_loginform(){
	if (  1  ||  !isset($_REQUEST['4ajax_in'])  ){	?><div id="wrapper_insite_login" class="wrapper_insite_ajax" style=" width:350px; height:200px; "><? }
	if ($query_string = mosGetParam( $_SERVER, 'QUERY_STRING', '' )) {	$return = 'index.php?' . $query_string;	}  else { $return = 'index.php'; }
	$return = str_replace( '&', '&amp;', $return );	// преобразование & в &amp; для совместимости с xtml
	$validate = josSpoofValue(1);
	?><form action="" method="post" name="insite_login" onsubmit="ins_ajax_login_validate(this); return false;" >
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  class="insite_ajax_form_table">
		<tr height="5"><th width="30%" style=" text-align:left" align="left"></th><th width="70%"></th></tr>
		<tr height="20"><th colspan="2" style=" text-align:left" align="left">Войти</th></tr>
		<tr height="20"><td colspan="2" style="font-size:8px"><div id="insite_login_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 250px;  height:20px;" >&nbsp;</div></td></tr>
		<tr height="8"><td></td><td style="font-size:8px">&nbsp;</td><td></td></tr>
		<tr>
			<td>Логин</td>
			<td><input name="username" id="insite_login_name" type="text" class="input_ajax input_width2 input_light" /></td>
		</tr>
		<tr>
			<td>Пароль</td>
			<td><input type="password" id="insite_login_pass" name="passwd" class="input_ajax input_width2 input_light" size="10" /></td>
		</tr>
		<tr>
			<td><input type="checkbox" name="remember" id="mod_login_remember" class="inputbox2" value="yes"  /> <?php echo _REMEMBER_ME; ?></td>
			<td align="right" style="text-align:right;"><input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGIN; ?>" /></td>
		</tr>
		<tr height="8">
			<td style="font-size:8px">&nbsp;</td>
			<td></td>
		</tr>
		<tr>
			<td><a href="javascript: ins_ajax_open('/?4ajax_module=login&task=lost_pass&4ajax_in=1', 550, 400); void(0);" class="log1">Забыли&nbsp;пароль?</a></td>
			<? /*  			<td style="text-align:right"><a href="javascript: ins_ajax_link('/?4ajax_module=login&task=register&4ajax_in=1', 'wrapper_insite_login', 70, 230, 500,500); void(0);" class="log1">Регистрация</a></td>  */ ?>
			<td style="text-align:right"><a href="javascript: ins_ajax_open('/?4ajax_module=login&task=register&4ajax_in=1', 680, 400); void(0);" class="log1">Регистрация</a></td>
		</tr>
		<tr height="8"><td colspan="2" style="font-size:8px">&nbsp;</td></tr>
	</table>
	<input type="hidden" name="c" value="in" />
	<input type="hidden" name="pi" value="11" />
	<input type="hidden" name="return" value="index.php?<?php echo $_SERVER['QUERY_STRING']; ?>" />
	<input type="hidden" name="message" value="<?php echo $message_login; ?>" />
	<input type="hidden" name="force_session" value="1" />
	<input type="hidden" name="4ajax_in" value="1" />
	<input name="4ajax" value="1" type="hidden" />
	<input type="hidden" id="insite_login_validate" name="<?php echo $validate; ?>" value="1" />
	</form>
	<?php
}
?>