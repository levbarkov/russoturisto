<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

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
 * ДОСТУП К ИНДИВИДУАЛЬНЫМ НАСТРОЙКАМ СОДЕРЖИМОГО
 * $reg['#__content_ID'.$reg['mainobj']->id.'__имя_переменной']
 *
 * ДОСТУП К ИНДИВИДУАЛЬНЫМ НАСТРОЙКАМ КАТЕГОРИИ
 * $reg['#__icat_ID'.$reg['mainobj']->id.'__имя_переменной']
 */

require_once('icontent_html.php');

$controller = new viewContent();
$controller->task();

class viewContent {
	private $_model_content  = '#__content';
	private $_model_rubric = '#__icat'; 
	private $_task;
	private $params;
	private $template;
	
	private $showAdminPanel = 0;	// Показывать кнопки редактирвания для администраторов сайта?
	
	public function __construct(){
		global $reg;
		
		$this->_task = $reg['task'];
		
		$this->template = new tplContent();
		
		$this->params['id'] = Api::$request->getParam('id', 'int', 0);
		$this->params['pop'] = Api::$request->getParam('pop', 'int', 0);
		$this->params['limit'] = Api::$request->getParam('limit', 'int', 100);		
		$this->params['limitstart'] = get_insite_limit_start($this->params['limit']);
	}
	
	public function task(){
		$task = $this->_task;
		if($task == 'view')
			$task = 'content';
		
		$method_name = 'fetch' . mb_ucfirst($task);
		if(method_exists($this, $method_name))
			$this->$method_name();
		else
			$this->fetchRubric();
	}
	
	public function fetchContent(){
		global $reg;
		
		$content = $reg['mainobj'];
		
		$component_foto = new component_foto( 0 );
		$component_foto->init('content');
		$component_foto->parent = $content->id;
			
		$component_file = new component_file( 0 );
		$component_file->init('content');
		$component_file->parent = $content->id;
		$component_file->load_files();
	
		# сохранение статистики
		$this->do_icat_stat_content($content);
	
		# отображаем кнопку редактирования товара и фото Yandex Google, отображается только для администраторов
		if($this->showAdminPanel == 1){
			editme('content', array('id' => $reg['mainobj']->id));
		}
	
		# ВЫВОДИМ СОДЕРЖИМОЕ
		$p->row = &$reg['mainobj'];
		$p->component_foto = &$component_foto;
		$p->component_file = &$component_file;
		
		$this->template->tpl('showContent', $p);
	}
	
	public function fetchRubric(){
		global $reg;
		
		if (!$reg['mainobj']->id)
			return;
		
		$icars = $reg['mainobj']; // здесь хранится просматриваемая рубрика, загруженна ядром в самом начале, чтобы постоянно не грузить одно и тоже
		
		$component_foto = new component_foto(0);
		$component_foto->init('icat');
		$component_foto->parent = $icars->id;
	
		# сохранение статистики
		$this->do_icat_stat_icat($icars);
		
		# отображаем кнопку редактирования, отображается только для администраторов
		if($this->showAdminPanel == 1){
			editme('Все рубрики', array('note' => 'Смотреть все рубрики сайта', 'img' => 'folder', 'id' => $icars->id)  );
			
			if ($reg['mainobj']->id)
				editme(  'icat', array('img' => 'editfolder', 'note' => 'Редактировать рубрику ' . $icars->name, 'id' => $icars->id)  );
			
			editme('Новая рубрика', array('img' => 'addfolder', 'note' => 'Добавить новую рубрику', 'id' => $icars->id)  );
			editme('icat_list', array('id' => $icars->id, 'note' => 'Редактировать все статьи рубрики')  );
			editme('icat_add_content', array('img' => 'quicklink', 'catid' => $icars->id, 'note' => 'Добавить новую статью/новость')  );
		}
		
		# ВЫВОДИМ РУБРИКИ
		if ($icars->dontautolist == 1){
			echo desafelySqlStr($icars->fdesc);
		}
		else{
			$rubrics = ggsql ("select * from {$this->_model_rubric} where `parent` = {$icars->id} order by `order` ");
			$p->rubrics = &$rubrics;
			$p->component_foto = &$component_foto;
			// var_dump ($p);
			$this->template->tpl('showRubrics', $p);
		}
	
		$where = array();
		$where[] = "`catid` = {$this->params['id']}";
		$where[] = "`state` > 0 ";

		#$where[] = "(`publish_up` = '0000-00-00 00:00:00' or `publish_up` < now() )";
		#$where[] = "(`publish_down` = '0000-00-00 00:00:00' or `publish_down` > now() )";
		
		$where 	= count($where) ? ' where ' . implode(' and ', $where) : '';
		
		$query = "select count(id) from {$this->_model_content} " . $where;
		$total = ggsqlr($query);
		if ($total <= $this->params['limit'])
			$this->params['limitstart'] = 0;
		
		$query = str_replace('count(id)', '*', $query) . ' order by `ordering` asc';
		$rows = ggsql($query, $this->params['limitstart'], $this->params['limit']);
		
		# формируем наш список НОВОСТЕЙ - СТАТЕЙ
		$p->rows 		= $rows;
		$p->total      	= $total;
		$p->limitstart 	= $this->params['limitstart'];
		$p->limit      	= $this->params['limit'];
		
		$component_foto->init('content');
		// var_dump ($p);
		$this->template->tpl('contentList', $p);
	}
	
	private function do_icat_stat_icat($thisicat){
		global $reg;
		if (ifipbaned())
			return;
		
		$sitelog = new sitelog();
		$sitelog->f[0] = $reg['c'];
		$sitelog->f[1] = "icat";
		$sitelog->f[2] = $thisicat->id;
		if ($sitelog->isnewlog()){
			$sitelog->desc = $sitelog->desc = $sitelog->get_description($thisicat, $this->_model_rubric, "parent", "", "", "Новости/статьи, просмотр рубрики: ");
		}
		$sitelog->savelog();
	}
	
	private function do_icat_stat_content($thiscontent){
		global $reg;
		if (ifipbaned())
			return;
		
		$sitelog = new sitelog();
		$sitelog->f[0] = $reg['c'];
		$sitelog->f[1] = "view";
		$sitelog->f[2] = $thiscontent->id;
		if ($sitelog->isnewlog()){
			$thisicat = ggo($thiscontent->catid, $this->_model_rubric);
			$sitelog->desc = $sitelog->desc = $sitelog->get_description($thisicat, $this->_model_rubric, "parent", "", "", "Новости/статьи, просмотр содержимого: ") . $reg['global_static_delimiter'] . $thiscontent->title;
		}
		$sitelog->savelog();
	}
	
}
