<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
/*
 * ПО УМОЛЧАНИЮ ССЫЛКА НА КАТАЛОГ НАЧИНАЕТСЯ С /catalogue/
 * ЕСЛИ НУЖНО ПОМЕНЯТЬ, ТО НЕОБХОДИМО В НАСТРОЙКАХ САЙТА ИЗМЕНИТЬ ЗНАЧЕНИЕ ПЕРЕМЕННОЙ 'ex_seoname'
 */
 
 /*
 *
 * КАЖДЫЙ ОБЪЕКТ - НОВОСТЬ, СТАТЬЯ ИЛИ РУБРИКА ТЕПЕРЬ ИМЕЕТ ИНДИВИДУАЛЬНЫЙ НАСТРОЙКИ
 * НАПРИМЕР ДЛЯ КАКОЙ-ТО ОПРЕДЕЛЕННОЙ СТАТЬИ ВЫ МОЖЕТЕ ПРОПИСАТЬ ДРУГИЕ РАЗМЕРЫ ФОТО 
 * ИЛИ ВВЕСТИ НОВУЮ ПЕРЕМЕННУЮ, УКАЗЫВАЮЩУЮ НА КАКОЕ-ТО ЕЕ ОПРЕДЕЛЕННОЕ СОСТОЯНИЕ.
 *
 * ИНДИВИДУАЛЬНЫЕ НАСТРОЙКИ ДОСТУПНЫ В АДМИНКЕ ВО ВКЛАДКЕ "ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ"
 * ИНДИВИДУАЛЬНЫЕ НАСТРОЙКИ ПРЕДСТАВЛЯЮТ ИЗ СЕБЯ ОБЫЧНУЮ ПЕРЕМЕНУЮ РЕЕСТРА С ПРЕФИКСОМ #__sql_таблица_ID###_НАЗВАНИЕ_ПЕРЕМЕННОЙ
 * ### - id ОБЪЕКТА
 * ТАКИМ ОБРАЗОМ ДОСТУП К ДАННОЙ НАСТРОЙКЕ В КОДЕ ВЫГЛЯДЕТ СЛЕДУЮЩИМ ОБРАЗОМ $reg['#__sql_таблица_ID###_НАЗВАНИЕ_ПЕРЕМЕННОЙ']
 * ПРИМЕРЫ ИСПОЛЬЗОВАНИЯ ИНДИВИДУАЛЬНЫХ НАСТРОЕК МОЖНО ПОСМОТРЕТЬ В РУБРИКЕ СТАТЕЙ/НОВОСТЕЙ - "ПРОВЕРКИ-УДАЛИТЬ"
 * И В СТАТЬЕ "Текст для проверки" В УКАЗАННОЙ ВЫШЕ РУБРИКЕ.
 * 
 */

/*
 * ДОСТУП К ИНДИВИДУАЛЬНЫМ НАСТРОЙКАМ ТОВАРА
 * $reg['#__exgood_ID'.$reg['mainobj']->id.'__имя_переменной']
 *
 * ДОСТУП К ИНДИВИДУАЛЬНЫМ НАСТРОЙКАМ КАТЕГОРИИ
 * $reg['#__excat_ID'.$reg['mainobj']->id.'__имя_переменной']
 */

require_once( 'ex_lib.php' );
require_once( 'ex_html.php' );
require_once(site_path."/lib/saver.php");

$controller = new viewCatalog();
$controller->task();

class viewCatalog {
	private $_model_product  = '#__exgood';
	private $_model_category = '#__excat'; 
	private $_task;
	
	private $template;
	
	private $showAdminPanel = 0;	// Показывать кнопки редактирвания для администраторов сайта?	
	private $params = array();
	
	public function __construct(){
		global $reg;
		
		$this->_task = $reg['task'];
		
		$this->template = new tplCatalog();
		
		$this->params['id'] = Api::$request->getParam('id', 'int', 0);
		$this->params['print_version'] = Api::$request->getParam('pop', 'int', 0);
		$this->params['limit'] = Api::$request->getParam('limit', 'int', 50);		
		$this->params['limitstart'] = get_insite_limit_start($this->params['limit']);
	}
	
