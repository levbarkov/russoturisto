<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();

function show_price(){
	$all_pricies_ggsql = ggsql (  "select * from #__exprice "  );
	$all_pricies = libarray::convert_ggsql_object_to_array ($all_pricies_ggsql); // ggtr ($all_pricies);
	$all_cy_ggsql = ggsql (  "select * from #__exprice_cy "  );
	$all_cy = libarray::convert_ggsql_object_to_array ($all_cy_ggsql); // ggtr ($all_pricies);
	
	$all_price_good = ggsql (  "select * from #__exprice_good where good=".$_REQUEST['good']  );
	$good = ggo ($_REQUEST['good'], "#__exgood");
	?><script language="javascript">
		var options_price={		target:        '#all_price',
								beforeSubmit:  function(){ 	$('.price_form_tr').css("visibility","hidden"); 
														$('#price_form_submit').html('<img src=/iadmin/images/loading16.gif border=0 align=absmiddle />&nbsp;&nbsp;Сохранение...'); }
					};
	</script>
	<form name="price_form" id="price_form" method="post" onsubmit=" $(this).ajaxSubmit(options_price); return false; ">
	<table cellspacing="0" cellpadding="5"><?
	?><tr>
		<td>Прайс</td>
		<td>Товар</td>
		<td>Комплектация</td>
		<td>Цена</td>
		<td></td>
		<td></td>
	</tr><?
	?><tr height="1px" style=" background-image:url(/iadmin/images/b1px.png); background-repeat:repeat-x;"><td colspan="6"></td></tr><?

	foreach ($all_price_good as $recgood){
		$expack = ggo ($recgood->expack, "#__expack");
		$link = "javascript: ins_ajax_open ('?ca=exprice&task=newprice&edit_price=".$recgood->id."&good=".$_REQUEST['good']."&4ajax=1',400,230); void(0);";
		?><tr class="rowajax price_form_tr">
			<td><a href="<?=$link ?>"><?=$all_pricies[$recgood->parent] ?></a></td>
			<td><a href="<?=$link ?>"><?=$good->name ?></a></td>
			<td><a href="<?=$link ?>"><?=$expack->name ?></a></td>
			<td><input type="text" name="price[<?=$recgood->id ?>]" value="<?=$recgood->val ?>" style="width:70px;" /></td>
			<td><a href="<?=$link ?>"><?=$all_cy[$recgood->cy] ?></a></td>
			<td><a href="javascript: ins_ajax_load ('ca=exprice&task=del_exprice&id=<?=$recgood->id; ?>&good=<?=$recgood->good ?>&4ajax=1'); void(0);"><img src="/iadmin/images/del.png" width="16" height="16"  border=0 /></a></td>
		</tr><?
	}
	?><tr><td colspan="3"></td><td colspan="3"><span id="price_form_submit"><input type="submit" value="Сохранить стоимость (Enter)" /></span></td></tr><?
	?><tr height="1px" style=" background-image:url(/iadmin/images/b1px.png); background-repeat:repeat-x; background-position:bottom;"><td colspan="6"></td></tr><?
	?></table>
	<input type="hidden" name="ca" value="exprice" />
	<input type="hidden" name="good" value="<?=$_REQUEST['good'] ?>" />
	<input type="hidden" name="task" value="save_price" />
	<input type="hidden" name="4ajax" value="1" />
	</form><?
}
function save_price(){
	global $reg;
	foreach ( $_POST['price'] as $price_id => $price_val ){
		$i24r = new mosDBTable( "#__exprice_good", "id", $reg['db'] );
		$i24r->id = $price_id;
		$i24r->val = $price_val;
	
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	show_price();
}

function newprice( ) {
	global $reg;
	$good = ggo ($_REQUEST['good'], "#__exgood");
	if (  isset ($_REQUEST['edit_price'])  )	$this_price = ggo ($_REQUEST['edit_price'], "#__exprice_good");
	else {	
		$this_price->id=0;
		$this_price->parent=0;
		$this_price->expack=$_REQUEST['edit_price'];
		$this_price->val="";
		$this_price->cy=1;
	}


	$ivcats2 = ggsql ( "select * from #__exprice" );
	foreach ($ivcats2 as $ivcat2)	$vcats2[] = mosHTML::makeOption( $ivcat2->id, $ivcat2->name);
	
	$ivcats_cy = ggsql ( "select * from #__exprice_cy" );
	foreach ($ivcats_cy as $ivcat_cy)	$vcats_cy[] = mosHTML::makeOption( $ivcat_cy->id, $ivcat_cy->name);
	
	$vcats[] = mosHTML::makeOption( "0", "- без комплектации -");
	$ivcats = ggsql ( "select * from #__expack where parent = ".$_REQUEST['good'] );
	foreach ($ivcats as $ivcat)	$vcats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);

	?><div  id="myprice_debug">Товар: «<?=$good->name ?>»</div>
	<form name="make_newprice" method="post" id="make_newprice" >
	<table cellpadding="4" cellspacing="0">

		<tr><td colspan="2">&nbsp;</td></tr>		

			<tr class="rowajax">
				<td>Прайс:</td>
				<td><?
					print mosHTML::selectList( $vcats2, 'parent', 'class="inputbox" style="width:200px;" size="1" ', 'value', 'text', $this_price->parent );	
				?></td>
			</tr>

			<tr class="rowajax">
				<td>Комплектация:</td>
				<td><?
					print mosHTML::selectList( $vcats, 'expack', 'class="inputbox" style="width:200px;" size="1" ', 'value', 'text', $this_price->expack );	
				?></td>
			</tr>
			<tr>
				<td>Стоимость: </td>
				<td><input type="text" value="<?=$this_price->val ?>" style="width:140px;" name="price"  /><? print mosHTML::selectList( $vcats_cy, 'cy', 'class="inputbox" size="1" ', 'value', 'text', $this_price->cy ); ?></td>
			</tr>			
		<tr>
			<td></td>
			<td><input type="submit" value="<? if (  isset ($_REQUEST['edit_price'])  ) print "Сохранить"; else print "Добавить"; ?>" class="button" /></td>
		</tr>		
	</table>
	<input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
	<input type='hidden' name='task' value='addprice' />
	<input type='hidden' name='good' value='<?=$_REQUEST['good'] ?>' />
	<? if (  isset ($_REQUEST['edit_price'])  ){ ?><input type='hidden' name='edit_price' value='<?=$_REQUEST['edit_price'] ?>' /><? } ?>
	<input type="hidden" name="4ajax" value="1" />
	</form>
	
	<script language="javascript">
		var options = {		dataType:		'script'		};
		$('#make_newprice').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script>
	<?
}


