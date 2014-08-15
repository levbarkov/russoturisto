<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
require_once( site_path.'/component/ex/ex_lib.php' );
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();

function pack_set_attrib_vals_save(){
	global $reg;
	foreach ($_POST['id'] as $index=>$val_id){
		$i24r = new mosDBTable( "#__expack_attrib_val", "id", $reg['db'] );
		$i24r->id = $val_id;
		$i24r->val = $_POST['val'][$index];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store(); 
	}
	?>ins_ajax_load_target ('ca=expack&task=show_pack_set_attrib_vals&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); <?

}
function show_pack_set_attrib_vals(){
	global $reg;
	$all_sets = ggsql (  "select * from #__expack_attrib_val where parent=".$_REQUEST['parent']  );
	?><script language="javascript">
		var options_attrib_vals={		dataType:		'script',
										beforeSubmit:  function(){ $('#pack_select_list').html('<img src=/iadmin/images/loading.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
								};
					//
		$('#attrib_vals').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script>
	<form name="attrib_vals" id="attrib_vals" method="post" onsubmit=" $(this).ajaxSubmit(options_attrib_vals); return false; "><table cellpadding="4" cellspacing="0"><?
	if (  $_REQUEST['parent']!=0  ){
		$thecat = ggo($_REQUEST['parent'], "#__expack_attrib");
		?><tr><td colspan=2><img src="/iadmin/images/arrow_up.png"  border=0 align="absmiddle" />&nbsp;&nbsp;<a href="javascript: ins_ajax_load_target ('ca=expack&task=show_pack_set_attribs&parent=<?=$thecat->parent ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);">Вернуться назад</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
			?></td>
		</tr><?
	} else {
		$thecat->name="Корневая";
		$thecat->id=0;
		$thecat->parent=0;
	}
	foreach ($all_sets as $recgood){
		?><tr class="rowajax">
			<td><input style="width:400px" value="<?=$recgood->val ?>" name="val[]"  id="val_<?=$recgood->val ?>" /><input type="hidden" name="id[]"  value="<?=$recgood->id ?>" /></td>
			<td>&nbsp;&nbsp;&nbsp;<a href="javascript: ins_ajax_load_target ('ca=expack&task=pack_set_attrib_val_del&id=<?=$recgood->id ?>&parent=<?=$recgood->parent ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);"><img src="/iadmin/images/del.png"  border=0 /></a><?
			?></td>
		</tr><?
	}
	?><tr class="rowajax">
		<td valign="middle" style="vertical-align:middle" colspan="2"><img src="/iadmin/images/ins.png"  border=0 align="absmiddle" width="16" height="16" />&nbsp;&nbsp;<a href="javascript: ins_ajax_load_target ('ca=expack&task=new_pack_set_attrib_val&parent=<?=$thecat->id ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);">добавить новое значение</a></td>
	</tr>
	
	<tr height="16"><td colspan="2">&nbsp;</td>
	<tr height="16"><td colspan="2"><input type="submit" value="Сохранить значения (Enter)" /></td><?
	?><tr height="16"><td colspan="2">&nbsp;</td>
	<tr>
		<td colspan="2" ><img src="/iadmin/images/application_xp_terminal.png"  border=0 align="absmiddle" />&nbsp;&nbsp;Характеристика: <?=just_del_quotes($thecat->name) ?></td>
	</tr><?
	
	?></table>
	<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
	<input type="hidden" name="good" value="<?=ggri('good') ?>" />
        <input type="hidden" name="excat" value="<?=ggri('excat') ?>" />
	<input type="hidden" name="parent" value="<?=$_REQUEST['parent'] ?>" />
	<input type="hidden" name="4ajax" value="1" />
	<input type="hidden" name="task" value="pack_set_attrib_vals_save" />
	</form><?
}
function show_pack_set_attribs(){
	global $reg;
	$all_sets = ggsql (  "select * from #__expack_attrib where parent=".$_REQUEST['parent']  );
	?><table cellpadding="4" cellspacing="0"><?
	if (  $_REQUEST['parent']!=0  ){
		$thecat = ggo($_REQUEST['parent'], "#__expack_set");
		?><tr>	<td width="1%"><img src="/iadmin/images/arrow_up.png"  border=0 /></td>
				<td colspan="2" width="99%"><a href="javascript: ins_ajax_load_target ('ca=expack&task=show_pack_set&parent=<?=$thecat->parent ?>&good=<?=ggri('good') ?>&excat=<?=ggri('excat') ?>&4ajax=1', '#pack_select_list'); void(0);">Вернуться назад</a></td></tr><?
	} else {
		$thecat->name="Корневая";
		$thecat->id=0;
		$thecat->parent=0;
	}
	foreach ($all_sets as $recgood){
		?><tr class="rowajax"><td width="16"><img src="/iadmin/images/attribtypes/<?=$recgood->type ?>.png"  border=0 /></td>
		<td><a href="javascript: ins_ajax_load_target ('ca=expack&task=show_pack_set_attrib_vals&parent=<?=$recgood->id ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);" ><?=just_del_quotes($recgood->name) ?></a></td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
			?><a href="javascript: ins_ajax_load_target ('ca=expack&task=pack_set_attrib_del&id=<?=$recgood->id ?>&parent=<?=$recgood->parent ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);"><img src="/iadmin/images/del.png"  border=0 /></a>
			<a href="#" class="update-filter" rel="<?=$recgood->id ?>" data="<?=$recgood->filter ?>" title="<?= ($recgood->filter == 1) ? 'Исключить из фильра товаров' : 'Включить в фильтр товаров' ?>"><img src="/iadmin/images/search_<?= ($recgood->filter == 1) ? 'on' : 'off' ?>.png"  border=0 /></a>
		</td>
		</tr><?
	}
	?><tr class="rowajax">
		<td><img src="/iadmin/images/ins.png"  border=0 align="absmiddle" /></td>
		<td nowrap="nowrap"><a href="javascript: ins_ajax_load_target ('ca=expack&task=new_pack_set_attrib&parent=<?=$thecat->id ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);">добавить новую характеристику</a></td>
		<td></td>
	</tr><?

	?><tr height="16"><td colspan="3">&nbsp;</td>
	<tr><td><img src="/iadmin/images/application_xp_terminal.png"  border=0 /></td>
		<td colspan="2">Группа характеристик: <?=just_del_quotes($thecat->name) ?></td>
	</tr>	
	</table>
  <script type="text/javascript">
	$(document).ready(function(){
		$('.update-filter').live('click', function () {
			var el = this;
			var data = parseInt($(el).attr('data')),
				rel = parseInt($(el).attr('rel')),
				txt = 'Включить в фильтр товаров',
				img = '/iadmin/images/search_off.png';
			if(data == 0) {
				data = 1;
				txt = 'Исключить из фильра товаров';
				img = '/iadmin/images/search_on.png';
			}
			else {
				data = 0;
			}
		
			$.ajax({
				url:'/iadmin/index2.php?ca=expack&task=pack_set_attrib_upd&id='+rel+'&filter='+data+'&4ajax=1',
				success: function(){
					$(el).attr('data', data);
					$(el).attr('title', txt);
					$('img', el).attr('src', img);					
				}
			});
			
			return false;
		});
	})
  </script>
  
  <?
}

function pack_set_attrib_upd() {
    global $reg;
    $id = (int)$_REQUEST['id'];
    $filter = (int)$_REQUEST['filter'];
    ggsqlq("UPDATE #__expack_attrib SET `filter` = {$filter} WHERE id = {$id}");
}

function pack_set_del(){
	pack_set_del_recourse( $_REQUEST['id'] );
	?><script language="javascript">
		ins_ajax_load_target ('ca=expack&task=show_pack_set&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list');
	</script><?	
}
function pack_set_del_recourse($id){
	$expack_sets = ggsql  (  "SELECT * FROM #__expack_set WHERE parent=".$id."; ");
	if (  count($expack_sets)>0  ){	
		foreach ($expack_sets as $expack_set)	 pack_set_del_recourse( $expack_set->id );
	}	
	lib_pack_set_del($id);
}
function lib_pack_set_del($id){
	global $reg;
	$expack_attribs = ggsql  (  "SELECT * FROM #__expack_attrib WHERE parent=".$id."; ");
	if (  count($expack_attribs)>0  ){	
		foreach ($expack_attribs as $expack_attrib)	 lib_pack_set_attrib_del( $expack_attrib->id );
	}
	ggsqlq("DELETE FROM #__expack_set WHERE id = ".$id."  ");

}
function lib_pack_set_attrib_del($id){
	ggsqlq("DELETE FROM #__expack_attrib WHERE id = ".$id."  ");
	// теперь необходимо удалить все значения для этой характеристики
	ggsqlq("DELETE FROM #__expack_attrib_val WHERE parent = ".$id."  ");
}

function pack_set_attrib_del(){
	global $reg;
	lib_pack_set_attrib_del( $_REQUEST['id'] );
	
	?><script language="javascript">
		ins_ajax_load_target ('ca=expack&task=show_pack_set_attribs&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list');
	</script><?
}
function pack_set_attrib_val_del(){
	global $reg;

	$reg['db']->setQuery("DELETE FROM #__expack_attrib_val WHERE id = ".$_REQUEST['id']."  ");
	$reg['db']->query();
	?><script language="javascript">
		ins_ajax_load_target ('ca=expack&task=show_pack_set_attrib_vals&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list');
	</script><?
}
/**
 * ПЕРЕИМЕНОВАТЬ ГРУППУ И ВЕРНУТЬСЯ К СПИСКУ ГРУПП ХАРАКТЕРИСТИК
 */
function rename_pack_set_create(){
	global $reg;
	if (  ggri('id')>0  ){
            $i24r = new mosDBTable( "#__expack_set", "id", $reg['db'] );
            $i24r->id = ggri('id');
            $i24r->name = $_REQUEST['name'];
            if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
        }
        ?><script language="javascript"><?
            ?>ins_ajax_load_target ('ca=expack&task=show_pack_set&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list');<?
        ?></script><?
}

function new_pack_set_create(){
	global $reg;
	
	$i24r = new mosDBTable( "#__expack_set", "id", $reg['db'] );
	$i24r->id = 0;
	$i24r->parent = $_REQUEST['parent'];
	$i24r->name = $_REQUEST['name'];
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

//	print $_REQUEST['parent'];
//	print $_REQUEST['good'];
	print "Папка ".$_REQUEST['name']." создана";
	?><br /><br /><a href="javascript: ins_ajax_load_target ('ca=expack&task=show_pack_set&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);">Перейти к просмотру</a><?
}
function new_pack_set_attrib_create(){
	global $reg;
	
	$i24r = new mosDBTable( "#__expack_attrib", "id", $reg['db'] );
	$i24r->id = 0;
	$i24r->parent = $_REQUEST['parent'];
	$i24r->name = $_REQUEST['name'];
	$i24r->type = $_REQUEST['type'];
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	
	?><script language="javascript">
		ins_ajax_load_target ('ca=expack&task=show_pack_set_attribs&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list');
	</script><?
}
function new_pack_set_attrib_val(){
	global $reg;
	
	$i24r = new mosDBTable( "#__expack_attrib_val", "id", $reg['db'] );
	$i24r->id = 0;
	$i24r->parent = $_REQUEST['parent'];
	$i24r->val = "";
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	
	?><script language="javascript">
		ins_ajax_load_target ('ca=expack&task=show_pack_set_attrib_vals&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list');
	</script><?
}


function new_pack_set(){
	global $reg;
	?><script language="javascript">
		var new_pack_set_vals={	target:       '#pack_select_list',
					beforeSubmit:   function(){ $('#pack_select_list').html('<img src=/iadmin/images/loading.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
                                      };

	</script>
	<form name="new_pack_set"  onsubmit=" $(this).ajaxSubmit(new_pack_set_vals); return false; "  >
            <input name="name" type="text"  />&nbsp;<input type="submit" value="Cоздать новую папку" >
            <input type="hidden" name="good" value="<?=ggri('good') ?>">
            <input type="hidden" name="excat" value="<?=ggri('excat') ?>">
            <input type="hidden" name="4ajax" value="1">
            <input type="hidden" name="ca" value="expack">
            <input type="hidden" name="parent" value="<?=$_REQUEST['parent'] ?>">
            <input type="hidden" name="task" value="new_pack_set_create">
	</form><?
}

/**
 * ПЕРЕИМЕНОВАТЬ ГРУППУ ХАРАКТЕРИСТИК
 */
function rename_pack_set(){
	global $reg;
        if (  ggri('id')>0  )  $expack_set = ggo (ggri('id'), "#__expack_set");

	?><script language="javascript">
		var rename_pack_set_vals={	target:       '#pack_select_list',
                                                beforeSubmit:   function(){ $('#pack_select_list').html('<img src=/iadmin/images/loading.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
                                      };

	</script>
	<form name="new_pack_set"  onsubmit=" $(this).ajaxSubmit(rename_pack_set_vals); return false; "  >
            <input name="name" value="<?=$expack_set->name ?>" type="text"  />&nbsp;<input type="submit" value="Сохранить" >
            <input type="hidden" name="id" value="<?=ggri('id') ?>">
            <input type="hidden" name="good" value="<?=ggri('good') ?>">
            <input type="hidden" name="excat" value="<?=ggri('excat') ?>">
            <input type="hidden" name="4ajax" value="1">
            <input type="hidden" name="ca" value="expack">
            <input type="hidden" name="parent" value="<?=$_REQUEST['parent'] ?>">
            <input type="hidden" name="task" value="rename_pack_set_create">
	</form><?
}

function new_pack_set_attrib(){
	global $reg;
	?><script language="javascript">
		var new_pack_set_attrib_vals={	target:       '#pack_select_list',
					beforeSubmit:   function(){ $('#pack_select_list').html('<img src=/iadmin/images/loading.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
                                      };

	</script>
	<form name="new_pack_set_attrib"  onsubmit=" $(this).ajaxSubmit(new_pack_set_attrib_vals); return false; ">
		<input name="name" type="text"  /><select style="width:187px" name="type">
			<option value="4">выбор из раскрывающегося списка</option>
			<option value="1">ввод данный в текстовое поле</option>
			<option value="2">выбор checkbox</option>
			<option value="3">выбор radiobox</option>
                </select>&nbsp;<input type="submit" value="Cоздать новую характеристику" >
                <input type="hidden" name="good" value="<?=ggri('good') ?>">
                <input type="hidden" name="excat" value="<?=ggri('excat') ?>">
                <input type="hidden" name="4ajax" value="1">
                <input type="hidden" name="ca" value="expack">
                <input type="hidden" name="parent" value="<?=$_REQUEST['parent'] ?>">
                <input type="hidden" name="task" value="new_pack_set_attrib_create">
	</form><?
}
/**
 * СОХРАНЯЕМ ВЫБРАННУЮ ГРУППУ ХАРАКТЕРИСТИК В ТОВАРЕ ИЛИ КОМПЛЕКТАЦИИ И ЗАКРЫВАЕМ ОКНО
 */
function set_pack_set(){
	global $reg;
	$thecat = ggo($_REQUEST['id'], "#__expack_set");
        $div_id = 0;

        /*
         * СОХРАНЯЕМ ВЫБРАННУЮ ГРУППУ ХАРАКТЕРИСТИК В ТОВАРЕ ИЛИ КОМПЛЕКТАЦИИ
         */
        if (  ggri ('excat')>0  ){
            $i24r = new mosDBTable( "#__excat", "id", $reg['db'] );
            $i24r->id = $_REQUEST['excat'];
            $i24r->expack_set = $_REQUEST['id'];
            if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
            $div_id = $_REQUEST['excat'];
        } else if (  ggri ('good')>0  ){
            $i24r = new mosDBTable( "#__exgood", "id", $reg['db'] );
            $i24r->id = $_REQUEST['good'];
            $i24r->expack_set = $_REQUEST['id'];
            if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
            $div_id = $_REQUEST['good'];
            ggsqlq("update #__expack set expack_set = {$_REQUEST['id']} where parent = {$_REQUEST['good']}");
        }

        /*
         * ВЫВОДИМ В ТОВАРЕ, КАТЕГОРИИ ВЫБРАННУЮ ГРУППУ И ЗАКРЫВАЕМ ОКНО
         */
        ?><script language="javascript">
                $("#pack_set_name<?=$div_id ?>").html ('<?=just_del_quotes($thecat->name) ?>');
                $("#pack_set_val").val ('<?=just_del_quotes($thecat->id) ?>');
                $.fn.colorbox.close();
        </script><?
}
function show_pack_set(){
	global $reg;
	$all_sets = ggsql (  "select * from #__expack_set where parent=".$_REQUEST['parent']  );
	?><table cellpadding="4" cellspacing="0"><?
	if (  $_REQUEST['parent']!=0  ){
		$thecat = ggo($_REQUEST['parent'], "#__expack_set");
		?><tr><td width=16><img src="/iadmin/images/arrow_up.png"  border=0 /><td colspan=2><a href="javascript: ins_ajax_load_target ('ca=expack&task=show_pack_set&parent=<?=$thecat->parent ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);">Вернуться назад</a></td><td></td></tr><?
	} else {
		$thecat->name="Корневая";
		$thecat->id=0;
		$thecat->parent=0;
	}
	foreach ($all_sets as $recgood){
		$how_many_child = ggsqlr(  "select count(id) from #__expack_set where parent=".$recgood->id  );
		?><tr class="rowajax"><td width="16"><?
			if (  $how_many_child  ) { ?><img src="/iadmin/images/folder.png"  border=0 /><? }
		?></td><td><a href="<?
			if (  1  ){ ?>javascript: ins_ajax_load_target ('ca=expack&task=show_pack_set&parent=<?=$recgood->id ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);<? }
			else { ?><? } ?>" ><?=just_del_quotes($recgood->name) ?></a></td>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript: if(  confirm('Удалить группу <?=just_del_quotes($recgood->name) ?>')  )  ins_ajax_load_target ('ca=expack&task=pack_set_del&id=<?=$recgood->id ?>&parent=<?=$recgood->parent ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0); " title="Удалить"><img src="/iadmin/images/folder_delete.png" width="16" height="16" border=0 /></a>&nbsp;&nbsp;<?
                        ?><a href="javascript: ins_ajax_load_target ('ca=expack&task=rename_pack_set&id=<?=$recgood->id ?>&parent=<?=$_REQUEST['parent'] ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);" title="Переименовать"><img src="/iadmin/images/folder_edit.png" width="16" height="16" border=0 /></a>&nbsp;&nbsp;<?
			?><a href="javascript: ins_ajax_load_target ('ca=expack&task=show_pack_set_attribs&parent=<?=$recgood->id ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);" title="Редактировать свойства"><img src="/iadmin/images/properties.png" width="16" height="16" border=0 /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
			?><a href="javascript: ins_ajax_load_target ('ca=expack&task=set_pack_set&id=<?=$recgood->id ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);"><img src="/iadmin/images/select.png" width="16" height="16"  border=0 /></a></td>
		</tr><?
	}
	?><tr class="rowajax">
		<td><img src="/iadmin/images/folder_add.png"  border=0 /></td>
		<td colspan="2"><a href="javascript: ins_ajax_load_target ('ca=expack&task=new_pack_set&parent=<?=$thecat->id ?>&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1', '#pack_select_list'); void(0);">Создать новую папку</a></td>
	</tr><?
	
	?><tr height="16"><td colspan="3">&nbsp;</td>
	<tr>
		<td><img src="/iadmin/images/application_xp_terminal.png"  border=0 /></td>
		<td colspan="2">Текущая папка: <?=just_del_quotes($thecat->name) ?></td>
	</tr><?
	
	?></table><?
}

function select_pack_set(){
	global $reg;
	?><div id="pack_select_list"></div>
	<script language="javascript">
		ins_ajax_load_target ("ca=expack&task=show_pack_set&parent=0&excat=<?=ggri('excat') ?>&good=<?=ggri('good') ?>&4ajax=1", "#pack_select_list");
	</script><?
}
function del_expack(){
	global $reg;
	$expack = new expack();
	$expack->delete_expack( $_REQUEST['id'] );
	?>ins_ajax_load_target ('ca=expack&task=show_packs&good=<?=$_REQUEST['good'] ?>&4ajax=1', '#all_packs'); <?
}

function show_packs(){
	$all_cy_ggsql = ggsql (  "select * from #__exprice_cy "  );
	$all_cy = libarray::convert_ggsql_object_to_array ($all_cy_ggsql); // ggtr ($all_pricies);
	foreach ($all_cy_ggsql as $ivcat_cy)	$vcats_cy[] = mosHTML::makeOption( $ivcat_cy->id, $ivcat_cy->name);

	$all_prices = ggsql (  "select * from #__exprice "  );
	$all_prices_cnt = count ($all_prices);
	
	$all_sklads = ggsql (  "select * from #__exsklad "  );
	$all_sklads_cnt = count ($all_sklads);
	$all_packs = ggsql (  "select * from #__expack where parent=".$_REQUEST['good']  );
	?><script language="javascript">
		var options_sklad_qty={		target:        '#all_packs',
							beforeSubmit:  function(){  $('.sklad_qty_tr').css("visibility","hidden"); 
														$('#sklad_qty_submit').html('<img src=/iadmin/images/loading16.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
					};
	</script>
	<form name="sklad_qty" id="sklad_qty" method="post" onsubmit=" $(this).ajaxSubmit(options_sklad_qty); return false; ">
	<table cellspacing="0" cellpadding="4" id="sklad_qty_table"><?
	?><tr>
                <td>Связь с внеш. БД (1С)</td>
		<td>Артикул</td>
		<td>Комплектация</td>
		<? foreach ($all_prices as $price) { ?><td><?=$price->name ?></td><? } ?>
		<? foreach ($all_sklads as $sklad) { ?><td><?=$sklad->name ?></td><? } ?>
		<td></td>
	</tr><?
	?><tr height="1px" style=" background-image:url(/iadmin/images/b1px.png); background-repeat:repeat-x;"><td colspan="<?=(3+$all_sklads_cnt+$all_prices_cnt); ?>"></td></tr><?

	foreach ($all_packs as $recgood){
		$link = "javascript: ins_ajax_open ('?ca=expack&task=editpack&expack=".$recgood->id."&good=".$_REQUEST['good']."&attrib_set='+$('#pack_set_val').val()+'&4ajax=1',570,570); void(0); ";
		?><tr class="rowajax2 sklad_qty_tr">
                        <td><a href="<?=$link ?>"><?=$recgood->connect ?></a></td>
			<td><a href="<?=$link ?>"><?=$recgood->sku ?></a></td>
			<td><a href="<?=$link ?>"><?=$recgood->name ?></a></td>
			<? foreach ($all_prices as $price) { ?><td><input type="text" name="price_cell[<?=$recgood->id ?>][<?=$price->id ?>]" value="<?
				// определяем количество
				$qty = ggsql ( "SELECT * FROM #__exprice_good WHERE parent=".$price->id."  AND expack=".$recgood->id );
				if (  $qty[0]->val==''  ) print "";
				else print($qty[0]->val);
			 ?>" style="width:70px; "  /><? print mosHTML::selectList( $vcats_cy, 'cy['.$recgood->id.']['.$price->id.']', 'class="inputbox" style="border:0px none; "  size="1" ', 'value', 'text', $qty[0]->cy ); ?></td><? } ?>

			<? foreach ($all_sklads as $sklad) { ?><td><input type="text" name="valik[<?=$recgood->id ?>][<?=$sklad->id ?>]" value="<?
				// определяем количество
				$qty = ggsql ( "SELECT * FROM #__exsklad_good WHERE parent=".$sklad->id."  AND expack=".$recgood->id );
				if (  !$qty[0]->val  ) print 0;
				else print($qty[0]->val);
			 ?>" style="width:43px; "  /></td><? } ?>
			<td><a href="javascript: ins_ajax_load ('ca=expack&task=del_expack&id=<?=$recgood->id; ?>&good=<?=$recgood->parent ?>&4ajax=1'); void(0);"><img src="/iadmin/images/del.png" width="16" height="16"  border=0 /></a></td>
		</tr><?
	}
	?><tr><td colspan="2"></td><td colspan="<?=(2+$all_sklads_cnt+$all_prices_cnt); ?>"><span id="sklad_qty_submit"><input type="submit" value="Сохранить остатки и стоимость (Enter)" /></span></td></tr><?
	?><tr height="1px" style=" background-image:url(/iadmin/images/b1px.png); background-repeat:repeat-x; background-position:bottom;"><td colspan="<?=(3+$all_sklads_cnt+$all_prices_cnt); ?>"></td></tr><?
	?></table>
	<input type="hidden" name="ca" value="expack" />
	<input type="hidden" name="good" value="<?=$_REQUEST['good'] ?>" />
	<input type="hidden" name="task" value="save_qty" />
	<input type="hidden" name="4ajax" value="1" />
	</form>
	<?
}
function save_qty(){
	foreach ($_POST['price_cell'] as $expack_id => $prices ){
		foreach ($prices as $sklad_id => $price_val){
			$i24r->parent = $sklad_id;
			$i24r->expack = $expack_id;
			$i24r->val = str_replace( ",", ".", $price_val );
			$i24r->cy = $_POST['cy'][$expack_id][$sklad_id];
                        if (  $i24r->val==''  ) $i24r->val = 0;
                        if (  $i24r->cy==''  ) $i24r->cy = 1;
			$expack = new expack();
			$expack->save_price_good ($i24r);
		}
	}
	foreach ($_POST['valik'] as $expack_id => $sklads ){
		foreach ($sklads as $sklad_id => $sklad_qty){
			$i24r->parent = $sklad_id;
			$i24r->expack = $expack_id;
			$i24r->val = $sklad_qty;
                        if (  $i24r->val==''  ) $i24r->val = 0;
			$expack = new expack();
			$expack->save_sklad_good ($i24r);			
		}
	}
	show_packs();
}
function editpack( ) {
	global $reg;
	$this_expack = ggo ($_REQUEST['expack'], "#__expack");
	
	$pack_set = ggo ( $this_expack->expack_set, "#__expack_set" );
	$all_attribs = ggsql (  "select * from #__expack_attrib where parent=".$_REQUEST['attrib_set']  );	
	/*[0] => stdClass Object
        (
            [id] => 15
            [parent] => 225
            [name] => Цвет
            [type] => 4
        )

    [1] => stdClass Object
        (
            [id] => 16
            [parent] => 225
            [name] => Память
            [type] => 4
        )
*/
/*////	if (  $this_expack->xml!=''  ){
////		$xml = simplexml_load_string(  '<?xml version="1.0" encoding="utf-8"?>'.$this_expack->xml  );
////		$data = get_object_vars($xml);
////	} */
	// ggd (  $data  );

	?><div  id="mypack_debug">Группа характеристик - <?=$pack_set->name ?></div>
	<form name="make_newpack" method="post" id="make_newpack" >
	<table cellpadding="4" cellspacing="0">
		<tr><td colspan="2">&nbsp;</td></tr>		
		<tr>
			<td>Наименование: </td>
			<td><input type="text" value="<?=$this_expack->name ?>" style="width:270px; " id="expack_name" name="name" /></td>
		</tr>		
		<tr><td colspan="2">&nbsp;</td></tr>		
		<tr class="rowajax">
			<td>Артикул: </td>
			<td><input type="text" value="<?=$this_expack->sku ?>" style="width:270px; " id="expack_sku" name="sku" /></td>
		</tr>
                <tr class="rowajax">
			<td>Поле для связи<br>с внешней БД (1С): </td>
			<td><input type="text" value="<?=$this_expack->connect ?>" style="width:270px; " id="expack_connect" name="connect" /></td>
		</tr><?
		//$all_attribs = ggsql (  "select * from #__expack_attrib where parent=".$_REQUEST['attrib_set']  );
		foreach ($all_attribs as $attrib){ ?>
			<tr class="rowajax">
				<td><?=$attrib->name ?><input type="hidden" name="attrib_id[]" value="<?=$attrib->id ?>" /></td>
				<td><?
					$ivcats = ggsql ( "SELECT * FROM #__expack_attrib_val where parent=$attrib->id ORDER BY val ASC " );
					$vcats = array(); $vcats[0] = mosHTML::makeOption( 0, "&nbsp;");
					foreach ($ivcats as $ivcat)	$vcats[] = mosHTML::makeOption( $ivcat->id, $ivcat->val);
					// определяем какое знаечние выбранно
					$attrib_val = ggsql (  " SELECT * FROM #__expack_set_val WHERE pack_id=".ggri('expack')." AND attrib=".$attrib->id." ; " );
					print mosHTML::selectList( $vcats, 'attrib_val[]', 'class="inputbox" auto_name="1" onchange=" expack_autoname(); " size="1"  ', 'value', 'text', $attrib_val[0]->attrib_val );
					echo ' или <input type="text" name="attrib_uval[]" value="" />';
					unset($vcats);
				?></td>
			</tr>
		<? }
		?>
		<tr class="rowajax">
			<td>Единица измерения:</td>
			<td><?
				$ivcats = ggsql ( "SELECT * FROM #__exgood_unit where parent=".$_REQUEST['good']." " );
				$vcats = array();
				foreach ($ivcats as $ivcat)	$vcats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);
				print mosHTML::selectList( $vcats, 'unit', 'class="inputbox" auto_name="end" onchange=" expack_autoname(); " size="1"  ', 'value', 'text', $this_expack->unit );
				unset($vcats); 
			?></td>
		</tr>

		<tr>
			<td></td>
			<td><input type="submit" value="Сохранить" class="button" /></td>
		</tr>		
	</table>
	<input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
	<input type='hidden' name='task' value='addpack' />
	<input type='hidden' name='edit_expack' value='<?=$_REQUEST['expack'] ?>' />
	<input type='hidden' name='good' value='<?=$_REQUEST['good'] ?>' />
	<input type="hidden" name="attrib_set" value="<?=$this_expack->expack_set ?>" />
	<input type="hidden" name="4ajax" value="1" />
	</form>
	<script language="javascript">
		var options = {		dataType:		'script'		};
		$('#make_newpack').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script>
	<?
}

function addpack( ) {
	global $reg;
	if (  isset ($_REQUEST['edit_expack'])  )	$this_expack = ggo ($_REQUEST['edit_expack'], "#__expack");
	else {
		$this_expack->id=0;
	}

        $expack = new expack();
        $expack->vars->id         = $this_expack->id;  // 0 добавить новую, если >0 - то изменить существующую
        $expack->vars->sku        = $_REQUEST['sku'];
        $expack->vars->connect    = $_REQUEST['connect']; // поле для связи с 1С и поэтому ОБЯЗАТЕЛЬНОЕ для заполнения !!!
        $expack->vars->name       = $_REQUEST['name'];
        $expack->vars->parent     = $_REQUEST['good'];  // id в #__exgood ( сам товар )
        $expack->vars->expack_set = $_REQUEST['attrib_set'];//используем группы характеристик с id=xxx (#___expack_set),
                                                            // если характеристики товара не используется, то =0
        $expack->vars->unit       = $_REQUEST['unit'];   // id в #__exgood_unit ( единицы измерения )

        $expack_set_val = array();
		if (count($_REQUEST['attrib_id']) > 0){
			foreach ($_REQUEST['attrib_id'] as $index => $attrib_id){
				$value_id = $_REQUEST['attrib_val'][$index];
				# Расширяем возможности
				if(mb_strlen($_REQUEST['attrib_uval'][$index], 'utf-8')){
					$value = trim($_REQUEST['attrib_uval'][$index]);
					$is_value = ggsqlr("select `id` from #__expack_attrib_val where `parent` = {$attrib_id} and `val` = '{$value}' limit 1");
					if($is_value)
						$value_id = $is_value;
					else{
						$attribute = new mosDBTable("#__expack_attrib_val", "id", $reg['db']);
						$attribute->id = 0;
						$attribute->parent = $attrib_id;
						$attribute->val = $value;
						$attribute->store();
						
						$value_id = mysql_insert_id($reg['db']->_resource);						
					}
				}
				
				$expack_set_val[$index]->attrib = $attrib_id;                           //свойство, например "Память" ( #__expack_attrib )
				$expack_set_val[$index]->attrib_val = $value_id;  //значение, например "4 Gb"   ( #__expack_attrib_val )
			}
		}
			
		$expack->expack_set_val = &$expack_set_val;

        // указание стоимости и остатков для комплектации
        $expack->sklad   = array(  );  // не задаем остатки
        $expack->price   = array(  );  // не задаем стоимость
        $expack->cy      = array(  );  // не задаем валюту

        $expack->saveme();

	?>$('#all_packs').html('<img src="/iadmin/images/loading.gif" width="32" height="32" />');
	<? if ( !isset ($_REQUEST['edit_expack']) ){ ?>$('#mypack_debug').html('<img src="/iadmin/images/loading16.gif" width="16" height="16" align="absmiddle" /> Сохранение...');	<? } ?>
	ins_ajax_load_target ("ca=expack&task=show_packs&good=<?=$_REQUEST['good'] ?>&4ajax=1", "#all_packs");
	<? if ( !isset ($_REQUEST['edit_expack']) ){ ?>$('#mypack_debug').html('Комплектация: «<?=just_del_quotes($_POST['name']) ?>» сохранена');		<? } ?>
	<? if ( isset ($_REQUEST['edit_expack']) ){ ?>$.fn.colorbox.close(); <? } ?>
	<?
}
/**
 * показать значения атрибута, при нажатии кнопка отмена при задании атрибута
 * @global <type> $reg
 */
function show_attrib_vals(){
    global $reg;
    $attrib_id = ggri('attrib_id');
    $ivcats = ggsql (  "select * from #__expack_attrib_val where parent=".$attrib_id." ORDER BY val ASC "  );

    $vcats = array(); $vcats[0] = mosHTML::makeOption( 0, "&nbsp;");
    foreach ($ivcats as $ivcat)	$vcats[] = mosHTML::makeOption( $ivcat->id, $ivcat->val);
    $vcats[] = mosHTML::makeOption( -1, '>> ДРУГОЕ');

    print mosHTML::selectList( $vcats, 'attrib_val[]', 'class="inputbox" type_autoname="select" do_autoname="1" auto_name="1" onchange=" if (this.value==-1) ins_ajax_load_target (\'ca=expack&task=new_pack_attrib_val&attrib_id='.$attrib_id.'&4ajax=1\', \'#arrib_div_'.$attrib_id.'\');               else expack_autoname(); " size="1"  ', 'value', 'text' );
    unset($vcats);
    ?><script language="javascript">
        $('#attrib_new_<?=$attrib_id ?>').val(0);
        expack_autoname();
    </script><?

}
function new_pack_attrib_val( ) {
    global $reg;
    $attrib_id = ggri('attrib_id');
    $last_attrib = ggsql (  "select * from #__expack_attrib_val where parent=".$attrib_id." ORDER BY id DESC limit 0,1 "  );
    ?><input name="attrib_val[]" value="<?=$last_attrib[0]->val ?>" type_autoname="input" do_autoname="1" auto_name="1" onkeyup=" expack_autoname(); " style="width: 230px; " >&nbsp;<a href="javascript: ins_ajax_load_target ('ca=expack&task=show_attrib_vals&attrib_id=<?=$attrib_id ?>&4ajax=1', '#arrib_div_<?=$attrib_id ?>');  void(0); ">отмена</a>
    <script language="javascript">
        $('#attrib_new_<?=$attrib_id ?>').val(1);
        expack_autoname();
    </script><?
}
function newpack( ) {
	global $reg;
	$pack_set = ggo ( $_REQUEST['attrib_set'], "#__expack_set" );
	$all_attribs = ggsql (  "select * from #__expack_attrib where parent=" . $_REQUEST['attrib_set']  );
	?><div  id="mypack_debug">Группа характеристик - <?=$pack_set->name ?></div>
	<form name="make_newpack" method="post" id="make_newpack" >
	<table cellpadding="4" cellspacing="0">
		<tr><td colspan="2">&nbsp;</td></tr>		
		<tr>
			<td>Наименование: </td>
			<td><input type="text" value="" style="width:270px; " id="expack_name" name="name"  /></td>
		</tr>		
		<tr><td colspan="2">&nbsp;</td></tr>		
		<tr class="rowajax">
			<td>Артикул: </td>
			<td><input type="text" value="<?=$this_expack->sku ?>" style="width:270px; " id="expack_sku" name="sku" /></td>
		</tr>
                <tr class="rowajax">
			<td>Поле для связи<br>с внешней БД (1С): </td>
			<td><input type="text" value="" style="width:270px; " id="expack_connect" name="connect" /></td>
		</tr>
		<?
		if(count($all_attribs)){
		foreach ($all_attribs as $attrib){
			?>
			<tr class="rowajax">
				<td><?=$attrib->name ?><input type="hidden" name="attrib_id[]" value="<?=$attrib->id ?>" /></td>
				<td><?
					$ivcats = ggsql ( "SELECT * FROM #__expack_attrib_val where parent=$attrib->id ORDER BY val ASC " );
					$vcats = array(); $vcats[0] = mosHTML::makeOption( 0, "&nbsp;");
					foreach ($ivcats as $ivcat)
						$vcats[] = mosHTML::makeOption( $ivcat->id, $ivcat->val);
                    $vcats[] = mosHTML::makeOption( -1, '>> ДРУГОЕ');
                    ?>
					<input type="hidden" value="0" name="attrib_new[]" id="attrib_new_<?=$attrib->id ?>">
					<div id="arrib_div_<?=$attrib->id ?>">
					<?
					print mosHTML::selectList( $vcats, 'attrib_val[]', 'class="inputbox" type_autoname="select" do_autoname="1" auto_name="1" onchange=" if (this.value==-1) ins_ajax_load_target (\'ca=expack&task=new_pack_attrib_val&attrib_id='.$attrib->id.'&4ajax=1\', \'#arrib_div_'.$attrib->id.'\');               else expack_autoname(); " size="1"  ', 'value', 'text' );
                    echo ' или <input type="text" name="attrib_uval[]" value="" />';
					?></div><?
					unset($vcats);
				?></td>
			</tr>
		<? } }?>
		<tr class="rowajax">
			<td>Единица измерения:</td>
			<td><?
				$ivcats = ggsql ( "SELECT * FROM #__exgood_unit where parent=".$_REQUEST['good']." " );
				$vcats = array();
				foreach ($ivcats as $ivcat)	$vcats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);
				print mosHTML::selectList( $vcats, 'unit', 'class="inputbox" type_autoname="select" do_autoname="1" auto_name="end" onchange=" expack_autoname(); " size="1"  ', 'value', 'text' );
				unset($vcats); 
			?></td>
		</tr>

		<tr>
			<td></td>
			<td><input type="submit" value="Добавить" class="button" /></td>
		</tr>		
	</table>
	<input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
	<input type='hidden' name='task' value='addpack' />
	<input type='hidden' name='good' value='<?=$_REQUEST['good'] ?>' />
	<input type="hidden" name="attrib_set" value="<?=$_REQUEST['attrib_set'] ?>" />
	<input type="hidden" name="4ajax" value="1" />
	</form>
	
	
	<script language="javascript">
		var options = {		dataType:		'script'		};
		$('#make_newpack').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script>
	<?
}
?>