	public function task(){
		$task = $this->_task;
		if($task == 'view')
			$task = 'product';
		
		$method_name = 'fetch' . mb_ucfirst($task);
		if(method_exists($this, $method_name))
			$this->$method_name();
		else
			$this->fetchCatalog();
	}
	
	
	public function fetchVisa()
	{
		global $reg;
		
		
		$rows = ggsql(" SELECT good.sefnamefullcat, good.sefname, good.name FROM #__exgood as good, #__excat as cat WHERE good.publish = 1 AND (cat.parent='7' AND good.parent = cat.id) ORDER BY good.name; ");
		
		?>
		
		<div class="iframe visa unl">
			<h1>Документы необходимые для оформления визы</h1>
			<? foreach($rows as $row){ echo "<a href='{$row->sefnamefullcat}/{$row->sefname}.html'>{$row->name}</a> \n"; } ?>
			<div class="clear"></div>
		</div>
		
		<?
	}
	
	public function fetchNewProducts($p){
		global $reg;
		
		$query = "select a.*, b.name as expack_name, b.id as expack_id, b.sku as expack_sku, c.cy as exprice_cy, c.val as exprice_val
				from #__exgood as a
				inner join #__expack as b on (b.parent = a.id)
				inner join #__exprice_good as c on (b.parent = {$p->price_parent}  and  b.id = c.expack)
				where a.small <> ''
				group by a.id
				order by a.name asc";

		$p->rows = ggsql( $query, 0, 5 );
		
		if(!count($p->rows))
			return false;
	
		// формируем список товаров
		//echo '<span class="cnt-emed">Новинки</span>';
		//$this->template->tpl('productsList', $p);	
	}
	
	public function fetchProduct(){
		global $reg;
		
		$p->price_parent = 1;
					
		$exgood = new exgood();
		$exgood->vars = &$reg['mainobj'];
		$exgood->id   = $reg['mainobj']->id;
		$exgood->expack_set = $reg['mainobj']->expack_set;
		$exgood->expack_select_type = $reg['mainobj']->expack_select_type;
			
		$component_foto = new component_foto(0);
		$component_foto->init('exgood');
		$component_foto->parent = $exgood->vars->id;
	
		$component_file = new component_file(0);
		$component_file->init('exgood');
		$component_file->parent = $exgood->vars->id;
		$component_file->load_files();
		
		$names = new names($exgood->id, 'exgood', $reg);
		$nlist =  $names->get();
		
		$exgood->expack = new expack();
		$exgood->expack->load ($exgood);
	
		# сохранение статистики
		$this->statProduct($exgood->vars);
	
		# отображаем кнопку редактирования товара и фото Yandex Google, отображается только дял администраторов
		if($this->showAdminPanel == 1){
			YandexGoogleFoto(desafelySqlStr($reg['mainobj']->name));
			editme('exgood', array('id' => $reg['mainobj']->id));
		}
	
		# получение списка рекомендованных товаров
		$sql_vars = "   exgood.id               	as exgood_id,
						exgood.name             	as exgood_name,
						exgood.sefname          	as exgood_sefname,
						exgood.sefnamefullcat   	as exgood_sefnamefullcat,
						exgood.sdesc            	as exgood_sdesc,
						exgood.fdesc            	as exgood_fdesc,
						exgood.small            	as exgood_small,
						exgood.org              	as exgood_org,
						exgood.brand            	as exgood_brand,
						exgood.expack_select_type   as exgood_expack_select_type,
						exgood.expack_set       	as exgood_expack_set,
	
						expack.id               	as expack_id,
						expack.name             	as expack_name,
						expack.sku              	as expack_sku,
	
						exprice.val        			as exprice_val,
						exprice.cy         			as exprice_cy
					";
			
		$query = "SELECT {$sql_vars} FROM #__exgood as exgood
				  INNER JOIN #__exrecommended as exrecommended ON ( exrecommended.parent = {$exgood->id} AND exrecommended.recommended=exgood.id  )
				  LEFT  JOIN #__expack as expack ON (expack.parent = exgood.id)
				  LEFT  JOIN #__exprice_good as exprice ON (  exprice.parent = {$p->price_parent}  AND  expack.id = exprice.expack  )
				  GROUP BY(exgood.id)
				 ";
		$p->recomended = ggsql ( $query );
	
		# с этим товаром покупают
		$rel = new relation($reg);
		$arr = $rel->get($exgood->id);
		if ($arr != false){
			$with_goods = implode(', ', $arr);
			$query = "SELECT {$sql_vars} FROM #__exgood as exgood
					  LEFT  JOIN #__expack as expack ON (expack.parent = exgood.id)
					  LEFT  JOIN #__exprice_good as exprice ON (  exprice.parent = {$p->price_parent}  AND  expack.id = exprice.expack  )
					  WHERE exgood.id IN ({$with_goods})
					  GROUP BY(exgood.id)
					 ";
					 
			$p->buy_with = ggsql ( $query );
		}
	
		#  показываем сам товар
		$p->component_foto  = &$component_foto;
		$p->component_file  = &$component_file;
		$p->exgood          = &$exgood;
		$p->nlist           = &$nlist;
		$p->names           = &$names;
		
		$this->template->tpl('showProduct', $p);
	}
	
