<?

require_once( '../wrapper.php' );

$rows = explode ( ";", $_POST['w'] );

if (   $reg['ajax_table_drug']==1  ) ggtr1 (  $rows  );

//require_once( '../lib/ajax_table_drug.php' );
$table_drug  = new ajax_table_drug ;
$table_drug->table=ggrr_strong('t'); // ggtr01($table_drug->table);
$table_drug->order=ggrr_strong('o'); // ggtr01($table_drug->order);
$table_drug->id_field=ggrr_strong('id'); // ggtr01($table_drug->order);

$allorders = array();

foreach ( $rows as $index=>$row ){
	if (  $row==''  ) continue;
	if(preg_match("/rowid_([0-9]+)/", $row, $match)){ // ggtr01 ( $match[1] );
		if (  $match[1]>0  ){ // НЕОБХОДИМО СОХРАНИТЬ ПОРЯДОК
			if(preg_match("/order_([0-9]+)/", $row, $order)){
				$allorders[] = $order[1];
			}
		}
	}
}
sort($allorders); if (   $reg['ajax_table_drug']==1  ) ggtr1($allorders);
$allaorder_index = 0;
foreach ( $rows as $index=>$row ){
	if (  $row==''  ) continue;
	if(preg_match("/rowid_([0-9]+)/", $row, $match)){ // ggtr01 ( $match[1] );
		if (  $match[1]>0  ){ // НЕОБХОДИМО СОХРАНИТЬ ПОРЯДОК
			if(preg_match("/order_([0-9]+)/", $row, $order)){
				if (  $order[1]!=$allorders[$allaorder_index]  ) $table_drug->saveorder($match[1], $allorders[$allaorder_index]); 	$allaorder_index++;
			}
		}
	}
}
?>