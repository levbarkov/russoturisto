<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* @package Joomla RE
* @subpackage Content
*/
class HTML_typedcontent {

        /**
        * Writes a list of the content items
        * @param array An array of content objects
        */
        function showContent( &$rows, &$pageNav, $option, $search, &$lists ) {
                global $my, $acl, $database, $reg;

                ?><form action="index2.php" method="post" name="adminForm"><?
?><table class="adminheading"><?
?><tr><?
		?><td width="100%" ><?php
		    $iway[0] = new stdClass();
			$iway[1] = new stdClass();
			$iway[0]->name="Статичное содержимое";
			$iway[0]->url="index2.php?ca=typedcontent";
			$iway[1]->name = "Список документов";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td><?
		?><td>Поиск:&nbsp;</td><?
		?><td><input type="text" name="icsmart_typedcontent_search" value="<?php echo htmlspecialchars( icsmart('icsmart_typedcontent_search') );?>" class="inputtop" /></td><?
		?><td><?php echo $lists['order']; ?></td><?
		?><td width="right"><?php echo $lists['authorid'];?></td><?
		?><td ><input type="submit" value="Искать" class="gosearch" /></td><?
?></tr><?
?></table><?
?><table class="adminlist"><?
?><tr><?
?><th width="5">#</th><?
?><th width="5px"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th><?
?><th class="title">Заголовок</th><?
?><th width="5%">На сайте</th><?
?><th width="3%"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )"   onMouseOver="return Tip('Сохранить заданный порядок отображения');" >Сохранить&nbsp;порядок</a></th><?
?><th width="10%">Фото</th><?
?><th width="5%">ID</th><?
?><th width="1%" align="left">Ссылок</th><?
?><th width="20%" align="left">Автор</th><?
?><th align="center" width="10">Дата</th><?
?></tr><?
		$k = 0;
		$nullDate = $database->getNullDate();
		
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);

		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$now = _CURRENT_SERVER_TIME;
			$component_foto->parent = $row->id;
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
										$times .= "<tr><td>Окончание: Без срока</td></tr>";
								} else {
										$times .= "<tr><td>Окончание: $row->publish_down</td></tr>";
						}
		
						if ( !$row->access ) {
								$color_access = 'style="color: green;"';
								$task_access = 'accessregistered';
						} else if ( $row->access == 1 ) {
								$color_access = 'style="color: red;"';
								$task_access = 'accessspecial';
						} else {
								$color_access = 'style="color: black;"';
								$task_access = 'accesspublic';
						}
		
						$link = 'index2.php?ca=typedcontent&task=edit&hidemainmenu=1&id='. $row->id;
						$access 	= mosCommonHTML::AccessProcessing( $row, $i );
						$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
						//ggtr ($my,2);
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
		
						$date = mosFormatDate( $row->created, '%x' );
						?>
		<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td align="left"><a href="<?php echo desafelySqlStr($link); ?>" title="Изменить статичное содержимое"><? echo $row->title; if ( $row->title_alias ) { echo ' (<i>'. $row->title_alias .'</i>)'; }	?></a></td>
				<?php if ( $times ) {
						?><td align="center"><? if ($altstyle) print "<span $altstyle >";
						echo $alt;
						if ($altstyle) print "</span>"; ?></td>
				<?php }
				?><td align="center" ><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /></td><?
				?><td align="center" nowrap="nowrap"><a target="_blank" href="<?=$component_foto->get_link(); ?>">смотреть (<? print $component_foto->howmany_fotos(); ?>)</a></td><?
				?><td align="center"><?php echo $row->id;?></td><?
				?><td align="center"><?php echo $row->links;?></td><?
				?><td align="left"><?php echo $author;?></td><?
				?><td><?php echo $date; ?></td><?
		?></tr><?
		$k = 1 - $k;
	}
