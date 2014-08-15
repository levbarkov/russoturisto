<?
define( "_VALID_INSITE", 1 );

require_once("../../../../../../../iconfig.php");
require_once(site_path . '/i24.php' );
require_once(site_path. '/idb.php');

$mosConfig_dbprefix = $DBPrefix;
$mosConfig_host = $DBhostname;
$mosConfig_user = $DBuserName;
$mosConfig_password = $DBpassword;
$mosConfig_db = $DBname;
$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );

function eGetShopId(){
		global $database;
		$G24sql2 = "SELECT id FROM #__menu WHERE link = 'index.php?c=ex'";
		$database->setQuery( $G24sql2 );
		$rows = $database->loadObjectList( );
		if (  count ($rows)==0  ) return -1;
		return $rows[0]->id;
}
$eShopId = eGetShopId();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Каталог товаров / услуг</title>
	<script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<base target="_self" />
</head>
<body style="display: none">
<? 
//24было так <body onLoad="tinyMCEPopup.executeOnLoad('init();');" style="display: none">
?>
	<div id="divAbout">
	<form>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
				<tr>
					<td>
					
<?
						// ФОРМИРУЕМ ВХОДНОЙ МАССИВ - СПИСОК ДИРЕКТОРИЙ
						static $links = array();
						if (empty($links)) {
								$G24sql = "SELECT * FROM #__excat WHERE parent=0 ORDER BY name ASC";
								$database->setQuery( $G24sql );
								$rows = $database->loadObjectList( );
//								ggr ($rows);
								array_push($links, array(0, 'folder', 'Folders', 'text', 'text', 1) );
								foreach ($rows as $row){
									$ifotocat = ggsql( "SELECT * FROM #__excat_foto WHERE excat_id=".$row->id );  // определим фотки
									if (  count ($ifotocat)==0  )
										array_push($links, array(1, $row->id, $row->name, "/includes/images/noimage.png", "/includes/images/noimage.png", 1) );
									else
										array_push($links, array(1, $row->id, $row->name, "/images/ex/cat/".$ifotocat[0]->small, "/images/ex/cat/".$ifotocat[0]->org, 1) );
								} 
								
							$num_links = sizeof($links);
			//				print "alert(".$num_links.")";
							$arr = array();
							for ($i=0; $i<$num_links; $i++) {
								if ((!empty($links[$i][1])) && (!empty($links[$i][2]))) {
									$links[$i][1] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][1]));
									$links[$i][2] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][2]));
									$links[$i][3] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][3]));
									$links[$i][4] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][4]));
									array_push($arr, '['.intval($links[$i][0]).',"'.$links[$i][1].'","'.$links[$i][2].'","'.$links[$i][3].'","'.$links[$i][4].'","'.$links[$i][5].'"]');
								}	
							}
							$links = implode(',',$arr);	
							
						}		
						?>					
						<div id="links" style="width:575px; height:443px; background-color:#FFFFFF; border: 2px inset threedface; overflow:auto; padding:5px 5px"> 
											<!-- a list of links to pages on your site should be generated below: -->
										</div>
<script language="javascript">
var CURRENT_HIGHLIGHT;		
function highlight(srcElement) {
	if (CURRENT_HIGHLIGHT) {
		CURRENT_HIGHLIGHT.style.backgroundColor='#ffffff';
		CURRENT_HIGHLIGHT.style.color ='#003399';
	}
	srcElement.style.backgroundColor='highlight';
	srcElement.style.color = 'highlighttext';
	CURRENT_HIGHLIGHT = srcElement;
}





