<?php
global $reg, $my;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

/*
 * ОТПРАВКА КОММЕНТАРИЯ НЕ ИЗ AJAX-ВСПЛЫВАЮЩЕГО ОКНА
 *
 * так как встреча.тся вредные фильтры-блокировщики, то popup меняем на floating
 */
if (  ggrr('task')=='reply_from_nofloating'  ){
	$server_answer_id = ggpr('server_answer_id');
	$comment_main_table_id = ggpr('comment_main_table_id');
	$com = new comments(ggpr('type'), $reg['db'], $reg);
	
	if (   ggpr('comname')==''     or  ggrr('comname')=='Ваше имя'   )                                 { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «Имя»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('commail')==''     or  ggrr('commail')=='E-mail'     ) 				   { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «E-mail»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('comcomment')==''  or  ggrr('comcomment')==$com->say[ggpr('say')]['MessageText'].':'  ){ ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «<?=$com->say[ggpr('say')]['MessageText'] ?>»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('gbcode')==''  )                                                                       { ?>$("#<?=$server_answer_id ?>").html('— Заполните поле «Код безопасности»').jTypeWriter({duration:1.5}); <? return; }
	$captcha = new captcha();	
	if(  !$captcha->check_me()  )                                                                      { ?>$("#<?=$server_answer_id ?>").html('— Введен неверный код безопасности').jTypeWriter({duration:1.5}); <? return; }
	
	// проверка закончена - отправляем письмо и выводим благодарность
	
	do_write_comment_stat(ggpr('type'), ggpi('parent'));
	$userid = 0;
	if (  $my->id  )	$userid = $my->id;
	
	$params = array(
					"parent" => ggpi('parent'),
					"uid" =>  $userid,
					"name" => ggpr('comname'),
					"mail" => ggpr('commail'),
					"text" => ggpr('comcomment'),
					"ip" => $_SERVER['REMOTE_ADDR'],
					"url" => $_SERVER['HTTP_REFERER']
	);	// ggtr ($params);
	$new_commid = $com->set($params);
	
	?>$('#<?=$comment_main_table_id ?>').fadeOut(300, function(){
															$("#<?=$server_answer_id ?>").hide();
															$("#<?=$server_answer_id ?>").html('<?=$com->say[ggpr('say')]['Thank'] ?>');
															$("#<?=$server_answer_id ?>").fadeIn(700);
														  }); <?
	return;
}

/*
 * ОТПРАВКА КОММЕНТАРИЯ ИЗ AJAX-ВСПЛЫВАЮЩЕГО ОКНА
 */
