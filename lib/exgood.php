<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/*
 *
 * КЛАСС ДЛЯ РАБОТЫ С ТОВАРАМИ КАТАЛОГА ПРОДУКТОВ
 *
 */
class exgood{
	var $id;
	var $vars;
	var $select_types =array(	1 => 'Выбор из списка', 
					2 => 'Без выбора, покупать сразу товар', 
					3 => 'Выбор характеристик',
					4 => 'Выбор из группы характеристик'
				);
        /**
         * ВАРИАНТ ЭФФЕКТА ДОБАВЛЕНИЯ В КОРЗИНУ
         * 1 - стандартная ссылка на корзину
         * 2 - отображаем окно для ввода количества, после чего окно летит в корзину
         * 3 - открываем ajax-корзину в всплывающем окне (по умолчанию)
         *
         * В настойщее время весь каталог настроен на эффект 3,
         * Чтобы использовать другие два эффекта необходимо их раскомментировать в местах "добавления в корзину"
         */


	function load_me(){
		if (  $this->id>0  ) $this->vars = ggo($this->id, "#__exgood");
	}
	function get_main_foto( $type, $noimage_return=0 ){
		if (  $this->vars->$type!=''  ) return "/images/ex/good/".$this->vars->$type;
		else {
			if (  $noimage_return==1  ) return false;
			else return $reg['exgood_main_small_noimage'];
		}
	}
	function get_expack_select_type (){
		foreach (  $this->select_types as $select_type_i => $select_type) $vcats[] = mosHTML::makeOption( $select_type_i, $select_type);
		return $vcats;
	}
        /*
         * Выбор из группы характеристик, HTML-вывод
         */
	private function show_order_form_4html(){
                global $reg;
                if (  !$this->first_expack->id  ){ // первая комплектация еще не определенна
                    $this->get_first_expack();
                }
		?><form name="select_attrib_<?=$this->id ?>">
		<table cellpadding="0" cellspacing="0"><!--вывод значений свойств-->
			<tr>
				<? 		
				$counter = count($this->mydata->expack_set_attribs);
				if (  count($this->mydata->expack_set_attribs)>0  )
				for (  $i=0; $i<$counter; $i++  ){ ?>
					<td nowrap="nowrap" style=" white-space:nowrap; <? print $i>0?'padding-left:34px;':''; ?> "><!--вывод значений свойства ID<?=$this->mydata->expack_set_attribs[$i]->id ?>-->
					<?=safelySqlStr(  $this->mydata->expack_set_attribs[$i]->name  ); ?>&nbsp;
					<? print mosHTML::selectList( 	$this->mydata->expack_set_attribs[$i]->val, 
														'select_attrib_'.$this->mydata->expack_set_attribs[$i]->id, 
														'class="inputbox" size="1" id="select_attrib_'.$this->mydata->expack_set_attribs[$i]->id.'" ', 'id', 'val', 0 ); ?></td>
				<? } ?>
				<td style="padding-left:34px;"><!-- кнопка заказать -->
					<?
					$link_ajax_onclick  = " javascript: var cart_options='mycart_options[display_type]=4&";
					for (  $i=0; $i<$counter; $i++  ) $link_ajax_onclick .= "mycart_options[".$this->mydata->expack_set_attribs[$i]->id."]='+$('#select_attrib_".$this->mydata->expack_set_attribs[$i]->id." :selected').val()+'&";

					$link_ajax_onclick .="';     ins_ajax_open('/index.php?c=ex&task=viewtrush&mycart_task=put&mycart_id=".$this->first_expack->id."&'+cart_options+'4ajax=1&floating=1', ".$reg['shop_cart_w'].", 0);      void(0); ";
					?><a class="buy"  href="<?=$link_ajax_onclick ?>"><img src="/component/ex/cart.png" width="68" height="48" border="0" /></a>
				</td>
			</tr>
		</table></form><?
	}
	private function show_order_form_4(){
		// загружаем группу
		$this->mydata->expack_set = ggo ($this->expack_set, "#__expack_set");
		$this->mydata->expack_set_attribs = ggsql ( " SELECT * FROM #__expack_attrib WHERE parent=".$this->expack_set  );
		//ggtr ($this->mydata);
		$counter = count($this->mydata->expack_set_attribs);
		if (  count($this->mydata->expack_set_attribs)>0  )
		for (  $i=0; $i<$counter; $i++  ){
			$this->mydata->expack_set_attribs[$i]->val = ggsql ( " SELECT * FROM #__expack_attrib_val WHERE parent=".$this->mydata->expack_set_attribs[$i]->id  );
		}
		// ggr ($this->mydata);
/*		ggr (  ggsql (	
		" SELECT * FROM #__expack_attrib as expack_attrib 
			inner join #__expack_attrib_val as expack_attrib_val on expack_attrib_val.parent=expack_attrib.id 
			WHERE expack_attrib.parent=".$this->vars->expack_set."
			"
		)   ); */
	}
	function show_order_form_3(){
		global $reg;
		?><script language="javascript">
			var attribs_select={	target:        '#pack_select_div',
						beforeSubmit:  function(){	over_fade('#pack_select_div', '#pack_select_table', '', 0.7);	}
                                            };
		</script>
		<form name="pack_select" id="pack_select" method="post" action="/">
		<div id="pack_select_div">
		<? $this->show_attribs(); ?>
		</div>
		<input type="hidden" name="good" value="<?=$this->id; ?>" />
		<input type="hidden" name="c" value="<?=$reg['c']; ?>" />
		<input type="hidden" name="task" value="show_attribs" />
		<input type="hidden" name="4ajax" value="1" />
		</form><?
	}

