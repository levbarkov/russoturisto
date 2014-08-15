<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

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

switch ( $task )
{
	default: show_backs( $task, $limit, $limitstart ); 		break;
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

	?>
	<div class="holst">
		<div class="inner_content fs18_36">
			<?ipathway();?>
			<h1>Отзывы</h1>
			<form action="" method="post" name="adminForm">
			<ul class="list_otzuvu">
			<?
			if ($rows)
			foreach ($rows as $row){
				?><li><b><?=$row->name ?></b><br> <span><?=$row->tel?"({$row->tel})":''; ?></span> <p><?=$row->txt ?></p></li><? 
			}
			?></ul><?
			
			global $mosConfig_absolute_path;
			require_once( $mosConfig_absolute_path . '/includes/pageNavigation.php' );
			$pageNav = new mosPageNav( $total, $limitstart, $limit  );
			$pageNav->sign['param1']='del_me_test';			
			$pageNav->sign['param2']='it_is_demonstration';	
			echo $pageNav->getListFooter(); 
			?>
			
			</form>
			<? write_feed(); ?>
		</div>
	</div>
	<?
}



function write_feed()
{
	global $reg;
	$captcha = new captcha();    $captcha->img_id="insite_feedback_code"; 	$captcha->codeid_id="insite_feedback_codeid";		$captcha->init();
	$myform = new insiteform();
	// $myform->formname = "feedback";
	// $myform->serveranswer = "insite_feedback_server_answer";
	// $myform->java_make_code();
	// $myform->debug_div();
	?>
	<form action="" method="post" name="feedback" id="feedback" >
	<div class="add_otzuv">
		<h4>Оставить отзыв</h4>
		<div id="insite_feedback_main_table" style="padding:1px;">
			<div class="bk1">
				<label for="name">ФИО</label>
				<input class="input" ireqname="Не заполненно поле Имя" type='text' name='name' id='name' value='' placeholder="Фамилия, Имя" />
				<label for="name2">Род занятий</label>
				<input class="input" type="text" name="name2" id="name2" placeholder="Род занятий" />
				<label for="gbcode">Код безопасности:</label>
			</div>
			<div class="bk2">
				<label for="txt">Отзыв</label>
				<textarea ireqname="Не забудьте написать отзыв" name='txt' id='txt' placeholder="Отзыв:"></textarea>
				<input type="submit" class="btn2 fr" value="Отправить" />
				<div class="spam_code unl">
					<input type='text' name='gbcode' class="input gbcode" id="gbcode" maxlength='5' title='Введите показанный код' placeholder="код" />
					<b>&nbsp;&larr;&nbsp;</b>
					<? $captcha->codeid_input(); $captcha->show_captcha() ?>
					<a href="javascript:spamfixreload('insite_feedback_code', '<?=$captcha->codeid ?>')" title="Обновить картинку" >не вижу</a>				
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div id="insite_feedback_server_answer">&nbsp;</div>
	</div>
	<input type="hidden" name="option" value="<?php echo $reg['c']; ?>" />
	<input type='hidden' name='task' value='addfeedback' />
	</form>
	<script>
	$(function(){
		$('#feedback').submit(function( event ){
			$.ajax({
				type: "POST",
				dataType: "script",
				data: $("#feedback").serialize()
			});
			event.preventDefault();
		});
	});
	</script>
	<?
}

function add_feedback()
{
	global $database, $my, $mosConfig_timeoffset, $reg; 
	
	$i24r = new mosDBTable( "#__feedback", "id", $database );
    $i24r->name = $_REQUEST['name'];
    $i24r->tel = $_REQUEST['name2'];
	$i24r->txt = $_REQUEST['txt'];
	$i24r->ctime = time();
	$i24r->publish = 0;
	
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();

	//$msg = 'Спасибо. Ваш отзыв сохранен.<br />После проверки он будет опубликован. ';
	//mosRedirect( '/'.$reg['feedback_seoname'].'/', $msg );
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