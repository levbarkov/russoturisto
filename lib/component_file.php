<?php
/* 
 * используется для прикрепления файлов
 */
class component_file {
        /** автоматическая загрузка parent_obj при вызове init() */
	var $load_parent = 1;

        /** ДИРЕКТОРИЯ ФАЙЛОВ */
        var $filecat = 0;

	var $component_for_save = "file";

        /** содержит выгрузку из базы данных информации для текущего фото (запись из таблицы #__file) */
	var $current_file_vars;

        /** массив файлов */
        var $files = array();

        /** количество файлов */
        var $file_total = 0;

        /** массив расширений, на которые имеется фото, для остальных выводится фото по-умлочанию (default.png) */
        var $ext=array();

        /**
         * генерируем уникальное имя файла
         * $fname - имя файла, включаю путь до файла
         * @param <string> $fname
         */
        function makeUniqName($fname){
            $fileinfo = pathinfo(  $fname  );
            mt_srand();
            return '_'.time().md5("aa".mt_rand(1000, 100000)."akeUni").'.'.$fileinfo['extension'];
        }
	
	/** создаем html код с фото превью и ссылкой на крупное изображение */
	function createDownloadLink($type_small, $type_org,  &$file_vars, $noimage='', $acode=' class="fancy"  ', $imgcode=' border="0" ', $link=''  ){
		global $reg;
		$fileLink = '';
		if (  $link=='nolink'  ) $href = '';
		else if (  $link!=''  ) $href = $link;
		else if (  $file_vars->$type_org!=''  ) $href = site_url.$this->url_prefix.$file_vars->$type_org;
		
		if (  $file_vars->$type_small!=''  ){
			if (  $href!=''  ){ $fileLink .= '<a '.$acode.' href="'.$href.'">'; }
								$fileLink .= '<img '.$imgcode.' src="'.site_url.$this->url_prefix.$file_vars->$type_small.'" />';
			if (  $href!=''  ){ $fileLink .= '</a>'; }
		} else {
			if (  $noimage==''  ) $noimage=$reg[$this->type.'_main_small_noimage'];
			if (  $href!=''  ){ $fileLink .= '<a '.$acode.' href="'.$href.'">'; }
								$fileLink .= '<img '.$imgcode.' src="'.$noimage.'" />';
			if (  $href!=''  ){ $fileLink .= '</a>'; }
		}
		return $fileLink;
	}
        /** загрузка всех фото для данного объекта в переменную files, в files_total - записываем количество фото
         *
         * @param <int> $limitstart
         * @param <int> $limit
         */
	function load_files (  $limitstart=0, $limit=0  ){
            global $reg;
            if (  !$this->parent  ||  $this->type==''  ) return;

            $limit_sql = '';
            if (  $limit>0  ) $limit_sql = " LIMIT $limitstart, $limit ";

            $file_parent = $this->parent;
            $file_type = $this->type;
            $filecat = $this->filecat;
            $this->file_total                        = ggsqlr (  "SELECT COUNT(a.id) FROM #__file AS a WHERE a.parent=".$file_parent." AND a.type='".$file_type."' AND a.filecat=".$filecat."; "  );
            if(  $this->file_total>0  ) $this->files = ggsql  (  "SELECT *           FROM #__file AS a WHERE a.parent=".$file_parent." AND a.type='".$file_type."' AND a.filecat=".$filecat." ORDER BY a.ordering ASC $limit_sql ; ");
	}

