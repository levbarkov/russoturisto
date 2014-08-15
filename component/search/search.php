<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class viewSearch {
	private $params = array();
	
	public function __construct(){
		$this->params['keywords'] 	= Api::$request->getParam('keywords', 'str', '');	
		$this->params['target'] 	= Api::$request->getParam('target', 'str', 'all');		
		$this->params['method'] 	= Api::$request->getParam('method', 'str', 'any');
	}
	
	private function actionSearch($keywords, $method = 'any', $target = 'all'){
		global $database, $reg;
		
		if(mb_strlen($keywords, 'utf-8') < 3)
			return array();
			
		if(!in_array($method, array('any', 'all', 'whole')))
			$method = 'any';
			
		if(!in_array($target, array('all', 'catalog')))
			$target = 'all';
		
		# КАТАЛОГ
		$where = array();
		if($method == 'whole'){
			$where []= "lower(a.name) like lower('%{$keywords}%')";
			$where []= "lower(a.sdesc) like lower('%{$keywords}%')";
			$where []= "lower(a.fdesc) like lower('%{$keywords}%')";
			$where = '(' . implode(') or (', $where) . ')';
		}
		else{
			$words = explode(' ', $keywords);
			foreach($words as $word){
				$wh = array();
				$wh[] 	= "lower(a.name) like lower('%{$word}%')";
				$wh[] 	= "lower(a.sdesc) like lower('%{$word}%')";
				$wh[] 	= "lower(a.fdesc) like lower('%{$word}%')";
				$where[] = implode(' or ', $wh );				
			}
			$where = '(' . implode(($method == 'all' ? ') and (' : ') or ('), $where) . ')';
		}
		
		# Поиск по товарам
		$sql = "select a.id as id, a.name as title, concat(a.sdesc, a.fdesc) as text, concat(a.sefnamefullcat, '/', a.sefname, '.html') as href, '2' as browsernav, 'Каталог / товары' as section, 'product' as type from #__exgood as a where a.publish = 1 and {$where} group by a.id";
		$database->setQuery($sql);
		$products = $database->loadObjectList();
		
		# Поиск по категориям
		$sql = "select a.id as id, a.name as title, concat(a.sdesc, a.fdesc) as text, concat(a.sefnamefull, '/', a.sefname) as href, '2' as browsernav, 'Каталог / категория' as section, 'category' as type from #__excat as a where a.publish = 1 and {$where} group by a.id";
		$database->setQuery($sql);
		$categories = $database->loadObjectList();		

		if($target == 'catalog')
			return array_merge($products, $categories);
			
		# СТАТИЧНОЕ СОДЕРЖИМОЕ И НОВОСТИ/СТАТЬИ
		$where = array();
		if($method == 'whole'){
			$where[] 	= "lower(a.title) like lower('%{$keywords}%')";
			$where[] 	= "lower(a.introtext) like lower('%{$keywords}%')";
			$where[] 	= "lower(a.fulltext) like lower('%{$keywords}%')";
			$where[] 	= "lower(a.metakey) like lower('%{$keywords}%')";
			$where[] 	= "lower(a.metadesc) like lower('%{$keywords}%')";
			$where = '(' . implode(') or (', $where) . ')';
		}
		else{
			$words = explode(' ', $keywords);
			foreach($words as $word){
				$wh = array();
				$wh[] 	= "lower(a.title) like lower('%{$word}%')";
				$wh[] 	= "lower(a.introtext) like lower('%{$word}%')";
				$wh[] 	= "lower(a.fulltext) like lower('%{$word}%')";
				$wh[] 	= "lower(a.metakey) like lower('%{$word}%')";
				$wh[] 	= "lower(a.metadesc) like lower('%{$word}%')";
				$where[] = implode(' or ', $wh );
			}
			$where = '(' . implode(($method == 'all' ? ') and (' : ') or ('), $where) . ')';
		}
				
		$content_name = $database->Quote($reg['content_name']);
		$user_gid = Api::$user->gid;
		$now = _CURRENT_SERVER_TIME;
		$nullDate = $database->Quote($database->getNullDate());
		
		$sql = "select
					a.id as id,
					a.title as title,
					a.created as created,
					concat(a.introtext, a.fulltext) as text,
					concat(a.sefnamefullcat, '/', a.sefname, '.html') as href,
					'2' as browsernav,
					{$content_name} as section,
					'content' as type,
					b.id as cat_id
				from
					#__content as a
					inner join #__icat as b on(b.id = a.catid)
				where
					a.state = 1 and
					b.publish = 1 and
					a.access <= {$user_gid} and
					(a.publish_up = {$nullDate} or a.publish_up <= '{$now}') and
					(a.publish_down = {$nullDate} or a.publish_down >= '{$now}') and
					{$where}
				group by
					a.id";
										
		$database->setQuery($sql);
		$contents = $database->loadObjectList();		
		
		$sql = "select
					a.id as id,
					a.title as title,
					a.created as created,
					a.introtext as text,
					concat('/', a.sefname) as href,
					'2' as browsernav,
					'Статичное содержимое' as section,
					'statcontent' as type
				from
					#__content as a
				where
					a.state = 1 and
					a.access <= {$user_gid} and
					a.catid = 0 and
					(a.publish_up = {$nullDate} or a.publish_up <= '{$now}') and
					(a.publish_down = {$nullDate} or a.publish_down >= '{$now}') and
					{$where}
				group by
					a.id";
					
		$database->setQuery($sql);
		$statcontents = $database->loadObjectList();	
					
		return array_merge($products, $categories, $contents, $statcontents);
	}
	
	private function mosLogSearch($keyword){
		global $database;
		
		$sql = "select cnt from #__stat_search where lower(search_term) = " . $database->Quote($keyword);
		$database->setQuery($sql);
		$hits = intval($database->loadResult());
		
		if ($hits){
			$sql = "update #__stat_search set `cnt` = (cnt + 1) where lower(search_term) = " . $database->Quote($keyword);
			$database->setQuery($sql);
			$database->query();
		}
		else {
			$sql = "insert into #__stat_search value (".$database->Quote( $keyword ).", 1)";
			$database->setQuery($sql);
			$database->query();
		}	
	}
	
	private function prepareContent($text, $length = 200, $keyword) {
		$text = preg_replace("~<script[^>]*>.*?</script>~si", '', $text);
		$text = preg_replace('~{.+?}~', '', $text);
		$text = preg_replace("~<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>~si", ' ', $text);
		$text = strip_tags($text);
		
		$wordpos = mb_strpos(mb_strtolower($text, 'utf-8'), mb_strtolower($keyword, 'utf-8'), 0, 'utf-8');

		$halfside = intval($wordpos - $length / 2 - mb_strlen($keyword, 'utf-8'));
		if ($wordpos && $halfside > 0) {
			$text = '...' . mb_substr($text, $halfside, $length, 'utf-8') . '...';
		}
		else {
			$text = mb_substr( $text, 0, $length, 'utf-8');
		}
 	
		return $text;
	}
	
	private function getCategoryBacktrace($category_id){
		$result = array();
		$category = ggsql("select id, name, parent from #__excat where `id` = {$category_id} and `publish` = 1 limit 1");
		if(!count($category))
			return false;
		
		$category = $category[0];
		
		$result[] = $category;
		if($category->parent != 0){
			$trace = $this->getCategoryBacktrace($category->parent);
			if($trace){
				$result = array_merge($result, $trace);
			}
		}
				
		return $result;		
	}
	
	public function fetchIndex(){
		$keywords = $this->params['keywords'];
		$keywords = trim(safelySqlStr(urldecode($keywords)));
		
		$target = array('catalog', 'all');
		foreach($target as $key)
			$checked[$key] = $this->params['target'] == $key ? 'checked="checked"' : '';
		
		$method = array('any', 'all', 'whole');
		foreach($method as $key)
			$checked['mode_' . $key] = $this->params['method'] == $key ? 'checked="checked"' : '';
		
		$html = <<<HTML
			<form action="/search/">
				<input type="radio" name="target" value="catalog" id="search_catalog" {$checked['catalog']} />
				<label for="search_catalog"><strong>Поиск по каталогу</strong></label>
				<br />
				<input type="radio" name="target" value="all" id="search_site" {$checked['all']} />
				<label for="search_site"><strong>Поиск по сайту</strong></label>
				<p>
					<span class="text">Поиск по ключевой фразе:</span>
					<input type="text" name="keywords" value="{$keywords}">
					<input type="submit" value="">
					<input type="radio" name="method" value="any" id="mode_any" {$checked['mode_any']} />
					<label for="mode_any">Любое слово</label>
					<input type="radio" name="method" value="all" id="mode_all" {$checked['mode_all']} />
					<label for="mode_all">Все слова</label>
					<input type="radio" name="method" value="whole" id="mode_phrase" {$checked['mode_whole']} />
					<label for="mode_phrase">Целую фразу</label>
				</p>
			</form>		
HTML;
		
		# Не более 200 символов
		if(mb_strlen($keywords, 'utf-8') > 200)
			$keywords = mb_substr($keywords, 0, 199, 'utf-8');
			
		if(mb_strlen($keywords, 'utf-8') > 3)
			$this->mosLogSearch($keywords);
					
		$results = $this->actionSearch($keywords, $this->params['method'], $this->params['target']);
		
		if(!count($results)){
			$html .= '<h5>По вашему запросу ничего не найдено.</h5>';
			echo $html;
			return;
		}
				
		$html .= '<div class="result">';
		$html .= '<ol>';
		foreach($results as $key => $result){
			$title = desafelysqlstr($result->title);
			$description = desafelysqlstr($result->text);
			
			if ($this->params['method'] == 'whole') {
				$words = array($keywords);
				$needle = $searchword;
				$text = $this->prepareContent($description, 333, $keywords);
			}
			else {
				$words = explode(' ', $keywords);
				$text = '';
				foreach ($words as $word)
					$text .= $this->prepareContent($description, 333, $word)."...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";			
			}
			
		  	foreach ($words as $word)
				$text = preg_replace('/' . preg_quote($word, '/') . '/iu', '<span class="highlight">\0</span>', $text);
				
		  	foreach ($words as $word)
				$title = preg_replace('/' . preg_quote($word, '/') . '/iu', '<span class="highlight">\0</span>', $title);
							
			
			$section = $result->section;
			if($result->type == 'product'){
				$product = ggo($result->id, '#__exgood');
				$categories = array_reverse($this->getCategoryBacktrace($product->parent));
				
				$category_names = array();
				foreach($categories as $category)
					$category_names []= desafelysqlstr($category->name);
					
				$section = implode(', ', $category_names);
			}
			
			$html .= <<<HTML
				<li>
					<p class="title">
					  <a href="{$result->href}">{$title}</a>
					  <span>({$section})</span>
					</p>
					<p class="text">
						{$text}
					</p>
				</li>				
HTML;
		}
		
		$html .= '</ol>';
		$html .= '</div>';

		echo $html;	
	}
		
}

$controller = new viewSearch();
echo '<section id="search">';
$controller->fetchIndex();
echo '</section>';
