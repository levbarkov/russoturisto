<?
global $reg, $task;

$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );

switch ( $reg['task'] ) {
	case 'items_xml':
		show_items_xml();
		break;
	case 'view':
	default:
		ishowfotogallery( $id, $gid, $pop, $option );
		break;

}

function ishowfotogallery( $id, $gid, $pop, $option ){
js("/includes/js/swfobject.js");
?><style type="text/css">
<!--
/* hide from ie on mac \*/
html {
	height: 100%;
	overflow: hidden;
}
#flashcontent {
	height: 100%;
}
/* end hide */
body {
	margin-left: 0px;
	margin-top: 0px;
	background-color: #000000;
	margin-right: 0px;
	margin-bottom: 0px;
	margin: 0;
	padding: 0;
	height: 100%;
}
.style5 {
	font-size: 12px;
	color: #CCCCCC;
	font-family: Arial, Helvetica, sans-serif;
}
a:link {
	text-decoration: none;
	color: #FF0000;
	font-family: Arial, Helvetica, sans-serif;
}
a:visited {
	text-decoration: none;
	color: #FF0000;
}
a:hover {
	text-decoration: underline;
}
a:active {
	text-decoration: none;
	
}
.style7 {color: #666666}
-->
</style><body topmargin="0" leftmargin="0" scroll="no">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle" class="style5">
<div id="flashcontent">
	<script type="text/javascript">
		// <![CDATA[
		
		var so = new SWFObject('/component/foto_full_screen_gallery/ultimate_v2.swf?' + Math.round(Math.random() * 99999), "Ultimate", "100%", "100%", "8", "#000000",true);
		so.addParam("scale", "noscale");
		so.addParam("menu", "false");
		so.addParam("allowFullScreen", "true");
		so.addVariable("items_xml", "/foto_full_screen_gallery_iteml.xml%3Fid%3D<?=ggri('id') ?>%26type=<?=ggrr('type') ?>%264ajax%3D1");
		so.addVariable("music_xml", "/component/foto_full_screen_gallery/music/music.xml");
		so.write("flashcontent");
		
		// ]]>
	</script>
</div></td></tr></table>
</body><? 
} 

function show_items_xml(){
$row = ggo (ggri('id'), '#__'.ggrr('type'));

$component_foto = new component_foto( 0 );
$component_foto->init( ggrr('type') );
$component_foto->parent = $row->id;

$fotocats = $fotocats = $component_foto->get_fotos();
//ggtr ($row);
$foto = $fotocats[0];
//ggtr ($foto->id);
print '<'; ?>?xml version="1.0" encoding="utf-8"?>
<content>
	<menu_item name="<?=$row->name; ?>"><?
	foreach ( $fotocats as $foto ){
		?><img filename="<?=$component_foto->url_prefix.$foto->full ?>" title="<?=$foto->desc ?>" thumbnail="<?=$component_foto->url_prefix.$foto->mid ?>"><![CDATA[<?=$foto->desc ?>]]></img><?
	}
	?></menu_item>
</content>


<?
}
?>