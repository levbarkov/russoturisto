<?php

/**
 *
 * Класс создания перетыскиваемых мышкой таблиц
 *
 */
 
/**
 *
 * ОСНОВНЫЕ МОМЕНТЫ ПРИ СОЗДАНИИ ТАСКАЕМОЙ ТАБЛИЦЫ
 *
 * ИНИЦИАЛИЗАЦИЯ:
 *		$table_drug  = new ajax_table_drug ;	
 *		$table_drug->id="ajax_table_drug_td";	// т.е. таскаем только за одну <TD>, если "ajax_table_drug" - то за весь <TR>
 *		$table_drug->table="#__names_prop";		// название таблице в базе данных
 *		$table_drug->order="ordering";			// название поля содержащего порядковый номер элемента, обычно order или ordering
 *
 * ДЕЙСТВИЯ ВНУТРИ ТАБЛИЦЫ:
 * 		<table class="adminlist" <?=$table_drug->table(); ?> 
 * 			<tr <?=$table_drug->row(); ?> >
 *
 * ВЫВОД ОТЛАДОЧНОЙ ИНФОРАЦИИ:
 * 		$table_drug->debug_div(); - после </table>
 */

class ajax_table_drug {
	var $id="ajax_table_drug";	// id таблицы
	var $table;
	var $order = "ordering";
	var $sep = '"';
	var $id_field = "mostwanted";
	
	function table(){
		if (  $this->id_field=='mostwanted'  ) $this->id_field = "id";
		return ' id='.$this->sep.$this->id.$this->sep.' ajax_table_drug_table='.$this->sep.$this->table.$this->sep.' ajax_table_drug_id='.$this->sep.$this->id_field.$this->sep.' ajax_table_drug_order='.$this->sep.$this->order.$this->sep.'  ';
	}
/*	function origin_id_array(){
		return '<span id='.$this->sep.$this->id.'_ids'.$this->sep.' >'.$this->ids.'</span> ';
	}*/

	function row($id='', $order=''){ //id="rowid_=$row->id _order_=$row->order "
		return ' id='.$this->sep.'rowid_'.$id.'_order_'.$order.$this->sep.'   ';
	}

	function saveorder($id, $order){
		global $reg;
		$i24r = new mosDBTable( $this->table, $this->id_field, $reg['db'] );  $ord_field = $this->order;  $id_field = $this->id_field;
		$i24r->$id_field = $id;
		$i24r->$ord_field = $order;	
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();  
		if (   $reg['ajax_table_drug']==1  ) ggtr3($reg['db']);
	}
	function debug_div(){
		global $reg;
		if (   $reg['ajax_table_drug']==0  ) return;
		?><div id="upd-dnd" style="position: relative; border: 1px solid #ccc; width: 300px; float: right; margin: 20px 10px 20px 20px; padding: 10px;">Отладка ajax_drug_table</div><?
	}
	

}
?>