        /*
         * для типа заказа, когда необходим ВЫБОР ХАРАКТЕРИСТИК. самый сложный вариант.
         * Строятся теги select для каждого свойства из выбранной группы характеристик
         * содержащие значения только если имеется комплектация с данным свойством.
         * ПРИМЕР: кроссовок размеры 40,41,42,43,44 цвет: белый, черный и всего
         * три комплектации: 42 белый, 42 черный, 44 черный,
         * если задать цвет черный - то будут доступны только 42 и 44 размеры
         */
	function show_attribs(){
	
		  global $reg, $_REQUEST;
		  $db = &$reg['db'];
		  //ggd ($this);
		  //$id = intval($_REQUEST['id']);	//	$id = 49;
		  $id = $this->id;
		  $unit = intval($_REQUEST['unit']);
		  if($unit == 0) {	// необходимо задать хоть какую-то единицу измерения
			$first_unit = ggsql (  "select unit from #__expack where parent=$id limit 0,1 "  );
			$unit = $first_unit[0]->unit;
		  }
		  $ids = Array();
		  
		  // сканирование выбранных значений
		  // example: $_REQUEST['attrib_15']=39;		  $_REQUEST['attrib_16']=45;
		  foreach($_REQUEST as $key=>$val)
		  {
			  if(preg_match("/attrib_(\d+)/", $key, $m))
			  {
				if($val != 0) $ids[$m[1]] = $val;
			  }
		  }
		  
		  /* Выбор тех паков, которые подходят */
		  $where = "";
		  $db->setQuery("SELECT DISTINCT ep.id FROM #__expack as ep LEFT JOIN #__expack_set_val as esv ON (ep.id = esv.pack_id) WHERE ep.parent = ".$id." AND unit = ".$unit);
		  $db->query();
		  $found = $db->loadResultArray();
	
		  foreach($ids as $key=>$value){
			$where = " AND ( attrib = ".$key." AND attrib_val = ".$value." ) ";
			$db->setQuery("SELECT DISTINCT ep.id FROM #__expack as ep LEFT JOIN #__expack_set_val as esv ON (ep.id = esv.pack_id) WHERE ep.parent = ".$id." AND unit = ".$unit." ".$where);
			$db->query();
			$pack = $db->loadResultArray();  
			if(count($found) > 0){
				  foreach($found as $k=>$v){
						$f = false;
						foreach($pack as $k1 =>$v1){
						  if($v == $v1) $f = true;
						}
						if(!$f) {  $found[$k] = 0;  }
				  }
			}  
		  }
		  $pack = attrib_search($id, $unit, $ids);
		  $toCart = 0;
		  if(count($pack) == 1) $toCart = $pack[0];
	
		  $pack = join (", ", $pack);
	
		  $attribs = Array();
		  $units = Array();
		  
		  $pack_unit = attrib_search($id, 0, $ids);
		  $pack_unit = join (", ", $pack_unit);
		  $db->setQuery("SELECT DISTINCT eu.* FROM #__exgood_unit as eu LEFT JOIN #__expack as ep ON (eu.id = ep.unit) where eu.parent = ".$id." AND ep.id IN (".$pack_unit.")");
		  $db->query();
	
		  $units = $db->loadObjectList();
		  $attribs[] = "Упаковка";
		  
		  /* 
		   * определяем список всех характеристик
		   */
		  $db->setQuery("SELECT * FROM #__expack_attrib where parent = ".$this->expack_set);
		  $db->query();
		  $objs = $db->loadObjectList();
		  foreach($objs as $o) $attribs[] = $o->name; 
			/*
			 * ВЫВОДИМ НАБОР SELECT'ов
			 */
			?><table width="" cellspacing="0" cellpadding="4" border="0" align="left" id="pack_select_table" >
					<tr>	
					  <? foreach ($attribs as $a) { print '<td  style="font-size:11px;">'.$a.'</td>'; } ?>
					</tr>
					<tr><?
						if(count($units) > 0) {
							  ?><td  style="font-size:11px;"><select style="width:150px" onchange=" $('#pack_select').ajaxSubmit(attribs_select); return false; " name="unit"><?
							foreach($units as $u)
							{
								  $str = "";
								  if($u->id == $unit) $str = " SELECTED ";
								  print '<option value="'.$u->id.'" '.$str.'>'.$u->name.'</option>'."\n";
							}
							print "</select></td>";
						}
				
						foreach($objs as $o)
						{
							$ids_new = Array();
							foreach($ids as $key=>$val)
							{
								  if($key != $o->id) $ids_new[$key] = $val;
							}
							  
							$pack = attrib_search($id, $unit, $ids_new);
							$pack = join (", ", $pack);
							?><td style="font-size:11px;"><select onchange=" $('#pack_select').ajaxSubmit(attribs_select); return false; " name="attrib_<?=$o->id; ?>"><option value="0">Не указанно</option><?
							$db->setQuery("SELECT distinct av.id as id, av.val as val, av.parent FROM #__expack_attrib_val as av left join #__expack_set_val as sv ON (av.id = sv.attrib_val) WHERE av.parent = ".$o->id." AND sv.pack_id IN (".$pack.")");
							$db->query();
							$ol = $db->loadObjectList();
						
							if(count($ol) > 0){
									foreach($ol as $ololo){
										  $sel = "";
										  if($ids[$ololo->parent] == $ololo->id) $sel = " SELECTED ";
										  print '<option '.$sel.' value="'.$ololo->id.'">'.$ololo->val."</option>\n";
									}
							}
							print "</select></td>";
						}
						if($toCart != 0) { ?><td><?
                                                    /* вариант добавления - открываем окно для ввода количества, после чего окно летит в корзину 
                                                    <a href="#" class="buy" onclick="toCart(<?=$toCart; ?>,'<?=$pack_price->val ?>','',this); return false; "><img src="/component/ex/quickbuy.gif" border="0" /></a>*/

                                                    /* открываем всплавающее ajax-окно с корзиной */
                                                    ?><a href=" javascript: ins_ajax_open('/<?=$reg['ex_seoname']; ?>/shop.html?mycart_task=put&mycart_id=<?=$toCart; ?>&4ajax=1&floating=1', <?=$reg['shop_cart_w'] ?>, 0); void(0); " class="buy"><img src="/component/ex/quickbuy.gif" border="0" /></a><?
                                                }
						?></td>
                                        </tr>
			</table>
			<input type="hidden" name="selected_pack" value="0" /><?
	}

