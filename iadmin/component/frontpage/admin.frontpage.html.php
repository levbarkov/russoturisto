<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* @package Joomla RE
* @subpackage Content
*/
class HTML_content {
	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
	function showList( &$rows, $search, $pageNav, $option, $lists ) {
		global $my, $acl, $database;

		$nullDate = $database->getNullDate();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<td  nowrap="nowrap" width="100%">Управление главной страницей</td>
			<td align="right" colspan="2">Фильтр:</td>
			<td><input type="text" name="icsmart_frontpage_search" value="<?php echo htmlspecialchars( $search );?>" class="text_area" onChange="document.adminForm.submit();" /></td>
			<td width="right"><?php echo $lists['catid'];?></td>
		</tr>
		</table><?
		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__content_frontpage";
		$table_drug->id_field="content_id";
		$table_drug->order="ordering";
		?><table class="adminlist" <?=$table_drug->table(); ?>>
		<tr <?=$table_drug->row(); ?>>
			<th width="5">#</th>
			<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
			<th class="title">Заголовок</th>
			<th width="10%" nowrap="nowrap">На сайте</th>
			<th nowrap="nowrap" width="5%">Сортировка</th>
			<th width="3%"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th>
			<th width="8%" nowrap="nowrap">Доступ</th>
			<th width="10%" align="left">Раздел</th>
			<th width="10%" align="left">Категория</th>
			<th width="10%" align="left">Автор</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$link = 'index2.php?ca=content&task=edit&hidemainmenu=1&id='. $row->id;

			$now = _CURRENT_SERVER_TIME;
			if ( $now <= $row->publish_up && $row->state == 1 ) {
			// Published
								$altstyle = '' ;
								$alt = 'Опубликовано';
			} else if ( ( $now <= $row->publish_down || $row->publish_down == $nullDate ) && $row->state == 1 ) {
			// Pending
								$altstyle = '' ;
								$alt = 'Опубликовано';
			} else if ( $now > $row->publish_down && $row->state == 1 ) {
			// Expired
								$altstyle = ' style="color:#ff0000" ' ;
								$alt = 'Истек срок&nbsp;публикации';
			} elseif ( $row->state == 0 ) {
			// Unpublished
								$altstyle = ' style="color:#ff0000" ' ;
								$alt = 'Не&nbsp;опубликовано';
			}	

			$times = '';
			if ( isset( $row->publish_up ) ) {
				  if ( $row->publish_up == $nullDate) {
						$times .= '<tr><td>Начало: Всегда</td></tr>';
				  } else {
						$times .= '<tr><td>Начало: '. $row->publish_up .'</td></tr>';
				  }
			}
			if ( isset( $row->publish_down ) ) {
				  if ($row->publish_down == $nullDate) {
						$times .= '<tr><td>Окончание: Без срока</td></tr>';
				  } else {
				  $times .= '<tr><td>Окончание: '. $row->publish_down .'</td></tr>';
				  }
			}

			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );

			$cont_user = ggo ( $row->created_by, "#__users" );
			if (  $cont_user!=false  ) {
				if ( $row->created_by_alias ) {
					$author = $row->created_by_alias;
				} else {
					$linkA 	= '';
					$author = $row->author;
				}
			} else {
				$author = "Нет автора";
			}
			?><tr <?=$table_drug->row($row->id, $row->ordering); ?>   class="<?php echo "row$k"; ?>"><?
				?><td><?php echo $pageNav->rowNumber( $i ); ?></td><?
				?><td><?php echo $checked; ?></td><?
				?><td align="left"><a href="<?php echo $link; ?>" title="Изменить содержимое"><?php echo $row->title; ?></a></td><?php
				if ( $times ) {
					?><td align="center"><? if ($altstyle) print "<span $altstyle >";
					echo $alt;
					if ($altstyle) print "</span>"; ?></td><?
				} 
				?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->fpordering;?>" class="text_area" style="text-align: center" /></td><?
				?><td align="center"><?php echo $access;?></td><?
				?><td><?php echo $row->sect_name; ?></td><?
				?><td><?php echo $row->name; ?></td><?
				?><td><?php echo $author; ?></td><?
			?></tr><?
			$k = 1 - $k;
		}
		?></table><?
		$table_drug->debug_div();
		echo $pageNav->getListFooter();
		?><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?><input type="hidden" name="boxchecked" value="0" /><?
		?></form><?php
	}
}
?>