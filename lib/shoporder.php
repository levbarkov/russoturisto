<?php
/**
 * Заказ в интернет - магазине
 *
 * @author dmitry
 */
class shopOrder implements standartStoreMethods {

    public $id; //Идентификатор
    public $uid; //Покупатель
    public $code; //Код заказа
    public $create_time; // Время заказа
    public $payment_type; //Тип оплаты
    public $delivery_type; //Тип доставки
    public $price; //Стоимость заказа    
    public $items; //Товары
    public $note; //Примечание
    public $managers; //Менеджеры заказа
    public $status; //Текущий статус заказа
    public $statusList; //Полный писок статусов
    public $statusFull; //Полное описание текущего статуса
    public $statusHistory; //История статусов

    public $clientFIO; //ФИО покупателя
    public $clientPhone; //Телефон покупателя
    public $clientEmail; //Почта покупателя
    public $clientAddress; //Адрес доставки

    private $mngAccess = 19;
    private $admAccess = 25;

    public function  __construct(registry $registry, $action = "")
    {
        $this->id  = NULL;
        $this->reg = $registry;
        $this->db = $registry['db'];
        $this->items = array();
        $this->my = $this->reg['my'];       
        $this->uid = $this->my->id;
        $this->create_time = time();
        
         // Загружаем статусы
        $this->db->setQuery("SELECT name FROM #__orderstatuslist order by `ordering`");
        $this->db->query();
        $this->statusList = $this->db->loadResultArray();
        $this->managers = Array();

        if($this->reg['shopMngAccess'] != "") $this->mngAccess = $this->reg['shopMngAccess'];
        
        switch($action)
        {
            case "new": $this->getNewIdent(); break;
        }        
    }
    
    /** Загрузка заказа */
    public function load($orderId)
    {
        $id = ($orderId == 0) ?  intval($this->id) :intval($orderId);
        if($id == 0) return false;

        // Загружаем заказ
        $this->db->setQuery("SELECT * FROM #__orders WHERE id = ".$id);
        $this->db->query();
        if($this->db->getNumRows() == 0) return false;
        $this->db->loadObject($this);

        // Загружаем манагеров
        $this->db->setQuery("SELECT manager_id FROM #__ordermanagers WHERE order_id = ".$id);
        $this->db->query();
        if($this->db->getNumRows() > 0)  $this->managers = $this->db->loadResultArray();

        //Загружаем товары
        $this->db->setQuery("SELECT pack_id, options from #__orderitems WHERE order_id = ".$id);
        $this->db->query();
        if($this->db->getNumRows() > 0)
        {
            $ids = $this->db->loadObjectList();
            foreach($ids as $oid)
            {
                $item = new orderitem($this->db);
                $item->order_id = $this->id;
                $item->load($oid->pack_id, $oid->options);
                $this->items[] = $item;
            }
        }
        // Загружаем текущий статус
        $this->db->setQuery("SELECT * FROM #__orderstatushistory WHERE time = (SELECT MAX(time) FROM #__orderstatushistory WHERE order_id = ".$id.") AND order_id = ".$id);
        $this->db->query();               
        list($this->statusFull) = $this->db->loadObjectList();
        $this->db->loadObject($this->statusFull);
        $this->status = $this->statusFull->status_id;
        $this->price = $this->recalcPrice();
    }

    /** Сохраняет элементы заказа */
    public function saveItems()
    {
        if(!is_array($this->items)) return false;
        foreach($this->items as $item)  $item->save();

    }

    private function makeOptions($options)
    {
            if(!is_array($options)) $options = '';
            else {
                $tmp = Array();
                foreach($options as $key=>$val)
                {
//                    if($key == "display_type") continue;
                    $tmp[] = $key."::".$val;
                }
                $options = join(";;", $tmp);
        }
        return $options;
    }

    /** Добавляет элемент в заказ */
    public function addItem($id, $quantity, $options)
    {

        $options = $this->makeOptions($options);
        $quantity = intval($quantity);
        if($quantity == 0) return false;
        $found = false;

        if(is_array($this->items)) //Поиск товара в уже имеющихся
        {
                foreach($this->items as $item)
                {
                    if($item->pack_id == $id && $item->options == $options) {
                        $item->quantity += $quantity;                        
                        $found = true;                       
                    }
                }
        }
        
        //Если товара нет - добавляем
        if(!$found)
        {
            $item = new orderItem($this->db);
            $item->loadFromCatalog($id);
            $item->quantity = $quantity;
            $item->order_id = $this->id;
            $item->options = $options;
            $this->items[] = $item;
        }
        
        $item->save();
        $this->price += $item->price_offer * $quantity;
	return $item;
    }

