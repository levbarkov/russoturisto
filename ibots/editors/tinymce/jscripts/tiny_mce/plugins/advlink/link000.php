<?	$is_admin = true;
	if (  strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'].'/iadmin') === false  )  $is_admin = false; 
//	if (  $is_admin == false  ) return;
?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$lang_insert_link_title}</title>
	<script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="../../utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="../../utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="jscripts/functions.js"></script>
	<link href="css/advlink.css" rel="stylesheet" type="text/css" />
	<base target="_self" />
</head>
<body id="advlink" onLoad="tinyMCEPopup.executeOnLoad('init();');" style="display: none">
    <form onSubmit="insertAction();return false;" action="#">
		<div class="tabs">
			<ul>
				<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onMouseDown="return false;">{$lang_advlink_general_tab}</a></span></li>
				<li id="popup_tab"><span><a href="javascript:mcTabs.displayTab('popup_tab','popup_panel');" onMouseDown="return false;">{$lang_advlink_popup_tab}</a></span></li>
				<li id="events_tab"><span><a href="javascript:mcTabs.displayTab('events_tab','events_panel');" onMouseDown="return false;">{$lang_advlink_events_tab}</a></span></li>
				<li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onMouseDown="return false;">{$lang_advlink_advanced_tab}</a></span></li>
			</ul>
		</div>

		<div class="panel_wrapper" style="height:530px">
			<div id="general_panel" class="panel current">
				<fieldset>
					<legend>{$lang_advlink_general_props}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
						  <td nowrap="nowrap"><label id="hreflabel" for="href">{$lang_insert_link_url}</label></td>
						  <td><table border="0" cellspacing="0" cellpadding="0">
								<tr>
								  <td><input id="href" name="href" type="text" value="" onChange="selectByValue(this.form,'linklisthref',this.value);" /></td>
								  <td id="hrefbrowsercontainer">&nbsp;</td>
<script language="javascript">
	function dfgfdhfg111(url, option) {

		if (  (option=='1')  &&  (!document.getElementById('ispopup').checked)  ) return;
		
		if (  url==''  ){
			alert ('Укажите поле "Адрес ссылки"');
			return;
		}
		// This is where you insert your custom filebrowser logic
//			dump(type, true);
		if (  option=='0'  )  option="status,menubar,height=700,width=1000, resizable=1, scrollbars=1";
		else{
			option="";
			if (  document.getElementById('popupwidth').value!=''  ) option += ", width="+document.getElementById('popupwidth').value;
			if (  document.getElementById('popupheight').value!=''  )option += ", height="+document.getElementById('popupheight').value;

			if (  document.getElementById('popuplocation').checked  ) option += ", location=yes";
			if (  document.getElementById('popupmenubar').checked  ) option += ", menubar=yes";
			if (  document.getElementById('popuptoolbar').checked  ) option += ", toolbar=yes";
			if (  document.getElementById('popupstatus').checked  ) option += ", status=yes";
			
			if (  document.getElementById('popupscrollbars').checked  ) option += ", scrollbars=yes";
			if (  document.getElementById('popupresizable').checked  ) option += ", resizable=yes";
			if (  document.getElementById('popupdependent').checked  ) option += ", dependent=yes";

		}

		if (  url.substring ( 0 , 1 )=="/"  ) e24_url = url;
		else if (  url.substring ( 0 , 7 )=="http://"  ) e24_url = url;
		else e24_url = "/" + url;
		
		newWindow = window.open(e24_url,"subWind",option);
		newWindow.focus( );			
		//win.document.forms[0].elements['src'].value = "22someurl.htm";
	}
</script>
								  <td id="e24_hrefbrowserpreview">
