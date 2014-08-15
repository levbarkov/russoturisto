<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class content{ 
	var $id;
	var $vars;
	function __construct ($id=0){
		if (  $id>0  )  $this->load ($id);
	}
	function load ($id){
		$this->vars = ggo ($id, "#__content");
	}
	function get_link(){
		return $this->vars->sefnamefullcat.'/'.$this->vars->sefname.".html";
	}
	
	function get_date(){	// выводим дату вида: "15 Октября 2008"
		global $reg;
		$real_time = strtotime ($this->vars->created) + $reg['iServerTimeOffset'];
		$real_date = getdate(  $real_time  );
		return num::fillzerro($real_date['mday'],2)."&nbsp;".ru::GGgetMonthNames($real_date['mon'])."&nbsp;".$real_date['year'];
	}

    function get_preview_text(){
        global $reg;
        $expr = '~(</p>|<br\s*/?>)~';
        if (strlen(strip_tags($this->vars->introtext)) == 0) {
            $text = preg_replace($expr, "\n", $this->vars->fulltext);
            $text = str::get_substr(strip_tags(desafelySqlStr($text)), $reg['content_contentmaxlength_intro']);
        }
        else
            $text = preg_replace($expr, "\n", $this->vars->introtext);
        $text = preg_replace("~\n+$~", '', $text);

        return preg_replace("~\n~", '<br/>', desafelySqlStr($text));
    }

    /**
     * Удаление статьи/новости
     *
     * @param adminlog $adminlog
     * @param <type> $delp
     */
    function delme( $adminlog=0, $type = 'content', &$delp=NULL ){
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
                    $component_foto->init( $type );
                    $component_foto->parent = $dfgd;
                    $component_foto->load_parent();
                    $component_foto->del_fotos();
                } else if (  $adminlog==1  ) {
                    $component_foto->parent_obj = ggo (  $dfgd, '#__exgood'  );
                }

                if (  $p->delfile==1  ){ // удаляем файлы
                    $component_file = new component_file ( 0 );
                    $component_file->init( $type );
                    $component_file->parent = $dfgd;
                    $component_file->load_parent();
                    $component_file->del_files();
                }

		if (  $p->delcomments==1  ) { // удаляем комментарии
                    $comments = new comments($type, $reg['db'], $reg);
                    $comments->del_for_type( $dfgd );
                }

		if (  $p->deltags==1  ) { //удаляем тэги
                    $tag = new tags($type, $reg['db'], $reg);
                    $tag->delete($dfgd);
                }

                if (  $p->delnames==1  ) { //удаляем свойства NAMES
                    $names = new names($dfgd, $type, $reg);
                    $names->delete();
                }

                if (  $adminlog==1  ){
                    $adminlog_obg = $component_foto->parent_obj;	$adminlog = new adminlog(); $adminlog->logme('del', $reg['content_name'], $adminlog_obg->title, $adminlog_obg->id );
                }
		ggsqlq ("DELETE FROM #__content WHERE id=".$dfgd);

                if (  $p->prefix_config==1  ) { // удаление индивидуальных настроек
                    load_adminclass('config');
                    $conf = new config($reg['db']);
                    $conf->prefix_id = '#__'.$type."_ID".$dfgd."__";
                    $conf->remove_addition_config();
                }

    }
	


	
}

?>