        /**
         * заказ товара. Тип: Выбор комплектации из списка
         *
         * @param <type> $sort
         */
	function show_order_form_1( $sort='' ){
            global $reg;
		$all_cy_ggsql = ggsql (  "select * from #__exprice_cy "  );
		$all_cy = libarray::convert_ggsql_object_to_array ($all_cy_ggsql); // ggtr ($all_pricies);
		$all_sklads = ggsql (  "select * from #__exsklad "  );
		$all_sklads_cnt = count ($all_sklads);

		?><hr style=" border:0px none; border-top:1px dotted #cccccc;" />
		<table width="100%" cellspacing="0" cellpadding="4" border="0" >
			<tr>
				<td colspan="3" style="font-size:11px;">Комплектации:</td>
				<? foreach ($all_sklads as $sklad) { ?><td  style="font-size:11px;"><?=$sklad->name ?></td><? } ?>
				<td  style="font-size:11px;">Цена</td>
				<td></td>
			</tr>
			<?
                        if (  !$this->expack  ) $this->expack = new expack();
                        if (  !$this->expack->parent  ) $this->expack->parent = $this->id;

                        if(  $sort == "priceup"  OR  $sort == "pricedown"  ) $all_expacks = array ( $this->first_expack );
                        else $all_expacks = $this->expack->get_packs();
                        
			foreach ($all_expacks as $expack1){
				?><tr class="hrow">
					<td>&nbsp;</td>
					<td width="10%"><?=$expack1->sku ?></td>
					<td><?=$expack1->name ?></td>
					<? foreach ($all_sklads as $sklad) { ?><td><? $ostatok=$this->expack->get_ostatok($expack1, $sklad->id); print $ostatok->val; ?></td><? } ?>
					<td><? $pack_price = $this->expack->get_price($expack1, 1);
							print num::flexprice($pack_price->val)."&nbsp;".$this->expack->cy($pack_price->cy,1);
					 ?></td>
					<td><?
                                            /* вариант добавления - открываем окно для ввода количества, после чего окно летит в корзину
                                            <a href="#" class="buy" onclick="toCart(<?=$expack1->id ?>,'<?=$pack_price->val ?>','',this); return false; "><img src="/component/ex/quickbuy.gif" border="0" /></a>*/
                                        
                                            /* открываем всплавающее ajax-окно с корзиной */
                                            ?><a href=" javascript: ins_ajax_open('/<?=$reg['ex_seoname']; ?>/shop.html?mycart_task=put&mycart_id=<?=$expack1->id ?>&4ajax=1&floating=1', <?=$reg['shop_cart_w'] ?>, 0); void(0); " class="buy"><img src="/component/ex/quickbuy.gif" border="0" /></a><?
                                         ?></td>
				</tr><?
			} ?>
		</table><?
	}


