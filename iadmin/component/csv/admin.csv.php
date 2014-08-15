<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'save':		savecsv( $task );
						break;
	case 'csv_step2':	csv_step2( $id, $option );
						break;
	case 'csv_step3':	csv_step3( $id, $option );
						break;
	case 'remove':		removecsv( 0, $option );
						break;
	default:			csv_step1( $option );
						break;
}
function csv_step1( $option ) {
	global $database, $my, $iConfig_list_limit;
		?><table class="adminheading" align="center" border="0"><tr><td nowrap="nowrap">Управление загрузкой прайс-листа из CSV - этап 1</td></tr></table><?

		?><center><form id="emailForm" target="_top" name="emailForm" method="post" action="index2.php" enctype="multipart/form-data">
		<table class="adminheading" align="center" border="0" width="150px" style="width:150px"><tr><?
			?><td align="right" >Выберите&nbsp;файл:&nbsp;</td><?
			?><td align="right" ><input type="file" name="userfile" size="50"></td><?
			?><td ><input type="submit" value="Импорт" class="button" /></td><?
		?></tr></table><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
		?><input type="hidden" name="task" value="csv_step2" /><?
		?><input type="hidden" name="cid" value="<? print safelySqlInt($_REQUEST['cid']); ?>" /><?
		?></form></center><?php
}


