<?
/*
 *
 * КАЖДЫЙ ОБЪЕКТ - НОВОСТЬ, СТАТЬЯ ИЛИ РУБРИКА ТЕПЕРЬ ИМЕЕТ ИНДИВИДУАЛЬНЫЙ НАСТРОЙКИ
 * НАПРИМЕР ДЛЯ КАКОЙ-ТО ОПРЕДЕЛЕННОЙ СТАТЬИ ВЫ МОЖЕТЕ ПРОПИСАТЬ ДРУГИЕ РАЗМЕРЫ ФОТО 
 * ИЛИ ВВЕСТИ НОВУЮ ПЕРЕМЕННУЮ, УКАЗЫВАЮЩУЮ НА КАКОЕ-ТО ЕЕ ОПРЕДЕЛЕННОЕ СОСТОЯНИЕ.
 *
 * ИНДИВИДУАЛЬНЫЕ НАСТРОЙКИ ДОСТУПНЫ В АДМИНКЕ ВО ВКЛАДКЕ "ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ"
 * ИНДИВИДУАЛЬНЫЕ НАСТРОЙКИ ПРЕДСТАВЛЯЮТ ИЗ СЕБЯ ОБЫЧНУЮ ПЕРЕМЕНУЮ РЕЕСТРА С ПРЕФИКСОМ _ID###_НАЗВАНИЕ_ПЕРЕМЕННОЙ
 * ### - id ОБЪЕКТА
 * ТАКИМ ОБРАЗОМ ДОСТУП К ДАННОЙ НАСТРОЙКЕ В КОДЕ ВЫГЛЯДЕТ СЛЕДУЮЩИМ ОБРАЗОМ $reg['_ID###_НАЗВАНИЕ_ПЕРЕМЕННОЙ']
 * ПРИМЕРЫ ИСПОЛЬЗОВАНИЯ ИНДИВИДУАЛЬНЫХ НАСТРОЕК МОЖНО ПОСМОТРЕТЬ В РУБРИКЕ СТАТЕЙ/НОВОСТЕЙ - "ПРОВЕРКИ-УДАЛИТЬ"
 * И В СТАТЬЕ "Текст для проверки" В УКАЗАННОЙ ВЫШЕ РУБРИКЕ.
 * 
 */

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database, $reg;
// ПОКАЗЫВАЕМ СТАТИЧНОЕ СОДЕРЖИМОЕ
// id - идентификатор содержимого

// ЗАГРУЗКА СОДЕРЖИМОГО
$realxml = "typedcontent";
$idstat = isset ($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$statcont = ggo ($idstat, "#__content");
if (  $idstat==true  ){
	// ПРОВЕРКА УСЛОВИЙ ВЫВОДА СОДЕРЖИМОГО
	
	// ВЫВОД СТАТИСТИКИ
	do_showscont_stat($statcont);	
	
	editme(  'typedcontent', array('id'=>$reg['mainobj']->id)  );
	/*
	 * ДОСТУП К ИНДИВИДУАЛЬНЫМ НАСТРОЙКАМ СОДЕРЖИМОГО
	 * $reg['#__content_ID'.$reg['mainobj']->id.'__имя_переменной']
	 */
	
	$viewintro = 1;
	if (  isset($_REQUEST['task'])  )
		if (  $_REQUEST['task']=='viewfull'  )	{ $showcont = desafelySqlStr( $statcont->fulltext ); $viewintro = 0; }
		
	if (  $viewintro==1  ) $showcont = desafelySqlStr( $statcont->introtext );

	// увеличение счетчика
	$i24r = new mosDBTable( "#__content", "id", $database );
		$i24r->id = $statcont->id;
		$i24r->hits = $statcont->hits+1;
	if (!$i24r->check()) { $showcont = "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();

}
else {	// не найден объект содержимого
	$showcont = _NOT_FIND_STAT_CONTENT;
}




if($reg['sefname1'] == 'hot_tours')
{ 
	$slider = slider();

	$showcont = str_replace('mesto_slidera', $slider, $showcont);
}


$wide = '';
$wide_arr = explode("\n", $statcont->attribs);
$wide_arr = explode('=', $wide_arr[2]);
if($wide_arr[1] == 'wide'){ $wide = ' wide'; }


?>
<div class="holst">
	<div class="inner_content<?=$wide ?> unl">
		<?ipathway();?>
		<?=$showcont; ?>
	</div>
</div>
<?











/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_showscont_stat($thisicat){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	$sitelog->f[1] = "viewcontent";
	$sitelog->f[2] = $thisicat->id;
	if (  $sitelog->isnewlog()  ) $sitelog->desc = icat_get_stat_desc($thisicat);
	$sitelog->savelog();
}
function icat_get_stat_desc($thisfotocat, $istr = "Просмотр информационного раздела: "){
	    $iret = $istr.$thisfotocat->title;
		return $iret;
}





function slider()
{
	ob_start();
		?>
		<link href="/includes/css/start.css" rel="stylesheet" type="text/css" />
		<script language="javascript" type="text/javascript" src="/includes/js/jquery.easing.js"></script>
		<script language="javascript" type="text/javascript" src="/includes/js/start.js"></script>
		<div class="st-slider">
			<div id="lofslidecontent45" class="lof-slidecontent  lof-snleft">
				<div class="preload"><div></div></div>
				<? $rows = ggsql(" select b.name, b.link, b.desc, b.org from `#__exfoto` as a, `#__foto` as b where a.id = b.parent and a.sefname='start' and b.publish = '1' order by b.ordering limit 15; "); ?>
				<div class="lof-main-outer">
					<ul class="lof-main-wapper">
						<? 
						$first = ' class="current"';
						foreach( $rows as $row )
						{
							$desc = $row->desc ? " <div class='slider-description'>". desafelySqlStr($row->desc) ."</div>" : '';
							
							if($row->link)	{ echo "<li{$first}><a href='{$row->link}'><img src='/images/foto/{$row->org}' />{$desc}</a></li>"; }
							else 			{ echo "<li{$first}><img src='/images/foto/{$row->org}' />{$desc}</li>"; }
							$first = '';
						} 
						?>
					</ul>  	
				</div>
				<div class="lof-navigator-outer">
					<ul class="lof-navigator">
						<? foreach( $rows as $row ){ echo "<li><div>{$row->name}</div></li>"; } ?>
					</ul>
				</div>
			</div>
			<div class="module_tours1">
				<?/**/?>
				<div id="TVSearchForm"></div><script src="http://tourvisor.ru/module/newform/searchform.min.js"></script>
				<script type="text/javascript"> TV.initModule({ formtype: '4', showoperator: '0', width: '250px', leftalign: false, city: 12}); </script><style>
				.TVMainForm{
				background-color:#2a7dc5;
				background-color:rgba(42,125,197,1.00);
				background-image:none;
				}
				.TVSearchButton{
				background-image:none;
				}
				</style>
				<?/**/?>
			</div>			
		</div>
		<?
	$result = ob_get_contents();
	ob_end_clean();

	return $result;
}



?>