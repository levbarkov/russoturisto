<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* @package Joomla RE
* @subpackage Modules
*/
class HTML_modules {

	/**
	* Writes a list of the defined modules
	* @param array An array of category objects
	*/
	function showModules( &$rows, $myid, $client, &$pageNav, $option, &$lists, $search ) {
		global $my;

		//mosCommonHTML::loadOverlib();
		?><form action="index2.php" method="post" name="adminForm"><?
		?><table class="adminheading"><?
		?><tr><?
			?><td width="100%" ><?
				$iway[0]->name = "Модули ".(  $lists['client_id'] ? 'админцентра' : 'cайта'  );
				$iway[0]->url="";
				i24pwprint_admin ($iway);
			?></td><?
			?><td align="right">Фильтр:</td><?
			?><td><input type="text" name="icsmart_modules_search" value="<?php echo htmlspecialchars( $search );?>" class="inputtop" onChange="document.adminForm.submit();" /></td><?
			?><td width="right"><?php echo $lists['position'];?></td><?
			?><td width="right"><?php echo $lists['type'];?></td><?
		?></tr><?
		?></table><?

		?><table class="adminlist"><?
		?><tr><?
			?><th width="20px">#</th><?
			?><th width="20px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th><?
			?><th class="title">Название модуля</th><?
			?><th nowrap="nowrap" width="10%">На сайте</th><?
			?><th colspan="2" align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th nowrap="nowrap" width="7%">Позиция</th><?
			?><th nowrap="nowrap" width="5%">ID</th><?
			?><th nowrap="nowrap" width="10%" align="left">Тип</th><?
		?></tr><?
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= &$rows[$i];

			$link = 'index2.php?ca=modules&client='. $client .'&task=editA&hidemainmenu=1&id='. $row->id;

			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			$published 	= mosCommonHTML::PublishedProcessing( $row, $i );
			?><tr class="<?php echo "row$k"; ?>"><?
				?><td align="right"><?php echo $pageNav->rowNumber( $i ); ?></td><?
				?><td><?php echo $checked; ?></td><?
				?><td align="left"><a href="<?php echo $link; ?>"><?php echo $row->title; ?></a></td><?
				?><td align="center"><?php echo $published;?></td><?
				?><td><?php echo $pageNav->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position) ); ?></td><?
				?><td><?php echo $pageNav->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position) ); ?></td><?
				?><td align="center" ><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /></td><?
				?><td align="center"><?php echo $row->position; ?></td><?
				?><td align="center"><?php echo $row->id;?></td><?
				?><td align="left"><?php echo $row->module ? $row->module : "User";?></td><?
			?></tr><?
			$k = 1 - $k;
		}
		?></table><?
		 echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client" value="<?php echo $client;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<? if (  isset($_REQUEST['filter_position'])  ) { ?><input type="hidden" name="filter_position" value="<? print $_REQUEST['filter_position']; ?>" /><? } ?>
		</form>
		<?php
	}

	function editModule( &$row, &$orders2, &$lists, &$params, $option ) {
		global $mosConfig_live_site, $mosConfig_cachepath, $my, $reg;

		$row->title = htmlspecialchars( $row->title );
		$row->titleA = '';
		if ( $row->id ) {
			$row->titleA = '<small><small>[ '. $row->title .' ]</small></small>';
		}
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if ( ( pressbutton == 'save' ) && ( document.adminForm.title.value == "" ) ) {
				alert("Модуль должен иметь заголовок");
			} else {
				<?php if ($row->module == "") {
					getEditorContents( 'editor1', 'content' );
				}?>
			}
			submitform(pressbutton);
		}
		<!--
		var originalOrder = '<?php echo $row->ordering;?>';
		var originalPos = '<?php echo $row->position;?>';
		var orders = new Array();	// array in the format [key,value,text]
		<?php	$i = 0;
		foreach ($orders2 as $k=>$items) {
			foreach ($items as $v) {
				echo "\n	orders[".$i++."] = new Array( \"$k\",\"$v->value\",\"$v->text\" );";
			}
		}
		?>
		//-->
		</script>
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name = "Модули ".(  $lists['client_id'] ? 'админцентра' : 'cайта'  );
			$iway[0]->url="index2.php?ca=modules";
			$iway[1]->name = $row->id ? 'Изменение' : 'Новый';
			$iway[1]->name .= ' '.$row->titleA;
			$iway[1]->url="";
			i24pwprint_admin ($iway);
			?></td></tr></table>
		<form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> action="index2.php" method="post" name="adminForm">
		<input type="hidden"  name="iuse" id="iuse" value="0" />
		<table cellspacing="0" cellpadding="0" width="100%" class="workspace">
		<tr valign="top">
			<td width="100%">
				<table class="adminform">
				<tr>
					<th colspan="2">Детали</th>
				</tr>
				<tr>
					<td class="rea" width="100" align="left">Название:</td>
                                        <td><input class="text_area" type="text" name="title" size="35" value="<?php echo $row->title; ?>" />&nbsp;Пишите название модуля по рускии, чтобы было понятно (допускается использование пробелов)</td>
				</tr>
				<tr>
					<td width="100" align="left">Показывать заголовок:</td>
					<td><?php echo $lists['showtitle']; ?></td>
				</tr>
				<tr>
					<td valign="top" align="left">Позиция:</td>
					<td><?php echo $lists['position']; ?></td>
				</tr>
				<tr>
					<td valign="top" align="left">Порядок модуля:</td>
					<td><script language="javascript" type="text/javascript">
					<!--
					writeDynaList( 'class="inputbox" name="ordering" size="1"', orders, originalPos, originalPos, originalOrder );
					//-->
					</script></td>
				</tr>
				<tr style="display:none">
					<td valign="top" align="left">Уровень доступа:</td>
					<td><?php echo $lists['access']; ?></td>
				</tr>
				<tr>
					<td valign="top">Опубликован:</td>
					<td><?php echo $lists['published']; ?></td>
				</tr>
				<tr>
					<td valign="top">Описание:</td>
					<td><?php echo $row->description; ?></td>
				</tr>
				</table>

				<table class="adminform">
				<tr>
					<th >Параметры</th>
				</tr>
				<tr><td><?php echo $params->render();?></td></tr>
                                <tr><td>— Для отключения кнопки "Редактировать" в параметрах укажите editme=0</td></tr>
				</table>
			</td>
		</tr>
		<?php
		if ($row->module == "") {
			?>
			<tr>
				<td>
						<table width="100%" class="adminform">
						<tr>
							<th colspan="2">Пользовательский код / Содержимое модуля</th>
						</tr>
						<tr>
							<td valign="top" align="left">Содержимое:</td>
							<td>
							<div style="display:none">
							<?php editorArea( 'editor1',  $row->content , 'content', '800', '400', '110', '40' ) ; ?>
							
							</div>
							
							<textarea id="content" name="content" cols="110" rows="40" style="width:800px; height:400px;"><?=$row->content ?></textarea></td>
						</tr>
						</table>
				</td>
			</tr>
			<?php
		}
		?></table><?
		?><input type="hidden" name="ca" value="<?php echo $option; ?>" /><?
		?><input type="hidden" name="id" value="<?php echo $row->id; ?>" /><?
		?><input type="hidden" name="original" value="<?php echo $row->ordering; ?>" /><?
		?><input type="hidden" name="module" value="<?php echo $row->module; ?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?><input type="hidden" name="client_id" value="<?php echo $lists['client_id']; ?>" /><?php
		if ( $row->client_id || $lists['client_id'] ) {
			echo '<input type="hidden" name="client" value="admin" />';
		}
		?>
		</form>
		<?php
	}

}
?>