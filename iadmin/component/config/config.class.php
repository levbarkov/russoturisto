<?

class config {
	public $vars = Array();
	public $idBox_postfix = "";
	private $url_return = "";
	function __construct(&$db){
		$this->db = $db;
	}
	function read($component="")
	{		if (  $component==''  )	$this->db->setQuery("select * from #__config order by ordering ");
			else 					$this->db->setQuery("select * from #__config where component='$component' order by ordering ");
			$this->vars = $this->db->loadObjectList();
			return $this->vars;
	}
	function set($id, $type, $val){
/*
    [vars] => Array
        (
   			[0] => stdClass Object
                (
                    [id] => 1
           $type -- [desc] => param101 -- $val
           $type -- [name] => name1 -- $val
           $type -- [val] => val10778 -- $val
           $type -- [component] => main -- $val
                )
            [1] => stdClass Object
                (
                    [id] => 2
                    [desc] => desss2
                    [name] => nnamma2
                    [val] => vaaala2
                    [component] => main
                )

*/
		//$type - 
		if(is_array($this->vars)){  
			foreach($this->vars as $var){
				if($var->id == $id) {
					$var->$type = $val;	return true;
				}
			}
		}
	 	return false;
	}
	function add($id, $type, $val){	
			$obj;
			$obj->id = $id;
			$obj->$type = $val;
			// ggd ($obj);
			// $this->db->insertObject("#__config", $obj, "id");
			array_push($this->vars, $obj);
	}

	function save()
	{
		 if(is_array($this->vars)) {	//	ggtr5 ($this->vars);
			foreach($this->vars as $var) {	// ggtr (  $var  );
				$this->db->updateObject("#__config", $var, "id" ); 
			}
		}
	}

	function remove($ids, $idarray)
	{
			if(is_array($ids)) $str = "";//join(", ", $ids);
			foreach (  $ids as $index){	$str .= $idarray[$index].", "; }
			$str = substr(  $str, 0, (strlen($str)-2)  );
			$query = "delete from #__config where id IN ( ".$str." )";
			$this->db->setQuery($query);
			$this->db->query();
	}

        function remove_addition_config()
	{
            if (  $this->prefix_id==''  ) return;
            $query = "delete from #__config where component = '".$this->prefix_id."'";
            $this->db->setQuery($query);
            $this->db->query();
	}
	
	function returnme ($url_return){
		$this->url_return =	str_replace ("?", "_vopr_",   str_replace ("&", "_ammp_",   str_replace ("=","_ravno_", $url_return)    )    );
	}
	function returnme_url($url_return){
					return 	str_replace ("_vopr_", "?",   str_replace ("_ammp_", "&",   str_replace ("_ravno_","=", $url_return)    )    );
	}
	



