/**************************
* 
* CMS Insite - ajax module for shop cart
* 
**************************/

var lang = 'ru';
var cartType = 'full'; // full || small
var goodsGroup = '1';

var langTxt = new Array(10);
langTxt['delAll'] = "Очистить корзину";
langTxt['select'] = "Вы выбрали:";
langTxt['empty'] = "Кол-во товара: 0 шт.<br>Сумма: 0 руб.";
langTxt['confirm'] = "Вы уверены?";
langTxt['continue'] = "OK";
langTxt['yes'] = "Да";
langTxt['cancel'] = "Отмена";
langTxt['cookieError'] = "Для нормальной работы в браузере должны быть включены cookies.";
langTxt['delete'] = "Удалить";
langTxt['delGoods'] = "Удалить товар";
langTxt['goods'] = "товар";
langTxt['count'] = "Кол-во";
langTxt['sumTotal'] = "Общая сумма:";
langTxt['executeOrder'] = "Оформить заказ";
langTxt['changeCount'] = "Изменить количество";

function changeCartQty(cartid, qty){
  $.get("/?4ajax_module=trash&mycart_task=changeqty&mycart_cartid="+cartid+"&mycart_qty="+qty, function(data){
		$('#shopCart').fadeOut('70', function() {
			document.getElementById('shopCart').innerHTML=data;
			// Animation complete.
			$('#shopCart').fadeIn(230);
		});
    }
);
}
function delFromCart(cartid){
  $.get("/?4ajax_module=trash&mycart_task=del1&mycart_cartid="+cartid, function(data){
		$('#shopCart').fadeOut('70', function() {
			document.getElementById('shopCart').innerHTML=data;
			// Animation complete.
			$('#shopCart').fadeIn(230);
		});
    }
);
}



var helper = '<div id="stuffHelper"><center><div id="stuffHelperName"><b></b></div>'
  +"\n"+'<div>'+langTxt['count']+' <input type="text" size="2" id="stuffCount" name="count" value="1" maxlength="3" />'
  +"\n"+'<img class="field-arr-up" src="/includes/shopkeeper/images/arr_up.gif" width="17" height="9" alt="" />'
  +'<img class="field-arr-down" src="/includes/shopkeeper/images/arr_down.gif" width="17" height="9" alt="" />'
  +"\n"+'</div><div><button id="confirmButton">'+langTxt['continue']+'</button> '
  +"\n"+'<button>'+langTxt['cancel']+'</button></div></center></div>'
  +"\n";

if (navigator.cookieEnabled==false){
  alert(langTxt['cookieError']);
}
function checkKey(e){
  var key_code = e.which ? e.which : e.keyCode;
  if((key_code>47&&key_code<58)||key_code==8){
    return true;
  }else{
    return false;
  }
}
function getPosition(el){
	var p = { x: el.offsetLeft, y: el.offsetTop };
	while (el.offsetParent){
		el = el.offsetParent;
		p.x += el.offsetLeft;
		p.y += el.offsetTop;
		if (el != document.body && el != document.documentElement){
			p.x -= el.scrollLeft;
			p.y -= el.scrollTop;
		}
	}
	return p;
}
function getCenterPos(elA,elB){
  posB = new Object();
  cntPos = new Object();
  posB = getPosition(elB);
  var correct;
  
  cntPos.y = Math.round(($(elB).outerHeight()-$(elA).outerHeight())/2)+posB.y;
  cntPos.x = Math.round(($(elB).outerWidth()-$(elA).outerWidth())/2)+posB.x;
  
  if(cntPos.x+$(elA).outerWidth()>$(window).width()){
    cntPos.x = Math.round($(window).width()-$(elA).outerWidth())-2;
  }
  if(cntPos.x<0){
    cntPos.x = 2;
  }
    
  return cntPos;
}

function changeCount(fieldid,action){
  if(action==1){	var num = parseInt($('#'+fieldid).attr('value'))+1;  }
  else{				var num = parseInt($('#'+fieldid).attr('value'))-1;  }
  if(num>=1){ $('#'+fieldid).val(num);  }
}