<a id="e24_link_prw" href="javascript: dfgfdhfg111(document.getElementById('href').value, '0');  " onMouseDown="return false;">
<img  src="/ibots/editors/tinymce/jscripts/tiny_mce/plugins/preview/images/preview.gif" onMouseOver="this.className='mceButtonOver';" onMouseOut="this.className='mceButtonNormal';" onMouseDown="this.className='mceButtonDown';" width="20" height="20" border="0" class="mceButtonNormal" alt="Посмотреть" />
</a>
								  </td>
								</tr>
							  </table></td>
						</tr>
						<tr id="linklisthrefrow">
							<td class="column1"><label for="linklisthref">{$lang_link_list}</label></td>
							<td colspan="2" id="linklisthrefcontainer">&nbsp;</td>
						</tr>
						<tr>
							<td class="column1"><label for="anchorlist">{$lang_advlink_anchor_names}</label></td>
							<td colspan="2" id="anchorlistcontainer">&nbsp;</td>
						</tr>
						<tr>
							<td><label id="targetlistlabel" for="targetlist">{$lang_insert_link_target}</label></td>
							<td id="targetlistcontainer">&nbsp;</td>
						</tr>
						<tr>
							<td nowrap="nowrap"><label id="titlelabel" for="title">{$lang_theme_insert_link_titlefield}</label></td>
							<td><input id="title" name="title" type="text" value="" /></td>
						</tr>
						<tr>
							<td><label id="classlabel" for="classlist">{$lang_class_name}</label></td>
							<td>
								 <select id="classlist" name="classlist" onChange="changeClass();">
									<option value="" selected>{$lang_not_set}</option>
								 </select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
							
							
							
							
							
							
							
							
							
							
							
							
						<?
						function connectDb($user, $pass, $host, $db) { 
							//this function connects to a mysql server 
							$sock = mysql_connect($host, $user, $pass);    
							//this function connects to a mysql database, once a server has been reached. 
							if(isset($sock)) { 
								if(!mysql_select_db($db, $sock)) { 
								} 
							} 
							mysql_query( "set session character_set_server=cp1251;" );
							mysql_query( "set session character_set_database=cp1251;" );
							mysql_query( "set session character_set_connection=cp1251;" );
							mysql_query( "set session character_set_results=cp1251;" );
							mysql_query( "set session character_set_client=cp1251;" );
							return $sock; 
						} 
						static $links = array();
						if (empty($links)) {
							// static content...
							require_once("../../../../../../../iconfig.php");
							$mosConfig_dbprefix = $DBPrefix;
							$mosConfig_host = $DBhostname;
							$mosConfig_user = $DBuserName;
							$mosConfig_password = $DBpassword;
							$mosConfig_db = $DBname;
							//require_once( 'common.php' );
							//require_once( 'database.php' );
							$socket = connectDb($mosConfig_user,$mosConfig_password,$mosConfig_host,$mosConfig_db); 
							$sql = "SELECT id, title,sefname FROM ".$mosConfig_dbprefix."content WHERE state = '1' ORDER BY ordering ASC;"; 
							$query = mysql_query($sql, $socket); 
							$j=0;
							while($data = mysql_fetch_assoc($query)) {
								//print '<pre>';print_r ($data);	print '<pre>';
								if ($j==0) {
									array_push($links, array(0, 'folder', 'Статичное содержимое', "f") );
								}
								//array_push($links, array(1, 'index.php?c=showscont&task=view&id='.$data['id'], $data['title'], "l") );
								array_push($links, array(1, '/'.$data['sefname'].'/', $data['title'], "l") );
								$j++;
							} 
							
							$socket = connectDb($mosConfig_user,$mosConfig_password,$mosConfig_host,$mosConfig_db); 
							$sql = "SELECT id, name,sefname,sefnamefull FROM ".$mosConfig_dbprefix."icat WHERE publish = '1' ORDER BY ".$mosConfig_dbprefix."icat.order ASC"; 
							$query = mysql_query($sql, $socket); 
							while($data = mysql_fetch_assoc($query)) {
								$socket2 = connectDb($mosConfig_user,$mosConfig_password,$mosConfig_host,$mosConfig_db); 
								$sql2 = "SELECT id, title,sefname,sefnamefullcat FROM ".$mosConfig_dbprefix."content WHERE state = '1' AND catid = '".$data['id']."' ORDER BY ordering ASC"; 
								// print $sql2;
								$query2 = mysql_query($sql2, $socket2); 
								$j=0;
								while($data2 = mysql_fetch_assoc($query2)) {
									if ($j==0) {
										//array_push($links, array(0, 'index.php?c=icontent&task=icat&pi=200&id='.$data['id'], "РУБРИКА: ".$data['name'], "f") );
									array_push($links, array(0, $data['sefnamefull']."/".$data['sefname'].'/', "РУБРИКА: ".$data['name'], "f") );
									}
									//array_push($links, array(1, 'index.php?c=icontent&task=view&pi=200&id='.$data2['id'], $data2['title'], "l") );
									array_push($links, array(1, $data2['sefnamefullcat']."/".$data2['sefname'].'.html', $data2['title'], "l") );
									$j++;
								} 
							}		
							
							
							$num_links = sizeof($links);
			//				print "alert(".$num_links.")";
							$arr = array();
							for ($i=0; $i<$num_links; $i++) { 
								if ((!empty($links[$i][1])) && (!empty($links[$i][2]))) {
									$links[$i][1] = str_replace(array("\t","\r\n","\r","\n",'"', '/'), array(' ','','','','\"', '\/'), strip_tags($links[$i][1]));
									$links[$i][2] = str_replace(array("\t","\r\n","\r","\n",'"', '/'), array(' ','','','','\"', '\/'), strip_tags($links[$i][2]));
									$links[$i][3] = str_replace(array("\t","\r\n","\r","\n",'"', '/'), array(' ','','','','\"', '\/'), strip_tags($links[$i][3]));
									$links[$i][4] = str_replace(array("\t","\r\n","\r","\n",'"', '/'), array(' ','','','','\"', '\/'), strip_tags($links[$i][4]));
									array_push($arr, '['.intval($links[$i][0]).',"'.$links[$i][1].'","'.$links[$i][2].'","'.$links[$i][3].'","'.$links[$i][4].'"]');
								}	
							}
							$links = implode(',',$arr);		
							//print_r($links);
							
						}		
						?>
