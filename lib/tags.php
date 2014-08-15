<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Класс для работы с тегами
 *
 * @author dmitry
 */

/* Значения конфига для  тэгов
 *  _component_AllowTags, i.e. exAllowTags, adAllowTags - разрешение для компонента использовать тэги
 *  tagDelimeter - Разделитель строки тэгов, по умолчанию ','
 *  tagMinLenght - Минимальная длина тэга, по умолчанию 4 символа
 *  tagMaxLenght - Максимальная длина тэга, по умолчанию 12 символов
 *  tagToSmall - приводить тэги к нижнему регистру. По умолчанию 1. Для русских букв требуется настроенная локаль.
 *  tagToBig - приводить тэги к верхнему регистру. По умолчанию 0. Имеет приоритет над tagToSmall.
 */
/** Функции для работы с тэгами
 * ->set(string, id) : устанавливает значение тэгов string для элемента с id
 * ->field(id, lenght) : создает поле с тэгами для элемента id длиной lenght
 * ->view(id) : просмотр элемента id, увеличивает счетчик просмотров всех его тэгов на 1
 * ->delete(id) : вызывать при удалении элемента, удаляет его тэги
 * ->resetViews() : сброс просмотров всех тэгов
 * ->resetWeight() : сброс весов всех тэгов
 * ->recalcAll(); : пересчет весов всех тэгов и удаление потерянных
 */
class tags {

    private $vars;

    function __get($val)
    {
        if(isset($this->vars[$val])) return $this->vars[$val];
        
        return false;
    }

    function __set($key, $val)
    {
        $this->vars[$key] = $val;
    }

    function __construct($component, database $database, registry $reg)
    {
        $this->component = $component;
        $this->db = $database;
        $this->registry = $reg;
        //Устанавливаем символ разбивки строки
        if($this->registry["tagDelimeter"] == false) $this->delimeter = ", ";
        else $this->delimeter = $registry["tagDelimeter"];

        //Проверяем, есть ли тэги для этого компонента. В конфиге значение переменной _component_AllowTags, i.e. exAllowTags, adAllowTags ...
        $var = $this->component."AllowTags";
        if($this->registry[$var] == false) $this->allow = false;
        else $this->allow = true;
    }
	
	function get_all_tegs_string($seppa=","){
		$tags = ggsql ( "select name from #__tags" ); 
		$tags_str=""; 
		foreach ($tags as $tag) $tags_str.=$tag->name.$seppa; 
		$tags_str=substr(   $tags_str,  0,  ( strlen($tags_str)-strlen($seppa) )   );
		return $tags_str;
	}

    /**
     * Установить новые теги
     *
     * @param <string> $tag_string
     */
    public function apply_tag($_tag_field){
              ilog::vlog('/* function tag::apply_tag');
              $need2Recalc = true;
              if (  $this->id>0  ){   // объект уже есть
                  $old_tags = $this->get_tegs_string( $this->id );
                  if (  $_tag_field==$old_tags  ) $need2Recalc = false;
              }
              else $need2Recalc = false;
              
              if (  $need2Recalc  ){
                  try {
                          $this->delete($this->id);
                          $this->set($_tag_field, $this->id);
                          $this->recalcAll();
                  }
                  catch (Exception $e){	print $e->getMessage(); }
              }
              ilog::vlog('function tag::apply_tag */');
    }

    /**
     * получить строку с тегами
     * @param <int> $id
     * @return <string>
     */
    public function get_tegs_string($id = 0){
            $val = "";
            /* Если есть входящий ID, то делаем значение для поля */
            if($id != 0 && $this->component != ""){
                $id = intval($id);
                $this->db->setQuery("SELECT t.name from #__tags as t right join #__tags_parent as p on (t.id = p.tag_id) where p.parent = ".$id." and type = \"".$this->component."\"");
                $items = $this->db->loadResultArray();
                $val = join($this->delimeter, $items);
            }
            return $val;
    }

    /** Генерирует поле с тэгами */    
    public function field($id = 0, $lenght = 30, $input_id="_tag_field", $css_class="ex_tegs_names_style")
    {
            if(!$this->allow) return false;
            
            $val = "";
            /* Если есть входящий ID, то делаем значение для поля */
            if($id != 0 && $this->component != "")
            {
                $id = intval($id);
                $this->db->setQuery("SELECT t.name from #__tags as t right join #__tags_parent as p on (t.id = p.tag_id) where p.parent = ".$id." and type = \"".$this->component."\"");
                $items = $this->db->loadResultArray();
                $val = join($this->delimeter, $items);
                //$val = htmlspecialchars($val);
            }
            return '<input type="text" name="_tag_field" id="'.$input_id.'" size="'.$lenght.'" value="'.$val.'" class="'.$css_class.'">'."\n";
    }

