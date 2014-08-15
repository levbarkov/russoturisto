<?php
/*
 * ПО УМОЛЧАНИЮ ССЫЛКА НА ДОСКУ ОБЪЯВЛЕНИЙ НАЧИНАЕТСЯ С /board/
 * ЕСЛИ НУЖНО ПОМЕНЯТЬ, ТО НЕОБХОДИМО В НАСТРОЙКАХ САЙТА ИЗМЕНИТЬ ЗНАЧЕНИЕ ПЕРЕМЕННОЙ 'ad_seoname'
 */

//ggtr5($_FILES);
//ggd($_REQUEST);
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
require_once( 'ad_lib.php' );

$id 		= intval( mosGetParam( $_REQUEST, 'id', 0 ) );			safelySqlInt ($id);
$print_version 	= intval( mosGetParam( $_REQUEST, 'pop', 0 ) );			safelySqlInt ($print_version);
$limit 		= intval( mosGetParam( $_REQUEST, 'limit', 10 ) );		safelySqlInt ($limit);
$limitstart 	= get_insite_limit_start ( $limit );
$task 		= mosGetParam( $_REQUEST, 'task', "adcat" );			safelySqlStr ($task);
if (  $task==''  ) $task = "adcat";

switch ( $task ) {

	case 'view':
		ishowItem( $id, $gid, $print_version, $option );
		break;
	case 'thank':
		thank( $id );
		break;
	case 'new':
		editad( 0 );
		break;
	case 'save':
		savead( $id );
		break;
	case 'adcat':
		showadcat( $id, $gid, $print_version, $Itemid, $limit, $limitstart, $task );
		break;
	case 'excomp':
		showexcomp( $id );
		break;


}
function editad( $uid ){
	global $database, $my, $acl, $mainframe, $reg;
	
	if (  $uid>0  ) $row = ggo ($uid, "#__adgood");
	else {
		$row->id = 0;
		$row->name = "";
		$row->sku = "";
		$row->parent = ggri('id');
		$row->sdesc = "";
		$row->publish = 1;
		$row->order = 1;
	}
	$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");
	do_adcatlist(0, $vcats, $row->parent);

?><form name="adminForm" action="/index.php" method="post" enctype="multipart/form-data"><input type="hidden"  name="iuse" id="iuse" value="0" /><?	
?><table border="0" cellpadding="4" cellspacing="0" width="95%" align="center">
	<tr style="background-color:#eef2fa">
		<td>Категории: </td>
		<td>
			<? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="adgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
		</td>
		<? $ins_adgood_price = ggsql (" select * from #__adgood_price ");
		$ins_adgood_price_count = count ($ins_adgood_price); ?>
		<td rowspan="2" valign="top" style="vertical-align:top;" >
			<table border="0" cellpadding="4" cellspacing="0" width="95%" align="right">
				<? for ($i=1; $i<=$ins_adgood_price_count; $i++){ ?>
				<tr style="background-color:#eef2fa">
					<td><? print $ins_adgood_price[$i-1]->d; ?>: </td>
					<td><input type="text" name="price<? print $i; ?>"  value="<?
						$eval_str = '$cprice = $row->price'.$i.'; ';
						eval ($eval_str);
						print $cprice;
					?>" /></td>
				</tr>
			<? } ?>
			</table>
		</td>
	</tr>
	<tr style="background-color:#eef2fa">
		<td>Название: </td>
		<td><input name="name" size="104" mosreq="1" moslabel="Название" value="<? print ($row->name); ?>" /></td>
	</tr>
	

	<tr style="background-color:#eef2fa">
		<td>Описание: </td>
		<td colspan="2"><textarea mce_editable="true" style="width: 85%; height: 150px;" rows="5" cols="45" name="sdesc" id="sdesc"><? ($row->sdesc); ?></textarea></td>
	</tr>
	
	<? $ins_adgood_f = ggsql (" select * from #__adgood_f ");
	$ins_adgood_f_count = count ($ins_adgood_f); ?>
	<? for ($i=1; $i<=$ins_adgood_f_count; $i++){ ?>
	<tr style="background-color:#eef2fa">
		<td><? print $ins_adgood_f[$i-1]->d; ?>: </td>
		<td colspan="2"><input type="text" size="120" name="f<? print $i; ?>"  value="<?
			$eval_str = '$cf = $row->f'.$i.'; ';
			eval ($eval_str);
			print $cf;
		?>" /></td>
	</tr>
	<? } 
	?><tr style="background-color:#eef2fa"><?
		?><td rowspan="2" valign="top" style="vertical-align:top">Основное изображение:</td><?
		?><td><? print $row->images; ?></td><?
		?><td></td><?
	?></tr><?
	?><tr style="background-color:#eef2fa"><?
		?><td><input type="file" class="inputbox" style="width:100%" name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /></td><?
		?><td></td><?
	?></tr><?
	
	?><tr style="background-color:#eef2fa; display:none; "><?
			?><td></td><?
			?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr></table></td><?
	?></tr><?
	?><tr style="background-color:#eef2fa"><?
			?><td>Основное изображение:</td><?
			?><td colspan="2"><a class="highslide" onclick="return hs.expand(this)" href="<? print site_url."/images/ad/good/".$row->imagesorg ?>" ><img name="view_imagelist" id="view_imagelist" src="<? print site_url."/images/ad/good/".$row->images ?>" border="0" /></a></td><?
	?></tr><?
	
	for ($i=2; $i<10; $i++){
		?><tr style="background-color:#eef2fa"><?
			?><td rowspan="2" valign="top" style="vertical-align:top">Фото <?=$i; ?>:</td><?
			?><td><? eval( 'print $row->images'.$i.';' );  ?></td><?
			?><td></td><?
		?></tr><?
		?><tr style="background-color:#eef2fa"><?
			?><td><input type="file" class="inputbox" style="width:100%" name="newfoto<?=$i; ?>" id="newfoto<?=$i; ?>" value="" onchange="document.getElementById('view_imagelist<?=$i; ?>').src = '/includes/images/after_save.jpg'" /></td><?
			?><td></td><?
		?></tr><?
		?><tr style="background-color:#eef2fa; display:none; "><?
				?><td></td><?
				?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto<?=$i; ?>" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr></table></td><?
		?></tr><?
		?><tr style="background-color:#eef2fa"><?
				?><td>Фото <?=$i; ?>:</td><?
				?><td colspan="2"><a class="highslide" onclick="return hs.expand(this)" href="<? eval( 'print site_url."/images/ad/good/".$row->imagesorg'.$i.' ;' ); ?>" >
				<img name="view_imagelist<?=$i; ?>" id="view_imagelist<?=$i; ?>" src="<? eval( 'print site_url."/images/ad/good/".$row->images'.$i.' ;' ); ?>" border="0" /></a></td><?
		?></tr><?
	}
	?><input type="hidden"  name="iuse" id="iuse" value="0" /><?
	?><input type="hidden" name="input_id" id="input_id" value="imagelist" /><?
	?><input type="hidden"  name="isrc_id" id="isrc_id" value="view_imagelist" /><?
	?><tr>
		<td><input type="button" value="Отправить" onclick="doform();" />
		</td>
	</tr>
</table>

<input type="hidden" name="task" value="save"  />
<input type="hidden" name="c" value="ad" />

<script language="javascript">
function doform(){
	if (  document.adminForm.parent.value==0  ) { alert ("Выберите категорию"); return;}
	document.adminForm.submit();
	return 1;
}
</script>

<? 
}

