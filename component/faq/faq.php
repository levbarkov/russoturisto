<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

global $reg, $database;

$task 		= Api::$request->getParam('task', 'str', 'view');
$limit 		= Api::$request->getParam('limit', 'int', 10);
$limitstart = get_insite_limit_start ($limit);

# Поступил ajax запрос
if(Api::$request->getParam('4ajax', 'int', false)){
	if($task == 'write_faq'){
		faq_form();
		return false;
	}
	
	$name 		= Api::$request->getParam('name', 'str', '');
	$question 	= Api::$request->getParam('question', 'str', '');
	$gbcode 	= Api::$request->getParam('gbcode', 'str', '');
	
	if($name == '' || $name == 'Имя, Фамилия, Отчество'){
		echo '$("#insite_feedback_server_answer").html(\'— Заполните поле «Ф.И.О.»\').jTypeWriter({duration:1.5});';
		return false;
	}
	
	if($question == '' || $question == 'Вопрос:'){
		echo '$("#insite_feedback_server_answer").html(\'— Напишите вопрос\').jTypeWriter({duration:1.5});';
		return false;
	}
	
	if($gbcode == ''){
		echo '$("#insite_feedback_server_answer").html(\'— Заполните поле «Код безопасности»\').jTypeWriter({duration:1.5});';
		return false;
	}
	
	$captcha = new captcha();
	if(!$captcha->check_me()){
		echo '$("#insite_feedback_server_answer").html(\'— Введен неверный код безопасности\').jTypeWriter({duration:1.5});';
		return false;
	}
	
	$i24r = new mosDBTable('#__faq', 'id', $database);
    $i24r->name 		= safelySqlStr($name);
	$i24r->question 	= safelySqlStr($question);
	$i24r->created_at 	= time();
	$i24r->publish 		= 0;
	
	$i24r->store();
	
	echo <<<HTML
		$('#insite_feedback_main_table').fadeOut(300, function(){
			$("#insite_feedback_server_answer").hide();
			$("#insite_feedback_server_answer").html('<br /><strong>Спасибо. Ваш вопрос сохранен.</strong><br />После проверки он будет опубликован.');
			$("#insite_feedback_server_answer").fadeIn(700);
		});
HTML;
	
	return false;
}



# вывод статистики
do_showscont_stat();

$query = "select count(*) from #__faq where `publish` = 1 " ;
$total = ggsqlr( $query );
if ($total <= $limit)
	$limitstart = 0;

$query = str_replace('count(*)', '*', $query)." order by `created_at` desc";
$rows = ggsql($query, $limitstart, $limit );


$html = "<h1>Вопрос-ответ</h1><a href='javascript: ins_ajax_open(\"/faq/?4ajax=1&task=write_faq\", 400, 430); void(0);' class='question'>Задать вопрос</a>";

if (count($rows) > 0){
	foreach ($rows as $row ){
		$question = desafelysqlstr($row->question);
		$answer = desafelysqlstr($row->answer);
		
		$html .= <<<HTML
			<article>
				<a href="javascript: void(0);">{$question}</a>
				<div>
					<p>{$answer}</p>
				</div>
			</article>			
HTML;
	}
	
	require_once(site_path . '/includes/pageNavigation.php');

	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$html .= '<br /><br />';
	$html .= $pageNav->getListFooter();	
			
}
else
	$html .= '<br /><br /><p>База вопросов пуста, хотите <a href="javascript: ins_ajax_open(\'/faq/?4ajax=1&task=write_faq\', 400, 430); void(0);">стать первым?</a></p>';

echo $html;


function faq_form(){
	global $reg;
	
	$captcha = new captcha();
	$captcha->img_id 	= 'insite_feedback_code';
	$captcha->codeid_id = 'insite_feedback_codeid';
	$captcha->init();
	
	$myform = new insiteform();
	// $myform->formname = "feedback";
	// $myform->serveranswer = "insite_feedback_server_answer";
	// $myform->java_make_code();
	// $myform->debug_div();
	?><div id="wrapper_insite_feedback" class="wrapper_insite_ajax" style=" width:350px; height:340px"><form action="" method="post" name="feedback" id="feedback" >
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  class="insite_ajax_form_table">
		<tr height="5"><th></th></tr>
		<tr height="20"><th style=" text-align:left" align="left">Написать вопрос</th></tr>
		<tr height="20"><td style="font-size:8px"><div id="insite_feedback_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 300px; height:20px;" >&nbsp;</div></td></tr>
	</table>
	<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_feedback_main_table" class="insite_ajax_form_table">
		<tr height="8"><th width="60%" style=" text-align:left" align="left">&nbsp;</th><th width="40%"></th></tr>
		<tr>
			<td><input <? $myform->make_java_text_effect('name', 'input_light'); ?> ireqname="Не заполненно поле Имя" type='text' name='name' id='name' class='input_ajax input_width input_gray' value='Имя, Фамилия, Отчество' title="Имя, Фамилия, Отчество" /></td>
		</tr>
		<tr>
			<td valign='top'><textarea <? $myform->make_java_text_effect('txt', 'input_light'); ?> ireqname="Не забудьте написать вопрос" rows='8' cols='50' name='question' id='txt' class='input_ajax input_width input_gray' title="Вопрос:">Вопрос:</textarea></td>
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
	<input type='hidden' name='task' value='addfaq' />
	<input type="hidden" name="4ajax" value="1" />
	</form></div><?
	?><script language="javascript">
		// function sdfsdfs(){ alert('kkk'); }	sdfsdfs();
		var options = {		dataType:		'script'		};
		$('#feedback').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script>
	
	<?	
}

function do_showscont_stat(){
	global $reg;
	
	if (ifipbaned())
		return false;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	$sitelog->f[1] = 'view';
	
	if ($sitelog->isnewlog())
		$sitelog->desc = 'FAQ';
		
	$sitelog->savelog();
}


