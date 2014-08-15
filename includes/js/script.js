//charset=utf-8;


//sdneo.browser Калдынушка
var matched,browser;
jQuery.uaMatch=function(ua){
ua = ua.toLowerCase();
var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) || /(webkit)[ \/]([\w.]+)/.exec( ua ) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
/(msie) ([\w.]+)/.exec( ua ) || ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) || [];
return {browser:match[1]||"",version:match[2]||"0"};
};
matched = jQuery.uaMatch( navigator.userAgent );
browser = {};
if (matched.browser){browser[matched.browser]=true; browser.version=matched.version;}
if(browser.chrome ){browser.webkit=true;}else if(browser.webkit){browser.safari=true;}
jQuery.browser = browser;
//sdneo


$(function()
{
	//
	$(document).on('submit','#jq_form, #jq_form2', function(event){
		var forma = $(this);
		$.ajax({
			type: "POST",
			url: "/",
			data: forma.serialize(),
			success: function(data){
				forma.find(".jq_data").html(data);
				forma.find(".jq_data .true").fadeOut(9000);
			}
		});
		event.preventDefault();
	});
	//
	$('#tabs a').click(function(){tabs($(this).index());});

});



function tabs(n){
	$('.tabx').hide();
	$('.tabx').eq(n).fadeIn();
	$('#tabs a').removeClass('active');
	$('#tabs a').eq(n).addClass('active');
}

function checkURL(){
	var hash = window.location.hash.replace('#','');
	if(!hash) hash=0;
	tabs(hash);
}

//  capcha
function spamfixreload (id, code_id){
	var a= Math.floor(Math.random()*1000);
	var neuesbild = document.getElementById(id);
	neuesbild.src = "/lib/captcha/img.php?CodeID="+code_id+"&reload="+a; 
}















