<?
defined( '_VALID_INSITE' ) or die( 'ACCESS ERROR' );
global $reg; $reg['conf'] = new config($reg['db']);

switch($reg['task'])
{
        case 'apply':
	case 'save':		load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', 'Конфигурация сайта', "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', 'Конфигурация сайта', "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						if (   isset($_REQUEST['returnme'])  )  {
							mosRedirect( $conf->returnme_url($_REQUEST['returnme']), "Настройки удалены" );		return;
						}
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case "cfg":
	default: 		show_config(); break;
}

function show_config() {
global $reg;
	?>
	<script language="javascript">
		function do_changed (id){ 
			document.getElementById('cb'+id).checked = true; 
		}
	</script>

	<form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> action="index2.php" method="post" name="adminForm">
	<table class="adminheading"><tr><td width="100%"><?
		
		
		$time = date("d.m.Y H:i:s");
		
		$iway[0]->name="Конфигурация сайта {$time}";
		$iway[0]->url="";

		i24pwprint_admin ($iway);
	?></td></tr></table>
	
	<? 	 $reg['conf']->show_config('main', ""); ?>
	
	
	<input type="hidden" name="ca" value="<?=$reg['ca'];?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="submit" style="display:none;" />
	<input type="hidden" name="hidemainmenu" value="0" />
	</form><? 
}

function save_config($reg){	//ggtr5($_REQUEST);
        global $reg; $reg['conf']->read();
		foreach (  $_REQUEST['cid'] as $index  ){
			$i24r = new mosDBTable( "#__config", "id", $reg['db'] );
			$i24r->id = $_REQUEST['id'][$index];
			$i24r->name = $_REQUEST['name'][$index];
			$i24r->val = $_REQUEST['val'][$index];
			$i24r->desc = $_REQUEST['desc'][$index];
			$i24r->ordering = $_REQUEST['ordering'][$index];
			$i24r->component = $_REQUEST['component'][$index];
			//ggtr5 ($i24r);
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		}
		mosRedirect( 'index2.php?ca='.$reg['ca'], "Конфигурация удачно сохранена" );
		return;
	/*	ggtr5 ($reg['conf']);
    [vars] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 1
                    [desc] => param101
                    [name] => name1
                    [val] => val10778
                    [component] => main
                )

            [1] => stdClass Object
                (
                    [id] => 2
                    [desc] => desss2
                    [name] => nnamma2
                    [val] => vaaala2
                    [component] => main
                )

        )
*/
	foreach($_REQUEST as $field_name=>$field_value){
            if(preg_match("/(desc|name|val|component)_([0-9]+)/", $field_name, $match)){
				;		
			}
	}
	/*
        foreach($_REQUEST as $field_name=>$field_value) {	//$key = название поля
            $match = "";
            if(preg_match("/(desc|name|val|component)_([0-9]+)/", $field_name, $match)){
                     if($field_value != "") {	// ggtr01(match); ggtr ($match);
						 // ggtr01 ( $value );
						  				   //set($id,       $type,     $val);   					  add($id,       $type,     $val)
                         if(  !$reg['conf']->set($match[2], $match[1], $field_value)  ) $reg['conf']->add($match[2], $match[1], $field_value);
                     }
            }            
        }
        $reg['conf']->save();
		*/
}

function remove($reg, $ids, $idarray){
//	ggtr ($idarray);	ggd ($ids);
    $reg['conf']->remove($ids, $idarray);
	mosRedirect( 'index2.php?ca='.$reg['ca'], "Настройки удалены" );
}