<?php
/**
 * Элемент заказа
 *
 * @author dmitry
 */
class orderItem { // implements standartStoreMethods {

    public $pack_id; //Идентификатор товара из каталога (комплектации товара)
    public $order_id; //Идентификатор заказа

    public $name; //Название товара
    public $item_name; // для загрузки из базы
    public $pack_name; //Название комплектации
    public $sefname; //ссылка
    public $sku; //хз что такое
    public $quantity; //количество товара
    public $options; //какие-то опции
    public $price; //Цена в каталоге
    public $price_offer; //Цена по факту
    public $price_offer_id; // идентификатор менеджера, сменившего цену
    public $note; //примечание к товару

    function  __construct(database $database) {
        $this->db = $database;
        $this->pack_id = null;
        $this->order_id = null;
		$this->price = 0;
		$this->price_offer = 0;
		$this->sku = 0;
    }

    /** установить текущий статус
     *
     * @param <INT> $order_id - ID заказа
     */
    public function update_current_status($order_id){
        global $reg;
        if (  $order_id>0  ){
            $i24r = new mosDBTable( "#__orders", "id", $reg['db'] );
            $i24r->id = $order_id;
            $i24r->status_id = ggsqlr (  ' select status_id from #__orderstatushistory where order_id='.$order_id.' order by time DESC limit 0,1 '  );
            if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
        }
    }

    /** Сохранение товара */
    public function save()
    {
        if($this->pack_id == null || $this->order_id == null) return false; //Если товар пустой - не сохраняем
        $query = sprintf("SELECT count(*) FROM #__orderitems WHERE order_id = %d AND pack_id = %d AND options = '%s'", $this->order_id, $this->pack_id, $this->options); //update or insert
        $this->db->setQuery($query);
        $this->db->query();        
        $num = $this->db->loadResult();
        $this->quotes(); // Экранируем кавычки

        $this->price = intval($this->price);
        $this->price_offer = intval($this->price_offer);
        $this->name = mysql_real_escape_string($this->name);
        $this->pack_name = mysql_real_escape_string($this->pack_name);
        $this->sku = mysql_real_escape_string($this->sku);

        if($num > 0) //update
        {            
            $query = sprintf('UPDATE #__orderitems SET item_name = "%s", pack_name = "%s", sku = "%s", quantity = %d, price="%s", price_offer = "%s", price_offer_id = %d, note = "%s" WHERE pack_id = %d AND order_id = %d AND options ="%s"',
                    $this->name, $this->pack_name, $this->sku, $this->quantity,  intval($this->price), intval($this->price_offer), $this->price_offer_id, $this->note, $this->pack_id, $this->order_id, $this->options);
            $this->db->setQuery($query);
            $this->db->query();
            $msg = $this->db->getErrorMsg();
            if($msg == "") return true;
            else throw new Exception($msg);
        }
        else  //insert
        {
            $query = sprintf("INSERT INTO #__orderitems (pack_id, order_id, item_name, pack_name, sku, quantity, options, price, price_offer, price_offer_id, note) VALUES (%d, %d, ' %s ', '%s', '%s', %d, '%s', '%s', '%s', %d, ' %s ')",
                $this->pack_id, $this->order_id, $this->name, $this->pack_name, $this->sku, $this->quantity, $this->options, intval($this->price), intval($this->price_offer), $this->price_offer_id, $this->note);
            $this->db->setQuery($query);
            $this->db->query();
            $msg = $this->db->getErrorMsg();
            if($msg == "") return true; 
            else throw new Exception($msg);
        }
        return false;
    }
    /** определяет необходимость вывода названия комплектации в описании товара */
    public function getRealUsePackname( $packName, $expack_select_type ){
        if (  $expack_select_type==1  or  $expack_select_type==3  ) return $packName;
        else return '';
    }

    /*Экранирует кавычки */
    private function quotes()
    {
        $elements = Array("sku", "options", "note");
        foreach($elements as $e) {
            $this->$e = str_replace('"', '\\"', $this->$e);
            $this->$e = str_replace("'", "\\'", $this->$e);
       }
    }
    /* Отэкранирует кавычки */
    private function unquote()
    {
        $elements = Array("sku", "options", "note");
        foreach($elements as $e) {
            $this->$e = str_replace( '\\"','"', $this->$e);
            $this->$e = str_replace( "\\'","'", $this->$e);
       }
    }

    /** Загружает товар с параментами pack_id и order_id или берет их из текущего объекта */
    public function load( $pack_id, $options )
    {
        $pack = ($pack_id == 0) ? intval($this->pack_id) : intval($pack_id);
        $order = ($order_id == 0) ? intval($this->order_id) : intval($order_id);

        if($pack == 0 || $order == 0) return false; //Неверно указан товар
		$this->loadFromCatalog($pack_id);

        $this->db->setQuery("SELECT * FROM #__orderitems WHERE pack_id = ". $pack. " and order_id = ".$order." AND options = '".$options."'");
        $this->db->query();
        if($this->db->getNumRows() > 0) { $this->db->loadObject($this); $this->name = $this->item_name; }
        else return false;
        $this->unquote();
        if($this->options != '')
        {
            $options = explode(";;", $this->options );
            $this->options = Array();
            foreach($options as $o)
            {
                    list($key, $val) = explode( "::", $o);
                    $this->options[$key] = $val;
            }
        }
        return true;
    }

    /** Удаляет элемент из заказа */
    public function delete ($pack_id = 0)
    {
        $id = ($pack_id == 0) ? intval($this->pack_id) : intval($pack_id);
        if($id == 0 || $this->order_id == 0 || $this->order_id == NULL) return false;

        $this->db->setQuery("DELETE FROM #__orderitems WHERE pack_id = ".$id. " AND order_id = ".$this->order_id);
        $this->db->query();        
        return $this->db->getAffectedRows();
    }

    /** Загружает товар из каталога */
    public function loadFromCatalog($catalog_id)
    {
	 $catalog_id = intval($catalog_id);
	 $this->pack_id = $catalog_id;
	 return true;
        //$this->db->setQuery("SELECT name, price1,sku FROM #__exgood WHERE id = ".$catalog_id);
	
	$this->db->setQuery("SELECT name FROM #__exgood WHERE id = ".$catalog_id);
        $this->db->query();
        if($this->db->getNumRows() == 0) throw new Exception("Товар не найден");
        list($obj) = $this->db->loadObjectList();

        
	$this->name = $obj->name;
        /*$this->price = $obj->price1;
        $this->price_offer = $obj->price1;
        $this->price_offer_id = 0;
        $this->sku = $obj->sku;	*/
    }

}
?>
