<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

global $task;

$controller = new AdminFAQ();
$controller->route($task);

class AdminFAQ {
    private $_component = 'faq';
    private $_model     = '#__faq';
    private $_task;
	
    public function __construct(){
        
    }
	
    public function route($task){
        $this->_task = $task;
		        
        $method_name = 'action' . ucfirst($task);
        if(method_exists($this, $method_name)){
            return $this->$method_name();
        }
        
        $this->actionIndex();        
    }
	
    public function actionIndex(){
        global $database, $iConfig_list_limit, $reg;
        
        require_once( site_path . '/iadmin/includes/pageNavigation.php' );
    
        $filter_type	= getUserStateFromRequest('filter_type', 0);
        $filter_logged	= intval( getUserStateFromRequest('filter_logged', 0));
        $limit 			= intval( getUserStateFromRequest('limit', $iConfig_list_limit));
        $limitstart 	= intval( getUserStateFromRequest('limitstart', 0));
        
        $total = ggsqlr("select count(*) from {$this->_model}");
        
        $pageNav = new mosPageNav($total, $limitstart, $limit);
        
        # Pathway
		$iway[0] = $iway[1] = new stdClass();
        $iway[0]->name = $reg[$this->_component . '_name'];
        $iway[0]->url  = "index2.php?ca={$this->_component}&task=view";
        $iway[1]->name = "Список ";
        $iway[1]->url  = "";
        
        i24pwprint_admin ($iway);
        
        # инициализация класса необходимого для перемящаемой таблицы
        $table_drug         = new ajax_table_drug();
        $table_drug->id     = "ajax_table_drug_td";
        $table_drug->table  = $this->_model;
        $table_drug->order  = "order";
        
        $table_attr = $table_drug->table();
        $tr_attr    = $table_drug->row();
        
        $paginator = $pageNav->getListFooter();
        
        $rows = ggsql("select * from {$this->_model} order by `order` asc", $limitstart, $limit);
        $count = count($rows) - 1;
        
        $rows_html = '';
        foreach($rows as $key => $row){
            $pp = $key + 1;
            
            $url = "index2.php?ca={$this->_component}&task=edit&id={$row->id}&hidemainmenu=1&search={$_REQUEST['search']}&filter_type={$filter_type}&filter_logged={$filter_logged}";
            $tr_cls = $table_drug->row($row->id, $row->order);
            
            $checkbox = mosHTML::idBox($key, $row->id);
            
            $name 		= desafelysqlstr($row->name);
            $question 	= desafelysqlstr($row->question);
			$answer 	= desafelysqlstr($row->answer);
			$publish 	= $row->publish == 1 ? '<b>Да</b>' : 'Нет';
			            
            $rows_html .= <<<HTML
                <tr {$tr_cls} class="row{$k}">
                    <td>{$pp}</td>
                    <td>{$checkbox}</td>
                    <td align="left"><a href="{$url}">{$name}</a></td>
                    <td>{$question}</td>
					<td>{$answer}</td>
					<td>{$publish}</td>
                    <td align="center" class="dragHandle drugme">&nbsp;</td>
                    <td align="center">
                        <input type="text" name="order[]" size="5" value="{$row->order}" class="text_area" style="text-align: center" />
                        <input type="hidden" name="{$this->_component}id[]" value="{$row->id}" />
                    </td>               
                </tr>
HTML;
        }
              
        $html = <<<HTML
            <form action="index2.php" method="post" name="adminForm">  
                <table class="adminlist" {$table_attr} >
                    <tr {$tr_attr} >
                        <th width="2%" class="title" class="dragHandle">#</th>
                        <th width="3%" class="title" class="dragHandle">
                            <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" />
                        </th>
                        <th width="20%" class="title" class="dragHandle">Имя пользователя</th>
						<th width="30%" class="title">Вопрос</th>
                        <th width="30%" class="title">Ответ</th>
						<th width="10%">Опубликован?</th>
                        <th width="10%" align="center" width="5%">Сортировка</th>
                        <th>
                            <a href="javascript: saveorder({$count})" onmouseover="return Tip('Сохранить заданный порядок отображения');">Сохранить порядок</a>
                        </th>
                    </tr>
                    {$rows_html}
                </table>
                {$paginator}
                <input type="hidden" name="ca" value="{$this->_component}" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="hidemainmenu" value="0" />             
            </form>
        
HTML;
        echo $html;       
    }
	