	public function fetchCatalog(){
		global $mainframe, $reg;
				
		$p->limit      		= $this->params['limit'];
		$p->limitstart 		= $this->params['limitstart'];
		$p->price_parent 	= 1; // Прайс лист по ценам из ПРАЙСА с ID=1
	
		if ($this->params['id'] > 0)
			$p->icars = $reg['mainobj'];
		else{
			$p->icars->id = 0;
			$p->icars->name = $reg['ex_name'];
		}
		
		# СОХРАНЕНИЕ СТАТИСТИКИ
		$this->statCategory($p->icars);
				
		# КНОПКА РЕДАКТИРОВАТЬ СОДЕРЖИМОЕ - ОТОБРАЖАЕТСЯ ТОЛЬКО ДЛЯ АДМИНИСТРАТОРОВ САЙТА
		if ($reg['mainobj']->id && $this->showAdminPanel == 1) {
			editme(  'все категории каталога', array('note'=>'Смотреть все категории каталога', 'img'=>'folder')  );
			editme(  'excat', array('note'=>'Редактировать категорию '.$reg['mainobj']->name, 'id'=>$reg['mainobj']->id, 'img'=>'editfolder')  );
			editme(  'Добавить подкатегорию', array('note'=>'Добавить подкатегорию', 'parent'=>$reg['mainobj']->id, 'img'=>'addfolder')  );
			editme(  'excat_list', array('id'=>$reg['mainobj']->id, 'note'=>'Редактировать товары категории')  );
			editme(  'Добавить товар', array('parent'=>$reg['mainobj']->id, 'note'=>'Добавить товар', 'img'=>'quicklink')  );
		}
		
	
		// ВЫВОДИМ СПИСОК ВЛОЖЕННЫХ КАТЕГОРИИ
		$p->rows = ggsql ("select * from #__excat where `parent`={$p->icars->id} order by `order` ");
		$p->icats_per_row = 1;
		
		$this->template->tpl('categoryList', $p);
	
		# ФОРМА ПОИСКА
		$this->template->tpl('searchFrom', $p);
	
		# Главная страница каталога
		if ($_SERVER['REQUEST_URI'] == '/' . $reg['ex_seoname'] || $_SERVER['REQUEST_URI'] == '/' . $reg['ex_seoname'] . '/'){
			$this->fetchNewProducts($p); //новинки
			//show_recent_purchased ( $p ); // последние купленные, в файле ex_lib.php
			return;
		}
	
		$where = '';
		$join = '';
		$params = array();
		$link_params = array();
	
		foreach ($_REQUEST as $k => $v) {
			if ($v == '') continue;
			$matches = array();
			if (preg_match('/^group_id_(\d+)$/', $k, $matches)) {
				$link_params[$k] = $v;
				$k = intval($matches[1], 10);
				if (is_array($v)) {
					$params[$k] = $v;
				}
				else {
					$v = intval($v, 10);
					$params[$k] = array($v);
				}
			}
			elseif ($k == 'price_from') {
				$link_params[$k] = $v;
				$v = intval($v, 10);
				$where .= " and c.val >= {$v}";
			}
			elseif ($k == 'price_till') {
				$link_params[$k] = $v;
				$v = intval($v, 10);
				$where .= " and c.val <= {$v}";
			}
			elseif ($k == 'name'){
				$link_params[$k] = $v;
				$v = urldecode($v);
				$where .= " and a.name like '%{$v}%' ";
			}
			elseif ($k == 'brand'){
				$link_params[$k] = $v;
				$v = intval($v);
				$join .= "inner join #__names_parent as br on (br.parent = a.id  and  br.nameid = {$v}) ";
			}
			elseif (preg_match('/^group_id_(\d+)_from$/', $k, $matches)) {
				$link_params[$k] = $v;
				$k = intval($matches[1], 10);
				$v = intval($v, 10);
				if (!isset($params[$k])) $params[$k] = array('from' => $v, 'till' => '');
				else $params[$k]['from'] = $v;
			}
			elseif (preg_match('/^group_id_(\d+)_till$/', $k, $matches)) {
				$link_params[$k] = $v;
				$k = intval($matches[1], 10);
				$v = intval($v, 10);
				if (!isset($params[$k])) $params[$k] = array('from' => '', 'till' => $v);
				else $params[$k]['till'] = $v;
			}
		}
		$p->sign = $link_params;
		$i = 0;
		foreach ($params as $id => $param) {
			$i++;
			if (sizeof($param) == 2 && isset($param['from'])) {
				$on = '';
				if ($param['from'] != '') $on = " and val{$i}.val >= {$param['from']}";
				if ($param['till'] != '') $on = " and val{$i}.val <= {$param['to']}";
				$join .= "
				 inner join #__expack_set_val set{$i} ON (set{$i}.pack_id = b.id and set{$i}.attrib={$id})
				 inner join #__expack_attrib_val val{$i} ON (val{$i}.id = set{$i}.attrib_val {$on})";
			}
			else {
				if (sizeof($param) == 1) $on = ' = ' . $param[0];
				else $on = ' in(' . implode(',', $param) . ')';
				$join .= " inner join #__expack_set_val set{$i} ON (set{$i}.pack_id = b.id and set{$i}.attrib_val {$on} and set{$i}.attrib={$id})";
			}
		}
		
		if (!isset($_REQUEST['sort']))
			$_REQUEST['sort'] = 'order-up';
			
		$sorts = array(
			'order-up' => 'order by a.order asc',
			'name-up' => 'order by a.name asc',
			'name-down' => 'order by a.name desc',
			'price-up' => 'order by c.val asc',
			'price-down' => 'order by c.val desc',
		);
		
		if (!in_array($_REQUEST['sort'], array_keys($sorts)))
			$_REQUEST['sort'] = 'name-up';
		
		# получаем список всех категорий и подкатегорий
		if ($reg['mainobj']->id) {
			$allcats = $this->getAllSections($reg['mainobj']->id);
			$allcats[] = $p->icars->id;
			$ids = implode(", ", $allcats);
			$where .= " and a.parent in ({$ids})";
		}
	
		$query = "SELECT {select} FROM #__exgood a
		inner join #__expack b ON (b.parent = a.id)
		inner join #__exprice_good c ON (c.parent = {$p->price_parent} AND b.id = c.expack)
		{$join}
		where a.publish = 1 {$where}
		{group_order}";
			
		$p->total = ggsqlr(str_replace('{group_order}', '', str_replace('{select}', 'count(*)', $query)));
		
		if (isset($_REQUEST['showall']) && $_REQUEST['showall'] = 1) {
			$p->limit = $p->total;
			$p->limitstart = 0;
		}
		elseif ($p->total <= $p->limit)
			$p->limitstart = 0;
	
		$query = str_replace('{group_order}', "group by a.id {$sorts[$_REQUEST['sort']]}", str_replace('{select}', 'a.*, b.name as expack_name, b.id as expack_id, b.sku as expack_sku, c.cy as exprice_cy, c.val as exprice_val', $query));
		
		$p->rows = ggsql($query, $p->limitstart, $p->limit);
						
		require_once( site_path . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $p->total, $p->limitstart, $p->limit  );
		$pageNav->sign = $p->sign;
		$pageNav->sign['sort'] = $_REQUEST['sort'];
		
		# формируем список товаров
		$this->template->tpl('productsList', $p);
		
		echo $pageNav->getListFooter();		
	}
	
