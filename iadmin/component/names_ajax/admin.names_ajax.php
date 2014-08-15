<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();

function shownames(){
	global $reg;
	?><div id="names_select_list"></div>
	<script language="javascript">
		ins_ajax_load_target ("ca=names_ajax&task=show_names_folder&icsmart_namesparent=<?=icsmarti('icsmart_namesparent') ?>&4ajax=1", "#names_select_list");
	</script><?
}

function showbrands(){
	global $reg;
	?><div id="names_select_list"></div>
	<script language="javascript">
		ins_ajax_load_target ("ca=names_ajax&task=show_brands_folder&4ajax=1", "#names_select_list");
	</script><?
}

function show_brands_folder(){
	global $reg;
	$all_sets = ggsql (  "select * from #__names_prop where id=".$reg['id_brand_names_prop']." ; "  );
	?><table cellpadding="4" cellspacing="0"><?
	foreach ($all_sets as $recgood){
		?><tr class="rowajax"><td width="16"><?
			?><img src="/iadmin/images/folder.png"  border=0 /><?
                    ?></td><td><?=just_del_quotes($recgood->name) ?></td><td></td>
		</tr><?
		
			$all_names = ggsql (  "select * from #__names where propid=".$recgood->id  );
			//ggr ($all_names);
			if (  count ($all_names)  )
			foreach ( $all_names as $name1){
				?><tr class="rowajax"><td width="16"></td><?
				?><td><a href="<?
					if (  1  ){ ?>javascript: $('#all_brand').val( '<?=$name1->innername ?>' ); $.fn.colorbox.close(); void(0);<? }
					else { ?><? } ?>" ><?=just_del_quotes($name1->innername) ?></a></td>
					<td><?
						?><a title="редактировать свойства" href="index2.php?ca=names&task=editA&id=<?=$name1->id ?>&hidemainmenu=1&search=&filter_type=&filter_logged=" target="_blank"><img src="/iadmin/images/properties01.png"  border=0 align="absmiddle" /></a>&nbsp;&nbsp;<?
						?><a href="javascript: ins_ajax_load ('ca=names_ajax&task=del_name_ajax&id=<?=$name1->id ?>&4ajax=1'); void(0);"><img src="/iadmin/images/del.png"  border=0 align="absmiddle" /></a><?
					?></td>
				</tr><?
			}
			?><tr class="rowajax">
				<td></td>
				<td id="namename"><img src="/iadmin/images/ins.png"  border=0 align="absmiddle" />&nbsp;&nbsp;<a href="javascript: ins_ajax_load_target ('ca=names_ajax&task=new_name_form&4ajax=1&propid=<?=$reg['id_brand_names_prop'] ?>', '#namename'); void(0);">Новое значение</a></td>
			</tr><?


	}

	?></table><?
}

