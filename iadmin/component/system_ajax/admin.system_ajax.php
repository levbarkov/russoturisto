<?php

/*
 * УНИВЕРСАЛЬНЫЙ КОМПОНЕНТ
 * СОДЕРЖИТ ОБРАБОТЧИК ДЛЯ СИСТЕМНЫХ AJAX ФУНКЦИЙ
 */

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();

function change_parent_menu(){
    global $reg;

    $id = '';
    if ( $row->id ) {
            $id = "\n AND id != " . (int) $row->id;
    }

    // get a list of the menu items
    // excluding the current menu item and its child elements
    $query = "SELECT m.*"
    . "\n FROM #__menu m"
    . "\n WHERE menutype = " . $reg['db']->Quote( ggrr('menutype') )
    . "\n AND published != -2"
    . $id
    . "\n ORDER BY parent, ordering"
    ;
    $reg['db']->setQuery( $query );
    $mitems = $reg['db']->loadObjectList();

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

    $output = mosHTML::selectList( $mitems, 'parent', 'class="inputbox" size="10"', 'value', 'text', 0 );

    print $output;
    
}
?>
