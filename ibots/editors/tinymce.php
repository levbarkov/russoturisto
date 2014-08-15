<?php

// no direct access
defined( '_VALID_INSITE' ) or die( 'Restricted access' );

$_MAMBOTS->registerFunction( 'onInitEditor', 'botTinymceEditorInit' );
$_MAMBOTS->registerFunction( 'onInitEditor_for_site_users', 'botTinymceEditorInit_for_site_users' );
$_MAMBOTS->registerFunction( 'onGetEditorContents', 'botTinymceEditorGetContents' );
$_MAMBOTS->registerFunction( 'onEditorArea', 'botTinymceEditorEditorArea' );

/**
* TinyMCE WYSIWYG Editor - javascript initialisation
*/

function botTinymceEditorInit() {
	global $database, $reg;

		$load 				= '<script type="text/javascript" src="'. site_url .'/ibots/editors/tinymce/jscripts/tiny_mce/tiny_mce_src.js"></script>';
/*		$load_init 			= '	<script type="text/javascript" src="'. site_url .'/mambots/editors/tinymce/jscripts/tiny_mce/dump.js"></script>	';*/
		$load_init 			= '<script type="text/javascript" src="'. site_url .'/ibots/editors/tinymce/e24code/AjexFileManager/ajex.js"></script>';

		$query = "SELECT theme"
		. "\n FROM #__theme_menu"
		. "\n WHERE pi = 0 ";
		$database->setQuery( $query );
		$template = $database->loadResult();
		$e24file_path = site_url .'/theme/'. $template .'/icss/icss.css';
		$site_url = site_url;
		$reg_tinymce_ctrlEnter_handler = $reg['tinymce_ctrlEnter_handler'];
		$reg_editor_content_css = site_url.$reg['editor_content_css'];
		$reg_editor_mainstyle_css = site_url.'/theme/theme_extfiles/editor_css.css';

return <<<EOD
	$load
	$load_init
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "example,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist",
//		plugins : "contextmenu,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,example,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,


		// Drop lists for link/image/media/template dialogs
//		template_external_list_url : "lists/template_list.js",
//		external_link_list_url : "lists/link_list.js",
//		external_image_list_url : "lists/image_list.js",
//		media_external_list_url : "lists/media_list.js", 

		$reg_tinymce_ctrlEnter_handler
		
		// Style formats
		style_formats : [
			{title : 'fancy', inline : 'a', classes : 'fancy'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],
		
		// INSITE SPECIAL CONFIG
			//file_browser_callback : "fileBrowserCallBack", - old_editor
			file_browser_callback : "AjexFileManager.open",
			template_external_list_url : "$site_url/ibots/editors/tinymce/e24code/templates/example_template_list.js",
			document_base_url : "$site_url",
			language : "ru",
			// Example content CSS (should be your site CSS)
			content_css : "$reg_editor_mainstyle_css,$reg_editor_content_css",
			relative_urls : false,
			//remove_script_host : false,


		// Replace values for the template plugin
		template_replace_values : {
			username : "KrasInsite",
			staffid : "78"
		}
	});

	AjexFileManager.init({
			returnTo: 'tinymce',
			skin: 'dark' // [dark, light], default=dark
	});
