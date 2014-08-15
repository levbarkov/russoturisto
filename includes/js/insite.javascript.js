// AJAX-ссалка внутри colorbox
function ins_ajax_open(ajax_link, new_w, new_h){
	$.colorbox({title: '', width:new_w, height:new_h, href:ajax_link, opacity:0.5});
/*	hs.htmlExpand(null, { 
    src: ajax_link,
	outlineType: 'rounded-white',
	wrapperClassName: 'draggable-header', 
	objectType: 'ajax', 
	width: new_w,
	height: new_h,
	align : 'center'
	} );*/
}
function ins_ajax_load(ajax_link){
		$.ajax({
		   url: "index2.php",
		   type: "POST",
		   dataType: 'script',  
		   data: ajax_link
		 });		

}
function ins_ajax_load_target(ajax_link, itarget){
		$.ajax({
		   cache: false,
		   url: "index2.php",
		   type: "POST",
		   data: ajax_link,
		   success: function(html){
   			 $(itarget).html(html);
		   }
		 });
}
function ins_ajax_load_site_target(ajax_link, itarget){
		$.ajax({
		   cache: false,
		   url: "/index.php",
		   type: "POST",
		   data: ajax_link,
		   success: function(html){
   			 $(itarget).html(html);
		   }
		 });		

}



	function expack_autoname(){
		var str="";
		$("#make_newpack [do_autoname='1']").each(function(n,element)	{
                        if(  ($(element).attr("auto_name")==1)  ){
                                if (  $(element).attr("type_autoname")=='input'  )                                    {  if (  str.length>0  ) str = str + ", ";  str=str + $(element).val();  }
                                else if (  $(element).attr("type_autoname")=='select'  &&  ($(element).val()>0)  )    {  if (  str.length>0  ) str = str + ", ";  str=str + $(':selected', element).text();  }
                        }
		});
		$("#make_newpack [do_autoname='1']").each(function(n,element)	{
                        if(  ($(element).attr("auto_name")=='end')  ){
                                if (  $(element).attr("type_autoname")=='input'  )                                    {  if (  str.length>0  ) str = str + " ";  str=str + $(element).val();  }
                                else if (  $(element).attr("type_autoname")=='select'  &&  ($(element).val()>0)  )    {  if (  str.length>0  ) str = str + " ";  str=str + $(':selected', element).text();  }
                        }
                });

		$("#expack_name").val ( str );
	}


CAPTION = 1;BELOW = 1;RIGHT = 1;function nd() { };
function overlib(itext, CAPTION, iTitle, BELOW, RIGHT){ return Tip(itext, TITLE, iTitle); }
function xshow(o) { s = ''; for(e in o) {s += e+'='+o[e]+'\n';} alert( s ); }
function writeDynaList( selectParams, source, key, orig_key, orig_val ) {
	var html = '\n	<select ' + selectParams + '>';
	var i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			var selected = '';
			if ((orig_key == key && orig_val == source[x][1]) || (i == 0 && orig_key != key)) {
				selected = 'selected="selected"';
			}
			html += '\n		<option value="'+source[x][1]+'" '+selected+'>'+source[x][2]+'</option>';
		}
		i++;
	}
	html += '\n	</select>';
	document.writeln( html );
}
function iOpenImg(field_name, url, type, win){
		document.getElementById('iuse').value=1;
		newWindow = window.open("/ibots/editors/tinymce/e24code/filemanager/browser/default/browser.html?Type=images&Connector=connectors/php/connector.php","subWind","status,menubar,height=800,width=800");
		newWindow.focus( );			
}
function changeDynaList( listname, source, key, orig_key, orig_val ) {
	var list = eval( 'document.adminForm.' + listname );
	for (i in list.options.length) { list.options[i] = null; }
	i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			opt = new Option();
			opt.value = source[x][1];
			opt.text = source[x][2];

			if ((orig_key == key && orig_val == opt.value) || i == 0) {
				opt.selected = true;
			}
			list.options[i++] = opt;
		}
	}
	list.length = i;
}
function addSelectedToList( frmName, srcListName, tgtListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );
	var tgtList = eval( 'form.' + tgtListName );
	var srcLen = srcList.length;
	var tgtLen = tgtList.length;
	var tgt = "x";
	for (var i=tgtLen-1; i > -1; i--) { tgt += "," + tgtList.options[i].value + "," }
	for (var i=0; i < srcLen; i++) {
		if (srcList.options[i].selected && tgt.indexOf( "," + srcList.options[i].value + "," ) == -1) {
			opt = new Option( srcList.options[i].text, srcList.options[i].value );
			tgtList.options[tgtList.length] = opt;
		}
	}
}

