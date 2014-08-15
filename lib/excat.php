<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/*
 *
 * КЛАСС ДЛЯ РАБОТЫ С КАТЕГОРИЯМИ КАТАЛОГА ПРОДУКТОВ
 *
 */
class excat{
	var $id;
	var $vars;

	function load_me(){
		if (  $this->id>0  ) $this->vars = ggo($this->id, "#__excat");
	}
	function get_main_foto( $type, $noimage_return=0 ){
		global $reg;
	
	
	
	
	
		if (  $this->vars->$type!=''  ) return "/images/ex/cat/".$this->vars->$type;
		else {
			if (  $noimage_return==1  ) return false;
			else return $reg['excat_noimage'];
		}
	}

        /**
         * УДАЛЕНИЕ ВСЕХ ТОВАРОВ И ПОДКАТЕГОРИЙ ИЗ КАТЕГОРИИ.
         * $include_main_folder=1 - удалять и саму категорию тоже (0 по умолчанию)
         * @param <int> $id
         * @param <int> $include_main_folder
         */
        function clean_folder ($id, $include_main_folder=0){
            global $reg;
            if (  !$id  ) return;

            // получаем id категорий
            $allcats = getAllSections($id);
            $allcats[] = $id;
            if (  count($allcats)==0  ) return;
            $allcats_str = join(", ", $allcats);
            $where_excats = " IN ( ".$allcats_str." ) ";
            //ggd ($where_excats);

            // удаляем все товары
            $query = " SELECT exgood.id FROM #__exgood as exgood
                       WHERE exgood.parent $where_excats
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
           foreach (  $allcats as $cat1  ){
                if (  $include_main_folder==0  and  $cat1==$id  ) continue; // корневую категорию не удаляем
                $excat->id = $cat1;
                $excat->delme( 0, 1 );
           }

        }
        /**
         * УДАЛЕНИЕ КАТЕГОРИИ
         *
         * @param <int> $adminlog=0 делать запись в логе (1)
         * @param <int> $del_anywhere=0 удалять категорию, даже если в ней содержатся подкатегории или товары (1)
         */
        function delme(  $adminlog=0, $del_anywhere=0  ){
            global $reg;
            $dfgd = $this->id;

		// проверяем, есть ли вложенные товары
                if (  $del_anywhere==0  ){
                    $exgoodsincat = ggsqlr( "SELECT count(id) FROM #__exgood WHERE parent=".$dfgd );
                    if ($exgoodsincat>0){
                            ?><script language="javascript">  alert("Категория: '<? $excattodel = ggo($dfgd, "#__excat"); print $excattodel->name; ?>' содержит объекты, удаление невозможно");  </script><?
                            return;
                    }
                    // проверяем, есть ли вложенные категории
                    $exgoodsincat = ggsqlr( "SELECT count(id) FROM #__excat WHERE parent=".$dfgd );
                    if ($exgoodsincat>0){
                            ?><script language="javascript">  alert("Категория: '<? $excattodel = ggo($dfgd, "#__excat"); print $excattodel->name; ?>' содержит вложенные категории, удаление невозможно");  </script><?
                            return;
                    }
                }

		// удаляем фото
		$component_foto = new component_foto ( 0 );
		$component_foto->init('excat');
		$component_foto->parent = $dfgd;
		$component_foto->load_parent();
		$component_foto->del_fotos();

                if (  $adminlog  ){
                    $adminlog_obg = $component_foto->parent_obj;	$adminlog = new adminlog(); $adminlog->logme('del_cat', $reg['ex_name'], $adminlog_obg->name, $adminlog_obg->id );
                }
		ggsqlq ("DELETE FROM #__excat WHERE id=".$dfgd);

                $names = new names($dfgd, 'excat', $reg);
                $names->delete();

                // удаление индивидуальных настроек
                load_adminclass('config');
                $conf = new config($reg['db']);
                $conf->prefix_id = '#__excat'."_ID".$dfgd."__";
                $conf->remove_addition_config();
        }
	
}
?>