/*
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		//language : "ru_CP1251",
		language : "ru",
		plugins : "contextmenu,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		theme_advanced_buttons1_add_before : "newdocument,separator",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_path_location : "bottom",
		content_css : "$e24file_path",
	    plugin_insertdate_dateFormat : "%Y-%m-%d",
	    plugin_insertdate_timeFormat : "%H:%M:%S",
		extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
//		external_link_list_url : "example_link_list.js",
//		external_image_list_url : "example_image_list.js",
//		flash_external_list_url : "example_flash_list.js",
//		media_external_list_url : "example_media_list.js",
		template_external_list_url : "$site_url/ibots/editors/tinymce/e24code/templates/example_template_list.js",
		file_browser_callback : "fileBrowserCallBack",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		nonbreaking_force_tab : true,
		apply_source_formatting : true,
		convert_urls : false,
		document_base_url : "$site_url/",
		template_replace_values : {
			username : "Jack Black",
			staffid : "991234"
		}
	});  */
	//		convert_urls : true,  - относительные ссылки на каталог good
	//						false - best
	
	function TinyMCE_Save(editor_id, content, node)
	{
		base_url = tinyMCE.settings['document_base_url'];
		var vHTML = content;
		if (true == true){
			vHTML = tinyMCE.regexpReplace(vHTML, 'href\s*=\s*"?'+base_url+'', 'href="', 'gi');
			vHTML = tinyMCE.regexpReplace(vHTML, 'src\s*=\s*"?'+base_url+'', 'src="', 'gi');
			vHTML = tinyMCE.regexpReplace(vHTML, 'mce_real_src\s*=\s*"?', '', 'gi');
			vHTML = tinyMCE.regexpReplace(vHTML, 'mce_real_href\s*=\s*"?', '', 'gi');
		}
		return vHTML;
	}
	
	function fileBrowserCallBack(field_name, url, type, win) {
		// This is where you insert your custom filebrowser logic
//			dump(type, true);
			my_type = type;
			my_field = field_name;
			my_win = win;
			my_url = "$site_url/ibots/editors/tinymce/e24code/filemanager/browser/default/browser.html?Type=images&Connector=connectors/php/connector.php";
			
			
			
//////		newWindow = window.open(my_url,"subWind","status,menubar,height=800,width=800");	
//////		newWindow.focus( );
		
			
			



/*    tinyMCE.activeEditor.windowManager.open({
        file : my_url,
        title : 'My File Browser',
        width : 420,  // Your dimensions may differ - toy around with them!
        height : 400,
        resizable : "yes",
        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : "no"
    }, {
        window : win,
        input : field_name,
		plugin_url : my_url
    });
    return false;	*/		

	}
	function e24BrowserPreview(field_name, url, type, win) {
		// This is where you insert your custom filebrowser logic
//			dump(type, true);
			my_type = type;
			my_field = field_name;
			my_win = win;					
			newWindow = window.open("$site_url/ibots/editors/tinymce/e24code/filemanager/browser/default/browser.html?Type=images&Connector=connectors/php/connector.php","subWind","status,menubar,height=800,width=800");	
			newWindow.focus( );			
		//win.document.forms[0].elements['src'].value = "22someurl.htm";
	}

</script>
EOD;
}
function botTinymceEditorGetContents( $editorArea, $hiddenField ) {
	return <<<EOD

		tinyMCE.triggerSave();
EOD;
}
function botTinymceEditorEditorArea( $name, $content, $hiddenField, $width, $height, $col, $row ) {
	global $_MAMBOTS;

	$results = $_MAMBOTS->trigger( 'onCustomEditorButton' );
	$buttons = array();
	foreach ($results as $result) {
		if ( $result[0] ) {
			$buttons[] = '<img src="'.site_url.'/ibots/editors-xtd/'.$result[0].'" onclick="tinyMCE.execCommand(\'mceInsertContent\',false,\''.$result[1].'\')" alt="'.$result[1].'" />';
		}
	}
	$buttons = implode( "", $buttons );

	return <<<EOD

<textarea id="$hiddenField" name="$hiddenField" cols="$col" rows="$row" style="width:{$width}px; height:{$height}px;" mce_editable="true">$content</textarea>
<br />$buttons
EOD;
}






        //////////////////////////////////
      //////////////////////////////////////
    ////////  section for site users  ////////
      //////////////////////////////////////
        //////////////////////////////////
function botTinymceEditorInit_for_site_users() {
	global $database;
	$load = '<script type="text/javascript" src="'. site_url .'/ibots/editors/tinymce/jscripts/tiny_mce/tiny_mce_src.js"></script>';
	return <<<EOD
	$load
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "simple"
	});	
</script>
EOD;
}
?>