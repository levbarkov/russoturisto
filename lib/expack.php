<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/*
 * КЛАСС ДЛЯ РАБОТЫ С КОМПЛЕКТАЦИЯМИ ТОВАРА
 */

class expack{
        /** содержит запись выборки из таблицы #__expack базы данных */
        var $vars;

        /**
         * ИЗМЕНЕНИЕ ( vars->id>0 ) ИЛИ СОЗДАНИЕ НОВОЙ ( vars->id=0 ) КОМПЛЕКТАЦИИ
         * $this->vars - содержит данные по комплектации
         *
         * @param <int> $update_expack=1 выполнить sql-запрос INSERT или UPDATE #__expack для комплектации
         */
        function saveme ($update_expack = 1){
            global $reg;

            $i24r = new mosDBTable('#__expack', 'id', $reg['db']);
            $i24r->id = $this->vars->id;
			
            if (isset($this->vars->name))
				$i24r->name = $this->vars->name;
				
            if (isset($this->vars->sku))
				$i24r->sku = $this->vars->sku;
				
            if (isset($this->vars->parent))
				$i24r->parent = $this->vars->parent;
				
            if (isset($this->vars->unit))
				$i24r->unit = $this->vars->unit;
				
            if (isset($this->vars->expack_set))
				$i24r->expack_set = $this->vars->expack_set;
				
            if (isset($this->vars->connect))
				$i24r->connect = $this->vars->connect;

            if ($update_expack){
                if (!$i24r->check()) {
					echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
					return false;
				}
				else
					$i24r->store();
					
                if ($i24r->_db->_errorNum != 0)
					ggd($i24r->_db); // выполнено c ошибками
            }
			
            $this->id = $i24r->id;

            // необходимо удалить старые значения свойств для этой комплектации, например: цвет-черный, размер-44
            if ($this->vars->id > 0 && $this->vars->expack_set > 0)
                ggsqlq("DELETE FROM #__expack_set_val WHERE `pack_id` = {$this->id}");
            
            // добавление новых значений свойств для комплектации, например: цвет-черный, размер-44
            if ($this->vars->expack_set > 0 && count($this->expack_set_val) > 0){
                foreach ($this->expack_set_val as $attrib)
                    ggsqlq ("INSERT INTO #__expack_set_val VALUES ({$this->id}, {$attrib->attrib}, {$attrib->attrib_val})");
            }

            # сохраняем остатки
            if (  count($this->sklad)>0  ){
				$all_sklads = ggsql ("select * from #__exsklad");
				foreach ($all_sklads as $sklad ){
					$ostatok->parent = $sklad->id;
					$ostatok->expack = $this->id;
					$ostatok->val = str_replace( ",", ".", $this->sklad[$sklad->id] );
					if (  $ostatok->val == ''  ) $ostatok->val = 0;
					$this->save_sklad_good ($ostatok);
				}
            }

            # создаем стоимости
            if (count($this->price) > 0){
				$all_prices = ggsql (  "select * from #__exprice "  );
				foreach ($all_prices as $price ){
					$price_good->parent = $price->id;
					$price_good->expack = $this->id;
					$price_good->val = str_replace( ",", ".", $this->price[$price->id] );
					$price_good->cy = $this->cy[$price->id];
					if (  $price_good->val==''  ) $price_good->val = 0;
					if (  $price_good->cy==''   ) $price_good->cy = 1;
					$this->save_price_good ($price_good);
				}
            }


        }

	function save_sklad_good(&$i24r){
		$where = array();
		$where[] = " parent=".	$i24r->parent." ";
		$where[] = " expack=".	$i24r->expack." ";
		$where 	= ( count( $where ) ? "\n WHERE ". implode( "\n AND ", $where ) : '' );
		$is_update_sql = ggsql("SELECT val FROM #__exsklad_good $where ; ");
		if ( count( $is_update_sql )>0 ){
            if ($is_update_sql[0]->val != $i24r->val  )
				ggsqlq ("UPDATE #__exsklad_good SET val = ".$i24r->val." $where ; ");
		} else {
			ggsqlq ("INSERT INTO #__exsklad_good VALUES(".$i24r->parent.", ".$i24r->expack.", ".$i24r->val.")");
		}

	}

