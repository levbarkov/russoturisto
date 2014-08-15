<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class HTML_menumanager {
	function show ( $option, $menus, $pageNav ) {
		global $mosConfig_live_site;
		?><script language="javascript" type="text/javascript">
		function menu_listItemTask( id, task, option ) {
			var f = document.adminForm;
			cb = eval( 'f.' + id );
			if (cb) { cb.checked = true; submitbutton(task); }
			return false;
		}
		</script><?

		?><form action="index2.php" method="post" name="adminForm"><?
		?><table class="adminheading"><?
		?><tr><?
			?><td>Управление меню</td><?
		?></tr><?
		?></table><?
		?><table class="adminlist"><?
		?><tr><?
			?><th width="20">#</th><?
			?><th width="20px"></th><?
			?><th class="title" nowrap="nowrap">Название меню</th><?
			?><th width="10%">Количество&nbsp;ссылок</th><?
			?><th width="15%">Скрыто</th><?
			?><th width="15%">Модулей</th><?
		?></tr><?

		$k = 0;
		$i = 0;
		$start = 0;
		if ($pageNav->limitstart)
			$start = $pageNav->limitstart;
		$count = count($menus)-$start;
		if ($pageNav->limit)
			if ($count > $pageNav->limit)
				$count = $pageNav->limit;
		for ($m = $start; $m < $start+$count; $m++) {
			$menu = $menus[$m];
			$menu->type = htmlspecialchars( $menu->type );
			$link 	= 'index2.php?ca=menumanager&task=edit&hidemainmenu=1&menu='. $menu->type;
			$linkA 	= 'index2.php?ca=menus&menutype='. $menu->type;
			?><tr class="<?php echo "row". $k; ?>"><?
				?><td align="center" width="30px"><?php echo $i + 1 + $pageNav->limitstart;?></td><?
				?><td width="30px" align="center"><input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $menu->type; ?>" onclick="isChecked(this.checked);" /></td><?
				?><td align="left"><a href="<?php echo $link; ?>" title="Изменить название меню"><?php echo $menu->type; ?></a></td><?
				?><td align="center"><a href="<?php echo $linkA; ?>" title="Перейти к ссылкам меню"><?php echo $menu->published; ?></a></td><?
				?><td align="center"><?php echo $menu->unpublished; ?></td><?
				?><td align="center"><?php echo $menu->modules;?></td><?
			?></tr><?
			$k = 1 - $k;
			$i++;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="ca" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}


	/**
	* writes a form to take the name of the menu you would like created
	* @param option	display options for the form
	*/
	function edit ( &$row, $option ) {
		global $mosConfig_live_site;

		$new = $row->menutype ? 0 : 1;
		$row->menutype = htmlspecialchars( $row->menutype );
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'savemenu') {
				if ( form.menutype.value == '' ) {
					alert( 'Пожалуйста, введите название меню' );
					form.menutype.focus();
					return;
				}
				var r = new RegExp("[\']", "i");
				if ( r.exec(form.menutype.value) ) {
					alert( 'Название меню не должно содержать \'' );
					form.menutype.focus();
					return;
				}
				<?php
				if ( $new ) {
					?>
					if ( form.title.value == '' ) {
						alert( 'Пожалуйста, введите название модуля меню' );
						form.title.focus();
						return;
					}
					<?php
				}
				?>
				submitform( 'savemenu' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr><td>Информация о меню</td></tr>
		</table>

		<table class="adminform">
		<tr height="45px;">
			<td width="100px" align="left"><strong>Имя меню:</strong></td>
			<td><input class="inputbox" type="text" name="menutype" size="30" maxlength="25" value="<?php echo isset( $row->menutype ) ? $row->menutype : ''; ?>" /> <?php
			$tip = 'Это имя меню используется системой для его идентификации - оно должно быть уникально. Рекомендуется давать имя без пробелов';
			echo mosToolTip( $tip );
			?></td>
		</tr>
		<?php
		if ( $new ) {
			?>
			<tr>
				<td width="100px" align="left" valign="top"><strong>Заголовок модуля:</strong></td>
				<td><input class="inputbox" type="text" name="title" size="30" value="<?php echo $row->title ? $row->title : 'mod_mainmenu';?>" /></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td colspan="2">
			</td>
		</tr>
		</table>
		<br /><br />

		<script language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>
		<?php
		if ( $new ) {
			?>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="iscore" value="<?php echo $row->iscore; ?>" />
			<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
			<input type="hidden" name="position" value="<?php echo $row->position; ?>" />
			<input type="hidden" name="module" value="mod_mainmenu" />
			<input type="hidden" name="params" value="<?php echo $row->params; ?>" />
			<?php
		}
		?>

		<input type="hidden" name="new" value="<?php echo $new; ?>" />
		<input type="hidden" name="old_menutype" value="<?php echo $row->menutype; ?>" />
		<input type="hidden" name="ca" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="savemenu" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
		}


	/**
	* A delete confirmation page
	* Writes list of the items that have been selected for deletion
	*/
	function showDelete( $option, $type, $items, $modules ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Удалить меню: <?php echo $type;?>
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="20%">
			<?php
			if ( $modules ) {
				?>
				<strong>Модуль(и) для удаления:</strong>
				<ol>
				<?php
				foreach ( $modules as $module ) {
					?>
					<li>
					<font color="#000066">
					<strong>
					<?php echo $module->title; ?>
					</strong>
					</font>
					</li>
					<input type="hidden" name="cid[]" value="<?php echo $module->id; ?>" />
					<?php
				}
				?>
				</ol>
				<?php
			}
			?>
			</td>
			<td align="left" valign="top" width="25%">
			<strong>Удаляемые пункты меню:</strong>
			<br />
			<ol>
			<?php
			foreach ( $items as $item ) {
				?>
				<li>
				<font color="#000066">
				<?php echo $item->name; ?>
				</font>
				</li>
				<input type="hidden" name="mids[]" value="<?php echo $item->id; ?>" />
				<?php
			}
			?>
			</ol>
			</td>
			<td>
			* Эта операция <strong><font color="#FF0000">удаляет</font></strong> это меню, <br />ВСЕ его пункты и модуль(и), назначенный ему *
			<br /><br /><br />
			<div style="border: 1px dotted gray; width: 70px; padding: 10px; margin-left: 100px;">
			<a class="toolbar" href="javascript:if (confirm('Вы уверены, что хотите удалить это меню? \nПроизойдет удаление меню, его пунктов и модулей.')){ submitbutton('deletemenu');}" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('remove','','images/delete_f2.png',1);">
			<img name="remove" src="images/delete.png" alt="Удалить" border="0" align="middle" />
			&nbsp;Удалить
			</a>
			</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="boxchecked" value="1" />
		</form>
		<?php
	}


	/**
	* A copy confirmation page
	* Writes list of the items that have been selected for copy
	*/
	function showCopy( $option, $type, $items ) {
	?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if (pressbutton == 'copymenu') {
				if ( document.adminForm.menu_name.value == '' ) {
					alert( 'Пожалуйста, введите имя для копии меню' );
					return;
				} else if ( document.adminForm.module_name.value == '' ) {
					alert( 'Пожалуйста, введите имя для нового модуля' );
					return;
				} else {
					submitform( 'copymenu' );
				}
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Копирование меню
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong>Имя нового меню:</strong>
			<br />
			<input class="inputbox" type="text" name="menu_name" size="30" value="" />
			<br /><br /><br />
			<strong>Имя нового модуля:</strong>
			<br />
			<input class="inputbox" type="text" name="module_name" size="30" value="" />
			<br /><br />
			</td>
			<td align="left" valign="top" width="25%">
			<strong>
			Копируемое меню:
			</strong>
			<br />
			<font color="#000066">
			<strong>
			<?php echo $type; ?>
			</strong>
			</font>
			<br /><br />
			<strong>
			Копируемые пункты меню:
			</strong>
			<br />
			<ol>
			<?php
			foreach ( $items as $item ) {
				?>
				<li>
				<font color="#000066">
				<?php echo $item->name; ?>
				</font>
				</li>
				<input type="hidden" name="mids[]" value="<?php echo $item->id; ?>" />
				<?php
			}
			?>
			</ol>
			</td>
			<td valign="top">
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		</form>
		<?php
	}
}
?>