        function get_first_expack(){
                global $reg;
                if (  !$this->first_expack->id  ){ // единственная комплектация еще не определенна
                    $this->expack = new expack();
                    $this->expack->expack_select_type = $this->expack_select_type;
                    $this->expack->parent = $this->id;
                    $this->first_expack = $this->expack->get_1stpack();
                }
        }
        /*
         * сразу покупаем товар, т.е. берем первую комплектацию по умолчанию
         */
	function show_order_form_2(){
                global $reg;
                if (  !$this->first_expack->id  ){ // единственная комплектация еще не определенна
                    $this->get_first_expack();
                }
		$link_ajax_onclick = " javascript: ins_ajax_open('/".$reg['ex_seoname']."/shop.html?mycart_task=put&mycart_id=".$this->first_expack->id."&4ajax=1&floating=1', ".$reg['shop_cart_w'].", 0); void(0); ";
		?><a class="buy"  href="<?=$link_ajax_onclick ?>"><img src="/component/ex/cart.png" width="68" height="48" border="0" /></a><?
	}
	function show_order_form( $sort='' ){
		if (  $this->expack_select_type==1){	//Выбор комплектации из списка
			$this->show_order_form_1( $sort );
		}
		if (  $this->expack_select_type==2){	//сразу покупаем товар, т.е. берем первую комплектацию по умолчанию
			$this->show_order_form_2();
		}
		if (  $this->expack_select_type==3){	//Выбор характеристик
			$this->show_order_form_3();
		}
		else if (  $this->expack_select_type==4){	//Выбор из группы характеристик
			$this->show_order_form_4();			// загрузка данных
			$this->show_order_form_4html();		// вывод html кода
		}
	}

