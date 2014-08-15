/*****************************************
 * Checkbox Area Select:  version 1.2 Created by Harry Pottash
 ****************************************/

/*globals $, document */
$.fn.checkboxAreaSelect = function(){
    var cbAS_startX;  /* where the click started, X & Y */
    var cbAS_startY;
    var cbAS_mouseIsDown; /*flag to record if mouse is currently draging */

    /*when a mouse clicks down, prepair to start a drag*/
    $(document).mousedown(function(e){
        cbAS_startX = e.pageX;
        cbAS_startY = e.pageY; /*record where the mouse started */
        $("body").append("<div id='cbAS_dragbox'><input type='text' id='cbAS_selectDistractor' value='' style='opacity:.01; visible:hidden; width=0px; height=0px; '></div>"); /*create a graphic indicator of select area */
        $("#cbAS_dragbox").css({ 
							"background-image":"url(/includes/checkbox_area_select/selectbg.png)", 
                            position:"absolute", 
                            left: cbAS_startX + "px", 
			    			top: cbAS_startY + "px", 
                            width: "0px", 
                            height: "0px"});
					        cbAS_mouseIsDown = true; /*flag that the mouse is down */
	$("#cbAS_selectDistractor").select();/*The select Distractor keeps chrome/safari from also drawing there own highlight boxes*/

    });/*close mousedown*/
    
    /*if the mouse is moving run this*/
    $(document).mousemove(function(e){
        if(cbAS_mouseIsDown){ /*check if they are currently dragging the mouse*/
            var dragHeight = e.pageY - cbAS_startY;
            var dragWidth = e.pageX - cbAS_startX; /*find the x & y diff of where they are and where they started */

	$("#cbAS_selectDistractor").select();/*The select Distractor keeps chrome/safari from also drawing there own highlight boxes*/

	    /*make the colored box fit the mouse movements */
            if (dragHeight < 0 && dragWidth < 0){ /* up and to the left */
		$("#cbAS_dragbox").css({ height: -dragHeight ,  width: -dragWidth, left: e.pageX, top: e.pageY});
	    } else if (dragHeight < 0 && dragWidth > 0){ /*up and to the right */
		$("#cbAS_dragbox").css({ height: -dragHeight ,  width: dragWidth, left: cbAS_startX, top: e.pageY});
	    } else if (dragHeight > 0 && dragWidth < 0){ /* down and to the left */
		$("#cbAS_dragbox").css({ height: dragHeight ,  width: -dragWidth, left: e.pageX, top: cbAS_startY});
	    } else { /* down and to the right */
		$("#cbAS_dragbox").css({ height: dragHeight , width: dragWidth, left: cbAS_startX, top: cbAS_startY});
	    }
        }
    });

    /* when they release the mouse button, check if they have dragged over any checkboxes,
     If they have, do work on them. Also reset things that started on mouse-down */
    $(document).mouseup(function(e){
        cbAS_mouseIsDown = false; /*cleare currently dragging flag */
        $("#cbAS_dragbox").remove(); /*get rid of select box */
        var endX = e.pageX;
        var endY = e.pageY; /*discover where mouse was released x&y */
	
	/*for each checkbox on the page check if its within the drag-area*/
        $(":checkbox").each(function(){
            var box_top = $(this).position().top + ($(this).height()/2);   /*checkboxes have an area */
            var box_left = $(this).position().left + ($(this).width()/2);  /*so find their centerpoint */
            if( (box_top > cbAS_startY && box_top < endY ) || (box_top < cbAS_startY && box_top > endY )){
		if( (box_left > cbAS_startX && box_left < endX ) || (box_left < cbAS_startX && box_left > endX )){
		    /*if checkbox was in the drag area */
		    if(e.shiftKey){
				boxchecked_val = ($("input[name=boxchecked]").val()*1.0)-1.0;
				$("input[name=boxchecked]").val(  boxchecked_val  );
				$(this).attr("checked",false); /*uncheck due to shift key */	  
            } else {
				boxchecked_val = ($("input[name=boxchecked]").val()*1.0)+1.0;
				$("input[name=boxchecked]").val(  boxchecked_val  );
				$(this).attr("checked",true);  /*check the box */
            }
		}
	    } 
	    
	});/*close each*/
    });/*close mouseup*/
};/*close checkboxAreaSelect*/