    /** Устанавливаем значение тэгов для элемента. Входящие даные строка с тэгами через разделитель и ID элемента  */
    public function set($string, $id){
		global $reg;

        if($string == "" || $this->component == "" || $id != intval($id)) return;
  
        /* Разбиваем на составные части */
        $minLenght = 2; // Минимальная длина тэга, если не задана
        $maxLenght = 64; //Максимальная длина тэга
        if($this->registry["tagMinLenght"] != false) $minLenght = $this->registry["tagMinLenght"];
        if($this->registry["tagMaxLenght"] != false) $maxLenght = $this->registry["tagMaxLenght"];
        $toSmall = 1; //Приводить тэг к нижнему регистру
        if($this->registry['tagToSmall'] != '') $toSmall = $this->registry["tagToSmall"];
        $toBig = 0; //Приводить тэг к верхнему регистру 
        if($this->registry['tagToBig'] != '') $toBig = $this->registry["tagToBig"];

        $tags = explode($this->delimeter, $string); //Разбиваем строку на составляющие
        $ids = Array(); // Запишем ID сохраненных тэгов

		$this->delete($id);
        foreach($tags as $tag){
            $tag = strip_tags($tag); //Режем тэги
            $tag = trim($tag); // Режем пробелы в начале и конце строки

            if(strlen($tag) >= $minLenght && strlen($tag) <= $maxLenght)
            {
                if($toSmall) { 	$tag = mb_convert_case($tag, MB_CASE_LOWER, "UTF-8");	/*strtolower($tag); */ }
                if($toBig)   {	$tag = mb_convert_case($tag, MB_CASE_UPPER, "UTF-8");	/*strtoupper($tag); */ }
                //$tag = preg_replace("/\"|'/",'', $tag); // Режем кавычки
                //$tag = htmlentities($tag); //превращаем все спецсимволы в коды
                $_id = $this->save($tag, $id);
                array_push($ids,$_id);               
            }
        }
        return $ids;
    }

    /** Вызывается при просмотре элемента, увеличивает счетчик просмотра тэгов на 1 */
    public function view($id)
    {
        $id = intval($id);
        if($id == 0) return; 
        if($this->component == "") throw new Exception("Тэги: не указан компонент");

        $query = "UPDATE #__tags SET cnt = cnt + 1 WHERE id IN (SELECT tag_id from #__tags_parent WHERE parent = ".$id." AND type = \"".$this->component."\")";
        $this->db->setQUery($query);
        $this->db->query();
        return $this->db->getAffectedRows();
    }

    /** Сбрасывает счетчик просмотров для всех тэгов */
    public function resetViews()
    {
        $this->db->setQuery("UPDATE #__tags SET cnt = 0");
        $this->db->query();
        return $this->db->getAffectedRows();
    }

    /** Сбрасывает весов для всех тэгов */
    public function resetWeight()
    {
        $this->db->setQuery("UPDATE #__tags SET size_weight = 0, bright_weight = 0");
        $this->db->query();
        return $this->db->getAffectedRows();
    }


    /** Проверка, существует ли тэг */
    private function exists($tag)
    {
        $this->db->setQuery("SELECT COUNT(*) FROM #__tags WHERE name = \"".$tag."\"");
        $this->db->query();
        if($this->db->loadResult() > 0) return true;
        return false;
    }

    /** Сохраняем тэг для элемента ID*/
    private function save($tag, $id){
		$tagId = 0;
		if(!$this->exists($tag)){ // Если тэга нет - запишем в таблицу
				$this->db->setQuery("INSERT INTO #__tags VALUES (0, '".$tag."', 0, 0, 0, ".time().")");
				$this->db->query();	    
				$tagId = $this->db->insertid();
		}
		else{
			$this->db->setQuery("SELECT id FROM #__tags WHERE name = '".$tag."' LIMIT 0,1");
			$this->db->query();
			$tagId = $this->db->loadResult();
		}
		$this->db->setQuery("INSERT INTO #__tags_parent values(".$tagId.", ".$id.", '".$this->component."')");
		$this->db->query();
		return $this->db->insertid();
	}