function show_names_folder(){
	global $reg;
	$all_sets = ggsql (  "select * from #__names_prop"  );
	?><table cellpadding="4" cellspacing="0"><?
	if (  icsmarti('icsmart_namesparent')!=0  ){
		$thecat = ggo(icsmarti('icsmart_namesparent'), "#__names_prop");
	} else {
		$thecat->name="Корневая";
		$thecat->id=0;
		$thecat->parent=0;
	}
	foreach ($all_sets as $recgood){
		?><tr class="rowajax"><td width="16"><?
			?><img src="/iadmin/images/folder.png"  border=0 /><?
		?></td><td><a href="<?
			if (  1  ){ ?>javascript: ins_ajax_load_target ('ca=names_ajax&task=show_names_folder&icsmart_namesparent=<?=$recgood->id ?>&4ajax=1', '#names_select_list'); void(0);<? }
			else { ?><? } ?>" ><?=just_del_quotes($recgood->name) ?></a></td>
			<td><?
			?></td>
		</tr><?
		if (  icsmarti('icsmart_namesparent')==$recgood->id  ){
			$all_names = ggsql (  "select * from #__names where propid=".$recgood->id  );
			//ggr ($all_names);
			if (  count ($all_names)  )
			foreach ( $all_names as $name1){
				?><tr class="rowajax"><td width="16"></td><?
				?><td><a href="<?
					if (  1  ){ ?>javascript: $('#all_names').val( $('#all_names').val()+', <?=$name1->innername ?>' ); void(0);<? }
					else { ?><? } ?>" ><?=just_del_quotes($name1->innername) ?></a></td>
					<td><?
						?><a title="редактировать свойства" href="index2.php?ca=names&task=editA&id=<?=$name1->id ?>&hidemainmenu=1&search=&filter_type=&filter_logged=" target="_blank"><img src="/iadmin/images/properties01.png"  border=0 align="absmiddle" /></a>&nbsp;&nbsp;<?
						?><a href="javascript: ins_ajax_load ('ca=names_ajax&task=del_name_ajax&id=<?=$name1->id ?>&4ajax=1'); void(0);"><img src="/iadmin/images/del.png"  border=0 align="absmiddle" /></a><?
					?></td>
				</tr><?
			}
			?><tr class="rowajax">
				<td></td>
				<td id="namename"><img src="/iadmin/images/ins.png"  border=0 align="absmiddle" />&nbsp;&nbsp;<a href="javascript: ins_ajax_load_target ('ca=names_ajax&task=new_name_form&4ajax=1&propid=<?=icsmarti('icsmart_namesparent') ?>', '#namename'); void(0);">Новое значение</a></td>
			</tr><?
		}
		
	}
	?><tr class="rowajax">
		<td><img src="/iadmin/images/folder_add.png"  border=0 /></td>
		<td colspan="2" id="create_new_folder"><a href="javascript: ins_ajax_load_target ('ca=names_ajax&task=new_folder_form&4ajax=1', '#create_new_folder'); void(0);">Создать новую папку</a></td>
	</tr><?
	
	?><tr height="16"><td colspan="3">&nbsp;</td>
	<tr>
		<td><img src="/iadmin/images/application_xp_terminal.png"  border=0 /></td>
		<td colspan="2">Текущая папка: <?=just_del_quotes($thecat->name) ?></td>
	</tr><?
	
	?></table><?
}
function del_name_ajax(){
	global $reg;
	// del name
		// удаляем фото
		$dfgd = ggri('id');
		$component_foto = new component_foto ( 0 );
		$component_foto->init('names');
		$component_foto->parent = $dfgd;
		$component_foto->load_parent();
		$component_foto->del_fotos();

		$adminlog_obg = ggo($dfgd, "#__names");	$adminlog = new adminlog(); $adminlog->logme('del', $reg['names_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq (  "DELETE FROM #__names WHERE id=".safelySqlStr($dfgd)  );

        if (  $adminlog_obg->propid==$reg['id_brand_names_prop']  ){ // удаление только брендов поэтому на них и возвращаемся
                 ?>ins_ajax_load_target ("ca=names_ajax&task=show_brands_folder&4ajax=1", "#names_select_list");<?
        } else { ?>ins_ajax_load_target ("ca=names_ajax&task=show_names_folder&icsmart_namesparent=<?=icsmarti('icsmart_namesparent') ?>&4ajax=1", "#names_select_list");<? }
}
function new_name_form(){
		?><script language="javascript">
			var options_namecreate={	dataType:		'script',
										beforeSubmit:  function(){  if (  $('#newnamename').val()==''  ) { alert('Введите значение'); return false; }
																	$('#namename').html('<img src=/iadmin/images/loading16.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
									};
		</script>
		<form action="index2.php" onsubmit=" $(this).ajaxSubmit(options_namecreate); return false; " method="post" name="fcreate" id="fcreate"><?
		?><input name="name" id="newnamename" style="width:240px; " /><input type="submit" value="сохранить" /><?
		?><input type="hidden" name="task" value="name_edit" /><?
		?><input type="hidden" name="ca" value="names_ajax" /><?
		?><input type="hidden" name="4ajax" value="1" /><?
		?><input type="hidden" name="id" value="0" /><?
		?><input type="hidden" name="propid" value="<?=ggri('propid'); ?>" /><?
		?></form><?		
}
function name_edit(){
	global $database, $my, $reg;

	$i24r = new mosDBTable( "#__names", "id", $database );
	$i24r->id = ggri('id');
	$i24r->propid = ggri('propid');
	$i24r->name = $_REQUEST['name'];	//safelySqlStr
	$i24r->innername = $_REQUEST['name'];
	$i24r->publish = 1;
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__names ORDER BY #__names.ordering DESC LIMIT 0,1 ");
		$i24r->ordering = $iexmaxorder[0]->ordering+1;
	}
	if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else $i24r->store();

	
	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new', $reg['names_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save', $reg['names_name'], $i24r->name, $i24r->id );

        if (  ggri('propid')==$reg['id_brand_names_prop']  ){ // редактирование только брендов поэтому на них и возвращаемся
            ?>ins_ajax_load_target ("ca=names_ajax&task=show_brands_folder&4ajax=1", "#names_select_list");<?
        }
	else { ?>ins_ajax_load_target ("ca=names_ajax&task=show_names_folder&icsmart_namesparent=<?=icsmarti('icsmart_namesparent') ?>&4ajax=1", "#names_select_list");<? }
}

function new_folder_form(){
	?><script language="javascript">
		var options_fcreate={		dataType:		'script',
									beforeSubmit:  function(){  if (  $('#fname').val()==''  ) { alert('Введите имя категории'); return false; }
																$('#create_new_folder').html('<img src=/iadmin/images/loading16.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
								};
	</script>
	<form action="index2.php" onsubmit=" $(this).ajaxSubmit(options_fcreate); return false; " method="post" name="fcreate" id="fcreate"><?
	?><input name="name" id="fname" style="width:240px; " /><input type="submit" value="создать" /><?
	?><input type="hidden" name="task" value="folder_edit" /><?
	?><input type="hidden" name="ca" value="names_ajax" /><?
	?><input type="hidden" name="4ajax" value="1" /><?
	?><input type="hidden" name="id" value="0" /><?
	?></form><?
}

function folder_edit(){
	global $reg;
	// создаем или редактируем новую папку
	$i24r = new mosDBTable( "#__names_prop", "id", $reg['db'] );
	$i24r->id = ggri('id');
	$i24r->name = $_REQUEST['name'];	//safelySqlStr
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__names_prop ORDER BY #__names_prop.ordering DESC LIMIT 0,1 ");
		$i24r->ordering = $iexmaxorder[0]->ordering+1;
	}
	if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else $i24r->store();
	
	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new_cat', $reg['names_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save_cat', $reg['names_name'], $i24r->name, $i24r->id );

	?>ins_ajax_load_target ("ca=names_ajax&task=show_names_folder&icsmart_namesparent=<?=icsmarti('icsmart_namesparent') ?>&4ajax=1", "#names_select_list");<?

}
?>
