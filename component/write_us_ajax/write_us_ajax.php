<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

/*
 * ПРОВЕРКА ВВЕДЕННЫХ ДАННЫХ
 */
if (  ggrr('task')=='sendmail'  ){
	if (   ggrr('uname')==''  or  ggrr('uname')=='Ваше имя'   ) 			 { ?>$("#insite_write_us_server_answer").html('— Заполните поле «Имя»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggrr('umail')==''  or  ggrr('umail')=='E-mail'  ) 			 { ?>$("#insite_write_us_server_answer").html('— Заполните поле «E-mail»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggrr('utel')==''   or  ggrr('utel')=='Телефон'  ) 			 { ?>$("#insite_write_us_server_answer").html('— Заполните поле «Телефон»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggrr('backlinkMailText')==''  or ggrr('backlinkMailText')=='Сообщение:'  ){ ?>$("#insite_write_us_server_answer").html('— Заполните поле «сообщение»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggrr('gbcode')==''  )                                                     { ?>$("#insite_write_us_server_answer").html('— Заполните поле «Код безопасности»').jTypeWriter({duration:1.5}); <? return; }

        // проверка капчи
	$captcha = new captcha();	
	if(  !$captcha->check_me()  )                                                    { ?>$("#insite_write_us_server_answer").html('— Введен неверный код безопасности').jTypeWriter({duration:1.5}); <? return; }

        // проверка закончена - отправляем письмо и выводим благодарность
	?> $('#insite_write_us_main_table').fadeOut(300, function(){
                                                                    $("#insite_write_us_server_answer").hide();
                                                                    $("#insite_write_us_server_answer").html('<br /><strong>Спасибо.</strong><br />Ваша информация успешно отправлена.');
                                                                    $("#insite_write_us_server_answer").fadeIn(700);
                                                                   }); <?
	sendmessage();
	return;
}
/**
 * Отпрапвляем письмо пользователю
 */
function sendmessage(){
	
	$backlinkgfg = ggo (1, "#__backlinkcfg");
	$etmp = file_get_contents(site_path."/component/write_us_ajax/email_template.html");
	$etmp = str_replace("{exVendorImage}", '', $etmp);
	$etmp = str_replace("{backlinkOrderHeader}",  desafelySqlStr($backlinkgfg->order_mail_subject), $etmp);
	$etmp = str_replace("{backlinkMailText}", desafelySqlStr($_REQUEST['backlinkMailText']), $etmp);	
	$etmp = str_replace("{backlinkName}", desafelySqlStr($_REQUEST['uname']), $etmp);	
	$etmp = str_replace("{backlinkEmail}", desafelySqlStr($_REQUEST['umail']), $etmp);	
	$etmp = str_replace("{backlinkTel}", desafelySqlStr($_REQUEST['utel']), $etmp);
	$etmpc = $etmp;
	$etmpc = str_replace("{backlinkCopyNote}",  desafelySqlStr($backlinkgfg->copy_note), $etmpc);	$etmp = str_replace("{backlinkCopyNote}", '', $etmp);
		
	$mymail = new mymail();
	$mymail->add_address ( $backlinkgfg->order_mail_to );
	if (  isset($_REQUEST['docopy'])  )  $mymail->add_address ( $_REQUEST['umail'] );
	$mymail->set_subject ( $backlinkgfg->order_mail_subject );
	$mymail->set_body	 ( $etmp );
	if (  $_FILES['afile']['tmp_name']  )	$mymail->attach_file(  $_FILES['afile']['tmp_name'], $_FILES['afile']['name']  );      // attachment
	$mymail->send ();

	return;
}

	do_write_us_stat();
	$captcha = new captcha();    $captcha->img_id="insite_write_us_code"; 	$captcha->codeid_id="insite_write_us_codeid";		$captcha->init();
	$myform = new insiteform();
	// $myform->formname = "feedback";
	// $myform->serveranswer = "insite_feedback_server_answer";
	// $myform->java_make_code();
	// $myform->debug_div();
	?><div id="wrapper_insite_write_us" class="wrapper_insite_ajax" style=" width:350px; height:380"><form action="/index.php" method="post" enctype="multipart/form-data" name="write_us" id="write_us" >
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_write_us_title_table" class="insite_ajax_form_table">
		<tr height="5"><th></th></tr>
		<tr height="20"><th style=" text-align:left" align="left">Написать Нам</th></tr>
		<tr height="20"><td style="font-size:8px"><div id="insite_write_us_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 300px; height:20px;" >&nbsp;</div></td></tr>
		<tr height="8"><td></td><td style="font-size:8px">&nbsp;</td></tr>
	</table>
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_write_us_main_table" class="insite_ajax_form_table">
		<tr>
			<td><input <? $myform->make_java_text_effect('uname', 'input_light'); ?> size="30" class="input_ajax input_width input_gray" name="uname" id="uname" value="Ваше имя" title="Ваше имя" /></td>
		</tr>
		<tr>
			<td><input <? $myform->make_java_text_effect('umail', 'input_light'); ?> size="30" class="input_ajax input_width input_gray" name="umail" id="umail" value="E-mail" title="E-mail" /></td>
		</tr>
		<tr>
			<td><input  <? $myform->make_java_text_effect('utel', 'input_light'); ?> size="30" class="input_ajax input_width input_gray"  name="utel" id="utel" value="Телефон" title="Телефон" /></td>
		</tr>
		<tr>
			<td><input type="checkbox" name="docopy" id="docopy" /><label for="docopy">&nbsp;Отправить мне копию письма</label></td>
		</tr>
		<tr>
			<td><textarea <? $myform->make_java_text_effect('backlinkMailText', 'input_light'); ?>  class="textarea_ajax input_width input_gray"  cols="35" rows="12" name="backlinkMailText" id="backlinkMailText" title="Сообщение:" >Сообщение:</textarea></td>
		</tr>
		<tr>
			<td>Прикрепить файл:<br /><input type="file" name="afile" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="padding-left:2px;">Код&nbsp;безопасности:&nbsp;*&nbsp;<br /><table cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" style="vertical-align:middle;"><? $captcha->codeid_input(); $captcha->show_captcha() ?></td>
				<td valign="middle" style="vertical-align:middle; font-size:22px; font-weight:normal; font-style:normal; font-family:Arial, Helvetica, sans-serif; ">&nbsp;&rarr;&nbsp;</td>
				<td valign="middle" style="vertical-align:middle; "><input type='text' name='gbcode'  maxlength='5' class='input_ajax input_ajax_gbcode' title='Введите показанный код' /></td>
				<td valign="middle" style="vertical-align:middle; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:spamfixreload('insite_write_us_code', '<?=$captcha->codeid ?>')" >не&nbsp;вижу&nbsp;код</a></td>
			</tr></table></td>
		</tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td style="text-align:center; " align="center"><input type="submit" value="Отправить" class="button" /></td></tr>
	</table>
	<input type="hidden" name="c" value="<?php echo $reg['c']; ?>" />
	<input type='hidden' name='task' value='sendmail' />
	<input type="hidden" name="4ajax" value="1" />
	</form></div><?
	?><script language="javascript">
		var options = {		dataType:		'script',
							beforeSubmit:  function(){	over_fade('#wrapper_insite_write_us', '#wrapper_insite_write_us', '', 0.5, 'popup'); },
							success: function(){ over_fade_hide(); }
					  };
		$('#write_us').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script><?
	
/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_write_us_stat(){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	if (  $sitelog->isnewlog()  ) $sitelog->desc = $reg['write_us_name'];
	$sitelog->savelog();
}