<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
function print_pathwayspliter(){
	return ' <b>»</b> ';
}
function shadow_effect($i24foto_link, $i24foto_desc="&nbsp;"){	
	if (  $i24foto_desc==''  ) $i24foto_desc = "&nbsp;";
?><table align="left" cellpadding="0" cellspacing="0" border="0" style="margin:0px; padding:0px;"><?
	?><tr><td class="gimg">
				<div style="position:relative;">
					<?php print $i24foto_link; ?>
					<div class="gdesc"><span><?=stripslashes($i24foto_desc) ?></span></div>					
				</div>
	</td></tr></table><?php
}

function print_foto_gallery($i24foto_link, $i24foto_desc="&nbsp;", $i24foto_path="/component/foto/ramka/"){ 
if (  $i24foto_desc==''  ) $i24foto_desc = "&nbsp;";
?><table align="left" cellpadding="0" cellspacing="0" border="0" style="margin:0px; padding:0px;"><?php
	?><tr><?php
		?><td valign="bottom" style="vertical-align:bottom; padding:0px; margin:0px; background:url(<?php print $i24foto_path ?>lt.gif) left top no-repeat;"></td><?php
		?><td valign="bottom" style="vertical-align:bottom; padding:0px; margin:0px; background:url(<?php print $i24foto_path ?>tt.gif) repeat-x; height:10px; line-height:5px;"></td><?php
		?><td valign="bottom" style="vertical-align:bottom; padding:0px; margin:0px; background:url(<?php print $i24foto_path ?>rt.gif) right top no-repeat;"></td><?
	?></tr><?
	?><tr><?
		?><td style="background:url(<?php print $i24foto_path ?>ll.gif); width:10px;"><img src="<?php print $i24foto_path ?>ll.gif" /></td><?
		?><td class="gimg">
				<div style="position:relative;">
					<?php print $i24foto_link; ?>					
					<div class="gdesc"><span><?=stripslashes($i24foto_desc) ?></span></div>
				</div>

		</td><?
		?><td style="background:url(<?php print $i24foto_path ?>rr.gif); width:10px;"><img src="<?php print $i24foto_path ?>rr.gif" /></td><?
	?></tr><?
	?><tr><?
		?><td style="padding:0px; margin:0px; background:url(<?php print $i24foto_path ?>lb.gif) left bottom no-repeat;"></td><?
		?><td style="background:url(<?php print $i24foto_path ?>bb.gif); height:10px;"></td><?
		?><td style="padding:0px; margin:0px; background:url(<?php print $i24foto_path ?>rb.gif) right bottom no-repeat;"></td><?
	?></tr><?
?></table><?
}
function print_foto_desc($i24foto_desc=""){
?><table cellpadding="0" cellspacing="0" width="100%">
	<tr><td align="justify" style="text-align:justify; padding-left:8px; padding-right:7px;"><?php print stripslashes($i24foto_desc); ?></td></tr>
	<tr height="10"><td align="justify" style="text-align:justify; font-size:10px">&nbsp;</td></tr>
</table><?
}
function i24pathadd($iway, $name, $url){
	$iwaycount = count ($iway);
	$iway[$iwaycount]->name = $name;
	$iway[$iwaycount]->url  = $url;
	return $iway;
}
function i24pwprint($icatway, $domainauto=1, $iwayprefix="iway")
{
		?><!--i24_pathway_start--><div id="ipathway_div" class="<?php print $iwayprefix; ?>_ipathway_div unl"><?
			if (  $domainauto==1  ) { ?><a class="<?php print $iwayprefix; ?>_pathway_link" href='/'>Главная</a><?php print print_pathwayspliter(); }
			for ($iii=0; $iii<count($icatway); $iii++ ){
				if (  $icatway[$iii]->url==''  ){
					?><span class="<?php print $iwayprefix; ?>_pathway_name"><?php print $icatway[$iii]->name; ?></span><?
				}
				else {
					?><a class="<?php print $iwayprefix; ?>_pathway_link" href='<?php print $icatway[$iii]->url; ?>'><?php print $icatway[$iii]->name; ?></a><?
				}
				if (  $iii<(count($icatway)-1)  ) {
					print print_pathwayspliter();
				}
			}
		?></div><!--i24_pathway_end--><?
}
function print_pathwayspliter_admin(){
	return ' / ';
}
function i24pwprint_admin($icatway, $domainauto=1, $iwayprefix="iway"){
		global $reg;
		?><table width="100%" cellspacing="1" cellpadding="4" border="0"><?
	    ?><tr><td class='ex_contentheading1' width='82%'><?
		if (  $domainauto==1  ) { ?><a class="<?php print $iwayprefix; ?>_pathway_link" href='index2.php'>Главная</a><?php print print_pathwayspliter_admin(); print print_pathwayspliter_admin(); }
		else if (  $domainauto==0  ) { ?>Главная<?php print print_pathwayspliter_admin(); print print_pathwayspliter_admin(); }
		for ($iii=0; $iii<count($icatway); $iii++ ){
			if (  $icatway[$iii]->url==''  ){
				?><span id="iclast" onclick="javascript:cht(<?=ggri("t");?>);" class="<?php print $iwayprefix; ?>_pathway_name"><?php print $icatway[$iii]->name; ?></span><?
			}
			else {
				?><a class="<?php print $iwayprefix; ?>_pathway_link" href='<?php print $icatway[$iii]->url; ?>'><?php print $icatway[$iii]->name; ?></a><?
			}
			if (  $iii<(count($icatway)-1)  ) {
				print print_pathwayspliter_admin();
			}
		}                                  
		
        ?></td>
		<?php if (  $reg['doCtrlEnter']    ){ ?>
			<td nowrap="nowrap" style="white-space:nowrap">(Ctrl+Enter) &nbsp;&nbsp;&nbsp;&mdash;&nbsp;&nbsp; сохранить<br />(Ctrl+Пробел) &mdash; применить</td>
		<?php } ?>
        </tr><?
		?></table><?
}

