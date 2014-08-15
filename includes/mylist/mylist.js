/* == MYLIST ========================================================== */
var bestAnimation = false;
var bestAmount = 0;

function getAbsoluteOffset(obj){
	var offsetX = 0;
	var offsetY = 0;
	var o = obj;
	while(o != document.body.offsetParent){
		offsetX += o.offsetLeft;
		offsetY += o.offsetTop;
		o = o.offsetParent;
	}
	return [offsetX, offsetY];
};

function bestTween(){
	var image = bestAnimation.image;
	var frameDivider = 6;
	var frameEnd = 10;

	if (bestAnimation.frame >= frameEnd){
		
		addTobest(bestAnimation.productId);
		if (bestAnimation.scrollBack){
			$(document).scrollTo( {top: bestAnimation.originalScrollTop + "px", left: bestAnimation.originalScrollLeft + "px"}, 700);
		};
		bestAnimationClear();
		return;
	}


	if (bestAnimation.frame < frameDivider){
		var progress = (bestAnimation.frame + 1) / frameDivider;
		image.style.top = parseInt(bestAnimation.original.y - (bestAnimation.original.y - bestAnimation.transition.y)*progress) + "px";
		image.style.left = parseInt(bestAnimation.original.x - (bestAnimation.original.x - bestAnimation.transition.x)*progress) + "px";
		image.style.width = parseInt(bestAnimation.original.w - (bestAnimation.original.w - bestAnimation.transition.w)*progress) + "px";
		image.style.height = parseInt(bestAnimation.original.h - (bestAnimation.original.h - bestAnimation.transition.h)*progress) + "px";
	} else {
		var progress = (bestAnimation.frame + 1 - frameDivider) / (frameEnd - frameDivider);
		image.style.top = parseInt(bestAnimation.transition.y - (bestAnimation.original.y - bestAnimation.target.y)*progress) + "px";
		image.style.left = parseInt(bestAnimation.transition.x - (bestAnimation.original.x - bestAnimation.target.x)*progress) + "px";
		image.style.width = parseInt(bestAnimation.transition.w - (bestAnimation.original.w - bestAnimation.target.w)*progress) + "px";
		image.style.height = parseInt(bestAnimation.transition.h - (bestAnimation.original.h - bestAnimation.target.h)*progress) + "px";

	}
	bestAnimation.frame++;
	setTimeout(bestTween, 40);
}


function addTobest(productId){
	//alert(productId);
  $.get("/?4ajax_module=mylist&mylist_task=put&mylist_comp=ex&mylist_parent="+productId, function(data){
		$('#mylist_div_wrapper').fadeOut('70', function() {
												//document.getElementById('shopbest').innerHTML=data;
												$("#mylist_div_wrapper").html(     data	   );
												// Animation complete.
												$('#mylist_div_wrapper').fadeIn(230);
											});
	});
	
	
}

function bestAnimationClear(){
	if (!bestAnimation) return;
	if (bestAnimation.image){
		bestAnimation.image.parentNode.removeChild(bestAnimation.image);
		delete bestAnimation.image;
	}
	bestAnimation = false;
};

function bestTriggerHandler(e){
	bestAnimationClear();
	
	var productId = (/mylist-trigger-([^\s^$]+)/.exec(this.className)[1]);
	var productImage = $(".mylist-image-" + productId).get(0);
	var best = document.getElementById("mylist");
	if (!best || !productImage){
		return false;	// РјС‹ СЃСЋРґР° РЅРµ РїРѕРїР°РґР°РµРј !!!
		addTobest(productId);
	}
	var productImageOffset = getAbsoluteOffset(productImage);
	var bestFloatingImage = document.createElement("img");
	bestFloatingImage.style.position = "absolute";
	bestFloatingImage.style.left = productImageOffset[0] + "px";
	bestFloatingImage.style.top = productImageOffset[1] + "px";
	bestFloatingImage.src = productImage.src;
	document.body.appendChild(bestFloatingImage);
	
	bestAnimation = new Object();
	
	bestAnimation.image = bestFloatingImage;
	
	bestAnimation.original = {
		x: productImageOffset[0],
		y: productImageOffset[1],
		w: productImage.offsetWidth,
		h: productImage.offsetHeight
	};
	
	bestAnimation.transition = {
		x: productImageOffset[0] - parseInt(productImage.offsetWidth * .05),
		y: productImageOffset[1] - parseInt(productImage.offsetHeight * .05),
		w: parseInt(productImage.offsetWidth * 1.1),
		h: parseInt(productImage.offsetHeight * 1.1)
	};

	bestOffset = getAbsoluteOffset(best);
	
	bestAnimation.target = {
		x: bestOffset[0] + 110,
		y: bestOffset[1] + 10,
		w: 10,
		h: parseInt(productImage.offsetHeight * 10 / productImage.offsetHeight)
	};
	
	bestAnimation.frame = 0;
	bestAnimation.productId = productId;
	

	bestAnimation.originalScrollTop = $(window).scrollTop();
	bestAnimation.originalScrollLeft = $(window).scrollLeft();

	if (bestAnimation.originalScrollTop > bestOffset[1]){
		bestAnimation.scrollBack = true;
		$(document).scrollTo( '#mylist', 300, {onAfter: bestTween});
	} else {
		bestAnimation.scrollBack = false;
		bestTween();
	};
	
	if (e.stopPropagation) e.stopPropagation();
	if (e.preventDefault) e.preventDefault();
};

$(document).ready(function(){
	$(".mylist-trigger").click(bestTriggerHandler);
});
/* == END OF MYLIST =================================================== */