function addprice( ) {
	global $reg;
	if (  isset ($_REQUEST['edit_price'])  )	$this_price = ggo ($_REQUEST['edit_price'], "#__exprice_good");
	else {	$this_price->id=0;	}
	
	// в переменной $_POST - значения введеной пользователем информации
	$i24r = new mosDBTable( "#__exprice_good", "id", $reg['db'] );
	$i24r->id = $this_price->id;
	$i24r->parent = $_POST['parent'];
	$i24r->good = $_REQUEST['good'];
	$i24r->expack = $_REQUEST['expack'];
	$i24r->val = $_REQUEST['price'];
	$i24r->cy = $_REQUEST['cy'];

	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	?>$('#myprice_debug').html('<img src="/iadmin/images/loading16.gif" width="16" height="16" align="absmiddle" /> Сохранение...');	
	ins_ajax_load_target ("ca=exprice&task=show_price&good=<?=$_REQUEST['good'] ?>&4ajax=1", "#all_price");
	$('#myprice_debug').html('Стоимость: сохранена');
	<? if ( isset ($_REQUEST['edit_price']) ){ ?>$.fn.colorbox.close(); <? } ?>
	<?
}
function del_exprice(){
	global $reg;
	ggsqlq("DELETE FROM #__exprice_good WHERE id = ".$_REQUEST['id']."  ");
	?>ins_ajax_load_target ('ca=exprice&task=show_price&good=<?=$_REQUEST['good'] ?>&4ajax=1', '#all_price'); <?

}

