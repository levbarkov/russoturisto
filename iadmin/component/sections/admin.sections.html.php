<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* @package Joomla RE
* @subpackage Sections
*/
class sections_html {
	/**
	* Writes a list of the categories for a section
	* @param array An array of category objects
	* @param string The name of the category section
	*/
	function show( &$rows, $scope, $myid, &$pageNav, $option ) {
		global $my;

		?><form action="index2.php" method="post" name="adminForm"><?
		?><table class="adminheading"><?
		?><tr><?
			 ?><td width="100%">Рубрики</td><?
		?></tr><?
		?></table><?

		?><table class="adminlist"><?
		?><tr><?
			?><th width="20">#</th><?
			?><th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows );?>);" /></th><?
			?><th class="title">Рубрика</th><?
			?><th colspan="2" width="5%">Сортировка</th><?
			?><th width="3%"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th width="10%">Состояние</th><?
			?><th width="8%">Видимо для</th><?
			?><th width="12%" nowrap="nowrap">ID</th><?
			?><th width="12%" nowrap="nowrap">Подрубрики</th><?
			?><th width="12%" nowrap="nowrap">Активных</th><?
		?></tr><?
		$k = 0;
		for ( $i=0, $n=count( $rows ); $i < $n; $i++ ) {
			$row = &$rows[$i];
			$link = 'index2.php?ca=sections&scope=content&task=editA&hidemainmenu=1&id='. $row->id;

			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			$published 	= "";//mosCommonHTML::PublishedProcessing( $row, $i );
            $published  = $row->published ? 'Опубликовано' : '<span style="color:#ff0000;">Не опубликовано</span>';
			
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20" align="right">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td width="20">
				<?php echo $checked; ?>
				</td>
				<td width="35%">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->name. " ( ". $row->title ." )";
				} else {
					?>
					<a href="<?php echo $link; ?>">
					<?php echo $row->name. " ( ". $row->title ." )"; ?>
					</a>
					<?php
				}
				?>
				</td>
				<td>
				<?php echo $pageNav->orderUpIcon( $i ); ?>
				</td>
				<td>
				<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
				</td>
				<td align="center" >
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td align="center">
				<?php echo $published;?>
				</td>
				<td align="center">
				<?php echo $access;?>
				</td>
				<td align="center">
				<?php echo $row->id; ?>
				</td>
				<td align="center">
				<?php echo $row->categories; ?>
				</td>
				<td align="center">
				<?php echo $row->active; ?>
				</td>
				<?php
				$k = 1 - $k;
				?>
			</tr>
			<?php
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); 
		?><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
		?><input type="hidden" name="scope" value="<?php echo $scope;?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?><input type="hidden" name="chosen" value="" /><?
		?><input type="hidden" name="act" value="" /><?
		?><input type="hidden" name="boxchecked" value="0" /><?
		?><input type="hidden" name="hidemainmenu" value="0" /><?
		?></form><?
	}

	function edit( &$row, $option, &$lists, &$menus ) {
		if ( $row->name != '' ) {
			$name = $row->name;
		} else {
			$name = "Новая рубрика";
		}

		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
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
					alert( "Пожалуйста, введите имя для этого пункта меню" );
					return;
				}
			}

			if (form.name.value == ""){
				alert("Рубрика должена иметь название");
			} else if (form.title.value ==""){
				alert("Рубрика должена иметь заголовок");
			} else {
				<?php getEditorContents( 'editor1', 'description' ) ; ?>
				submitform(pressbutton);
			}
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="sections">
			Рубрика:
			
			<?php echo $row->id ? 'Изменение' : 'Новая';?>
			
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td valign="top" width="60%">
				<table class="adminform">
				<tr>
					<th colspan="3">
					Детали рубрики
					</th>
				<tr>
				<tr>
					<td>
					Заголовок:
					</td>
					<td colspan="2">
					<input class="text_area" type="text" name="title" value="<?php echo $row->title; ?>" size="50" maxlength="50" title="Короткое имя для меню" />
					</td>
				</tr>
				<tr>
					<td>
					Название <?php echo (isset($row->section) ? "подрубрики" : "рубрики");?>:
					</td>
					<td colspan="2">
					<input class="text_area" type="text" name="name" value="<?php echo $row->name; ?>" size="50" maxlength="255" title="Длинное название, отображаемое в заголовках" />
					</td>
				</tr>
				<tr>
					<td> 
					Порядок отображения:
					</td>
					<td colspan="2">
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td>
					Уровень доступа:
					</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td>
					Опубликовано:
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
			<td valign="top">
				<table class="adminform">
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
				</table>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="scope" value="<?php echo $row->scope; ?>" />
		<? if (  strcmp($row->id,"")!=0  ){ ?>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<? } ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="oldtitle" value="<?php echo $row->title ; ?>" />
		</form>
		<?php
	}


	/**
	* Form to select Section to copy Category to
	*/
	function copySectionSelect( $option, $cid, $categories, $contents, $section ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th class="sections">
			Копирование рубрики
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong>Название копии рубрики:</strong>
			<br />
			<input class="text_area" type="text" name="title" value="" size="35" maxlength="50" title="Название новой рубрики" />
			<br /><br />
			</td>
			<td align="left" valign="top" width="20%">
			<strong>Копируемые категории:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $categories as $category ) {
				echo "<li>". $category->name ."</li>";
				echo "\n <input type=\"hidden\" name=\"category[]\" value=\"$category->id\" />";
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top" width="20%">
			<strong>Копируемые объекты содержимого:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $contents as $content ) {
				echo "<li>". $content->title ."</li>";
				echo "\n <input type=\"hidden\" name=\"content[]\" value=\"$content->id\" />";
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top">
			Во вновь созданный рубрике будут <br />
			скопированы перечисленные категории <br />
			и все перечисленные объекты<br /> 
			содержимого категорий.
			</td>.
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="boxchecked" value="1" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="scope" value="content" />
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