	function show_config($component, $iconftitle) {
		global $reg;
		$obj = $this->read( $component );
		?><script language="javascript"> 
			function do_changed<?=$this->idBox_postfix ?> (id){  document.getElementById('conf_values<?=$this->idBox_postfix ?>'+id).checked = true;   }
			function delme<?=$this->idBox_postfix ?> (id, name){  
				if (confirm('Вы действительно хотите удалить переменную '+name+' ?')){ document.location.href='index2.php?ca=<?=$reg['ca'] ?>&task=removecfg&conf_values[]=0&id[]='+id; }
			}
			function delme_returnme<?=$this->idBox_postfix ?> (id, name, returnme){  
				if (confirm('Вы действительно хотите удалить переменную '+name+' ?')){ 
					document.location.href='index2.php?ca=config&task=<? if ($this->component_task!='') print $this->component_task; else print 'removecfg' ?>&conf_values[]=0&id[]='+id+'&returnme=<?=$this->url_return ?>'; 
				}
			}
		</script>
		<? if (  $iconftitle!=''  ) { 
			?><table class="adminheading"><tr><td width="100%"><?
			if (  $iconftitle=='addition_ajax'  ){
				?><br /><a class="ajax_link" href="javascript: $('#conf_section').toggle(0); void(0);">Дополнительные настройки</a><?
			} else print $iconftitle ?></td>
			<? if (  $reg['doCtrlEnter']    ){ ?>
				<td nowrap="nowrap" style="white-space:nowrap">(Ctrl+Enter) &nbsp;&nbsp;&nbsp;&mdash;&nbsp;&nbsp; сохранить<br />(Ctrl+Пробел) &mdash; применить</td>
			<? } ?>
			</tr></table><?
		} 
		?><div id="conf_section" <? if (  $iconftitle=='addition_ajax'  ) print 'style="display:none;"'; ?>><?
		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__config";
		$table_drug->order="ordering";
		/*
		 * ВЫВОДИМ СУЩЕСТВУЮЩИЕ ПЕРЕМЕННЫЕ
		 */
		?><table class="adminlist" border="0" <?=$table_drug->table(); ?> >
			<tr  <?=$table_drug->row(); ?> ><th style="width:15px">&nbsp;</th><th style="width:15px" align="left">ID</th><th style="width:15px" align="left">&nbsp;&nbsp;&nbsp;</th><th width="10%" align="left">Переменная</th><th width="10%" align="left">Значение</th><th width="80%" align="left">Примечание</th><th width="30px" align="left" >№</th><th width="30px" align="left" ></th></tr><?
			$c = 0;
			foreach($obj as $k=>$o){ $rowk = ($c%2);
                                mb_internal_encoding("UTF-8");
				// если переменная - индивидуальный конфиг  - то удаляем префикс, так как пользователю его видеть не обязательно
                                //ggtr (  substr($o->name, 26)  );
                                //ggtr (  strlen($this->prefix_id)  );
				if (  $this->prefix_id!=''  ) $o->name = mb_substr(   $o->name, mb_strlen($this->prefix_id)   );
				?><tr <?=$table_drug->row($o->id, $o->ordering); ?> class='config_row'><?
					?><td><input id="conf_values<?=$this->idBox_postfix ?><?=$o->id ?>" type="checkbox" value="<?=$k ?>" name="conf_values[]"/></td><?
					?><td class="config_id"><label for="cb<?=$o->id ?>"><?=$o->id ?></label></td><?
					?><td class="dragHandle drugme">&nbsp;</td><?
					?><td><input class='control_elem conf_input' type='text' name='conf_name[]' value='<?=$o->name ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
					?><td><input class='control_elem conf_vals' type='text' name='conf_val[]' value='<?=$o->val ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
					?><td><input class='control_elem conf_desc' type='text' name='conf_desc[]' value='<?=$o->desc ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"></td><?
					?><td><input class='control_elem cong_ord' type='text' name='conf_ordering[]' value='<?=$o->ordering ?>' onkeyup="do_changed<?=$this->idBox_postfix ?>(<?=$o->id ?>);"><?
						?><input type='hidden' name='conf_component[]' value='<?=$o->component ?>'><?
						?><input type='hidden' name='conf_id[]' value='<?=$o->id ?>'></td><?
					?><td><a href="javascript: delme<?  if ($this->url_return!='') print '_returnme'; ?><?=$this->idBox_postfix ?>(<?=$o->id ?>, '<?=$o->name ?>'<?  if ($this->returnme!='') print " , '".$this->returnme."' "; ?>); void(0); "><img height="16" border="0" width="16" alt="" src="/iadmin/images/delme.gif" /></a></td><?
				?></tr><? $c++;
			}
			
			if(!isset($o))
				$o = new stdClass();
				
			$o->id=0; 
			/*
			 * Для нового элемента добавляем пустую строку
			 */
			?><tr <?=$table_drug->row(); ?> ><td colspan="8">Добавить новую переменную</td></tr><? $rowk = ($c%2);
			?><tr <?=$table_drug->row(); ?> class='config_row'><?
				?><td><input id="conf_values<?=$this->idBox_postfix ?>0" type="checkbox" value="<?=$c ?>" name="conf_values[]"/></td><?
				?><td></td><?
				?><td></td><?
				?><td><input class='control_elem conf_input' type='text' name='conf_name[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"></td><?
				?><td><input class='control_elem' type='text' name='conf_val[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"></td><?
				?><td colspan="2"><input class='control_elem' style="width:540px; " type='text' name='conf_desc[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"><?
					?><input class='control_elem' type='type' name='conf_component[]' style="width:90px" readonly="readonly" value='<?=$component ?>'><?
					?><input type='hidden' name='conf_id[]' value='0'></td><?
				?><td><input class='control_elem cong_ord' type='text' name='conf_ordering[]' value='' onkeyup="do_changed<?=$this->idBox_postfix ?>(0);"><?
			?></tr><?
                        ?><tr>
                            <td colspan="8">
                                <!-- help show -->

                                
                                <?
                                    // ggtr (  $this->prefix_id  );
                                    if (  preg_match("/^#__foto_cat_/", $this->prefix_id)  )  $this->foto_cat_help1();
                                    if (  preg_match("/^#__exfoto_ID/", $this->prefix_id)  )  $this->exfoto_help1();
                                    if (  preg_match("/^#__exfoto_ID/", $this->prefix_id)  )  $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=exfoto&task=cfg');
                                    if (  preg_match("/^#__exgood_ID/", $this->prefix_id)  )  $this->exgood_main_help1();
                                    if (  preg_match("/^#__exgood_ID/", $this->prefix_id)  )  $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=excfg&task=cfg');
                                    //if (  preg_match("/^#__exgood_ID/", $this->prefix_id)  )  $this->exgood_help1();
                                    if (  preg_match("/^#__excat_ID/", $this->prefix_id)  )  $this->excat_help1();
                                    if (  preg_match("/^#__excat_ID/", $this->prefix_id)  )  $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=excfg&task=cfg');
                                    if (  preg_match("/^#__icat_ID/", $this->prefix_id)  )  $this->icat_help1();
                                    if (  preg_match("/^#__icat_ID/", $this->prefix_id)  )  $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=contentcfg&task=cfg');
                                    if (  preg_match("/^#__content_ID/", $this->prefix_id)  )  $this->content_main_help1();
                                    if (  preg_match("/^#__content_ID/", $this->prefix_id)  )  $this->content_video_help1();
                                    if (  preg_match("/^#__content_ID/", $this->prefix_id)  ) {
                                        if (  $this->typedcontent==1  ) $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=typedcontent&task=cfg');
                                        else                            $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=contentcfg&task=cfg');
                                    }
                                    if (  preg_match("/^#__names_prop_ID/", $this->prefix_id)  )  $this->foto_help1();
                                    if (  preg_match("/^#__names_prop_ID/", $this->prefix_id)  )  $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=names&task=cfg');
                                    if (  preg_match("/^#__names_ID/", $this->prefix_id)  )  $this->foto_help1();
                                    if (  preg_match("/^#__names_ID/", $this->prefix_id)  )  $this->link_conf('Открыть параметры по умолчанию', 'index2.php?ca=names&task=cfg');
                                ?>
                            </td>
                        </tr><?
		?></table><? 
		$table_drug->debug_div();
		?></div><?

	}
	function save_config(){	//ggtr5($_REQUEST);
		global $reg;
                
		if (  count($_REQUEST['conf_values'])==0  ) return;
		foreach (  $_REQUEST['conf_values'] as $index  ){ 
                        if (  $this->prefix_id!=''  ){  // т.е. это индивидуальный конфиг
                                                        // поэтому prefix_id - и есть название компонента
                            preg_match("/_ID(\d*)__$/",$_REQUEST['conf_component'][$index], $matches);
                            //ggtr (  $matches[1]==0  ); ggtr5($matches);
                            if (  $matches[1]==0  ||  $matches[1]=='' )  // если ID элемента равен 0 (был создан новый объект) - заменяем с ID
                                $_REQUEST['conf_component'][$index] = $this->prefix_id;
                        }
			$i24r = new mosDBTable( "#__config", "id", $reg['db'] );
			$i24r->id = $_REQUEST['conf_id'][$index];
			$i24r->name = $_REQUEST['conf_name'][$index];
			// если переменная - индивидуальный конфиг  - то добавляем префикс, так как пользователю его не вводил
			if (  $this->prefix_id!=''  ) $i24r->name = $this->prefix_id.$i24r->name;
			$i24r->val = $_REQUEST['conf_val'][$index];
			$i24r->desc = $_REQUEST['conf_desc'][$index];
			$i24r->component = $_REQUEST['conf_component'][$index];
			//$i24r->ordering = $_REQUEST['ordering'][$index]; if (  $i24r->ordering==''  ) $i24r->ordering=0;
			if (  $_REQUEST['conf_ordering'][$index]==0  ){
				// определение максимального значения для ordering
				$ordering = ggsql ("SELECT * FROM #__config WHERE component='".$i24r->component."' ORDER BY ordering DESC LIMIT 0,1 ");
				if (  isset($ordering[0]->ordering)  ) $i24r->ordering = $ordering[0]->ordering+5; else $i24r->ordering=100;
			} else $i24r->ordering = $_REQUEST['conf_ordering'][$index];
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
                        //ggdd();
		}
	}