function edit_cy(){
	global $reg;
	$good = ggo ($_REQUEST['good'], "#__exgood");
	$good = ggsql ( "SELECT * FROM #__exprice_good WHERE parent=".$_REQUEST['parent']."  AND good=".$_REQUEST['good']." AND expack=".$_REQUEST['expack'] );
	if (  count($good)>0  ) $good = $good[0];
	else {	$good->cy=1;	}
	$ivcats_cy = ggsql ( "select * from #__exprice_cy" );
	foreach ($ivcats_cy as $ivcat_cy)	$vcats_cy[] = mosHTML::makeOption( $ivcat_cy->id, $ivcat_cy->name);
	
	$price_name = ggo($_REQUEST['parent'], "#__exprice");
	$exgood_name = ggo($_REQUEST['good'], "#__exgood");
	$expack_name = ggo($_REQUEST['expack'], "#__expack");

	?><div  id="myprice_debug">Изменение валюты</div>
	<form name="make_newprice" method="post" id="make_newprice" >
	<table cellpadding="4" cellspacing="0">
		<tr><td colspan="2">&nbsp;</td></tr>		
			<tr class="rowajax">
				<td>Прайс:</td><td><? print $price_name->name; ?></td>
			</tr>
			<tr class="rowajax">
				<td>Товар:</td><td><? print $exgood_name->name; ?></td>
			</tr>
			<tr class="rowajax">
				<td>Комплектация:</td><td><? print $expack_name->name; ?></td>
			</tr>
			<tr>
				<td>Стоимость: </td><td><? print mosHTML::selectList( $vcats_cy, 'cy', 'class="inputbox" size="1" ', 'value', 'text', $good->cy ); ?></td>
			</tr>			
		<tr>
			<td></td><td><input type="submit" value="<? if (  isset ($_REQUEST['edit_price'])  ) print "Сохранить"; else print "Сохранить"; ?>" class="button" /></td>
		</tr>		
	</table>
	<input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
	<input type='hidden' name='task' value='save_cy' />
	<input type='hidden' name='parent' value='<?=$_REQUEST['parent'] ?>' />
	<input type='hidden' name='good' value='<?=$_REQUEST['good'] ?>' />
	<input type='hidden' name='expack' value='<?=$_REQUEST['expack'] ?>' />
	<? if (  isset ($_REQUEST['edit_price'])  ){ ?><input type='hidden' name='edit_price' value='<?=$_REQUEST['edit_price'] ?>' /><? } ?>
	<input type="hidden" name="4ajax" value="1" />
	</form>
	
	<script language="javascript">
		var options = {		dataType:		'script'		};
		$('#make_newprice').submit(function() { 	$(this).ajaxSubmit(options); 	return false; }); 
	</script>
	<?
}


/*	<script language="javascript">
	function toggle_price(){
		if (  $("#pricegood").attr ('i24state')=='hide'  ){
			$("#pricegood").show();	$("#pricegood").attr ('i24state','display');
			ins_ajax_load_target ("ca=exprice&task=show_price&good=<?=$row->id ?>&4ajax=1", "#all_price");
		} else {
			$("#pricegood").hide();	$("#pricegood").attr ('i24state','hide');
		}	
	}
	</script>
	<table class="adminheading"><tr><td class="edit"><br /><a class="ajax_link" href="javascript: toggle_price(); void(0); ">Стоимость</a></td></tr></table>
	
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="pricegood" style="display:none; ">
		<tr class="workspace">
			<td>
				<table width="100%" >
					<tr>
						<td><div id="all_price" ><img src="/iadmin/images/loading.gif" width="32" height="32" /></div></td>
					</tr>
					<tr>
						<td><a href="javascript: ins_ajax_open('?ca=exprice&task=newprice&good=<?=$row->id ?>&4ajax=1', 400, 230); void(0);">Добавить стоимость</a></td>
					</tr>
				</table>
			</td>
		</tr>
	</table> 
	
	<a href="javascript: ins_ajax_open ('?ca=expack&task=edit_cy&parent=<?=$price->id ?>&good=<?=$recgood->parent ?>&expack=<?=$recgood->id ?>&4ajax=1',400,230); void(0);"><?=$all_cy[$qty[0]->cy] ?></a>
	*/
?>
