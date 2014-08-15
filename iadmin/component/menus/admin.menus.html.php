<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class HTML_menusections {

	function showMenusections( $rows, $pageNav, $search, $levellist, $menutype, $option ) {
		global $my;

		?><form action="index2.php" method="post" name="adminForm"><?
		?><table class="adminheading"><?
		?><tr><?
			?><td width="100%">Меню: <?php echo $menutype;?></td><?
			?><td nowrap="nowrap">Максимально уровней</td><?
			?><td><?php echo $levellist;?></td><?
			?><td>Поиск:</td><?
			?><td><input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="inputtop" onChange="document.adminForm.submit();" /></td><?
		?></tr><?php
		if (0)
		if ( $menutype == 'mainmenu' ) {
			?>
			<tr>
				<td align="right" nowrap="nowrap" style="color: black; font-weight: normal;" colspan="5">
				<?php echo _MAINMENU_DEL; ?>
				<br/>
				<span style="color: black;">
				<?php echo _MAINMENU_HOME; ?>
				</span>
				</td>
			</tr>
			<?php
		}
		?></table><?
		?><table class="adminlist"><?
		?><tr><?
			?><th width="20">#</th><?
			?><th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th><?
			?><th class="title" width="40%">Пункт меню</th><?
			?><th width="5%">Включен</th><?
			?><th colspan="2" width="5%">Сортировка</th><?
			?><th width="3%"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )">Сохранить&nbsp;порядок</a></th><?
			?><th width="10%">Доступ</th><?
			?><th>PI</th><?
			?><th width="35%" align="left">Тип</th><?
			?><th>CID</th><?
		?></tr><?

		$k = 0;
		$i = 0;
		$n = count( $rows );
		foreach ($rows as $row) {
			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			$published 	= mosCommonHTML::PublishedProcessing( $row, $i );
			
			?><tr class="<?php echo "row$k"; ?>"><?
				?><td><?php echo $i + 1 + $pageNav->limitstart;?></td><?
				?><td><?php echo $checked; ?></td><?
				?><td nowrap="nowrap" align="left"><?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->treename;
				} else {
					$link = 'index2.php?ca=menus&menutype='. $row->menutype .'&task=edit&id='. $row->id . '&hidemainmenu=1';
					?><a href="<?php echo $link; ?>"><?php echo $row->treename; ?></a><?php
				}
				?></td><?
				?><td width="10%" align="center"><?php echo $published;?></td><?
				?><td><?php echo $pageNav->orderUpIcon( $i ); ?></td><?
				?><td><?php echo $pageNav->orderDownIcon( $i, $n ); ?></td><?
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /></td><?
				?><td align="center"><?php echo $access;?></td><?
				?><td align="center"><?php echo $row->id; ?></td><?
				?><td align="left"><span class="editlinktip"><?php echo mosToolTip( $row->descrip, '', 280, 'tooltip.png', $row->type, $row->edit );?></span></td><?
				?><td align="center"><?php echo $row->componentid; ?></td><?
			?></tr><?
			$k = 1 - $k;
			$i++;
		}
		?></table><?

		echo $pageNav->getListFooter(); 

		?><input type="hidden" name="ca" value="<?php echo $option; ?>" /><?
		?><input type="hidden" name="menutype" value="<?php echo $menutype; ?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?><input type="hidden" name="boxchecked" value="0" /><?
		?><input type="hidden" name="hidemainmenu" value="0" /><?
		?></form><?php
	}


	/**
	* Displays a selection list for menu item types
	*/
	function addMenuItem( &$cid, $menutype, $option, $types_content, $types_component, $types_link, $types_other, $types_submit ) {

		?><style type="text/css">
		fieldset { border: 1px solid #999999; }
		legend { font-weight:normal; }
		</style><?
		?><form action="index2.php" method="post" name="adminForm"><?
				
				?><fieldset><legend style="color:#000000;">Основные типы ссылок</legend><?
					?><table class="adminlist"><?
					
						for ( $i=0; $i < count( $types_other ); $i++ ) {
							$row = &$types_other[$i]; $link = 'index2.php?ca=menus&menutype='. $menutype .'&task=edit&type='. $row->type.'&hidemainmenu=1'; HTML_menusections::iOptions1( $row, $link, "" );
						}
					?></table><?
				?></fieldset><?
                                ?><fieldset><legend style="color:#000000;">Дополнительные типы ссылок</legend><?
					?><table class="adminlist"><?
						for ( $i=0; $i < count( $types_content ); $i++ ) {
							$row = &$types_content[$i]; $link = 'index2.php?ca=menus&menutype='. $menutype .'&task=edit&type='. $row->type .'&hidemainmenu=1'; HTML_menusections::iOptions1( $row, $link, "" );
						}
					?></table><?
				?></fieldset><?
		?><input type="hidden" name="ca" value="<?php echo $option; ?>" /><?
		?><input type="hidden" name="menutype" value="<?php echo $menutype; ?>" /><?
		?><input type="hidden" name="task" value="edit" /><?
		?><input type="hidden" name="boxchecked" value="0" /><?
		?><input type="hidden" name="hidemainmenu" value="0" /><?
		?></form><?
	}

	function htmlOptions( &$row, $link, $k, $i ) {
		?><tr class="<?php echo "row$k"; ?>"><?
			?><td width="20"><?
			?></td>
			<td style="height: 30px;"><span class="editlinktip" style="cursor: pointer;"><?php echo mosToolTip( $row->descrip, $row->name, 250, '', $row->name, $link, 1 );?></span></td>
			<td width="20">
				<input type="radio" id="cb<?php echo $i;?>" name="type" value="<?php echo $row->type; ?>" onClick="isChecked(this.checked);" />
			</td>
			<td width="20">
			</td>
		</tr>
		<?php		
	}
	function iOptions1( &$row, $link, $rowclass ) {
		?><tr class="<?php echo "$k"; ?>"><?
			?><td style="height: 30px;"><span class="editlinktip" style="cursor: pointer;"><?php echo mosToolTip( $row->descrip, "", 250, '', $row->name, $link, 1 );?></span></td><?
		?></tr><?php		
	}
	

	/**
	* Form to select Menu to move menu item(s) to
	*/
	function moveMenu( $option, $cid, $MenuList, $items, $menutype  ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th>
      Перемещение пунктов меню
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong>Переместить в меню:</strong>
			<br />
			<?php echo $MenuList ?>
			<br /><br />
			</td>
			<td align="left" valign="top">
			<strong>
			Перемещаемые пункты меню:
			</strong>
			<br />
			<ol>
			<?php
			foreach ( $items as $item ) {
				?>
				<li>
				<?php echo $item->name; ?>
				</li>
				<?php
			}
			?>
			</ol>
			</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="boxchecked" value="1" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="menutype" value="<?php echo $menutype; ?>" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php
	}


	/**
	* Form to select Menu to copy menu item(s) to
	*/
	function copyMenu( $option, $cid, $MenuList, $items, $menutype  ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th>
			Копирование пунктов меню
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong>
			Копировать в меню:
			</strong>
			<br />
			<?php echo $MenuList ?>
			<br /><br />
			</td>
			<td align="left" valign="top">
			<strong>
			Копируемые пункты меню:
			</strong>
			<br />
			<ol>
			<?php
			foreach ( $items as $item ) {
				?>
				<li>
				<?php echo $item->name; ?>
				</li>
				<?php
			}
			?>
			</ol>
			</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="menutype" value="<?php echo $menutype; ?>" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php
	}
}
?>