        /** отображаем список прикрепленных файлов, функция только для использования в админке */
        function iadmin_show_files(){
            if (  !$this->files[0]->id  ) $this->load_files();
            if (  $this->files[0]->id  ) {
		?><table >
		<?php
		$k = 0;
		for ($i=0; $i < $this->file_total; $i++) {
			$row 	= &$this->files[$i];
			?><tr><?
                            ?><td><a title="нажмите чтобы скачать"  href="<?=$this->url_prefix ?><? print $row->filename; ?>" ><img src="/ibots/editors/tinymce/e24code/AjexFileManager/skin/dark/ext/<? print $row->fileext; ?>.png"  border="0"  /></a></td><?
                            ?><td align="left"  style="white-space: normal; "><? if (  $row->name  ){ ?><strong><? echo $row->name; ?></strong><br /><? }
				if (  $row->desc  ){  echo $row->desc; ?><br /><br /><? }
				?><strong>Файл:</strong> <? echo $row->filename; ?></td><?
			?></tr><?
			$k = 1 - $k;
		}
		?></table><?
            }
        }

        /** путь до фото типа файла
         *
         * @param <type> $fileext
         * @return <STRING> 
         */
        function getExtFoto ($fileext){
            if (  in_array($fileext, $this->ext  )  ) return site_url.'/images/files/ext/'.$fileext.'.png';
            else                                      return site_url.'/images/files/ext/default.png';
        }

        /** формируем название файла - реальное, если заданно, или на основе имени файла
         *
         * @param <type> $file
         * @return <STRING>
         */
        function getScreenFileName($file){
            if (  $file->name!=''  ) return $file->name;
            else                     return $file->filename;
        }

        /** отображаем список прикрепленных файлов */
        function show_files(){
            global $reg;
            if (  !$this->files[0]->id  ) $this->load_files();
            if (  $this->files[0]->id  ) {
		?><table >
		<?php
		$k = 0;
		for ($i=0; $i < $this->file_total; $i++) {
			$row = &$this->files[$i];
			?><tr><?
                            ?><td><a title="нажмите чтобы скачать"  href="<?=$this->url_prefix.$row->filename; ?>" ><img src="<?=$this->getExtFoto($row->fileext); ?>"  border="0" /></a></td><?
                            ?><td align="left"><a href="<?=$this->url_prefix.$row->filename; ?>"><? echo $this->getScreenFileName( $row ); ?></a><?
                                if (  $row->desc  ){  echo '<br />'.desafelySqlStr($row->desc); ?><br /><br /><? } ?></td><?
			?></tr><?
			$k = 1 - $k;
		}
		?></table><?
                editme('file_list', array('id'=>$this->parent, 'type'=>$this->type, 'note'=>'Редактировать список файлов'), 'small');
            }
        }
        function show_movies(){
            global $reg;
            if (  !$this->files[0]->id  ) $this->load_files();
            if (  $this->files[0]->id  ) {
		?><table >
		<?php
		$k = 0;
		for ($i=0; $i < $this->file_total; $i++) {
			$row = &$this->files[$i];
                        $movieW = $reg['#__file_ID'.$this->type.$row->id.'__ширина видео'];     if (  !$movieW  )       $movieW  = 520;
                        $movieH = $reg['#__file_ID'.$this->type.$row->id.'__высота видео'];     if (  !$movieH  )       $movieH  = 350;
                        $movieBGColor = $reg['#__file_ID'.$this->type.$row->id.'__цвет фона'];  if (  !$movieBGColor  ) $movieBGColor  = '000000';
                        $moviesSrc = site_url.'/images/files/'.$row->filename;

                        $component_filefoto = new component_foto( 0 );
                        $component_filefoto->init( 'file' );
                        $component_filefoto->parent = $row->id;
                        $first_foto = $component_filefoto->get_1stfoto();
                        $preview = site_url.'/images/files/fotos/'.$first_foto->org;

			?><tr><?
                            ?><td>

                                <div id='mediaspace<?=$row->id ?>'>This text will be replaced</div>
                                <script type='text/javascript'>
                                  var so = new SWFObject('<?=site_url ?>/includes/jw_player/jw_player.swf','ply','<?=$movieW ?>','<?=$movieH ?>','9','#<?=$movieBGColor ?>');
                                  so.addParam('allowfullscreen','true');
                                  so.addParam('allowscriptaccess','always');
                                  so.addParam('wmode','opaque');
                                  so.addVariable('autostart','false');
                                  so.addVariable('image',' <?=$preview ?>');
                                  so.addVariable('file','<?=$moviesSrc ?>');
                                  so.write('mediaspace<?=$row->id ?>');
                                </script>

                            </td><?
                         ?></tr><tr><?
                            ?><td align="left"><strong><? echo $this->getScreenFileName( $row ); ?></strong><?
                                if (  $row->desc  ){  echo '<br />'.desafelySqlStr($row->desc); ?><br /><br /><? } ?></td><?
			?></tr><?
			$k = 1 - $k;
		}
		?></table><?
                editme('file_list', array('id'=>$this->parent, 'type'=>$this->type, 'note'=>'Редактировать видео-список'), 'small');
            }
        }

