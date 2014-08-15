<?php
/**
 * Класс работы с массивами
 */
class libarray {

    /**
     * ПРЕОБРАЗОВАТЬ К МАССИВУ ТИПА [id]=>name
     * @param &<array> $rows
     * @return <array> 
     */
    public static function convert_ggsql_object_to_array(  &$rows  ){
            $res = array();
            foreach ( $rows as $row){
                    $res[$row->id] = $row->name;
            }
            return $res;
    }

}
?>
