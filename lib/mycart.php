<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class mycart {
	public $mycart = array();
	public $mycart_session_name = 'mycart';
	public $mycart_index = 0;
	public $total = 0;
    public $goodsInShop = 0;

	/** если планируется использовать скрипт SHOP KEEPER
	 *  - при нажатии на корзину - открывается окно для ввода количества
	 *    далее окно улетает в корзину.
	 */
    public $shopkeeperEnable = 0;
	
	public function __construct(){
		
	}

	public function java_init(){
        if ($this->shopkeeperEnable){
			css("/includes/shopkeeper/css/style.css");
			js("/includes/shopkeeper/js/shopkeeper_insite.js");
        }	
	}
	
	public function load (){
		$this->mycart 			= $_SESSION[$this->mycart_session_name];
		$this->priceTotal_first = $_SESSION[$this->mycart_session_name . '_priceTotal_first'];
		$this->priceTotal 		= $_SESSION[$this->mycart_session_name . '_priceTotal'];
		$this->mycart_index 	= count($this->mycart);
	}
	
	public function maketask($task){
		switch ($task){
			case 'put':
				$this->put();
				break;
			case 'del1':
				$this->del1(ggrr('mycart_cartid'));
				break;
			case 'clear':
				$this->clear();
				break;				
			case 'changeqty':
				$this->changeqty(ggrr('mycart_cartid'), ggri('mycart_qty'));
				break;
			case 'recalc':
				$this->recalc( );
				break;
		}
	}
	
	public function recalc(){
		foreach ($_REQUEST['ex_trush_id'] as $index => $cart_id){
			$cart_id = safelySqlStr($cart_id);
			$qty = safelySqlStr($_REQUEST['ex_trush_count'][$index]);
			if (intval($qty) < 0)
				$qty = 1;
			
			$this->changeqty($cart_id, $qty);
		}
		
		$this->recalcPrice();
	}
	
	public function changeqty($cart_id, $qty){
		if (intval($qty) == 0){
			$this->del1($cart_id);
			return;
		}
		foreach ($this->mycart as $index => $cart){
			if ($cart['cartid'] == $cart_id) {
				$this->mycart[$index]['qty'] = $qty;
				$_SESSION[$this->mycart_session_name] = $this->mycart;
				break;
			}
		}
		
		$this->recalcPrice();
	}
	
	public function del1($cart_id){
		foreach ($this->mycart as $index => $cart){
			if ($cart['cartid'] == $cart_id) {
				unset ($this->mycart[$index]);
				$_SESSION[$this->mycart_session_name] = $this->mycart;
				break;
			}
		}
		
		$this->recalcPrice();
	}
	
	public function clear(){
		foreach ($this->mycart as $index => $cart){
			unset($this->mycart[$index]);
		}
		$_SESSION[$this->mycart_session_name] = $this->mycart;
		$this->recalcPrice();
	}

	public function put(){
		$product = array(
			'id' 		=> Api::$request->getParam('mycart_id', 'int'),
			'cartid'	=> md5("mycart" . $product['id'] . rand(0,99999)),
			'sku'		=> Api::$request->getParam('mycart_sku', 'str', ''),
			'qty'		=> Api::$request->getParam('mycart_qty', 'int', 1),
			'name'		=> Api::$request->getParam('mycart_name', 'str', ''),
			'options'	=> Api::$request->getParam('mycart_options', 'str', ''),
		);

		$this->put_tovar($product);
		$this->recalcPrice();
	}
	
	public function recalcPrice(){
        $this->goodsInShop = 0;
		$this->priceTotal_first = 0;
		$this->priceTotal = 0;
		
		if (count($this->mycart)){
			foreach ($this->mycart as $index => $product){
				$this->goodsInShop += $product['qty'];
				
				$pack = ggo ($product['id'], "#__expack");	
				$good = ggo ($pack->parent, "#__exgood");
	
				// ОПРЕДЕЛЯЕМ СТОИМОСТЬ ЗА ТОВАР С УЧЕТОМ КОЛИЧЕСТВА:
				$expack = new expack();
				$expack_first = new stdClass();
				$expack_first->id = $product['id'];
				$expack_first->parent = $pack->parent;	
				$price = $expack->get_price($expack_first, 1);
				$total = $pack_price->val_rub * $product['qty'];
								
				$this->mycart[$index]['price'] = $price->val_rub;
				$this->mycart[$index]['price_qty'] = $total;
				
				$this->priceTotal_first += $total;
				$this->priceTotal += $total;
			}
		}

		$_SESSION[$this->mycart_session_name] = $this->mycart;
		$_SESSION[$this->mycart_session_name . '_priceTotal_first'] = $this->priceTotal_first;
		$_SESSION[$this->mycart_session_name . '_priceTotal'] = $this->priceTotal;
	}
	
	public function get_tovar($cart_id){
		if (count($this->mycart) == 0)
			return false;
		
		foreach ($this->mycart as $cart){
			if ($cart['cartid'] == $cart_id)
				return $cart;
		}
		
		return false;
	}

	public function update_qty($cart_id, $qty){
		if (count($this->mycart) == 0)
			return;
		
		foreach ($this->mycart as $index => $cart){
			if ($cart['cartid'] == $cart_id){
				$this->mycart[$index]['qty'] = intval($qty);
				return true;
			}
		}
		return false;
	}

	public function find($product){
		$cart_id = '';
		if (count($this->mycart) == 0)
			return $cart_id;
		
		foreach ($this->mycart as $cart){
			if ($cart['id'] == $product['id']){
				$cart_id = $cart['cartid'];
				if (is_array($cart['options'])){
					foreach ($cart['options'] as $opt_name => $opt_value){
						if ($cart['options'][$opt_name] != $product['options'][$opt_value]){
							$cart_id = '';
							break;
						}
					}
				}
				break;
			}
		}
		return $cart_id;
	}
	
	public function put_tovar($product){
		$cart_id = $this->find($product);
		
		if ($cart_id == ''){
            if (!is_array($this->mycart))
				$this->mycart = array();
            
			array_unshift($this->mycart, $product);
			$this->mycart_index++;
		}
		else {
			$product_old = $this->get_tovar($cart_id);
			$this->update_qty($cart_id, $product_old['qty'] + $product['qty']);
		}
		
		$_SESSION[$this->mycart_session_name] = $this->mycart;
	}
	
	public function get_options_str($product){
		$result = '';
		if ($product['options']['display_type'] == 4){
			foreach ($product['options'] as $opt_name => $opt_value){
				if ($opt_name == 'display_type')
					continue;
				$attrib = ggo($opt_name, "#__expack_attrib");
				$attrib_val = ggo($opt_value, "#__expack_attrib_val");
				$result .= '<br />' . safelySqlStr($attrib->name) . " : " . $attrib_val->val;
			}
		}
		return $result;
	}
}
