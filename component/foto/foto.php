<?
/*
 * ПО УМОЛЧАНИЮ ССЫЛКА НА ФОТОГАЛЕРЕЮ НАЧИНАЕТСЯ С /foto/
 * ЕСЛИ НУЖНО ПОМЕНЯТЬ, ТО НЕОБХОДИМО В НАСТРОЙКАХ САЙТА ИЗМЕНИТЬ ЗНАЧЕНИЕ ПЕРЕМЕННОЙ 'foto_seoname'
 *
 * $reg['mainobj'] - глобальная переменная, определяется ядром СМС и содержит текущий объект - в данном случае - "Категорию фотогалереии"
 *
 */

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database, $reg;

// ОБРАБОТЧИК ДЛЯ СОХРАНЕНИЯ ФОТОГРАФИЙ НА ВСЕМ САЙТЕ - НЕУДАЛЯТЬ
if (  ggrr('task')=='newfoto_store'  ){
	global $database, $my, $reg;
	
	$component_foto = new component_foto();
        $component_foto->default_init();
	$component_foto->init( ggrr('type') );
	$component_foto->savefoto_foto($task);
	return;
}

// id - идентификатор категории
$fotocatid = isset($_REQUEST['id'])?$_REQUEST['id']:0;
if (  $fotocatid>0  ) $thisfotocat = &$reg['mainobj'];
else{
	$thisfotocat->id = 0;
	$thisfotocat->name = $reg['exfoto_name'];
}
do_foto_stat($thisfotocat, $fotocatid);

$component_foto = new component_foto( 0 );
$component_foto->init( 'exfoto' );

/*
 * ДОСТУП К ИНДИВИДУАЛЬНЫМ НАСТРОЙКАМ КАТЕГОРИИ ФОТОГАЛЕРЕИИ
 * $reg['#__exfoto_ID'.$reg['mainobj']->id.'__имя_переменной']
 */


// ВЫВОДИМ КАТЕГОРИИ
$fotocats = ggsql ("select * from #__exfoto WHERE parent=".$thisfotocat->id." ORDER BY #__exfoto.order  ");
$icats_per_row = 3;
$icats_index = 0;
?><table width="100%" cellspacing="1" cellpadding="4" border="0" align="left">
<tr><?
foreach ($fotocats as $fotocat){
	if (  ($icats_index>0)  &&  ($icats_index % $icats_per_row==0)  ) { ?></tr><tr><? }
	?><td nowrap="nowrap" width="<? print round(100/$icats_per_row); ?>%" valign="middle" align="left" class="foto_td" style=" text-align:left"><?
	
		$component_foto->parent = $fotocat->id;
		$fotocats = $component_foto->get_1stfoto();
		
		$href = $component_foto->createPreviewFotoLink ( 'small', 'org', $fotocats, '', '', ' border="0" ', $fotocat->sefnamefull.'/'.$fotocat->sefname  );
		$href_desc = "";
		if (  $fotocat->name  ) $href_desc = '<center><b style="white-space:nowrap; width:100%; ">'.$fotocat->name.'</b><br />'; 
		$href_desc .= '<span class="foto_cnt" style="white-space:nowrap; text-align:center">('.$component_foto->howmany_fotos ().' изображений)</span></center>';
		
		print_foto_gallery($href, $href_desc);
	?></td><?
	$icats_index++;
}
?></tr></table><?

// ВЫВОДИМ ФОТО

$component_foto->parent = $thisfotocat->id;
$fotocats = $component_foto->get_fotos();

$icats_per_row = 3;
$icats_index = 0;
$fotos_cnt = $component_foto->howmany_fotos ();

if (  $fotos_cnt>0  ){ ?>Всего изображений: <? print $fotos_cnt; } 

?><br />
<?php 
if (  $reg['mainobj']->id  ){
    editme(  'exfoto', array('id'=>$reg['mainobj']->id, 'note'=>'Редактировать описание категории фотогалереи')  );
    editme(  'exfoto_list', array('id'=>$reg['mainobj']->id, 'note'=>'Редактировать фотографии в категории')  );
}
?>
<table width="100%" cellspacing="1" cellpadding="4" border="0" >
<tr><?
if (  count ($fotocats)>0  )
foreach ($fotocats as $fotocat){
	if (  ($icats_index>0)  &&  ($icats_index % $icats_per_row==0)  ) { ?></tr><tr height="14"><td colspan="<?=$icats_per_row ?>">&nbsp;</td></tr><tr><? }
	?><td width="<? print round(100/$icats_per_row); ?>%" valign="middle" align="center" class="foto_td" style="text-align:center"><?
	$i24foto_desc = desafelySqlStr($fotocat->desc);
	$i24foto_link = $component_foto->createPreviewFotoLink ('small', 'org', $fotocat, '', ' rel="foto_group" class="highslide fancy" '  );
	print_foto_gallery($i24foto_link, $i24foto_desc, "/component/icontent/ramka/");
	?></td><?
	$icats_index++;
}
?></tr></table>

<?
/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_foto_stat($thisfotocat, $fotocatid){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	$sitelog->f[1] = $fotocatid;
	if (  $sitelog->isnewlog()  ) $sitelog->desc = $sitelog->get_description($thisfotocat, "#__exfoto", "parent", "/".$reg['exfoto_seoname'], $reg['exfoto_name'], $reg['exfoto_name'].", просмотр категории: ");

	$sitelog->savelog();
}