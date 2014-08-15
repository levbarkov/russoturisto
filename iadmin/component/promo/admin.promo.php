<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
require_once( site_path.'/component/ad/ad_lib.php' );
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cancel':		$msg = 'Изменения не были сохранены: ';
						mosRedirect( 'index2.php?ca=adcfg&task=cfg', $msg );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['promo_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'save':		savepromo( $task );
						break;
	case 'save_promo_meta':	save_promo_meta();
							break;						
	case 'cfg':			
	default:			promocfg( $option );
						break;
}
function promocfg(){
	global $option, $reg;
	load_adminclass('config');	$conf = new config($reg['db']);
	
	$exgfg = ggo (1, "#__excfg");
	?><form <? ctrlEnterCtrlAS (' '.$reg['submit_save_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
			<table class="adminheading"><tr><td width="100%"><?
				$iway[0]->name=$reg['promo_name'];
				$iway[0]->url="";
	
				i24pwprint_admin ($iway);
				?></td></tr></table>
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
		<tr class="workspace">
			<td><strong>Внимание: </strong></td>
			<td>- Данный раздел содержит базовые настройки для оптимизации сайта под поисковые системы: Yandex, Mail, Google, Aport, Rambler и другие </td>
		</tr>
		</tr><? itable_hr(2); ?>
		<tr class="workspace">
			<td valign="top" style="vertical-align:top; "><strong>Зачем это нужно ?</strong></td>
			<td>-  Если Ваш сайт в результатах поиска в поисковых системах выводится на первых местах, значит всё сделано правильно и Ваш клиент к Вам придёт. А если сайт создан, но затерялся за дальними горизонтами интернет-пространства. Что предпринять в этом случае?.<br />
<br />
Поисковую оптимизацию сайтов (на профессиональном языке — SEO) можно считать первым шагом на пути к изменению сложившейся ситуации.<br />
Оптимизация сайта — основной этап подготовки к дальнейшему продвижению. Сделанная профессионально оптимизация помогает:<br /><br />
<strong>— Улучшить позиции в выдаче поисковых систем;</strong><br />
<strong>— Увеличить посещаемость сайта;</strong><br />
<strong>— Привлечение дополнительных клиентов;</strong><br />
<strong>— Увеличение продаж.</strong><br />
<br />
Работы по оптимизации сайта затрагивают все его стороны — от изменения текста до кардинальной переделки программного ядра и структуры:<br /><br />
— Оптимизация текстового наполнения сайта;<br />
— Оптимизация технической и программной составляющей сайта;<br />
— Оптимизация визуальной составляющей сайта;<br />
— Оптимизация эргономичности сайта.<br />
<br />
Итог поисковой оптимизации — отличная видимость web сайта поисковыми системами (индексируемость всех страниц сайта), а также повышение позиций сайта в выдаче поисковой системы. </td>
		</tr>
	</table>
	<? $conf->show_config('promo', "<br />Дополнительные настройки") ?>
	<input type="hidden" name="task" value="save"  />
	<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
	<input type="submit" style="display:none;" /></form>
	<table class="adminheading"><tbody><tr><td width="100%"><br>Мета-тэги для подтверждение прав на сайт</td></tr></tbody></table>
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
		<tr class="workspace">
			<td><strong>Внимание: </strong></td>
			<td>Подтверждение прав на сайт, необходимо для просматра подробных данных и управления индексированием сайта. Чтобы подтвердить свои права, скопируйте соответствующий мета-тег, сгенерированный поисковой системой в поле, привиденное ниже и нажмите кнопку «Сохранить мета-теги».</td>
		</tr>
		</tr><? itable_hr(2); ?>
		<tr class="workspace">
			<td valign="top" style="vertical-align:top; "><strong>Мета-тэги </strong></td>
			<td><?
				$promo_data = ggo (1, "#__promo");
				if (  $promo_data->search_passdata==''  ) $promo_data->search_passdata = "Google:	http://www.google.ru/webmasters/
	login:	
	pass:	
Yandex:	http://passport.yandex.ru/passport?mode=auth&retpath=http://webmaster.yandex.ru
	login:	
	pass:	";
				?><form name="promometaForm" action="index2.php" method="post">
				<textarea style=" width:100%; height:70px;" name="search_meta_validation"><?=$promo_data->search_meta_validation; ?></textarea><?
			?></td>
		</tr>
                <tr class="workspace">
			<td valign="top" style="vertical-align:top; "><strong>Дополнительная статистика</strong><br>(Каждая строка - ссылка для вызова статистики, отображается на главной странице в админ. части сайта)</td>
			<td><textarea style=" width:100%; height:70px;" name="ext_stat"><?=$promo_data->ext_stat; ?></textarea><?
			?></td>
		</tr>
                <tr class="workspace">
			<td valign="top" style="vertical-align:top; "><strong>Дополнительная статистика</strong><br>(Скрипты статистики, вставляются в конце тега body)</td>
			<td><textarea style=" width:100%; height:70px;" name="ext_stat_script"><?=$promo_data->ext_stat_script; ?></textarea><?
			?></td>
		</tr>
		<tr class="workspace">
			<td valign="top" style="vertical-align:top; "><strong>Данные для входа<br />в кабинет поисковых систем:<br /><a target="_blank" href="http://www.google.ru/webmasters/">Google</a>,<br /><a target="_blank" href="http://passport.yandex.ru/passport?mode=auth&retpath=http://webmaster.yandex.ru">Yandex</a></strong></td>
			<td><textarea style=" width:100%; height:100px;" name="search_passdata"><?=$promo_data->search_passdata; ?></textarea>
				<input type="hidden" name="task" value="save_promo_meta"  />
				<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
				<input type="submit" value="Сохранить мета-теги и доп. статистику"  /></form><?
			?></td>
		</tr>

	</table>
	<?
}
function savepromo( $task ) {
	global $reg, $my;
	load_adminclass('config');	$config = new config($reg['db']); $config->save_config();
	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['promo_name'], "", "" );
	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: '; mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg ); break;
	}
}
function save_promo_meta(){
	global $reg;
	$i24r = new mosDBTable( "#__promo", "id", $reg['db'] );
	$i24r->id = 1;
	$i24r->search_meta_validation = $_REQUEST['search_meta_validation'];
	$i24r->search_passdata = $_REQUEST['search_passdata'];
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	mosRedirect( 'index2.php?ca='.$reg['ca'], "Мета-тэги сохранены" );
}



?>