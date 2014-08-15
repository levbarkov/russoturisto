<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );




class template_config {
	public $vars = Array();
	public $idBox_postfix = "";
	function __construct(&$db){
		$this->db = $db;
	}
	function read(){
			$this->db->setQuery("select * from #__theme_config order by ordering ");
			$this->vars = $this->db->loadObjectList();	//	ggd ($this->vars);
			return $this->vars;
	}
	function set($id, $type, $val){
		if(is_array($this->vars)){  
			foreach($this->vars as $var){
				if($var->id == $id) {
					$var->$type = $val;	return true;
				}
			}
		}
	 	return false;
	}
	function add($id, $type, $val){	
			$obj;
			$obj->id = $id;
			$obj->$type = $val;
			array_push($this->vars, $obj);
	}

	function save()
	{
		 if(is_array($this->vars)) {	//	ggtr5 ($this->vars);
			foreach($this->vars as $var) {	// ggtr (  $var  );
				$this->db->updateObject("#__theme_config", $var, "id" ); 
			}
		}
	}

	function remove($ids, $idarray)
	{
			if(is_array($ids)) $str = "";//join(", ", $ids);
			foreach (  $ids as $index){	$str .= $idarray[$index].", "; }
			$str = substr(  $str, 0, (strlen($str)-2)  );
			$query = "delete from #__theme_config where id IN ( ".$str." )";
			$this->db->setQuery($query);
			$this->db->query();
	}
	
	function show($iconftitle) {
		global $reg;
		$obj = $this->read();
		?><script language="javascript"> 
			function do_changed<?=$this->idBox_postfix ?> (id){  document.getElementById('conf_values<?=$this->idBox_postfix ?>'+id).checked = true;   }
			function delme<?=$this->idBox_postfix ?> (id, name){  
				if (confirm('Вы действительно хотите удалить переменную '+name+' ?')){ document.location.href='index2.php?ca=<?=$reg['ca'] ?>&task=removecfg&conf_values[]=0&id[]='+id; }
			}
		</script>
		<? if (  $iconftitle!=''  ) { 
			?><table class="adminheading"><tr><td width="100%"><?=$iconftitle ?></td></tr></table><?
		} 
		$vcats[] = mosHTML::makeOption( 0, "Компонент");	$vcats[] = mosHTML::makeOption( 1, "Регулярное выр. для url");	$vcats[] = mosHTML::makeOption( 2, "Для остальных страниц");

		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__theme_config";
		$table_drug->order="ordering";
		?><table class="adminlist" border="0"  <?=$table_drug->table(); ?> >
			<tr  <?=$table_drug->row(); ?> >
				<th style="width:15px">&nbsp;</th>
				<th style="width:15px" align="left">ID</th>
				<th style="width:15px" align="left">&nbsp;&nbsp;&nbsp;</th><!--drug here-->
				<th align="left">Шаблон</th>
				<th width="10%" align="left">Тип записи</th>
				<th width="10%" align="left">Значение</th>
				<th width="10%" align="left">Внешний файл</th>
				<th width="50%" align="left">Комментарий</th>
				<th width="30px" align="left" >№</th>
				<th width="30px" align="left" ></th><!--удаление-->
			</tr><?
			$c = 0;
			foreach($obj as $k=>$o){ $rowk = ($c%2);
				?><tr <?=$table_drug->row($o->id, $o->ordering); ?> class='config_row'><?
					?><td><input id="conf_values<?=$this->idBox_postfix ?><?=$o->id ?>" type="checkbox" value="<?=$k ?>" name="conf_values[]"/></td><?
					?><td class="config_id"><label for="cb<?=$o->id ?>"><?=$o->id ?></label></td><?
					?><td class="dragHandle drugme">&nbsp;</td><?
					?><td><input class='control_elem' type='text' name='theme[]' value='<?=$o->theme ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
					?><td><? print mosHTML::selectList( $vcats, 'type[]', 'class="inputbox" size="1"  onchange="do_changed'.$this->idBox_postfix.'('.$o->id.');"  ', 'value', 'text', $o->type ); ?></td><?
					?><td><input class='control_elem' style="width:250px;" type='text' name='val[]' value='<?=desafelySqlStr($o->val) ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
					?><td><input class='control_elem' type='text' name='ext_file[]' value='<?=$o->ext_file ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
					?><td><input class='control_elem' style="width:340px; color:#666666;" type='text' name='icomment[]' value='<?=$o->icomment ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
					?><td><input class='control_elem cong_ord' type='text' name='ordering[]' value='<?=$o->ordering ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"><?
						?><input type='hidden' name='id[]' value='<?=$o->id ?>'></td><?
					?><td><a href="javascript: delme<?=$this->idBox_postfix ?>(<?=$o->id ?>, '<?=$o->theme." &mdash; ".$o->val ?>'); void(0); "><img height="16" border="0" width="16" alt="" src="/iadmin/images/delme.gif" /></a></td><?
				?></tr><? $c++;
			}
			$o->id=0; 
			//Для нового элемента добавляем пустую строку
			?><tr <?=$table_drug->row(); ?> ><td colspan="10">Добавить новую запись.<br />Если выбран тип - <strong>"Компонент"</strong>, значит в поле <strong>"Значение"</strong> необходимо указать название компонента.<br /><?
																						?>Если выбран тип - <strong>"Регулярное выр. для url"</strong>, значит в поле <strong>"Значение"</strong> необходимо написать регулярное выражение, применяемое к адресу старницы.<br /></td></tr><? $rowk = ($c%2);
			?><tr <?=$table_drug->row(); ?> class='config_row'><?
				?><td><input id="conf_values<?=$this->idBox_postfix ?>0" type="checkbox" value="<?=$c ?>" name="conf_values[]"/></td><?
				?><td></td><?
				?><td></td><?
				?><td><input class='control_elem' type='text' name='theme[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"></td><?
				?><td><? print mosHTML::selectList( $vcats, 'type[]', 'class="inputbox" size="1"', 'value', 'text', 0 ); ?></td><?
				?><td><input class='control_elem' style="width:250px;" type='text' name='val[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"></td><?
				?><td><input class='control_elem' type='text' name='ext_file[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
				?><td><input class='control_elem' style="width:340px; color:#666666;" type='text' name='icomment[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"><?
					?><input type='hidden' name='id[]' value='0'></td><?
				?><td><input class='control_elem cong_ord' type='text' name='ordering[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"><?
				?><td>&nbsp;</td><?
			?></tr><?
		?></table><? 
		$table_drug->debug_div();
	}
	function save_config(){	// ggtr5($_REQUEST);
		global $reg; 
		if (  count($_REQUEST['conf_values'])==0  ) return;
		foreach (  $_REQUEST['conf_values'] as $index  ){
			$i24r = new mosDBTable( "#__theme_config", "id", $reg['db'] );
			$i24r->id = $_REQUEST['id'][$index];
			$i24r->theme = $_REQUEST['theme'][$index];
			$i24r->type = $_REQUEST['type'][$index];
			$i24r->val = $_REQUEST['val'][$index];
			$i24r->ext_file = $_REQUEST['ext_file'][$index];
			$i24r->icomment = $_REQUEST['icomment'][$index];
			$i24r->ordering = $_REQUEST['ordering'][$index]; if (  $i24r->ordering==''  ) $i24r->ordering=0;
			// ggtr5 ($i24r);
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		}
	}
	
}



class mosTemplatePosition extends mosDBTable {
	var $id				= null;
	var $position		= null;
	var $description	= null;

	function mosTemplatePosition() {
		global $database;

		$this->mosDBTable( '#__template_positions', 'id', $database );
	}
}
?>