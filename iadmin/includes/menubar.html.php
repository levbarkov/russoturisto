<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class mosMenuBar {

	function startTable() {
                global $reg; 
                if (  $reg['toolbar_footer']==1  ) $fooretclass = ' class="toolbar_footer" '; else $fooretclass="";
		?><table cellpadding="0" cellspacing="0" border="0" id="toolbar" <?=$fooretclass ?>><?
		?><tr valign="middle" align="center"><?php
	}

	function custom( $task='', $icon='', $iconOver='', $alt='', $listSelect=true ) {
		global $reg;
		if (  $task=='showme'  ){
		if (  ggri('id')==0  ) return; ?>
			<td>
				<a target="_blank" class="toolbar" href="<? include  site_path.'/iadmin/component/'.$reg['ca'].'/showme.php'; ?>"><?php echo $alt; ?></a>
			</td>
		<? return;
		}
		if ($listSelect) {
			$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('Пожалуйста, сделайте выбор из списка для $alt');}else{submitbutton('$task')}";
		} else {
			$href = "javascript:submitbutton('$task')";
		}

		?>
		<td>
			<a class="toolbar" href="<?php echo $href;?>"><?php echo $alt; ?></a>
		</td>
		<?php
	}
	function customG( $alt='', $href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Пожалуйста, сделайте выбор из списка категорий');}else{hideMainMenu();submitbutton('movesave')}" ) {
		?><td>
			<a class="toolbar" href="<?php echo $href;?>"><?php echo $alt; ?></a>
		</td><?php
	}

	function customX( $task='', $icon='', $iconOver='', $alt='', $listSelect=true ) {
		if ($listSelect) {
			$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('Пожалуйста, сделайте выбор из списка $alt');}else{hideMainMenu();submitbutton('$task')}";
		} else {
			$href = "javascript:hideMainMenu();submitbutton('$task')";
		}

		?><td><a class="toolbar" href="<?php echo $href;?>"><?php echo $alt; ?></a></td><?php
	}

	function addNew( $task='new', $alt='Новый' ) {
		?><td><a class="toolbar" href="javascript:submitbutton('<?php echo $task;?>');"> <?php echo $alt; ?></a></td><?php
	}

	function addNewX( $task='new', $alt='Новый' ) {
		?><td><a class="toolbar" href="javascript:hideMainMenu();submitbutton('<?php echo $task;?>');"><?php echo $alt; ?></a></td><?php
	}

	function publish( $task='publish', $alt='Допустить' ) {
		?><td><a class="toolbar" href="javascript:submitbutton('<?php echo $task;?>');"><?php echo $alt; ?></a></td><?php
	}

	function publishList( $task='publish', $alt='Допустить' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Пожалуйста, выберите из списка объекты для их публикации на сайте'); } else {submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function makeDefault( $task='default', $alt='По умолчанию' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Пожалуйста, выберите объект, чтобы назначить его по умолчанию'); } else {submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function assign( $task='assign', $alt='Назначить' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Пожалуйста, для назначения объекта выберите его'); } else {submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function unpublish( $task='unpublish', $alt='Скрыть' ) {
		?><td><a class="toolbar" href="javascript:submitbutton('<?php echo $task;?>');"><?php echo $alt; ?></a></td><?php
	}

	function unpublishList( $task='unpublish', $alt='Скрыть' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Для отмены публикации объекта, сначала выберите его'); } else {submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function archiveList( $task='archive', $alt='В&nbsp;архив' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Пожалуйста, выберите из списка объекты для их архивации'); } else {submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

    function unarchiveList( $task='unarchive', $alt='Из&nbsp;архива' ) {
		?>
		<td>
			<a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект для восстановления его из архива'); } else {submitbutton('<?php echo $task;?>', '');}">
				<?php echo $alt; ?></a>
		</td>
		<?php
	}

	function editList( $task='edit', $alt='Изменить' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function editListX( $task='edit', $alt='Изменить' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {hideMainMenu();submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function editHtml( $task='edit_source', $alt='Ред.&nbsp;HTML' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function editHtmlX( $task='edit_source', $alt='Ред.&nbsp;HTML' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {hideMainMenu();submitbutton('<?php echo $task;?>', '');}""><?php echo $alt; ?></a></td><?php
	}

	function editCss( $task='edit_css', $alt='Ред.&nbsp;CSS' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {submitbutton('<?php echo $task;?>', '');}""><?php echo $alt; ?></a></td><?php
	}

	function editCssX( $task='edit_css', $alt='Ред.&nbsp;CSS' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его редактирования'); } else {hideMainMenu();submitbutton('<?php echo $task;?>', '');}"><?php echo $alt; ?></a></td><?php
	}

	function deleteList( $msg='', $task='remove', $alt='Удалить' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его удаления'); } else if (confirm('Вы действительно хотите удалить выбранные объекты? <?php echo $msg;?>')){ submitbutton('<?php echo $task;?>');}"><?php echo $alt; ?></a></td><?php
	}

	function deleteListX( $msg='', $task='remove', $alt='Удалить' ) {
		?><td><a class="toolbar" href="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для его удаления'); } else if (confirm('Вы действительно хотите удалить выбранные объекты? <?php echo $msg;?>')){ hideMainMenu();submitbutton('<?php echo $task;?>');}"><?php echo $alt; ?></a></td><?php
	}

    function trash( $task='remove', $alt='В&nbsp;корзину', $check=true ) {
		if ( $check ) {
			$js = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('Выберите объект из списка для перемещения его в корзину'); } else { submitbutton('$task');}";
		} else {
			$js = "javascript:submitbutton('$task');";
		}

		?><td><a class="toolbar" href="<?php echo $js; ?>"><?php echo $alt; ?></a></td><?php
	}

	function preview( $popup='', $updateEditors=false ) {
		return;
	}

	function help( $ref, $com=false ) {
		$helpUrl = mosGetParam( $GLOBALS, 'mosConfig_helpurl', '' );

		if ( $helpUrl == 'http://help.mamboserver.com' ) {
			$helpUrl = 'http://help.joomla.org';
		}

		if ($com) {
	   // help file for 3PD Components
			$url = site_url . '/iadmin/component/' . $GLOBALS['option'] . '/help/';
			if (!eregi( '\.html$', $ref ) && !eregi( '\.xml$', $ref )) {
				$ref = $ref . '.html';
			}
			$url .= $ref;
		} else if ( $helpUrl ) {
	   // Online help site as defined in GC
			$ref .= $GLOBALS['_VERSION']->getHelpVersion();
			$url = $helpUrl . '/index2.php?option=com_content&amp;task=findkey&amp;pop=1&amp;keyref=' . urlencode( $ref );
		} else {
	   // Included html help files
			$url = site_url . '/help/';
			if (!eregi( '\.html$', $ref ) && !eregi( '\.xml$', $ref )) {
			$ref = $ref . '.html';
			}			
			$url .= $ref;
		}
		?>
		<td>
			<a class="toolbar" href="#" onclick="window.open('<?php echo $url;?>', 'mambo_help_win', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');">
				Помощь</a>
		</td>
		<?php
	}

	function apply( $task='apply', $alt='Применить' ) {
		?><td><a class="toolbar" href="javascript:submitbutton('<?php echo $task;?>');"><?php echo $alt;?></a></td><?php
	}

	function save( $task='save', $alt='Сохранить' ) {
		?><td><a class="toolbar" href="javascript:submitbutton('<?php echo $task;?>');"><?php echo $alt;?></a></td><?php
	}

	function savenew() {
		?><td><a class="toolbar" href="javascript:submitbutton('savenew');">Сохранить</a></td><?php
	}

	function saveedit() {
		?><td><a class="toolbar" href="javascript:submitbutton('saveedit');">Сохранить</a></td><?php
	}

	function cancel( $task='cancel', $alt='Отмена' ) {
		?><td><a class="toolbar" href="javascript:submitbutton('<?php echo $task;?>');"><?php echo $alt;?></a></td><?php
	}

	function back( $alt='Назад', $href='' ) {
		if ( $href ) {
			$link = $href;
		} else {
			$link = 'javascript:window.history.back();';
		}
		?><td><a class="toolbar" href="<?php echo $link; ?>"><?php echo $alt;?></a></td><?php
	}

	function divider() {
		?><td>&nbsp;&nbsp;</td><?php
	}

	function media_manager( $directory='', $alt='Загрузить' ) {
		global $database;	
		?><td><a class="toolbar" href="#" onclick="popupWindow('popups/uploadimage.php?directory=<?php echo $directory; ?>&amp;','win1',250,100,'no');"><?php echo $alt;?></a></td><?php
	}

	function spacer( $width='' ) {
		if ($width != '') {
			?><td width="<?php echo $width;?>">&nbsp;</td><?php
		} else {
			?><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><?php
		}
	}

	function endTable() {
		?></tr></table><?php
	}
}




















class mosAdminMenus {
        /**
        * build the select list for Menu Ordering
        */
        function Ordering( &$row, $id ) {
                global $database;

                if ( $id ) {
                        $query = "SELECT ordering AS value, name AS text"
                        . "\n FROM #__menu"
			. "\n WHERE menutype = " . $database->Quote ( $row->menutype )
			. "\n AND parent = " . (int) $row->parent
                        . "\n AND published != -2"
                        . "\n ORDER BY ordering"
                        ;
                        $order = mosGetOrderingList( $query );
                        $ordering = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
                } else {
                        $ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. _CMN_NEW_ITEM_LAST;
                }
                return $ordering;
        }

        function sitemenus( &$row, $id ) {
                global $database;

                $menuTypes = menutypes();
                
                $sitemenus = array();
                foreach ($menuTypes as $menuType){
                   $sitemenus[] = mosHTML::makeOption( $menuType, $menuType );
                }
                return mosHTML::selectList( $sitemenus, 'menutype', 'class="inputbox" id="menutype" onchange="javascript: $(\'#parent_s_menu\').html(\'<img src=/includes/images/loading16.gif > загрузка ...\'); ins_ajax_load_target(\'ca=system_ajax&4ajax=1&task=change_parent_menu&menutype=\'+$(\'#menutype option:selected\').val(), \'#parent_s_menu\'); void(0);"  style=" width:250px;"  ', 'value', 'text', ggrr('menutype') );
        }

        /**
        * build the select list for access level
        */
        function Access( &$row ) {
                global $database;

                $query = "SELECT id AS value, help AS text"
                . "\n FROM #__usertypes"
                . "\n ORDER BY id DESC"
                ;
                $database->setQuery( $query );
                $groups2 = $database->loadObjectList();
				$groups[] = mosHTML::makeOption( '0', '- Все -' );
				foreach ($groups2 as $group) $groups[] = mosHTML::makeOption( $group->value, $group->text );
                $access = mosHTML::selectList( $groups, 'access', 'class="inputbox" size="8"', 'value', 'text', intval( $row->access ) );

                return $access;
        }

        /**
        * build the select list for parent item
        */
        function Parent( &$row ) {
                global $database;

		$id = '';
		if ( $row->id ) {
			$id = "\n AND id != " . (int) $row->id;
		}

                // get a list of the menu items
		// excluding the current menu item and its child elements
                $query = "SELECT m.*"
                . "\n FROM #__menu m"
		. "\n WHERE menutype = " . $database->Quote( $row->menutype )
                . "\n AND published != -2"
		. $id
		. "\n ORDER BY parent, ordering"
                ;
                $database->setQuery( $query );
                $mitems = $database->loadObjectList();

                // establish the hierarchy of the menu
                $children = array();

		if ( $mitems ) {
                // first pass - collect children
                foreach ( $mitems as $v ) {
                        $pt = $v->parent;
                        $list = @$children[$pt] ? $children[$pt] : array();
                        array_push( $list, $v );
                        $children[$pt] = $list;
                }
		}

                // second pass - get an indent list of the items
		$list = mosTreeRecurse( 0, '', array(), $children, 20, 0, 0 );

                // assemble menu items to the array
                $mitems = array();
                $mitems[] = mosHTML::makeOption( '0', 'Top' );
		
                foreach ( $list as $item ) {
			$mitems[] = mosHTML::makeOption( $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename );
		}

		$output = mosHTML::selectList( $mitems, 'parent', 'class="inputbox" size="10"', 'value', 'text', $row->parent );
		
		return $output;
        }

        /**
        * build a radio button option for published state
        */
        function Published( &$row ) {
                $published = mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published );
                return $published;
        }

        /**
        * build the link/url of a menu item
        */
        function Link( &$row, $id, $link=NULL ) {
		global $mainframe;
        if ( $id ) {
			switch ($row->type) {
				case 'content_item_link':
				case 'content_typed':
					// load menu params
					$params = new mosParameters( $row->params, $mainframe->getPath( 'menu_xml', $row->type ), 'menu' );

					if ( $params->get( 'unique_itemid' ) ) {
						$row->link .= '&pi='. $row->id;
					} else {
						$temp = split( '&task=view&id=', $row->link);
						$row->link .= '&pi='. $row->id;
					}
					
					$link = $row->link;
					break;
				
				default:
                        if ( $link ) {
                                $link = $row->link;
                        } else {
                                $link = $row->link .'&amp;Itemid='. $row->id;
                        }
					break;
			}			
                } else {
                        $link = NULL;
                }
		
                return $link;
        }

        /**
        * build the select list for target window
        */
        function Target( &$row ) {
                $click[] = mosHTML::makeOption( '0', 'текущем окне с панелью навигации' );
                $click[] = mosHTML::makeOption( '1', 'новом окне с панелью навигации' );
                $click[] = mosHTML::makeOption( '2', 'новом окне без панели навигации' );
                $target = mosHTML::selectList( $click, 'browserNav', 'class="inputbox" size="4"', 'value', 'text', intval( $row->browserNav ) );
                return $target;
        }

        /**
        * build the multiple select list for Menu Links/Pages
        */
        function MenuLinks( &$lookup, $all=NULL, $none=NULL, $unassigned=1 ) {
                global $database;

                // get a list of the menu items
                $query = "SELECT m.*"
                . "\n FROM #__menu AS m"
		. "\n WHERE m.published = 1"
		//. "\n AND m.type != 'separator'"
		//. "\n AND NOT ("
		//	. "\n ( m.type = 'url' )"
		//	. "\n AND ( m.link LIKE '%index.php%' )"
		//	. "\n AND ( m.link LIKE '%Itemid=%' )"
		//. "\n )"
                . "\n ORDER BY m.menutype, m.parent, m.ordering"
                ;
                $database->setQuery( $query );
                $mitems = $database->loadObjectList();
                $mitems_temp = $mitems;

                // establish the hierarchy of the menu
                $children = array();
                // first pass - collect children
                foreach ( $mitems as $v ) {
                        $id = $v->id;
                        $pt = $v->parent;
                        $list = @$children[$pt] ? $children[$pt] : array();
                        array_push( $list, $v );
                        $children[$pt] = $list;
                }
                // second pass - get an indent list of the items
		$list = mosTreeRecurse( intval( $mitems[0]->parent ), '', array(), $children, 20, 0, 0 );

                // Code that adds menu name to Display of Page(s)
		$text_count 	= 0;
                $mitems_spacer = $mitems_temp[0]->menutype;
                foreach ($list as $list_a) {
                        foreach ($mitems_temp as $mitems_a) {
                                if ($mitems_a->id == $list_a->id) {
                                        // Code that inserts the blank line that seperates different menus
                                        if ($mitems_a->menutype != $mitems_spacer) {
                                                $list_temp[] = mosHTML::makeOption( -999, '----' );
                                                $mitems_spacer = $mitems_a->menutype;
                                        }
					
					// do not display `url` menu item types that contain `index.php` and `Itemid`
					if (!($mitems_a->type == 'url' && strpos($mitems_a->link, 'index.php') !== false && strpos($mitems_a->link, 'Itemid=') !== false)) {
						$text 			= $mitems_a->menutype .' | '. $list_a->treename;
                                        $list_temp[] = mosHTML::makeOption( $list_a->id, $text );
						
                                        if ( strlen($text) > $text_count) {
                                                $text_count = strlen($text);
                                        }
                                }
                        }
                }
		}
                $list = $list_temp;

                $mitems = array();
                if ( $all ) {
                        // prepare an array with 'all' as the first item
                        $mitems[] = mosHTML::makeOption( 0, 'Все' );
                        // adds space, in select box which is not saved
                        $mitems[] = mosHTML::makeOption( -999, '----' );
                }
                if ( $none ) {
                        // prepare an array with 'all' as the first item
                        $mitems[] = mosHTML::makeOption( -999, 'Отсутствует' );
                        // adds space, in select box which is not saved
                        $mitems[] = mosHTML::makeOption( -999, '----' );
                }
		if ( $unassigned ) {
                        // prepare an array with 'all' as the first item
                        $mitems[] = mosHTML::makeOption( 99999999, 'Со свободными' );
                        // adds space, in select box which is not saved
                        $mitems[] = mosHTML::makeOption( -999, '----' );
                }
														
                // append the rest of the menu items to the array
                foreach ($list as $item) {
                        $mitems[] = mosHTML::makeOption( $item->value, $item->text );
                }
                $pages = mosHTML::selectList( $mitems, 'selections[]', 'class="inputbox" size="26" multiple="multiple"', 'value', 'text', $lookup );
                return $pages;
        }


        /**
        * build the select list to choose a category
        */
        function Category( &$menu, $id, $javascript='' ) {
                global $database;

                $query = "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text`"
                . "\n FROM #__sections AS s"
                . "\n INNER JOIN #__categories AS c ON c.section = s.id"
                . "\n WHERE s.scope = 'content'"
                . "\n ORDER BY s.name, c.name"
                ;
                $database->setQuery( $query );
                $rows = $database->loadObjectList();
                $category = '';
                if ( $id ) {
                        foreach ( $rows as $row ) {
                                if ( $row->value == $menu->componentid ) {
                                        $category = $row->text;
                                }
                        }
                        $category .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
                        $category .= '<input type="hidden" name="link" value="'. $menu->link .'" />';
                } else {
                        $category = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"'. $javascript, 'value', 'text' );
                        $category .= '<input type="hidden" name="link" value="" />';
                }
                return $category;
        }

        /**
        * build the select list to choose a section
        */
        function Section( &$menu, $id, $all=0 ) {
                global $database;

                $query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text`"
                . "\n FROM #__sections AS s"
                . "\n WHERE s.scope = 'content'"
                . "\n ORDER BY s.name"
                ;
                $database->setQuery( $query );
                if ( $all ) {
                        $rows[] = mosHTML::makeOption( 0, '- Все разделы -' );
                        $rows = array_merge( $rows, $database->loadObjectList() );
                } else {
                        $rows = $database->loadObjectList();
                }

                if ( $id ) {
                        foreach ( $rows as $row ) {
                                if ( $row->value == $menu->componentid ) {
                                        $section = $row->text;
                                }
                        }
                        $section .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
                        $section .= '<input type="hidden" name="link" value="'. $menu->link .'" />';
                } else {
                        $section = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text' );
                        $section .= '<input type="hidden" name="link" value="" />';
                }
                return $section;
        }

        /**
        * build the select list to choose a component
        */
        function Component( &$menu, $id ) {
                global $database;

                $query = "SELECT c.id AS value, c.name AS text, c.link"
                . "\n FROM #__components AS c"
                . "\n WHERE c.link != ''"
                . "\n ORDER BY c.name"
                ;
                $database->setQuery( $query );
                $rows = $database->loadObjectList( );

                if ( $id ) {
                        // existing component, just show name
                        foreach ( $rows as $row ) {
                                if ( $row->value == $menu->componentid ) {
                                        $component = $row->text;
                                }
                        }
                        $component .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
                } else {
                        $component = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text' );
                }
                return $component;
        }

        /**
        * build the select list to choose a component
        */
        function ComponentName( &$menu, $id ) {
                global $database;

                $query = "SELECT c.id AS value, c.name AS text, c.link"
                . "\n FROM #__components AS c"
                . "\n WHERE c.link != ''"
                . "\n ORDER BY c.name"
                ;
                $database->setQuery( $query );
                $rows = $database->loadObjectList( );

                $component = 'Component';
                foreach ( $rows as $row ) {
                        if ( $row->value == $menu->componentid ) {
                                $component = $row->text;
                        }
                }

                return $component;
        }

        /**
        * build the select list to choose an image
        */
        function Images( $name, &$active, $javascript=NULL, $directory=NULL ) {
                

                if ( !$directory ) {
                        $directory = '/images/stories';
                }

		if ( !$javascript ) {
			$javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='..$directory/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		}
		
                $imageFiles = mosReadDirectory( site_path . $directory );
                $images = array(  mosHTML::makeOption( '', '- Выберите изображение -' ) );
                foreach ( $imageFiles as $file ) {
                        if ( eregi( "bmp|gif|jpg|png", $file ) ) {
                                $images[] = mosHTML::makeOption( $file );
                        }
                }
                $images = mosHTML::selectList( $images, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

                return $images;
        }

        /**
        * build the select list for Ordering of a specified Table
        */
        function SpecificOrdering( &$row, $id, $query, $neworder=0 ) {
                global $database;

                if ( $neworder ) {
                        $text = _CMN_NEW_ITEM_FIRST;
                } else {
                        $text = _CMN_NEW_ITEM_LAST;
                }

                if ( $id ) {
                        $order = mosGetOrderingList( $query );
                        $ordering = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
                } else {
                        $ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. $text;
                }
                return $ordering;
        }

        /**
        * Select list of active users
        */
        function UserSelect( $name, $active, $nouser=0, $javascript=NULL, $order='name', $reg=1 ) {
                global $database, $my;

                $and = '';
                if ( $reg ) {
                // does not include registered users in the list
                        $and = "\n AND gid > 18";
                }

                $query = "SELECT id AS value, name AS text"
                . "\n FROM #__users"
                . "\n WHERE block = 0"
                . $and
                . "\n ORDER BY $order"
                ;
                $database->setQuery( $query );
                if ( $nouser ) {
                        $users[] = mosHTML::makeOption( '0', '- Нет пользователя -' );
                        $users = array_merge( $users, $database->loadObjectList() );
                } else {
                        $users = $database->loadObjectList();
                }

                $users = mosHTML::selectList( $users, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

                return $users;
        }

        /**
        * Select list of positions - generally used for location of images
        */
        function Positions( $name, $active=NULL, $javascript=NULL, $none=1, $center=1, $left=1, $right=1 ) {
                if ( $none ) {
                        $pos[] = mosHTML::makeOption( '', _CMN_NONE );
                }
                if ( $center ) {
                        $pos[] = mosHTML::makeOption( 'center', _CMN_CENTER );
                }
                if ( $left ) {
                        $pos[] = mosHTML::makeOption( 'left', _CMN_LEFT );
                }
                if ( $right ) {
                        $pos[] = mosHTML::makeOption( 'right', _CMN_RIGHT );
                }

                $positions = mosHTML::selectList( $pos, $name, 'class="inputbox" size="1"'. $javascript, 'value', 'text', $active );

                return $positions;
        }

        /**
        * Select list of active categories for components
        */
        function ComponentCategory( $name, $section, $active=NULL, $javascript=NULL, $order='ordering', $size=1, $sel_cat=1 ) {
                global $database;

                $query = "SELECT id AS value, name AS text"
                . "\n FROM #__categories"
		. "\n WHERE section = " . $database->Quote( $section )
                . "\n AND published = 1"
                . "\n ORDER BY $order"
                ;
                $database->setQuery( $query );
                if ( $sel_cat ) {
                        $categories[] = mosHTML::makeOption( '0', _SEL_CATEGORY );
                        $categories = array_merge( $categories, $database->loadObjectList() );
                } else {
                        $categories = $database->loadObjectList();
                }

                if ( count( $categories ) < 1 ) {
                        mosRedirect( 'index2.php?option=com_categories&section='. $section, 'You must create a category first.' );
                }

                $category = mosHTML::selectList( $categories, $name, 'class="inputbox" size="'. $size .'" '. $javascript, 'value', 'text', $active );

                return $category;
        }

        /**
        * Select list of active sections
        */
        function SelectSection( $name, $active=NULL, $javascript=NULL, $order='ordering' ) {
                global $database;

                $categories[] = mosHTML::makeOption( '0', _SEL_SECTION );
                $query = "SELECT id AS value, title AS text"
                . "\n FROM #__sections"
                . "\n WHERE published = 1"
                . "\n ORDER BY $order"
                ;
                $database->setQuery( $query );
                $sections = array_merge( $categories, $database->loadObjectList() );

                $category = mosHTML::selectList( $sections, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

                return $category;
        }

        /**
        * Select list of menu items for a specific menu
        */
        function Links2Menu( $type, $and ) {
                global $database;

                $query = "SELECT *"
                . "\n FROM #__menu"
		. "\n WHERE type = " . $database->Quote( $type )
                . "\n AND published = 1"
                . $and
                ;
                $database->setQuery( $query );
                $menus = $database->loadObjectList();

                return $menus;
        }

        /**
        * Select list of menus
	 * @param string The control name
	 * @param string Additional javascript
	 * @return string A select list
        */
        function MenuSelect( $name='menuselect', $javascript=NULL ) {
                global $database;

                $query = "SELECT params"
                . "\n FROM #__modules"
                . "\n WHERE module = 'mod_mainmenu'"
                ;
                $database->setQuery( $query );
                $menus = $database->loadObjectList();
                $total = count( $menus );
		$menuselect = array();
                for( $i = 0; $i < $total; $i++ ) {
                        $params = mosParseParams( $menus[$i]->params );
                        $menuselect[$i]->value         = $params->menutype;
                        $menuselect[$i]->text         = $params->menutype;
                }
                // sort array of objects
                SortArrayObjects( $menuselect, 'text', 1 );

                $menus = mosHTML::selectList( $menuselect, $name, 'class="inputbox" size="10" '. $javascript, 'value', 'text' );

                return $menus;
        }

        /**
        * Internal function to recursive scan the media manager directories
        * @param string Path to scan
        * @param string root path of this folder
        * @param array  Value array of all existing folders
        * @param array  Value array of all existing images
        */
        function ReadImages( $imagePath, $folderPath, &$folders, &$images ) {
                $imgFiles = mosReadDirectory( $imagePath );

                foreach ($imgFiles as $file) {
                        $ff_         = $folderPath . $file .'/';
                        $ff         = $folderPath . $file;
                        $i_f         = $imagePath .'/'. $file;

                        if ( is_dir( $i_f ) && $file != 'CVS' && $file != '.svn') {
                                $folders[] = mosHTML::makeOption( $ff_ );
                                mosAdminMenus::ReadImages( $i_f, $ff_, $folders, $images );
                        } else if ( eregi( "bmp|gif|jpg|png", $file ) && is_file( $i_f ) ) {
                                // leading / we don't need
                                $imageFile = substr( $ff, 1 );
                                $images[$folderPath][] = mosHTML::makeOption( $imageFile, $file );
                        }
                }
        }

	/**
	* Internal function to recursive scan the media manager directories
	* @param string Path to scan
	* @param string root path of this folder
	* @param array  Value array of all existing folders
	* @param array  Value array of all existing images
	*/
	function ReadImagesX( &$folders, &$images ) {
		
		
		if ( $folders[0]->value != '*0*' ) {	
			foreach ( $folders as $folder ) {
				$imagePath 	= site_path .'/images/stories' . $folder->value;
				$imgFiles 	= mosReadDirectory( $imagePath );
				$folderPath = $folder->value .'/';
				
				foreach ($imgFiles as $file) {
					$ff 	= $folderPath . $file;
					$i_f 	= $imagePath .'/'. $file;
					
					if ( eregi( "bmp|gif|jpg|png", $file ) && is_file( $i_f ) ) {
						// leading / we don't need
						$imageFile = substr( $ff, 1 );
						$images[$folderPath][] = mosHTML::makeOption( $imageFile, $file );
					}
				}
			}
		} else {
			$folders 	= array();
			$folders[] 	= mosHTML::makeOption( 'None' );
		}
	}

	function GetImageFolders( &$temps, $path ) {
		if ( $temps[0]->value != 'None' ) {
			foreach( $temps as $temp ) {
				if ( substr( $temp->value, -1, 1 ) != '/' ) { 
					$temp 		= $temp->value .'/';
					$folders[] 	= mosHTML::makeOption( $temp, $temp );
				} else {
					$temp 		= $temp->value;
					$temp 		= ampReplace( $temp );
					$folders[] 	= mosHTML::makeOption( $temp, $temp );
				}
			}
		} else {
			$folders[] 	= mosHTML::makeOption( 'None Selected' );
		}
		
		$javascript 	= "onchange=\"changeDynaList( 'imagefiles', folderimages, document.adminForm.folders.options[document.adminForm.folders.selectedIndex].value, 0, 0);\"";
                $getfolders         = mosHTML::selectList( $folders, 'folders', 'class="inputbox" size="1" '. $javascript, 'value', 'text', '/' );
		
                return $getfolders;
        }

	function GetImages( &$images, $path, $base='/' ) {
		if ( is_array($base) && count($base) > 0 ) {
			if ( $base[0]->value != '/' ) {
				$base = $base[0]->value .'/';
			} else {
				$base = $base[0]->value;
                }
		} else {
			$base = '/';
		}
		
		if ( !isset($images[$base] ) ) {
			$images[$base][] = mosHTML::makeOption( '' );
		}

		$javascript	= "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\" onfocus=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
		$getimages	= mosHTML::selectList( $images[$base], 'imagefiles', 'class="inputbox" size="10" multiple="multiple" '. $javascript , 'value', 'text', null );

                return $getimages;
        }

        function GetSavedImages( &$row, $path ) {
                $images2 = array();
		
                foreach( $row->images as $file ) {
                        $temp = explode( '|', $file );
                        if( strrchr($temp[0], '/') ) {
                                $filename = substr( strrchr($temp[0], '/' ), 1 );
                        } else {
                                $filename = $temp[0];
                        }
                        $images2[] = mosHTML::makeOption( $file, $filename );
                }
		
                $javascript        = "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \"";
                $imagelist         = mosHTML::selectList( $images2, 'imagelist', 'class="inputbox" size="10" '. $javascript, 'value', 'text' );

                return $imagelist;
        }

        /**
        * Checks to see if an image exists in the current templates image directory
         * if it does it loads this image.  Otherwise the default image is loaded.
        * Also can be used in conjunction with the menulist param to create the chosen image
        * load the default or use no image
        */
	function ImageCheck( $file, $directory='/images/M_images/', $param=NULL, $param_directory='/images/M_images/', $alt=NULL, $name=NULL, $type=1, $align='middle', $title=NULL, $admin=NULL ) {
                global  $mainframe;

                $cur_template = $mainframe->getTemplate();

		$name = ( $name ? 'name="'. $name .'"' : '' );
		$title 	= ( $title 	? 'title="'. $title .'"' 	: '' );		
		$alt 	= ( $alt 	? ' alt="'. $alt .'"' 		: ' alt=""' );
		$align 	= ( $align 	? ' align="'. $align .'"' 	: '' );

		// change directory path from frontend or backend
		if ($admin) {
			$path 	= '/administrator/templates/'. $cur_template .'/images/';
		} else {
			$path 	= '/templates/'. $cur_template .'/images/';
		}


                if ( $param ) {
                        $image = site_url. $param_directory . $param;
                        if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $align .' border="0" />';
                        }
                } else if ( $param == -1 ) {
                        $image = '';
                } else {
			if ( file_exists( site_path . $path . $file ) ) {
				$image = site_url . $path . $file;
                        } else {
                                // outputs only path to image
                                $image = site_url. $directory . $file;
                        }

                        // outputs actual html <img> tag
                        if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $title . $align .' border="0" />';
                        }
                }

                return $image;
        }

        /**
        * Checks to see if an image exists in the current templates image directory
         * if it does it loads this image.  Otherwise the default image is loaded.
        * Also can be used in conjunction with the menulist param to create the chosen image
        * load the default or use no image
        */
	function ImageCheckAdmin( $file, $directory='/administrator/images/', $param=NULL, $param_directory='/administrator/images/', $alt=NULL, $name=NULL, $type=1, $align='middle', $title=NULL ) {
/*		
                global  site_url, $mainframe;

                $cur_template = $mainframe->getTemplate();

		$name = ( $name ? 'name="'. $name .'"' : '' );
		$title 	= ( $title 	? 'title="'. $title .'"' 	: '' );		
		$alt 	= ( $alt 	? ' alt="'. $alt .'"' 		: ' alt=""' );
		$align 	= ( $align 	? ' align="'. $align .'"' 	: '' );

		$path 	= '/administrator/templates/'. $cur_template .'/images/';

                if ( $param ) {
                        $image = site_url. $param_directory . $param;
                        if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $align .' border="0" />';
                        }
                } else if ( $param == -1 ) {
                        $image = '';
                } else {
			if ( file_exists( site_path . $path . $file ) ) {
				$image = site_url . $path . $file;
                        } else {
				// outputs only path to image
                                $image = site_url. $directory . $file;
                        }

                        // outputs actual html <img> tag
                        if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $title . $align .' border="0" />';
			}
		}
*/
		// functionality consolidated into ImageCheck 
		$image = mosAdminMenus::ImageCheck( $file, $directory, $param, $param_directory, $alt, $name, $type, $align, $title, $admin=1 );

                return $image;
        }

        function menutypes() {
                global $database;

                $query = "SELECT params"
                . "\n FROM #__modules"
                . "\n WHERE module = 'mod_mainmenu'"
                . "\n ORDER BY title"
                ;
                $database->setQuery( $query        );
                $modMenus = $database->loadObjectList();
                $query = "SELECT menutype"
                . "\n FROM #__menu"
                . "\n GROUP BY menutype"
                . "\n ORDER BY menutype"
                ;
                $database->setQuery( $query        );
                $menuMenus = $database->loadObjectList();

                $menuTypes = '';
                foreach ( $modMenus as $modMenu ) {
                        $check = 1;
						
                        $modParams         = mosParseParams( $modMenu->params );
                        $menuType         = @$modParams->menutype;
                        if (!$menuType) {
                                $menuType = 'mainmenu';
                        }

                        // stop duplicate menutype being shown
                        if ( !is_array( $menuTypes) ) {
                                // handling to create initial entry into array
                                $menuTypes[] = $menuType;
                        } else {
                                $check = 1;
                                foreach ( $menuTypes as $a ) {
                                        if ( $a == $menuType ) {
                                                $check = 0;
                                        }
                                }
                                if ( $check ) {
                                        $menuTypes[] = $menuType;
                                }
                        }

                }
                // add menutypes from jos_menu
				
                foreach ( $menuMenus as $menuMenu ) {
                        $check = 1;
                        foreach ( $menuTypes as $a ) {
                                if ( $a == $menuMenu->menutype ) {
                                        $check = 0;
                                }
                        }
                        if ( $check ) {
                                $menuTypes[] = $menuMenu->menutype;
                        }
                }

                // sorts menutypes
                asort( $menuTypes );

                return $menuTypes;
        }

        /*
        * loads files required for menu items
        */
        function menuItem( $item ) {
                

                $path = site_path .'/iadmin/component/menus/'. $item .'/';
                include_once( $path . $item .'.class.php' );
                include_once( $path . $item .'.menu.html.php' );
        }
}
?>