    /**
     * Удаление товара
     *
     * @param adminlog $adminlog
     * @param <type> $delp 
     */
    function delme( $adminlog=0, &$delp=NULL ){
                global $reg;
                $dfgd = $this->id;
                if (  isset ($delp->delfoto)  )         $p->delfoto         = $delp->delfoto;       else $p->delfoto = 1;
                if (  isset ($delp->delfile)  )         $p->delfile         = $delp->delfile;       else $p->delfile = 1;
                if (  isset ($delp->delcomments)  )     $p->delcomments     = $delp->delcomments;   else $p->delcomments = 1;
                if (  isset ($delp->deltags)  )         $p->deltags         = $delp->deltags;       else $p->deltags = 1;
                if (  isset ($delp->delnames)  )        $p->delnames        = $delp->delnames;      else $p->delnames = 1;
                if (  isset ($delp->prefix_config)  )   $p->prefix_config   = $delp->prefix_config; else $p->prefix_config = 1;

                if (  $p->delfoto==1  ){ // удаляем фото
                    $component_foto = new component_foto ( 0 );
                    $component_foto->init('exgood');
                    $component_foto->parent = $dfgd;
                    $component_foto->load_parent();
                    $component_foto->del_fotos();
                } else if (  $adminlog==1  ) {
                    $component_foto->parent_obj = ggo (  $dfgd, '#__exgood'  );
                }

                if (  $p->delfile==1  ){ // удаляем файлы
                    $component_file = new component_file ( 0 );
                    $component_file->init('exgood');
                    $component_file->parent = $dfgd;
                    $component_file->load_parent();
                    $component_file->del_files();
                }

                ggsqlq("DELETE #__exprice_good FROM #__exprice_good LEFT JOIN #__expack ON ( #__expack.id = #__exprice_good.expack ) WHERE #__expack.parent = ".$dfgd." ");
                ggsqlq("DELETE #__exsklad_good FROM #__exsklad_good LEFT JOIN #__expack ON ( #__expack.id = #__exsklad_good.expack ) WHERE #__expack.parent = ".$dfgd." ");
		//удаляем единицы измерения и комплектации
		ggsqlq("DELETE FROM #__exgood_unit WHERE parent = ".$dfgd."  ");
		ggsqlq("DELETE FROM #__expack WHERE parent = ".$dfgd."  ");

		if (  $p->delcomments==1  ) { // удаляем комментарии
                    $comments = new comments('exgood', $reg['db'], $reg);
                    $comments->del_for_type( $dfgd );
                }

		if (  $p->deltags==1  ) { //удаляем тэги
                    $tag = new tags('exgood', $reg['db'], $reg);
                    $tag->delete($dfgd);
                }

                if (  $p->delnames==1  ) { //удаляем свойства NAMES
                    $names = new names($dfgd, 'exgood', $reg);
                    $names->delete();
                }

                if (  $adminlog==1  ){
                    $adminlog_obg = $component_foto->parent_obj;	$adminlog = new adminlog(); $adminlog->logme('del', $reg['ex_name'], $adminlog_obg->name, $adminlog_obg->id );
                }
		ggsqlq ("DELETE FROM #__exgood WHERE id=".$dfgd);

                if (  $p->prefix_config==1  ) { // удаление индивидуальных настроек
                    load_adminclass('config');
                    $conf = new config($reg['db']);
                    $conf->prefix_id = '#__exgood'."_ID".$dfgd."__";
                    $conf->remove_addition_config();
                }

    }