function csv_step2( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe;
	
//	ggtr ($_FILES);
	$userfile_tmp  = $_FILES['userfile']['tmp_name']; 
	$lives = file_exists($userfile_tmp);
	$iexuni = md5(uniqid("exsalon"));
	$iexfototype = "csv";
	$ismallexname = $_FILES['userfile']['name'].$iexuni.".".$iexfototype;
	copy($userfile_tmp, site_path."/images/ex/csv/".$ismallexname);
	//	определение кол-ва столбцов
	$f = fopen(site_path."/images/ex/csv/".$ismallexname, "r") or die("Ошибка!");
	$i100 = 0; $max_cols = 0;
	for ($i=0; $data=fgetcsv($f,1000,";"); $i++) {
	  $num = count($data);
	  if ($num > $max_cols) $max_cols = $num;
	  $i100++;
	  if (  $i100>100  ) break;
	}
	fclose($f);
	for ($i=0; $i<$max_cols; $i++) $vfff[] = mosHTML::makeOption( $i, "$i");
	$vcats[] = mosHTML::makeOption( "0", "Не используется");	
	$vcats[] = mosHTML::makeOption( "sku", "Артикул");
	$vcats[] = mosHTML::makeOption( "name", "Название");
	$vcats[] = mosHTML::makeOption( "sdesc", "Краткое описание");
	$vcats[] = mosHTML::makeOption( "fdesc", "Полное описание");
	$vcats[] = mosHTML::makeOption( "ostatok", "Остаток");
	$vcats[] = mosHTML::makeOption( "price1", "Цена, розн, нал");
	$vcats[] = mosHTML::makeOption( "price2", "Цена, розн, безнал");
	$vcats[] = mosHTML::makeOption( "price3", "Цена, опт, нал");
	$vcats[] = mosHTML::makeOption( "price4", "Цена, опт, безнал");

	?><form name="adminForm" action="index2.php"><?
	?><table border="1"><?
	?><tr><?
	?><td style="font-size:9px; vertical-align:top;" valign="top">номер<br />(кол-во полей)</td><?
	for ($i=0; $i<$max_cols; $i++) {
		?><td valign="top" style="vertical-align:top;"><? print "поле&nbsp;$i"; ?>&nbsp;это<br /><?
		print mosHTML::selectList( $vcats, 'fields[]', 'class="inputbox" style="width:70px;" size="1" id="city_id" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->city_id ); 
		?><br />префикс: <input type="text" name="prefix[]" style="width:70px;" /><?
		?><br />постфикс: <input type="text" name="postfix[]" style="width:70px;" /></td><?
	}
	?></tr><?
	$f = fopen(site_path."/images/ex/csv/".$ismallexname, "r") or die("Ошибка!"); $i100 = 0;
	for ($i=0; $data=fgetcsv($f,1000,";"); $i++) {
	  $num = count($data);
	  ?><tr><td><? echo "<h3>$i($num)</h3>"; ?></td><?
	  for ($c=0; $c<$num; $c++){
	  	?><td><? print "$data[$c]"; ?></td><?
	  }
	  ?></tr><?
	  $i100++;
	  if (  $i100>100  ) break;
	}
	?></table><?
	?><table>
		<tr>
			<td><strong>ФИЛЬТР ДАННЫХ</strong></td>
			<td>
				<table>
					<tr>
						<td><input type="checkbox" name="ddd1" id="ddd1" /><label for="ddd1" >Удалить если пустое поле №</label></td><td><? print mosHTML::selectList( $vfff, 'del_if_empty', 'class="inputbox"  size="1"', 'value', 'text', $row->city_id );  ?></td>
					</tr>
					<tr>
						<td><input type="checkbox" name="ddd2" id="ddd2" /><label for="ddd2" >Удалить если поле №</label></td><td><? print mosHTML::selectList( $vfff, 'del_if_string_c', 'class="inputbox"  size="1"', 'value', 'text', $row->city_id );  ?> равно <input type="text" name="del_if_string_value" value="" /> (разделитель - ;)</td>
					</tr>

				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="checkbox" name="spec" id="spec" /><label for="spec" ><b> - Специальный товар</b></label></td>
		</tr>
		<tr>
			<td colspan="2"><input type="checkbox" name="delall" id="delall" /><label for="delall" ><b> - Удалить все</b></label></td>
		</tr>

	</table><?
	?><input type="hidden" name="maxf" value="<? print $max_cols; ?>" /><?
	?><input type="hidden" name="fname" value="<? print $ismallexname; ?>" /><?
	?><input type="hidden" name="ca" value="<? print $option; ?>" /><?
	?><input type="hidden" name="parent" value="<? print safelySqlInt($_REQUEST['cid']); ?>" /><?
	?><input type="hidden" name="task" value="csv_step3" /><?
	?></form><?
	fclose($f);

	
	
}


function csv_step3( $task ) {
	global $database, $my;
	// test options for filter
		$import_type = 1;  //1 - insert, 2 - update
		$parent = safelySqlInt( $_REQUEST['parent'] );
		$spec = isset($_REQUEST['spec']) ? 1:0; 
		$delall = isset($_REQUEST['delall']) ? 1:0; 
		$del_if_empty = safelySqlStr( $_REQUEST['del_if_empty'] );
		$del_if_string_c = safelySqlInt( $_REQUEST['del_if_string_c'] );
		$del_if_string_value = safelySqlStr( $_REQUEST['del_if_string_value'] );
		
	if (  $delall  )  ggsqlq ("delete from #__exgood where parent=$parent");
//	ggd ($_REQUEST, 50);
//	ggtr ($_REQUEST);
	
	$ismallexname = $_REQUEST['fname'];
	$del_if_string_value_arr = explode(";", $del_if_string_value); //$del_if_string_value_arrc = count ($del_if_string_value_arr); //ggtr ($del_if_string_value_arr); ggtr (count ($del_if_string_value_arr) );
	$f = fopen(site_path."/images/ex/csv/".$ismallexname, "r") or die("Ошибка!"); $i100 = 0;
	$fsqli = 50; 								$fsql = "INSERT INTO #__exgood (`name`,				`sku`,				`parent`,			`publish`,	`sdesc`,			`fdesc`,			`spec`,				`price1`, 			`price2`,			`price3`,			`price4`,			`ostatok`) VALUES ";
	for ($i=0; $data=fgetcsv($f,1000,";"); $i++) {
		// filter
		$do_it = true;
//		ggtr ($data[$del_if_empty]);
		if (  isset($_REQUEST['ddd1'])  )
			if (  $data[$del_if_empty]==''  ) $do_it = false;
		if (  isset($_REQUEST['ddd2'])  )
			foreach ($del_if_string_value_arr as $del_if_string_value_a){
				if (  strcmp($data[$del_if_string_c],$del_if_string_value_a)==0  ) $do_it = false;
			}
//		ggtr ($do_it,1);
		if (  $do_it == false  ) continue;
		$num = count($data);
		$sku = "";  $name = ""; $price1 = 0; $price2 = 0; $price3 = 0; $price4 = 0; $ostatok = 0; $sdesc  = ""; $fdesc = "";
		for ($c=0; $c<$num; $c++){
			if (  $_REQUEST['fields'][$c]=='sku'  ) $sku .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='name'  ) $name .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='price1'  ) $price1 .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='price2'  ) $price2 .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='price3'  ) $price3 .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='price4'  ) $price4 .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='ostatok'  ) $ostatok .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='sdesc'  ) $sdesc .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
			if (  $_REQUEST['fields'][$c]=='fdesc'  ) $fdesc .= $_REQUEST['prefix'][$c].$data[$c].$_REQUEST['postfix'][$c];
		}
	  	// создаем объект для заполнения
