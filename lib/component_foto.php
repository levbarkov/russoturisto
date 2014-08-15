<?php
/*
 * используется для
 */
class component_foto {
	var $load_parent = 1;

        /** ДИРЕКТОРИЯ ФОТОК */
        var $fotocat = 0;

	var $component_for_save = "foto";
	var $current_foto_vars;	// содержит выгрузку из базы данных информации для текущего фото (запись с полями small, mid, org, full)

	/** создаем html код с фото превью и ссылкой на крупное изображение */
	function createPreviewFotoLink($type_small, $type_org,  &$foto_vars, $noimage='', $acode=' class="fancy"  ', $imgcode=' border="0" ', $link=''  ){
		global $reg;
		$fotoLink = '';
		if (  $link=='nolink'  ) $href = '';
		else if (  $link!=''  ) $href = $link;
		else if (  $foto_vars->$type_org!=''  ) $href = site_url.$this->url_prefix.$foto_vars->$type_org;

		if (  $foto_vars->$type_small!=''  ){
			if (  $href!=''  ){ $fotoLink .= '<a '.$acode.' href="'.$href.'">'; }
								$fotoLink .= '<img '.$imgcode.' src="'.site_url.$this->url_prefix.$foto_vars->$type_small.'" />';
			if (  $href!=''  ){ $fotoLink .= '</a>'; }
		} else {
			if (  $noimage==''  ) $noimage=$reg[$this->type.'_main_small_noimage'];
			if (  $href!=''  ){ $fotoLink .= '<a '.$acode.' href="'.$href.'">'; }
								$fotoLink .= '<img '.$imgcode.' src="'.$noimage.'" />';
			if (  $href!=''  ){ $fotoLink .= '</a>'; }
		}
		return $fotoLink;
	}
	function delmainfoto_checkbox(){
		?><td><input name="i24_delmainfoto" id="i24_delmainfoto" type="checkbox"  /></td><td><label for="i24_delmainfoto">&nbsp;Удалить изображение</label></td><?
	}
	function delmainfoto_ifUserSetChackBox(){
		global $reg;
		if (  isset($_REQUEST['i24_delmainfoto'])  ){
                    $this->delmainfoto();
		}
	}
	function delmainfoto(){
		global $reg;
                $this->load_parent();
                delfile ($this->dir.$this->parent_obj->small);
                delfile ($this->dir.$this->parent_obj->mid);
                delfile ($this->dir.$this->parent_obj->org);
                delfile ($this->dir.$this->parent_obj->full);

                // теперь обнулим в базе
                $i24r = new mosDBTable( $this->table_parent, "id", $reg['db'] );
                $i24r->id = $this->parent;
                $i24r->small = '';
                $i24r->mid   = '';
                $i24r->org   = '';
                $i24r->full  = '';
                if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	/** ФУНКЦИЯ ДЛЯ АДМИНКИ */
	function previewMainFoto ($noimage='', $acode=' class="highslide" onclick="return hs.expand(this)" ', $imgcode=' name="view_imagelist" id="view_imagelist" ' ){
		global $reg;
		if (  $this->parent_obj->small!=''  ){
			if (  $this->parent_obj->org!=''  ){ ?><a <?=$acode ?> href="<? print site_url.$this->url_prefix.$this->parent_obj->org ?>"><? }
			?><img <?=$imgcode ?> src="<? print site_url.$this->url_prefix.$this->parent_obj->small ?>" border="0" /><?
			if (  $this->parent_obj->org!=''  ){ ?></a><? }
		} else {
			if (  $noimage==''  ) $noimage=$reg[$this->type.'_main_small_noimage'];
			?><img <?=$imgcode ?> src="<?=$noimage ?>" /><? }
	}

	function savefoto_foto($task){
		global $reg;

                $this->loadImageSizes();

		if (  $_REQUEST['ret_url']!=''  ) 	$i24r = new mosDBTable( $this->table_parent, "id", $reg['db'] );
		else 								$i24r = new mosDBTable( "#__foto", "id", $reg['db'] );

		if (  $this->file_crop  ){
			if (  $task=='apply_store'  or  $task=='save_store'  or  $task==''  ){	// УДАЛЕНИЕ СТАРЫХ ФОТО
				if (  $_REQUEST['ret_url']!=''  ) 	$ithisfoto = ggo ($this->id, $this->table_parent);
				else 					$ithisfoto = ggo ($this->id, "#__foto");
				delfile( $this->dir.$ithisfoto->small ); delfile( $this->dir.$ithisfoto->mid ); delfile( $this->dir.$ithisfoto->org ); delfile( $this->dir.$ithisfoto->full );
			}
			if (  $this->small_use!=0  ){	// необходимо создать уменьшенное фото
				$cropper_small = new foto_crop();
				// учет режима auto - когда берется вся ширина или высота фото
					if (  $this->small_h_mode=='auto'  ) $this->small_h = round( $_REQUEST['preview_small_h_max'] );
					if (  $this->small_w_mode=='auto'  ) $this->small_w = round( $_REQUEST['preview_small_w_max'] );
				$cropper_small->config_w = $this->small_w;
				$cropper_small->config_h = $this->small_h;
				$cropper_small->zoom_ifsmall = $this->small_zoom_ifsmall;
				$cropper_small->config->jpeg_quality = $this->small_quality;
				$cropper_small->config->type = $this->small_type;
				$cropper_small->make_foto($this->file_crop, $this->prefix_small, $this->dir);
				$i24r->small = $cropper_small->handle->file_dst_name;
			} else $i24r->small = "";
			if (  $this->mid_use!=0  ){	// необходимо создать среднее фото
				$cropper_mid = new foto_crop();
				// учет режима auto - когда берется вся ширина или высота фото
					if (  $this->mid_h_mode=='auto'  ) $this->mid_h = round( $_REQUEST['preview_mid_h_max'] );
					if (  $this->mid_w_mode=='auto'  ) $this->mid_w = round( $_REQUEST['preview_mid_w_max'] );
				$cropper_mid->config_w = $this->mid_w;
				$cropper_mid->config_h = $this->mid_h;
				$cropper_mid->zoom_ifsmall = $this->mid_zoom_ifsmall;
				$cropper_mid->config->jpeg_quality = $this->mid_quality;
				$cropper_mid->config->type = $this->mid_type;
				$cropper_mid->make_foto($this->file_crop, $this->prefix_mid, $this->dir);
				$i24r->mid = $cropper_mid->handle->file_dst_name;
			} else $i24r->mid = "";
			if (  $this->org_use!=0  ){	// необходимо создать основное фото
				$cropper_org = new foto_crop();
				// учет режима auto - когда берется вся ширина или высота фото
					if (  $this->org_h_mode=='auto'  ) $this->org_h = round( $_REQUEST['preview_org_h_max'] );
					if (  $this->org_w_mode=='auto'  ) $this->org_w = round( $_REQUEST['preview_org_w_max'] );
				$cropper_org->config_w = $this->org_w;
				$cropper_org->config_h = $this->org_h;
				$cropper_org->zoom_ifsmall = $this->org_zoom_ifsmall;
				$cropper_org->config->jpeg_quality = $this->org_quality;
				$cropper_org->config->type = $this->org_type;
				$cropper_org->make_foto($this->file_crop, $this->prefix_org, $this->dir);
				$i24r->org = $cropper_org->handle->file_dst_name;
			} else $i24r->org = "";
			if (  $this->full_use!=0  ){	// необходимо создать гиганское фото
				$cropper_full = new foto_crop();
				$cropper_full->get_sizes (0, $this->full_w, $this->full_h, $this->file_crop); //ggr ($cropper_full);
				$cropper_full->config->image_x = $cropper_full->new_image_width; //$this->full_width;
				$cropper_full->config->image_y = $cropper_full->new_image_height;//$this->full_height;
				$cropper_full->zoom_ifsmall = $this->full_zoom_ifsmall;
				$cropper_full->config->jpeg_quality = $this->full_quality;
				$cropper_full->config->type = $this->full_type;
				$cropper_full->make_foto_full($this->file_crop, $this->dir);
				$i24r->full = $cropper_full->handle->file_dst_name;
			} else $i24r->full = "";

			// секция копирования файлов (ex. mid_copy='small')
			if (  $this->mid_copy!=''  ){// необходимо создать копию
				$field = $this->mid_copy;
				$file = $i24r->$field;

				$cropper_copy = new foto_crop();
				$cropper_copy->copy_foto($file, $this->dir);
				$i24r->mid = $cropper_copy->handle->file_dst_name;
			}

			// ПРИМЕНЯЕМ ЭФФЕКТЫ К ИЗОБРАЖЕНИЯМ
			$cropper_effect = new foto_crop();
			if (  $this->small_effect!=''  )	$cropper_effect->make_effect($i24r->small, 	$this->dir, $this->small_effect	);
			if (  $this->mid_effect!=''  )		$cropper_effect->make_effect($i24r->mid, 	$this->dir, $this->mid_effect	);
			if (  $this->org_effect!=''  )		$cropper_effect->make_effect($i24r->org, 	$this->dir, $this->org_effect	);
			if (  $this->full_effect!=''  )		$cropper_effect->make_effect($i24r->full, 	$this->dir, $this->full_effect	);

			delfile (  $this->file_crop  );
		}
		if (  $task=='apply_store'  or  $task=='save_store'  )	$i24r->id = $this->id;
		else if (  $task=='newfoto_store'  )	$i24r->id = 0;
		if (  $_REQUEST['ret_url']==''  ){	// сохраняем в нашей базе foto => необходимо считать все остальные поля
			$i24r->parent = $this->parent;
			$i24r->fotocat = $this->fotocat;
			$i24r->type = $this->type;
			$i24r->link = $this->link;
			$i24r->name = ggrr ('name');	if (  $i24r->name=="Название фото (не обязательное)"  )	$i24r->name="";
			$i24r->desc = ggrr ('desc');	if (  $i24r->desc=="Описание фото (не обязательное)"  )	$i24r->desc="";
			
			
			#xmp($this); exit();
			
			
			
			
			if (  $task=='newfoto_store'  ){
				// определение максимального значения для ordering
				$ordering = ggsql ("SELECT * FROM #__foto WHERE parent=".$i24r->parent." AND type='".$i24r->type."' AND link='".$i24r->link."' AND fotocat=".$i24r->fotocat." ORDER BY #__foto.ordering DESC LIMIT 0,1 "); // ggtr ($ordering); ggdd();
				if (  isset($ordering[0]->ordering)  ) $i24r->ordering = $ordering[0]->ordering+1; else $i24r->ordering=1;
			}
		} else $i24r->id = $this->parent;	// на всякий случай еще раз прописать ID

                if (  $this->publish!='dont_save_publish' )   $i24r->publish = $this->publish;

		if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else  $i24r->store();

		$adminlog = new adminlog();
		$adminlog->logme('save_foto', $this->parent_component_name, isset($this->parent_obj->name) ? $this->parent_obj->name : '', $this->parent_obj->id );

                //names
                $names = new names($i24r->id, 'fotoname'.$this->type, $reg);
                $names->apply_names(  ggrr('_names_field')  );

		//// ggr ($i24r); ggr ($this); ggdd(); return;
		if (  $_REQUEST['ret_url']!=''  ){
			$msg = $_REQUEST['ret_msg'];	mosRedirect( $_REQUEST['ret_url'], $msg );
			return;
		}
		//ggd($_POST);
		if (  isset($_REQUEST['fnum'])  ) $this->name = $_REQUEST['fnum'];
		switch ( $task ) {
			case 'apply_store':
				$msg = 'Фото №'.$this->name.' сохранено';	mosRedirect( 'index2.php?ca=foto&type='.$this->type.'&parent='.$this->parent.'&fotocat='.$this->fotocat.'&cid[]='.$this->id.'&task=edit', $msg );
				break;
			case 'save_store':
				$msg = 'Фото №'.$this->name.' сохранено';	mosRedirect( 'index2.php?ca=foto&type='.$this->type.'&parent='.$this->parent.'&fotocat='.$this->fotocat, $msg );
				break;
			case 'newfoto_store':
				$msg = 'Новое фото сохранено: ';			mosRedirect( 'index2.php?ca=foto&type='.$this->type.'&parent='.$this->parent.'&fotocat='.$this->fotocat, $msg );
				break;
		}

	}


	function load_fotos ( $limitstart=0, $limit=0  ){
                global $reg;
                if (  !$this->parent  ||  $this->type==''  ) return;

                $limit_sql = '';
                if (  $limit>0  ) $limit_sql = " LIMIT $limitstart, $limit ";

		$foto_parent = $this->parent;
		$foto_type = $this->type;
		$fotocat = $this->fotocat;
		$this->foto_total                         = ggsqlr (  "SELECT COUNT(a.id) FROM #__foto AS a WHERE a.parent=".$foto_parent." AND a.type='".$foto_type."' AND a.fotocat=".$fotocat."; "  );
                if (  $this->foto_total>0  ) $this->fotos = ggsql  (  "SELECT *           FROM #__foto AS a WHERE a.parent=".$foto_parent." AND a.type='".$foto_type."' AND a.fotocat=".$fotocat." ORDER BY a.ordering ASC $limit_sql ; ");
	}
	function del_fotos (){
		$foto_parent = $this->parent;
		$foto_type = $this->type;
		$foto_total = ggsqlr (  "SELECT COUNT(a.id) FROM #__foto AS a WHERE a.parent=".$foto_parent." AND a.type='".$foto_type."'; "  );
		if (  $foto_total>0  ){
			$fotos = 		ggsql  (  "SELECT *     FROM #__foto AS a WHERE a.parent=".$foto_parent." AND a.type='".$foto_type."'; ");  //ggtr ($database);
			foreach ($fotos as $foto){
				delfile ($this->dir.$foto->small);
				delfile ($this->dir.$foto->mid);
				delfile ($this->dir.$foto->org);
				delfile ($this->dir.$foto->full);
				ggsqlq ("DELETE FROM #__foto WHERE id=".$foto->id);
			}
		}
		if (  $this->parent_obj->id  ){
			delfile ($this->dir.$this->parent_obj->small);
			delfile ($this->dir.$this->parent_obj->mid);
			delfile ($this->dir.$this->parent_obj->org);
			delfile ($this->dir.$this->parent_obj->full);
		}

	}

	function foto_foto_crop ( $cid, $task ){
		global $reg;
		?><form name="adminForm"  action="index2.php" method="post"><?
		if (  $_FILES["newfoto"]['tmp_name']  ||  $_REQUEST['furl']!=''  ){
			$ithisgood = ggo ($this->parent, $this->table_parent);
                        $file_tmp = $_FILES['newfoto']['tmp_name'];
                        $this->fileCropName = sefname($_FILES['newfoto']['name']);

                        // проверка директории
                        if(  !is_dir($this->dir.'tmp/')  )  mkdir($this->dir.'tmp/');
                        /*
                         * КОПИРУЕМ ФАЙЛ
                         */
                        if (!defined( 'CLASS_UPLOAD' )) { include(site_path.'/includes/class.upload/class.upload.php');	define( 'CLASS_UPLOAD', 1 ); }
                        ini_set("max_execution_time",0);

                        $handle = new Upload(  $file_tmp  );
                        if ($handle->uploaded) {    // then we check if the file has been "uploaded" properly in our case, it means if the file is present on the local file system
                                $fileinfo = pathinfo(  $this->fileCropName  );
                                $handle->file_new_name_body = $fileinfo['filename'];
                                $handle->file_new_name_ext  = $fileinfo['extension'];
                                $handle->mime_check = false;
                                $handle->Process( $this->dir.'tmp/' );
                        } else ggd('component_foto.php # ОШИБКА 1 - НЕМОГУ СКОПИРОВАТЬ ВРЕМЕННЫЙ ФАЙЛ В ДИРЕКТОРИЮ /tmp/');

                        if (  $_REQUEST['furl']!=''  ) // ФАЙЛ СКОПИРОВАН, НЕОБХОДИМО ВРЕМЕННЫЙ - УДАЛИТЬ
                            delfile(  $_FILES['newfoto']['tmp_name']  );

                        if (  $handle->file_dst_name!=''  ){
                            $this->fileCropName = $handle->file_dst_name;
                            $file_crop = $this->dir.'tmp/'.$this->fileCropName;
                        } else ggd('component_foto.php # ОШИБКА 2 - НЕМОГУ СКОПИРОВАТЬ ВРЕМЕННЫЙ ФАЙЛ В ДИРЕКТОРИЮ /tmp/');

			foreach ( $this->cropper as $i=>$cropper ){
                                $this->cropper[$i]->file_crop_url = $this->url_prefix.'tmp/'.$this->fileCropName;
				$this->cropper[$i]->make_java_code();
			}

			?><table class="adminheading"><tr><td class="edit"><?
					$iway[0]->name=$this->parent_component_name;
					$iway[0]->url="";
					$iway[1]->name=$ithisgood->name;
					$iway[1]->url="";
					$iway[2]->name="Фото ".$this->fnum;
					$iway[2]->url="";
					$iway[3]->name="Выделение области";
					$iway[3]->url="";
					i24pwprint_admin ($iway, 0);
			?></td></tr></table><?
			foreach ( $this->cropper as $i=>$cropper ){
				$this->cropper[$i]->make_crop_table ();
				?><table class="clear_tr"><tr><td class="edit">&nbsp;<br />&nbsp;</td></tr></table><?
			}
			?><INPUT type="hidden" name="file_crop" value="<?=$file_crop ?>"><?
		}
		?><input type="hidden" name="fotocat" value="<?php echo $this->fotocat; ?>" />
		<input type="hidden" name="desc" value="<?=$_REQUEST['desc'] ?>"  />
		<input type="hidden" name="name" value="<?=$_REQUEST['name'] ?>"  />
		<input type="hidden" name="link" value="<?=$_REQUEST['link'] ?>"  />
		<input type="hidden" name="id" value="<?=$_REQUEST['id'] ?>"  />
		<input type="hidden" name="task" value="<? if (  $_REQUEST['task']=='newfoto'  ) print 'newfoto_store'; else if (  $_REQUEST['task']=='apply'  ) print 'apply_store'; else if (  $_REQUEST['task']=='save'  ) print 'save_store'; ?>"  />
		<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
		<input type="hidden" name="parent" value="<?php echo $this->parent; ?>" />
		<input type="hidden" name="ca" value="<?=$_REQUEST['ca'] ?>" />
		<input type="hidden" name="c" value="<?=$_REQUEST['ca'] ?>" />
		<input type="hidden" name="fnum" value="<?=$_REQUEST['fnum'] ?>" />
		<input type="hidden" name="ret_url" value="<?=$_REQUEST['ret_url'] ?>" />
		<input type="hidden" name="ret_msg" value="<?=$_REQUEST['ret_msg'] ?>" />
                <input type="hidden" name="_names_field" value="<?=$_REQUEST['_names_field'] ?>" />
                <input type="hidden" name="publish" value="<?=$_REQUEST['publish'] ?>" />
		</form><?
		
			//xmp ($_REQUEST); exit();
		
		// фото не изменяли, значит и нам здесь делать нечего
		if (  !$_FILES["newfoto"]['tmp_name']  ){	?><script language="javascript"> document.adminForm.submit(); </script><? }
	}
	function external_foto($ret_url, $ret_msg){
		global $reg;

		$_REQUEST['ca'] = $this->component_for_save;
		$_REQUEST['type'] = $this->type;
		if (  $this->id==0  ) $_REQUEST['task'] = "newfoto";
		else $_REQUEST['task'] = "save";
		$_REQUEST['parent'] = $this->parent;
		$_REQUEST['id'] = $this->parent;
		$_REQUEST['ret_url'] = $ret_url;
		$_REQUEST['ret_msg'] = $ret_msg;
		require_once(site_path."/iadmin/component/foto/admin.foto.php");

	}
	function get_link (){
		return "?ca=foto&type=$this->type&parent=$this->parent&fotocat=$this->fotocat";
	}

        function make_galery_link(){
            ?><a target="_blank" href="<?=$this->get_link() ?>">Редактировать</a><?
        }
	function get_fotos($limitstart = 0, $limit = 0){
        if (!$this->parent)
			return;
		return ggsql("SELECT * FROM #__foto WHERE parent=".$this->parent." AND fotocat=$this->fotocat AND type='$this->type' ORDER BY ordering ", $limitstart, $limit);
	}
	function get_1stfoto($order = " ORDER BY ordering "){
                if (  !$this->parent  ) return false;
		$ret = ggsql("SELECT * FROM #__foto WHERE parent=".$this->parent." AND fotocat=$this->fotocat AND type='$this->type' $order limit 0,1; ");
		if (  count($ret)>0  ) return $ret[0];
		else return false;
	}
	function howmany_fotos (){
		$total_fotos =  ggsqlr ("SELECT count(id) FROM #__foto WHERE parent=".$this->parent." AND type='".$this->type."'; ");
		if (  $total_fotos==''  ) return 0;
		else return $total_fotos;
	}

	function  __construct( $load_parent=1 ) {
		$this->load_parent = $load_parent;
	}

	function load_parent (  &$parent_obj=NULL  ){
		if (  $parent_obj!=NULL  ) $this->parent_obj = $parent_obj;
		else if (  $this->parent>0  ){
			$this->parent_obj = ggo (  $this->parent, $this->table_parent, $this->table_parent_id_field  );
			if (  $this->type=="content"  ||  $this->type=="content_main"  ||  $this->type=="typedcontent"  ||  $this->type=="typedcontent_main"  ) $this->parent_obj->name = $this->parent_obj->title;
		}
	}
	function copy_main_foto (  $parent_new  ){
		global $reg;

		$new_small=$new_mid=$new_org=$new_full="";
		if (  $this->parent_obj->small!=''  ){
			$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($this->parent_obj->small, $this->dir);
			$new_small = $cropper_copy->handle->file_dst_name;
		}
		if (  $this->parent_obj->mid!=''  ){
			$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($this->parent_obj->mid, $this->dir);
			$new_mid = $cropper_copy->handle->file_dst_name;
		}
		if (  $this->parent_obj->org!=''  ){
			$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($this->parent_obj->org, $this->dir);
			$new_org = $cropper_copy->handle->file_dst_name;
		}
		if (  $this->parent_obj->full!=''  ){
			$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($this->parent_obj->full, $this->dir);
			$new_full = $cropper_copy->handle->file_dst_name;
		}

		// теперь сохраним в базе
		$i24r = new mosDBTable( $this->table_parent, "id", $reg['db'] );
		$i24r->id = $parent_new;
		$i24r->small = $new_small;
		$i24r->mid   = $new_mid;
		$i24r->org   = $new_org;
		$i24r->full  = $new_full;
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}

	function copy_fotos (  $parent_new  ){
		global $reg;
		$fotocats = $this->get_fotos();
		//$exgoodfotos = ggsql( "SELECT * FROM #__foto WHERE type='exgood' parent=".$exgood->id );
		foreach ($fotocats as $foto){

			$new_small=$new_mid=$new_org=$new_full="";
			if (  $foto->small!=''  ){
				$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($foto->small, $this->dir);
				$new_small = $cropper_copy->handle->file_dst_name;
			}
			if (  $foto->mid!=''  ){
				$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($foto->mid, $this->dir);
				$new_mid = $cropper_copy->handle->file_dst_name;
			}
			if (  $foto->org!=''  ){
				$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($foto->org, $this->dir);
				$new_org = $cropper_copy->handle->file_dst_name;
			}
			if (  $foto->full!=''  ){
				$cropper_copy = new foto_crop();	$cropper_copy->copy_foto($foto->full, $this->dir);
				$new_full = $cropper_copy->handle->file_dst_name;
			}

			// теперь сохраним в базе новую фото
			$i24r = new mosDBTable( "#__foto", "id", $reg['db'] );
			$i24r->id = 0;
			$i24r->parent   = $parent_new;
			$i24r->name     = $foto->name;
			$i24r->desc     = $foto->desc;
			$i24r->ordering = $foto->ordering;
			$i24r->publish  = $foto->publish;
			$i24r->fotocat  = $foto->fotocat;
			$i24r->small = $new_small;
			$i24r->mid   = $new_mid;
			$i24r->org   = $new_org;
			$i24r->full  = $new_full;
			$i24r->type  = $foto->type;
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

			//$foto = new foto_foto($exgoodfoto->id, "#__foto");
			//$foto->makecopy( $iexgoodnewID );
		}
	}

	function get_size_for_auto_mode($type, $storona){
		// режим auto - т.е. ширина или высота расчитывается в зависимости от размеров текущего загруженного фото
		$type_h = $type.'_h';
		$type_w = $type.'_w';
		$type_storona 		= $type.'_'.$storona;
		$type_storona_mode 	= $type.'_'.$storona.'_mode';

		if (  $this->$type_storona=='auto'  ){
			$this->$type_storona_mode='auto';
			if (  $storona=='w'  )	$this->$type_w = $this->$type_h;
			else 					$this->$type_h = $this->$type_w;
			// если фото загруженно - берем реальные размеры с фото
			if (  isset($_FILES['newfoto']['tmp_name'])  ){
				$imginfo = getimagesize($_FILES['newfoto']['tmp_name']);
				$imginfo[0] = abs($imginfo[0]);
				$imginfo[1] = abs($imginfo[1]);
				if (  $storona=='w'  )	$this->$type_w = $this->$type_h*$imginfo[0]/$imginfo[1]; // width / height
				else 					$this->$type_h = $this->$type_w*$imginfo[1]/$imginfo[0]; // height / width

			}
		} else $this->$type_storona_mode='fix';
	}
	/** загрузка параметров по умолчанию из $_REQUEST */
        function default_init(){
		$this->id 		= ggri('id', $_REQUEST['cid'][0]);
		$this->parent 		= ggri("parent");
        $this->publish 		= ggri("publish");
		$this->desc 		= ggrr("desc");
		$this->link 		= ggrr("link");
		$this->file_crop 	= ggrr("file_crop");
        $this->fotocat 		= ggri('fotocat');
        }

        /** инициализация основных переменных, которые определяются типом фото */
	function init( $type, &$parent_obj=NULL ){
		global $reg;

		$this->prefix_small     = 'preview_small';
		$this->prefix_mid 	= 'preview_mid';
		$this->prefix_org 	= 'preview_org';
		$this->type 		= $type;
		$this->table_parent_id_field = "id";

		if (  $type=="file"  ){
			$this->table_parent = "#__file";
			$this->parent_component_name = $reg['file_name'];
			$this->dir = site_path."/images/files/fotos/";
			$this->url_prefix = "/images/files/fotos/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
                                $this->icatway[0]->url='index2.php?ca=file&type='.$this->parent_obj->type.'&parent='.$this->parent_obj->parent.'&filecat=0&task=edit&cid[]='.$this->parent_obj->id;
                                $this->icatway[0]->name='Файл';
			}
		}
		if (  $type=="names_main"  ||  $type=="names"  ){
			$this->table_parent = "#__names";
			$this->parent_component_name = $reg['names_name'];
			$this->dir = site_path."/images/names/";
			$this->url_prefix = "/images/names/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				//$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, "exgood");
			}
		}
		if (  $type=="names_prop_main"  ||  $type=="names_prop"  ){
			$this->table_parent = "#__names_prop";
			$this->parent_component_name = $reg['names_name'];
			$this->dir = site_path."/images/names/";
			$this->url_prefix = "/images/names/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				//$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, "exgood");
			}
		}
		
                // ---- Sh ----
		if ($type == 'video'){
			$this->table_parent = "#__video";
			$this->parent_component_name = "Видео";
			$this->dir = site_path . "/images/video/img/";
			$this->url_prefix = "/images/video/img/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				//$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, "Видео" , 1, $this->type);
			}
		}
		
		if ($type == 'partners'){
			$this->table_parent = "#__partners";
			$this->parent_component_name = "Наши партнеры";
			$this->dir = site_path . "/images/partners/";
			$this->url_prefix = "/images/partners/";
			if ($this->load_parent){
				$this->load_parent ($parent_obj);
				//$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, "Видео" , 1, $this->type);
			}
		}

		if (  $type=="exgood"  ||  $type=="exgood_main"  ){
			$this->table_parent = "#__exgood";
			$this->parent_component_name = $reg['ex_name'];
			$this->dir = site_path."/images/ex/good/";
			$this->url_prefix = "/images/ex/good/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, "exgood");
			}
		}
		if (  $type=="excat"  ){
			$this->table_parent = "#__excat";
			$this->parent_component_name = $reg['ex_name'];
			$this->dir = site_path."/images/ex/cat/";
			$this->url_prefix = "/images/ex/cat/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, $this->type);
			}
		}
		if (  $type=="content"  ||  $type=="content_main"  ){
			$this->table_parent = "#__content";
			$this->parent_component_name = $reg['content_name'];
			$this->dir = site_path."/images/icat/icont/";
			$this->url_prefix = "/images/icat/icont/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, $reg['content_name'] , 1, $this->type);
			}
		}
		if (  $type=="typedcontent"  or  $type=="typedcontent_main"  ){
			$this->table_parent = "#__content";
			$this->parent_component_name = $reg['typedcontent_name'];
			$this->dir = site_path."/images/icat/icont/";
			$this->url_prefix = "/images/icat/icont/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, $reg['typedcontent_name'] , 1, $this->type);
			}
		}

		if (  $type=="icat"  ){
			$this->table_parent = "#__icat";
			$this->parent_component_name = $reg['content_name'];
			$this->dir = site_path."/images/icat/icat/";
			$this->url_prefix = "/images/icat/icat/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, $reg['content_name'] , 1, $this->type);
			}
		}
		if (  $type=="exfoto"  ){
			$this->table_parent = "#__exfoto";
			$this->parent_component_name = $reg['exfoto_name'];
			$this->dir = site_path."/images/foto/";
			$this->url_prefix = "/images/foto/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array($this->parent_obj, "#__exfoto", "parent", "index2.php?ca=".$this->type, $reg['exfoto_name'], 1, $this->type);
			}
		}
		if (  $type=="user_main"  ){
			$this->table_parent = "#__users";
			$this->parent_component_name = $reg['cab_name'];
			$this->dir = site_path."/images/cab/logo/";
			$this->url_prefix = "/images/cab/logo/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
			}
		}

	}

        /** Загрузить размеры фото */
        function loadImageSizes(){
            global $reg;
            $type = $this->type;

                /*
		 * def предназначенна только для работы с РЕЕСТРОМ - $reg
		 * def (param1, param2) - если определен param1 - то возвращает param1, иначе param2
		 */

		/*
		 * def_request предназначенна только для работы с $_REQUEST
		 * def_request (name, val) - если определен $_REQUEST['name'] - то возвращает $_REQUEST['name'], иначе val
		 */

                /*
                 * для файлов префикс другой, например: #__file_IDcontent103__file_small_w, добавлен тип файла между ID и числом  - IDcontent103
                 * делаем уточнение префикса для файлов
                 */
                if(  $this->type=='file'  ) $this->prefix_id = $this->parent_obj->type.$this->parent;
		else                        $this->prefix_id = $this->parent;

                $gen_name = '#__foto_cat_'.$this->type.'_'.$this->parent.'_ID'.$this->fotocat;
                $pos = strpos($type, "_main");
                if (  !($pos===false)  ){  // имеем дело с основным фото, поэтому и префикс к параметрам - другой, пример - #__content_ID5__small_w
                    $foto_type = substr($this->type, 0, $pos);
                    if (  $foto_type=='typedcontent') $foto_type = 'content';
                    $gen_name = '#__'.$foto_type.'_ID'.$this->parent;
					
					/* Дополнение от Ч.Анатолия sdneo
					 * 
					 * 	fun_icat_id => Использовать настройки для вложенных статей с основным фото
					 * */
					if(  $reg['iadmin'] == '1'  )
					{
						// icat
						$gen_name_new = "#__icat_ID".$this->parent_obj->catid;
						if( $reg[$gen_name_new.'__fun_icat_id'] == 1 ){ $gen_name = $gen_name_new; }

						// UP icat
						$up_icat = ggsql(" select parent from #__icat where id='{$this->parent_obj->catid}' limit 1 ");
						if($up_icat)
						{
							$gen_name_new = "#__icat_ID".$up_icat[0]->parent; 
							if( $reg[$gen_name_new.'__fun_icat_id'] == 1 ){ $gen_name = $gen_name_new; }
						}
					}
					/* \Дополнение от Ч.Анатолия sdneo */
                }
				
				//xmp($gen_name);
				
				

		$this->small_use = 		def(            $reg[$gen_name.'__small_use'],            $reg[$type.'_small_use']                                                                     );
		$this->small_w = 		def(            $reg[$gen_name.'__small_w'],              $reg[$type.'_small_w']                                                                       );
		$this->small_h = 		def(            $reg[$gen_name.'__small_h'],              $reg[$type.'_small_h']                                                                       );
		$this->small_watermark=         def(            $reg[$gen_name.'__small_watermark'],      $reg[$type.'_small_watermark']                                                               );
		$this->small_select= 		def(       def( $reg[$gen_name.'__small_select'],         $reg[$type.'_small_select']),			"auto"                                         );
		$this->small_quality =		def(	   def( $reg[$gen_name.'__small_quality'],	  $reg[$type.'_small_quality']),                90                                             );
		$this->small_zoom_ifsmall =	def(       def( $reg[$gen_name.'__zoom_ifsmall'],         $reg[$type.'_zoom_ifsmall']),			0                                              );
		$this->small_copy =		def(	   def( $reg[$gen_name.'__small_copy'],		  $reg[$type.'_small_copy']),			""                                             );
		$this->small_type =		def_request(    $this->prefix_small.'_type',            def($reg[$gen_name.'__small_type'],	$reg[$type.'_small_type'])                             );
		$this->small_effect =		def_request(    $this->prefix_small.'_effect',		def($reg[$gen_name.'__small_effect'],	$reg[$type.'_small_effect'])                           );
		// учет режима auto - когда берется вся ширина или высота фото
		$this->get_size_for_auto_mode('small', 'w'); 	$this->get_size_for_auto_mode('small', 'h');

		$this->mid_use = 		def(            $reg[$gen_name.'__mid_use'],              $reg[$type.'_mid_use']                                                       );
		$this->mid_w = 			def(            $reg[$gen_name.'__mid_w'],                $reg[$type.'_mid_w']                                                         );
		$this->mid_h = 			def(            $reg[$gen_name.'__mid_h'],                $reg[$type.'_mid_h']                                                         );
		$this->mid_watermark =          def(            $reg[$gen_name.'__mid_watermark'],        $reg[$type.'_mid_watermark']                                                 );
		$this->mid_select= 		def(	   def( $reg[$gen_name.'__mid_select'],		  $reg[$type.'_mid_select']),			"auto"                         );
		$this->mid_quality=             def(	   def( $reg[$gen_name.'__mid_quality'],	  $reg[$type.'_mid_quality']),			90                             );
		$this->mid_zoom_ifsmall=        def(	   def( $reg[$gen_name.'__zoom_ifsmall'],	  $reg[$type.'_zoom_ifsmall']),			0                              );
		$this->mid_copy =		def(	   def( $reg[$gen_name.'__mid_copy'],		  $reg[$type.'_mid_copy']),			""                             );
		$this->mid_type =		def_request(    $this->prefix_mid.'_type',		def($reg[$gen_name.'__mid_type'],	$reg[$type.'_mid_type'])	       );
		$this->mid_effect =		def_request(    $this->prefix_mid.'_effect',	        def($reg[$gen_name.'__mid_effect'],	$reg[$type.'_mid_effect'])             );
		// учет режима auto - когда берется вся ширина или высота фото
		$this->get_size_for_auto_mode('mid', 'w'); 	$this->get_size_for_auto_mode('mid', 'h');

		$this->org_use = 		def(            $reg[$gen_name.'__org_use'],              $reg[$type.'_org_use']                                                       );
		$this->org_w = 			def(            $reg[$gen_name.'__org_w'],                $reg[$type.'_org_w']                                                         );
		$this->org_h = 			def(            $reg[$gen_name.'__org_h'],                $reg[$type.'_org_h']                                                         );
		$this->org_watermark =          def(            $reg[$gen_name.'__org_watermark'],        $reg[$type.'_org_watermark']                                                 );
		$this->org_select= 		def(	   def( $reg[$gen_name.'__org_select'],		  $reg[$type.'_org_select']),			"auto"                         );
		$this->org_quality=             def(	   def( $reg[$gen_name.'__org_quality'],	  $reg[$type.'_org_quality']),			75                             );
		$this->org_zoom_ifsmall=        def(       def( $reg[$gen_name.'__zoom_ifsmall'],	  $reg[$type.'_zoom_ifsmall']),			0                              );
		$this->org_copy =		def(	   def( $reg[$gen_name.'__org_copy'],		  $reg[$type.'_org_copy']),			""                             );
		$this->org_type =		def_request(    $this->prefix_org.'_type',		def($reg[$gen_name.'__org_type'],            $reg[$type.'_org_type'])          );
		$this->org_effect =		def_request(    $this->prefix_org.'_effect',            def($reg[$gen_name.'__org_effect'],          $reg[$type.'_org_effect'])        );
		// учет режима auto - когда берется вся ширина или высота фото
		$this->get_size_for_auto_mode('org', 'w'); 	$this->get_size_for_auto_mode('org', 'h');

		$this->full_use = 		def(            $reg[$gen_name.'__full_use'],             $reg[$type.'_full_use']                                              );
		$this->full_w = 		def(            $reg[$gen_name.'__full_w'],               $reg[$type.'_full_w']                                                );
		$this->full_h =			def(            $reg[$gen_name.'__full_h'],               $reg[$type.'_full_h']                                                );
		$this->full_watermark =         def(            $reg[$gen_name.'__full_watermark'],       $reg[$type.'_full_watermark']                                        );
		$this->full_quality =           def(	   def( $reg[$gen_name.'__full_quality'],	  $reg[$type.'_full_quality']),			60                     );
		$this->full_zoom_ifsmall=       def(	   def( $reg[$gen_name.'__zoom_ifsmall'],	  $reg[$type.'_zoom_ifsmall']),			0                      );
		$this->full_copy =		def(	   def( $reg[$gen_name.'__full_copy'],		  $reg[$type.'_full_copy']),			""                     );
		$this->full_type =		def(       def( $reg[$gen_name.'__full_type'],		  $reg[$type.'_full_type']),			'jpg'                  );
		$this->full_effect =            def_request(    $this->prefix_org.'_effect',            def($reg[$gen_name.'__full_effect'],	$reg[$type.'_full_effect'])    );

        }
}
?>