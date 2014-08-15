<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

/*
 * ОТПРАВКА ПИСЬМА
 */
if (  isset($_REQUEST['4ajax'])  ){
	$backlinkgfg = ggo (1, "#__backlinkcfg");
	$myform = new insiteform();
	$myform->formname = "adminForm";
        // css чтобы верныть поля к первонасальному виду
	$myform->reset_jquery = "animate({ backgroundColor: '#ffffff' }, 100 )";
        // css чтобы подсветить неправильно заполненные поля, оставьте пустым, если эффект подсветки не используется
	$myform->light_jquery = "animate({ backgroundColor: '#ffe4e4' }, 300 ";
        //$myform->light_jquery = "";  // оставьте пустым, если эффект подсветки не используется
	
	$myform->reset_form();	// снятие выделения со всех полей формы
	$myform->check_for_empty_required_fields(); // проверка обязательных полей, если пустое - то добавляется ID поля в массив в $myform->errorids
	$myform->check_user_mail('umail');          // проверка, что введен корректный email адрес, если нет - то добавляется ID поля в массив в $myform->errorids
	$myform->check_captcha();                   // проверка, что введена правильная капча, если нет - то добавляется ID поля в массив в $myform->errorids
	
	$myform->tell_user_result(  desafelySqlStr($backlinkgfg->thanku)  );
        // если не все поля заполнены верно, то ID полей, заполненных неверно лежат в $myform->errorids

        /*
         * ПИШЕМ ПИСЬМО
         */
	if (  $myform->if_filled_correct()  ){	// все поля заполнены верно, выполняем основной код
		?>$('#<?=$myform->formname ?>').resetForm();<?
		$etmp = file_get_contents(site_path."/component/backlink/email_template.html"); $etmpc = $etmp;
		$etmp = str_replace("{exVendorImage}", '', $etmp);
		$etmp = str_replace("{backlinkOrderHeader}",  ($backlinkgfg->order_mail_subject), $etmp);
		$etmp = str_replace("{backlinkMailText}", desafelySqlStr($_REQUEST['backlinkMailText']), $etmp);
		$etmp = str_replace("{backlinkName}", desafelySqlStr($_REQUEST['uname']), $etmp);
		$etmp = str_replace("{backlinkEmail}", desafelySqlStr($_REQUEST['umail']), $etmp);
		$etmp = str_replace("{backlinkTel}", desafelySqlStr($_REQUEST['utel']), $etmp);
		$etmpc = $etmp;	
		$etmpc = str_replace("{backlinkCopyNote}", "", $etmpc);	$etmp = str_replace("{backlinkCopyNote}", '', $etmp);
		
		$headers="From: \"".$backlinkgfg->order_mail_from_name."\" <".$backlinkgfg->order_mail_from.">\n";
		$headers.="Content-Type: text/html; charset=\"utf-8\"";
		//ggtr ($etmp);
		$mymail = new mymail();
		$mymail->add_address ( $backlinkgfg->order_mail_to );
		$mymail->set_subject ( $backlinkgfg->order_mail_subject );
		$mymail->set_body	 ( $etmp );
		$mymail->send ();
		//mail($backlinkgfg->order_mail_to,	  ($backlinkgfg->order_mail_subject),   $etmp,   $headers);
		if (  isset($_REQUEST['docopy'])  ){
			$mymail->clear_addresses();
			$mymail->add_address ( $_REQUEST['umail'] );
			$mymail->set_subject ( $backlinkgfg->copy_order_mail_subject );
			$mymail->set_body	 ( $etmpc );
			$mymail->send ();
			//mail($_REQUEST['u_mail'],	  ($backlinkgfg->copy_order_mail_subject),   $etmpc,   $headers);
		}
		return;
	}
	return;
}

// с новой версией jquery - получается уже лишняя библиотека jquery.color.js
// js("/includes/js/jquery.color.js");   // !!!НЕОБХОДИМ ЧТОБЫ РАБОТАЛО ЗАМЕНЕНИЕ ЦВЕТА ТЕГА INPUT

// сохраняем статистику посещений раздела
do_bigform_stat();