    /** Удаляет тэги для элемента ID */
   public function delete($id)
   {
    if($id == 0 || $id != intval($id)) throw new Exception("Тэги: неверный инедтификатор");
    if($this->component == "") throw new Exception("Тэги: не задан компонент");

    $this->db->setQuery("delete from #__tags_parent WHERE parent = ".$id." and type = '".$this->component."'");
    $this->db->query();
    return $this->db->getAffectedRows();
   }

    /** Пересчитываем все тэги */
    public function recalcAll()
    {
        ilog::vlog('/* function tag::recalcAll');
        //Выбираем критические значения
        $this->db->setQuery("SELECT MAX(cnt) as maxCnt, MIN(cnt) as minCnt FROM #__tags");
        $this->db->query();
		$obj = $this->db->loadObjectList();
	    $maxCnt = $obj[0]->maxCnt;
		$minCnt = $obj[0]->minCnt;

        //Делаем пересчет для всех тэгов
        $this->db->setQuery("SELECT * FROM #__tags ORDER BY id DESC");
        $this->db->query();
        $tags = $this->db->loadObjectList();
        
        $ent = Array(); //массив с кол-вом встречаемости тэгов
        foreach ($tags as $tag)
        {
            if($ent[$tag->id] == "")
            {
                $this->db->setQuery("SELECT COUNT(t.id) FROM #__tags as t join #__tags_parent as p on (t.id = p.tag_id) where t.id = ".$tag->id);
                $this->db->query();		
                $ent[$tag->id] = $this->db->loadResult();	      
                /* Если тэг нигде не встречается, то удаляем его */
                if($ent[$tag->id] == 0)
                {
                    $this->db->setQuery("DELETE FROM #__tags WHERE id='".$tag->id."'");
                    $this->db->query();
                }
            }
        }

        //Выбираем критические значения встречаемости
        $entMax = max($ent);
        $entMin = min($ent);

        //Теперь пересчитываем значения весов
        foreach($tags as $tag)
        {
            $tag->size_weight = $this->weight($tag->cnt, $minCnt, $maxCnt );
            $tag->bright_weight = $this->weight($ent[$tag->id], $entMin, $entMax);
            $this->db->updateObject("#__tags", $tag, "id");
        }
        ilog::vlog('function tag::recalcAll */');
    }

    /** Пересчитывает весовые коэффициенты тэга */
    private function weight($val, $valMin, $valMax)
    {
          if($valMax == $valMin) return 0; //DIVISION BY ZERO
	  else return abs(round(($val - $valMin) / ($valMax - $valMin),2));  	     
    }

    /** Создает XML файл */
    public function generateXML($file = "")
    {
	
	$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n<tags>\n";	
	$this->db->setQuery("SELECT * from #__tags order by size_weight DESC limit 0,100 ");
	$this->db->query();
	$tags = $this->db->loadObjectList();
        foreach($tags as $tag){
			$components = Array();
			$this->db->setQuery("SELECT DISTINCT type FROM #__tags_parent where tag_id = ".$tag->id);
			$this->db->query();
			$components = $this->db->loadResultArray();
			$xml .= $this->xml_wrap_tag($tag, $components);
        }
	$xml .= "</tags>";
	if($file != "")
	{
		  $fp = @fopen($file,"w");
		  if(!$fp) return false;
		  fwrite($fp, $xml);
		  fclose($fp);
	}
    }

    /* Заворачивает объект тэга в xml обертку */
    private function xml_wrap_tag($tag, array $components)
    {
	if(!is_object($tag)) return;
	$str = "";
	foreach($components as $o)
	{
		$str .= " ".$o."=\"1\"";
	}
        //$name = @iconv("Windows-1251","UTF-8",$tag->name);
        $xml = " <tag".$str.">\n";
        $xml .= "\t<id >".$tag->id."</id>\n";
        $xml .= "\t<name>".$tag->name."</name>\n";
        $xml .= "\t<size>".$tag->size_weight."</size>\n";
        $xml .= "\t<bright>".$tag->bright_weight."</bright>\n";
        $xml .= "\t<cnt>".$tag->cnt."</cnt>\n";
        $xml .= "\t<m_time>".$tag->m_time."</m_time>\n";
        $xml .= " </tag>\n";
	
	return $xml;
    }

}
?>
