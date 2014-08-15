<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $task, $id;

$controller = new AdminVideo();
$controller->route($task);

class AdminVideo {
    private $_model;
    private $_video_path;
    
    private $params;
    
    private $Photos;
    
    public function __construct(){
        $this->_model = '#__video';
        $this->_video_path = '/images/video/img/';
        
        $this->params['type']   = Api::$request->getParam('type', 'str', 'video');
        $this->params['parent']  = Api::$request->getParam('parent', 'int', 0);
        
        $this->Photos = new component_foto(0);
        $this->Photos->init('video');
    }
    
    public function route($task){
        if(strlen($task) > 1){
            $method_name = 'action' . mb_ucfirst($task);
            
            if(method_exists($this, $method_name)){
                return $this->$method_name();
            }
        }
        
        return $this->actionIndex();
    }
    
    public function actionIndex(){
        global $database, $reg;
        
        $id         = Api::$request->getParam('id', 'int', 0);
        $limit      = Api::$request->getParam('limit', 'int', 10);
        $limitstart = Api::$request->getParam('limitstart', 'int', 0);
        
        $myform = new insiteform();
        
        ?>
        
        <form action="index2.php" method="post" name="newfotoForm" enctype="multipart/form-data">
            <table class="adminheading" align="left" width="300" style="width:300px;">
                <tr>
                    <td>Добавление нового видео</td>
                </tr>
                <tr class="workspace">
                    <td rowspan="6" colspan="2"><input type="submit" style=" width: 134px; height: 100%; " value="Добавить видео" /></td>
                </tr>
                <tr class="workspace">
                    <td>
                        <input <? $myform->make_java_text_effect('link', 'input_light'); ?> type="text" name="link" id="name" class="input_gray" style="width:340px;" value="Ссылка на видео..." title="Ссылка на видео..." />
                    </td>
                </tr>
                <tr class="workspace">
                    <td>
                        <input <? $myform->make_java_text_effect('title', 'input_light'); ?> type="text" name="title" id="name" class="input_gray" style="width:340px;" value="Заголовок..." title="Заголовок..." />
                    </td>
                </tr>
                <tr class="workspace">
                    <td>
                        <textarea <? $myform->make_java_text_effect('description', 'input_light'); ?> name="description" id="desc" cols="70" class="input_gray" style="width:340px;" title="Описание видео" rows="4">Описание видео</textarea>
                    </td>
                </tr>   
                <tr>
                    <td><b>Изображение для ролика:</b></td>
                </tr>
                <tr>
                    <td><input type="file" name="newfoto" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>    
            </table>
            <input type="hidden" name="ca" value="video" />
            <input type="hidden" name="task" value="save" />
        </form>
        <br />
        <table class="adminheading">
            <tr>
                <td width="100%">
                    <?
                        $component_foto->icatway = i24pathadd(  $component_foto->icatway, "Видео", ""  );
                        i24pwprint_admin ($component_foto->icatway, 0);
                    ?>
                </td>
            </tr>
        </table>
        <form action="index2.php" method="post" name="adminForm">
            <?php
                // инициализация класса необходимого для перемящаемой таблицы
                $table_drug         = new ajax_table_drug ;
                $table_drug->id     = "ajax_table_drug_td";
                $table_drug->table  = $this->_model;
                $table_drug->order  = "ordering";
                
                $sql = "select count(*) from {$this->_model} where `type` = '{$this->params['type']}' and `parent` = {$this->params['parent']}";
                $count_video = ggsqlr($sql);
                
                $sql = str_replace('count(*)', '*', $sql) . ' order by `ordering`';
                $videos = ggsql($sql, $limitstart, $limit);
                
                require_once( site_path . '/iadmin/includes/pageNavigation.php' );
                $pageNav = new mosPageNav( $count_video, $limitstart, $limit  );
                
                $video_html = '';
                foreach($videos as $k => $video){
                    $tbl_d = $table_drug->row($video->id, $video->ordering);
                    $pp = $k + 1 + $pageNav->limitstart;
                    $checkbox = mosHTML::idBox($k, $video->id);
                    
                    $title          = $video->title != '' ? "<h3>{$video->title}</h3>" : '';
                    $link           = $video->link  != '' ? "<b>{$video->link}</b>" : '';
                    $description    = $video->description != '' ? "<p>{$video->description}</p>" : '';
                    
                    $seflink = urlencode($video->link);
                    
                    $image = '';
                    if($video->small != '')
                        $image = "<img src='{$this->_video_path}{$video->small}' alt='' title='Кликните чтобы открыть' border='5' style='border-color: #ccc' />";
                
                    $video_html .= <<<HTML
                        <tr {$tbl_d} class="row{$k}">
                            <td>{$pp}</td>
							<td>{$checkbox}</td>
                            <td align="left">
                                {$title}
                                {$link}
                                {$description}
                            </td>
                            <td>
                                <a href="javascript:ins_ajax_open('index2.php?ca=video&task=showVideo&link={$seflink}&4ajax=1',0,0);">
                                    {$image}
                                </a>
                            </td>
                            <td align="center" class="dragHandle drugme"></td>
                            <td align="center"><input type="text" name="order[]" size="5" value="{$video->ordering}" class="text_area" style="text-align: center" /><input type="hidden" name="videoid[]" value="{$video->id}" /></td>
                            <td align="center"><a href="index2.php?ca=video&type={$this->params['type']}&parent={$this->params['parent']}&task=remove&cid[]={$video->id}">Удалить</a></td>
                        </tr>               
HTML;
                }

            ?>
            <table class="adminlist" <?php echo $table_drug->table(); ?> >
                <tr <? echo $table_drug->row(); ?> >
                    <th width="2%" class="title">#</th>
                    <th width="3%" class="title"><input type="checkbox" name="toggle" value="" /></th>
                    <th class="title">Информация</th>
                    <th class="title">Видео</th>
                    <th align="center" width="5%">Сортировка</th>
                    <th width="3%" ><a href="javascript: saveorder( <?php echo $count_video - 1; ?> )" onmouseover="javascript: Tip('Сохранить заданный порядок отображения');">Сохранить порядок</a></th>
                    <th class="title"></th>
                </tr>
                <?php echo $video_html; ?>
            </table>
            <input type="hidden" name="ca" value="video" />
            <input type="hidden" name="parent" value="<? echo $this->params['parent']; ?>" />
            <input type="hidden" name="task" value="save" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="1" />
        </form><?
        
        echo $pageNav->getListFooter();
    }
    