function savead( $id ){
	global $database, $my, $reg;
	//ggtr(  get_adcat_sefnamefull( ggri('parent') )  );
	//ggd($_REQUEST); 
	//ggd ($_FILES);
	  
	$sefnamefullcat = adgood_get_sefnamefullcat( ggri('parent') );
	$i24r = new mosDBTable( "#__adgood", "id", $database );
	$i24r->id = safelySqlInt($_REQUEST['id']);
	$i24r->parent = $_REQUEST['parent'];
    $i24r->name = ggrr('name');
	$i24r->sdesc = $_REQUEST['sdesc'];
	$i24r->sefname = sefname( ggrr('name') );
	$i24r->sefnamefullcat = $sefnamefullcat;
	$i24r->publish = 1;

	$ins_adgood_price = ggsql (" select * from #__adgood_price ");
	$ins_adgood_price_count = count ($ins_adgood_price);
	for ($i=1; $i<=$ins_adgood_price_count; $i++){
			$eval_str = 'if (  $_REQUEST["price'.$i.'"]>0  ) $i24r->price'.$i.'=$_REQUEST["price'.$i.'"]; '; eval ($eval_str);
	}
	$ins_adgood_f = ggsql (" select * from #__adgood_f ");
	$ins_adgood_f_count = count ($ins_adgood_f);
	for ($i=1; $i<=$ins_adgood_f_count; $i++){
			$eval_str = '$i24r->f'.$i.'=$_REQUEST["f'.$i.'"]; '; eval ($eval_str);
	}

	if (  $i24r->id>0  ) { $exoldgood = ggo (  $i24r->id, "#__adgood"  ); $_REQUEST["imagelistorg"] = $exoldgood->imagesorg; $_REQUEST["imagelist"] = $exoldgood->images; }

	if (  $_FILES["newfoto"]['tmp_name']  ){

		$iexfototype = 'jpg';//i24get_file_extension ($_FILES["newfoto"]['tmp_name']);
		//$iexfototype = "jpg"; ggtr ($iexfototype);
//		ggd ($_FILES["newfoto"]);
		$iexuni = md5(uniqid("exsalon"));
		$_FILES["newfoto"]['name'] = trans2eng ($_FILES["newfoto"]['name']);
		$_FILES["newfoto"]['name'] = str_replace(" ", "_", $_FILES["newfoto"]['name']);
		$ismallexname = $_FILES["newfoto"]['name']."_small___".$iexuni.".".$iexfototype;
		$isorgexname = $_FILES["newfoto"]['name']."_orign___".$iexuni.".".$iexfototype;
		$ismidexname = $_FILES["newfoto"]['name']."_middle___".$iexuni.".".$iexfototype;
		$isfullexname = $_FILES["newfoto"]['name']."_full___".$iexuni.".".$iexfototype;
		
		$i24makesmallfoto_func = $reg['adgoodmain_org_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
		$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path."/images/ad/good/".$isorgexname,
								$reg['adgoodmainorgwidth'],	$reg['adgoodmainorgheight'],	$reg['adgoodmaintag']); 
								$i24r->imagesorg = $isorgexname;
		if (  $reg['adgoodmainmidwidth']!=0  or  $reg['adgoodmainmidheight']!=0  ){	// необходимо создать среднее фото для объявления
			$i24makesmallfoto_func = $reg['adgoodmain_mid_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto'; // ggd ($i24makesmallfoto_func);
			$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path."/images/ad/good/".$ismidexname,
									$reg['adgoodmainmidwidth'],	$reg['adgoodmainmidheight'],	$reg['adgoodmaintag']);
									$i24r->imagesmid = $ismidexname;
		}
		if (  $reg['adgoodmainfullwidth']!=0  or  $reg['adgoodmainfullheight']!=0  ){	// необходимо создать гиганское фото для объявления
			$i24makesmallfoto_func = $reg['adgoodmain_full_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
			$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path."/images/ad/good/".$isfullexname,
									$reg['adgoodmainfullwidth'],	$reg['adgoodmainfullheight'],	$reg['adgoodmaintag'], 70);
									$i24r->imagesfull = $isfullexname;
		}
		//  необходимо уменьшить основное изображение		
		$i24makesmallfoto_func = $reg['adgoodmain_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
		$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path."/images/ad/good/".$ismallexname,
								$reg['adgoodmainsmallwidth'],	$reg['adgoodmainsmallheight'],	$reg['adgoodmaintag']);
								$i24r->images = $ismallexname;
	}
	
	for ($i=2; $i<10; $i++){
		if (  $_FILES["newfoto".$i]['tmp_name']  ){
			$iexfototype = 'jpg';//i24get_file_extension ($_FILES["newfoto"]['tmp_name']);
			$iexuni = md5(uniqid("exsalon"));
			$_FILES["newfoto".$i]['name'] = trans2eng ($_FILES["newfoto".$i]['name']);
			$_FILES["newfoto".$i]['name'] = str_replace(" ", "_", $_FILES["newfoto".$i]['name']);
			$ismallexname = $_FILES["newfoto".$i]['name']."_dop_small___".$iexuni.".".$iexfototype;
			$isorgexname = $_FILES["newfoto".$i]['name']."_dop_orign___".$iexuni.".".$iexfototype;
			$isfullexname = $_FILES["newfoto".$i]['name']."_dop_full___".$iexuni.".".$iexfototype;
			if (  $reg['adgoodmainfullwidth']!=0  or  $reg['adgoodmainfullheight']!=0  ){	// необходимо создать гиганское фото для объявления
				$i24makesmallfoto_func = $reg['adgoodmain_full_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
				$i24makesmallfoto_func( $_FILES["newfoto".$i]['tmp_name'], site_path."/images/ad/good/".$isfullexname,
										$reg['adgoodmainfullwidth'],	$reg['adgoodmainfullheight'],	$reg['adgoodmaintag'], 70);
										eval ( '$i24r->imagesfull'.$i.' = $isfullexname;' );
			}
			$i24makesmallfoto_func = $reg['adgoodmain_org_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
			$i24makesmallfoto_func( $_FILES["newfoto".$i]['tmp_name'], site_path."/images/ad/good/".$isorgexname,
										$reg['adgoodmainorgwidth'],	$reg['adgoodmainorgheight'],	$reg['adgoodmaintag']);
										eval ( '$i24r->imagesorg'.$i.' = $isorgexname;' );
			//  необходимо уменьшить основное изображение
			$i24makesmallfoto_func = $reg['adgood_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
			$i24makesmallfoto_func( $_FILES["newfoto".$i]['tmp_name'], site_path."/images/ad/good/".$ismallexname,
										$reg['adgoodmainsmallwidth'],	$reg['adgoodmainsmallheight'],	$reg['adgoodmaintag']);
										eval ( '$i24r->images'.$i.' = $ismallexname;' );
		}
	}

	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__adgood WHERE parent=".$_REQUEST['parent']." ORDER BY #__adgood.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
		$i24r->order = $iexmaxorder[0]->order+1;
	}

	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	adcat_update_goods ( ggri('parent') );
	switch ( $task ) {
		default:  $msg = 'Объект сохранен: '. $i24r->name;  mosRedirect( $sefnamefullcat, $msg ); break;
	}
}

function ishowItem( $id, $gid, $print_version, $option ){
global $database, $reg;
	$row = ggo ($id, "#__adgood");
	do_icat_stat_content($row);
	
?><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" >
						<tbody><tr>
						<td width="10%" valign="top" style="vertical-align:top;"><?
							// ВЫВОДИМ ФОТО
							$fotocats = array();
							if (  $row->imagesorg!=''  and  $row->images!=''  ){ $fotocats[1]->org = $row->imagesorg; $fotocats[1]->small = $row->images; }
							
							for ($i=2; $i<10; $i++){
								eval ( '$url_orgpic = $row->imagesorg'.$i.';' ); eval ( '$url_pic = $row->images'.$i.';' );
								if (  $url_pic!=''  and  $url_orgpic!=''  ){ $fotocats[$i]->org = $url_orgpic; $fotocats[$i]->small = $url_pic; }
							}
							$icats_per_row = 1;
							$icats_index = 0;
							$fotos_cnt = count($fotocats);
							
							if (  $fotos_cnt>0  ){ 							
								?><table width="100%" cellspacing="1" cellpadding="4" border="0" >
								<tr><?
								foreach ($fotocats as $fotocat){
									if (  ($icats_index>0)  &&  ($icats_index % $icats_per_row==0)  ) { ?></tr><tr><? }
									?><td width="<? print round(100/$icats_per_row); ?>%" nowrap="nowrap" valign="middle" align="center" class="foto_td" style="text-align:center"><?
										$i24foto_link = '<a href="'.site_url.'/images/ad/good/'.$fotocat->org.'" rel="ad" class="highslide fancy"><img border="0"  src="'.site_url.'/images/ad/good/'.$fotocat->small.'"/></a>';
										$i24foto_desc = stripslashes($fotocat->desc);
										print_foto_gallery($i24foto_link, $i24foto_desc, "/component/ad/ramka/"); // print_foto_desc($i24foto_desc);
									?></td><?
									$icats_index++;
								}
								?></tr></table><?
							}
						?></td>
						<td valign="top" height="100%" width="90%" style="padding: 0px 15px 15px; ">

							<table class="contentpaneopen">
								<tbody><tr><td width="100%" class="contentheading"><strong><? print stripslashes($row->name); ?></strong></td></tr>
							</tbody></table>

							<table class="contentpaneopen" border="0"><tbody><tr>
								<td valign="top" colspan="2"><? print stripslashes( str_replace("\r", "<br />", $row->sdesc) ); ?></td>
							</tr></tbody></table>

							<table class="contentpaneopen"><tbody><tr>
								<td valign="top" colspan="2">Тел.: <?=$row->f1 ?><br />Адрес: <?=$row->f2 ?><br /><strong>Стоимость: <? print num::flexprice($row->price1); ?> <? print rub1(); ?></strong></td>
							</tr></tbody></table>

							<span class="article_seperator"> </span><div class="back_button" ><a href="javascript:history.go(-1)">Вернуться</a></div>

						</td></tr></tbody></table>
									

<? 
}

function showadcat( $id=0, $gid, $print_version, $now=NULL, $limit, $limitstart, $task ) {
global $database, $mainframe, $Itemid, $reg;

// ОПРЕДЕЛЯЕМ ID ПАПЫ
$papa = ggsql ("select * from #__adcat WHERE id='".ggri('id')."'  ");

if (  count($papa)>0  ) {
	$id = $papa[0]->id;
	$icars = $papa[0];
} else {
	$id=0;
	$icars->id = 0;
	$icars->name = $reg['ad_name'];
}
do_icat_stat_icat(  $icars  );

// ВЫВОДИМ КАТЕГОРИИ
$fotocats = ggsql ("select * from #__adcat WHERE parent=".$icars->id."  ORDER BY #__adcat.order  ");
if (  count ($fotocats)==0  ){	// считываем категории верхнего уровня
	$fotocats = ggsql ("select * from #__adcat WHERE parent=".$icars->parent." ORDER BY #__adcat.order  ");
}

$showeee = 0;
if (  $id==73  &&  ggrr('task')=='adcat') $showeee = 1;	// категория Продажа
if (  $id==74  &&  ggrr('task')=='adcat') $showeee = 1; // категория Покупка

$icats_per_row = 1;
if (  $showeee==1  ) $icats_per_row = 2;
$icats_index = 0;
?><table width="100%" cellspacing="1" cellpadding="1" border="0">
<tr><td>&nbsp;&nbsp;Выберите рубрику: <br /></td></tr>
<tr><?
foreach ($fotocats as $fotocat){
	if (  ($icats_index>0)  &&  ($icats_index % $icats_per_row==0)  ) { ?></tr><tr><? }
	if (  $showeee==1  ) { ?><tr valign="top" height="7"><td colspan="2"></td></tr>
							<tr valign="top" height="2"><td class="adline" colspan="2"></td></tr>
							<tr valign="top" height="7"><td colspan="2"></td></tr><? }
	?><td valign="top" nowrap="nowrap" width="<? print round(100/$icats_per_row); ?>%"  align="left" class="foto_td" style="text-align:left; padding-left:45px; vertical-align:top;"><?

		if (  $fotocat->id==$id  ) $i24foto_desc = '<span class="ad_cat_selected" >'.stripslashes($fotocat->name).'</span>';
		else $i24foto_desc = '<a class="wewsds" href="'.$fotocat->sefnamefull.'/'.$fotocat->sefname.'" >'.stripslashes($fotocat->name).'</a>';
		print  ($i24foto_desc);	
		if (  $showeee==1  ) {
			$fotocatseee = ggsql ("select * from #__adcat WHERE parent=".$fotocat->id." and id<>33 ORDER BY #__adcat.order  ");	
			$icats_per_roweee = 2;
			$icats_indexeee = 0;
			?><table width="100%" cellspacing="1" cellpadding="1" border="0">
			<tr><?
			foreach ($fotocatseee as $fotocateee){
				if (  ($icats_indexeee>0)  &&  ($icats_indexeee % $icats_per_roweee==0)  ) { ?></tr><tr><? }
				?><td nowrap="nowrap" width="<? print round(100/$icats_per_roweee); ?>%" valign="middle" align="left" class="foto_td" style="text-align:left; padding-left:45px;"><?
					$ccccoooiu = ggsqlr ("select count(id) from #__adgood WHERE parent=".$fotocateee->id." ; ");
					
					if (  $fotocateee->id==$id  ) $i24foto_desceee = '<span style="white-space:nowrap; width:100%; ">'.stripslashes($fotocateee->name).'</span>';
					else $i24foto_desceee = '<span style="white-space:nowrap; width:100%; "><a class="wewdw" href="'.$fotocat->sefnamefull.'/'.$fotocat->sefname.'/'.$fotocateee->sefname.'" >'.stripslashes($fotocateee->name).' ('.$ccccoooiu.')</a></span>';
					print  ($i24foto_desceee);	
				?></td><?
				$icats_indexeee++;
			}
			?></tr></table><?
		}
	?></td><?
	$icats_index++;
}
?></tr></table><br /><?

	$where = array();
	$sub_adcats_array = array(); $sub_adcats_array[] = " parent=".$id." ";
	$sub_adcats = ggsql ( " SELECT * FROM #__adcat WHERE parent = $id " );
	foreach ($sub_adcats as $sub_adcat){ $sub_adcats_array[] = " parent=".$sub_adcat->sefname." "; }
	$sub_adcats_sql = ( count( $sub_adcats_array ) ? " ( ". implode( "\n OR ", $sub_adcats_array )." ) " : '' );
	$where[] = $sub_adcats_sql;
	
	$where 	= ( count( $where ) ? "\n WHERE ". implode( "\n AND ", $where ) : '' );
	if (  $id==0  ) $show_cat = 1; else $show_cat = 0;
	if (  $id==0  ){	$where = " ";	}
	// query to determine total number of records
	$query = "SELECT COUNT(id) FROM #__adgood ". $where ;
	$total = ggsqlr( $query );
	if ( $total <= $limit ) $limitstart = 0;
	
	$query = str_replace("COUNT(id)","*", $query). " ORDER BY #__adgood.order DESC";
	$rows = ggsql( $query, $limitstart, $limit );

	// формируем наш список объявлений
	?><form action="index.php" method="get" name="adminForm">
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="ex_good_list"> <?
	if (  count($rows)>0  )
	foreach ($rows as $row ){
		$ilink = $row->sefnamefullcat.'/'.$row->sefname.".html";
				?><tr valign="top">
					<td width="22%" <? if (  !$row->images  ) print ' class="adnoimg" '; ?> ><?
						if (  $row->images==''  ) { ?>&nbsp;<? }
						else {
							$i24foto_link = '<a href="'.site_url.'/images/ad/good/'.$row->imagesorg.'" class="highslide fancy" rel="ad" ><img vspace="0" hspace="0" border="0" align="left" src="';
							$i24foto_link .= site_url.'/images/ad/good/'.$row->images;
							$i24foto_link .= '" /></a>';
							print_foto_gallery($i24foto_link, "", "/component/ad/ramka/");
						}
					 ?></td>
					<td width="1%"/>&nbsp;</td>
					<td width="77%" align="justify" style="text-align:justify"><a class="adgood_ih2newtitle" href="<? print $ilink; ?>"><? print stripslashes($row->name); ?></a><?
						?><br/><span class="adgood_ih2newtext"><? if (  strlen(trim($row->sdesc))<$reg['adcat_contentmaxlength_intro']  ) print stripslashes(  str_replace("\r","<br />",$row->sdesc)  )."<div class='back_button'>подробнее...</div>"; else print stripslashes((  str_replace("\r","<br />", str::get_substr_clean($row->sdesc, $reg['adcat_contentmaxlength_intro']))  )). "<div class='back_button'>подробнее...</div>"; ?></span><br /><?
						?><br /><span class="adgood_ih2extrainfo">Тел.: <?=$row->f1 ?><br />Адрес: <?=$row->f2 ?><br />Стоимость: <? print num::flexprice($row->price1); ?> <? print rub1(); ?></span></td>
                  </tr>
				<tr valign="top" height="7"><td colspan="3"></td></tr>
				<tr valign="top" height="2"><td class="adline" colspan="3"></td></tr>
				<tr valign="top" height="7"><td colspan="3"></td></tr>
		<?
	}
	?></table><center><? 
	global $mosConfig_absolute_path;
	require_once( $mosConfig_absolute_path . '/includes/pageNavigation.php' );
	if (  $total>200  ){	
		$pageNav = new mosPageNav( $total, $limitstart, $limit  ); 
		/*
		 * УКАЗАНИЕ ДОПОЛНИТЕЛЬНЫХ ПАРАМЕТРОВ.
		 * Часто возникает необходимость при переходе по страницам передовать дополнительные параметры.
		 * Дополнительные параметры необходимо записать в массив sign
		 */
		$pageNav->sign['param1']='del_me_test';			// пример дополнительных параметров для поиска
		$pageNav->sign['param2']='it_is_demonstration';	// пример дополнительных параметров для поиска

		echo $pageNav->getListFooter(); 
	}
	?></center><?
	?><span class="article_seperator"> </span><div class="back_button" ><a href="javascript:history.go(-1)">Вернуться</a></div>	
	<?	global $option;	?>
	<input type="hidden" name="c" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	</form><?php 
}

function showexcomp($id){
global $reg;

	$cval = $_COOKIE["icsmart_ex_addcomp_all"];
//	$cval = substr($cval, 0, (strlen($cval)-1)  );
	$cvala = explode(",", $cval);
	$cgood = array();
	if (  count($cvala)==1  ){
		print "Список сравнения пуст";
		 
		return;
	}
	for ($i=0; $i<count($cvala)-1; $i++){
		$cgood[$i] = ggo($cvala[$i], "#__adgood");
	}
	$cwidth = round (  100/(count($cvala))  );

//	ggtr ($cvala);
	?><table width="100%" border="1" style="border:1px solid; border-collapse:collapse; border-color:#CCCCCC" ><?
		?><tr><?
			?><td width="<? print $cwidth; ?>%">Операции</td><?
			for ($i=0; $i<count($cvala)-1; $i++){
				?><td width="<? print $cwidth; ?>%" align="center" valign="middle" style="text-align:center; vertical-align:middle"><?
				if (  $i>0  ){ ?><a href="index.php?c=ad&task=excomp&id=<? print $id; ?>&icsmart_ex_leftcomp=<? print $cgood[$i]->id ?>"><img src="component/ad/aleft.gif" width="32" height="32" border="0" align="absmiddle"></a>&nbsp;&nbsp;&nbsp;<? }
				?><a class="ex_comp_del" href="index.php?c=ad&task=excomp&id=<? print $id; ?>&icsmart_ex_delcomp=<? print $cgood[$i]->id ?>">удалить</a><?
				if (  $i<(count($cvala)-2)  ){ ?><a href="index.php?c=ad&task=excomp&id=<? print $id; ?>&icsmart_ex_rightcomp=<? print $cgood[$i]->id ?>">&nbsp;&nbsp;&nbsp;<img src="component/ad/aright.gif" width="32" height="32" border="0" align="absmiddle"></a><? } ?></td><?
		}?></tr><?
		?><tr><?
			?><td>Название</td><?
			for ($i=0; $i<count($cvala)-1; $i++){
				?><td><? print ($cgood[$i]->name); ?></td><?
		}?></tr><?
		?><tr><?
			?><td>Фото</td><?
				for ($i=0; $i<count($cvala)-1; $i++){
				?><td nowrap="nowrap" valign="middle" align="center" class="foto_td" style=" text-align:center"><center><a href="<? print site_url.'/images/ad/good/'.$cgood[$i]->imagesorg; ?>" class="highslide" onclick="return hs.expand(this)" ><img style="border: 6px solid #ebe5dc; " src="<? print site_url.'/images/ad/good/'.$cgood[$i]->images; ?>" /></a></td><?
		}?></tr><?
		?><tr><?
			?><td>Стоимость</td><?
			for ($i=0; $i<count($cvala)-1; $i++){
				?><td><? print $cgood[$i]->price1; ?> <? print rub1(); ?></td><?
		}?></tr><?
		$ins_adgood_f = ggsql (" select * from #__adgood_f ");
		$ins_adgood_f_count = count ($ins_adgood_f); ?>
		<? for ($ii=1; $ii<=$ins_adgood_f_count; $ii++){ 
			?><tr><?
				?><td><? print $ins_adgood_f[$ii-1]->d; ?></td><?
				for ($i=0; $i<count($cvala)-1; $i++){
					$eval_str = '$cf = $cgood['.$i.']->f'.$ii.'; '; eval ($eval_str);
					?><td><? print ad_get_real_f($cf); ?></td><?
			}?></tr><?
		}
	?></table><?
	
	return;
}
function thank($id){
	global $reg;
	if (  $id>0  ) $icars = ggo ($id, "#__adcat");
	else{
		$icars->id = 0;
		$icars->name = $reg['ad_name'];
	}
	$adgfg = ggo (1, "#__excfg");
	print ($adgfg->thanku);
	
}



/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_icat_stat_icat($thisicat){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	$sitelog->f[1] = "adcat";
	$sitelog->f[2] = $thisicat->id;
	if (  $sitelog->isnewlog()  ){
		$sitelog->desc = $sitelog->desc = $sitelog->get_description($thisicat, "#__adcat", "parent", "/".$reg['ad_seoname'], $reg['ad_name'], $reg['ad_name'].", просмотр категории: ");
	}
	$sitelog->savelog();
}
function do_icat_stat_content($thiscontent){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	$sitelog->f[1] = "view";
	$sitelog->f[2] = $thiscontent->id;
	if (  $sitelog->isnewlog()  ){
		$thisicat = ggo($thiscontent->parent, "#__adcat");
		$sitelog->desc = $sitelog->get_description($thisicat, "#__adcat", "parent", "/".$reg['ad_seoname'], $reg['ad_name'], $reg['ad_name'].", просмотр объявления: ").$reg['global_static_delimiter'].stripslashes($thiscontent->name);
	}
	$sitelog->savelog();
}
?>