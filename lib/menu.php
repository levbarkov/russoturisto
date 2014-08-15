<?php

/**
 * Class menu
 *
 * Автор Георгий
 */
class menu {

    /**
     * ВОЗВРАЩАЕТ МАССИВ С ЭЛЕМЕНТАМИ МЕНЮ
     * ПЕРВЫЙ ПАРАМЕТР = NULL
     * @param <sntring> $menutype
     * @param <int> $parent
     * @return array
     */
    function get_menu_items_rec(&$this_level_items, $menutype, $parent, $this_level, $max_level){
       global $reg;

       $allmenus = array();
       // NULL только при первом вызове, в остальных случаях всегда содержит данные, для сокращения числа sql-запросов
       if (  $this_level_items==NULL  ){
           $sql = "SELECT * FROM #__menu WHERE menutype = '$menutype' AND published = '1' AND parent = $parent ORDER BY ordering";
           $this_level_items = ggsql($sql);
       }
       foreach (  $this_level_items as $index=>$this_level_item  ){
           $allmenus[$index] = $this_level_item;
           
           // смотрим, есть ли подменю
           if (  $this_level>=$max_level  ) continue;
           $sub_level_items = ggsql (  " select * from #__menu where menutype='$menutype' and parent=".$this_level_item->id." and published=1 order by ordering  "  );
           if (  count($sub_level_items)>0  ){
               $allmenus[$index]->submenu = menu::get_menu_items_rec($sub_level_items, $menutype, $this_level_item->id, ($this_level+1), $max_level);
           }
       }
       return $allmenus;
    }

    /**
     * ВОЗВРАЩАЕТ МАССИВ С ЭЛЕМЕНТАМИ МЕНЮ
     * @param <sntring> $menutype название меню
     * @param <int> $parent родительское меню
     * @param <int> $max_level максимально уровней
     * @return array
     */
    function get_menu_items($menutype, $parent, $max_level){
        $sql_first = NULL;
        return menu::get_menu_items_rec($sql_first, $menutype, $parent, 1, $max_level);
    }
   
}
?>