function get_page_time(){
    global  $page_time;
    return ( getmicrotime() - $page_time['start'] );
}

function get_array_str_without($val, $brands_selected_array, $splitter=','){
    unset($brands_selected_array[array_search($val,$brands_selected_array)]);
    return implode($splitter,$brands_selected_array);
}

function get_link_params(&$sign, $new_params = null){
    if (sizeof($sign) == 0) {
        if ($new_params === null || sizeof($new_params) == 0)
            return '';
        else {
            $sign = $new_params;
            $new_params = null;
        }
    }

    $result = array();
    foreach ($sign as $key => $val) {
        if ($new_params !== null && isset($new_params[$key]))
            $val = $new_params[$key];

        if (is_array($val))
            foreach ($val as $v)
                $result []= $key . '[]=' . urlencode($v);

        elseif (is_int($val) || (is_string($val) && $val != ''))
            $result []= $key . '=' . urlencode($val);
    }
    return implode('&', $result);
}


class mosCommonHTML {

        function checkedOut( &$row, $overlib=1 ) {
                $hover = '';
                if ( $overlib ) {
                        $date                                 = mosFormatDate( $row->checked_out_time, _CURRENT_SERVER_TIME_FORMAT );
                        $time                                = mosFormatDate( $row->checked_out_time, '%H:%M' );
			$editor				= addslashes( htmlspecialchars( html_entity_decode( $row->editor, ENT_QUOTES ) ) );
                        $checked_out_text         = '<table>';
			$checked_out_text 	.= '<tr><td>'. $editor .'</td></tr>';
                        $checked_out_text         .= '<tr><td>'. $date .'</td></tr>';
                        $checked_out_text         .= '</table>';
                        $hover = 'onMouseOver="return overlib(\''. $checked_out_text .'\', CAPTION, \'Заблокировано\', BELOW, RIGHT);" onMouseOut="return nd();"';
                }
                $checked                         = '<img src="images/checked_out.png" '. $hover .'/>';

                return $checked;
        }

