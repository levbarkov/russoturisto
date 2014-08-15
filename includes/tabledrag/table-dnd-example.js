$(document).ready(function() {
    // Initialise the table 1
    $("#table-1").tableDnD({
      onDragClass: "myDragClass"
    });
    
    // Initialise the table 2
    $("#table-2").tableDnD({
      onDragClass: "myDragClass",
      dragHandle: "dragHandle"
    });

    $("#ajax_table_drug_td tr").hover(function() {
		$(this.cells).each(function() {
			if (  $(this).hasClass('drugme')   ) $(this).addClass('showDragHandle');
		});
    }, function() {
		$(this.cells).each(function() {
			if (  $(this).hasClass('drugme')   ) $(this).removeClass('showDragHandle');
		});
    });


    $("#ajax_table_drug tr").hover(function() {
		$(this.cells).each(function() {
			if (  $(this).hasClass('drugme')   ) $(this).addClass('showDragHandle');
		});
    }, function() {
		$(this.cells).each(function() {
			if (  $(this).hasClass('drugme')   ) $(this).removeClass('showDragHandle');
		});
    });

    // Initialise the table 3    
    $("#ajax_table_drug").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
          var rows = table.tBodies[0].rows;
          var w = "";
          for (var i = 0; i < rows.length; i++) {
            w += rows[i].id + ";";
          }
        
          $.ajax({
        		type: "POST",
         		url: "/iadmin/ajax_drug_table.php",
         		timeout: 5000,
         		data: "t="+$("#ajax_table_drug").attr("ajax_table_drug_table")+"&id="+$("#ajax_table_drug").attr("ajax_table_drug_id")+"&o="+$("#ajax_table_drug").attr("ajax_table_drug_order")+"&w=" + w,
         		success: function(data){$("div#upd-dnd").html(data);},
         		error: function(data){$("div#upd-dnd").html("Error" + data);}
         	});
        }
  	});
	
	
    $("#ajax_table_drug_td").tableDnD({
	    onDragClass: "myDragClass",
        dragHandle: "dragHandle",
	    onDrop: function(table, row) {
          var rows = table.tBodies[0].rows;
          var w = "";
          for (var i = 0; i < rows.length; i++) {
            w += rows[i].id + ";";
          }
        
          $.ajax({
        		type: "POST",
         		url: "/iadmin/ajax_drug_table.php",
         		timeout: 5000,
         		data: "t="+$("#ajax_table_drug_td").attr("ajax_table_drug_table")+"&id="+$("#ajax_table_drug_td").attr("ajax_table_drug_id")+"&o="+$("#ajax_table_drug_td").attr("ajax_table_drug_order")+"&w=" + w,
         		success: function(data){$("div#upd-dnd").html(data);},
         		error: function(data){$("div#upd-dnd").html("Error" + data);}
         	});
        }
  	});



});