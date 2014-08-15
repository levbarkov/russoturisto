<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );


css("/theme/theme_extfiles/css/insite.css");
js("/includes/js/jquery.form.js");      // для выполнения форм в ajax (ajaxForm, ajaxSubmit)
js("/includes/js/jTypeWriter.js");      // для эффекта печатания сиволов ( используется в формах при написании ошибки )
js("/includes/js/insite.js");           // основные js-функции cms insite







global $reg;
if (  isset($_REQUEST['4ajax'])  ){// обработка запроса формы
	if (  ggrr('task')=='write_feed'  ){
		write_feed(); return;
	}
	if (   ggrr('name')==''  or  (ggrr('name'))=='Имя, Фамилия, Отчество'  )    { ?>$("#insite_feedback_server_answer").html('— Заполните поле «Ф.И.О.»').jTypeWriter({duration:1.5}); <? return; }
	if (   ggrr('txt')==''   or  (ggrr('txt'))== 'Отзыв:'  )                    { ?>$("#insite_feedback_server_answer").html('— Напишите отзыв').jTypeWriter({duration:1.5}); <? return; }
	if (   ggrr('gbcode')==''  )                                                { ?>$("#insite_feedback_server_answer").html('— Заполните поле «Код безопасности»').jTypeWriter({duration:1.5}); <? return; }
	$captcha = new captcha();	
	if(  !$captcha->check_me()  )                                               { ?>$("#insite_feedback_server_answer").html('— Введен неверный код безопасности').jTypeWriter({duration:1.5}); <? return; }
	// проверка закончена - добавляем отзыв и выводим благодарность
	add_feedback();
	?> $('#insite_feedback_main_table').fadeOut(300, function(){
                                                                    $("#insite_feedback_server_answer").hide();
                                                                    $("#insite_feedback_server_answer").html('<br /><strong>Спасибо. Ваш отзыв сохранен.</strong><br />После проверки он будет опубликован.');
                                                                    $("#insite_feedback_server_answer").fadeIn(700);
                                                                    }); <?
	return;
}

$limit 		= intval( mosGetParam( $_REQUEST, 'limit', 10 ) );  safelySqlStr ($limit);
$limitstart 	= get_insite_limit_start ( $limit );
$task 		= mosGetParam( $_REQUEST, 'task', "view" );         safelySqlStr ($task);

switch ( $task ) {
	default:
		show_backs( $task, $limit, $limitstart );
		break;
}
function add_feedback(){
	global $database, $my, $mosConfig_timeoffset, $reg; 
	
	$i24r = new mosDBTable( "#__feedback", "id", $database );
        $i24r->name = $_REQUEST['name'];
	$i24r->txt = $_REQUEST['txt'];
	$i24r->ctime = time();
	$i24r->publish = 0;
	
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();

	//$msg = 'Спасибо. Ваш отзыв сохранен.<br />После проверки он будет опубликован. ';
	//mosRedirect( '/'.$reg['feedback_seoname'].'/', $msg );
}