	public function fetchViewtrush(){
		global $reg;
		
		$id = $this->params['id'];
		$user_id = Api::$user->id;
		$mycart_task = Api::$request->getParam('mycart_task', 'str', '');
		
		switch($mycart_task){
			case 'submit_order':
				# ЗАКАЗ СДЕЛАН - СОХРАНЯЕМ И ОТПРАВЛЯЕМ УВЕДОМЛЕНИЯ
				submit_order($id); return;
				break;
			case 'order_register':
				# РЕГИСТРИРУЕМ НОВОГО ПОЛЬЗОВАТЕЛЯ
				if (ggri('show_order_register_me')){
					$_REQUEST['task'] = 'saveRegistration';
					$_REQUEST['script_mode'] = 'html2script';
					$_REQUEST['register_from_order'] = 1;
					$_REQUEST['register_ok'] = 0;
					require_once(site_path . '/component/reg/reg.php' );
					$_REQUEST['task'] = $reg['task'];
					if ($_REQUEST['register_ok'] == 1)
						return;
				}				
				break;
			case 'blank':
				payment_bank_bank($id); return;
				break;
			default:
				
		}
		
		$show_order = Api::$request->getParam('show_order', 'str', '');		
		$show_trash = in_array($show_order, array('order', 'order_register_form', 'order_contact_form', 'thank')) ? false : true;
	
		// <div id='mycart_allcart' style='width:{$reg['shop_cart_w']}px; display: table; padding: 10px;'>
		echo "<table id='mycart_allcart' width='{$reg['shop_cart_w']}'><tr><td>";
		if(ggri('floating') == 1){
			$title = 'Корзина заказов';
			if($show_order == 'order')
				$title = 'Оформление заказа';
			elseif($show_order == 'order_register_form')
				$title = 'Регистрация на сайте и оформление заказа';
			elseif($show_order == 'order_contact_form')
				$title = ggri('show_order_register_me') == 1 ? 'Оформление заказа' : 'Быстрое оформление заказа без регистрации';
			elseif($show_order == 'thank'){
				$title = 'Заказ оформлен';
			}
			
			echo <<<HTML
				<table cellspacing="0" cellpadding="0" border="0" align="left" class="insite_ajax_form_table">
					<tr height="5">
						<th align="left" width="30%" style="text-align: left;"></th>
					</tr>
					<tr height="20">
						<th align="left" style="text-align: left;" >{$title}</th>
					</tr>
					<tr height="10">
						<td></td>
					</tr>
					<tr>
						<td></td>
					</tr>
				</table>
				<div style="clear:both"></div>			
HTML;
		}
					
		# ОТОБРАЖАЕМ КОРЗИНУ - СПИСОК КУПЛЕННЫХ ТОВАРОВ
		if ($show_trash)
			lib_show_trush_list ();
			
		/*
		 * ВЫБОР ОФОРМЛЕНИЯ ЗАКАЗА:
		 * - С РЕГИСТРАЦИЕЙ ИЛИ
		 * - БЫСТРОЕ ОФОРМЛЕНИЕ БЕЗ РЕГИСТРАЦИИ
		 */
		if ($show_order == 'order')
			lib_select_order_type();
			
		/*
		 * РЕГИСТРИРУЕМ НОВОГО ПОЛЬЗОВАТЕЛЯ - ФОРМА
		 */
		if ($show_order == 'order_register_form')
			lib_order_register_form();

		/*
		 * КОНТАКТНАЯ ИНФОРМАЦИЯ ДЛЯ ЗАКАЗА - ФОРМА
		 */
		if ($show_order == 'order_contact_form')
			lib_order_contact_form();

		/*
		 * вывод благодарственного письма в конце после покупки
		 */
		if ($show_order == 'thank')
			thank();
		
		echo '</td></tr></table>';			
	}
	