//		$i24r = new mosDBTable( "#__exgood", "id", $database );
/*		$i24r->id = 0;
		$i24r->sku = $sku;
		$i24r->name = $name;
		$i24r->price1 = $price1;
		$i24r->sdesc = $sdesc;
		$i24r->fdesc = $fdesc;
		$i24r->parent = $parent;
		$i24r->spec = $spec;
		$i24r->publish = 1;*/

		$i24r->id = 0;
		$i24r->sku = safelySqlStr($sku);
		$i24r->name = safelySqlStr($name);
		$i24r->price1 = safelySqlInt($price1);
		$i24r->price2 = safelySqlInt($price2);
		$i24r->price3 = safelySqlInt($price3);
		$i24r->price4 = safelySqlInt($price4);
		$i24r->ostatok = safelySqlInt($ostatok);
		$i24r->sdesc = $sdesc;
		$i24r->fdesc = $fdesc;
		$i24r->parent = $parent;
		$i24r->spec = safelySqlInt($spec);
		$i24r->publish = 1;
		
		if (  $fsqli == 0  ){
				$fsql = substr(   $fsql, 0, (strlen($fsql)-1)   );
				ggsqlq ($fsql);
				$fsql = "INSERT INTO #__exgood (`name`,				`sku`,				`parent`,			`publish`,	`sdesc`,			`fdesc`,			`spec`,				`price1`, 				`price2`,				`price3`,				`price4`,				`ostatok`) VALUES ";
				$fsqli = 50;
		}
		$fsqli--;				  $fsql .=  "\n('".$i24r->name."',	'".$i24r->sku."',	".$i24r->parent.",	1, 			'".$i24r->sdesc."',	'".$i24r->fdesc."',	'".$i24r->spec."',	'".$i24r->price1."',	'".$i24r->price2."',	'".$i24r->price3."',	'".$i24r->price4."',	'".$i24r->ostatok."'),";
//		ggtr ($i24r, 33);
		
/*		if (  $i24r->id==0  ){
			$iexmaxorder = ggsql ("SELECT * FROM #__exgood WHERE #__exgood.parent=".$parent." ORDER BY #__exgood.order DESC LIMIT 0,1 ");
			$i24r->order = $iexmaxorder[0]->order+1;
		}*/
/*
		if (!$i24r->check()) {
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		} else $i24r->store();*/
	  ?></tr><?
	}
		if (  $fsqli != 49  ){
				$fsql = substr(   $fsql, 0, (strlen($fsql)-1)   );
				ggsqlq ($fsql);
		}

	?></table><?
	?><? echo "<h3>Добавленно позиций: $i</h3>"; ?><?
	?><input type="text" name="maxf" value="<? print $max_cols; ?>" /><?
	?><input type="text" name="fname" value="<? print $ismallexname; ?>" /><?
	?><input type="text" name="ca" value="<? print $option; ?>" /><?
	?><input type="text" name="task" value="csv_step3" /><?
	?></form><?
	fclose($f);

}


function savecsv( $task ) {
	global $database, $my;

	$i24r = new mosDBTable( "#__csv", "id", $database );
	$i24r->id = safelySqlInt($_REQUEST['id']);
	$i24r->v1 = safelySqlStr($_REQUEST['v1']);
    $i24r->v2 = safelySqlStr($_REQUEST['v2']);
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__csv ORDER BY #__csv.order DESC LIMIT 0,1 ");
		$i24r->order = $iexmaxorder[0]->order+1;
	}
	if (!$i24r->check()) {
		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
	} else $i24r->store();

	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Сохранено: '. $row->name;
			mosRedirect( 'index2.php?ca=csv', $msg );
			break;
	}
}
?>