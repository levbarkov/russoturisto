<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
$cid = josGetArrayInts( 'cid' );

switch ($reg['task']) {
	case 'del_recommended':		del_recommended( $_POST['good'], $_POST['id'] );
								break;
	case 'get_cats_and_goods':	get_cats_and_goods( );
								break;
	case 'show_recommended':	show_recommended( );
								break;
	case 'newrecommended':		newrecommended( );
								break;
	case 'addrecommended':		addrecommended( );
								break;

}
function del_recommended( $parent, $id ){
	global $reg;

	$reg['db']->setQuery("DELETE FROM #__exrecommended WHERE parent = ".$parent." AND recommended='".$id."' ");
	$reg['db']->query();

	?> ins_ajax_load ("ca=exrecommended&task=show_recommended&good=<?=$_POST['good'] ?>&4ajax=1"); <?
}
function show_recommended(){
	$str = '<table cellpadding=4 cellspacing=0>';
	$recgoods = ggsql ( "select * from #__exrecommended where parent=".$_REQUEST['good'] );
	foreach ($recgoods as $recgood){
		$thegood = ggo($recgood->recommended, "#__exgood");
		$str .= '<tr class=rowajax2 ><td><a href=\''.$thegood->sefnamefullcat.'/'.$thegood->sefname.'.html\' target=\'_blank\'>'.just_del_quotes($thegood->name).'</a></td><td><a href=\'javascript: ins_ajax_load (\"ca=exrecommended&task=del_recommended&id='.$thegood->id.'&good='.$_REQUEST['good'].'&4ajax=1\"); void(0);\'><img src=\'/iadmin/images/del.png\'  border=0 /></a></td></tr>';
	}
	$str .= '</table>';
	?>
	$("#all_recommended").html ("<?=$str ?>");
	<?
}
function addrecommended( ) {
	global $reg;
	// в переменной $_POST - значения введеной пользователем информации
	$recgoods = ggsqlr ( "select * from #__exrecommended where parent=".$_REQUEST['good']." AND recommended=".$_REQUEST['id'] );
	if ($recgoods==0){
		$query = sprintf("INSERT INTO #__exrecommended VALUES(%d, %d)", $_POST['good'], $_POST['id']);
		$reg['db']->setQuery($query);
		$reg['db']->query();
		
		?>ins_ajax_load ("ca=exrecommended&task=show_recommended&good=<?=$_POST['good'] ?>&4ajax=1");
		<?
	}
}
function get_cats_and_goods(){
	$str = '<table>';
	$recgoods = ggsql ( "select * from #__excat where parent=".$_REQUEST['parent'] );
//	ggdd();
	if (  $_REQUEST['parent']!=0  ){
		$thecat = ggo($_REQUEST['parent'], "#__excat");
		$str .= '<tr><td width=16><img src=\'/iadmin/images/arrow_up.png\'  border=0 /><td colspan=2><a href=\'javascript: ins_ajax_load (\"ca=exrecommended&task=get_cats_and_goods&parent='.$thecat->parent.'&good='.$_REQUEST['good'].'&4ajax=1\"); void(0);\'>Вернуться назад</a></td><td></td></tr>';
	}
	foreach ($recgoods as $recgood){
//		$thegood = ggo($recgood->recommended, "#__exgood");
		$str .= '<tr><td width=16><img src=\'/iadmin/images/folder.png\'  border=0 /></td><td><a href=\'javascript: ins_ajax_load (\"ca=exrecommended&task=get_cats_and_goods&parent='.$recgood->id.'&good='.$_REQUEST['good'].'&4ajax=1\"); void(0);\'>'.just_del_quotes($recgood->name).'</a></td><td></td></tr>';
	}
	
	$recgoods = ggsql ( "select * from #__exgood where parent=".$_REQUEST['parent'] );
//	ggdd();
	foreach ($recgoods as $recgood){
//		$thegood = ggo($recgood->recommended, "#__exgood");
		$str .= '<tr><td > </td><td><a href=\'javascript: ins_ajax_load (\"ca=exrecommended&task=addrecommended&id='.$recgood->id.'&good='.$_REQUEST['good'].'&4ajax=1\"); void(0);\'>'.just_del_quotes($recgood->name).'</a></td><td></td></tr>';
	}
	
	$str .= '</table>';
	?>$("#recommended_list").html("<?=$str ?>");	<?
	//print "dsfsd";
}

function newrecommended( ) {
	global $reg;
	?>
	<div  id="mypack_debug">Выберите товар или директорию и нажмите на нее</div>
	
	<div  id="recommended_list"><img src="/iadmin/images/loading.gif" width="32" height="32" /></div>
	<script language="javascript">
		ins_ajax_load ("ca=exrecommended&task=get_cats_and_goods&parent=0&good=<?=$_REQUEST['good'] ?>&4ajax=1", "recommended_list");
	</script>
	<?
}

?>