<div id="links" style="width:460px; height:360px; background-color:#FFFFFF; border: 2px inset threedface; overflow:auto; padding:5px 5px"> 
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

function localLink(page,e24title) {
	page = page.replace(/&quote;/gi, '"');
	document.getElementById('href').value=page;
	if (document.getElementById('ispopup').checked) document.getElementById('popupurl').value=page;
	
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
											if (links[i][1] == 'folder') {
												str += "<nobr><p class=\"filelink\" style=\"height:22px;margin:2px 2px 2px "+(depth+2)+"px\">" + indent + "<img src=\"/ibots/editors/tinymce/e24code/img/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">" + (links[i][2].replace(/' '/gi, '&nbsp;')) + " </p></nobr>";
											} else {
												if (links[i][3] == 'f')
												str += "<nobr><a style=\"display:block;\" id=\"" + (links[i][1].replace(/"/gi, '&quote;')) + "\" style=\"text-decoration:none; height:22px; margin:0px 0px 0px " + depth + "px;\" onclick=\"highlight(this)\" href=\"javascript:localLink(\'" + (links[i][1].replace(/'/gi, "\\'").replace(/"/gi, '&quote;')) + "\',\'"+(links[i][2].replace(/'/gi, "\\'").replace(/"/gi, '&quot;'))+"\');\" title=\"" + (links[i][2].replace(/"/gi, '&quote;')) + "\">" + indent + "<img src=\"/ibots/editors/tinymce/e24code/img/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">" + (links[i][2].replace(/ /gi, '&nbsp;')) + " </a></nobr>";
												else 
												str += "<nobr><a style=\"display:block;\" id=\"" + (links[i][1].replace(/"/gi, '&quote;')) + "\" style=\"text-decoration:none; height:22px; margin:0px 0px 0px " + depth + "px;\" onclick=\"highlight(this)\" href=\"javascript:localLink(\'" + (links[i][1].replace(/'/gi, "\\'").replace(/"/gi, '&quote;')) + "\', \'" + (links[i][2].replace(/'/gi, "\\'").replace(/"/gi, '&quot;'))+"\');\" title=\"" + (links[i][2].replace(/"/gi, '&quote;')) + "\">" + indent + "<img src=\"/ibots/editors/tinymce/e24code/img/htm_icon.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">" + (links[i][2].replace(/ /gi, '&nbsp;')) + " </a></nobr>";
											}
										}
									}
									document.getElementById('links').innerHTML = str;
									obj.linksHTML = str;
								} else {
									document.getElementById('links').innerHTML = obj.linksHTML;
								}
							}
						</script>						
							
							
							
							
							
							
							
							
							
							</td>
						</tr>
					</table>
				</fieldset>
			</div>

			<div id="popup_panel" class="panel">
				<fieldset>
					<legend>{$lang_advlink_popup_props}</legend>

					<input type="checkbox" id="ispopup" name="ispopup" class="radio" onClick="setPopupControlsDisabled(!this.checked);buildOnClick();" />
					<label id="ispopuplabel" for="ispopup">{$lang_advlink_popup}</label>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td nowrap="nowrap"><label for="popupurl">{$lang_advlink_popup_url}</label>&nbsp;</td>
							<td>
								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td><input type="text" name="popupurl" id="popupurl" value="" onFocus="buildOnClick();" onChange="buildOnClick();" /></td>
										<td id="popupurlbrowsercontainer">&nbsp;</td>
										<td id="e24_hrefbrowserpreview">
<a id="e24_link_prw" href="javascript: dfgfdhfg111(document.getElementById('popupurl').value, '1');  " onMouseDown="return false;">
<img  src="/ibots/editors/tinymce/jscripts/tiny_mce/plugins/preview/images/preview.gif" onMouseOver="this.className='mceButtonOver';" onMouseOut="this.className='mceButtonNormal';" onMouseDown="this.className='mceButtonDown';" width="20" height="20" border="0" class="mceButtonNormal" alt="Посмотреть" />
</a>
								  </td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td nowrap="nowrap"><label for="popupname">{$lang_advlink_popup_name}</label>&nbsp;</td>
							<td><input type="text" name="popupname" id="popupname" value="" onChange="buildOnClick();" /></td>
						</tr>
						<tr>
							<td nowrap="nowrap"><label>{$lang_advlink_popup_size}</label>&nbsp;</td>
							<td nowrap="nowrap">
								<input type="text" id="popupwidth" name="popupwidth" value="" onChange="buildOnClick();" /> x
								<input type="text" id="popupheight" name="popupheight" value="" onChange="buildOnClick();" /> px
							</td>
						</tr>
						<tr>
							<td nowrap="nowrap" id="labelleft"><label>{$lang_advlink_popup_position}</label>&nbsp;</td>
							<td nowrap="nowrap">
								<input type="text" id="popupleft" name="popupleft" value="" onChange="buildOnClick();" /> /                                
								<input type="text" id="popuptop" name="popuptop" value="" onChange="buildOnClick();" /> (c /c = center)
							</td>
						</tr>
					</table>

					<fieldset>
						<legend>{$lang_advlink_popup_opts}</legend>

						<table border="0" cellpadding="0" cellspacing="4">
							<tr>
								<td><input type="checkbox" id="popuplocation" name="popuplocation" class="checkbox" onChange="buildOnClick();" /></td>
								<td nowrap="nowrap"><label id="popuplocationlabel" for="popuplocation">{$lang_advlink_popup_location}</label></td>
								<td><input type="checkbox" id="popupscrollbars" name="popupscrollbars" class="checkbox" onChange="buildOnClick();" /></td>
								<td nowrap="nowrap"><label id="popupscrollbarslabel" for="popupscrollbars">{$lang_advlink_popup_scrollbars}</label></td>
							</tr>
							<tr>
								<td><input type="checkbox" id="popupmenubar" name="popupmenubar" class="checkbox" onChange="buildOnClick();" /></td>
								<td nowrap="nowrap"><label id="popupmenubarlabel" for="popupmenubar">{$lang_advlink_popup_menubar}</label></td>
								<td><input type="checkbox" id="popupresizable" name="popupresizable" class="checkbox" onChange="buildOnClick();" /></td>
								<td nowrap="nowrap"><label id="popupresizablelabel" for="popupresizable">{$lang_advlink_popup_resizable}</label></td>
							</tr>
							<tr>
								<td><input type="checkbox" id="popuptoolbar" name="popuptoolbar" class="checkbox" onChange="buildOnClick();" /></td>
								<td nowrap="nowrap"><label id="popuptoolbarlabel" for="popuptoolbar">{$lang_advlink_popup_toolbar}</label></td>
								<td><input type="checkbox" id="popupdependent" name="popupdependent" class="checkbox" onChange="buildOnClick();" /></td>
								<td nowrap="nowrap"><label id="popupdependentlabel" for="popupdependent">{$lang_advlink_popup_dependent}</label></td>
							</tr>
							<tr>
								<td><input type="checkbox" id="popupstatus" name="popupstatus" class="checkbox" onChange="buildOnClick();" /></td>
								<td nowrap="nowrap"><label id="popupstatuslabel" for="popupstatus">{$lang_advlink_popup_statusbar}</label></td>
								<td><input type="checkbox" id="popupreturn" name="popupreturn" class="checkbox" onChange="buildOnClick();" checked="checked" /></td>
								<td nowrap="nowrap"><label id="popupreturnlabel" for="popupreturn">{$lang_advlink_popup_return}</label></td>
							</tr>
						</table>
					</fieldset>
				</fieldset>
			</div>

			<div id="advanced_panel" class="panel">
			<fieldset>
					<legend>{$lang_advlink_advanced_props}</legend>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td class="column1"><label id="idlabel" for="id">{$lang_advlink_id}</label></td> 
							<td><input id="id" name="id" type="text" value="" /></td> 
						</tr>

						<tr>
							<td><label id="stylelabel" for="style">{$lang_advlink_style}</label></td>
							<td><input type="text" id="style" name="style" value="" /></td>
						</tr>

						<tr>
							<td><label id="classeslabel" for="classes">{$lang_advlink_classes}</label></td>
							<td><input type="text" id="classes" name="classes" value="" onChange="selectByValue(this.form,'classlist',this.value,true);" /></td>
						</tr>

						<tr>
							<td><label id="targetlabel" for="target">{$lang_advlink_target_name}</label></td>
							<td><input type="text" id="target" name="target" value="" onChange="selectByValue(this.form,'targetlist',this.value,true);" /></td>
						</tr>

						<tr>
							<td class="column1"><label id="dirlabel" for="dir">{$lang_advlink_langdir}</label></td> 
							<td>
								<select id="dir" name="dir"> 
										<option value="">{$lang_not_set}</option> 
										<option value="ltr">{$lang_advlink_ltr}</option> 
										<option value="rtl">{$lang_advlink_rtl}</option> 
								</select>
							</td> 
						</tr>

						<tr>
							<td><label id="hreflanglabel" for="hreflang">{$lang_advlink_target_langcode}</label></td>
							<td><input type="text" id="hreflang" name="hreflang" value="" /></td>
						</tr>

						<tr>
							<td class="column1"><label id="langlabel" for="lang">{$lang_advlink_langcode}</label></td> 
							<td>
								<input id="lang" name="lang" type="text" value="" />
							</td> 
						</tr>

						<tr>
							<td><label id="charsetlabel" for="charset">{$lang_advlink_encoding}</label></td>
							<td><input type="text" id="charset" name="charset" value="" /></td>
						</tr>

						<tr>
							<td><label id="typelabel" for="type">{$lang_advlink_mime}</label></td>
							<td><input type="text" id="type" name="type" value="" /></td>
						</tr>

						<tr>
							<td><label id="rellabel" for="rel">{$lang_advlink_rel}</label></td>
							<td><select id="rel" name="rel"> 
									<option value="">{$lang_not_set}</option> 
									<option value="lightbox">Lightbox</option> 
									<option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option>
									<option value="nofollow">No Follow</option>
									<option value="tag">Tag</option>
								</select> 
							</td>
						</tr>

						<tr>
							<td><label id="revlabel" for="rev">{$lang_advlink_rev}</label></td>
							<td><select id="rev" name="rev"> 
									<option value="">{$lang_not_set}</option> 
									<option value="alternate">Alternate</option> 
									<option value="designates">Designates</option> 
									<option value="stylesheet">Stylesheet</option> 
									<option value="start">Start</option> 
									<option value="next">Next</option> 
									<option value="prev">Prev</option> 
									<option value="contents">Contents</option> 
									<option value="index">Index</option> 
									<option value="glossary">Glossary</option> 
									<option value="copyright">Copyright</option> 
									<option value="chapter">Chapter</option> 
									<option value="subsection">Subsection</option> 
									<option value="appendix">Appendix</option> 
									<option value="help">Help</option> 
									<option value="bookmark">Bookmark</option> 
								</select> 
							</td>
						</tr>

						<tr>
							<td><label id="tabindexlabel" for="tabindex">{$lang_advlink_tabindex}</label></td>
							<td><input type="text" id="tabindex" name="tabindex" value="" /></td>
						</tr>

						<tr>
							<td><label id="accesskeylabel" for="accesskey">{$lang_advlink_accesskey}</label></td>
							<td><input type="text" id="accesskey" name="accesskey" value="" /></td>
						</tr>
					</table>
				</fieldset>
			</div>

			<div id="events_panel" class="panel">
			<fieldset>
					<legend>{$lang_advlink_event_props}</legend>

					<table border="0" cellpadding="0" cellspacing="4">
						<tr>
							<td class="column1"><label for="onfocus">onfocus</label></td> 
							<td><input id="onfocus" name="onfocus" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onblur">onblur</label></td> 
							<td><input id="onblur" name="onblur" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onclick">onclick</label></td> 
							<td><input id="onclick" name="onclick" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="ondblclick">ondblclick</label></td> 
							<td><input id="ondblclick" name="ondblclick" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmousedown">onmousedown</label></td> 
							<td><input id="onmousedown" name="onmousedown" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmouseup">onmouseup</label></td> 
							<td><input id="onmouseup" name="onmouseup" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmouseover">onmouseover</label></td> 
							<td><input id="onmouseover" name="onmouseover" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmousemove">onmousemove</label></td> 
							<td><input id="onmousemove" name="onmousemove" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onmouseout">onmouseout</label></td> 
							<td><input id="onmouseout" name="onmouseout" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onkeypress">onkeypress</label></td> 
							<td><input id="onkeypress" name="onkeypress" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onkeydown">onkeydown</label></td> 
							<td><input id="onkeydown" name="onkeydown" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label for="onkeyup">onkeyup</label></td> 
							<td><input id="onkeyup" name="onkeyup" type="text" value="" /></td> 
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" id="insert" name="insert" value="{$lang_insert}" onClick="insertAction();" />
			</div>

			<div style="float: right">
				<input type="button" id="cancel" name="cancel" value="{$lang_cancel}" onClick="tinyMCEPopup.close();" />
			</div>
		</div>
    </form>
</body>
</html>
