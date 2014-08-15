var Cookie = window.Cookie = {
    set: function (name, value, expires, path, domain, secure) {
        document.cookie = name + "=" + escape(value) + ((expires) ? "; expires=" + expires.toUTCString() : "") + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");
    },

    get: function (name) {
        var prefix = name + "=",
            cookieStartIndex = document.cookie.indexOf(prefix),
            cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length);

        if (cookieStartIndex == -1)
            return null;
        if (cookieEndIndex == -1)
            cookieEndIndex = document.cookie.length;

        return unescape(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex));
    },

    erase: function (name, path, domain) {
        if (this.get(name)) {
            var d = new Date();
            d.setTime(0);
            this.set(name, '', d, path, domain);
        }
    }
};

function ins_ajax_load(ajax_link){
		$.ajax({
		   url: "/index.php",
		   type: "POST",
		   dataType: 'script',  
		   data: ajax_link
		 });		

}
// пример ins_ajax_load_target ("ca=exprice&task=show_price&good=<?=$_REQUEST['good'] ?>&4ajax=1", "#all_price");
function ins_ajax_load_target(ajax_link, itarget){
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

function ins_ajax_open(ajax_link, new_w, new_h){
	if (  new_w>0  &&  new_h  )	$.fn.colorbox({width:new_w, height:new_h, href:ajax_link, opacity:0.5});
	else $.fn.colorbox({href:ajax_link, opacity:0.5});
}

// AJAX-ссылка внутри fancybox
function ins_ajax_logout(){
	ajax_link="4ajax=1&c=out"; //alert (ajax_link); // return false;		
	$.ajax({
	   url: "/",
	   type: "POST",
	   dataType: 'script',  
	   data: ajax_link
	 });		
	/*
  $.get('/?c=out', function(data){
							location.reload(true);
    });*/
}

// AJAX-ссылка внутри colorbox
function ins_ajax_link_colorbox (ajax_link, new_w, new_h){
	$.fn.colorbox({width:new_w, height:new_h, href:ajax_link, opacity:0.5});
}
// AJAX-ссылка внутри fancybox
function ins_ajax_link_fancybox(ajax_link, ajax_id, ajax_fadeout, ajax_fadein, new_w, new_h, obj){
  var new_w20 = new_w+20; var new_h20 = new_h+20; 
  $.get(ajax_link, function(data){
		$('#'+ajax_id).fadeOut(ajax_fadeout, function() {
			
			// Animation complete. 			//$('#fancybox-outer').width(700);
			$('#fancybox-wrap').animate({	width: new_w20,	height: new_h20	}, 500, 'swing', function() {
																										  $('#fancybox-inner').width(new_w); // можно быстро
																										  $('#fancybox-inner').height(new_h); // можно быстро
																										  document.getElementById(ajax_id).innerHTML=data;
																										  $('#'+ajax_id).fadeIn(ajax_fadein);
																									  	});
		});
    });
}

function ins_ajax_login_validate(ins_this){
		// собираем введеные данные
		var name = $("#insite_login_name").attr("value");
		var pass = $("#insite_login_pass").attr("value");
		var ins_validate = $("#insite_login_validate").attr("name");
		ajax_link="4ajax_login=1&username="+name+"&passwd="+pass+"&c=in&return=/errrr404&force_session=1&"+ins_validate+"=1"; //alert (ajax_link); // return false;		
		over_fade('#wrapper_insite_login', '#wrapper_insite_login', '', 0.5, 'popup');
		$.ajax({
		   url: "/",
		   type: "POST",
		   dataType: 'script',  
		   data: ajax_link,
		   success: function(){ over_fade_hide(); }
		 });		
}

function ins_ajax_register_validate(ins_this){
		// собираем введеные данные
		var name = $("#insite_register_name").attr("value");
		var usersurname = $("#insite_register_usersurname").attr("value");
		var userparentname = $("#insite_register_userparentname").attr("value");
		var email = $("#insite_register_email").attr("value");
		var pass = $("#insite_register_password").attr("value");
		var pass2 = $("#insite_register_password2").attr("value");
		var username = $("#insite_register_username").attr("value");	// login
		var ins_validate = $("#insite_register_validate").attr("name");
		var ins_code = $("#insite_register_gbcode").attr("value");
		var CodeID = $("#insite_login_register_codeid").attr("value");
		
		// проверка входных данных
		var r_email = new RegExp("^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$" ,"i");
		$("#insite_register_server_answer").html('— Проверка данных...');
		if (  username==''  ){			$("#insite_register_server_answer").html('— Заполните поле «Логин»').jTypeWriter({duration:1.5});									return;		}
		if (  name==''  ){				$("#insite_register_server_answer").html('— Заполните поле «ФИО»').jTypeWriter({duration:1.5});										return;		}
		if (  email==''  ){				$("#insite_register_server_answer").html('— Заполните поле «E-mail»').jTypeWriter({duration:1.5});									return;		}
		if (  !r_email.exec(email)  ){	$("#insite_register_server_answer").html('— Укажите правильный электронный адрес').jTypeWriter({duration:1.5});						return;		}
		if (  pass==''  ){				$("#insite_register_server_answer").html('— Заполните поле «Пароль»').jTypeWriter({duration:1.5});									return;		}
		if (  pass.length < 5  ){		$("#insite_register_server_answer").html('— Пароль должен содержать не менее 5 символов ').jTypeWriter({duration:1.5});				return;		}
		if (  pass2==''  ){				$("#insite_register_server_answer").html('— Заполните поле «Подтверждение пароля»').jTypeWriter({duration:1.5});					return;		}
		if (  ins_code==''  ){			$("#insite_register_server_answer").html('— Заполните поле «Код безопасности»').jTypeWriter({duration:1.5});						return;		}
		if (  pass!=pass2  ){			$("#insite_register_server_answer").html('— «Пароль» и «Подтверждение пароля» должны совпадать').jTypeWriter({duration:1.5});		return;		}
		//$("#insite_register_server_answer").html('— '+name+pass+pass2+username+ins_validate+ins_code);	return;

		ajax_link="4ajax=1&name="+name+"&username="+username+"&usersurname="+usersurname+"&userparentname="+userparentname+"&email="+email+"&password="+pass+"&password2="+pass2+"&c=reg&task=saveRegistration&useractivation=0&gid=0&id=0&"+ins_validate+"=1&CodeID="+CodeID+"&gbcode="+ins_code;
		//$("#insite_register_server_answer").html('— '+ajax_link);	return;
		over_fade('#wrapper_insite_login', '#wrapper_insite_login', '', 0.5, 'popup');
		$.ajax({
		   url: "/",
		   type: "POST",
		   dataType: 'script',  
		   data: ajax_link,
		   success: function(){ over_fade_hide(); }
		 });
}


function ins_ajax_register_lost_pass(ins_this){
		// собираем введеные данные
		var username = $("#insite_login_checkusername").attr("value");
		var email = $("#insite_login_confirmEmail").attr("value");
		var ins_validate = $("#insite_login_validate").attr("name");
		
		// проверка входных данных
		var r_email = new RegExp("^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$" ,"i");
		$("#insite_register_server_answer").html('— Проверка данных...');
		if (  username==''  ){			$("#insite_register_server_answer").html('— Заполните поле «Логин»').jTypeWriter({duration:1.5});									return;		}
		if (  email==''  ){				$("#insite_register_server_answer").html('— Заполните поле «E-mail»').jTypeWriter({duration:1.5});									return;		}
		if (  !r_email.exec(email)  ){	$("#insite_register_server_answer").html('— Укажите правильный электронный адрес').jTypeWriter({duration:1.5});						return;		}
		//$("#insite_register_server_answer").html('— '+name+pass+pass2+username+ins_validate+ins_code);	return;

		ajax_link="4ajax=1&checkusername="+username+"&confirmEmail="+email+"&c=reg&task=sendNewPass&"+ins_validate+"=1";
		//$("#insite_register_server_answer").html('— '+ajax_link);	return;
		over_fade('#wrapper_insite_login', '#wrapper_insite_login', '', 0.5, 'popup');
		$.ajax({
		   url: "/",
		   type: "POST",
		   dataType: 'script',  
		   data: ajax_link,
		   success: function(){ over_fade_hide(); }
		 });
}


//  ДЛЯ РАБОТЫ КАПЧИ
function spamfixreload (id, code_id) { 
	var a= Math.floor(Math.random()*1000);
	var neuesbild = document.getElementById(id);
	neuesbild.src = "/lib/captcha/img.php?CodeID="+code_id+"&reload="+a; 
}


/* == TEXT HOVER EFFECT =============================================== */
$(function() {
/* gallery */
if ($.browser.msie && parseInt($.browser.version, 10) < 8){
	h= $(".gimg img").height()-20+"px";
	w= $(".gimg img").width()-5+"px";
	$(".gdesc").css({"height":h, "width":w});
}

$(".gimg").hover(function(){
	$("img", this).stop().animate({"opacity":0.7});
	$(this).find(".gdesc").show();	
},
function(){
	$("img", this).stop().animate({"opacity":1});
	$(this).find(".gdesc").hide();
});
/* end gal*/
});
/* == END OF TEXT HOVER EFFECT =============================================== */

/* == TRIM FUNCTION =============================================== */
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
/* == ENF OF TRIM FUNCTION =============================================== */


/* == FUNCTION ДЛЯ СОЗДАНИЯ ЭФФЕКТА ЗАКРЫТОЙ ОБЛАСТИ БЕЛЫМ ФОНОМ =============================================== */
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