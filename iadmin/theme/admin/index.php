<?php
/** проверка включения этого файла файлом-источником */
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
$tstart = getmicrotime();
global $reg;
// needed to seperate the ISO number from the language file constant _ISO
$iso = explode( '=', _ISO );
// xml prolog
echo '<?xml version="1.0" encoding="'. $iso[0] .'"?' .'>';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $sitename; ?> - Панель управления INSITE</title><?
?><link rel="stylesheet" href="theme/admin/css/theme.css" type="text/css" />
<script type="text/javascript" src="/includes/tinymce/js/tinymce/tinymce.min.js"></script>
		<script type="text/javascript">
		// alert (1);
		tinymce.init({
			selector: "textarea#introtext, textarea#fulltext",
			language: "ru",
			valid_elements : "*[*]",
			inline_styles : false,
			extended_valid_elements : "*[*]",
			remove_script_host : true,
			convert_urls: true,
			relative_urls: false,
			plugins: [
				"advlist autolink lists link image charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons template paste textcolor colorpicker textpattern"
			],
			toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | preview forecolor backcolor",
			image_advtab: true,
			templates: [
				{title: 'Верхняя часть статьи', url: '/includes/tinymce/js/tinymce/templates/article1.php'},
				{title: 'Нижняя часть статьи', url: '/includes/tinymce/js/tinymce/templates/article2.php'}
			]
			// toolbar2: "code preview forecolor backcolor",
			// image_advtab: true,
		});
		</script>
<?
// ОПРЕДЕЛЯЕМ НУЖНО ЛИ ЗАПУСКАТЬ HTML РЕДАКТОР
$doeditor = false;

if		(  strcmp($option, "typedcontent")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;
else if (  strcmp($option, "typedcontent")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "typedcontent")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;


else if (  strcmp($option, "content")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "content")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "content")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "sections")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "sections")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "sections")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "categories")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "categories")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "categories")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "foto")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "categories")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "modules")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "modules")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "modules")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "excat")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "excat")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "exgood")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "exgood")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "shopcfg")==0  &&  strcmp($task, "cfg")==0  ) $doeditor = true;

else if (  strcmp($option, "foto")==0  &&  strcmp($task, "fotocat_edit")==0  ) $doeditor = true;

else if (  strcmp($option, "backlinkcfg")==0  &&  strcmp($task, "cfg")==0  ) $doeditor = true;

else if (  strcmp($option, "icat")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "icat")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "icat")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "exfoto")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "exfoto")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "exfoto")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "names")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
else if (  strcmp($option, "names")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
else if (  strcmp($option, "names")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;

else if (  strcmp($option, "nopage")==0  &&  strcmp($task, "cfg")==0  ) $doeditor = true;
//$doeditor = true;

// ОПРЕДЕЛЯЕМ НУЖНО ЛИ ЗАПУСКАТЬ БЫСТРОЕ ВЫДЕЛЕНИЕ
$doqselect = false; // ggtr01($option); ggtr01($task);
if		(  strcmp($option, "frontpage")==0  &&  $task==""  ) $doqselect = true;
else if	(  strcmp($option, "menus")==0  &&  $task==""  ) $doqselect = true;
else if	(  strcmp($option, "users")==0  &&  strcmp($task, "view")==0  ) $doqselect = true;
else if (  strcmp($option, "modules")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "typedcontent")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "content")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "icat")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "icat")==0  &&  $task=="cancel"  ) $doqselect = true;

else if (  strcmp($option, "excat")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "excat")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "excat")==0  &&  $task=="cancel"  ) $doqselect = true;
else if (  strcmp($option, "exgood")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "exgood")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "exgood")==0  &&  $task=="cancel"  ) $doqselect = true;

else if (  strcmp($option, "adcat")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "adcat")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "adcat")==0  &&  $task=="cancel"  ) $doqselect = true;
else if (  strcmp($option, "adgood")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "adgood")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "adgood")==0  &&  $task=="cancel"  ) $doqselect = true;

else if (  strcmp($option, "exfoto")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "exfoto")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "exfoto")==0  &&  $task=="cancel"  ) $doqselect = true;

else if (  strcmp($option, "easybook")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "feedback")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "banners")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "banners")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "poll")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "poll")==0  &&  $task==""  ) $doqselect = true;

else if (  strcmp($option, "easylist")==0  &&  $task==""  ) $doqselect = true;
else if (  strcmp($option, "easylist")==0  &&  $task=="view"  ) $doqselect = true;
else if (  strcmp($option, "easylist")==0  &&  $task=="cancel"  ) $doqselect = true;