        /**
         * выводит надпись-ссылку для открытия помощи
         * @param <type> $id ID объекта
         * @param <type> $title надпись с картинкой help
         */
        function help_title($id, $title, $func_name = 'toggle_unit'){
            ?>
            <script language="javascript">
            function <?=$func_name ?>(){
                    if (  $("<?=$id ?>").attr ('i24state')=='hide'  ){
                            $("<?=$id ?>").show();	$("<?=$id ?>").attr ('i24state','display');
                    } else {
                            $("<?=$id ?>").hide();	$("<?=$id ?>").attr ('i24state','hide');
                    }
            }
            </script>
            <table class="adminheading"><tr><td class="edit"><img src="/iadmin/images/help22.png" width="22" height="22"></td><td class="edit" width="100%"><a class="ajax_link" href="javascript: <?=$func_name ?>(); void(0); "><?=$title ?></a></td></tr></table>
            <?
        }

        function foto_param_help($prefix, $prename, $postnote=''){
            ?>
            <table width="100%" >
                <tr>
                    <td colspan="2"><?=$prename ?>При сохранении 1 фотографии возможно создание до 4 фото с разными параметрами, тип: small, mid, org, full<br>
                    Считается, что: <br>
                    <strong>small</strong> - уменьшенное фото<br>
                    <strong>mid</strong> - промежуточное фото<br>
                    <strong>org</strong> - фото большого разрешения<br>
                    <strong>full</strong> - оригинальный размер<br><br>
                    расмотрим параметры для типа <strong>small</strong>, для остальных типов параметры аналогичные:</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_use</td>
                        <td>1 - использовать или 0 - нет данный тип фото</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_w</td>
                        <td>Ширина фото</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_h</td>
                        <td>высота фото</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_watermark</td>
                        <td>Наложение лого поверх изображения</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_select</td>
                        <td>auto-выделять в превью область согласно заданным размерам, full-выделять всю область (по умолчанию используется - auto)</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_quality</td>
                        <td>качество сохранения фото (0...100), только для jpg-форматов</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_h</td>
                        <td>высота фото</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>zoom_ifsmall</td>
                        <td>принудительное увеличение фото, если заданный размер больше исходного, одинаков для всех типов</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_copy</td>
                        <td>копия другого фото (mid, org, full), применяются только новые эффекты (small_effect)</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_type</td>
                        <td>сохранять в формат: jpg/gif/png/bmp</td>
                </tr>
                <tr>
                        <td class="bold"><?=$prefix ?>small_effect</td>
                        <td>какой эффект применить к фото, &nbsp;<a class="highslide   " onclick="return hs.htmlExpand(this, {
	outlineType: 'rounded-white',
	wrapperClassName: 'draggable-header',
	objectType: 'ajax',
	width: '650',
	height: '650',
	align : 'center'
} )" href="/iadmin/component/foto/foto_effect_help.html">смотреть описание кодов</a>, пример round_png#10#3 - скругление углов</td>
                </tr>
                <tr>
                        <td colspan="2"><?=$postnote ?></td>

                </tr>

        </table>
            <?
        }
        /**
         * Вывод подсказки для подкатегорий фото (параметры фото)
         */
        function foto_cat_help1(){
            config::help_title("#foto_cat_help1", "Помощь — настройка параметров фото");
            ?>
                                
                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="foto_cat_help1" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?  
                                                        config::foto_param_help('', "Для фотографий подкатегории можно настроить свои параметры. "); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }

        function foto_help1(){
            config::help_title("#foto_cat_help1", "Помощь — настройка параметров фото");
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="foto_cat_help1" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?
                                                        config::foto_param_help('', "Для фотографий можно настроить свои параметры. "); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }
        /**
         * Вывод подсказки для категории фото (тип exfoto)
         */
        function exfoto_help1(){
            config::help_title("#exfoto_help1", "Помощь — настройка параметров фото");
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="exfoto_help1" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    Для изменения параметров изображения необходимо перейти к редактированию фото и в "дополнительных настройках" указать новые параметры.
                                                </td>
                                        </tr>
                                </table>
            <?
        }
        /**
         * Вывод подсказки для категории фото (тип exfoto)
         */
        function exgood_main_help1(){
            config::help_title("#exfoto_help1", "Помощь — настройка параметров основного фото товара");
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="exfoto_help1" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?
                                                        config::foto_param_help('', "Для фото товара можно настроить свои параметры. "); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }
        /**
         * Вывод подсказки для категории фото (тип exfoto)
         */
        function link_conf($txt, $link){
            ?><table class="adminheading"><tr><td class="edit"><img src="/iadmin/images/conf22.png" width="22" height="22"></td><td class="edit" width="100%"><a class="ajax_link" target="_blank" href="<?=$link ?>"><?=$txt ?></a></td></tr></table><?
        }
        /**
         * Вывод подсказки для категории фото (тип exfoto)
         */
        function content_main_help1(){
            config::help_title("#content_help1", "Помощь — настройка параметров основного фото");
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="content_help1" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?
                                                        config::foto_param_help('', "Для основного фото новости/статьи можно настроить свои параметры. ", ''); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }
        /**
         * Вывод подсказки для фотошалереи товара (тип exfoto)
         */
        function exgood_help1(){
            config::help_title("#exfoto_help2", "Помощь — настройка параметров прикрепляемых к товару фото", 'toggle_unit2');
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="exfoto_help2" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?
                                                        config::foto_param_help('exgood_', "Для прикрепляемых к товару фотографий можно настроить свои параметры. "); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }

        /**
         * Вывод подсказки для фотогалереи новости/статьи
         */
        function content_help1(){
            config::help_title("#content_help2", "Помощь — настройка параметров фото, прикрепляемых к фотогалереи", 'toggle_unit2');
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="content_help2" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?
                                                        config::foto_param_help('content_', "Для прикрепляемых фотографий можно настроить свои параметры. ", 'Если статисное содержимое, то content меняется на typedcontent (пример: typedcontent_small_w)'); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }
        /**
         * Вывод подсказки для фотогалереи новости/статьи
         */
        function content_video_help1(){
            config::help_title("#content_help3", "Помощь — как настроить отображение видео", 'toggle_unit3');
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="content_help3" style="display:none; ">
                                        <tr class="workspace">
                                                <td colspan="2">Необходимо создать дополнительный параметр - <strong>показывать видео=да</strong><br>
                                                Каждый видео-ролик это прикрепленный файл. В дополнительных параметрах файла задаются настройки видео:</td>
                                        </tr>
                                        <tr class="workspace">
                                                <td class="bold">ширина видео</td><td>пример - 640</td>
                                        </tr>
                                        <tr class="workspace">
                                                <td class="bold">высота видео</td><td>пример - 356</td>
                                        </tr>
                                        <tr class="workspace">
                                                <td class="bold">цвет фона</td><td>формат RGB, указывается в hex кодировке, пример - c7c7c7</td>
                                        </tr>
                                        <tr class="workspace">
                                                <td class="bold">file_org_w</td><td>Ширина заставки видео-ролика, пример - 640</td>
                                        </tr>
                                        <tr class="workspace">
                                                <td class="bold">file_org_h</td><td>Высота заставки видео-ролика, пример - 356</td>
                                        </tr>
                                        <tr class="workspace">
                                                <td colspan="2">Примечание: заставка для видео - это первое прикрепленное в ролику фото.<br>
                                                Размеры по умолчанию для заставки указываются в Меню//Компоненты/Файлы/Настройки<br>
                                                Чтобы задать другие параметры заставки необходимо их указать в разделе <strong>"Дополнительные параметры"</strong> фотогалереи.</td>
                                        </tr>
                                </table>
            <?
        }

        /**
         * Вывод подсказки для фотогалереи категории товаров (тип excat)
         */
        function excat_help1(){
            config::help_title("#excat_help2", "Помощь — настройка параметров фотогалереи категории товаров");
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="excat_help2" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?
                                                        config::foto_param_help('excat_', "Для фото категории товаров можно настроить свои параметры. "); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }
        /**
         * Вывод подсказки для фотогалереи рубрики (тип icat)
         */
        function icat_help1(){
            config::help_title("#excat_help2", "Помощь — настройка параметров фотогалереи рубрики");
            ?>

                                <table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="excat_help2" style="display:none; ">
                                        <tr class="workspace">
                                                <td>
                                                    <?
                                                        config::foto_param_help('', "Для фото рубрики можно настроить свои параметры. "); ?>
                                                </td>
                                        </tr>
                                </table>
            <?
        }
	
}