function deleteItem(num,el){
  $('#stuffHelper').remove();
  $('body').append(helper);

  elHelper = document.getElementById('stuffHelper');
  boxPos = getCenterPos(elHelper,el);
  
  $('b','#stuffHelperName').text(langTxt['confirm']);
  $('div:eq(1)','#stuffHelper').remove();
  $('button:eq(1)','#stuffHelper').click(function(){
    $('#stuffHelper').fadeOut(300,function(){$(this).remove()});
    return false;
  });
  $('#confirmButton').text(langTxt['yes']).click(function(){	/*  $('#stuffHelper').fadeOut(500).remove(); */
    $('#stuffHelper').fadeOut(500,function(){	delFromCart(num);	$('#stuffHelper').remove();		});
  });
  $('#stuffHelper').css({'top':boxPos.y+'px','left':boxPos.x+'px'}).fadeIn(500);
}


function recountItem(id,el){
  $('#stuffHelper').remove();
  $('body').append(helper);
  
  $('img.field-arr-up:eq(0)','#stuffHelper').click(function(){
    changeCount('stuffCount',1);
  });
  $('img.field-arr-down:eq(0)','#stuffHelper').click(function(){
    changeCount('stuffCount',2);
  });
  $('#stuffCount').keypress(function(e){
    return checkKey(e);
  });
  $('button:eq(1)','#stuffHelper').click(function(){
    $('#stuffHelper').fadeOut(300,function(){$(this).remove()});
    return false;
  });

  elHelper = document.getElementById('stuffHelper');
  boxPos = getCenterPos(elHelper,el);
  
  $('#stuffHelperName').remove();
  $('#stuffCount').val($(el).text());
  $('#stuffHelper').css({'top':boxPos.y+'px','left':boxPos.x-40+'px'}).fadeIn(500);
  
  $('#confirmButton').click(function(){	//id - cartid, 
  	var count = $('#stuffCount').val();
  	$('#stuffHelper').fadeOut(500,function(){	changeCartQty(id,count);	});
  });
}

function toCart(id, price1, mycart_options, el){
  var name = $('#stuff_'+id+'_name').text().replace(',','.');
  var price = parseFloat($('#stuff_'+id+'_price').text().replace(',','.'));

  $('#stuffHelper').remove();
  
  $('body').append(helper);
  $('img.field-arr-up:eq(0)','#stuffHelper').click(function(){
    changeCount('stuffCount',1);
  });
  $('img.field-arr-down:eq(0)','#stuffHelper').click(function(){
    changeCount('stuffCount',2);
  });
  $('#stuffCount').keypress(function(e){
    return checkKey(e);
  });

  $('button:eq(1)','#stuffHelper').click(function(){
    $('#stuffHelper').fadeOut(300,function(){$(this).remove()});
    return false;
  });
  
  elHelper = document.getElementById('stuffHelper');
  btPos = getCenterPos(elHelper,el);
  
  $('b','#stuffHelperName').text(name);
  $('#stuffHelper').css({'top':btPos.y+'px','left':btPos.x+'px'}).fadeIn(500);
    
  $('#confirmButton').click(function(){
  
    
    var count = parseInt($('#stuffCount').val());

    var cart = document.getElementById('shopCart');    
    cartPos = getCenterPos(elHelper,cart);
	
    $('#stuffHelper').animate({
        top: cartPos.y+'px',
        left: cartPos.x+'px'
      }).fadeOut(500,function(){
		// 	alert (id);  alert (count); alert (price1);
		$.get("/?4ajax_module=trash&mycart_task=put&mycart_id="+id+"&mycart_qty="+count+"&mycart_price="+price1+"&mycart_options="+mycart_options, function(data){
				$('#shopCart').fadeOut('70', function() {
					document.getElementById('shopCart').innerHTML=data;
					$('#shopCart').fadeIn(230);
				});
			}
		);

    });
  });
}

$(document).ready(function(){ ; }
); 