if (  ggrr('task')=='reply'  ||  ggrr('task')=='safe_after_edit'  ){
	$com = new comments(ggpr('type'), $reg['db'], $reg);
	if (   ggpr('uname')==''    or  ggrr('uname')=='Ваше имя'   ) 				      { ?>$("#insite_write_comment_server_answer").html('— Заполните поле «Имя»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('umail')==''    or  ggrr('umail')=='E-mail'  ) 				      { ?>$("#insite_write_comment_server_answer").html('— Заполните поле «E-mail»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('comment')==''  or  ggrr('comment')==$com->say[ggpr('say')]['MessageText'].':'  ) { ?>$("#insite_write_comment_server_answer").html('— Заполните поле «<?=$com->say[ggpr('say')]['MessageText'] ?>»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggpr('gbcode')==''  )                                                                  { ?>$("#insite_write_comment_server_answer").html('— Заполните поле «Код безопасности»').jTypeWriter({duration:1.5}); <? return; }
	$captcha = new captcha();	
	if(  !$captcha->check_me()  )                                                                 { ?>$("#insite_write_comment_server_answer").html('— Введен неверный код безопасности').jTypeWriter({duration:1.5}); <? return; }
	
	// проверка закончена - отправляем письмо и выводим благодарность
	do_write_comment_stat(ggpr('type'), ggpi('parent'));
	$userid = 0;
	if (  $my->id  )	$userid = $my->id;
	
	if (   ggrr('task')=='reply'  ){
		$params = array(
						"parent" => ggpi('parent'),
						"uid" =>  $userid,
						"name" => ggpr('uname'),
						"mail" => ggpr('umail'),
						"text" => ggpr('comment'),
						"ip" => $_SERVER['REMOTE_ADDR'],
						"url" => $_SERVER['HTTP_REFERER']
		);	// ggtr ($params);
		$new_commid = $com->set($params);
	} else if (   ggrr('task')=='safe_after_edit'  ){
		$thiscomment = $com->load_comment(ggpi('id'));

		if (  $thiscomment->userid==$my->id  ){
			$params = array(
							"id" =>   ggpi('id'),
							"text" => ggpr('comment'),
			);
			$new_commid = $com->edit($params);
		}
	}
	
	?>$('#insite_write_comment_main_table').fadeOut(300, function(){
															$("#insite_write_comment_server_answer").hide();
															$("#insite_write_comment_server_answer").html('<?=$com->say[ggpr('say')]['Thank'] ?>');
															$("#insite_write_comment_server_answer").fadeIn(700);
														  }); <?
	return;
}

	$com = new comments(ggpr('type'), $reg['db'], $reg);
	if (  ggrr('task')=='edit'  ) $mycomment = $com->load_comment(  ggri('id')  );
	$captcha = new captcha();    $captcha->img_id="insite_write_comment_code"; 	$captcha->codeid_id="insite_write_comment_codeid";		$captcha->init();
	$myform = new insiteform();
	$myname = "Ваше имя";
	$myemail = "E-mail";
	if (  $my->id  ) $user = new user($my);
	if (  $my->id  ) $myname = $user->getGentleName();
	if (  $my->id  and  $my->email!=''  ) $myemail = desafelysqlstr( $my->email );
	// $myform->formname = "feedback";
	// $myform->serveranswer = "insite_feedback_server_answer";
	// $myform->java_make_code();
	// $myform->debug_div();
	?><div id="wrapper_insite_write_us" class="wrapper_insite_ajax" style=" width:350px; height:380px; "><?
	?><script language="javascript">
		var options_write_comment_popup = {	dataType:		'script',
											beforeSubmit:  function(){	over_fade('#wrapper_insite_write_us', '#wrapper_insite_write_us', '', 0.5, 'popup'); },
											success: function(){ over_fade_hide(); }
					  };
		<? // $('#write_comment_ajax').submit(function() {  $(this).ajaxSubmit(options); 	return false; });  ?>
	</script>
	<form <? ctrlEnter( "  $('#write_comment_ajax').ajaxSubmit(options_write_comment_popup); return false; " ) ?> action="/index.php" method="post" name="write_comment_ajax" id="write_comment_ajax" onsubmit=" $(this).ajaxSubmit(options_write_comment_popup); 	return false; " >
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_write_comment_title_table" class="insite_ajax_form_table">
		<tr height="5"><th></th></tr>
		<tr height="20"><th style=" text-align:left" align="left"><?=$com->say[ggpr('say')]['Write'] ?> <?=$com->say[ggpr('say')]['one'] ?></th></tr>
		<tr height="20"><td style="font-size:8px"><div id="insite_write_comment_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 300px; height:20px;" >&nbsp;</div></td></tr>
		<tr height="8"><td></td><td style="font-size:8px">&nbsp;</td></tr>
	</table>
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_write_comment_main_table" class="insite_ajax_form_table">
		<tr>
			<td><input <? if (  !$my->id  ) $myform->make_java_text_effect('uname', 'input_light'); ?> size="30" class="input_ajax input_width input_gray" name="uname" id="uname" value="<?=$myname ?>" <? if (  $my->id  ) print 'readonly="1"'; ?> title="<?=$myname ?>" /></td>
		</tr>
		<tr>
			<td><input <? if (  !$my->id  ) $myform->make_java_text_effect('umail', 'input_light'); ?> size="30" class="input_ajax input_width input_gray" name="umail" id="umail" value="<?=$myemail ?>" <? if (  $my->id  ) print 'readonly="1"'; ?> title="<?=$myemail ?>" /></td>
		</tr>
		<tr>
			<td><textarea <? $myform->make_java_text_effect('comment', 'input_light'); ?>  class="textarea_ajax input_width input_gray"  cols="35" rows="12" name="comment" id="comment" title="<?=$com->say[ggpr('say')]['MessageText'] ?>:" ><? if (  ggrr('task')=='edit'  ) print $mycomment->text; else print $com->say[ggpr('say')]['MessageText'].':'; ?></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="padding-left:2px;">Код&nbsp;безопасности:&nbsp;*&nbsp;<br /><table cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" style="vertical-align:middle;"><? $captcha->codeid_input(); $captcha->show_captcha(); ?></td>
				<td valign="middle" style="vertical-align:middle; font-size:22px; font-weight:normal; font-style:normal; font-family:Arial, Helvetica, sans-serif; ">&nbsp;&rarr;&nbsp;</td>
				<td valign="middle" style="vertical-align:middle; "><input type='text' name='gbcode'  maxlength='5' class='input_ajax input_ajax_gbcode' title='Введите показанный код' /></td>
				<td valign="middle" style="vertical-align:middle; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:spamfixreload('insite_write_comment_code', '<?=$captcha->codeid ?>')" >не&nbsp;вижу&nbsp;код</a></td>
			</tr></table></td>
		</tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td style="text-align:center; " align="center"><input type="submit" value="Отправить" class="button" /><?=ctrlEnterHint() ?></td></tr>
	</table>
	<input type="hidden" name="c" value="<?php echo $reg['c']; ?>" />
	<input type="hidden" name="type" value="<?=ggpr('type') ?>" />
	<input type="hidden" name="say" value="<?=ggpr('say') ?>" />
	<input type="hidden" name="4ajax" value="1" />
	<? if (  ggrr('task')=='edit'  ) { ?><input type='hidden' name='task' value='safe_after_edit' /><input type='hidden' name='id' value='<?=ggri('id'); ?>' /><? }
	else  { ?><input type='hidden' name='task' value='reply' /><? } ?>
	<input type="hidden" name="parent" value="<?=ggpi('parent') ?>" />
	</form></div>
<?
/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_write_comment_stat_desc($type, $parent){
	$retcomment = "";
	if (  $type=='comment'){	$retcomment = "Комментарий к комментарию";				}
	else if (  $type=='icontent'){	$comm_obg = ggo($parent, "#__content"); $retcomment = "Комментарий к новости '".stripslashes($comm_obg->title)."'";		}
	else if (  $type=='ex'){	$comm_obg = ggo($parent, "#__exgood"); $retcomment = "Комментарий к товару '".stripslashes($comm_obg->name)."'";		}
	else if (  $type=='ad'){	$comm_obg = ggo($parent, "#__adgood"); $retcomment = "Комментарий к объявлению '".stripslashes($comm_obg->name)."'";	}
	return $retcomment;
}
function do_write_comment_stat($type, $parent){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	$sitelog->f[1] = $type;
	$sitelog->f[2] = $parent;
	if (  $sitelog->isnewlog()  ) $sitelog->desc = do_write_comment_stat_desc($type, $parent);
	$sitelog->savelog();
}