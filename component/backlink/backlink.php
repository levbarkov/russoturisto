<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

/*
 * ПРОВЕРКА ВВЕДЕННЫХ ДАННЫХ И ОТПРАВКА ПИСЬМА
 */
if (  isset($_REQUEST['4ajax'])  ){
        // загрузка настроек компонента - ОБРАТНАЯ СВЯЗЬ
	$backlinkgfg = ggo (1, "#__backlinkcfg");

        // ID ответа сервера об ошибках
	$server_answer_id = 'serveranswer';

	if (   ggpr('uname')==''     or  ggrr('uname')=='Ваше имя'   )                     { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «Имя»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('umail')==''     or  ggrr('umail')=='E-mail'     )                     { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «E-mail»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('utel')==''      or  ggrr('utel')=='Телефон'     )                     { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «Телефон»').jTypeWriter({duration:1.5}); <? return; }
        if (   ggpr('backlinkMailText')==''  or  ggrr('backlinkMailText')=='Сообщение'  )  { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «Сообщение»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('gbcode')==''  )                                                                       { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «Код безопасности»').jTypeWriter({duration:1.5}); <? return; }

        // проверка капчи
	$captcha = new captcha();
	if(  !$captcha->check_me()  )                                                                      { ?>$("#<?=$server_answer_id ?>").html('— Введен неверный код безопасности').jTypeWriter({duration:1.5}); <? return; }

        /*
         * ПРОВЕРКА ЗАКОНЧЕНА - отправляем письмо и выводим благодарность
         */
        $etmp = file_get_contents(site_path."/component/backlink/email_template.html"); $etmpc = $etmp;
        $etmp = str_replace("{exVendorImage}", '', $etmp);
        $etmp = str_replace("{backlinkOrderHeader}",  ($backlinkgfg->order_mail_subject), $etmp);
        $etmp = str_replace("{backlinkMailText}", desafelySqlStr($_REQUEST['backlinkMailText']), $etmp);
        $etmp = str_replace("{backlinkName}", desafelySqlStr($_REQUEST['uname']), $etmp);
        $etmp = str_replace("{backlinkEmail}", desafelySqlStr($_REQUEST['umail']), $etmp);
        $etmp = str_replace("{backlinkTel}", desafelySqlStr($_REQUEST['utel']), $etmp);
        $etmpc = $etmp;
        $etmpc = str_replace("{backlinkCopyNote}", "", $etmpc);	$etmp = str_replace("{backlinkCopyNote}", '', $etmp);

        $mymail = new mymail();
        $mymail->add_address ( $backlinkgfg->order_mail_to );
        $mymail->set_subject ( $backlinkgfg->order_mail_subject );
        $mymail->set_body	 ( $etmp );
        $mymail->send ();

        if (  isset($_REQUEST['docopy'])  ){
                $mymail->clear_addresses();
                $mymail->add_address ( $_REQUEST['umail'] );
                $mymail->set_subject ( $backlinkgfg->copy_order_mail_subject );
                $mymail->set_body	 ( $etmpc );
                $mymail->send ();
        }

        /*
         * ВЫВОДИМ БЛАГОДАРНОСТЬ ЗА ПИСЬМО
         */
	?>$('#backlink_table').fadeOut(300, function(){
                                                        $("#<?=$server_answer_id ?>").hide();
                                                        $("#<?=$server_answer_id ?>").html('<?=nl2br($backlinkgfg->thanku) ?>');
                                                        $("#<?=$server_answer_id ?>").fadeIn(700);
                                                      }); <?
	return;
}
// сохраняем статистику посещений этого раздела в БД сайта
do_backlink_stat();

// загрузка настроек компонента - ОБРАТНАЯ СВЯЗЬ
$backlinkgfg = ggo (1, "#__backlinkcfg");

$captcha = new captcha(); 	$captcha->init();
$myform = new insiteform();

?><script language="javascript">
    var options_backlink_vars = {   dataType:		'script',
                                    beforeSubmit:  	function(){ over_fade('#backlink_table', '#backlink_table', '', 0.5, 'nopopup'); },
                                    success: 		function(){ over_fade_hide(); }
                              };
</script>
<form <? ctrlEnter( "  $('#backlinkForm').ajaxSubmit(options_backlink_vars); return false; " ) ?> action="<?=$sefname1 ?>" name="backlinkForm" id="backlinkForm" method="post"  onsubmit=" $(this).ajaxSubmit(options_backlink_vars); 	return false; " >
<table border="0" >
	<tr>
		<td width="60%" valign="top" style="vertical-align:top"><?
		$exgfg = ggo (1, "#__backlinkcfg");
		print $exgfg->intro;
		?></td>
		<td width="3%">&nbsp;</td>
                <td width="37%" valign="top" style="vertical-align: top; "><span style="color:#a90c00; font-size:14px; font-weight:bold;">Задать вопрос</span><br />
			<div style="width:400px;"><div id="serveranswer" style=" width:370px; height: 20px; padding-top: 10px; ">&nbsp;</div></div>
			<div id="backlink_table">
			<table cellpadding="2">
				<tr>
                                    <td><input  size="30" class="input_ajax input_gray" <? $myform->make_java_text_effect('uname', 'input_light'); ?> name="uname" id="uname" value="Ваше имя" title="Ваше имя" /></td>
				</tr>
				<tr>
                                    <td><input  size="30" class="input_ajax input_gray" <? $myform->make_java_text_effect('umail', 'input_light'); ?>  name="umail" id="umail" value="E-mail" title="E-mail" /></td>
				</tr>
				<tr>
                                    <td><input  size="30" class="input_ajax input_gray" <? $myform->make_java_text_effect('utel', 'input_light'); ?> name="utel" id="utel" value="Телефон" title="Телефон" /></td>
				</tr>
				<tr>
                                    <td><input type="checkbox" name="docopy" id="docopy" /><label for="docopy">&nbsp;Отправить мне копию письма</label></td>
				</tr>
				<tr>
					<td><textarea  <? $myform->make_java_text_effect('backlinkMailText', 'input_light'); ?>  class="textarea_ajax input_width input_gray"  cols="35" rows="12" name="backlinkMailText" id="backlinkMailText" title="Сообщение" >Сообщение</textarea></td>
				</tr><?
                                    // ИНИЦИАЛИЗАЦИЯ КАПЧИ
                                    $captcha->codeid_input();
                                ?><tr>
                                        <td  style="padding-left:2px;">Код&nbsp;безопасности:<br><?
                                            ?><table cellpadding="0" cellspacing="0" border="0"><tr>
                                                <td valign="middle" style="vertical-align:middle;"><input type='text' name='gbcode'  maxlength='5' class='input_ajax input_light input_width_gbcode' title='Введите показанный код' /></td>
                                                <td valign="middle" style="vertical-align:middle; font-size:22px; font-weight:normal; font-style:normal; font-family:Arial, Helvetica, sans-serif; ">&nbsp;&larr;&nbsp;</td>
                                                <td valign="middle" style="vertical-align:middle; "><? $captcha->show_captcha(); ?></td>
                                                <td valign="middle" style="vertical-align:middle; ">&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:spamfixreload('<?=$captcha->img_id ?>', '<?=$captcha->codeid ?>')" >не&nbsp;вижу&nbsp;код</a></td>
                                        </tr></table></td>
                                </tr>
                                <tr><td >&nbsp;</td></tr>
                                <tr><td style="text-align:left; " align="left"><?
                                    ?><table cellpadding="0" cellspacing="0" border="0"><tr>
                                        <td valign="middle" style="text-align:left; vertical-align: middle; " align="left"><input type="submit" value="Отправить" class="button" /></td>
                                        <td  valign="middle" style="text-align:left; vertical-align: middle; " align="left">&nbsp;<?=ctrlEnterHint() ?></td>
                                    </tr></table><?
                                ?></td></tr>
			</table>
			</div>
		</td>
	</tr>
</table><?
?><input name="act" value="domail" type="hidden" /><?
?><input name="4ajax" value="1" type="hidden" /><?
?></form><?

/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_backlink_stat(){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	if (  $sitelog->isnewlog()  ) $sitelog->desc = $reg['backlink_name'];
	$sitelog->savelog();
}