?>
</table>

                <?php echo $pageNav->getListFooter(); ?>

                <input type="hidden" name="ca" value="<?php echo $option;?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="hidemainmenu" value="0" />
                </form>
                <?php
        }

        function edit( &$row, &$images, &$lists, &$params, $option, &$menus ) {
		global $database, $reg;
		
		$create_date = null;
		$mod_date 		= null;
		$nullDate 		= $database->getNullDate();
		
		if ( $row->created != $nullDate ) {
			$create_date 	= mosFormatDate( $row->created, '%A, %d %B %Y %H:%M', '0' );
		}
		if ( $row->modified != $nullDate ) {
			$mod_date 		= mosFormatDate( $row->modified, '%A, %d %B %Y %H:%M', '0' );
		}
		
                $tabs = new iTabs( 1 );
				
                $component_foto = new component_foto ( 0 );
                $component_foto->init($reg['ca']);
                $component_foto->parent = $row->id;

                $component_file = new component_file ( 0 );
                $component_file->init( $reg['ca'] );
                $component_file->parent = $row->id;


                /*mosCommonHTML::loadOverlib();*/
                mosCommonHTML::loadCalendar();
                ?>
                <script language="javascript" type="text/javascript">
                var folderimages = new Array;
                <?php
                $i = 0;
                foreach ($images as $k=>$items) {
                        foreach ($items as $v) {
				echo "folderimages[".$i++."] = new Array( '$k','".addslashes( $v->value )."','".addslashes( $v->text )."' );\t";
                        }
                }
                ?>
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
                                } else if ( form.link_name.value == "" ) {
                                        alert( "Пожалуйста, введите имя для этого пункта меню" );
                                        return;
                                }
                        }

                        //form.images.value = form.imagelist.value;

                        try {
                                document.adminForm.onsubmit();
                        }
                        catch(e){}
                        if (trim(form.title.value) == ""){
                                alert( "Объект содержимого должен иметь заголовок" );
                        } else if (trim(form.name.value) == ""){
                                alert( "Объект содержимого должен иметь название" );
                        } else {
                                <?php getEditorContents( 'editor1', 'introtext' ) ; ?>
                                submitform( pressbutton );
                        }
                }
                </script>

                <table class="adminheading">
                <tr>
                        <td class="edit"><?
			$iway[0]->name="Статичное содержимое";
			$iway[0]->url="";
			$iway[1]->name = $row->id ? 'Изменение' : 'Новое';
			$iway[1]->url="";

			i24pwprint_admin ($iway, 0);
			?></td>
                </tr>
                </table>
                <form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> action="index2.php" method="post" name="adminForm"  enctype="multipart/form-data">

                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                        <td width="75%" valign="top">
                                <table class="adminform">
									<tr><th colspan="2">Информация о содержимом</th></tr>
									<tr>
											<td align="left">Заголовок:</td>
											<td width="100%"><input class="inputbox" type="text" name="title" size="100" maxlength="100" value="<?php echo $row->title; ?>" /></td>
									</tr>
									<tr>
											<td align="left"><span style="float:left;">Адрес&nbsp;страницы:&nbsp;</span><span style="float:right;"><?=site_url ?>/</span></td>
											<td><input class="inputbox" type="text" name="sefname" size="100" maxlength="100" value="<?php echo $row->sefname; ?>" /></td>
									</tr>
	
									<tr>
											<td valign="top" align="left" colspan="2">Текст: (обязательно)<br /><?php
											// parameters : areaname, content, hidden field, width, height, rows, cols
											editorArea( 'editor1',  $row->introtext, 'introtext', '100%;', '500', '75', '50' ); 
											?></td>
									</tr>
                                </table>
                        </td>
                        <td width="25%" valign="top">
                                <?php
                                $tabs->startPane("content-pane");
                                $tabs->startTab("Публикация","publish-page");
                                ?>
                                <table class="adminform" width="100%">
                                <tr>
                                    <th colspan="2">Информация о публикации</th>
								</tr>
                                <tr>
									<td valign="top" align="right" width="120">Состояние:</td>
                                    <td><?php echo $row->state > 0 ? 'Опубликовано' : 'Черновик - Не опубликовано'; ?></td>
                                </tr>
                                <tr>
									<td valign="top" align="right">Опубликовано (на сайте):</td>
									<td><input type="checkbox" name="published" value="1" <?php echo $row->state ? 'checked="checked"' : ''; ?> /></td>
                                </tr>
                                <tr>
									<td valign="top" align="right">Уровень доступа:</td>
									<td><?php echo $lists['access']; ?></td>
                                </tr>
                                <tr>
									<td valign="top" align="right">Псевдоним автора:</td>
									<td><input type="text" name="created_by_alias" size="30" maxlength="100" value="<?php echo $row->created_by_alias; ?>" class="inputbox" /></td>
                                </tr>
                                <tr>
									<td valign="top" align="right">Изменить автора:</td>
									<td><?php echo $lists['created_by']; ?></td>
                                </tr>
                                <tr>
									<td valign="top" align="right">Перезаписать дату создания</td>
									<td><input class="inputbox" type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $row->created; ?>" />
									<input name="reset" type="reset" class="button" onClick="return showCalendar('created', 'y-mm-dd');" value="..."></td>
                                </tr>
                                <tr>
									<td align="right">Начало публикации:</td>
									<td><input class="inputbox" type="text" name="publish_up" id="publish_up" size="25" maxlength="19" value="<?php echo $row->publish_up; ?>" />
                                        <input type="reset" class="button" value="..." onclick="return showCalendar('publish_up', 'y-mm-dd');"></td>
                                </tr>
                                <tr>
									<td align="right">Окончание публикации:</td>
									<td>
                                        <input class="inputbox" type="text" name="publish_down" id="publish_down" size="25" maxlength="19" value="<?php echo $row->publish_down; ?>" />
                                        <input type="reset" class="button" value="..." onclick="return showCalendar('publish_down', 'y-mm-dd');">
                                    </td>
                                </tr>
                                </table>
                                <br />
                                <table class="adminform" width="100%">
                                <?php
                                if ( $row->id ) {
                                        ?>
                                        <tr>
											<td><strong>ID содержимого:</strong></td>
											<td><?php echo $row->id; ?></td>
                                        </tr>
                                        <?php
                                }
                                ?><tr>
									<td width="120" valign="top" align="right"><strong>Состояние</strong></td>
									<td><?php echo $row->state > 0 ? 'Опубликовано' : ($row->state < 0 ? 'В архиве' : 'Черновик - Не опубликовано');?></td>
                                </tr>
                                <tr>
									<td valign="top" align="right"><strong>Версия</strong></td>
									<td><?php echo $row->version;?></td>
                                </tr>
                                <tr>
									<td valign="top" align="right"><strong>Создано</strong></td>
									<td><?php if ( !$create_date ) {	?>Новый документ<?php
										} else {
											echo $create_date;
										} ?></td>
                                </tr>
                                <tr>
									<td valign="top" align="right"><strong>Последнее изменение</strong></td>
                                    <td><?php
										if ( !$mod_date ) { ?>Не изменялось<?php
										} else { echo $mod_date; ?><br /><?php echo $row->modifier; } ?>
                                    </td>
                                </tr>
                                <tr>
                                        <td valign="top" align="right"><strong>Истек срок публикации</strong></td>
                                        <td><?php echo "$row->publish_down";?></td>
                                </tr>
                                </table>
                                <?php
                                $tabs->endTab();
								$tabs->startTab("Изображения","images-page");
                                ?>
                                <table class="adminform">
                                <tr>
                                        <th colspan="2">Основное изображение</th>
                                </tr>
                                <tr>
                                        <td colspan="2">
                                                <table width="100%">
                                                <tr>
							
                                                        <td width="30%" >Изображения содержимого:</td>
                                                        <td width="70%" ><input type="file" class="inputbox" style="width:100%" name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /></td>

                                                </tr><?
												?><tr><?
														?><td></td><?
														?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr>
																			<tr><? component_foto::delmainfoto_checkbox(); ?></tr>
																		  </table></td><?
												?></tr><?
                                                ?><tr>
                                                        <td>Текущее<br />основное<br />изображение:</td>
                                                        <td><? $component_foto->parent_obj=&$row; $component_foto->previewMainFoto($reg['content_main_small_noimage']); ?></td>
                                                </tr>
                                                <tr>
                                                        <td colspan="3"><input type="hidden" name= "_source" value="" /><?
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
														?></td>
                                                </tr>
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
                                                        <table class="adminform">
                                                        <tr>
                                                                <th colspan="2">
                                                                Управление параметрами
                                                                </th>
                                                        </tr>
                                                        <tr>
                                                                <td>
                                                                <?php echo $params->render();?>
                                                                </td>
                                                        </tr>
                                                        </table>
                                                        <?php
                                                        $tabs->endTab();
                                                        $tabs->startTab("Метаданные","metadata-page");
                                                        ?>
                                                        <table class="adminform">
                                                            <tr><th colspan="2">Данные мета-тегов</th></tr>
                                                            <tr>
                                                                <td align="left">Псевдоним заголовка:<br />
                                                                <input class="inputbox" type="text" name="title_alias" size="30" maxlength="100" value="<?php echo $row->title_alias; ?>" /></td>
                                                            </tr>
                                                            <tr><?
                                                                            ?><td>Содержимое &lt;title&gt;:<br /><?
                                                                            ?><input name="seo_title" type="text" class="text_area" id="seo_title" style="width:300px; " value="<?php echo $row->seo_title; ?>" size="30" maxlength="100" /></td><?
                                                            ?></tr>
                                                            <tr>
                                                                <td align="left">Описание (Description):<br />
                                                                <input type="text" class="inputbox" name="metadesc" style="width:280px" value="<?php echo str_replace('&','&amp;',$row->metadesc); ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left">Ключевые слова (Keywords):<br />
                                                                <input type="text" class="inputbox" name="metakey" style="width:280px" value="<?php echo str_replace('&','&amp;',$row->metakey); ?>"></td>
                                                            </tr>
                                                        </table>
                                                         <table class="adminform">
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
                                        $conf->typedcontent = 1;
					$conf->returnme('index2.php?ca='.$reg['ca'].'&task=edit&hidemainmenu=1&id='.$row->id );
					$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
				?>

                <input type="hidden" name="images" value="" />
                <input type="hidden" name="ca" value="<?php echo $option; ?>" />
				<? if (  $row->id  ) { ?>
	                <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<? } ?>
                <input type="hidden" name="task" value="" />
                </form>
                <?php
        }
}
?>