        /*
        * Loads all necessary files for JS Calendar
        */
        function loadCalendar() {
                ?>
                <link rel="stylesheet" type="text/css" media="all" href="<?php echo site_url;?>/includes/js/calendar/calendar-mos.css" title="green" />
                <!-- import the calendar script -->
                <script type="text/javascript" src="<?php echo site_url;?>/includes/js/calendar/calendar_mini.js"></script>
                <!-- import the language module -->
                <script type="text/javascript" src="<?php echo site_url;?>/includes/js/calendar/lang/calendar-en.js"></script>
                <?php
        }

        function AccessProcessing( &$row, $i ) {
                if ( !$row->access ) {
                        $color_access = 'style="color: green;"';
                        $task_access = 'accessregistered';
                } else if ( $row->access == 1 ) {
                        $color_access = 'style="color: red;"';
                        $task_access = 'accessspecial';
                } else {
                        $color_access = 'style="color: black;"';
                        $task_access = 'accesspublic';
                }

                $href = '
                <a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task_access .'\')" '. $color_access .'>
                '. $row->groupname .'
                </a>'
                ;

                return $href;
        }

        function CheckedOutProcessing( &$row, $i ) {
        	global $my;
			$checked = mosHTML::idBox( $i, $row->id, 0 );
			if (0){	// отключаем систему замочков...
				if ( $row->checked_out) {	$checked = mosCommonHTML::checkedOut( $row );  } 
				else { $checked = mosHTML::idBox( $i, $row->id, ($row->checked_out && $row->checked_out != $my->id ) );   }	
			}
			return $checked;
        }

        function PublishedProcessing( &$row, $i, $idolink=1 ) {
                $img         = $row->published ? 'publish_g.png' : 'publish_x.png';
                $task         = $row->published ? 'unpublish' : 'publish';
                $alt         = $row->published ? 'Видимо' : '<span style="color:#ff0000;">Заблокированно</span>';
                $action        = $row->published ? 'Не показывать на сайте' : 'Доступно для просмотра на сайте';
				if (  $idolink==1  ){
                $href = '
					<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
					'. $alt .'
					</a>'
					;
				} else {
					$href = $alt;
				}

                return $href;
        }
	}


function rewrite_option(){
	global $option, $icom;
	$icom = ggrr(c); $option = ggrr(c); return;
}
function iseo(){
	
	require_once( site_path."/seo.php" );
}
/** ЗАГРУЗКА КОМПОНЕНТА ДЛЯ ИСПОЛЬЗОВАНИЯ ВНУТРИ СИСТЕМЫ (ДЛЯ ИСКЛЮЧЕНИЯ ЛИШНИХ ЗАПРОСОВ SELECT)
 *
 * создает объекты:
 *	$reg['mainobj']    - текущий объект, например новость или товар который сейчас открыт;
 *	$reg['mainparent'] - родитель текущего объект, например рубрика новости или категория товара который сейчас открыт;
 *
 * если объекты не создал, то 
 *	$reg['mainobj']    = false;
 *	$reg['mainparent'] = false;
 *
 */
