<?php
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
echo '<?xml version="1.0" encoding="UTF-8"?' .'>';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?
?><title>Панель управления сайтом!</title><?
?><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><?
?><style type="text/css"> @import url(theme/admin/css/theme.css); </style><?
?><style type="text/css"> @import url(theme/admin/css/admin_login.css); </style><?
?><script type="text/javascript" src="/includes/tabledrag/jquery.js"></script><?
?><script language="javascript" type="text/javascript"> function setFocus() { document.loginForm.usrname.select(); document.loginForm.usrname.focus(); } </script><?
?></head><?
?><body onload="setFocus();" >
<table width="100%" style="width:100%; " cellpadding="0" cellspacing="0" border="0" align="center" ><? /* САМАЯ ГЛАВНАЯ ТАБЛИЦА */ ?>
	<tr>
		<td width="7%" style=" width:7%; ">&nbsp;</td>
		<td width="86%" style=" width:86%; "><?
			?><table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0" align="center"><? // кнопки  типа сохранить отменить редактировать
			?><tr><?
				?><td  class="menudottedline" align="left" width="100%"><? 
					?><table cellspacing="0" cellpadding="0" border="0" id="toolbar"><tr valign="middle"><td nowrap="nowrap"><div class="itoolbar">Система управления сайтом&nbsp;<strong>CMS Insite</strong></div></td></tr></table><?
				?></td><?
				?><td width="100%" class="menudottedline" align="right"><?
					?><table cellspacing="0" cellpadding="0" border="0" id="toolbar"><tr align="center" valign="middle"><td nowrap="nowrap" style="white-space:normal; "><a href="/" class="toolbar">Смотреть&nbsp;сайт</a></td></tr></table><?
				?></td><?
			?></tr><?
			?></table><?
		?></td>
		<td width="7%" style=" width:7%; ">&nbsp;</td>
	</tr>
	<tr><td></td><td><br /><br /><br /><br /><br /></td><td></td></tr>
	<tr>
		<td></td><td align="center" style="text-align:center; font-size:16px;"><?
			$mosmsg = isset($_REQUEST['mosmsg']) ? $_REQUEST['mosmsg'] : "";
			if (  $mosmsg  ) {	if ( strlen( $mosmsg ) > 200 ) { $mosmsg = substr( $mosmsg, 0, 200 );	}	 
				?><?php echo $mosmsg; ?><?php
			}

		?></td><td></td>
	</tr>
	<tr><td></td><td><br /><br /></td><td></td></tr>
	<tr>
		<td colspan="3" align="center" class="tr_login"><?
		?><table cellpadding="0" cellspacing="0" border="0" align="center"><tr><td><?
			?><form action="index.php" method="post" name="loginForm" id="loginForm"><?
			?><div class="ilogcofrm"><?
				?><div class="inputlabel">Имя пользователя</div><?
				?><div><input name="usrname" type="text" class="inputbox" size="15" value="" /></div><?
				?><div class="inputlabel">Пароль</div><?
				?><div><input name="pass" type="password" class="inputbox" size="15" value="" /><?
				?><input type="submit" name="submit" class="subbutton" value=""   /></div><?
				?><div class="inputlabel">Для входа &mdash; Enter</div><?
				?><input type="hidden" name="query_url" value="<?=ggrr('query_url') ?>" /><?
			?></div><?
			?></form><?
		?></td></tr></table><?

		?></td>
	</tr>

	
</table>
<noscript>Javascript должны быть разрешены для правильной работы панели управления администратора</noscript><?
?></body><?
?></html><?