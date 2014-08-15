/* == БИБЛИОТЕКА ================== */
function wrapping() {
	var win=$(window).height()-40;
	var div=$("#tpl-counter").height();
	if($("#tpl-counter").height()>$(window).height()) {$("#tpl-counter").height(win);
	var flag=1}
	else $("#tpl-counter").height("100%")
}
function swap(num){

    var obj=document.getElementById(num);
	var id_num=/\d+/.exec(num); //alert(id_num);
	if(!(id_num<11||id_num>22)) for(var i=11;i<=22;i++) if(i!=id_num) {var obj_i=document.getElementById('morem'+i); if(obj_i) {obj_i.style.display='none'; obj_i.style.visibility='hidden';}
	var obj_i=document.getElementById('linkmorem'+i); if(obj_i) {obj_i.style.display=''; obj_i.style.visibility='visible';}}
	if(id_num<11||id_num>22) for(var i=23;i<=220;i++) if(i!=id_num) {var obj_i=document.getElementById('morem'+i); if(obj_i) {obj_i.style.display='none'; obj_i.style.visibility='hidden';}
	var obj_i=document.getElementById('linkmorem'+i); if(obj_i) {obj_i.style.display=''; obj_i.style.visibility='visible';}}	
    
	if(obj.style.visibility=='hidden'){
          obj.style.display='';
          obj.style.visibility='visible';
    }else{
          obj.style.display='none';
          obj.style.visibility='hidden';
    }
  }
  
function swapMore(oid){
	wrapping();
	swap('more'+oid);
	swap('linkmore'+oid);
	wrapping();
	return false;
}
function toggleBottomLine(){
	$("#tpl-counter").height(500);
	if($("#tpl-bottomline-wrapper").is(":visible")){
		$("#tpl-bottomline-wrapper").slideUp(300);
		$("#tpl-bottomline-label").html("Библиотека");
		setTimeout(function(){$("#tpl-bottom-overwrap").removeClass("grow");$("#tpl-map-pixline").css("display", "none");},300);
	} else {
		setTimeout(function(){$("#tpl-bottom-overwrap").addClass("grow");$("#tpl-map-pixline").css("display", "block");},100);
		$("#tpl-bottomline-wrapper").slideDown(300);
		$("#tpl-bottomline-label").html("Спрятать");
	};
	return false;
}
/* == END//БИБЛИОТЕКА ================== */
function toggleCatalogState(){
	if (!$("#home-catalog-wrapper:not(:animated)").size()) return false;
	if ($("#home-catalog-block").hasClass("closed")){
		$("#home-catalog-wrapper").slideDown({duration: 200, easing: "easeInCubic"});
		$("#home-catalog-block").removeClass("closed");
	} else {
		$("#home-catalog-wrapper").slideUp({duration: 200, easing: "easeOutCubic"});
		$("#home-catalog-block").addClass("closed");
	};
	return false; //suppress link
};

function toggleInnerCatalogState(){
	if (!$("#cnt-catalog-wrapper:not(:animated)").size()) return false;
	if ($("#cnt-catalog-block").hasClass("closed")){
		$("#cnt-catalog-wrapper").slideDown({duration: 200, easing: "easeInCubic"});
		$("#cnt-catalog-block").removeClass("closed");
	} else {
		$("#cnt-catalog-wrapper").slideUp({duration: 200, easing: "easeOutCubic"});
		$("#cnt-catalog-block").addClass("closed");
	};
	return false; //suppress link
};

$(document).ready(function(){
	$(".cnt-product-price-additional-1").mouseover(function(){
		$(document.body).addClass("js-balloon-state-1");
		$(document.body).removeClass("js-balloon-state-2")
		$(document.body).removeClass("js-balloon-state-3")
	});
	$(".cnt-product-price-additional-2").mouseover(function(){
		$(document.body).addClass("js-balloon-state-2");
		$(document.body).removeClass("js-balloon-state-1")
		$(document.body).removeClass("js-balloon-state-3")
	});
	$(".cnt-product-price-label").mouseover(function(){
		$(document.body).addClass("js-balloon-state-3");
		$(document.body).removeClass("js-balloon-state-1")
		$(document.body).removeClass("js-balloon-state-2")
	});
});



function BrandColor(brandId,doit)
{
	if(doit=="over")
	{
		$("#cnt-brand-logo-shad-"+brandId).addClass("js-hidden");
		$("#cnt-brand-logo-norm-"+brandId).removeClass("js-hidden");
	}
	if(doit=="out")
	{
		$("#cnt-brand-logo-shad-"+brandId).removeClass("js-hidden");
		$("#cnt-brand-logo-norm-"+brandId).addClass("js-hidden");
	}

}





$(document).ready(function(){
$("#alf-2").css('display','none');
$("#alf-link1").addClass('active');
	$("#alf-link1").click(function(){
$("#alf-2").hide(200);
$("#alf-1").show(200);
$("#alf-link1").addClass('active');
$("#alf-link2").removeClass('active');
});
	$("#alf-link2").click(function(){
$("#alf-1").hide(200);
$("#alf-2").show(200);
$("#alf-link2").addClass('active');
$("#alf-link1").removeClass('active');

});
	
});