    public function actionSave(){
 		global $reg, $database;
        
        $error = array();
        
        $id          = Api::$request->getParam('id', 'int', 0);
        $link        = Api::$request->getParam('link', 'str', 'Ссылка на видео...');
        $title       = Api::$request->getParam('title', 'str', 'Заголовок...');
        $description = Api::$request->getParam('description', 'str', '');
        
        if(strlen($link) <= 0 || $link == 'Ссылка на видео...')
            $error[] = '- Не указана ссылка на видео!';
            
        if(strlen($title) <= 0 || $title == 'Заголовок...')
            $error[] = '- Не указано название видео!';
            
        if(count($error) > 0){
            $error = implode('<br />', $error);
            mosRedirect("index2.php?ca=video&parent={$this->params['parent']}&type={$this->params['type']}", $error);
            return false;
        }
        
        $max_order = ggsqlr("select max(`ordering`) from {$this->_model} where `type` = '{$this->params['type']}' and `parent` = {$this->params['parent']}");

		$object = new mosDBTable($this->_model, 'id', $database);
		$object->id = $id;
        $object->title = $title;
		$object->link = $link;
		$object->description = $description;		
		$object->parent = $this->params['parent'];
		$object->type = $this->params['type'];               
		$object->ordering = $max_order + 1;
        
        if(!$object->check()){
            $error = $object->getError();
            echo "<script> alert('{$error}'); window.history.go(-1); </script>\n";
        }
        else
            $object->store();
            
        $return_url = "index2.php?ca=video&parent={$object->parent}&type={$object->type}";
        $return_msg = "Объект сохранен: {$object->title}";          
        
        if ($_FILES["newfoto"]['tmp_name']){
            $this->Photos->parent = $object->id;
            $this->Photos->delmainfoto();
            $this->Photos->external_foto($return_url, $return_msg);
            
            return;
        }

		mosRedirect($return_url, $return_msg);
        
		return;        
    }	
	
	public function actionSaveorder() {
		global $database;
		$orders = Api::$request->getParam('order', 'array');
		
		if(count($orders)){
			$object = new mosDBTable($this->_model, "id", $database );
			foreach($orders as $k => $order){
				$object->id = $_REQUEST['videoid'][$k];
				$object->ordering = $order;
				$object->store();
			}		
		}
		
		$msg = 'Новый порядок сохранен';
		mosRedirect("index2.php?ca=video&type={$this->params['type']}&parent={$this->params['parent']}", $msg);
		return;	
	}
	
	public function actionShowVideo() {
		$link = urldecode(Api::$request->getParam('link', 'str', ''));
		
		$vds = new get_videos();
		echo $vds->make($link);	
	}
	
	function actionRemove() {
		global $database, $reg;
		$video_ids = Api::$request->getParam('cid', 'array');
				
		foreach($video_ids as $id){
			$video = ggo($id, $this->_model);
			if(file_exists(site_path . $this->_video_path . $video->small))
				unlink(site_path . $this->_video_path . $video->small);
				
			ggsqlq ("delete from {$this->_model} where `id`={$id} limit 1");
		}
		
		$msg = 'Видео удалено';		
		mosRedirect("index2.php?ca=video&parent={$this->params['parent']}&type={$this->params['type']}", $msg);
	}
       
}
