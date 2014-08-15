<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

class categories_html {

	function show( &$rows, $section, $section_name, &$pageNav, &$lists, $type ) {
		global $my;

		?><form action="index2.php" method="post" name="adminForm"><?
		?><table class="adminheading"><?
		?><tr><?
		?><td width="100%">Подрубрики</td><?
		?><td width="right"><?php echo $lists['sectionid'];?></td><?
		?></tr><?
		?></table><?
		?><table class="adminlist"><?
		?><tr><?
			?><th width="10" align="left">#</th><?
			?><th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows );?>);" /></th><?
			?><th class="title">Подрубрика</th><?
			if ( $section == 'content') { ?><th width="12%" align="left">Рубрика</th><?php }
			if ( $section != 'content') {
				?><th colspan="2" width="5%">Сортировка</th><?php
			}
			?><th width="3%"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )">Сохранить&nbsp;порядок</a></th><?
			?><th width="8%">Состояние</th><?
			?><th width="8%">Видимо для</th><?php
			?><th width="5%" nowrap>ID</th><?php
			if ( $type == 'content') {
				?><th width="6%">В архиве</th><?php
			} else {
				?><th width="20%"></th><?php
			}
			?>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	= &$rows[$i];
			$row->sect_link = 'index2.php?ca=sections&task=editA&hidemainmenu=1&id='. $row->section;

			$link = 'index2.php?ca=categories&section='. $section .'&task=editA&hidemainmenu=1&id='. $row->id;
			if ($row->checked_out_contact_category) {
				$row->checked_out = $row->checked_out_contact_category;
			}
			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			$published 	= mosCommonHTML::PublishedProcessing( $row, $i, 0 );
			?>
			<tr class="<?php echo "row$k"; ?>"><?
				?><td><?php echo $pageNav->rowNumber( $i ); ?></td><?
				?><td><?php echo $checked; ?></td><?
				?><td><?php
				if ( $row->checked_out_contact_category && ( $row->checked_out_contact_category != $my->id ) ) {
					echo stripslashes( $row->name ) .' ( '. stripslashes( $row->title ) .' )';
				} else {
					?><a href="<?php echo $link; ?>"><?php echo stripslashes( $row->name ) .' ( '. stripslashes( $row->title ) .' )'; ?></a><?php
				}
				?></td><?
				if ( $section == 'content' ) { ?><td align="left"><?php echo $row->section_name; ?></td><?php } 
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /></td><?
				?><td align="center"><?php echo $published;?></td><?php
				if ( $section != 'content' ) {
					?><td><?php echo $pageNav->orderUpIcon( $i ); ?></td><?
					?><td><?php echo $pageNav->orderDownIcon( $i, $n ); ?></td><?php
				}
				?><td align="center"><?php echo $access;?></td><?
				?><td align="center"><?php echo $row->id; ?></td><?php
				if ( $type == 'content') {
					?><td align="center"><?php echo $row->active; ?></td><?php
				} else {
					?><td></td><?php
				}
				$k = 1 - $k;		
			?></tr><?
		}
		?></table><?
		echo $pageNav->getListFooter(); 
		?><input type="hidden" name="ca" value="categories" /><?
		?><input type="hidden" name="section" value="<?php echo $section;?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?><input type="hidden" name="chosen" value="" /><?
		?><input type="hidden" name="act" value="" /><?
		?><input type="hidden" name="boxchecked" value="0" /><?
		?><input type="hidden" name="type" value="<?php echo $type; ?>" /><?
		?><input type="hidden" name="hidemainmenu" value="0" /><?
		?></form><?
	}

	function edit( &$row, &$lists, $redirect, $menus ) {

		if ( $redirect == 'content' ) {
			$component = 'Содержимое';
		} else {
			$component = ucfirst( substr( $redirect, 4 ) );
			if ( $redirect == 'icacontact_details' ) {
				$component = 'Контакт';
			}
		}
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton, section) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			if ( pressbutton == 'menulink' ) {
				if ( form.menuselect.value == "" ) {
					alert( "Пожалуйста, выберите меню" );
					return;
				} else if ( form.link_type.value == "" ) {
					alert( "Пожалуйста, выберите тип меню" );
					return;
				} else if ( form.link_name.value == "" ) {
					alert( "Пожалуйста, введите название для этого пункта меню" );
					return;
				}
			}

			if ( form.name.value == "" ) {
				alert("Категория должна иметь название");
			} else {
				<?php getEditorContents( 'editor1', 'description' ) ; ?>
				submitform(pressbutton);
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm"><?
		?><table class="adminheading"><?
		?><tr><?
			?><th class="categories">Подрубрика: <?php echo $row->id ? 'Изменение' : 'Новая';?></th><?
		?></tr><?
		?></table><?
		?><table width="100%"><?
		?><tr><?
			?><td valign="top" width="60%"><?
				?><table class="adminform"><?
				?><tr><?
					?><th colspan="3">Свойства подрубрики</th><?
				?><tr><?
				?><tr><?
					?><td>Заголовок подрубрики (Title):</td><?
					?><td colspan="2"><input class="text_area" type="text" name="title" value="<?php echo stripslashes( $row->title ); ?>" size="50" maxlength="50" title="Короткое имя для меню" /></td>
				</tr>
				<tr>
					<td>
					Название подрубрики (Name):
					</td>
					<td colspan="2">
					<input class="text_area" type="text" name="name" value="<?php echo stripslashes( $row->name ); ?>" size="50" maxlength="255" title="Длинное название, отображаемое в заголовках" />
					</td>
				</tr>
				<tr>
					<td>
					Рубрика:
					</td>
					<td colspan="2">
					<?php echo $lists['section']; ?>
					</td>
				</tr>
				<tr>
					<td>
                    Порядок расположения:
					</td>
					<td colspan="2">
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td>
					Видимо для:
					</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td>
					Видимо на сайте:
					</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" colspan="2">
					Описание:
					</td>
				</tr>
				<tr>
					<td colspan="3">
					<?php
					// parameters : areaname, content, hidden field, width, height, rows, cols
					editorArea( 'editor1',  $row->description , 'description', '100%;', '300', '60', '20' ) ; ?>
					</td>
				</tr>
				</table>
			</td>
			<td valign="top" width="40%"><?
				if ( $row->section > 0 || $row->section == 'content' ) {
				?><table class="adminform">
					<tr>
						<th colspan="2">
						Прикрепленное изображение
						</th>
					<tr>
					<tr>
						<td>
							<table width="100%">
								<tr>
										<td colspan="3"><?
										?><input type="hidden"  name="isrc_id" id="isrc_id" value="imagelib" /><?
										?><input type="hidden"  name="iuse" id="iuse" value="0" /><?
										?><input type="hidden"  name="input_id" id="input_id" value="image" /><?								
										?></td>
								</tr>
							
								<tr>
			
										<td width="30%" >Изображения содержимого:</td>
										<td width="60%" ><input type="text" class="inputbox" style="width:100%" name="image" id="image" value="<?php echo $row->image; ?>" onchange="document.getElementById('view_imagelist').src = document.getElementById('imagelist').value;" /></td>
										<td width="10%" ><a class="imagelist_class" onmousedown="return false;" href="javascript:iOpenImg('imagelist','src', 'image','advimage_image_browser_callback');">Изменить</a></td>
								</tr>
								<tr>
									<td>
									Расположение изображения:
									</td>
									<td colspan="2">
									<?php echo $lists['image_position']; ?>
									</td>
								</tr>
	
								<tr>
										<td width="30%">Выбранное изображение:</td>
										<td colspan="2"><img src="<? if ($row->image=='') print site_url."/images/insite/blank.png"; else echo $row->image; ?>" id="imagelib" name="imagelib" border="0" alt="Предпросмотр" /></td>
								</tr>
	
							</table>
						</td>
					</tr>
				</table><?

			
			
			
			
			
			
			}
			?>
			</td>
		</tr>
		</table><?
		?><input type="hidden" name="ca" value="categories" /><?
		?><input type="hidden" name="oldtitle" value="<?php echo $row->title ; ?>" /><?
		if (  strcmp($row->id,"")!=0  ){ 
			?><input type="hidden" name="id" value="<?php echo $row->id; ?>" /><?
		}
		?><input type="hidden" name="sectionid" value="<?php echo $row->section; ?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?><input type="hidden" name="redirect" value="<?php echo $redirect; ?>" /><?
		?><input type="hidden" name="hidemainmenu" value="0" /><?
		?></form><?
	}


	/**
	* Form to select Section to move Category to
	*/
	function moveCategorySelect( $option, $cid, $SectionList, $items, $sectionOld, $contents, $redirect ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th class="categories">
			Перемещение подрубрики
			</th>
		</tr>
		</table>

		<br />
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (!getSelectedValue( 'adminForm', 'sectionmove' )) {
				alert( "Пожалуйста, выберите раздел для перемещаемой подрубрики" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong>Переместить в раздел:</strong>
			<br />
			<?php echo $SectionList ?>
			<br /><br />
			</td>
			<td align="left" valign="top" width="20%">
			<strong>Перемещаемые подрубрики:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $items as $item ) {
				echo "<li>". $item->name ."</li>";
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top" width="20%">
			<strong>Перемещаемые объекты содержимого:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $contents as $content ) {
				echo "<li>". $content->title ."</li>";
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top">
			В выбранный раздел будут перемещены все
			<br />
			 перечисленные подрубрики и всё 
			<br />
			перечисленное содержимое этих подрубрик.
			</td>.
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $sectionOld;?>" />
		<input type="hidden" name="boxchecked" value="1" />
		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
		<input type="hidden" name="task" value="" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php
	}


	/**
	* Form to select Section to copy Category to
	*/
	function copyCategorySelect( $option, $cid, $SectionList, $items, $sectionOld, $contents, $redirect ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th class="categories">
			Копирование подрубрики
			</th>
		</tr>
		</table>

		<br />
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (!getSelectedValue( 'adminForm', 'sectionmove' )) {
				alert( "Пожалуйста, выберите рубрику для копируемой подрубрики" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong>Копировать в раздел:</strong>
			<br />
			<?php echo $SectionList ?>
			<br /><br />
			</td>
			<td align="left" valign="top" width="20%">
			<strong>Копируемые подрубрики:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $items as $item ) {
				echo "<li>". $item->name ."</li>";
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top" width="20%">
			<strong>Копируемое содержимое подрубрики:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $contents as $content ) {
				echo "<li>". $content->title ."</li>";
				echo "\n <input type=\"hidden\" name=\"item[]\" value=\"$content->id\" />";
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top">
			В выбранный раздел будут скопированы все
			<br />
			перечисленные подрубрики и всё 
			<br />
			перечисленное содержимое этих подрубрик.
			</td>.
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $sectionOld;?>" />
		<input type="hidden" name="boxchecked" value="1" />
		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
		<input type="hidden" name="task" value="" />
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