function show_backs( $task, $limit, $limitstart ) {
	global $database, $mainframe, $Itemid, $reg;
	
	// ВЫВОД СТАТИСТИКИ
	do_showscont_stat();
	
	if (  isset($_REQUEST['mosmsg'])  ){	?><div class="message"><? print urldecode(ggrr('mosmsg')); ?></div><?	}

	// query to determine total number of records
	$query = "SELECT COUNT(id) FROM #__feedback WHERE publish=1 " ;
	$total = ggsqlr( $query );
	if ( $total <= $limit ) $limitstart = 0;
	
	$query = str_replace("COUNT(id)","*", $query)." ORDER BY #__feedback.ctime DESC";
	$rows = ggsql( $query, $limitstart, $limit );

	// формируем наш список
	?><form action="" method="post" name="adminForm"><?
	if (  count($rows)>0  )
	foreach ($rows as $row ){
		$ilink = "index.php?c=feedback&task=view&id=".$row->id."&pi=60";
		?><div><br /><div class='feedback_frame' style=''>
				<div class='feedback_top' style=''>
					<div class='feedback_top_left'>
						<b class='feedback_big'><? print ($row->name); ?></b>
						<b class='feedback_small' class='feedback_small2' style='	font-size:12px; font-weight:normal;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
							$ctime = getdate ($row->ctime);
						?><? print num::fillzerro( $ctime['mday'],2 ); ?> <? print ru::GGgetMonthNames($ctime['mon']); ?> <? print $ctime['year']; ?> <? print num::fillzerro( $ctime['hours'],2); ?>:<? print num::fillzerro( $ctime['minutes'],2); ?></b>
					</div>
					<div class='feedback_top_right'></div><?
					?><div style='clear: both;'></div><?
				?></div><?
				?><div class='feedback_content'><? print ($row->txt); ?></div><?
		?></div><p class="clr"></p></div>
	<? } ?>
	<div align='center'><br /><b style='font-size: 14px;'>Всего отзывов: <? print count($rows); ?></b></div>
	<?
	global $mosConfig_absolute_path;
	require_once( $mosConfig_absolute_path . '/includes/pageNavigation.php' );

	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	/*
	 * УКАЗАНИЕ ДОПОЛНИТЕЛЬНЫХ ПАРАМЕТРОВ.
	 * Часто возникает необходимость при переходе по страницам передовать дополнительные параметры.
	 * Дополнительные параметры необходимо записать в массив sign
	 */
	$pageNav->sign['param1']='del_me_test';			// пример дополнительных параметров для поиска
	$pageNav->sign['param2']='it_is_demonstration';	// пример дополнительных параметров для поиска

	echo $pageNav->getListFooter(); 
	?></form>
	<br />
	<a href="javascript: ins_ajax_open('/feedback?4ajax=1&task=write_feed', 400, 430); void(0);">Написать отзыв</a>
	<a class=colorbox href="/feedback?4ajax=1&task=write_feed">Написать отзыв2</a>
	<?
	 
}
function write_feed(){
	global $reg;
	$captcha = new captcha();    $captcha->img_id="insite_feedback_code"; 	$captcha->codeid_id="insite_feedback_codeid";		$captcha->init();
	$myform = new insiteform();
	// $myform->formname = "feedback";
	// $myform->serveranswer = "insite_feedback_server_answer";
	// $myform->java_make_code();
	// $myform->debug_div();
	?><div id="wrapper_insite_feedback" class="wrapper_insite_ajax" style=" width:350px; height:340px"><form action="" method="post" name="feedback" id="feedback" >
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  class="insite_ajax_form_table">
		<tr height="5"><th></th></tr>
		<tr height="20"><th style=" text-align:left" align="left">Написать отзыв</th></tr>
		<tr height="20"><td style="font-size:8px"><div id="insite_feedback_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 300px; height:20px;" >&nbsp;</div></td></tr>
	</table>
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_feedback_main_table" class="insite_ajax_form_table">
		<tr height="8"><th width="60%" style=" text-align:left" align="left">&nbsp;</th><th width="40%"></th></tr>
		<tr>
			<td><input <? $myform->make_java_text_effect('name', 'input_light'); ?> ireqname="Не заполненно поле Имя" type='text' name='name' id='name' class='input_ajax input_width input_gray' value='Имя, Фамилия, Отчество' title="Имя, Фамилия, Отчество" /></td>
		</tr>
		<tr>
			<td valign='top'><textarea <? $myform->make_java_text_effect('txt', 'input_light'); ?> ireqname="Не забудьте написать отзыв" rows='8' cols='50' name='txt' id='txt' class='input_ajax input_width input_gray' title="Отзыв:">Отзыв:</textarea></td>
		</tr>
		<tr>
			<td style="padding-left:2px;">Код&nbsp;безопасности:&nbsp;*&nbsp;<br /><table cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" style="vertical-align:middle;"><? $captcha->codeid_input(); $captcha->show_captcha() ?></td>
				<td valign="middle" style="vertical-align:middle; font-size:22px; font-weight:normal; font-style:normal; font-family:Arial, Helvetica, sans-serif; ">&nbsp;&rarr;&nbsp;</td>
				<td valign="middle" style="vertical-align:middle; "><input type='text' name='gbcode'  id="insite_feedback_gbcode" maxlength='5' style='width:60px;vertical-align:middle;' class='input_ajax input_ajax_gbcode' title='Введите показанный код' /></td>
				<td valign="middle" style="vertical-align:middle; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:spamfixreload('insite_feedback_code', '<?=$captcha->codeid ?>')" >не&nbsp;вижу</a></td>
			</tr></table></td>
		</tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td style="text-align:right" align="right"><input type="submit" value="Отправить" class="button" /></td></tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $reg['c']; ?>" />
	<input type='hidden' name='task' value='addfeedback' />
	<input type="hidden" name="4ajax" value="1" />
	</form></div><?
	?><script language="javascript">
		// function sdfsdfs(){ alert('kkk'); }	sdfsdfs();
		var options = {		dataType:		'script'		};
		$('#feedback').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script><?
}

/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_showscont_stat(){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	$sitelog->f[1] = "view";
	if (  $sitelog->isnewlog()  ) $sitelog->desc = icat_get_stat_desc();
	$sitelog->savelog();
}
function icat_get_stat_desc($istr = ""){
		global $reg;
		if (  $istr==''  ) $istr = $reg['feedback_name'];
		return $istr;
}
?>