	public function actionUnblock(){
		global $database;
		$cid = josGetArrayInts('cid');
		if (count($cid) < 1) {
			echo "<script type=\"text/javascript\"> alert('Выберите объект для разблокировки'); window.history.go(-1);</script>\n";
			exit;
		}
		$cids = implode(', ', $cid);
		$sql = "update {$this->_model} set `publish` = 1 where `id` in ({$cids})";
		$database->setQuery($sql);
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		mosRedirect('index2.php?ca=' . $this->_component . '&task=view&limit=' . $_REQUEST['limit'] . '&limitstart=' . $_REQUEST['limitstart']);	
	}
	
	public function actionBlock(){
		global $database;
		$cid = josGetArrayInts('cid');
		if (count($cid) < 1) {
			echo "<script type=\"text/javascript\"> alert('Выберите объект для блокировки'); window.history.go(-1);</script>\n";
			exit;
		}
		$cids = implode(', ', $cid);
		$sql = "update {$this->_model} set `publish` = 0 where `id` in ({$cids})";
		$database->setQuery($sql);
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		mosRedirect('index2.php?ca=' . $this->_component . '&task=view&limit=' . $_REQUEST['limit'] . '&limitstart=' . $_REQUEST['limitstart']);				
	}
		
	public function actionSave() {
		global $database, $reg;
	
		$i24r = new mosDBTable($this->_model, "id", $database );
		$i24r->id = safelySqlInt($_REQUEST['id']);
		$i24r->name = safelySqlStr($_REQUEST['name']);
		$i24r->question = safelySqlStr($_REQUEST['question']);
		$i24r->answer = safelySqlStr($_REQUEST['answer']);
		$i24r->publish = !empty($_REQUEST['publish']) ? 1 : 0;
		if ($i24r->id == 0){
			$maxorder = ggsqlr("select max(`order`) from {$this->_model}");
			$i24r->order = $maxorder + 1;
		}
		if (!$i24r->check())
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		else
			$i24r->store();
		
		$adminlog = new adminlog();	
		if ($i24r->id == 0)
			$adminlog->logme('new', $reg[$this->_component . '_name'], $i24r->name, $i24r->id );
		else
			$adminlog->logme('save', $reg[$this->_component . '_name'], $i24r->name, $i24r->id );
	
		$msg = 'Сохранено: '. $i24r->name;
		mosRedirect("index2.php?ca={$this->_component}", $msg);
	}
	
	public function actionEdit($id = 0){
		global $database, $mainframe, $reg;		
		$id = intval(getUserStateFromRequest('id', $id));
			
		if ($id > 0)
			$row = ggo ($id, $this->_model);
		else{
			$row->id = 0;
			$row->name = '';
			$row->question = '';
			$row->answer = '';
			$row->publish = 0;
		}
			
		$iway[0]->name = $reg[$this->_component . '_name'];
		$iway[0]->url  = '';
		$iway[1]->name = $row->id ? 'Редактирование' : 'Новый';
		$iway[1]->url  = '';			
			
		i24pwprint_admin ($iway, 0);
		
		$publish_check = $row->publish ? 'checked="checked"' : '';
		
		$html = <<<HTML
			<form action="index2.php" name="adminForm" method="post">
				<input type="hidden" name="iuse" id="iuse" value="0" />
				<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
					<tr class="workspace">
						<td>Имя пользователя: </td>
						<td><input name="name" size="120" mosreq="1" moslabel="Название" value="{$row->name}" /></td>				
					</tr>
					<tr class="workspace">
						<td>Вопрос: </td>
						<td><textarea name="question" cols="86" rows="10">{$row->question}</textarea></td>
					</tr>
					<tr class="workspace">
						<td>Ответ: </td>
						<td><textarea name="answer" cols="86" rows="10">{$row->answer}</textarea></td>
					</tr>
					<tr class="workspace">
						<td>Показывать на сайте: </td>
						<td><input type="checkbox" name="publish" value="1" {$publish_check}/></td>
					</tr>
				</table>
				<input type="hidden" name="id" value="{$row->id}" />
				<input type="hidden" name="task" value="save"  />
				<input type="hidden" name="ca" value="{$this->_component}" />
			</form>
HTML;
			
		echo $html;	
					
	}
	