else if (  strcmp($option, "exfoto_foto")==0  &&  (  $task=="view"  ||    $task=="cancel_edit")  ) $doqselect = true;
else if (  strcmp($option, "foto")==0  &&  (  $task==""  ||    $task=="cancel_edit")  ) $doqselect = true;

else if (  strcmp($option, "comment")==0  &&  (  $task=="view"  ||    $task=="")  ) $doqselect = true;

else if (  strcmp($option, "file")==0  &&  (  $task=="view"  ||    $task=="")  ) $doqselect = true;

if (  ibrowser()=='Mozilla Firefox'  )	$doqselect = false;
if (  ibrowser()=='Opera'  ) 			$doqselect = false;
if (  ibrowserpro()=='MS Internet Explorer 8.0')	$doqselect = false;

$doqselect = false;

js("/includes/js/jquery-1.7.1.min.js");

if (  $doeditor  )  iLoadEditor();
?><script language="JavaScript" src="<?php echo site_url; ?>/includes/js/JSCookMenu_mini.js" type="text/javascript"></script><?
?><script language="JavaScript" src="<?php echo site_url; ?>/includes/js/insite.javascript.js" type="text/javascript"></script><?

highslide_init();
if (0) { ?>
<script language="JavaScript" src="<?php echo site_url; ?>/includes/js/overlib_mini.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo site_url; ?>/includes/js/overlib_hideform_mini.js" type="text/javascript"></script>
<? } 

// секция перетаскиваемых строк в таблицах
?><!--<script type="text/javascript" src="/includes/tabledrag/jquery.js"></script>--><?



js("/iadmin//includes/js/jquery.easydrag.js");
js("/iadmin//includes/js/jquery.easydrag.handler.beta2.js");

js("/includes/js/jTypeWriter.js");
js("/includes/js/jquery.form.js");
?><script type="text/javascript" src="/includes/tabledrag/jquery.tablednd_0_5.js"></script><?
?><script type="text/javascript" src="/includes/tabledrag/table-dnd-example.js"></script><?	// here placed JQUERY READY function
?><link type="text/css" rel="stylesheet" href="/includes/tabledrag/table-dnd-example.css" /><?

$imgareaselect = new imgareaselect(); $imgareaselect->java_init();

// секция мгновенного выделения
if (  $doqselect  ){
	?><script type="text/javascript" src="/includes/checkbox_area_select/checkboxAreaSelect.js"></script><?
}
// секция jquery_autocomplete
?><link rel="stylesheet" href="/includes/autocomplete/jquery.autocomplete.css" type="text/css" />
  <script type="text/javascript" src="/includes/autocomplete/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="/includes/autocomplete/jquery.autocomplete.js"></script>
  
<? $colorbox = new colorbox(); 		$colorbox->theme='theme_admin'; $colorbox->java_init(); ?>

  <script>
  $(document).ready(function(){
  	<? 	$tags = new tags("exgood", $reg['db'], $reg); ?>	var data = "<?=$tags->get_all_tegs_string() ?>".split(",");
	$("#exgood_tags").autocomplete(data,{
		multiple: true
	});
  	<? 	$names = new names(); ?>	var names_data = "<?=$names->get_all_names_string() ?>".split(",");
	$("#all_names").autocomplete(names_data,{
		multiple: true
	});
<? /*   // учет брендов, уже используем чисто в names
  	var brand_data = "<?=$names->get_brand_string() ?>".split(",");
	$("#all_brand").autocomplete(brand_data,{
		multiple: false,
                multipleSeparator: ''
	});
*/ ?>
  	var effects_data = "gray,opacity#ffffff#50,tint#ff0000,brightness#20,reflection#33%#3#ffffff#60,watermark###BR#/images/watermark.png,round_png#15#3".split(",");
	$("#all_effects").autocomplete(effects_data,{
		multiple: true
	});
	
//	$("#box1").easydrag();
//	$("#box1").setHandler('sss5544'); 
  	$("#colorbox").easydrag(); 
	$("#colorbox").setHandler('cboxTitle'); 
	
  });
</script><?

?><meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" /><?
?><meta name="Generator" content="Система управления содержимым - INSITE!" /><?

?><script type='text/javascript'> 
  $(document).ready(function(){
	<? if (  $doqselect  ){ ?> $(document).checkboxAreaSelect(); <? } ?>
  });