function get_mainobj(){
	global $reg;
	if (    $reg['c']=="ex"  and    (  $reg['task']=="excat"  or  $reg['task']=="excomp"  or  $reg['task']=="thank"  or  $reg['task']=="viewtrush"  )  and  ggri('id')>0  ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__excat"  );
	}
	else if (    $reg['c']=="ex"    and    $reg['task']=="view"    and    ggri('id')>0    ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__exgood"  );
		$reg['mainparent'] = ggo (  $reg['mainobj']->parent, "#__excat"  );
	}
	else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="adcat"    and    ggri('id')>0    ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__adcat"  );	}
	else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__adgood"  );
		$reg['mainparent'] = ggo (  $reg['mainobj']->parent, "#__adcat"  );
	}
	else if (    $_REQUEST['c']=="showscont"   and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__content"  );
	}
	else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="icat"    and    ggri('id')>0    ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__icat"  );
	}
	else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__content"  );
		$reg['mainparent'] = ggo (  $reg['mainobj']->catid, "#__icat"  );
	}
	else if (    $_REQUEST['c']=="foto"    and    ggri('id')>0    ){
		$reg['mainobj'] = ggo (  ggri('id'), "#__exfoto"  );
	} else {
		$reg['mainobj'] = false;
		$reg['mainparent'] = false;
	}
	return;
}
function ititle(){
	global $reg;
	$reg['siteTitle'] = 'Туристическое агентство Руссо Туристо г. Красноярска';
	if (  $reg['iseoshowscont']  ){
		if (    $_REQUEST['c']=="showscont"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!='') return desafelySqlStr( $reg['mainobj']->seo_title );
			else if (  desafelySqlStr($reg['showscont_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['showscont_seo_title'])   );
			else return $reg['siteTitle'];
		}
	}
	if (  $reg['iseoex']  ){
		if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="excat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['ex_seo_title'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['ex_seo_title'])   );
			else return $reg['siteTitle'];
		} else if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="excat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!=''  )  return desafelySqlStr($reg['mainobj']->seo_title);
			else if (  desafelySqlStr($reg['ex_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ex_seo_title'])   );
			else return $reg['siteTitle'];
		}
		else if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!='') return desafelySqlStr( $reg['mainobj']->seo_title );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodtitle)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['mainparent']->seo_goodtitle)   );
			else if (  desafelySqlStr($reg['ex_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ex_seo_title'])   );
			else return $reg['siteTitle'];
		}
	}
	
	if (  $reg['iseoad']  ){
		if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="adcat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['ad_seo_title'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['ad_seo_title'])   );
			else return $reg['siteTitle'];
		} else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="adcat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!=''  )  return desafelySqlStr($reg['mainobj']->seo_title);
			else if (  desafelySqlStr($reg['ad_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ad_seo_title'])   );
			else return $reg['siteTitle'];
		}
		else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!='') return desafelySqlStr( $reg['mainobj']->seo_title );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodtitle)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['mainparent']->seo_goodtitle)   );
			else if (  desafelySqlStr($reg['ad_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ad_seo_title'])   );
			else return $reg['siteTitle'];
		}
	}
	if (  $reg['iseocontent']  ){
		if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="icat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['content_seo_title'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['content_seo_title'])   );
			else return $reg['siteTitle'];
		} else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="icat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!=''  )  return desafelySqlStr($reg['mainobj']->seo_title);
			else if (  desafelySqlStr($reg['content_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['content_seo_title'])   );
			else return $reg['siteTitle'];
		} else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!='') return desafelySqlStr( $reg['mainobj']->seo_title );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodtitle)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['mainparent']->seo_goodtitle)   );
			else if (  desafelySqlStr($reg['content_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['content_seo_title'])   );
			else return $reg['siteTitle'];
		}
	}
	if (  $reg['iseofoto']  ){
		if (    $_REQUEST['c']=="foto"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['foto_seo_title'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['foto_seo_title'])   );
			else return $reg['siteTitle'];
		} else if (    $_REQUEST['c']=="foto"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_title)!=''  )  return desafelySqlStr($reg['mainobj']->seo_title);
			else if (  desafelySqlStr($reg['foto_seo_title'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['foto_seo_title'])   );
			else return $reg['siteTitle'];
		}
	}
	else return $reg['siteTitle'];
	return $reg['siteTitle'];
}
function imeta_description(){
	global $reg;
	if (  $reg['iseoshowscont']  ){
		if (    $_REQUEST['c']=="showscont"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->metadesc)!='') return desafelySqlStr( $reg['mainobj']->metadesc );
			else if (  desafelySqlStr($reg['showscont_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['showscont_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
	}
	if (  $reg['iseoex']  ){
		if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="excat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['ex_seo_metadesc'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['ex_seo_metadesc'])   );
			else return $reg['siteDescription'];
		} else if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="excat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metadesc)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metadesc);
			else if (  desafelySqlStr($reg['ex_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ex_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
		else if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metadesc)!='') return desafelySqlStr( $reg['mainobj']->seo_metadesc );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodmetadesc)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['mainparent']->seo_goodmetadesc)   );
			else if (  desafelySqlStr($reg['ex_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ex_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
	}
	if (  $reg['iseoad']  ){
		if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="adcat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['ad_seo_metadesc'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['ad_seo_metadesc'])   );
			else return $reg['siteDescription'];
		} else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="adcat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metadesc)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metadesc);
			else if (  desafelySqlStr($reg['ad_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ad_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
		else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metadesc)!='') return desafelySqlStr( $reg['mainobj']->seo_metadesc );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodmetadesc)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['mainparent']->seo_goodmetadesc)   );
			else if (  desafelySqlStr($reg['ad_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ad_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
	}
	if (  $reg['iseocontent']  ){
		if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="icat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['content_seo_metadesc'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['content_seo_metadesc'])   );
			else return $reg['siteDescription'];
		} else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="icat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metadesc)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metadesc);
			else if (  desafelySqlStr($reg['content_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['content_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
		else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->metadesc)!='') return desafelySqlStr( $reg['mainobj']->metadesc );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodmetadesc)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['mainparent']->seo_goodmetadesc)   );
			else if (  desafelySqlStr($reg['content_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['content_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
	}
	if (  $reg['iseofoto']  ){
		if (    $_REQUEST['c']=="foto"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['foto_seo_metadesc'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['foto_seo_metadesc'])   );
			else return $reg['siteDescription'];
		} else if (    $_REQUEST['c']=="foto"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metadesc)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metadesc);
			else if (  desafelySqlStr($reg['foto_seo_metadesc'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['foto_seo_metadesc'])   );
			else return $reg['siteDescription'];
		}
	}
	else return $reg['siteDescription'];
	return $reg['siteDescription'];
}
function imeta_keywords(){
	global $reg;
	if (  $reg['iseoshowscont']  ){
		if (    $_REQUEST['c']=="showscont"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->metakey)!='') return desafelySqlStr( $reg['mainobj']->metakey );
			else if (  desafelySqlStr($reg['showscont_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['showscont_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
	}
	if (  $reg['iseoex']  ){
		if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="excat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['ex_seo_metakey'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['ex_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		} else if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="excat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metakey)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metakey);
			else if (  desafelySqlStr($reg['ex_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ex_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
		else if (    $_REQUEST['c']=="ex"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metakey)!='') return desafelySqlStr( $reg['mainobj']->seo_metakey );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodmetakey)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['mainparent']->seo_goodmetakey)   );
			else if (  desafelySqlStr($reg['ex_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ex_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
	}
	if (  $reg['iseoad']  ){
		if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="adcat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['ad_seo_metakey'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['ad_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		} else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="adcat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metakey)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metakey);
			else if (  desafelySqlStr($reg['ad_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ad_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
		else if (    $_REQUEST['c']=="ad"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metakey)!='') return desafelySqlStr( $reg['mainobj']->seo_metakey );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodmetakey)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['mainparent']->seo_goodmetakey)   );
			else if (  desafelySqlStr($reg['ad_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['ad_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
	}
	if (  $reg['iseocontent']  ){
		if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="icat"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['content_seo_metakey'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['content_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		} else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="icat"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metakey)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metakey);
			else if (  desafelySqlStr($reg['content_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['content_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
		else if (    $_REQUEST['c']=="icontent"    and    $_REQUEST['task']=="view"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->metakey)!='') return desafelySqlStr( $reg['mainobj']->metakey );
			else if (  desafelySqlStr($reg['mainparent']->seo_goodmetakey)!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['mainparent']->seo_goodmetakey)   );
			else if (  desafelySqlStr($reg['content_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->title),   desafelySqlStr($reg['content_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
	}
	if (  $reg['iseofoto']  ){
		if (    $_REQUEST['c']=="foto"    and    ggri('id')==0    ){
			if (  desafelySqlStr($reg['foto_seo_metakey'])!=''  )  return str_replace(   "//**//",   "",   desafelySqlStr($reg['foto_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		} else if (    $_REQUEST['c']=="foto"    and    ggri('id')>0    ){
			if (  desafelySqlStr($reg['mainobj']->seo_metakey)!=''  )  return desafelySqlStr($reg['mainobj']->seo_metakey);
			else if (  desafelySqlStr($reg['foto_seo_metakey'])!=''  )  return str_replace(   "//**//",   desafelySqlStr($reg['mainobj']->name),   desafelySqlStr($reg['foto_seo_metakey'])   );
			else return desafelySqlStr( $reg['siteKeywords'] );
		}
	}
	else return desafelySqlStr( $reg['siteKeywords'] );
	return desafelySqlStr( $reg['siteKeywords'] );
}
function url2seo($icatway, $url_start="/"){
	$url = site_url.$url_start;
	foreach ($icatway as $i=>$icatway1){
		if (  $icatway[$i]->url==''  ) continue;
		$url .= $icatway1->sefname."/";
		$icatway[$i]->url = $url;
	}
	return $icatway;
}
function itable_hr(  $icolspan,  $istyle="x", $itd="x"){
	if (  $istyle=='x'  ) 	$istyle="  ";
	if (  $itd=='x'  ) 		$itd='<hr style=" border:1px solid #cccccc; border-bottom:none; " />';
	?><tr class="workspace" height="15px;" style="<?=$istyle ?>"><td colspan="<?=$icolspan ?>"><?=$itd ?></td></tr><?
}
/**
 * ФУНКЦИЯ search_systems_meta_validation - выводит теги подтверждение прав на сайт.
 */
function search_systems_meta_validation(){
        global $reg;
	print desafelySqlStr(  $reg['promo']->data->search_meta_validation  );
}
/**
 * Кнопка редактировать
 *
 * $type - тип ссылки: exgood, excat, content, icat, typedcontent
 * если указать в $type реальный url - то адрес перехода будет использоваться именно этот
 *
 * $link_format: формат формируемой ссылки  - full - большая картинка
 *                                          - small - маленькая картинка
 *
 * ПРИМЕР: editme(  'content', array('id'=>$reg['mainobj']->id)  ); - ссылка на редактирование статьи / новости
 * ПРИМЕР: editme(  '/iadmin/index2.php?ca=exgood&task=editA&id=443', array('note'=>'изменить это')  );
 * @param <string> $type
 * @param <array> $params
 */
function editme($type, $params=NULL, $link_format='full'){
	global $my;
	if (  $my->gid>23  ){
		$link   = '';
                $target =' target="_blank" ';
		switch (  $type  ){
			case 'exfoto':       $link = site_url.'/iadmin/index2.php?ca=exfoto&task=editA&id='.$params['id'].'&hidemainmenu=1&search=&filter_type=&filter_logged='; 		break;
                        case 'exfoto_list':  $link = site_url.'/iadmin/index2.php?ca=foto&type=exfoto&parent='.$params['id'].'&fotocat=0';                                                      break;
			case 'exgood':       $link = site_url.'/iadmin/index2.php?ca=exgood&task=editA&id='.$params['id'].'&hidemainmenu=1&search=&filter_type=&filter_logged='; $target='';	break;
			case 'excat':        $link = site_url.'/iadmin/index2.php?ca=excat&task=editA&id='.$params['id'].'&hidemainmenu=1&search=&filter_type=&filter_logged=';		 	break;
                        case 'excat_list':   $link = site_url.'/iadmin/index2.php?ca=exgood&task=view&icsmart_exgood_parent='.$params['id'];                                                    break;
                        case 'Добавить подкатегорию':   $link = site_url.'/iadmin/index2.php?ca=excat&task=new&id=0&hidemainmenu=1&search=&filter_type=&filter_logged=&parent='.$params['parent'];   break;
                        case 'Добавить товар':   $link = site_url.'/iadmin/index2.php?ca=exgood&task=new&id=0&hidemainmenu=1&search=&filter_type=&filter_logged=&icsmart_exgood_parent='.$params['parent'];   break;
                        case 'все категории каталога': $link = site_url.'/iadmin/index2.php?ca=excat&task=view';                                    break;
			case 'content':      $link = site_url.'/iadmin/index2.php?ca=content&task=edit&hidemainmenu=1&id='.$params['id'];							break;
			case 'icat':         $link = site_url.'/iadmin/index2.php?ca=icat&task=editA&id='.$params['id'].'&hidemainmenu=1&search=&filter_type=&filter_logged=';			break;
                        case 'Новая рубрика': $link = site_url.'/iadmin/index2.php?ca=icat&task=new&id=0&hidemainmenu=1&search=&filter_type=&filter_logged=';                                    break;
                        case 'Все рубрики': $link = site_url.'/iadmin/index2.php?ca=icat&task=view';                                    break;
                        case 'icat_list':    $link = site_url.'/iadmin/index2.php?ca=content&icsmart_content_catid='.$params['id'];                                                             break;
                        case 'icat_add_content':    $link = site_url.'/iadmin/index2.php?ca=content&task=new&hidemainmenu=1&id=0&icsmart_content_catid='.$params['catid'];                                                             break;
			case 'typedcontent': $link = site_url.'/iadmin/index2.php?ca=typedcontent&task=edit&hidemainmenu=1&id='.$params['id'];							break;
                        case 'menu':         $link = site_url.'/iadmin/index2.php?ca=menus&menutype='.$params['menutype'];                                                                      break;
                        case 'module':       $link = site_url.'/iadmin/index2.php?ca=modules&client=&task=editA&hidemainmenu=1&id='.$params['id'];                                              break;
                        case 'file_list':    $link = site_url.'/iadmin/index2.php?ca=file&type='.$params['type'].'&parent='.$params['id'].'&filecat=0';                                         break;
                        case 'body'     :    $urlcode = codeurl( $_SERVER['REQUEST_URI'] );
                                             $link = site_url.'/iadmin/index2.php?ca=menus&menutype='.$params['menutype'].'&task=edit&type=url&hidemainmenu=1&link='.$urlcode;                  break;
                        default:             $link = $type;	break;
		}
                $textlink = isset($params['note'])?$params['note']:'Редактировать';

                $imgfilename = isset($params['img'])?$params['img']:'edit';

                if (  $link_format=='full'){
                    ?><div style="display:block;"><p style="vertical-align: middle; height: 32px;"><a <?=$target ?> href="<?=$link ?>" ><img src="/includes/images/<?=$imgfilename ?>.png" width="32" height="32" border="0" align="absmiddle" /></a>&nbsp;&nbsp;&nbsp;<a class="fulledita" <?=$target ?> href="<?=$link ?>" ><?=$textlink ?></a></p></div><?
                } else {
                    ?><div style="display:block"><a <?=$target ?> href="<?=$link ?>" ><img src="/includes/images/<?=$imgfilename ?>16.png" width="16" height="16" border="0" align="middle" /></a>&nbsp;<a class="smalledita" <?=$target ?> href="<?=$link ?>" ><?=$textlink ?></a></div><?
                }
	}
}
function YandexGoogleFoto($name=''){
	global $my;
	if (  $my->gid>23  ){
		$name = desafelySqlStr($name);
                $name = str_replace(  array('(',  ')',  '"',  "'"),     '',     $name );
 		?><a target="_blank" href="http://images.yandex.ru/yandsearch?text=<?=$name ?>"><img src="/includes/images/yfoto.gif" style="float:left;"  /></a><?
		?><a target="_blank" href="http://www.google.ru/images?hl=ru&rlz=&q=<?=$name ?>&um=1&ie=UTF-8&source=og&sa=N&tab=wi&biw=1280&bih=933"><img src="/includes/images/gfoto.gif" style="float:left;"  /></a><?
	}
}
/**
 * ОТОБРАЖАЕМ ОТЛАДОЧНУЮ ИНФОРМАЦИЮ
 *  (только если установлен $reg['show_debug_info'])
 */
function show_debug_info(){
    global $reg;
    if (  ($reg['my']->gid>23  and  $reg['show_debug_info']==1)  or  (isset($_REQUEST['who']))  ){
            ggtr5 ($reg);
            ggtr5 ($_REQUEST);
    }
}
?>