    function saveme( &$delp=NULL ) {
            global $database, $my, $reg;
            ilog::vlog('/* function exgood::saveme');
            if (  isset ($delp->saveNames)  )           $p->saveNames           = $delp->saveNames;         else $p->saveNames = 1;
            if (  isset ($delp->saveTag)  )             $p->saveTag             = $delp->saveTag;           else $p->saveTag = 1;
            if (  isset ($delp->updateGooodsCount)  )   $p->updateGooodsCount   = $delp->updateGooodsCount; else $p->updateGooodsCount = 1;
            if (  isset ($delp->SmartOrder)  )          $p->SmartOrder          = $delp->SmartOrder;        else $p->SmartOrder = 1;

            if (  $this->vars->id  )  $exgood = ggo ($this->vars->id, "#__exgood"); //ggd ($exgood);

            $i24r = new mosDBTable( "#__exgood", "id", $database );
            $i24r->id      = safelySqlInt($this->vars->id);
            $i24r->parent  = $this->vars->parent;
            $i24r->name    = $this->vars->name;
            $i24r->sdesc   = $this->vars->sdesc;
            $i24r->fdesc   = $this->vars->fdesc;
            $i24r->publish = $this->vars->publish;
            $i24r->spec    = $this->vars->spec;
            $i24r->expack_select_type = $this->vars->expack_select_type;
            if (  $this->vars->expack_set>0  )    $i24r->expack_set = $this->vars->expack_set;
            if (  isset($this->vars->connect)  )  $i24r->connect = $this->vars->connect;
            
            /*
             * НЕОБХОДИМО ИНИЦИАЛИЗИРОВАТЬ
             */
            //$exgood->vars->_tag_field   = $_REQUEST['_tag_field'];
            //$exgood->vars->_names_field = $_REQUEST['_names_field'];

            if (  $this->vars->sefname!=''  ) $i24r->sefname = sefname( $this->vars->sefname );
            else $i24r->sefname = sefname( $i24r->name );

            $cond = "parent = {$i24r->parent} and sefname = '{$i24r->sefname}' limit 1";
            if ($i24r->id == 0) {
                $max_id = ggsqlr('select max(id) + 1 as max_id from #__exgood');
                if (count(ggsql("select id from #__exgood where {$cond}")))
                    $i24r->sefname .= "_{$max_id}";
            }
            elseif (count(ggsql("select id from #__exgood where id != {$i24r->id} and {$cond}")))
                $i24r->sefname .= "_{$i24r->id}";

            if (  !isset($exgood->parent)  )  $exgood->parent=-1;	// возможно переменная $exgood - не определена - делаем ее не нулевой

            if (  isset($this->vars->sefnamefullcat)  )  $i24r->sefnamefullcat = $this->vars->sefnamefullcat;
            else if (  $this->vars->id==0  or  $exgood->parent!=$this->vars->parent  ){	// поменяли родителя - необходимо обновить информацию sefnamefullcat
                    if (  $this->vars->parent==0  ) $i24r->sefnamefullcat = '/'.$reg['ex_seoname'];
                    else { $papa = ggo (  $this->vars->parent, "#__excat"  ); $i24r->sefnamefullcat = $papa->sefnamefull.'/'.$papa->sefname; }
            }

            //if (  $i24r->id>0  ) { $exoldgood = ggo (  $i24r->id, "#__exgood"  ); $_REQUEST["imagelistorg"] = $exoldgood->imagesorg; $_REQUEST["imagelist"] = $exoldgood->images; }

            if (  $i24r->id==0  ){
                    if (  $p->SmartOrder==1  ){
                        $iexmaxorder = ggsql ("SELECT * FROM #__exgood WHERE parent=".$this->vars->parent." ORDER BY #__exgood.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
                        $i24r->order = $iexmaxorder[0]->order+1;
                    } else $i24r->order = 2478;
            }

            if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
            if (  $i24r->_db->_errorNum!=0  ) ggd(  $i24r->_db  ); // выполнено c ошибками

            $this->id = $i24r->id;  // сохранили новый ID
            
            // Сохраняем тэг
            if (  $p->saveTag==1  ){
                if($reg["exgoodAllowTags"] == 1){
                    $tag = new tags("exgood", $database, $reg);
                    $tag->id=$i24r->id;
                    if($i24r->publish == 0)  $tag->delete($i24r->id);
                    else                     $tag->apply_tag($this->vars->_tag_field);
                }
            }

            // сохраняем NAMES
            if (  $p->saveNames==1  ){
                $names = new names($i24r->id, "exgood", $reg);
                $names->apply_names($this->vars->_names_field);
            }

            if(  $p->updateGooodsCount==1  ){
                if (  $this->vars->id==0  or  $exgood->parent!=$this->vars->parent  ){ // поменяли родителя - необходимо обновить информацию о количестве детей у родителей
                        if (  $exgood->parent>0  ) excat_update_goods ( $exgood->parent );
                        if (  $this->vars->parent>0  ) excat_update_goods ( $this->vars->parent );
                }
            }
            ilog::vlog('function exgood::saveme */');
    }



    /**
     * УДАЛЕНИЕ ВСЕХ ТОВАРОВ И ПОДКАТЕГОРИЙ ПО ЗНАЧЕНИЮ ПОЛЯ order.
     *
     * @param <int> $order_val
     */
    function clean_by_order ($order_val){
        global $reg;
        if (  !$order_val  ) return;

        // получаем id категорий
        $query = " SELECT exgood.parent FROM #__exgood as exgood
                   WHERE exgood.order=$order_val
                   GROUP BY exgood.parent
        ";  // ggd ($query);
        $excats = ggsql ( $query );

        // удаляем все товары
        $query = " SELECT exgood.id FROM #__exgood as exgood
                   WHERE exgood.order=$order_val
        ";  // ggd ($query);
        $exgoods = ggsql ( $query );  //  ggd(  $exgoods  );
        /* настройка режима быстрого удаления (т.е. удаляются только товары без файлов, фото и прочих доп. аттрибутов)
        $delp->delfoto       = 0;
        $delp->delfile       = 0;
        $delp->deltags       = 0;
        $delp->delnames      = 0;
        $delp->delcomments   = 0;
        $delp->prefix_config = 0;
        */
        $exgood = new exgood();
        foreach ($exgoods as $exgoods1){
            $exgood->id = $exgoods1->id;
            $exgood->delme( 0 );
        }

       /*
        * УДАЛЯЕМ ВСЕ КАТЕГОРИИ
        */
       //ggd ($allcats);
       $excat = new excat();
       foreach (  $excats as $cat1  ){
            if (  $cat1->parent==0  ) continue; // корневую категорию не удаляем
            $excat->id = $cat1->parent;
            $excat->delme( 0, 1 );
       }

    }
	
}
?>