    /** Удаляет элемент из заказа
     *   если кол-во = 0, удаляет все */
    public function removeItem($id, $options, $quantity = 0)
    {
        $id = intval($id);
        $options = $this->makeOptions($options);
        if(is_array($this->items))
        {
            $n = count($this->items);
            for($i = 0; $i < $n; $i++)
            {
                $item = $this->items[$i];
                if($item->pack_id == $id && $item->options == $options)
                {                    
                    if($quantity == 0 )
                    {
                        $item->delete();
                        unset($this->items[$i]);
                    }
                    else
                    {
                        $item->quantity -= $quantity;
                        if($item->quantity < 1)
                        {
                            $item->delete();
                            unset($this->items[$i]);
                        }
                    }
                }
            }
        }
        $this->items = array_values($this->items);
        return false;
    }

    /** Удаляет заказ */
   public function delete()
   {
        if($this->my->gid < $this->admAccess) return false;

       if($this->id == NULL) return false;
       
       if(is_array($this->items))
       {
           foreach($this->items as $item) $item->delete();
       }

       $this->db->setQuery("DELETE FROM #__orders WHERE id = ". $this->id);
       $this->db->query();       
       $this->db->setQuery("DELETE FROM #__ordermanagers WHERE order_id = ".$this->id);
       $this->db->query();
       $this->db->setQuery("DELETE FROM #__orderstatushistory WHERE order_id = ".$this->id);
       $this->db->query();
       
   }

   /**Сохраняет заказ */ 
   public function save()
   {
       $this->saveItems();      

       $query = sprintf('UPDATE #__orders set clientFIO = "%s", clientPhone = "%s", clientEmail = "%s", clientAddress = "%s", note = "%s", uid = %d, create_time = %d, payment_type = %d, delivery_type = %d WHERE id = %d',
       $this->filter($this->clientFIO), $this->filter($this->clientPhone), $this->filter($this->clientEmail), $this->filter($this->clientAddress), $this->filter($this->note), $this->uid, $this->create_time, $this->payment_type, $this->delivety_type, $this->id);
       $this->db->setQuery($query);
       $this->db->query();
       $e = $this->db->getErrorMsg();
       if($e != "") throw new Exception($e);

       $this->db->setQuery("SELECT count(*) from #__orderstatushistory WHERE order_id = ".$this->id);
       $this->db->query();
       $c = $this->db->loadResult();
       if($c == 0)
       {           
           $this->db->setQuery("INSERT INTO #__orderstatushistory (order_id, status_id, time, manager_id) VALUES (".$this->id.", ".$this->status.", ".time().", 0)");
           $this->db->query();
       }

   }

   /** Рассчитывает цену с учетом скидок и акций */
   public function calcPrice()
   {
        return $this->price;
   }
   
    /** Пересчет цены по товарам */
   public function recalcPrice()
   {
       $price = 0;       
       if(is_array($this->items)) {
            foreach($this->items as $item) $price += $item->price_offer * $item->quantity;
       }
       return $price;
   }
   
   /** Создает номер заказа */
   public function getNewIdent()
   {
        global $reg;

        mt_srand();
        $code = md5("aa".mt_rand(1000, 100000)."bbzz");
        $this->code = $code;
        $this->db->setQuery("SELECT id FROM #__orders WHERE code ='".$code."'");
        $this->db->query();
        if($this->db->getNumRows() != 0) return $this->getNewIdent();

        // все нормально - создаем новый заказ
        $i24r = new mosDBTable( "#__orders", "id", $reg['db'] );
        $i24r->id = 0;
        $i24r->code = $code;
        $i24r->status_id = $reg['shop_order_first_status_id'];
        $i24r->note = '';
        $i24r->clientAddress = '';
        if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

        $this->id = $i24r->id;
        $this->status = $reg['shop_order_first_status_id'];
        if (  $i24r->_db->_errorNum==0  ) return $i24r->id;
        else { ggtr5($i24r); return false; };
   }


   /**Добавляет менеджера, обслуживающего заказ */
   public function addManager($id)
   {
        if($this->my->gid <  $this->admAccess ) return 0; //Если пользователь не админ
	if($this->checkManager($id)) return 0; // Если менеджер уже есть

        $id = intval($id);
        if($this->id == NULL) return false;
        if($this->checkUser($id, $this->mngAccess))
        {
            $this->db->setQuery("INSERT INTO #__ordermanagers (order_id, manager_id) VALUES (". $this->id.", ".$id.")");
            $this->db->query();
            $this->managers[] = $id;
            return true;
        }
        return false;
   }