	public function fetchShow_attribs(){
		$exgood = new exgood ();
		$exgood->id = ggri('good');
		$exgood->load_me();
		$exgood->expack_select_type = $exgood->vars->expack_select_type;
		$exgood->expack_set = $exgood->vars->expack_set;
		$exgood->show_attribs();		
	}
	
	public function fetchExcomp(){
		global $reg;
		
		$user_id = Api::$user->id;
		
		if($user_id == 0)
			return false;
		
		$mylist = new mylist();
		$mylist = $mylist->get_list($user_id, 'ex');
		$mylist_count = count($mylist);
		$cwidth = round (100 / ($mylist_count));
		
		if(!count($mylist)){
			echo '<p>Список избранных товаров пуст.</p>';
			return false;
		}
				
		$ids = array();
		foreach($mylist as $excomp)
			$ids []= $excomp->parent;
			
		$ids = implode(',', $ids);
		
		$excomp_html = '';
		$products = ggsql("select * from #__exgood where id in ({$ids}) and `publish` = 1 order by `order`");
		if(count($products)){
			$excomp_html = '<ul>';
			foreach($products as $product){
				$url = $product->sefnamefullcat . '/' . $product->sefname . '.html';
				$name = desafelysqlstr($product->name);
				$excomp_html .= <<<HTML
					<li id="arrayorder_{$product->id}"><a href="{$url}" class="mylist_link">{$this->params['id']} {$name}</a><div class="clear"></div>
						<img style="border: 6px solid #F0F2F4; " src="/images/ex/good/{$product->small}" />
						<div class="clear"></div>
					</li>				
HTML;
			}
			$excomp_html .= '</ul>';
		}
		
		echo <<<HTML
			<script type="text/javascript">
				$(document).ready(function(){
					$("#list ul").sortable({
						opacity: 0.3,
						cursor: 'move',
						update: function() {
							var order = $(this).sortable("serialize") + '&4ajax_module=mylist&mylist_task=update_order&mylist_comp=ex';
							$.post("/index.php", order, function(theResponse){
								// $("#response").html(theResponse);
								// $("#response").slideDown('slow');
								// slideout();
							}); 						
						}
				});
			</script>
			<div id="container">
				<div id="list">
					<div id="response" style="display:none"></div>
					{$excomp_html}
				</div>
			</div>
		
HTML;
	}
	
	private function updateProducts($category_id){
		global $database;
		$i24r = new mosDBTable("#__excat", "id", $database);
		$i24r->id = $category_id;
		$i24r->goods = ggsqlr("select count(id) from #__exgood where `parent` = {$category_id}");
		$i24r->store();
		return true;		
	}
	
	private function getAllSections($parent){
		global $reg;
		
		if (!$parent)
			return;
		
		$db = &$reg['db'];
	
		$db->setQuery("select * from #__excat where `parent` = {$parent} and `publish` = 1");
		$db->query();
		if($db->getNumRows() > 0){
			$sect = $db->loadResultArray();
			$temp = Array();
			foreach($sect as $s) {
				$tmp = $this->getAllSections($s);
				if(is_array($tmp))
					$temp = array_merge($temp, $tmp);
			}
			if(is_array($temp))
				$sect = array_merge($sect, $temp);
			return $sect;
		}
		
		return false;		
	}
	
	
	private function statCategory($category){
		global $reg;
		if (ifipbaned())
			return;
		
		$sitelog = new sitelog();
		$sitelog->f[0] = $reg['c'];
		$sitelog->f[1] = "excat";
		$sitelog->f[2] = $category->id;
		if ($sitelog->isnewlog()){
			$sitelog->desc = $sitelog->desc = $sitelog->get_description($category, "#__excat", "parent", "/".$reg['ex_seoname'], $reg['ex_name'], $reg['ex_name'].", просмотр категории: ");
		}
		$sitelog->savelog();
	}
	
	private function statProduct($product){
		global $reg;
		if (ifipbaned())
			return;
		
		if($reg["exgoodAllowTags"] == 1){
			$tag = new tags("exgood", $reg['db'], $reg);
			$tag->view($product->id);
		}
		
		$sitelog = new sitelog();
		$sitelog->f[0] = $reg['c'];
		$sitelog->f[1] = "view";
		$sitelog->f[2] = $product->id;
		if ($sitelog->isnewlog()){
			$category = ggo($product->parent, "#__excat");
			$sitelog->desc = $sitelog->desc = $sitelog->get_description($category, "#__excat", "parent", "/".$reg['ex_seoname'], $reg['ex_name'], $reg['ex_name'].", просмотр товара: ") . $reg['global_static_delimiter'] . desafelySqlStr($product->name);
		}
		$sitelog->savelog();
	}	
}
