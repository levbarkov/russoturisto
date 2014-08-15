<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();



function show_units(){
	global $reg;
	$all_units = ggsql (  "select * from #__exgood_unit where parent=".$_REQUEST['good']." order by id ; "  );
	?><script language="javascript">
		var options_units={		target:        '#all_units',
								beforeSubmit:  function(){ 	$('.units_form_tr').css("visibility","hidden"); 
														$('#units_form_submit').html('<img src=/iadmin/images/loading16.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
					};
	</script>
	<form name="units_form" id="units_form" method="post" onsubmit=" $(this).ajaxSubmit(options_units); return false; ">
	<table cellspacing="0" cellpadding="4"><?


	foreach ($all_units as $recgood){
		?><tr class="rowajax2 units_form_tr"><?
			?><td><input type="text" name="unit[<?=$recgood->id ?>]" value="<?=$recgood->name ?>" style="width:240px; "  /></td>
			<td><a href="javascript: ins_ajax_load ('ca=exunit&task=del_unit&id=<?=$recgood->id; ?>&good=<?=$recgood->parent ?>&4ajax=1'); void(0);"><img src="/iadmin/images/del.png" width="16" height="16"  border=0 /></a></td>
		</tr><?
	}
	?><tr class="units_form_tr"><td colspan="2"><img src="/iadmin/images/ins.png"  border=0 align="absmiddle" width="16" height="16" />&nbsp;&nbsp;<?
		?><a href="javascript: ins_ajax_load_target ('ca=exunit&task=new_unit&good=<?=$_REQUEST['good'] ?>&4ajax=1', '#all_units'); void(0);">добавить новое единицу</a></td></tr><?
	?><tr><td colspan="2"><span id="units_form_submit"><input type="submit" value="Сохранить изменения (Enter)" /></span></td></tr><?

	?></table>
	<input type="hidden" name="ca" value="exunit" />
	<input type="hidden" name="good" value="<?=$_REQUEST['good'] ?>" />
	<input type="hidden" name="task" value="save_units" />
	<input type="hidden" name="4ajax" value="1" />
	</form><?
}
function save_units(){
	global $reg;
	
	foreach (  $_POST['unit'] as $unit_id => $unit_name ){
		//ggtr01($unit_id);	ggtr($unit_name);
		$i24r = new mosDBTable( "#__exgood_unit", "id", $reg['db'] );
		$i24r->id = $unit_id;
		$i24r->parent = $_REQUEST['good'];
		$i24r->name = $unit_name;
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	show_units();
//	ggr($_POST);
}
function new_unit(){
	global $reg;
	
	$i24r = new mosDBTable( "#__exgood_unit", "id", $reg['db'] );
	$i24r->id = 0;
	$i24r->parent = $_REQUEST['good'];
	$i24r->name = "";
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	
	?><script language="javascript">
		ins_ajax_load_target ('ca=exunit&task=show_units&good=<?=$_REQUEST['good'] ?>&4ajax=1', '#all_units');
	</script><?
}
function del_unit(){
	global $reg;
	$expack = new expack();
	$expack->delete_unit ($_REQUEST['id']);
	?>ins_ajax_load_target ('ca=exunit&task=show_units&good=<?=$_REQUEST['good'] ?>&4ajax=1', '#all_units'); <?
	?>ins_ajax_load_target ('ca=expack&task=show_packs&good=<?=$_REQUEST['good'] ?>&4ajax=1', '#all_packs'); <?
}


?>