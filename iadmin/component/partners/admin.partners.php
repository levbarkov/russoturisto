<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $task;

class AdminPartners {
    private $_component = 'partners';
    private $_model     = '#__partners';
    private $_task;
	
	private $Photos;
    
    public function __construct(){
        $this->Photos = new component_foto(0);
        $this->Photos->init('partners');          
    }
    
    public function route($task){
        $this->_task = $task;
		
		if($task == 'new')
			$task = 'edit';
        
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
        $iway[0]->name = $reg['partners_name'];
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
            
            $name = desafelysqlstr($row->name);
            $site = desafelysqlstr($row->site);
			$desc = desafelysqlstr($row->description);
			$show_on_main = $row->show_on_main == 1 ? '<b>Да</b>' : 'Нет';
			
			$logotip = '';
			if(!empty($row->small)){
				$logotip = "<a onclick='return hs.expand(this)' href='/images/partners/{$row->org}'><img src='/images/partners/{$row->small}' title='{$name}' width='50' alt='' border='0' /></a>";
			}
            
            $rows_html .= <<<HTML
                <tr {$tr_cls} class="row{$k}">
                    <td>{$pp}</td>
                    <td>{$checkbox}</td>
                    <td align="left"><a href="{$url}">{$name}</a></td>
					<td>{$logotip}</td>
                    <td>{$site}</td>
					<td>{$desc}</td>
					<td>{$show_on_main}</td>
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
                        <th class="title" class="dragHandle">Название ораганизации</th>
						<th class="title">Логотип компании</th>
                        <th class="title">Сайт</th>
						<th width="50%">Описание</th>
						<th>На главной</th>
                        <th align="center" width="5%">Сортировка</th>
                        <th width="3%">
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
	
	public function actionSave() {
		global $database, $reg;
	
		$i24r = new mosDBTable($this->_model, "id", $database );
		$i24r->id = safelySqlInt($_REQUEST['id']);
		$i24r->name = safelySqlStr($_REQUEST['name']);
		$i24r->site = safelySqlStr($_REQUEST['site']);
		$i24r->description = safelySqlStr($_REQUEST['description']);
		$i24r->show_on_main = empty($_REQUEST['show_on_main']) ? 0 : 1;
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
			$adminlog->logme('new', $reg['partners_name'], $i24r->name, $i24r->id );
		else
			$adminlog->logme('save', $reg['partners_name'], $i24r->name, $i24r->id );
			
        $return_url = "index2.php?ca={$this->_component}";
        $return_msg = "Сохранено: {$i24r->name}";          
        
        if ($_FILES["newfoto"]['tmp_name']){
            $this->Photos->parent = $i24r->id;
            $this->Photos->delmainfoto();
            $this->Photos->external_foto($return_url, $return_msg);
            
            return;
        }

		mosRedirect($return_url, $return_msg);
        
		return; 
	}
	
	public function actionEdit($id = 0){
		global $database, $mainframe, $reg;		
		$id = intval(getUserStateFromRequest('id', $id));
			
		if ($id > 0)
			$row = ggo ($id, $this->_model);
		else{
			$row->id = 0;
			$row->name = '';
			$row->site = '';
			$row->description = '';
			$row->show_on_main = 0;
			$row->small = '';
		}
		
		$logotip = '';
		if(!empty($row->small)){
			$logotip = "<p> Текущий логотип: </p><a onclick='return hs.expand(this)' href='/images/partners/{$row->org}'><img src='/images/partners/{$row->small}' title='{$name}' width='50' alt='' border='0' /></a>";
		}
		
		$check_show_main = $row->show_on_main == 1 ? 'checked="checked"' : '';
			
			
		$iway[0]->name = $reg['partners_name'];
		$iway[0]->url  = '';
		$iway[1]->name = $row->id ? 'Редактирование' : 'Новый';
		$iway[1]->url  = '';			
			
		i24pwprint_admin ($iway, 0);
		
		$html = <<<HTML
		<script type="text/javascript">
			$(document).ready(function(){
				$('a.toolbar').click(function(){
					if($(this).html() != 'Сохранить')
						return true;
						
					var name = $('#prt_name').val(),
						desc = $('#prt_description').val();
						
					if(name.length < 3 || desc.length < 10){
						alert('Необходимо заполнить все обяазтельные поля.');
						return false;
					}
					
					return true;
				});
			});
		</script>
		<form action="index2.php" name="adminForm" method="post" enctype="multipart/form-data">
			<input type="hidden" name="iuse" id="iuse" value="0" />
			<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
				<tr class="workspace">
					<td>Название организации: <font color="#cc0000">*</font></td>
					<td><input name="name" id="prt_name" size="120" value="{$row->name}" /></td>				
				</tr>
				<tr class="workspace">
					<td>Сайт: </td>
					<td><input name="site" size="120" value="{$row->site}" /></td>
				</tr>
				<tr class="workspace">
					<td>Описание: <font color="#cc0000">*</font></td>
					<td><textarea name="description" id="prt_description" cols="87" rows="7">{$row->description}</textarea></td>
				</tr>
				<tr class="workspace">
					<td>Показывать на главной?</td>
					<td><input type="checkbox" name="show_on_main" value="1" {$check_show_main} /></td>
				</tr>
				<tr class="workspace">
					<td>Логотип компании:</td>
					<td>
						<input type="file" name="newfoto" accept="image/*" value="" />
						{$logotip}
					</td>
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
			$this->Photos->parent = $adminlog_obj->id;
			$this->Photos->load_parent();
			$this->Photos->del_fotos();
			$adminlog = new adminlog();
			$adminlog->logme('del', $reg['partners_name'], $adminlog_obj->name, $adminlog_obj->id );
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
		
		$conf->show_config($this->_component, "Настройки / " . $reg['partners_name']);
		
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
		$adminlog->logme('cfg', $reg['partners_name'], "", "" );
		
		mosRedirect("index2.php?ca={$this->_component}&task=cfg", 'Настройки сохранены');		
	}
	
	public function actionRemovecfg(){
		global $reg;
		load_adminclass('config');
		
		$conf = new config($reg['db']);
		$conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
		
		$adminlog = new adminlog();
		$adminlog->logme('delcfg', $reg['partners_name'], '', '');
		
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

$controller = new AdminPartners();
$controller->route($task);