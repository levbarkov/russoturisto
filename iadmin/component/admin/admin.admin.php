<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $task;
switch ($task) {
	case 'view_stat_sbot_days'	: view_stat_sbot_days(); 		break;
	case 'view_gen_cont'		: view_gen_cont(); 				break;
	case 'view_gen_razd'		: view_gen_razd();				break;
	case 'view_error_razd'		: view_error_razd();			break;
	case 'view_gen_search'		: view_gen_search();			break;
	case 'view_gen_sbot'		: view_gen_sbot();				break;
	case 'view_gen_sbot_site'	: view_gen_sbot_site();			break;
	case 'clean_gen_razd'		: clean_gen_razd();				break;
	case 'clean_gen_search'		: clean_gen_search();			break;
	case 'clean_gen_sbot'		: clean_gen_sbot();				break;
	case 'clean_gen_sbot_site'	: clean_gen_sbot_site();		break;
	case 'clean_stat_sbot_days'	: clean_stat_sbot_days();		break;
	case 'clean_error_razd'		: clean_error_razd();			break;
	case 'preview'				: HTML_admin_misc::preview();	break;
	case 'preview2'				: HTML_admin_misc::preview( 1 ); break;
	case 'cpanel':
	default						: istatPanel();					break;
}
function istatPanel(){
global $database, $my, $option, $reg;
?><table class="adminheading"><tbody><tr><th class="edit">Общая статистика сайта</th></tr></tbody></table>
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title">Название&nbsp;раздела&nbsp;сайта&nbsp;(Топ&nbsp;10)</th>
						<th class="title">ip пользователя</th>
						<th class="title">Время последнего посещения</th>
                        <th class="title">Просмотров</th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<?
				$query = "SELECT * "
				. "\n FROM #__stat "
				. "\n ORDER BY ctime DESC"
				;
				$database->setQuery( $query, 0, 10 );
				$rows = $database->loadObjectList();
				$rowi = 1;
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0;
				foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				
				foreach ( $rows as $row ){
				$rowi = 1 - $rowi;
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print stripslashes(  addhttp_ifneed($row->url)  ); ?>"><?php echo stripslashes($row->desc);?></a></td>
						<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
						<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td><? print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
				<tr height="35px">
                        <td colspan="5" align="center" valign="middle" style="text-align:center; vertical-align:middle; "><a href="index2.php?ca=<? print $option; ?>&task=view_gen_razd&sort=ctime" class="imagelist_class">Cмотреть полный список</a></td>
                </tr>
           </tbody></table>
		</td>
	</tr>
</table>
<table class="adminheading"><tbody><tr><th class="edit">Статистика поисковых запросов сайта</th></tr></tbody></table>
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title">Поисковый&nbsp;запрос&nbsp;(Топ&nbsp;10)</th>
                        <th class="title">Просмотров</th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<?
				$query = "SELECT * "
				. "\n FROM #__stat_search "
				. "\n ORDER BY cnt DESC"
				;
				$database->setQuery( $query, 0, 10 );
				$rows = $database->loadObjectList();
				$rowi = 1;
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0;
				foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				
				foreach ( $rows as $row ){
				$rowi = 1 - $rowi;
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print site_url; ?>/index.php?pi=10&c=search&isearch=<? print stripslashes(  $row->search_term  ); ?>"><?php echo stripslashes($row->search_term);?></a></td>
						<td><? print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
				<tr height="35px">
                        <td colspan="3" align="center" valign="middle" style="text-align:center; vertical-align:middle; "><a href="index2.php?ca=<? print $option; ?>&task=view_gen_search" class="imagelist_class">Cмотреть полный список</a></td>
                </tr>
           </tbody></table>
		</td>
	</tr>
</table>
<table class="adminheading"><tbody><tr><th class="edit">Статистика переходов из поисковых систем (yandex, google, rambler, yahoo, aport, mail)</th></tr></tbody></table>
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title">Поисковый&nbsp;запрос&nbsp;(Топ&nbsp;10)</th>
						<th class="title">Поисковая система</th>
						<th class="title">ip пользователя</th>
                        <th class="title">Просмотров</th>
                        <th class="title">Время последнего посещения</th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<? $query = "SELECT * FROM #__stat_sbot ORDER BY ctime DESC";
				$database->setQuery( $query, 0, 10 ); $rows = $database->loadObjectList(); $rowi = 1;
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0; foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;

				foreach ( $rows as $row ){ $rowi = 1 - $rowi;  $search_text = (  trim($row->text)!='' ) ? stripslashes(  $row->text  ):'##без названия##';
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print stripslashes(  $row->url  ); ?>"><?php echo $search_text; ?></a></td>
						<td><? print $row->sbot; ?></td>
						<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
						<td><? print $row->cnt; ?></td>
						<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
				<tr height="35px">
                        <td colspan="6" align="center" valign="middle" style="text-align:center; vertical-align:middle; "><a href="index2.php?ca=<? print $option; ?>&task=view_gen_sbot&sort=ctime" class="imagelist_class">Cмотреть полный список</a></td>
                </tr>
           </tbody></table>
		</td>
	</tr>
</table><? $site_url_short = getshorturl(site_url); ?>
<table class="adminheading"><tbody><tr><th class="edit">Индексация сайта поисковыми системами</th></tr></tbody></table>
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
			<table class="adminlist">
                <tbody>
                <tr >
						<td width="25%" class="admin_ps_td" ><img src="/iadmin/theme/admin/images/ps/yandex.gif" width="135" height="78" border="0" /><br /><?
							?><ul><li><a target="_blank" href="http://yandex.ru/yandsearch?serverurl=<?=$site_url_short?>">для сайта <?=$site_url_short?></a></li>
							<li><a target="_blank" href="http://yandex.ru/yandsearch?serverurl=www.<?=$site_url_short?>">для сайта www.<?=$site_url_short?></a></li></ul></td>
						<td width="25%" class="admin_ps_td" ><img src="/iadmin/theme/admin/images/ps/google.gif" width="187" height="78" border="0" align="absmiddle" /><br /><?
							?><ul><li><a target="_blank" href="http://www.google.ru/search?as_q=&hl=ru&newwindow=1&num=10&btnG=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA+%D0%B2+Google&as_epq=&as_oq=&as_eq=&lr=&cr=&as_ft=i&as_filetype=&as_qdr=all&as_occt=any&as_dt=i&as_sitesearch=<?=$site_url_short?>&as_rights=&safe=images">для сайта <?=$site_url_short?></a></li>
							<li><a target="_blank" href="http://www.google.ru/search?as_q=&hl=ru&newwindow=1&num=10&btnG=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA+%D0%B2+Google&as_epq=&as_oq=&as_eq=&lr=&cr=&as_ft=i&as_filetype=&as_qdr=all&as_occt=any&as_dt=i&as_sitesearch=www.<?=$site_url_short?>&as_rights=&safe=images">для сайта www.<?=$site_url_short?></a></li></ul></td>
						<td width="25%" class="admin_ps_td" ><img src="/iadmin/theme/admin/images/ps/aport.gif" width="139" height="78" border="0"  /><br /><?
							?><ul><li><a target="_blank" href="http://sm.aport.ru/scripts/template.dll?That=std&r=URL%3D<?=$site_url_short?>">для сайта <?=$site_url_short?></a></li>
							<li><a target="_blank" href="http://sm.aport.ru/scripts/template.dll?That=std&r=URL%3Dwww.<?=$site_url_short?>">для сайта www.<?=$site_url_short?></a></li></ul></td>
						<td width="25%" class="admin_ps_td" ><img src="/iadmin/theme/admin/images/ps/rambler.gif" width="261" height="78" border="0" align="absmiddle" /><br /><?
							?><ul><li><a target="_blank" href="http://nova.rambler.ru/srch?query=&and=1&dlang=0&mimex=0&st_date=&end_date=&news=0&limitcontext=0&exclude=&filter=<?=$site_url_short?>&sort=3&pagelen=15&gopic=%D0%9D%D0%B0%D0%B9%D1%82%D0%B8">для сайта <?=$site_url_short?></a></li>
							<li><a target="_blank" href="http://nova.rambler.ru/srch?query=&and=1&dlang=0&mimex=0&st_date=&end_date=&news=0&limitcontext=0&exclude=&filter=www.<?=$site_url_short?>&sort=3&pagelen=15&gopic=%D0%9D%D0%B0%D0%B9%D1%82%D0%B8">для сайта www.<?=$site_url_short?></a></li></ul></td>
                 </tr>			 
           </tbody></table>
		</td>
	</tr>
</table><?
$promo_data = ggo (1, "#__promo");
$ext_stats = isset($promo_data->ext_stat) ?  explode('<br />', nl2br($promo_data->ext_stat)) : array();
if (count($ext_stats) > 0  and  $ext_stats[0] != ''){
?><table class="adminheading"><tbody><tr><th class="edit">Дополнительная статистика</th></tr></tbody></table>
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
                <table class="adminlist">
                <tbody>
                <tr >
                    <td width="100%" class="admin_ps_td" ><?
                    foreach ($ext_stats as $ext_stat){
                        ?><a href="<?=$ext_stat ?>"><img src="/includes/images/<?=promo::get_stat_img($ext_stat); ?>" width="135" height="78" border="0" /></a>
                    <? } ?>
                    </td>
                 </tr>
           </tbody></table>
		</td>
	</tr>
</table>
<? } ?>
<table class="adminheading"><tbody><tr><th class="edit">Статистика переходов из внешних web-сайтов</th></tr></tbody></table>
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title">Название сайта&nbsp;запрос&nbsp;(Топ&nbsp;10)</th>
						<th class="title">ip пользователя</th>
                        <th class="title">Просмотров</th>
                        <th class="title">Время последнего посещения</th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr><?
				$query = "SELECT * FROM #__stat_sbot_site ORDER BY ctime DESC";
				$database->setQuery( $query, 0, 10 ); $rows = $database->loadObjectList();
				$rowi = 1;
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0; foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;

				foreach ( $rows as $row ){ $rowi = 1 - $rowi;
				?><tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print stripslashes(  $row->url  ); ?>"><?php echo stripslashes($row->site);?></a></td>
						<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
						<td><? print $row->cnt; ?></td>
						<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
				<tr height="35px">
                        <td colspan="6" align="center" valign="middle" style="text-align:center; vertical-align:middle; "><a href="index2.php?ca=<? print $option; ?>&task=view_gen_sbot_site&sort=ctime" class="imagelist_class">Cмотреть полный список</a></td>
                </tr>
           </tbody></table>
		</td>
	</tr>
</table>
<table class="adminheading"><tbody><tr><th class="edit">Статистика посещаемости сайта по дням</th></tr></tbody></table>
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title">Последние 10 дней</th>
                        <th class="title">Время последнего посещения</th>
                        <th class="title">Просмотров</th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr><?
				$query = "SELECT * FROM #__stat_sbot_days ORDER BY last DESC "; $database->setQuery( $query, 0, 10 ); $rows = $database->loadObjectList();
				$rowi = 1;
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0; foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				foreach ( $rows as $row ){ $rowi = 1 - $rowi;
				?><tr class="row<? print $rowi; ?>">
						<td align="left"><?php echo $row->cdate; ?></td>
						<td align="left"><?php $cdate = getdate($row->last); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td align="left"><?php print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
				<tr height="35px">
                        <td colspan="6" align="center" valign="middle" style="text-align:center; vertical-align:middle; "><a href="index2.php?ca=<? print $option; ?>&task=view_stat_sbot_days&sort=ctime" class="imagelist_class">Cмотреть полный список</a></td>
                </tr>
           </tbody></table>
		</td>
	</tr>
</table>
<? if (  $my->id==2478  ){ ?>
	<table class="adminheading"><tbody><tr><th class="edit">Статистика ошибок сайта</th></tr></tbody></table>
	<table border="0" width="100%">
		<tr>
			<td width="100%" valign="top" style="vertical-align:top">
				<table class="adminlist">
					<tbody>
						<tr>
							<th class="title">Адрес&nbsp;страницы&nbsp;с&nbsp;ошибкой&nbsp;(Топ&nbsp;10)</th>
							<th class="title">Адрес&nbsp;внешней&nbsp;страницы</th>
							<th class="title">ip пользователя</th>
							<th class="title">Время последнего посещения</th>
							<th class="title">Просмотров</th>
							<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
						</tr>
					<?
					$rows = ggsql ("SELECT * FROM #__stat_nopage ORDER BY ctime DESC", 0, 10);
					$rowi = 1;
					// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
					$max_cnt = 0;
					foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
					
					foreach ( $rows as $row ){
					$rowi = 1 - $rowi;
					?>
					<tr class="row<? print $rowi; ?>">
							<td align="left"><a target="_blank" href="<? print stripslashes(  addhttp_ifneed($row->url)  ); ?>"><?php echo substr( stripslashes(urldecode($row->url)),0, 59 );?></a></td>
							<td align="left"><a target="_blank" href="<? print stripslashes(  addhttp_ifneed($row->url_reffer)  ); ?>"><?php echo substr( stripslashes(urldecode($row->url_reffer)),0, 59 );?></a></td>
							<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
							<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
							<td><? print $row->cnt; ?></td>
							<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
					 </tr>			 
					 <? } ?>
					<tr height="35px">
							<td colspan="6" align="center" valign="middle" style="text-align:center; vertical-align:middle; "><a href="index2.php?ca=<? print $option; ?>&task=view_error_razd&sort=ctime" class="imagelist_class">Cмотреть полный список</a></td>
					</tr>
			   </tbody></table>
			</td>
		</tr>
	</table>
	<?
}
}
function view_stat_sbot_days(){ //вывод статистики чисто по дням 
global $database, $my, $option;
?><form name="adminForm" method="post" action="index2.php">
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Статистика посещаемости сайта по дням";
			$iway[0]->url="";
			i24pwprint_admin ($iway);
			?></td></tr>
		</table><?
		switch (  ggrr('sort')  ){
			case 'ctime' 	: $isort  ="ORDER BY last DESC"; break;
			case 'cdate' 	: $isort  ="ORDER BY cdate DESC"; break;
			default			: $isort  ="ORDER BY cnt DESC"; break;
		} ?>
			<table class="adminlist">
				<tbody><tr>	
                        <th class="title"><? if (  ggrr('sort')!='cdate'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=cdate">День</a><? } else { ?>День<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!='ctime'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ctime">Время последнего посещения</a><? } else { ?>Время последнего посещения<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!=''  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>">Просмотров</a><? } else { ?>Просмотров<? } ?></th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr><? $rows = ggsql("SELECT * FROM #__stat_sbot_days $isort ");  $rowi = 1;
				$max_cnt = 0; foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;	// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				foreach ( $rows as $row ){ $rowi = 1 - $rowi;
                ?><tr class="row<? print $rowi; ?>">
						<td align="left"><?php echo $row->cdate; ?></td>
						<td align="left"><?php $cdate = getdate($row->last); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td align="left"><?php print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
           </tbody></table>
		</td>
	</tr>
</table>
<input type="hidden" value="admin" name="ca"/>
<input type="hidden" value="" name="task"/>
<input type="hidden" value="0" name="boxchecked"/>
<input type="hidden" value="0" name="hidemainmenu"/>
</form><? return;
	
	
	
	
}

function clean_gen_razd(){
	global $my;
	if (  $my->gid>23  ) ggsqlq ( "TRUNCATE #__stat" );
	else {  ?><script language="javascript">alert ('У Вас недостаточно прав для осуществления данной функции')</script><?	}
	view_gen_razd();
}
function clean_gen_search(){
	global $my;
	if (  $my->gid>23  ) ggsqlq ( "TRUNCATE #__stat_search" );
	else {  ?><script language="javascript">alert ('У Вас недостаточно прав для осуществления данной функции')</script><?	}
	view_gen_search();
}
function clean_gen_sbot(){
	global $my;
	if (  $my->gid>23  ) ggsqlq ( "TRUNCATE #__stat_sbot" );
	else {  ?><script language="javascript">alert ('У Вас недостаточно прав для осуществления данной функции')</script><?	}
	view_gen_sbot();
}
function clean_gen_sbot_site(){
	global $my;
	if (  $my->gid>23  ) ggsqlq ( "TRUNCATE #__stat_sbot_site " );
	else {  ?><script language="javascript">alert ('У Вас недостаточно прав для осуществления данной функции')</script><?	}
	view_gen_sbot_site();
}
function clean_stat_sbot_days(){
	global $my;
	if (  $my->gid>23  ) ggsqlq ( "TRUNCATE #__stat_sbot_days " );
	else {  ?><script language="javascript">alert ('У Вас недостаточно прав для осуществления данной функции')</script><?	}
	view_stat_sbot_days();
}
function clean_error_razd(){
	global $my;
	if (  $my->gid>23  ) ggsqlq ( "TRUNCATE #__stat_nopage " );
	else {  ?><script language="javascript">alert ('У Вас недостаточно прав для осуществления данной функции')</script><?	}
	view_error_razd();
}



function view_error_razd(){
global $database, $my, $option;
switch (  ggrr('sort')  ){
	case 'ctime' 	: $isort  ="ORDER BY ctime DESC"; break;
	case 'ip' 		: $isort  ="ORDER BY ip"; break;
	case 'url' 	: $isort  ="ORDER BY url"; break;
	case 'url_reffer' 	: $isort  ="ORDER BY url_reffer"; break;
	default			: $isort  ="ORDER BY cnt DESC"; break;
} 
?><form name="adminForm" method="post" action="index2.php">
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Статистика ошибок сайта";
			$iway[0]->url="";
			i24pwprint_admin ($iway);
			?></td></tr></table>
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title"><? if (  ggrr('sort')!='url'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=url">Адрес&nbsp;страницы&nbsp;с&nbsp;ошибкой</a><? } else { ?>Адрес&nbsp;страницы&nbsp;с&nbsp;ошибкой<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='url_reffer'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=url_reffer">Адрес&nbsp;внешней&nbsp;страницы</a><? } else { ?>Адрес&nbsp;внешней&nbsp;страницы<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='ip'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ip">ip пользователя</a><? } else { ?>ip пользователя<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='ctime'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ctime">Время последнего посещения</a><? } else { ?>Время последнего посещения<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!=''  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>">Просмотров</a><? } else { ?>Просмотров<? } ?></th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<?
				$query = "SELECT * FROM #__stat_nopage $isort "; $database->setQuery( $query ); $rows = $database->loadObjectList(); $rowi = 1; //  ggtr01($query);
				
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0;
				foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				
				foreach ( $rows as $row ){
				$rowi = 1 - $rowi;
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print stripslashes(  addhttp_ifneed($row->url)  ); ?>"><?php echo substr( stripslashes(urldecode($row->url)),0, 59 );?></a></td>
						<td align="left"><a target="_blank" href="<? print stripslashes(  addhttp_ifneed($row->url_reffer)  ); ?>"><?php echo substr( stripslashes(urldecode($row->url_reffer)),0, 59 );?></a></td>
						<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
						<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td><? print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
           </tbody></table>
		</td>
	</tr>
</table>
<input type="hidden" value="admin" name="ca"/>
<input type="hidden" value="" name="task"/>
<input type="hidden" value="0" name="boxchecked"/>
<input type="hidden" value="0" name="hidemainmenu"/>
</form>
	<?
}



function view_gen_razd(){
global $database, $my, $option;
switch (  ggrr('sort')  ){
	case 'ctime' 	: $isort  ="ORDER BY ctime DESC"; break;
	case 'ip' 		: $isort  ="ORDER BY ip"; break;
	case 'desc' 	: $isort  ="ORDER BY #__stat.desc"; break;
	default			: $isort  ="ORDER BY cnt DESC"; break;
} 
?><form name="adminForm" method="post" action="index2.php">
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Общая статистика сайта";
			$iway[0]->url="";
			i24pwprint_admin ($iway);
			?></td></tr></table>
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title"><? if (  ggrr('sort')!='desc'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=desc">Название&nbsp;раздела&nbsp;сайта</a><? } else { ?>Название&nbsp;раздела&nbsp;сайта<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='ip'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ip">ip пользователя</a><? } else { ?>ip пользователя<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='ctime'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ctime">Время последнего посещения</a><? } else { ?>Время последнего посещения<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!=''  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>">Просмотров</a><? } else { ?>Просмотров<? } ?></th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<?
				$query = "SELECT * FROM #__stat $isort "; $database->setQuery( $query ); $rows = $database->loadObjectList(); $rowi = 1;
				
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0;
				foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				
				foreach ( $rows as $row ){
				$rowi = 1 - $rowi;
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print stripslashes(  addhttp_ifneed($row->url)  ); ?>"><?php echo stripslashes($row->desc);?></a></td>
						<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
						<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td><? print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
           </tbody></table>
		</td>
	</tr>
</table>
<input type="hidden" value="admin" name="ca"/>
<input type="hidden" value="" name="task"/>
<input type="hidden" value="0" name="boxchecked"/>
<input type="hidden" value="0" name="hidemainmenu"/>
</form>
	<?
}

function view_gen_search(){
global $database, $my, $option;
switch (  ggrr('sort')  ){
	case 'term' 	: $isort  ="ORDER BY search_term"; break;
	default			: $isort  ="ORDER BY cnt DESC"; break;
}
?><form name="adminForm" method="post" action="index2.php">
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Статистика поисковых запросов сайта";
			$iway[0]->url="";
			i24pwprint_admin ($iway);
			?></td></tr></table>
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title"><? if (  ggrr('sort')!='term'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=term">Поисковый&nbsp;запрос</a><? } else { ?>Поисковый&nbsp;запрос<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!=''  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>">Просмотров</a><? } else { ?>Просмотров<? } ?></th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<?
				$query = "SELECT * FROM #__stat_search $isort ";  $database->setQuery( $query );  $rows = $database->loadObjectList(); $rowi = 1;
				
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0;
				foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				
				foreach ( $rows as $row ){
				$rowi = 1 - $rowi;
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print site_url; ?>/index.php?pi=10&c=search&isearch=<? print (  $row->search_term  ); ?>"><?php echo htmlspecialchars($row->search_term, ENT_QUOTES);?></a></td>
						<td><? print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
           </tbody></table>
		</td>
	</tr>
</table>
<input type="hidden" value="admin" name="ca"/>
<input type="hidden" value="" name="task"/>
<input type="hidden" value="0" name="boxchecked"/>
<input type="hidden" value="0" name="hidemainmenu"/>
</form>
	<?
}
function view_gen_sbot(){
global $database, $my, $option;
	?>
<form name="adminForm" method="post" action="index2.php">
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Статистика переходов из поисковых систем (yandex, google, rambler, yahoo, aport, mail)";
			$iway[0]->url="";
			i24pwprint_admin ($iway);
			?></td></tr>
		</table><?
switch (  ggrr('sort')  ){
	case 'ctime' 	: $isort  ="ORDER BY ctime DESC"; break;
	case 'ip' 		: $isort  ="ORDER BY ip"; break;
	case 'text' 	: $isort  ="ORDER BY text"; break;
	case 'sbot' 	: $isort  ="ORDER BY sbot"; break;
	default			: $isort  ="ORDER BY cnt DESC"; break;
} ?>
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title"><? if (  ggrr('sort')!='text'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=text">Поисковый&nbsp;запрос</a><? } else { ?>Поисковый&nbsp;запрос<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='sbot'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=sbot">Поисковая система</a><? } else { ?>Поисковая система<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='ip'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ip">ip пользователя</a><? } else { ?>ip пользователя<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!='ctime'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ctime">Время последнего посещения</a><? } else { ?>Время последнего посещения<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!=''  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>">Просмотров</a><? } else { ?>Просмотров<? } ?></th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<?
				$query = "SELECT * FROM #__stat_sbot $isort ";  $database->setQuery( $query ); $rows = $database->loadObjectList(); $rowi = 1;
//				if (  isset($_REQUEST['ctime'])  ) $query .= "\n ORDER BY ctime DESC";
//				else $query .= "\n ORDER BY cnt DESC";
				
				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0;
				foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				
				foreach ( $rows as $row ){
				$rowi = 1 - $rowi;    $search_text = (  trim($row->text)!='' ) ? stripslashes(  $row->text  ):'##без названия##';
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print (  $row->url  ); ?>"><?php echo $search_text  ?></a></td>
						<td><? print $row->sbot; ?></td>
						<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
						<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td><? print $row->cnt; ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
           </tbody></table>
		</td>
	</tr>
</table>
<input type="hidden" value="admin" name="ca"/>
<input type="hidden" value="" name="task"/>
<input type="hidden" value="0" name="boxchecked"/>
<input type="hidden" value="0" name="hidemainmenu"/>
</form>
	<?
}

function view_gen_sbot_site(){
global $database, $my, $option;
switch (  ggrr('sort')  ){
	case 'ctime' 	: $isort  ="ORDER BY ctime DESC"; break;
	case 'ip' 		: $isort  ="ORDER BY ip"; break;
	case 'site' 	: $isort  ="ORDER BY site"; break;
	default			: $isort  ="ORDER BY cnt DESC"; break;
}
?><form name="adminForm" method="post" action="index2.php">
<table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Статистика переходов из внешних web-сайтов";
			$iway[0]->url="";
			i24pwprint_admin ($iway); ?></td></tr>
		</table><?
			?><table class="adminlist">
                <tbody>
					<tr>
                        <th class="title"><? if (  ggrr('sort')!='site'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=site">Сайт</a><? } else { ?>Сайт<? } ?></th>
						<th class="title"><? if (  ggrr('sort')!='ip'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ip">ip пользователя</a><? } else { ?>ip пользователя<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!=''  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>">Просмотров</a><? } else { ?>Просмотров<? } ?></th>
                        <th class="title"><? if (  ggrr('sort')!='ctime'  ){ ?><a href="index2.php?ca=<?=$option ?>&task=<?=ggrr('task') ?>&sort=ctime">Время последнего посещения</a><? } else { ?>Время последнего посещения<? } ?></th>
						<th class="title" valign="top" style="vertical-align:top"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="250" height="1" /></th>
	                </tr>
				<?
				$query = "SELECT * FROM #__stat_sbot_site $isort "; $database->setQuery( $query ); $rows = $database->loadObjectList(); $rowi = 1;
//				if (  $_REQUEST['ctime']==1  ) $query .= "\n ORDER BY ctime DESC";
//				else $query .= "\n ORDER BY cnt DESC";

				// ОПРЕДЕЛЕНИЕ МАКСИМАЛЬНОГО ЗНАЧЕНИЯ
				$max_cnt = 0;
				foreach ( $rows as $row ) if (  $row->cnt>$max_cnt) $max_cnt = $row->cnt;
				
				foreach ( $rows as $row ){
				$rowi = 1 - $rowi;
				?>
                <tr class="row<? print $rowi; ?>">
						<td align="left"><a target="_blank" href="<? print (  $row->url  ); ?>"><?php echo htmlspecialchars($row->site, ENT_QUOTES);?></a></td>
						<td><a target='_blank' href='<?=site_statistics::get_ip_addr_url($row->ip); ?>'><? print $row->ip; ?></a></td>
						<td><? print $row->cnt; ?></td>
						<td><? $cdate = getdate($row->ctime); print $cdate['year'].".".num::fillzerro($cdate['mon'],2).".".num::fillzerro($cdate['mday'],2)."&nbsp;&nbsp;&nbsp;".num::fillzerro($cdate['hours'],2).":".num::fillzerro($cdate['minutes'],2); ?></td>
						<td valign="middle" align="left"  class="per_stat_css"><img src="/iadmin/theme/admin/images/stat_fix.gif" width="<? print round(  250*($row->cnt/$max_cnt)  ) ?>" height="14" /></td>
                 </tr>			 
				 <? } ?>
           </tbody></table>
		</td>
	</tr>
</table>
<input type="hidden" value="admin" name="ca"/>
<input type="hidden" value="" name="task"/>
<input type="hidden" value="0" name="boxchecked"/>
<input type="hidden" value="0" name="hidemainmenu"/>
</form>
	<?
}
?>