$captcha = new captcha(); 	$captcha->init();
$myform = new insiteform();
$myform->formname = "adminForm";
$myform->serveranswer = "serveranswer";
$myform->java_make_code();
$myform->debug_div();
?><form action="<?=$sefname1 ?>" name="adminForm" id="adminForm" method="post">
<table border="0" >
	<tr>
		<td width="57%"><span style="color:#a90c00; font-size:14px; font-weight:bold;">Задать вопрос</span><br /><br />
			<div style="width:400px"><div id="serveranswer" style=" width:370px; ">&nbsp;</div></div>
                        <!--
                            ireq="1" = значит поле обязательно для заполнения, обязательно поле должно иметь ID равный name
                            ireqname="Текст сообшения о шибке"
                        -->
			<table cellpadding="2">
				<tr>
					<td>Ваше имя:</td>
					<td><input  ireq="1" ireqname="Не заполненно поле Имя" size="30" class="i24inputbox"  name="uname" id="uname" value="<?=urldecode($_REQUEST['uname']) ?>" /></td>
				</tr>
				<tr>
					<td>E-mail:</td>
					<td><input  ireq="1" ireqname="Не заполненно поле E-mail" size="30" class="i24inputbox"  name="umail" id="umail" value="<?=urldecode($_REQUEST['umail']) ?>" /></td>
				</tr>
				<tr>
					<td>Телефон:</td>
					<td><input  ireq="1" ireqname="Не заполненно поле Телефон" size="30" class="i24inputbox"  name="utel" id="utel" value="<?=urldecode($_REQUEST['utel']) ?>" /></td>
				</tr>
				<tr>
					<td>Поле 1:</td>
					<td><input  ireq="1" ireqname="Не заполненно поле Поле 1" size="30" class="i24inputbox"  name="pole1" id="pole1" value="<?=urldecode($_REQUEST['pole1']) ?>" /></td>
				</tr>
				<tr>
					<td>Поле 2:</td>
					<td><input  ireq="1" ireqname="Не заполненно поле Поле 2" size="30" class="i24inputbox"  name="pole2" id="pole2" value="<?=urldecode($_REQUEST['pole2']) ?>" /></td>
				</tr>
				<tr>
					<td>Поле 3:</td>
					<td><input  ireq="1" ireqname="Не заполненно поле Поле 3" size="30" class="i24inputbox"  name="pole3" id="pole3" value="<?=urldecode($_REQUEST['pole3']) ?>" /></td>
				</tr>
				<tr>
					<td>Поле 4:</td>
					<td><input  ireq="1" ireqname="Не заполненно поле Поле 4" size="30" class="i24inputbox"  name="pole4" id="pole4" value="<?=urldecode($_REQUEST['pole4']) ?>" /></td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="docopy" id="docopy" /><label for="docopy">&nbsp;Отправить мне копию письма</label></td>
				</tr>
				<tr>
					<td>Сообщение:</td>
					<td><textarea  ireq="1" ireqname="Не введен текст сообщение" class="i24inputbox"  cols="35" rows="12" name="backlinkMailText" id="backlinkMailText" ><?=urldecode($_REQUEST['backlinkMailText']) ?></textarea></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;<input name="act" value="domail" type="hidden" /><?
					?><input name="4ajax" value="1" type="hidden" /></td>
				</tr>
				<tr>
					<td colspan='2'><?
						?><table>
							<tr>
								<td width='120'><? $captcha->codeid_input(); ?>Код:</td>
								<td><input ireq="1"  ireqname="Не введен код безопасности" type='text' name='gbcode' id='gbcode' maxlength='5' style='width:60px;vertical-align:middle;' class='inputbox' title='Введите показанный код' /></td>
								<td rowspan='2'>&#160;&#160;<? $captcha->show_captcha() ?></td>
							</tr>
							<tr>
								<td>Новый код безопасности:</td>
								<td><? $captcha->newimg_button(); ?></td>
							</tr>
						</table><?
					?></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Отправить" class="button"  <? /* onclick='i24validate_form(this);' */ ?> /></td>
				</tr>
			</table>
		</td>
                <td width="3%">&nbsp;</td>
                <td width="40%" valign="top" style="vertical-align:top"><?
		$exgfg = ggo (1, "#__backlinkcfg");
		print $exgfg->intro;
		?></td>

	</tr>
</table>
</form>
<? $myform->java_make_stars();  

/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_bigform_stat(){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	if (  $sitelog->isnewlog()  ) $sitelog->desc = "Большая форма";
	$sitelog->savelog();
}