function localLink(page,e24title, e24img, e24fimg) {

		var elm = document.getElementById('prev');
		elm.innerHTML = '<img id="previewImg" src="' + e24img + '" border="0" onload="updateImageData();"/>';


		//   write load img
		document.getElementById('links').innerHTML = '<table width="100%" height="90%"><tr><td align="center" valign="middle">Loading...<br><br><img src="<?php echo site_url; ?>/ibots/editors/tinymce/e24code/img/load_bar.gif" height="12" width="251" alt="" class="inset"><br></td></tr></table>';

		document.getElementById('txtUrl').value="<?php echo site_url; ?>/index.php?option=com_virtuemart&page=shop.browse&category_id="+page+"&Itemid=<? print $eShopId; ?>";
		document.getElementById('e24hit').value=e24title;
		document.getElementById('e24img').value="<?php echo site_url; ?>"+e24img;
		document.getElementById('e24fimg').value="<?php echo site_url; ?>"+e24fimg;


				var pt = document.getElementById('js_loader');
				pt.parentNode.removeChild(pt);

				pt = document.createElement('script');
				document.body.appendChild(pt);
				pt.setAttribute('type', 'text/javascript');
				pt.setAttribute('id', 'js_loader');

				//alert ("<?php echo site_url; ?>/ibots/editors/tinymce/e24code/calcdata.php?id="+page);
				pt.setAttribute('src', '<?php echo site_url; ?>/ibots/editors/tinymce/e24code/calcdata.php?id='+page);	
				
				page = page.replace(/&quote;/gi, '"');
				
}						
							olinks=[<? print($links); ?>];
							obj = "";
							obj.links = olinks;
							// show links
							if (obj.links != '') {
								if (1) {
									var links = olinks;
									var depth = '';
									var indent = '';
									var str = '';
									var num = links.length;
									for (var i=0; i<num; i++) { 
										if (links[i][1] && links[i][2]) {
											if (links[i][0] >= 1) {
												depth = (links[i][0]-1)*23;
												indent = "<img src=\"/ibots/editors/tinymce/e24code/img/branch.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">";
											} else {
												depth = 0;
												indent = '';
											}
											if (links[i][5] == 1) {
												e24imgfile = "<img src=\"/ibots/editors/tinymce/e24code/img/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">";
											} else if (links[i][5] == 2) {
												e24imgfile = "<img src=\"/ibots/editors/tinymce/e24code/img/htm_icon.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">";
											}
											
											
											if (links[i][1] == 'folder') {
												str += "<nobr><p class=\"filelink\" style=\"height:22px;margin:2px 2px 2px "+(depth+2)+"px\">" + indent + "<img src=\"/ibots/editors/tinymce/e24code/img/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">" + (links[i][2].replace(/' '/gi, '&nbsp;')) + " </p></nobr>";
											} else {
												str += "<nobr><a style=\"display:block;\" id=\"" + (links[i][1].replace(/"/gi, '&quote;')) + "\" style=\"text-decoration:none; height:22px; margin:0px 0px 0px " + depth + "px;\" onclick=\"highlight(this)\" href=\"javascript:localLink(\'" + (links[i][1].replace(/'/gi, "\\'").replace(/"/gi, '&quote;')) + "\',\'"+(links[i][2].replace(/'/gi, "\\'").replace(/"/gi, '&quot;'))+"\',\'"+links[i][3]+"\',\'"+links[i][4]+"\');\" >" + indent + e24imgfile + (links[i][2].replace(/ /gi, '&nbsp;')) + " </a></nobr>";
											}
										}
									}
									document.getElementById('links').innerHTML = str;
									obj.linksHTML = str;
								} else {
									document.getElementById('links').innerHTML = obj.linksHTML;
								}
							}
							
		
function setAttrib(elm, attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib.toLowerCase()];

	if (typeof(value) == "undefined" || value == null) {
		value = "";

		if (valueElm)
			value = valueElm.value;
	}

	if (value != "") {
		elm.setAttribute(attrib.toLowerCase(), value);

		if (attrib == "style")
			attrib = "style.cssText";

		if (attrib.substring(0, 2) == 'on')
			value = 'return true;' + value;

		if (attrib == "class")
			attrib = "className";

		eval('elm.' + attrib + "=value;");
	} else
		elm.removeAttribute(attrib);
}
		
function insertAction_Img() {
	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24img.value;
	
	var html = "<img src='" + document.forms[0].e24img.value + "' width='" +hiddenImg.width + "' height='" +hiddenImg.height + "'>";
	tinyMCE.execCommand('mceInsertContent', false, html);			
	tinyMCEPopup.close();
}	
function insertAction_FullImg() {
	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24fimg.value;

	var html = "<img src='" + document.forms[0].e24fimg.value + "' >";
	tinyMCE.execCommand('mceInsertContent', false, html);			
	tinyMCEPopup.close();
}
function insertAction_ImgLink() {
	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24img.value;

	var html = "<a href='" + document.forms[0].txtUrl.value + "'><img src='" + document.forms[0].e24img.value + "' border='0' width='" +hiddenImg.width + "' height='" +hiddenImg.height + "'></a>";
	tinyMCE.execCommand('mceInsertContent', false, html);			
	tinyMCEPopup.close();
}
function insertAction_FullImgLink() {
	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24fimg.value;

	var html = "<a href='" + document.forms[0].txtUrl.value + "'><img src='" + document.forms[0].e24fimg.value + "' border='0' ></a>";
	tinyMCE.execCommand('mceInsertContent', false, html);			
	tinyMCEPopup.close();
}


function insertAction_ImgLinkToFullImg() {
	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24img.value;
	hiddenfImg= new Image();
	hiddenfImg.src= document.forms[0].e24fimg.value;
		
	var html = "<a href='" + document.forms[0].e24fimg.value + "' onclick=\"window.open('" + document.forms[0].e24fimg.value + "','','width=" + (hiddenfImg.width+20) + ",height=" + (hiddenfImg.height+25) + "');return false;\"><img src='" + document.forms[0].e24img.value + "' border='0' width='" + hiddenImg.width + "' height='" + hiddenImg.height + "'></a>";
	tinyMCE.execCommand('mceInsertContent', false, html);			
	tinyMCEPopup.close();
}


function insertAction_LinkToImg() {

	var html = "<a href='" + document.forms[0].e24img.value + "' class='for_real_effect' >" + document.forms[0].e24hit.value + "</a>";
	tinyMCE.execCommand('mceInsertContent', false, html);			
	tinyMCEPopup.close();
	return;	
	
	// старый код для версии тинимсе - 2

	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24img.value;
	
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();

	elm = tinyMCE.getParentElement(elm, "a");

	tinyMCEPopup.execCommand("mceBeginUndoLevel");
	// Create new anchor elements
	if (elm == null) {	
		
		if (tinyMCE.isSafari)
			tinyMCEPopup.execCommand("mceInsertContent", false, '<a href="#mce_temp_url#">' + inst.selection.getSelectedHTML() + '</a>');
		else
			tinyMCEPopup.execCommand("createlink", false, "#mce_temp_url#");
	
		var elementArray = tinyMCE.getElementsByAttributeValue(inst.getBody(), "a", "href", "#mce_temp_url#");
		
		// if elementArray.length==0 so we not select anything
		if (  elementArray.length==0  ){
			var html = "<a href='" + document.forms[0].e24img.value + "' onclick=\"window.open('" + document.forms[0].e24img.value + "','','width=" + (hiddenImg.width+20) + ",height=" + (hiddenImg.height+25) + "');return false;\">" + document.forms[0].e24hit.value + "</a>";
			tinyMCE.execCommand('mceInsertContent', false, html);			
		}
		
		for (var i=0; i<elementArray.length; i++) {
		
			var elm = elementArray[i];
			// Move cursor behind the new anchor
			if (tinyMCE.isGecko) {
				var sp = inst.getDoc().createTextNode(" ");

				if (elm.nextSibling)
					elm.parentNode.insertBefore(sp, elm.nextSibling);
				else
					elm.parentNode.appendChild(sp);

				// Set range after link
				var rng = inst.getDoc().createRange();
				rng.setStartAfter(elm);
				rng.setEndAfter(elm);

				// Update selection
				var sel = inst.getSel();
				sel.removeAllRanges();
				sel.addRange(rng);
			}

			setAllAttribs2(elm);
		}
	} else
		setAllAttribs2(elm);

	tinyMCE._setEventsEnabled(inst.getBody(), false);
	tinyMCEPopup.execCommand("mceEndUndoLevel");
	tinyMCEPopup.close();
}

			
function insertAction_LinkToFullImg() {

	var html = "<a href='" + document.forms[0].e24fimg.value + "' class='for_real_effect' >" + document.forms[0].e24hit.value + "</a>";
	tinyMCE.execCommand('mceInsertContent', false, html);			
	tinyMCEPopup.close();
	return;	
	
	// старый код для версии тинимсе - 2

	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24fimg.value;
	
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();

	elm = tinyMCE.getParentElement(elm, "a");

	tinyMCEPopup.execCommand("mceBeginUndoLevel");
	// Create new anchor elements
	if (elm == null) {	
		
		if (tinyMCE.isSafari)
			tinyMCEPopup.execCommand("mceInsertContent", false, '<a href="#mce_temp_url#">' + inst.selection.getSelectedHTML() + '</a>');
		else
			tinyMCEPopup.execCommand("createlink", false, "#mce_temp_url#");
	
		var elementArray = tinyMCE.getElementsByAttributeValue(inst.getBody(), "a", "href", "#mce_temp_url#");
		
		// if elementArray.length==0 so we not select anything
		if (  elementArray.length==0  ){	
			var html = "<a href='" + document.forms[0].e24fimg.value + "' onclick=\"window.open('" + document.forms[0].e24fimg.value + "','','width=" + (hiddenImg.width+20) + ",height=" + (hiddenImg.height+25) + "');return false;\">" + document.forms[0].e24hit.value + "</a>";
			tinyMCE.execCommand('mceInsertContent', false, html);			
		}
		
		for (var i=0; i<elementArray.length; i++) {
		
			var elm = elementArray[i];

			// Move cursor behind the new anchor
			if (tinyMCE.isGecko) {
				var sp = inst.getDoc().createTextNode(" ");

				if (elm.nextSibling)
					elm.parentNode.insertBefore(sp, elm.nextSibling);
				else
					elm.parentNode.appendChild(sp);

				// Set range after link
				var rng = inst.getDoc().createRange();
				rng.setStartAfter(elm);
				rng.setEndAfter(elm);

				// Update selection
				var sel = inst.getSel();
				sel.removeAllRanges();
				sel.addRange(rng);
			}

			setAllAttribs3(elm);
		}
	} else
		setAllAttribs3(elm);

	tinyMCE._setEventsEnabled(inst.getBody(), false);
	tinyMCEPopup.execCommand("mceEndUndoLevel");
	tinyMCEPopup.close();
}
							
function insertAction_Link() {

	var html = "<a href='" + document.forms[0].txtUrl.value + "'>" + document.forms[0].e24hit.value + "</a>";
	tinyMCE.execCommand('mceInsertContent',false, html);
	tinyMCEPopup.close();
	return;	
	
	// старый код для версии тинимсе - 2
	var inst = tinyMCE.getInstanceById(tinyMCEPopup.getWindowArg('editor_id'));
	var elm = inst.getFocusElement();

	elm = tinyMCE.getParentElement(elm, "a");

	tinyMCEPopup.execCommand("mceBeginUndoLevel");
	// Create new anchor elements
	if (elm == null) {	
		
		if (tinyMCE.isSafari)
			tinyMCEPopup.execCommand("mceInsertContent", false, '<a href="#mce_temp_url#">' + inst.selection.getSelectedHTML() + '</a>');
		else
			tinyMCEPopup.execCommand("createlink", false, "#mce_temp_url#");
	
		var elementArray = tinyMCE.getElementsByAttributeValue(inst.getBody(), "a", "href", "#mce_temp_url#");
		
		// if elementArray.length==0 so we not select anything
		if (  elementArray.length==0  ){
			var html = "<a href='" + document.forms[0].txtUrl.value + "'>" + document.forms[0].e24hit.value + "</a>";
			tinyMCE.execCommand('mceInsertContent', true, html);
		}
		
		for (var i=0; i<elementArray.length; i++) {
		
			var elm = elementArray[i];
			// Move cursor behind the new anchor
			if (tinyMCE.isGecko) {
				var sp = inst.getDoc().createTextNode(" ");

				if (elm.nextSibling)
					elm.parentNode.insertBefore(sp, elm.nextSibling);
				else
					elm.parentNode.appendChild(sp);

				// Set range after link
				var rng = inst.getDoc().createRange();
				rng.setStartAfter(elm);
				rng.setEndAfter(elm);

				// Update selection
				var sel = inst.getSel();
				sel.removeAllRanges();
				sel.addRange(rng);
			}

			setAllAttribs(elm);
		}
	} else
		setAllAttribs(elm);

	tinyMCE._setEventsEnabled(inst.getBody(), false);
	tinyMCEPopup.execCommand("mceEndUndoLevel");
	tinyMCEPopup.close();
}			


/**
 * вставляем ссылку в редактор, в зависимости от выбранных параметров
 */
function insertAction() {

	if (  document.forms[0].eLinkType.value=='eLink'  )
		insertAction_Link();
	else if (  document.forms[0].eLinkType.value=='eImg'  )
		insertAction_Img();
	else if (  document.forms[0].eLinkType.value=='eFullImg'  )
		insertAction_FullImg();
	else if (  document.forms[0].eLinkType.value=='eImglink'  )
		insertAction_ImgLink();
	else if (  document.forms[0].eLinkType.value=='eFullImgLink'  )
		insertAction_FullImgLink();
	else if (  document.forms[0].eLinkType.value=='eImgLinktoFullImg'  )
		insertAction_ImgLinkToFullImg();
	else if (  document.forms[0].eLinkType.value=='eLinkToImg'  )
		insertAction_LinkToImg();
	else if (  document.forms[0].eLinkType.value=='eLinkToFullImg'  )
		insertAction_LinkToFullImg();
		
	return true;
}

function setAllAttribs(elm) {

	var formObj = document.forms[0];
	var href = formObj.txtUrl.value;
	
	setAttrib(elm, 'href', href);
	setAttrib(elm, 'mce_href', href);
	setAttrib(elm, 'title');
	setAttrib(elm, 'target', '_self');
	setAttrib(elm, 'id');
	setAttrib(elm, 'style');
	setAttrib(elm, 'class');
	setAttrib(elm, 'rel');
	setAttrib(elm, 'rev');
	setAttrib(elm, 'charset');
	setAttrib(elm, 'hreflang');
	setAttrib(elm, 'dir');
	setAttrib(elm, 'lang');
	setAttrib(elm, 'tabindex');
	setAttrib(elm, 'accesskey');
	setAttrib(elm, 'type');
	setAttrib(elm, 'onfocus');
	setAttrib(elm, 'onblur');
	setAttrib(elm, 'onclick');
	setAttrib(elm, 'ondblclick');
	setAttrib(elm, 'onmousedown');
	setAttrib(elm, 'onmouseup');
	setAttrib(elm, 'onmouseover');
	setAttrib(elm, 'onmousemove');
	setAttrib(elm, 'onmouseout');
	setAttrib(elm, 'onkeypress');
	setAttrib(elm, 'onkeydown');
	setAttrib(elm, 'onkeyup');
	
//	alert (href);

	// Refresh in old MSIE
	if (tinyMCE.isMSIE5)
		elm.outerHTML = elm.outerHTML;
}		

function setAllAttribs2(elm) { // img to link
	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24img.value;
	
	var formObj = document.forms[0];
	var href = document.forms[0].e24img.value;
	var e24onclick = "window.open('" + document.forms[0].e24img.value + "','','width=" + (hiddenImg.width+20) + ",height=" + (hiddenImg.height+25) + "');return false;";
	
	setAttrib(elm, 'href', document.forms[0].e24img.value);
	setAttrib(elm, 'mce_href', document.forms[0].e24img.value);
	setAttrib(elm, 'title');
	setAttrib(elm, 'target', '');
	setAttrib(elm, 'id');
	setAttrib(elm, 'style');
	setAttrib(elm, 'class');
	setAttrib(elm, 'rel');
	setAttrib(elm, 'rev');
	setAttrib(elm, 'charset');
	setAttrib(elm, 'hreflang');
	setAttrib(elm, 'dir');
	setAttrib(elm, 'lang');
	setAttrib(elm, 'tabindex');
	setAttrib(elm, 'accesskey');
	setAttrib(elm, 'type');
	setAttrib(elm, 'onfocus');
	setAttrib(elm, 'onblur');
	setAttrib(elm, 'onclick', e24onclick);
	setAttrib(elm, 'ondblclick');
	setAttrib(elm, 'onmousedown');
	setAttrib(elm, 'onmouseup');
	setAttrib(elm, 'onmouseover');
	setAttrib(elm, 'onmousemove');
	setAttrib(elm, 'onmouseout');
	setAttrib(elm, 'onkeypress');
	setAttrib(elm, 'onkeydown');
	setAttrib(elm, 'onkeyup');
	
//	alert (href);

	// Refresh in old MSIE
	if (tinyMCE.isMSIE5)
		elm.outerHTML = elm.outerHTML;
}	
function setAllAttribs3(elm) { // link to fiil img
	hiddenImg= new Image();
	hiddenImg.src= document.forms[0].e24fimg.value;
	


	var formObj = document.forms[0];
	var href = document.forms[0].e24fimg.value;
	var e24onclick = "window.open('" + document.forms[0].e24fimg.value + "','','width=" + (hiddenImg.width+20) + ",height=" + (hiddenImg.height+25) + "');return false;";

	
	setAttrib(elm, 'href', document.forms[0].e24fimg.value);
	setAttrib(elm, 'mce_href', document.forms[0].e24fimg.value);
	setAttrib(elm, 'title');
	setAttrib(elm, 'target', '');
	setAttrib(elm, 'id');
	setAttrib(elm, 'style');
	setAttrib(elm, 'class');
	setAttrib(elm, 'rel');
	setAttrib(elm, 'rev');
	setAttrib(elm, 'charset');
	setAttrib(elm, 'hreflang');
	setAttrib(elm, 'dir');
	setAttrib(elm, 'lang');
	setAttrib(elm, 'tabindex');
	setAttrib(elm, 'accesskey');
	setAttrib(elm, 'type');
	setAttrib(elm, 'onfocus');
	setAttrib(elm, 'onblur');
	setAttrib(elm, 'onclick', e24onclick);
	setAttrib(elm, 'ondblclick');
	setAttrib(elm, 'onmousedown');
	setAttrib(elm, 'onmouseup');
	setAttrib(elm, 'onmouseover');
	setAttrib(elm, 'onmousemove');
	setAttrib(elm, 'onmouseout');
	setAttrib(elm, 'onkeypress');
	setAttrib(elm, 'onkeydown');
	setAttrib(elm, 'onkeyup');
	
//	alert (href);

	// Refresh in old MSIE
	if (tinyMCE.isMSIE5)
		elm.outerHTML = elm.outerHTML;
}	



function updateImageData() {
/*hiddenImg= new Image();
hiddenImg.src= "/images/stories/main_1.jpg";
alert (hiddenImg.height);
*/
	var formObj = document.forms[0];
	preloadImg = document.getElementById('previewImg');
	if (  preloadImg.width>200  )
		preloadImg.width=200;
	if (  preloadImg.height>100  )
		preloadImg.height=100;
}
						</script>										
					</td>
				</tr>
				<tr>
					<td><script id="js_loader" type="text/javascript"></script></td>
				</tr>
				<tr height="100%" >
					<td align="justify">
						<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
							<span fckLang="DlgLnkProProtoUrlSel">Выбранный объект: </span></td>
							<td><input id="e24hit" style="WIDTH: 203px"  type="text" /></td>
							<td rowspan="7" width="210px" align="center" valign="middle"><div id="prev" style="height:100px"></div></td>
						</tr>
						<tr>
							<td>
							<span>Стоимость: </span></td>
							<td><input id="ggprice" type="text" style="WIDTH: 203px" /></td>
						</tr>
						<tr>
							<td>
							<span>Ссылка на описание: </span></td>
							<td><input id="txtUrl" type="text" style="WIDTH: 203px" /></td>
						</tr>
						<tr>
							<td><span>Ссылка на изображение: </span></td>
							<td><input id="e24img" style="WIDTH: 203px"  type="text" /></td>
						</tr>
						<tr>
							<td><span>Ссылка на п. изображение: </span></td>
							<td><input id="e24fimg" style="WIDTH: 203px"  type="text" /></td>
						</tr>
						<tr>
							<td><span>Тип ссылки: </span></td>
							<td><select id="eLinkType" style="width:203px">
									<option value="eLink" selected="selected">Ссылка</option>
									<option value="eImglink" fckLang="DlgLnkPro2Proto">Изображение-ссылка</option>
									<option value="eImg" fckLang="DlgLnkPro3Proto">Только изображение</option>
									<option value="eFullImgLink" fckLang="DlgLnkPro4Proto">Полноразмерное изображение-ссылка</option>
									<option value="eFullImg" fckLang="DlgLnkPro5Proto">Только полноразмерное изображение</option>
									<option value="eImgLinktoFullImg" fckLang="DlgLnkPro6Proto">Изображение-ссылка на полноразмерное изображение</option>
									<option value="eLinkToImg" fckLang="DlgLnkPro7Proto">Ссылка на изображение</option>
									<option value="eLinkToFullImg" fckLang="DlgLnkPro8Proto">Ссылка на полноразмерное изображение</option>
								</select></td>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td><input type="button" id="insert" name="insert" value="Вставить" onClick="insertAction();" /></td>
							<td align="left"><input type="button" id="ggprvfimg" name="ggprvfimg" value="Показать фото" onClick='window.open(document.getElementById("e24fimg").value,"subWind","status,menubar, scrollbars,resizable,height=600,width=600")' /></td>
							<td align="right"><input type="button" id="cancel" name="cancel" value="Отменить" onClick="tinyMCEPopup.close();" /></td>
						</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
</form>
</body>
</html>
