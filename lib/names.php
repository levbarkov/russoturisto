<?php
class names {
	public  $type;
        public  $id;	
	var $name;

        function __construct($id = 0, $type = 0, registry $reg = null)
        {
            $this->id = intval($id);
            $this->type = $type;
            $this->db = $reg['db'];
	    $this->reg = $reg;
            $this->delimeter = ",";
        }

        /** Получение всех свойств элемента */
        public function get()
        {
            $query = "SELECT n.*, npr.name as parname, npr.small as parsmall, npr.mid as parmid, npr.org as parorg, npr.full as parfull from #__names_parent as np
                                                     left join #__names as n on np.nameid = n.id
                                                     left join #__names_prop as npr on n.propid = npr.id
                                                     WHERE np.parent = ".$this->id." AND type = '".$this->type."'
                                                     order by npr.ordering ASC, n.ordering asc";
            
            $this->db->setQuery($query);
            $this->db->query();
            if($this->db->getNumRows() > 0)
             {
                   $objs = $this->db->loadObjectList();
                   $result = Array();

                   $prev = new stdClass();
                   $prev->name = $objs[0]->parname;
                   $prev->small = $objs[0]->parsmall;
                   $prev->mid = $objs[0]->parmid;
                   $prev->org = $objs[0]->parorg;
                   $prev->full = $objs[0]->parfull;
                   $prev->children = Array();
                   
                    foreach($objs as $o)
                    {
                        if($o->parname != $prev->name)
                        {
                             $result[] = $prev;
                             $prev = new stdClass();
                             $prev->name = $o->parname;
                             $prev->small = $o->parsmall;
                             $prev->mid = $o->parmid;
                             $prev->org = $o->parorg;
                             $prev->full = $o->parfull;
                             $prev->children = Array();
                             $prev->children[] = $o;
                        }
                        else $prev->children[] = $o;
                    }
                    $result[] = $prev;
                    return $result;
             }
             
             return false;
       }


        /** Установка значений */
        public function set($string){		

        if($this->type == "" || $this->id != intval($this->id)) return;

         $this->db->setQuery("DELETE FROM #__names_parent WHERE parent = ".$this->id." AND type='".$this->type."'");
         $this->db->query();        

	 if(strlen($string) == 0) return;
         $elements = explode($this->delimeter, $string);

        foreach($elements as $element){
            $element = preg_replace("/\"|'|;/", '', $element);
            $element = trim($element);            
	    if($element != "")
	    {
		$this->db->setQuery("SELECT id FROM #__names WHERE innername = '". $element."'");
		$this->db->query();	      
            
		 if($this->db->getNumRows() > 0)
		  {                
		      $elid = $this->db->loadResult();
		      $query = sprintf("INSERT INTO #__names_parent VALUES(%d, %d,'%s')", $elid, $this->id, $this->type);
		      $this->db->setQuery($query);
		      $this->db->query();                   
		    }        
	      }
           }
           return true;
        }


	public function delete()
	{
	      $this->db->setQuery ( "DELETE FROM #__names_parent WHERE parent = ".$this->id." AND type='".$this->type."' " );
	      $this->db->query();
	      return true;
	}

	function get_all_names_string($seppa=","){
                global $reg;
                // выбираем все names
		$tags = ggsql ( " select innername from #__names " );
		$tags_str=""; 
		foreach ($tags as $tag) $tags_str.=$tag->innername.$seppa; 
		$tags_str=substr(   $tags_str,  0,  ( strlen($tags_str)-strlen($seppa) )   );
		return $tags_str;
	}

    /**
     * Установить новые names
     *
     * @param <string> $names_string
     */
    public function apply_names($_names_field){
            ilog::vlog('/* function apply names');
            $need2SaveNames = true;
            if (  $this->id>0  ){   // объект уже есть
                $old_names = $this->get_names_string();
                if (  $_names_field==$old_names  ) $need2SaveNames = false;  // names совпадают, обновлять не стоит
            }
            else $need2Recalc = false;
            
            if (  $need2SaveNames  ){
                $this->set( $_names_field );
            }
            ilog::vlog('function apply names */');
    }

    /** Генерирует строку с свойствами
     * ($id = 0)
     */
    public function get_names_string($id = 0){
        if($id == 0) $id = $this->id;
        else $id = intval($id);
        $this->db->setQuery("SELECT n.innername FROM #__names_parent as np
                             left join #__names as n on np.nameid = n.id
                             WHERE np.parent = $id AND type='".$this->type."'
                             order by n.ordering desc");
        $res = $this->db->loadResultArray();
        $value = join($this->delimeter." ", $res);
        return $value;
    }

    /** Генерирует поле с свойствами
     * ($id = 0, $lenght = 30, $input_id="_tag_field", $input_name="_names_field", $css_class="ex_tegs_names_style")
     */
    public function field($id = 0, $lenght = 30, $input_id="_tag_field", $input_name="_names_field", $css_class="ex_tegs_names_style")
    {
        if($id == 0) $id = $this->id;
        else $id = intval($id);
        $this->db->setQuery("SELECT n.innername FROM #__names_parent as np
                             left join #__names as n on np.nameid = n.id
                             WHERE np.parent = $id AND type='".$this->type."' 
                             order by n.ordering desc");
        $res = $this->db->loadResultArray();
        $value = join($this->delimeter." ", $res);
            return '<input type="text" name="'.$input_name.'" id="'.$input_id.'" size="'.$lenght.'" value="'.$value.'" class="'.$css_class.'">'."\n";
    }
	
	function get_names ($prop){
		return ggsql("  SELECT * FROM #__names_parent WHERE type='$this->type' and parent=$this->parent; ");
	}
	function get_props (){
		return ggsql("  SELECT * FROM #__names_parent WHERE type='$this->type' and parent=$this->parent; ");
	}
	function get_main_foto( &$name, $type, $noimage_return=0 ){
		if (  $name->$type!=''  ) return "/images/names/".$name->$type;
		else {
			if (  $noimage_return==0  ) return false;
			else return $reg['names_main_small_noimage'];
		}
	}
	function add_name($id){
		$this->name[$id] = ggo($id, "#__names");
	}
	
	function get_main_name( &$name ){
		return desafelySqlStr($name->name);
	}
	function get_main_desc( &$name, $type ){
		return desafelySqlStr($name->$type);
	}
	function howmany_names( $id ){
		$total_names =  ggsqlr ("SELECT count(id) FROM #__names WHERE propid=".$id." ; ");
		if (  $total_names==''  ) return 0;
		else return $total_names;
	}

}
?>