   /** Удаляет менеджера */
   public function removeManager($id)
   {
       if($this->my->gid <  $this->admAccess) return false;

        $id = intval($id);
        if($id == 0) return false;
        
        if(is_array($this->managers))
        {            
            $n = count($this->managers);
            for($i = 0; $i < $n ;$i++)
            {
                if($this->managers[$i] == $id)
                {                    
                    unset($this->managers[$i]);
                    $this->db->setQuery("DELETE FROM #__ordermanagers WHERE order_id = ".$this->id." AND manager_id =".$id);
                    $this->db->query();
                }
            }
            $this->managers = array_values($this->managers);
        }
        return true;
   }

   /** Проверка пользователя на существование */
   public function checkUser($uid, $gid = 0)
   {
       $uid = intval($uid);
       if($uid == 0) return false;
       $query = "select count(*) FROM #__users WHERE id = ".$uid;
       if($gid != 0) $query .= " AND gid >= ".$gid;
       $this->db->setQuery($query);
       $this->db->query();
       return $this->db->loadResult();
   }

   /** Проверка наличия менеджера ID у объекта */
   public function checkManager($id)
   {
       $id = intval($id);
       if(!is_array($this->managers)) return false;

       foreach($this->managers as $manager)
       {
           if($manager == $id) return true;
       }
       return false;
   }

   /** Установка статуса */
   public function setStatus($status_id, $note)
   {
       $manager_id = intval($this->my->id);
       if(isset($this->statusList[$status_id-1]))
       {
           if(!$this->checkManager($manager_id)) return "nomanager";
           $this->status = $status_id;
           $note = $this->filter($note);
           
           $query = sprintf("INSERT INTO #__orderstatushistory (order_id, status_id, time, manager_id, note) values (%d, %d, %d, %d, \" %s \")",
                   $this->id, $status_id, time(), $manager_id, $note);
           $this->db->setQuery($query);
           $this->db->query();
           $e = $this->db->getErrorMsg();
           if($e != "") throw new Exception($e);

           // ОБНОВИТЬ ТЕКУЩИЙ СТАТУС ЗАКАЗА
           orderItem::update_current_status($this->id);

           return true;
       }
       return "nostatus";
   }

   /**Получение истории статусов */
  public function getStatusHistory()
  {
        $this->db->setQuery("SELECT * FROM #__orderstatushistory WHERE order_id = ".$this->id." order by `time` DESC ");
        $this->db->query();
        if($this->db->getNumRows() > 0)
        {
            $this->statusHitory = $this->db->loadObjectList();
            return $this->statusHistory;
        }
        return false;
  }

   /** Получение имени пользователя */
   function getUserName($uid)
   {
	if($uid == 0) return;
        $uid = intval($uid);
        $this->db->setQuery("SELECT name,userparentname  FROM #__users WHERE id = ".$uid);
        $this->db->query();
        if($this->db->getNumRows() > 0) {
			$manager = $this->db->loadObjectList();
			return desafelySqlStr(  $manager[0]->name.' '.$manager[0]->userparentname  );
		}
        return false;
   }

   /** Кол-во заказов у менеджера */
   function getManagerOrders($uid)
   {
       $uid = intval($uid);
       $this->db->setQuery("SELECT count(*) FROM #__ordermanagers WHERE manager_id = ".$uid);
       $this->db->query();
       return $this->db->loadResult();
   }

   /**
    * ОПРЕДЕЛЯЕМ ЛЮБИМОГО МЕНЕДЖЕРА (т.е. Если клиент совершает не первую покупку - предыдущего менеджера)
    *
    * @param <int> $orderid
    * @param <int> $shoper_id
    */
   function getFavouriteManager($orderid, $shoper_id){
        global $reg;
        
        $this->db->setQuery("SELECT * FROM #__ordermanagers as om
                       left join #__orders as o on (om.order_id = o.id)
                       WHERE o.uid = ".$shoper_id." order by o.id DESC LIMIT 0,1");
        list($res) = $this->db->loadObjectList();
        return $res->manager_id;
   }
   
   /** Фильтрует строку */
   function filter($string)
   {
       //$string = str_replace('"', '\\"', $string);
       //$string = str_replace("'", "\'", $string);
       return safelySqlStr($string);
   }

  /** Загрузка заказа по коду */
      public function loadByCode($code)
      {
	    if(!preg_match("/[A-Za-z0-9]/", trim($code))) return 0;
	    else	{
		$this->db->setQuery("SELECT id FROM #__orders WHERE code = \"".$code."\" LIMIT 0,1 ");
		$this->db->query();
		if($this->db->getNumRows() > 0) { $id = intval($this->db->loadResult()); $this->load($id);  return true;}	      
	    }
	    return 0;
      }

}