	function del_files (){
		$file_parent = $this->parent;
		$file_type = $this->type;
		$file_total = ggsqlr (  "SELECT COUNT(a.id) FROM #__file AS a WHERE a.parent=".$file_parent." AND a.type='".$file_type."'; "  );
		if (  $file_total>0  ){
			$files = 		ggsql  (  "SELECT *     FROM #__file AS a WHERE a.parent=".$file_parent." AND a.type='".$file_type."'; ");  //ggtr ($database);
			foreach ($files as $file){
				delfile ($this->dir.$file->filename);
				ggsqlq ("DELETE FROM #__file WHERE id=".$file->id);
			}
		}

	}

        function make_edit_link(){
            ?><a target="_blank" href="<?=$this->get_link() ?>">Редактировать</a><?
        }
	
	function get_link (){
		return "?ca=file&type=$this->type&parent=$this->parent&filecat=$this->filecat";
	}
	function get_files(){
		return ggsql("SELECT * FROM #__file WHERE parent=".$this->parent." AND filecat=$this->filecat AND type='$this->type' ORDER BY ordering ");
	}

	function howmany_files (){
		$total_files =  ggsqlr ("SELECT count(id) FROM #__file WHERE parent=".$this->parent." AND type='".$this->type."'; ");
		if (  $total_files==''  ) return 0;
		else return $total_files;
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

        function save_new_file( &$p ){
            global $reg;
            if (!defined( 'CLASS_UPLOAD' )) { include(site_path.'/includes/class.upload/class.upload.php');	define( 'CLASS_UPLOAD', 1 ); }
            ini_set("max_execution_time",0);

            //$this->handle = new Upload($dir.$img_fullname);
            $handle = new Upload(  $p->filedest  );
            if ($handle->uploaded) {    // then we check if the file has been "uploaded" properly in our case, it means if the file is present on the local file system
                    $fileinfo = pathinfo(  $p->filename  );
                    $handle->file_new_name_body = sefname($fileinfo['filename']);
                    $handle->file_new_name_ext = $fileinfo['extension'];
                    $handle->mime_check = false;
                    $handle->Process( $this->dir );
            }
            
            /*
             * СОХРАНЯЕМ В БД
             */
            $i24r = new mosDBTable( "#__file", "id", $reg['db'] );
            $i24r->id = $p->id;
            if (  $handle->file_dst_name!=''  ){
                $i24r->filename = $handle->file_dst_name;
                $i24r->fileext = $fileinfo['extension'];
            }
            $i24r->name = $p->name;
            $i24r->desc = $p->desc;
            $i24r->filecat = $p->filecat;
            if (  $p->id==0  )  $i24r->ordering = ggsqlr (  "SELECT ordering FROM #__file ORDER BY ordering DESC LIMIT 0,1 "  )+1;
            $i24r->type = $p->type;
            $i24r->parent = $p->parent;
            $i24r->publish = $p->publish;

            if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

            //names
            $names = new names($i24r->id, 'filename'.$p->type, $reg);
            $names->apply_names(  ggrr('_names_field')  );
        }

        function default_init(){
		$this->id 		= ggri('id', $_REQUEST['cid'][0]);
		$this->parent 		= ggri("parent");
		$this->desc 		= ggrr("desc");
		$this->filecat 		= ggri('filecat');
        }
	
	function init( $type, &$parent_obj=NULL ){
		global $reg;
		$this->type 		= $type;
		$this->table_parent_id_field = "id";

                /* default valuues */
		$this->dir = site_path."/images/files/";
		$this->url_prefix = "/images/files/";

		if (  $type=="names_main"  ||  $type=="names"  ){
			$this->table_parent = "#__names";
			$this->parent_component_name = $reg['names_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				//$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, "exgood");
			}
		}
		if (  $type=="names_prop_main"  ||  $type=="names_prop"  ){
			$this->table_parent = "#__names_prop";
			$this->parent_component_name = $reg['names_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				//$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, "exgood");
			}
		}

		if (  $type=="exgood"  ||  $type=="exgood_main"  ){
			$this->table_parent = "#__exgood";
			$this->parent_component_name = $reg['ex_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, "exgood");
			}
		}
		if (  $type=="excat"  ){
			$this->table_parent = "#__excat";
			$this->parent_component_name = $reg['ex_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, $this->type);
			}
		}
		if (  $type=="content"  ||  $type=="content_main"  ){
			$this->table_parent = "#__content";
			$this->parent_component_name = $reg['content_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, $reg['content_name'] , 1, $this->type);
			}
		}
		if (  $type=="typedcontent"  or  $type=="typedcontent_main"  ){
			$this->table_parent = "#__content";
			$this->parent_component_name = $reg['typedcontent_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, $reg['typedcontent_name'] , 1, $this->type);
			}
		}

		if (  $type=="icat"  ){
			$this->table_parent = "#__icat";
			$this->parent_component_name = $reg['content_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "index2.php?ca=".$this->type, $reg['content_name'] , 1, $this->type);
			}
		}
		if (  $type=="exfoto"  ){
			$this->table_parent = "#__exfoto";
			$this->parent_component_name = $reg['exfile_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array($this->parent_obj, "#__exfile", "parent", "index2.php?ca=".$this->type, $reg['exfile_name'], 1, $this->type);
			}
		}
		if (  $type=="user_main"  ){
			$this->table_parent = "#__users";
			$this->parent_component_name = $reg['cab_name'];
			$this->dir = site_path."/images/files/";
			$this->url_prefix = "/images/files/";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
			}
		}

                // те расширения, для которых есть картинки
                $this->ext = array(
                    'aac', 'ac3', 'ace', 'ade', 'adp', 'ai', 'aiff', 'aspx', 'au', 'avi', 'bak', 'bat', 'bmp', 'cab', 'cat', 'chm', 'css', 'csv', 'der', 'dic', 'divx', 'diz', 'dll', 'doc', 'docx', 'dos', 'dvd', 'dwg', 'dwt', 'emf', 'exc', 'fav', 'fla', 'font', 'gif', 'hlp', 'html', 'ifo', 'inf', 'ini', 'iso', 'isp', 'java', 'jfif', 'jpeg', 'jpg', 'js', 'log', 'm4a', 'm4p', 'mmf', 'mov', 'movie', 'mp2', 'mp2v', 'mp3', 'mp4', 'mpe', 'mpeg', 'mpg', 'mpv2', 'nfo', 'one', 'pdd', 'pdf', 'php', 'png', 'pps', 'ppt', 'pptx', 'psd', 'rar', 'rb', 'reg', 'rtf', 'scp', 'sql', 'swf', 'sys', 'tif', 'tiff', 'tmp', 'ttf', 'txt', 'uis', 'vcr', 'vob', 'wba', 'wma', 'wmv', 'wpl', 'wri', 'wtx', 'wzv', 'xls', 'xlsx', 'xml', 'xsl', 'zap', 'zip'
                );

	}
}
?>