	public function actionRemove() {
		global $database, $my, $reg;		
		$objects = $_REQUEST['cid'];
		
		foreach ($objects as $object){
			$object = intval($object);
			$adminlog_obj = ggo($object, $this->_model);
			$adminlog = new adminlog();
			$adminlog->logme('del', $reg[$this->_component . '_name'], $adminlog_obj->v1, $adminlog_obj->id );
			ggsqlq ("delete from {$this->_model} where `id` = {$object}");
		}
		
		$msg = 'Объект(ы) удалены: ';
		mosRedirect("index2.php?ca={$this->_component}", $msg);
	}
	
	public function actionCfg(){
		global $reg;
		load_adminclass('config');
		
		$conf = new config($reg['db']);
		
		echo '<form name="adminForm" action="index2.php" method="post">';
		echo '<input type="hidden" name="iuse" id="iuse" value="0" />';
		
		$conf->show_config($this->_component, "Настройки / " . $reg[$this->_component . '_name']);
		
		echo <<<HTML
			<input type="hidden" name="task" value="savecfg"  />
			<input type="hidden" name="ca" value="{$this->_component}" />
			<input type="submit" style="display:none;"/>			
HTML;
		echo '</form>';	
	}
	
	public function actionSavecfg(){
		global $reg;
		load_adminclass('config');
		
		$conf = new config($reg['db']);
		$conf->save_config();
		
		$adminlog = new adminlog();
		$adminlog->logme('cfg', $reg[$this->_component . '_name'], "", "" );
		
		mosRedirect("index2.php?ca={$this->_component}&task=cfg", 'Настройки сохранены');		
	}
	
	public function actionRemovecfg(){
		global $reg;
		load_adminclass('config');
		
		$conf = new config($reg['db']);
		$conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
		
		$adminlog = new adminlog();
		$adminlog->logme('delcfg', $reg[$this->_component . '_name'], '', '');
		
		mosRedirect("index2.php?ca={$this->_component}&task=cfg", 'Настройки удалены');	
	}
	
	public function actionOrderup() {
		global $database;
		
		$current = ggo($_REQUEST['cid'][0], $this->_model);
		$up		 = ggsql("select * from {$this->_model} where `order` < {$current->order} order by `order` desc limit 0, 1");		
		$up = $up[0];
		
		$i24r = new mosDBTable($this->_model, "id", $database );
		$i24r->id 	 = $_REQUEST['cid'][0];
		$i24r->order = $up->order;
		if (!$i24r->check())
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		else
			$i24r->store();
			
		$i24r = new mosDBTable($this->_model, "id", $database );
		$i24r->id 	 = $up->id;
		$i24r->order = $current->order;
		if (!$i24r->check())
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		else
			$i24r->store();

		$msg = "Порядок изменен"; 
		mosRedirect("index2.php?ca={$this->_component}&task=view&limit={$_REQUEST['limit']}&limitstart={$_REQUEST['limitstart']}", $msg);
	}
	
	public function actionOrderdown() {
		global $database;
		
		$current = ggo($_REQUEST['cid'][0], $this->_model);
		$up		 = ggsql("select * from {$this->_model} where `order` > {$current->order} order by `order` asc limit 0, 1");		
		$up = $up[0];
		
		$i24r = new mosDBTable($this->_model, "id", $database );
		$i24r->id 	 = $_REQUEST['cid'][0];
		$i24r->order = $up->order;
		if (!$i24r->check())
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		else
			$i24r->store();
			
		$i24r = new mosDBTable($this->_model, "id", $database );
		$i24r->id 	 = $up->id;
		$i24r->order = $current->order;
		if (!$i24r->check())
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		else
			$i24r->store();

		$msg = "Порядок изменен"; 
		mosRedirect("index2.php?ca={$this->_component}&task=view&limit={$_REQUEST['limit']}&limitstart={$_REQUEST['limitstart']}", $msg);
	}
	
	public function actionSaveorder() {
		global $database;
		
		foreach($_REQUEST['order'] as $key => $order){
			$ukey = $this->_component . 'id';
			
			$i24r = new mosDBTable($this->_model, "id", $database );
			$i24r->id 		= $_REQUEST[$ukey][$key];
			$i24r->order 	= $order;
			$i24r->store();
		}
		
		$msg = 'Новый порядок сохранен';
		mosRedirect("index2.php?ca={$this->_component}&task=view&limit={$_REQUEST['limit']}&limitstart={$_REQUEST['limitstart']}", $msg);
	}
	
}