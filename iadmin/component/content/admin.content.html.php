<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class HTML_content {
	function showContent( &$rows, &$lists, $search, $pageNav, $all=NULL, $redirect ) {
		global $my, $database, $iConfig_offset, $option, $reg;
		
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);
		
		$component_comment = new comments($reg['ca'], $reg['db'], $reg);
		$component_comment->init();
		
?><form action="index2.php?ca=content" method="post" name="adminForm"><?
?><table class="adminheading"><?
?><tr><?
	?><td width="100%" nowrap="nowrap"><?
		$patheay_row_id = icsmarti('icsmart_content_catid');
		if (  $patheay_row_id>0  ) $patheay_row_data = ggo($patheay_row_id, "#__icat");
		else{
			$patheay_row_data = new stdClass();
			$patheay_row_data->name = '';
		}
		$iway[0] = new stdClass();
		$iway[1] = new stdClass();
		$iway[2] = new stdClass();
		
		$iway[0]->name=$reg['content_name'];
		$iway[0]->url="index2.php?ca=content";
		$iway[1]->name=stripslashes($patheay_row_data->name);
		$iway[1]->url="";
		$iway[2]->name="список документов";
		$iway[2]->url="";
	
		i24pwprint_admin ($iway);
	?></td><?
	?><td align="right" >Поиск:&nbsp;</td><?
	?><td align="right" ><input type="text" name="icsmart_content_search" value="<?php echo htmlspecialchars( icsmart('icsmart_content_search') );?>" class="inputtop" onchange="document.adminForm.submit();" /></td><?
	?><td align="right" ><?php echo $lists['catid'];?></td><?
	?><td ><input type="submit" value="Искать" class="gosearch" /></td><?
?></tr><?
?></table><?
if (  $patheay_row_id>0  ){
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__content";
		$table_drug->order="ordering";
}
?><table class="adminlist" <? if (  $patheay_row_id>0  ) print $table_drug->table(); ?> ><?
?><tr  <? if (  $patheay_row_id>0  ) print $table_drug->row(); ?>  ><?
	?><th width="5">#</th><?
	?><th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th><?
	?><th class="title">Заголовок</th><?
	?><th width="5%">На сайте</th><?
	?><th nowrap="nowrap" width="5%">На главной</th><?
	?><th <? ($patheay_row_id>0 ? print '' : print 'colspan="2"') ?> align="center" width="5%">Сортировка</th><?
	?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
	?><th >Фото</th><?
	?><th align="left" >Комментарии</th><?
	?><th width="2%" align="left">ID</th><?
	?><th align="left">Категория</th><?
	?><th align="left">Автор</th><?
	?><th align="center" width="10">Дата&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th><?
  ?></tr><?php
$k = 0;
$nullDate = $database->getNullDate();
for ($i=0, $n=count( $rows ); $i < $n; $i++) {
	$row = &$rows[$i];

	$link 	= 'index2.php?ca=content&task=edit&hidemainmenu=1&id='. $row->id;
	$row->cat_link 	= 'index2.php?ca=categories&task=editA&hidemainmenu=1&id='. $row->catid;
	$component_foto->parent = $row->id;
	$component_comment->parent = $row->id;

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
						$alt = 'Истек срок публикации';
	} elseif ( $row->state == 0 ) {
	// Unpublished
						$altstyle = ' style="color:#ff0000" ' ;
						$alt = 'Не&nbsp;опубликовано';
	}			

	// correct times to include server offset info
	$row->publish_up 	= mosFormatDate( $row->publish_up, _CURRENT_SERVER_TIME_FORMAT );
	if (trim( $row->publish_down ) == $nullDate || trim( $row->publish_down ) == '' || trim( $row->publish_down ) == '-' ) {
		$row->publish_down = 'Никогда';
	}
	$row->publish_down 	= mosFormatDate( $row->publish_down, _CURRENT_SERVER_TIME_FORMAT );

	$times = '';
		if ($row->publish_up == $nullDate) {
			$times .= "<tr><td>Начало: Всегда</td></tr>";
		} else {
			$times .= "<tr><td>Начало: $row->publish_up</td></tr>";
		}
	if ($row->publish_down == $nullDate || $row->publish_down == 'Никогда') {
			$times .= "<tr><td>Окончание: Без окончания</td></tr>";
		} else {
			$times .= "<tr><td>Окончание: $row->publish_down</td></tr>";
	}

	$cont_user = ggo ( $row->created_by, "#__users" );
	if (  $cont_user!=false  ) {
		if ( $row->created_by_alias ) {
			$author = $row->created_by_alias;
		} else {
			$linkA 	= '';
			$author = $cont_user->name;
		}
	} else {
		$author = "Нет автора";
	}

	$date = mosFormatDate( $row->created, '%d-%m-%Y' );

	$access 	= mosCommonHTML::AccessProcessing( $row, $i );
	$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
	?><tr   <? if (  $patheay_row_id>0  ) print $table_drug->row($row->id, $row->ordering); ?>     class="<?php echo "row$k"; ?>"><?
		?><td><?php echo $pageNav->rowNumber( $i ); ?></td><?
		?><td align="center"><?php echo $checked; ?></td><?
		?><td align="left"><a href="<?php echo $link; ?>" title="Изменить содержимое"><?php echo $row->title; ?><?php if (  $row->title_alias  )  echo " (".$row->title_alias.")"; ?></a></td><?
		if ( $times ) {
			?><td align="center"><? if ($altstyle) print "<span $altstyle >";
			echo $alt;
			if ($altstyle) print "</span>"; ?></td><?
		} 
		if (  ggsqlr("select count(content_id) from #__content_frontpage where #__content_frontpage.content_id=".$row->id."; ")>0  ) $row->frontpage  = true;
		else $row->frontpage  = false;
		$real_alt = ( $row->frontpage ) ? 'Убрать с главной' : 'Отобразить на главной';
		$real_action = ( $row->frontpage ) ? 'toggle_frontpage' : 'toggle_frontpage';
		?><td align="center"><a title="<? print $real_alt; ?>" onclick="return listItemTask('cb<? print $i ?>','<? print $real_action; ?>')" href="javascript: void(0);"><?php echo ( $row->frontpage ) ? 'Да' : 'Нет';?></a></td><?
		if (  $patheay_row_id>0  ){
			?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
		} else {
			?><td align="right"><?php echo $pageNav->orderUpIcon( $i, ($row->catid == @$rows[$i-1]->catid) ); ?></td><?
			?><td align="left"><?php echo $pageNav->orderDownIcon( $i, $n, ($row->catid == @$rows[$i+1]->catid) ); ?></td><?
		}
		?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /></td><?
		?><td align="center" nowrap="nowrap"><a target="_blank" href="<?=$component_foto->get_link(); ?>">смотреть (<? print $component_foto->howmany_fotos(); ?>)</a></td><?
		?><td align="center" nowrap="nowrap"><a href="<?=$component_comment->get_link(); ?>">смотреть (<?=$component_comment->howmany_comments(); ?>)</a></td><?
		?><td align="left"><?php echo $row->id; ?></td><?
		?><td><?php $iexcurcat = ggo($row->catid, "#__icat");
			$iexsubcat_prefix = "";  $iexcurcat_name = $iexcurcat->name; $iexcurcat_id = $iexcurcat->id;
			$iexcatlev = 0;  $iexcatslevs = array();
			while ($iexcurcat->parent!=0){
				$iexcurcat = ggo($iexcurcat->parent, "#__icat");
				$iexcatslevs[] = $iexcurcat->name;
			}
			for ($exc = count($iexcatslevs)-1; $exc>=0; $exc--){
				for ($j=0; $j<$exc; $j++) $iexsubcat_prefix_white .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$iexsubcat_prefix = $iexsubcat_prefix.$iexcatslevs[$exc]."<br />".$iexsubcat_prefix_white;
			}
			if (  $iexsubcat_prefix  ){
				$iexsubcat_prefix = $iexsubcat_prefix."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$iexcurcat_name;
				?><span onMouseOver="return Tip('<? print $iexsubcat_prefix; ?>');"><? print $iexcurcat_name;?></span><?
			} else {
				print $iexcurcat_name;
			}
		?></td><?
		?><td align="left"><?php echo $author; ?></td><?
		?><td align="left"><?php echo $date; ?></td><?
	?></tr><?
	$k = 1 - $k;
}
?></table><?
if (  $patheay_row_id>0  ) $table_drug->debug_div();
echo $pageNav->getListFooter(); 
?><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
?><input type="hidden" name="task" value="" /><?
?><input type="hidden" name="boxchecked" value="0" /><?
?><input type="hidden" name="hidemainmenu" value="0" /><?
?><input type="hidden" name="redirect" value="<?php echo $redirect;?>" /><?
?></form>
<?php
}

	function showArchive( &$rows, &$lists, $search, $pageNav, $option, $all=NULL, $redirect ) {
		global $my, $acl, $reg;

		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if (pressbutton == 'remove') {
				if (document.adminForm.boxchecked.value == 0) {
					alert('Пожалуйста, выберите из списка объекты, которые Вы хотите отправить в корзину');
				} else {
					submitform('remove');
				}
			} else {
				submitform(pressbutton);
			}
		}
		</script><?
		?><form action="index2.php" method="post" name="adminForm"><?
		?><table class="adminheading"><?
		?><tr><?
			?><td width="100%" ><?
				$iway[0]->name=$reg['content_name'];
				$iway[0]->url="index2.php?ca=content";
				$iway[1]->name="Управление архивом";
				$iway[1]->url="";
			
				i24pwprint_admin ($iway);
			?></td><?
			?><td valign="middle">Поиск:&nbsp;</td><?
			?><td valign="middle" align="right" nowrap="nowrap" style="vertical-align:top; padding-top:0px;"><input type="text" name="icsmart_content_search" value="<?php echo htmlspecialchars( $search );?>" class="inputbox" onChange="document.adminForm.submit();" /></td><?
			?><td align="right" valign="middle">
			<?php echo $lists['catid'];?>
			</td>
		</tr>
		</table><?

		?><table class="adminlist"><?
		?><tr><?
			?><th width="5">#</th><?
			?><th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th><?
			?><th class="title">Заголовок</th><?
			?><th width="3%"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )">Сохранить&nbsp;порядок</a></th><?
			?><th width="15%" align="left">Категория</th><?
			?><th width="15%" align="left">Автор</th><?
			?><th align="center" width="10">Дата</th><?
			?><th align="left" width="10">Комментарии</th><?
		?></tr><?
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

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

			$date = mosFormatDate( $row->created, '%x' );
			?><tr class="<?php echo "row$k"; ?>"><?
				?><td><?php echo $pageNav->rowNumber( $i ); ?></td><?
				?><td width="20"><?php echo mosHTML::idBox( $i, $row->id ); ?></td><?
				?><td align="left"><?php echo $row->title; ?></td><?
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /></td><?
				?><td><?php echo $row->name; ?></td><?
				?><td><?php echo $author; ?></td><?
				?><td><?php echo $date; ?></td><?
				?><td align="center" nowrap="nowrap"><a href="index2.php?ca=content_comment&amp;task=view&amp;icsmart_comment_parent=<? print $row->id; ?>&amp;icsmart_content_comment_ire=<? print $row->id; ?>">смотреть (<? print ggsqlr ("SELECT count(id) FROM #__ire WHERE cid=".$row->id.""); ?>)</a></td><?
			?></tr><?php
			$k = 1 - $k;
		}
		?></table><?

		echo $pageNav->getListFooter(); 

		?><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
		?><input type="hidden" name="task" value="showarchive" /><?
		?><input type="hidden" name="returntask" value="showarchive" /><?
		?><input type="hidden" name="boxchecked" value="0" /><?
		?><input type="hidden" name="hidemainmenu" value="0" /><?
		?><input type="hidden" name="redirect" value="<?php echo $redirect;?>" /><?
		?></form><?

	}

	function editContent( &$row, &$lists, &$images, &$params, $option, $redirect, &$menus ) {
		global $database, $reg;

		$nullDate 	= $database->getNullDate();
		$create_date = null;

		if ( $row->created != $nullDate ) {
			$create_date 	= mosFormatDate( $row->created, '%A, %d %B %Y %H:%M', 0 );
		}
		$mod_date = null;
		if ( $row->modified != $nullDate ) {
			$mod_date 		= mosFormatDate( $row->modified, '%A, %d %B %Y %H:%M', 0 );
		}

		$tabs = new iTabs(1);

		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);
		$component_foto->parent = $row->id;

                $component_file = new component_file ( 0 );
                $component_file->init( $reg['ca'] );
                $component_file->parent = $row->id;

		//names
		$names = new names($row->id, $reg['ca'], $reg);


		//mosCommonHTML::loadOverlib();
		mosCommonHTML::loadCalendar();
		?>
		<script language="javascript" type="text/javascript">
		<!--
		var folderimages = new Array;

		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if ( pressbutton == 'menulink' ) {
				if ( form.menuselect.value == "" ) {
					alert( "Пожалуйста, выберите меню" );
					return;
				} else if ( form.link_name.value == "" ) {
					alert( "Пожалуйста, введите имя для этого пункта меню" );
					return;
				}
			}
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			//form.images.value = form.imagelist.value;

			if (form.title.value == ""){
				alert( "Этот объект должен иметь заголовок" );
			} else if (form.catid.value == "0"){
				alert( "Вы должны выбрать категорию." );
			} else {
				<?php getEditorContents( 'editor1', 'introtext' ) ; ?>
				<?php getEditorContents( 'editor2', 'fulltext' ) ; ?>
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		
		<form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<td class="edit"><?
			
				$iway[0]->name=$reg['content_name'];
				$iway[0]->url="";
				$iway[1]->name= $row->id ? 'Редактирование' : 'Новый';
				$iway[1]->url="";
	
				i24pwprint_admin ($iway,0);

			?></td>
		</tr>
		</table>

		<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="75%" valign="top">
				<table width="100%" class="adminform">
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr><th colspan="4">Детали объекта</th></tr>
						<tr>
							<td>Заголовок:</td>
							<td><input class="text_area" type="text" name="title" size="55" maxlength="100" value="<?php echo $row->title; ?>" /></td>
							<td>Категория:</td>
							<td><?php echo $lists['catid']; ?></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<td><span style="float:left;">Адрес&nbsp;страницы:&nbsp;</span><span style="float:right;"><?=site_url ?><?php echo $row->sefnamefullcat; ?></span></td>
							<td><input class="inputbox" type="text" name="sefname" size="100" maxlength="100" value="<?php echo $row->sefname; ?>" /></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="100%">Вводный Текст: (обязательно)<br />
					<!--<textarea name="area" id="introtext" rows="30"><?php echo $row->introtext; ?></textarea>-->
					<?php editorArea( 'editor1',  $row->introtext , 'introtext', '100%;', '350', '75', '20' ) ; ?></td>
				</tr>
				<tr>
					<td width="100%">Основной текст: (опционально)<br />
					<!--<textarea name="area" id="fulltext" rows="30"><?php echo $row->fulltext; ?></textarea>-->
					<?php editorArea( 'editor2',  $row->fulltext , 'fulltext', '100%;', '800', '75', '60' ) ; ?></td>
				</tr>
				</table>
			</td>
			<td valign="top" width="25%">
					<?php	
					$tabs->startPane("content-pane");
                                        $tabs->table_width = 310;
					$tabs->startTab("Публикация","publish-page"); 
					?>
							<table class="adminform" style="width:<?=$tabs->table_width ?>px; ">
								<tr><th colspan="2">Сведения о публикации </th></tr>
								<tr>
									<td valign="top" align="right" width="120">Показывать на главной:</td>
									<td><input type="checkbox" name="frontpage" value="1" <?php echo $row->frontpage ? 'checked="checked"' : ''; ?> /></td>
								</tr>
								<tr>
									<td valign="top" align="right">Опубликовано:</td>
									<td><input type="checkbox" name="published" value="1" <?php echo $row->state ? 'checked="checked"' : ''; ?> /></td>
								</tr>
								<tr>
									<td valign="top" align="right">Уровень доступа:</td>
									<td><?php echo $lists['access']; ?></td>
									</tr>
								<tr>
									<td valign="top" align="right">Псевдоним автора:</td>
									<td><input type="text" name="created_by_alias" size="30" maxlength="100" value="<?php echo $row->created_by_alias; ?>" class="text_area" /></td>
								</tr>
								<tr>
									<td valign="top" align="right">Автор:</td>
									<td><?php echo $lists['created_by']; ?></td>
								</tr>
								<tr>
									<td valign="top" align="right">Порядок:</td>
									<td><?php echo $lists['ordering']; ?></td>
								</tr>
								<tr>
									<td valign="top" align="right">Перезаписать дату создания:</td>
									<td><input class="text_area" type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $row->created; ?>" />
										<input name="reset" type="reset" class="button" onclick="return showCalendar('created', 'y-mm-dd');" value="..." />
									</td>
								</tr>
								<tr>
									<td valign="top" align="right">Начало публикации:</td>
									<td><input class="text_area" type="text" name="publish_up" id="publish_up" size="25" maxlength="19" value="<?php echo $row->publish_up; ?>" />
										<input type="reset" class="button" value="..." onclick="return showCalendar('publish_up', 'y-mm-dd');" />
									</td>
								</tr>
								<tr>
									<td valign="top" align="right">Окончание публикации:</td>
									<td><input class="text_area" type="text" name="publish_down" id="publish_down" size="25" maxlength="19" value="<?php echo $row->publish_down; ?>" />
										<input type="reset" class="button" value="..." onclick="return showCalendar('publish_down', 'y-mm-dd');" />
									</td>
								</tr>
							</table>
							<br />
							<table class="adminform" style="width:<?=$tabs->table_width ?>px; ">
								<?php
								if ( $row->id ) { ?>
									<tr>
										<td><strong>ID объекта:</strong></td>
										<td><?php echo $row->id; ?></td>
									</tr><?php
								} ?>
								<tr>
									<td width="120" valign="top" align="right"><strong>Состояние:</strong></td>
									<td><?php echo $row->state > 0 ? 'Опубликовано' : ($row->state < 0 ? 'В архиве' : 'Черновик - Не опубликован');?></td>
								</tr>
								<tr>
									<td valign="top" align="right"><strong>Изменялось:</strong></td>
									<td><?php echo $row->version;?> раз</td>
								</tr>
								<tr>
									<td valign="top" align="right"><strong>Создано</strong></td>
									<td><?php
										if ( !$create_date ) { ?>Новый документ<?php } 
										else { echo $create_date;	} ?>
									</td>
								</tr>
								<tr>
									<td valign="top" align="right"><strong>Последнее изменение:</strong></td>
									<td><?php
										if ( !$mod_date ) { ?>Не менялся<?php } 
										else { echo $mod_date; ?><br /><?php echo $row->modifier;	}	?>
									</td>
								</tr>
							</table>
					<?php
					$tabs->endTab();
					$tabs->startTab("Фото","images-page");
							?><table class="adminform" style="width:<?=$tabs->table_width ?>px; "><?
								?><tr><?
										?><th colspan="2">Основное изображение</th><?
								?></tr><?
								?><tr><?
										?><td colspan="2"><?
												?><table width="100%"><?
												?><tr><?
														?><td width="30%" >Изображения содержимого:</td><?
														?><td width="70%" ><input type="file" class="inputbox" style="width:100%" name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /></td><?
												?></tr><?
												?><tr><?
														?><td></td><?
														?><td ><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr>
																	<tr><? component_foto::delmainfoto_checkbox(); ?></tr>
															   </table></td><?
												?></tr><?
		
												?><tr><?
														?><td>Текущее<br />основное<br />изображение:</td><?
														?><td><? $component_foto->parent_obj=&$row; $component_foto->previewMainFoto(); ?></td><?
												?></tr><?
												?><tr><?
														?><td colspan="2"><input type="hidden" name= "_source" value="" /><?
														?><input type="hidden"  name="iuse" id="iuse" value="0" /><?
														?><input type="hidden" name="input_id" id="input_id" value="imagelist" /><?
														?><input type="hidden"  name="isrc_id" id="isrc_id" value="view_imagelist" /><?
														?><input type="hidden" name="_alt" value="" /><?
														?><input type="hidden" name="_border" value="" size="3" maxlength="1" /><?
														?><input type="hidden" name="_caption" value="" size="30" /><?
														?><input type="hidden" name="_width" value="" size="5" maxlength="5" /><?
														?><input type="hidden" name="_align" value="" size="5" maxlength="5" /><?
														?><input type="hidden" name="_caption_position" value="bottom" size="5" maxlength="5" /><?
														?><input type="hidden" name="_caption_align" value="" size="5" maxlength="5" /><?
														?><input type="hidden" name="link_name" class="inputbox" value="" size="30" /><?
														?></td><?
												?></tr>
												</table>
										</td>
								</tr><?
								?><tr><?
										?><th colspan="2">Фотогалерея &mdash; <?
												if (  $row->id  ){ $component_foto->make_galery_link(); }
												else{	?>Нет<?	}
										?></th><?
								?></tr><?
								$icontent_fotos = $component_foto->get_fotos();
								if (  count ($icontent_fotos)>0  )
								foreach ($icontent_fotos as $icontent_foto){
									?><tr><?
											?><td colspan="2" align="center" style="text-align:center"><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<? print site_url."/images/icat/icont/".$icontent_foto->org ?>" ><img name="view_imagelist" id="view_imagelist" src="<? print site_url."/images/icat/icont/".$icontent_foto->small ?>" border="5" style="border-color:#cccccc" /></a></td><?
									?></tr><?
									?><tr><?
											?><td colspan="2" align="center" style="text-align:center"><? print $icontent_foto->desc; ?><br /><br /></td><?
									?></tr><?
								} 
								?></table>
					<?php
					$tabs->endTab();
					$tabs->startTab("Параметры","params-page");
					?>
							<table class="adminform" style="width:<?=$tabs->table_width ?>px; ">
							<tr><th colspan="2">Управление параметрами</th></tr>
							<tr>
								<td>* Эти параметры управляют внешним видом только в режиме полного просмотра *<br /><br /></td>
							</tr>
							<tr><td><?php echo $params->render();?></td></tr>
							</table>
					<?php
					$tabs->endTab();
					$tabs->startTab("Расширеные","metadata-page");
					?>
							<table class="adminform" style="width:<?=$tabs->table_width ?>px; ">
							<tr><th colspan="2">Данные  мета-тегов</th></tr>
							<tr><?
									?><td>Псевдоним заголовка:<br /><?
									?><input name="title_alias" type="text" class="text_area" id="title_alias" style="width:300px; " value="<?php echo $row->title_alias; ?>" size="30" maxlength="100" /></td><?
							?></tr><?
							?><tr><?
									?><td>Содержимое &lt;title&gt;:<br /><?
									?><input name="seo_title" type="text" class="text_area" id="seo_title" style="width:300px; " value="<?php echo $row->seo_title; ?>" size="30" maxlength="100" /></td><?
							?></tr><?
							?><tr><?
								?><td>Описание (Description):<br /><input type="text" class="text_area"  style="width: 300px; " name="metadesc" value="<?php echo str_replace('&','&amp;',$row->metadesc); ?>"></td><?
							?></tr><?
							?><tr><?
								?><td>Ключевые слова (Keywords)<br /><input type="text" class="text_area" style="width: 300px; " name="metakey" value="<?php echo str_replace('&','&amp;',$row->metakey); ?>"></td><?
							?></tr>
							</table>
							
							<? if($reg["contentAllowTags"] == 1){ ?> 
									<table class="adminform" style="width:<?=$tabs->table_width ?>px; ">
										<tr><th>Тэги: </th></tr>
										<tr>
											<td nowrap="nowrap" style="white-space:nowrap; "><a href="javascript: ins_ajax_open('?ca=tags_ajax&task=showtags&4ajax=1', 570, 570); void(0);" title="Показать список тегов"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle" /></a><?
												try {	$tag = new tags("content", $database, $reg);  print $tag->field($row->id, 150, "exgood_tags", "content_tegs_names_style");	}
												catch (Exception $e){	print $e->getMessage();   }
											?></td>
										</tr>
								  </table>
							<? } ?>
							<table class="adminform" style="width:<?=$tabs->table_width ?>px; ">
								<tr>
									<th><?=$reg['names_name']?>: </th></tr>
								<tr>
									<td nowrap="nowrap" style="white-space:nowrap; "><a href="javascript: ins_ajax_open('?ca=names_ajax&task=shownames&4ajax=1', 570, 570); void(0);" title="Паказать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a><?
										 print $names->field($row->id, 150, "all_names", "_names_field", "content_tegs_names_style");
									?></td>
								</tr>
                                                         </table>
                                                         <table class="adminform" style="width:<?=$tabs->table_width ?>px; ">
								<tr>
									<th><?=$reg['file_name']?> &mdash; <?
												if (  $row->id  ){ $component_file->make_edit_link(); }
												else{	?>Нет<?	}
										?></th></tr>
								<tr>
									<td nowrap="nowrap" style="white-space:nowrap; "><?
                                                                            $component_file->iadmin_show_files();
									?></td>
								</tr>
                                                        </table>

							
							
					<?php
					$tabs->endTab();
					$tabs->endPane();
					?>
					</td>
				</tr>
				</table>




				<?
					/*
					 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
					 * например индивидуальные параметры для фотографий
					 */
					load_adminclass('config');	$conf = new config($reg['db']);
					$conf->prefix_id = '#__content'."_ID".$row->id."__";
					$conf->returnme('index2.php?ca='.$reg['ca'].'&task=edit&hidemainmenu=1&id='.$row->id );
					$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
                                        
                                        

				?>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="version" value="<?php echo $row->version; ?>" />
		<input type="hidden" name="mask" value="0" />
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="images" value="" />
		<input type="hidden" name="hidemainmenu" value="1" />
		</form>
		
		<?php

	}



	/**
	* Form to select Section/Category to move item(s) to
	* @param array An array of selected objects
	* @param int The current section we are looking at
	* @param array The list of sections and categories to move to
	*/
	function moveSection( $cid, $sectCatList, $option, $items ) {
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (!getSelectedValue( 'adminForm', 'sectcat' )) {
				alert( "Пожалуйста, выберите что-нибудь" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th class="edit">
			Перемещение объектов
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td align="left" valign="top" width="40%">
			<strong>Переместить в рубрику:</strong>
			<br />
			<?php echo $sectCatList; ?>
			<br /><br />
			</td>
			<td align="left" valign="top">
			<strong>Будут перемещены объекты:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $items as $item ) {
				echo "<li>". $item->title ."</li>";
			}
			echo "</ol>";
			?>
			</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<?php
		foreach ($cid as $id) {
			echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php
	}
	
	/**
	* Form to select Section/Category to copys item(s) to
	*/
	function copySection( $option, $cid, $sectCatList, $items  ) {
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (!getSelectedValue( 'adminForm', 'sectcat' )) {
				alert( "Пожалуйста, выберите рубрику для копирования объектов в " );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th class="edit">
			Копирование объектов содержимого
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td align="left" valign="top" width="40%">
			<strong>Копировать в рубрику:</strong>
			<br />
			<?php echo $sectCatList; ?>
			<br /><br />
			<strong>Префикс (для названия копии):</strong><br/>
			<input type="text" name="copyprefix" value="_копия" />
			</td>
			<td align="left" valign="top">
			<strong>Будут скопированы объекты:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $items as $item ) {
				echo "<li>". $item->title ."</li>";
			}
			echo "</ol>";
			?>
			</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<?php
		foreach ($cid as $id) {
			echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php
	}
}
?>