<?php

/**
 * Соотношения товаров
 *
 * @author dmitry
 */
/** Функции для работы с соотношениями 
* --> set( array ids) : установка массива с ID товаров
* --> get( int id) : получение товаров, относящихся к Id, отсортированные по частоте встречаемости и времени
*/


class relation {

    function __construct(registry $reg)
    {
        $this->reg  = $reg;
        $this->db = $reg['db'];        
    }

    /* Сохраняет отношение id1 к id2 */
    private function saveRelation($id1, $id2)
    {
        $id1 = intval($id1);
        $id2 = intval($id2);
        $time = time();

        //пусть id1 будет всегда меньше id2
            if($id1 == $id2) return;
            if($id1 > $id2)
                {
                $tmp = $id2;
                $id2 = $id1;
                $id1 = $tmp;
                }
            
          $this->db->setQuery("SELECT id1 FROM #__ex_relation WHERE id1 = ".$id1." AND id2 = ".$id2);
          $this->db->query();
          if($this->db->getNumRows() > 0) //делаем update
          {
              $this->db->setQuery("UPDATE #__ex_relation SET cnt = cnt + 1, time = ".$time." WHERE id1 = ".$id1." AND id2 = ".$id2);
              $this->db->query();
              return $this->db->getAffectedRows();
          }
          else //делаем insert
          {
              $this->db->setQuery("INSERT INTO #__ex_relation VALUES(".$id1.", ".$id2.", 1, ".$time.")");
              $this->db->query();
              return $this->db->getAffectedRows();
          }           
    }

    /** Задает массив с индексами товаров */
    public function set(array $id)
    {
        $n = count($id);
        if($n < 2) return false;

        //Делаем матрицу
        for($i = 0; $i < $n; $i++)
        {
            for($k = $i+1; $k < $n; $k++)
            {
                $this->saveRelation($id[$i], $id[$k]);
            }
        }
    }

    /** Возвращает массив ID, относящихся к заданному */
    public function get($id, $limit = 10)
    {
        $id = intval($id);
        $result = array();
        $query = sprintf("SELECT * FROM #__ex_relation WHERE id1 = %d OR id2 = %d ORDER BY cnt desc, time desc", $id, $id);
        if($limit != 0 && $limit = intval($limit)) $query .= " LIMIT 0,".$limit;
        
        $this->db->setQuery($query);
        $this->db->query();

        if($this->db->getNumRows() == 0) return false;

        $objs = $this->db->loadObjectList();
        foreach($objs as $o)
        {
            if($o->id1 == $id) array_push($result, $o->id2);
            else array_push($result,$o->id1);
        }
        return $result;
    }
}
?>