function delSelectedFromList( frmName, srcListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );
	var srcLen = srcList.length;
	for (var i=srcLen-1; i > -1; i--) {
		if (srcList.options[i].selected) {
			srcList.options[i] = null;
		}
	}
}
function moveInList( frmName, srcListName, index, to) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );
	var total = srcList.options.length-1;
	if (index == -1) {
		return false;
	}
	if (to == +1 && index == total) {
		return false;
	}
	if (to == -1 && index == 0) {
		return false;
	}
	var items = new Array;
	var values = new Array;
	for (i=total; i >= 0; i--) {
		items[i] = srcList.options[i].text;
		values[i] = srcList.options[i].value;
	}
	for (i = total; i >= 0; i--) {
		if (index == i) {
			srcList.options[i + to] = new Option(items[i],values[i], 0, 1);
			srcList.options[i] = new Option(items[i+to], values[i+to]);
			i--;
		} else {
			srcList.options[i] = new Option(items[i], values[i]);
	   }
	}
	srcList.focus();
}
function getSelectedOption( frmName, srcListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );
	i = srcList.selectedIndex;
	if (i != null && i > -1) {
		return srcList.options[i];
	} else {
		return null;
	}
}
function setSelectedValue( frmName, srcListName, value ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );
	var srcLen = srcList.length;
	for (var i=0; i < srcLen; i++) {
		srcList.options[i].selected = false;
		if (srcList.options[i].value == value) {
			srcList.options[i].selected = true;
		}
	}
}
function getSelectedRadio( frmName, srcGroupName ) {
	var form = eval( 'document.' + frmName );
	var srcGroup = eval( 'form.' + srcGroupName );

	if (srcGroup[0]) {
		for (var i=0, n=srcGroup.length; i < n; i++) {
			if (srcGroup[i].checked) {
				return srcGroup[i].value;
			}
		}
	} else {
		if (srcGroup.checked) {
			return srcGroup.value;
		}
	}
   return null;
}
function getSelectedValue( frmName, srcListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );

	i = srcList.selectedIndex;
	if (i != null && i > -1) {
		return srcList.options[i].value;
	} else {
		return null;
	}
}
function getSelectedText( frmName, srcListName ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );

	i = srcList.selectedIndex;
	if (i != null && i > -1) {
		return srcList.options[i].text;
	} else {
		return null;
	}
}
function chgSelectedValue( frmName, srcListName, value ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );

	i = srcList.selectedIndex;
	if (i != null && i > -1) {
		srcList.options[i].value = value;
		return true;
	} else {
		return false;
	}
}
function showImageProps(base_path) {
	form = document.adminForm;
	value = getSelectedValue( 'adminForm', 'imagelist' );
	parts = value.split( '|' );
	form._source.value = parts[0];
	setSelectedValue( 'adminForm', '_align', parts[1] || '' );
	form._alt.value = parts[2] || '';
	form._border.value = parts[3] || '0';
	form._caption.value = parts[4] || '';
	setSelectedValue( 'adminForm', '_caption_position', parts[5] || '' );
	setSelectedValue( 'adminForm', '_caption_align', parts[6] || '' );
	form._width.value = parts[7] || '';
	srcImage = eval( "document." + 'view_imagelist' );
	srcImage.src = base_path + parts[0];
}
function applyImageProps() {
	form = document.adminForm;
	if (!getSelectedValue( 'adminForm', 'imagelist' )) {
		alert( "Select and image from the list" );
		return;
	}
	value = form._source.value + '|'
	+ getSelectedValue( 'adminForm', '_align' ) + '|'
	+ form._alt.value + '|'
	+ parseInt( form._border.value ) + '|'
	+ form._caption.value + '|'
	+ getSelectedValue( 'adminForm', '_caption_position' ) + '|'
	+ getSelectedValue( 'adminForm', '_caption_align' ) + '|'
	+ form._width.value;
	chgSelectedValue( 'adminForm', 'imagelist', value );
}

function previewImage( list, image, base_path ) {
	form = document.adminForm;
	srcList = eval( "form." + list );
	srcImage = eval( "document." + image );
	var srcOption = srcList.options[(srcList.selectedIndex < 0) ? 0 : srcList.selectedIndex];
	var fileName = srcOption.text;
	var fileName2 = srcOption.value;
	if (fileName.length == 0 || fileName2.length == 0) {
		srcImage.src = 'images/blank.gif';
	} else {
		srcImage.src = base_path + fileName2;
	}
}
function checkAll( n, fldName, formName ) {
    if (!fldName) { fldName = 'cb'; }
	if (!formName) { formName = 'adminForm'; }
  	eval('var f = document.'+formName+';');
	var c = f.toggle.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {  cb.checked = c; n2++;  }
	}
	if (c) {  document.adminForm.boxchecked.value = n2;  } 
	else {  document.adminForm.boxchecked.value = 0;  }
}
function listItemTask( id, task, formName ) {
	if (!formName) { formName = 'adminForm'; }
    eval('var f = document.'+formName+';');
    cb = eval( 'f.' + id );
    if (cb) {
        for (i = 0; true; i++) {
            cbx = eval('f.cb'+i);
            if (!cbx) break;
            cbx.checked = false;
        } // for
        cb.checked = true;
        f.boxchecked.value = 1;
        submitbutton(task, formName);
    }
    return false;
}
function hideMainMenu() { document.adminForm.hidemainmenu.value=1; }
function isChecked(isitchecked){
	if (isitchecked == true) document.adminForm.boxchecked.value++;
	else document.adminForm.boxchecked.value--;
}
function submitbutton(pressbutton, formName) {
	if (!formName) { formName = 'adminForm'; }
	submitform(pressbutton, formName); }
