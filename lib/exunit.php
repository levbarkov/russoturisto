<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/*
 *
 * КЛАСС ДЛЯ РАБОТЫ С ИДИНИЦАМИ ИЗМЕРЕНИЯ ТОВАРА
 *
 */
class exunit{
        /** содержит запись выборки из таблицы #__exgood_unit базы данных */
        var $vars;

        /**
         * ПОЛУЧИТЬ ID ЕДИНИЦЫ ИЗМЕРЕНИЯ ПО УНИКАЛЬНОМУ ПОЛЮ Connect, ЕСЛИ НЕТ - СОЗДАТЬ И ВОЗВРАТИТЬ ID
         * @param <str> $connect
         * @param <str> $name
         * @param <int> $exgood_id
         * @return <int>
         */
        function getUnitIDbyConnect($connect, $name, $exgood_id){
            $check_unit = ggsql (  "SELECT id FROM #__exgood_unit WHERE connect='".$connect."'  AND parent=".$exgood_id." ; "  );
            if (  !$check_unit[0]->id  ){//создаем единицу измерения
                    $this->vars->id=0;              // 0 добавить новую, если >0 - то изменить существующую
                    $this->vars->parent=$exgood_id; // id товара (  таблица excgood )
                    $this->vars->name=$name;
                    $this->vars->connect=$connect;
                    $this->saveme();
                    return $this->id;
            } else return $check_unit[0]->id;
        }

        /**
         * ИЗМЕНЕНИЕ ( vars->id>0 ) ИЛИ СОЗДАНИЕ НОВОЙ ( vars->id=0 ) ЕДИНИЦЫ ИЗМЕРЕНИЯ
         * $this->vars - содержит данные по единице измерения
         */
        function saveme (){
            global $reg;

            $i24r = new mosDBTable( "#__exgood_unit", "id", $reg['db'] );
            $i24r->id = $this->vars->id;
            $i24r->name = $this->vars->name;
            $i24r->parent = $this->vars->parent;
            if (  $this->vars->connect  )  $i24r->connect = $this->vars->connect;
            if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
            $this->id = $i24r->id;
        }






}
?>