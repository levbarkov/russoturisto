<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();




function fotocat_up(){
    global $reg;
    global $database;
    $excatfoto_this = ggo(ggri('id'), '#__foto_cat');
//    ggtr ($excatfoto_this );
    $excatfoto_up = ggsql(" SELECT * FROM #__foto_cat WHERE #__foto_cat.order< ".$excatfoto_this->order." AND #__foto_cat.parent=".$excatfoto_this->parent." AND #__foto_cat.type='".$excatfoto_this->type."' ORDER BY #__foto_cat.order DESC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
    if (  !$excatfoto_up->id  ){
        $excatfoto_up = ggsql(" SELECT * FROM #__foto_cat WHERE #__foto_cat.parent=".$excatfoto_this->parent." AND #__foto_cat.type='".$excatfoto_this->type."' ORDER BY #__foto_cat.order DESC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
    }
//	ggtr($excatfoto_up); ggtr ($database); die();
    $i24r = new mosDBTable( "#__foto_cat", "id", $database );
    $i24r->id = ggri('id');
    $i24r->order = $excatfoto_up->order;
//	ggtr ($i24r);
    if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

    $i24r = new mosDBTable( "#__foto_cat", "id", $database );
    $i24r->id = $excatfoto_up->id;
    $i24r->order = $excatfoto_this->order;
//	ggtr ($i24r); die();
    if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
    ?><script language="javascript">
         ins_ajax_load_target ("ca=foto_ajax&task=showfotocats&type=<?=$excatfoto_this->type ?>&parent=<?=$excatfoto_this->parent ?>&4ajax=1", "#show_all_list");
         over_fade_hide();
    </script><?
}





function fotocat_down(){
    global $reg;
    global $database;
    $excatfoto_this = ggo(ggri('id'), '#__foto_cat');
//    ggtr ($excatfoto_this );
    $excatfoto_up = ggsql(" SELECT * FROM #__foto_cat WHERE #__foto_cat.order> ".$excatfoto_this->order." AND #__foto_cat.parent=".$excatfoto_this->parent." AND #__foto_cat.type='".$excatfoto_this->type."' ORDER BY #__foto_cat.order ASC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
    if (  !$excatfoto_up->id  ){
        $excatfoto_up = ggsql(" SELECT * FROM #__foto_cat WHERE #__foto_cat.parent=".$excatfoto_this->parent." AND #__foto_cat.type='".$excatfoto_this->type."' ORDER BY #__foto_cat.order ASC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
    }
//	ggtr($excatfoto_up); ggtr ($database); die();
    $i24r = new mosDBTable( "#__foto_cat", "id", $database );
    $i24r->id = ggri('id');
    $i24r->order = $excatfoto_up->order;
//	ggtr ($i24r);
    if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

    $i24r = new mosDBTable( "#__foto_cat", "id", $database );
    $i24r->id = $excatfoto_up->id;
    $i24r->order = $excatfoto_this->order;
//	ggtr ($i24r); die();
    if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
    ?><script language="javascript">
         ins_ajax_load_target ("ca=foto_ajax&task=showfotocats&type=<?=$excatfoto_this->type ?>&parent=<?=$excatfoto_this->parent ?>&4ajax=1", "#show_all_list");
         over_fade_hide();
    </script><?
}


function showfotocats(){
	global $reg;
        $foto_cats_array = ggsql("SELECT * FROM #__foto_cat AS a WHERE a.parent=".ggri('parent')." AND a.type='".ggrr('type')."' ORDER BY a.order ; ");  //ggtr ($database);
        ?><table class="adminheading" align="right" width="300" style="width:300px;" id="show_all_list_t">
        <tr><td colspan="3">Управление подкатегориями фотогалереи</td></tr>
        <? foreach ($foto_cats_array as $foto_cat1){ ?>
            <tr class="rowajax2">
                <td><a href="javascript: over_fade('#show_all_list_t', '#show_all_list_t', '', 0.1, 'nopopup');      ins_ajax_load_target('ca=foto_ajax&task=fotocat_del&id=<?=$foto_cat1->id ?>&4ajax=1', '#show_all_list');                 void(0);" title="Удалить подкатегорию"><img src="/iadmin/images/del.png" width="16px" height="16px" border="0"></a></td>
                <td><a href="index2.php?ca=foto&task=fotocat_edit&id=<?=$foto_cat1->id ?>&type=<?=ggrr('type') ?>&parent=<?=ggrr('parent') ?>&fotocat=<?=ggrr('fotocat') ?>" target="_blank" class="ajax_link" title="Изменить подкатегорию" ><?=$foto_cat1->name; ?></a></td>
                <td><a href="javascript: over_fade('#show_all_list_t', '#show_all_list_t', '', 0.1, 'nopopup');      ins_ajax_load_target('ca=foto_ajax&task=fotocat_down&id=<?=$foto_cat1->id ?>&4ajax=1', '#show_all_list');      void(0);" title="Переместить вниз подкатегорию"><img src="/iadmin/images/downarrow.png" width="14px" height="14px" border="0"></a></td>
                <td><a href="javascript: over_fade('#show_all_list_t', '#show_all_list_t', '', 0.1, 'nopopup');      ins_ajax_load_target('ca=foto_ajax&task=fotocat_up&id=<?=$foto_cat1->id   ?>&4ajax=1', '#show_all_list');      void(0);" title="Переместить вверх подкатегорию"><img src="/iadmin/images/uparrow.png" border="0" width="14px" height="14px"></a></td>
            </tr>
        <? } ?>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr class="rowajax2">
            <td><a href="index2.php?ca=foto&task=fotocat_edit&id=0&type=<?=ggrr('type') ?>&parent=<?=ggrr('parent') ?>&fotocat=<?=ggrr('fotocat') ?>" target="_blank"><img border="0" src="/iadmin/images/ins.png" width="16px" height="16px"></a></td>
            <td><a class="ajax_link" href="index2.php?ca=foto&task=fotocat_edit&id=0&type=<?=ggrr('type') ?>&parent=<?=ggrr('parent') ?>&fotocat=<?=ggrr('fotocat') ?>" target="_blank">Добавить подкатегорию</a></td>
            <td colspan="2">&nbsp;</td>
        </tr>
        </table><?
}


function fotocat_del(){
	global $reg;
        $excatfoto_this = ggo(ggri('id'), '#__foto_cat');

        // проверка есть ли фото в категории
        $f_cnt = ggsqlr (  '  select count(id) from #__foto where fotocat='.ggri('id')  );
        if (  $f_cnt>0  ) {
            ?><script language="javascript">
                ins_ajax_load_target ("ca=foto_ajax&task=showfotocats&type=<?=$excatfoto_this->type ?>&parent=<?=$excatfoto_this->parent ?>&4ajax=1", "#show_all_list");
                over_fade_hide();
                alert ('Удаление невозможно, так как в подкатегории имеются фото');
            </script><?
            return;
        }

        // удаление индивидуальных настроек
        load_adminclass('config');
        $conf = new config($reg['db']);
        $conf->prefix_id = '#__foto_cat'."_ID".ggri('id')."__";
        $conf->remove_addition_config();

        // делаем запись в логе операций
        $adminlog_obg = $excatfoto_this;
        $adminlog = new adminlog(); $adminlog->logme('del_foto_subcat', 'тип '.$adminlog_obg->type, $adminlog_obg->name, $adminlog_obg->id );

        ggsqlq (  "DELETE FROM #__foto_cat WHERE id=".ggri('id')  );


        ?><script language="javascript">
            ins_ajax_load_target ("ca=foto_ajax&task=showfotocats&type=<?=$excatfoto_this->type ?>&parent=<?=$excatfoto_this->parent ?>&4ajax=1", "#show_all_list");
            over_fade_hide();
        </script><?
        
}
?>
