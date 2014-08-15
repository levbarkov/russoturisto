<?php

class ctrlEnter{
	function go(){
		global $reg, $option, $task;
		
		$reg_submit_event_apply = " submitbutton('apply'); ";
		$reg_submit_event_save  = " submitbutton('save'); ";
		
		$doeditor = false;
		if		(  strcmp($option, "typedcontent")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;
		else if (  strcmp($option, "typedcontent")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
		else if (  strcmp($option, "typedcontent")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
		
		else if (  strcmp($option, "content")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
		else if (  strcmp($option, "content")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
		else if (  strcmp($option, "content")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;
				
		else if (  strcmp($option, "modules")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
		else if (  strcmp($option, "modules")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
		else if (  strcmp($option, "modules")==0  &&  strcmp($task, "new")==0  ) $doeditor = true; 
		
		else if (  strcmp($option, "excat")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
		else if (  strcmp($option, "excat")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;
		
		else if (  strcmp($option, "exgood")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
		else if (  strcmp($option, "exgood")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;
		
		else if (  strcmp($option, "excfg")==0  &&  strcmp($task, "cfg")==0  ) { $doeditor = true; $reg_submit_event_apply = " submitbutton('save'); ";  }
		else if (  strcmp($option, "shopcfg")==0  &&  strcmp($task, "cfg")==0  ) { $doeditor = true; $reg_submit_event_apply = " submitbutton('save'); ";  }
		else if (  strcmp($option, "themes")==0  ) { $doeditor = true; $reg_submit_event_save = " submitbutton('savecfg'); ";  }
		
		else if (  strcmp($option, "promo")==0  ) { $doeditor = true; $reg_submit_event_apply = " submitbutton('save'); ";  }

		else if (  strcmp($option, "menus")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
		
		else if (  strcmp($option, "backlinkcfg")==0  &&  strcmp($task, "cfg")==0  ) { $doeditor = true; $reg_submit_event_apply = " submitbutton('save'); ";  }
		
		else if (  strcmp($option, "icat")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
		else if (  strcmp($option, "icat")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
		else if (  strcmp($option, "icat")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;
		
		else if (  strcmp($option, "config")==0  &&  strcmp($task, "cfg")==0  ) { $doeditor = true; $reg_submit_event_save = $reg_submit_event_apply = " submitbutton('save'); ";  }

                else if (  strcmp($option, "file")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
                else if (  strcmp($option, "file")==0  &&  strcmp($task, "cfg")==0  ) { $doeditor = true; $reg_submit_event_apply = " submitbutton('savecfg'); ";   $reg_submit_event_save = " submitbutton('savecfg'); ";    }	

		else if (  strcmp($option, "foto")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true;
		else if (  strcmp($option, "exfoto")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true;
		else if (  strcmp($option, "exfoto")==0  &&  strcmp($task, "new")==0  ) $doeditor = true;
		
		else if (  strcmp($option, "names")==0  &&  strcmp($task, "edit")==0  ) { $doeditor = true;   }
		else if (  strcmp($option, "names")==0  &&  strcmp($task, "editA")==0  ) { $doeditor = true;   }
		else if (  strcmp($option, "names")==0  &&  strcmp($task, "new")==0  ) { $doeditor = true; }
		else if (  strcmp($option, "names")==0  &&  strcmp($task, "cfg")==0  ) { $doeditor = true; $reg_submit_event_apply = " submitbutton('savecfg'); ";   $reg_submit_event_save = " submitbutton('savecfg'); ";    }	
		
		else if (  strcmp($option, "names_prop")==0  &&  strcmp($task, "editA")==0  ) $doeditor = true; 
		else if (  strcmp($option, "names_prop")==0  &&  strcmp($task, "edit")==0  ) $doeditor = true; 
		else if (  strcmp($option, "names_prop")==0  &&  strcmp($task, "new")==0  ) $doeditor = true; 
		
		else if (  strcmp($option, "nopage")==0  &&  strcmp($task, "cfg")==0  ) { $doeditor = true; $reg_submit_event_apply = " submitbutton('save'); ";  }

                else if (  strcmp($option, "foto")==0  &&  strcmp($task, "fotocat_edit")==0  ) { $doeditor = true; $reg_submit_event_save = " submitbutton('fotocat_save'); "; $reg_submit_event_apply = " submitbutton('fotocat_apply'); ";  }
		
		$reg['doCtrlEnter'] = $doeditor;
		$reg['submit_apply_event'] = $reg_submit_event_apply;
		$reg['submit_save_event'] = $reg_submit_event_save;
		
		if (  $doeditor  ){
			$reg['tinymce_ctrlEnter_handler'] = '
				setup : function (ed) {
					ed.onKeyPress.add(
						function (ed, evt) {
							//alert("Editor-ID: "+ed.id+"\nEvent: "+evt.keyCode);
							if((evt.ctrlKey) && ((evt.keyCode==10)||(evt.keyCode==13))) {   '.$reg['submit_save_event'].'   }
							if((evt.ctrlKey) && (evt.charCode==32)) {   '.$reg['submit_apply_event'].'   }
							// Do some great things here...
						}
					);
				},
			';
		} else $reg['tinymce_ctrlEnter_handler'] = '';
		
	}
}


?>