	function save_price_good (&$i24r){
		$where = array();
		$where[] = " parent=".	$i24r->parent." ";
		$where[] = " expack=".	$i24r->expack." ";
		$where 	= ( count( $where ) ? "\n WHERE ". implode( "\n AND ", $where ) : '' );
		$is_update_sql = ggsql("SELECT val FROM #__exprice_good $where ; ");
		if ( count($is_update_sql)>0 ){
			if (  $is_update_sql[0]->val!=$i24r->val  ) ggsqlq ("UPDATE #__exprice_good SET val = ".$i24r->val.", cy = ".$i24r->cy." $where ; ");
		} else {
			ggsqlq ("INSERT INTO #__exprice_good VALUES(".$i24r->parent.", ".$i24r->expack.", ".$i24r->val.", ".$i24r->cy.")");
		}
	}
	/** удаление комплектации ($id) */
	function delete_expack( $id ){
		global $reg;
		ggsqlq("DELETE FROM #__expack WHERE id = ".$id."  ");
		ggsqlq("DELETE FROM #__exprice_good WHERE expack = ".$id."  ");
		ggsqlq("DELETE FROM #__exsklad_good WHERE expack = ".$id."  ");
                ggsqlq("DELETE FROM #__expack_set_val WHERE pack_id = ".$id."  ");
	}
	
	/** удаление единицы измерения ($id) */
	function delete_unit($id){
		global $reg;
		$all_expack = ggsql ( "select * from #__expack where unit=".$id );
		foreach ($all_expack as $expack1){
			$this->delete_expack( $expack1->id );
		}
		ggsqlq("DELETE FROM #__exgood_unit WHERE id = ".$id."  ");
	}

	function load (&$exgood){
		$this->expack_select_type = $exgood->expack_select_type;
		$this->parent = $exgood->id;
	}
	
	function get_packs(){
		return ggsql (  "select * from #__expack where parent=".$this->parent  );
	}
	function get_1stpack(){
		$pack = ggsql (  "select * from #__expack where parent=".$this->parent." ORDER BY ID "  );
                //ggdd();
                //ggtr ($pack);
		if (  $pack[0]->id  ){
			$pack = $pack[0];
			$pack->price = ggsql (  "select * from #__exprice_good where expack=".$pack->id." ORDER BY parent "  );
			$pack->sklad = ggsql (  "select * from #__exsklad_good where expack=".$pack->id." ORDER BY parent "  );
			return $pack;
		}
		else return false;
	}

	function get_price(&$pack, $sklad){
		$pack_price = ggsql ( "select * from #__exprice_good where parent=".$sklad." and expack=".$pack->id." ; " );
		if (  $pack_price[0]->cy  ) {
			if (  $pack_price[0]->cy!=1  ){
				$price_cy = ggo ( $pack_price[0]->cy, "#__exprice_cy" );
				$pack_price[0]->val_rub = $pack_price[0]->val * $price_cy->conv;
			} else $pack_price[0]->val_rub = $pack_price[0]->val;
			return $pack_price[0];
		}
		else {
			$pack_price_empty->val="";
			$pack_price_empty->val_rub="";
			$pack_price_empty->cy=0;
			return $pack_price_empty;
		}
	}
	/*
         * ОТОБРАЗИТЬ СИМВОЛ ВАЛЮТЫ.
         * $type - стиль написания, предполагается что для сайта достаточно 5-ти стилей для каждой валюты
         * и для каждого стиля определена своя функция
         * таким образом например для рубля определенно 5 функций rub0() ... rub4()
         * rub0() - самое маленькое обозначение, rub4() - самое большое
         */
	function cy($cy, $type=1){
		$func_name = false;
		if (  $cy==1  ) $func_name = "rub$type";
		else if (  $cy==2  ) $func_name = "usd$type";
		else if (  $cy==3  ) $func_name = "eur$type";
		//print ($func_name);
		if (  $func_name!=false  ) return $func_name();
	}

	function get_ostatok(&$pack, $sklad){
		$pack_price = ggsql ( "select * from #__exsklad_good where parent=".$sklad." and expack=".$pack->id." ; " ); 
		if (  $pack_price[0]->val  ) 	return $pack_price[0];
		else {
			$pack_price_empty->val="";
			return $pack_price_empty;
		}
	}

}