</script><?

?></head>
<body>
<script language="Javascript" type="text/javascript" src="<?php echo site_url; ?>/includes/js/wz_tooltip.js" type="text/javascript"></script>
<table width="100%" style="width:100%; " cellpadding="0" cellspacing="0" border="0" align="center" ><? /* САМАЯ ГЛАВНАЯ ТАБЛИЦА */ ?>
	<tr>
		<td width="7%" style=" width:7%; background-color:#6e9a41; ">&nbsp;</td>
		<td width="86%" style=" width:86%; background-color:#6e9a41; "><?
			?><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background-color:#6e9a41"><tr><td style="padding-left:2px;"><?php
                        ilog::vlog('{ меню админки'); mosLoadAdminModule( 'fullmenu' );  ilog::vlog('меню админки }');  ?></td><td align="right" nowrap="nowrap" style="text-align:right; padding-right:10px; white-space:nowrap; color:#FFFFFF;"><a href="/iadmin/index2.php" style="color:#FFFFFF;">v.&nbsp;<? global $iversion; print $iversion ?></a></td></tr></table><?
		?></td>
		<td width="7%" style=" width:7%; background-color:#6e9a41; ">&nbsp;</td>
	</tr>
	<tr>
		<td></td><td><?
			?><table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0" align="center"><? // кнопки  типа сохранить отменить редактировать
			?><tr><?
				?><td  class="menudottedline" align="left"><?
				?><table cellspacing="0" cellpadding="0" border="0" id="toolbar"><tr valign="middle"><td nowrap="nowrap" style="padding-left:6px;"><a href="index2.php?ca=users&task=editA&id=<? print $my->id; ?>&hidemainmenu=1" class="toolbar" style=" width:100%;"><? print ( $my->username ); ?><?
					$iusertype = ggo($my->gid, "#__usertypes"); 
				    ?></a></td></tr></table><?
				?></td><? $params = new mosParameters( $my->params, '', 'component' ); if (  $params->_params->editor=='none'  ) $params->_params->editor='html';
				?><td  class="menudottedline" align="left" width="100%"><? 
					?><table cellspacing="0" cellpadding="0" border="0" id="toolbar"><tr valign="middle"><td nowrap="nowrap"><div class="itoolbar">&nbsp;&nbsp;&nbsp;&nbsp;Редактор:&nbsp;</div></td><td nowrap="nowrap"><a href="index2.php?change_mode=<?   print ($params->_params->editor=='html' ? 'wysiwyg' : 'html')    ?>&<?=$_SERVER['QUERY_STRING'] ?>" class="toolbar" style=" width:100%"><? print ($params->_params->editor);
					 ?></a></td></tr></table><?
				?></td><?
				?><td width="100%" class="menudottedline" align="right"><?
					 mosLoadAdminModule( 'toolbar' );
				?></td><?
			?></tr><?
			?></table><?
		?></td><td></td>
	</tr>
	<tr>
		<td></td><td><? mosLoadAdminModule( 'msg' ); ?></td><td></td>
	</tr>

	<tr>
		<td></td><td><?
                        ilog::vlog('{ компонент '.$reg['ca']);
                        ilog::vlog('task='.$reg['task']);
			iMainBody_Admin();
                        ilog::vlog('компонент }'.$reg['ca']);
		?></td><td></td>
	</tr>

	<tr>
		<td></td>
		<td><? 
			?><table width="85%" class="menubar" cellpadding="0" cellspacing="0" border="0" align="center"><? // кнопки  типа сохранить отменить редактировать
				?><tr><?
					?><td  class="menudottedline" align="left">&nbsp;</td><?
					?><td  class="menudottedline" align="left" width="100%">&nbsp;</td><?
					?><td width="100%" class="menudottedline" align="right"><?
						 mosLoadAdminModule( 'toolbar2' );
					?></td><?
				?></tr><?
			?></table><?
		?></td>
		<td></td>
	</tr>

	<tr height="43">
		<td></td><td>&nbsp;</td><td></td>
	</tr>

	<tr>
		<td></td><td align="center" style="text-align:center;"><?
				?><?php echo '<span class="smallgrey">';  $tend = getmicrotime(); $totaltime = ($tend - $tstart); printf ("Время создания страницы %f секунд", $totaltime); echo '</span>' 
		?></td><td></td>
	</tr>

	<tr height="17">
		<td></td><td>&nbsp;</td><td></td>
	</tr>
		
</table>

</body>
</html>