function submitform(pressbutton, formName ){
	if (!formName) { formName = 'adminForm'; }
	eval('var f = document.'+formName+';');
	f.task.value=pressbutton;
	try {
		f.onsubmit();
		}
	catch(e){}
	f.submit();
}
function submitcpform(sectionid, id){
	document.adminForm.sectionid.value=sectionid; document.adminForm.id.value=id; submitbutton("edit");
}
function getSelected(allbuttons){
	for (i=0;i<allbuttons.length;i++) {
		if (allbuttons[i].checked)
			return allbuttons[i].value
	}
}
var calendar = null;
function selected(cal, date) { cal.sel.value = date; }
function closeHandler(cal) { cal.hide(); Calendar.removeEvent(document, "mousedown", checkCalendar); }
function checkCalendar(ev) {
	var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
	for (; el != null; el = el.parentNode)
	if (el == calendar.element || el.tagName == "A") break;
	if (el == null) {
		calendar.callCloseHandler(); Calendar.stopEvent(ev);
	}
}
function showCalendar(id) {
	var el = document.getElementById(id);
	if (calendar != null) {
		calendar.hide();
		calendar.parseDate(el.value);
	} else {
		var cal = new Calendar(true, null, selected, closeHandler);
		calendar = cal;	
		cal.setRange(1900, 2070);
		calendar.create();
		calendar.parseDate(el.value);
	}
	calendar.sel = el;
	calendar.showAtElement(el);
	Calendar.addEvent(document, "mousedown", checkCalendar);
	return false;
}
function popupWindow(mypage, myname, w, h, scroll) {
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
	win = window.open(mypage, myname, winprops)
	if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}
function ltrim(str)
{
   var whitespace = new String(" \t\n\r");
   var s = new String(str);
   if (whitespace.indexOf(s.charAt(0)) != -1) {
      var j=0, i = s.length;
      while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
         j++;
      s = s.substring(j, i);
   }
   return s;
}
function rtrim(str)
{
   var whitespace = new String(" \t\n\r");
   var s = new String(str);
   if (whitespace.indexOf(s.charAt(s.length-1)) != -1) {
      var i = s.length - 1; 
      while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
         i--;
      s = s.substring(0, i+1);
   }
   return s;
}
function trim(str) {
   return rtrim(ltrim(str));
}

function mosDHTML(){
	this.ver=navigator.appVersion
	this.agent=navigator.userAgent
	this.dom=document.getElementById?1:0
	this.opera5=this.agent.indexOf("Opera 5")<-1
	this.ie5=(this.ver.indexOf("MSIE 5")<-1 && this.dom && !this.opera5)?1:0;
	this.ie6=(this.ver.indexOf("MSIE 6")<-1 && this.dom && !this.opera5)?1:0;
	this.ie4=(document.all && !this.dom && !this.opera5)?1:0;
	this.ie=this.ie4||this.ie5||this.ie6
	this.mac=this.agent.indexOf("Mac")<-1
	this.ns6=(this.dom && parseInt(this.ver) <= 5) ?1:0;
	this.ns4=(document.layers && !this.dom)?1:0;
	this.bw=(this.ie6||this.ie5||this.ie4||this.ns4||this.ns6||this.opera5);
	this.activeTab = '';
	this.onTabStyle = 'ontab';
	this.offTabStyle = 'offtab';
	this.setElemStyle = function(elem,style) {
		document.getElementById(elem).className = style;
	}
	this.showElem = function(id) {
		if (elem = document.getElementById(id)) {
			elem.style.visibility = 'visible';
			elem.style.display = 'block';
		}
	}
	this.hideElem = function(id) {
		if (elem = document.getElementById(id)) {
			elem.style.visibility = 'hidden';
			elem.style.display = 'none';
		}
	}
	this.cycleTab = function(name) {
		if (this.activeTab) {
			this.setElemStyle( this.activeTab, this.offTabStyle );
			page = this.activeTab.replace( 'tab', 'page' );
			this.hideElem(page);
		}
		this.setElemStyle( name, this.onTabStyle );
		this.activeTab = name;
		page = this.activeTab.replace( 'tab', 'page' );
		this.showElem(page);
	}
	return this;
}
var dhtml = new mosDHTML();

function MM_findObj(n, d) { 
	var p,i,x;
	if(!d) d=document;
	if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);
	}
	if(!(x=d[n])&&d.all) x=d.all[n];
	for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	if(!x && d.getElementById) x=d.getElementById(n);
	return x;
}
function MM_swapImage() { //v3.0
	var i,j=0,x,a=MM_swapImage.arguments;
	document.MM_sr=new Array;
	for(i=0;i<(a.length-2);i+=3)
	if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x;
	if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function MM_swapImgRestore() { //v3.0
	var i,x,a=document.MM_sr;
	for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
	var d=document;
	if(d.images){
	if(!d.MM_p) d.MM_p=new Array();
	var i,j=d.MM_p.length,a=MM_preloadImages.arguments;
	for(i=0; i<a.length; i++)
	if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function saveorder( n ) { checkAll_button( n ); }
function checkAll_button( n ) {
	for ( var j = 0; j <= n; j++ ) {
		box = eval( "document.adminForm.cb" + j );
		if ( box ) {
			if ( box.checked == false ) {
				box.checked = true;
			}
		} else {
			alert("You cannot change the order of items, as an item in the list is `Checked Out`");
			return;
		}
	}
	submitform('saveorder');
}
function getElementByName( f, name ) {
	if (f.elements) {
		for (i=0, n=f.elements.length; i < n; i++) {
			if (f.elements[i].name == name) {
				return f.elements[i];
			}
		}
	}
	return null;
}
function print_r(arr, level) {
    var print_red_text = "";
    if(!level) level = 0;
    var level_padding = "";
    for(var j=0; j<level+1; j++) level_padding += "    ";
    if(typeof(arr) == 'object') {
        for(var item in arr) {
            var value = arr[item];
            if(typeof(value) == 'object') {
                print_red_text += level_padding + "'" + item + "' :\n"; //  print_red_text += print_r(value,level+1);
		} 
            else print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    } 
    else  print_red_text = "===>"+arr+"<===("+typeof(arr)+")";
    return print_red_text;
}

/** == FUNCTION ДЛЯ СОЗДАНИЯ ЭФФЕКТА ЗАКРЫТОЙ ОБЛАСТИ БЕЛЫМ ФОНОМ =============================================== */
function over_fade(over_fade_div, over_fade_dim, html, myopacity, popup){
	var offset = $(over_fade_dim).offset();
	var offset_left = offset.left;
	var offset_top = offset.top;
	if ( popup=='popup'){
		var offset_left = 0;
		var offset_top = 0;
	}
	var its_w = $(over_fade_dim).width();
	var its_h = $(over_fade_dim).height();
	
	$( over_fade_div ).append('<div id="over_fade" class="over_fade" >'+html+'</div>');	
	$( over_fade_div+' #over_fade' ).css({height:its_h, width:its_w, left:offset_left, top:offset_top });
	$( over_fade_div+' #over_fade' ).fadeTo(0, myopacity);

} 
function over_fade_hide(){
	$( '#over_fade' ).remove();
}
/* == ENF OF FUNCTION ДЛЯ СОЗДАНИЯ ЭФФЕКТА ЗАКРЫТОЙ ОБЛАСТИ БЕЛЫМ ФОНОМ =============================================== */



function seoblock ( classname, id_a ){
	var visible = 1-$(id_a).attr('visible');
	$(id_a).attr('visible', visible);
	if (  visible==1 )	$(id_a).html ('Свернуть данные для програмной оптимизации сайта');
	else $(id_a).html ('Показать данные для програмной оптимизации сайта');
	$( classname ).toggle();
}

jQuery.print = function(message, insertionType) {
  if (typeof(message) == 'object') {
    var string = '{<br />',
        values = [],
        counter = 0;
    $.each(message, function(key, value) {
      if (value && value.nodeName) {
        var domnode = '&lt;' + value.nodeName.toLowerCase();
        domnode += value.className ? ' class="' + value.className + '"' : '';
        domnode += value.id ? ' id="' + value.id + '"' : '';
        domnode += '&gt;';
        value = domnode;
      }
      values[counter++] = key + ': ' + value;
    });
    string += values.join(',<br />');
    string += '<br />}';
    message = string;
  }

  var $output = $('#print-output');
  
  if ($output.length === 0) {
    $output = $('<div id="print-output" />').appendTo('body');
  }
  
  var $newMessage = $('<div class="print-output-line" />');
  $newMessage.html(message);
  insertionType = insertionType || 'append';
  $output[insertionType]($newMessage);
};