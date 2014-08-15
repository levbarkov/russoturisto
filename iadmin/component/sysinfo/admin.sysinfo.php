<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
		global  $database, $mosConfig_cachepath, $mainframe, $DBPrefix, $reg;

		$width = 400;	// width of 100%
		$tabs = new iTabs(0);
		?>
		<table class="adminheading"><tr><th class="info">Информация</th></tr></table>		
		<?php
		$tabs->startPane("sysinfo");
		$tabs->startTab("Проверки","system-page");
		require_once( site_path.'/iadmin/install_check.php' );
		$install_check = new install_check();
		?>
			<table class="adminform" border="1">
			<tr>
				<th>Права доступа на каталоги</th>
			</tr>
			<tr>
				<td><?php $sp = ini_get('session.save_path');
					mosHTML::writableCell( 'images' );
					mosHTML::writableCell( $sp, 0, '<strong>Каталог сессий</strong> ' ); ?>
				</td>
			</tr>
			<tr>
				<th>Важные настройки PHP</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0">
                                        <? if (@fopen(site_path."/cleanme.php", "r")) { ?>
					<tr>
						<td colspan="2"><b>Не удален файл www/cleanme.php</b><br>Только удаление, нельзя переименовывать</td>
						<td>
							<?php $img = 'error.png'; ?>
							<img src="images/<?php echo $img; ?>" />
						</td>
					</tr>
                                        <? } ?>

					<tr>
						<td>Register Globals:</td>
						<td style="font-weight: bold;">
							<?php echo get_php_setting('register_globals',1,0); ?>
						</td>
						<td>
							<?php $img = ((ini_get('register_globals')) ? 'error.png' : 'ok.png'); ?>
							<img src="images/<?php echo $img; ?>" />
						</td>
					</tr>
					<tr>
						<td>Magic Quotes:</td>
						<td style="font-weight: bold;">
							<?php echo get_php_setting('magic_quotes_gpc',1,1); ?>
						</td>
						<td>
							<?php $img = (!(ini_get('magic_quotes_gpc')) ? 'error.png' : 'ok.png'); ?>
							<img src="images/<?php echo $img; ?>" />
						</td>
					</tr>
					<tr>					
						<td>Safe Mode:</td>
						<td style="font-weight: bold;">
							<?php echo get_php_setting('safe_mode',1,0); ?>
						</td>
						<td>
							<?php $img = ((ini_get('safe_mode')) ? 'error.png' : 'ok.png'); ?>
							<img src="images/<?php echo $img; ?>" />
						</td>
					</tr>
					<tr>
						<td>File Uploads:</td>
						<td style="font-weight: bold;">
							<?php echo get_php_setting('file_uploads',1,1); ?>
						</td>
						<td>
							<?php $img = ((!ini_get('file_uploads')) ? 'error.png' : 'ok.png'); ?>
							<img src="images/<?php echo $img; ?>" />
						</td>
					</tr>
					<tr>
						<td>Session auto start:</td>
						<td style="font-weight: bold;">
							<?php echo get_php_setting('session.auto_start',1,0); ?>
						</td>
						<td>
							<?php $img = ((ini_get('session.auto_start')) ? 'error.png' : 'ok.png'); ?>
							<img src="images/<?php echo $img; ?>" />
						</td>
					</tr>
					<tr>
						<td>Session save path:</td>
						<td style="font-weight: bold;" colspan="2">
							<?php echo (($sp=ini_get('session.save_path'))?$sp:'none'); ?>
						</td>
					</tr>
					<tr>
						<td>Short Open Tags:</td>
						<td style="font-weight: bold;">
							<?php echo get_php_setting('short_open_tag'); ?>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td>Output Buffering:</td>
						<td style="font-weight: bold;">
							<?php echo get_php_setting('output_buffering'); ?>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td>Open basedir:</td>
						<td style="font-weight: bold;" colspan="2">
							<?php echo (($ob = ini_get('open_basedir')) ? $ob : 'none'); ?>
						</td>
					</tr>
					<tr>
						<td>Display Errors:</td>
						<td style="font-weight: bold;" colspan="2">
							<?php echo get_php_setting('display_errors'); ?>
						</td>
					</tr>
					<tr>
						<td>XML enabled:</td>
						<td style="font-weight: bold;" colspan="2">
						<?php echo extension_loaded('xml')?'Yes':'No'; ?>
						</td>
					</tr>
					<tr>
						<td>Zlib enabled:</td>
						<td style="font-weight: bold;" colspan="2">
							<?php echo extension_loaded('zlib')?'Yes':'No'; ?>
						</td>
					</tr>
					<tr>
						<td>Disabled Functions:</td>
						<td style="font-weight: bold;" colspan="2">
							<?php echo (($df=ini_get('disable_functions'))?$df:'none'); ?>
						</td>
					</tr>
					</table>
				</td>
			</tr>

			<tr>
				<th>Ошибки установки Insite</th>
			</tr><?
					/*/
                                        $install_check->check_error('sql_log', '1', '&mdash; Включен режим отладки SQL // МЕНЮ: Сайт / Конфигурация поле sql_log ');
                                        $install_check->check_error('show_debug_info', '1', '&mdash; Включено отображения отладочной информации // МЕНЮ: Сайт / Конфигурация поле show_debug_info ');

					$install_check->check_error('sitename', 'СМС - Инсайт', '&mdash; Не задано имя сайта // МЕНЮ: Сайт / Конфигурация поле _mes_ ');
					$install_check->check_error('siteTitle', 'Создание сайтов Красноярск, веб дизайн, разработка сайтов и продвижение. КрасИнсайт. Изготовление web сайта Красноярск', '&mdash; Не задан title сайта // МЕНЮ: Сайт / Конфигурация поле _mes_ ');
					if (   site_url=='http://insite.dev'  			or  site_url=='http://demo.krasinsite.ru'  )	$install_check->print_error('&mdash; Не задан адрес сайта // ФАЙЛ КОНФИГУРАЦИИ ');
					if (   site_path=='D:/30/40 WWW/insite.dev/www'	or  site_path=='/var/www/demo.dev/www'   )		$install_check->print_error('&mdash; Не задан путь до корневой директории сайта // ФАЙЛ КОНФИГУРАЦИИ ');
					$back_link = ggo(1, "#__backlinkcfg"); // ggd($back_link);
					if (   $back_link->order_mail_to=='insite@j-as.ru'  ||  $back_link->order_mail_from=='insite@j-as.ru'  )	$install_check->print_error('&mdash; Не указан e-mail Клиента // МЕНЮ: Компоненты / Обратная связь ');
					if (   $back_link->order_mail_from_name=='Имя клиента'  )	$install_check->print_error('&mdash; Не указано "Имя Клиента" (используется при отправке email как ОТПРАВИТЕЛЬ) // МЕНЮ: Компоненты / Обратная связь ');
					$install_check->print_error_hidden('&mdash; Не правильно задана ВРЕМЕННАЯ РАЗНИЦА (время на сервере: '.strftime( '%H:%M',time()+$reg['iServerTimeOffset'] ).') // МЕНЮ: Конфигурация поле «iServerTimeOffset»  ', "server_time_error");
					if (  $DBPrefix=="ins_"  )	$install_check->print_error('&mdash; Не используйте стандартный префикс таблицы // ИЗМЕНИТЕ ФАЙЛ КОНФИГУРАЦИИ И ПЕРЕЗАЛЕЙТЕ ВСЮ БАЗУ ДАННЫХ С НОВЫМ ПРЕФИКСОМ');
					if (   $reg['mail_username']=='2955591@gmail.com'  or  $reg['mail_username']=='krasinsite_mail@mail.ru'  )	$install_check->print_error('&mdash; Не заданы настройки smtp-сервера // Создать новый почтовый ящик и прописать в МЕНЮ: Сайт / Конфигурация поля mail_host, mail_port, mail_username и mail_password ');
					if (   $reg['mail_from_name']=='OOO Василек'  )	$install_check->print_error('&mdash; Не задано имя отправителя, настройки почты // МЕНЮ: Сайт / Конфигурация поле mail_from_name');
                                        if (   $reg['send_from_debug_smtp']==1  )	$install_check->print_error('&mdash; почта отправляется через ТЕСТОВЫЙ ЯЩИК GMAIL, нужно через smtp-сервер клиента // МЕНЮ: Сайт / Конфигурация поле send_from_debug_smtp');
					if (  preg_match("/^http:\/\/www./", site_url)  )	$install_check->print_error('&mdash; Указан адрес сайта с www // ФАЙЛ КОНФИГУРАЦИИ: укажите адрес сайта (site_url) без www');
					if (  !preg_match("/insite.dev$/", site_url)  and  !preg_match("/krasinsite.ru$/", site_url)  ){
						$install_check->writeable_array = array(
																site_path.'/iadmin/images/adminlog.xml',
																site_path.'/ibots/editors/tinymce/e24code/phpimageeditor/editimagesoriginal',
																site_path.'/ibots/editors/tinymce/e24code/phpimageeditor/editimagespng',
																site_path.'/ibots/editors/tinymce/e24code/phpimageeditor/editimagesworkwith',
																site_path.'/images'
																);
						$install_check->skip_array = array (
															site_path.'/cgi-bin',
															);
						$install_check->check_readonly ( site_path );
						foreach ($install_check->writeable_array as $path_writeble)	
							$install_check->check_writeable($path_writeble);
					} 
					
					/**/
					?>
			</table>
			<div id="server_time" style="display:none"><?=date("h", time()+$reg['iServerTimeOffset']) ?></div>
			<script language="javascript" type="text/javascript">
				$(document).ready(function(){
					current_date =  new  Date();
					if (  current_date.getHours()!=$('#server_time').html()  ) $('#server_time_error').show();
				  });
			</script><?
		$tabs->endTab();
		$tabs->startTab('О системе','perms');
		?>
<table class="adminform">
			<tr><th colspan="2">Системная информация</th></tr>
			<tr>
				<td colspan="2">
					<?php
					// show security setting check
					iSecurityCheck();
					?>
				</td>
			</tr>
			<tr>
				<td valign="top" width="250"><strong>Система:</strong></td>
				<td><?php echo php_uname(); ?></td>
			</tr>
			<tr>
				<td><strong>Версия базы данных:</strong></td>
				<td><?php echo $database->getVersion(); ?></td>
			</tr>
			<tr>
				<td><strong>Версия PHP:</strong></td>
				<td><?php echo phpversion(); ?></td>
			</tr>
			<tr>
				<td><strong>Веб-сервер:</strong></td>
				<td><?php echo get_server_software(); ?></td>
			</tr>
			<tr>
				<td><strong>Интерфейс между веб-сервером и PHP:</strong></td>
				<td><?php echo php_sapi_name(); ?></td>
			</tr>
			<tr>
				<td><strong>Версия CMS Insite:</strong></td>
				<td><?php global $iversion; echo $iversion; ?></td>
			</tr>
			<tr>
				<td><strong>Браузер (User Agent):</strong></td>
				<td><?php echo phpversion() <= '4.2.1' ? getenv( 'HTTP_USER_AGENT' ) : $_SERVER['HTTP_USER_AGENT'];?></td>
			</tr>
			<tr>
				<td colspan="2" style="height: 10px;">&nbsp;</td>
			</tr>			
			<tr>
				<td colspan="2" style="height: 10px;">
				</td>
			</tr>			
			
			</table>
		<?php
		$tabs->endTab();
		$tabs->startTab("PHP Info","php-page");
		?>
			<table class="adminform">
			<tr>
				<th colspan="2">Информация о PHP</th>
			</tr>
			<tr>
				<td><?php
				ob_start();
				phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
				$phpinfo = ob_get_contents();
				ob_end_clean();
				preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
				$output = preg_replace('#<table#', '<table class="adminlist" align="center"', $output[1][0]);
				$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
				$output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
				$output = preg_replace('#<hr />#', '', $output);
				echo $output;
				?></td>
			</tr>
			</table>
		<?php
		$tabs->endTab();
		
		$tabs->endPane();
		
		
		
function get_server_software() {
	if (isset($_SERVER['SERVER_SOFTWARE'])) {
		return $_SERVER['SERVER_SOFTWARE'];
	} else if (($sf = phpversion() <= '4.2.1' ? getenv('SERVER_SOFTWARE') : $_SERVER['SERVER_SOFTWARE'])) {
		return $sf;
	} else {
		return 'n/a';
	}
}

function get_php_setting($val, $colour=0, $yn=1) {
	$r =  (ini_get($val) == '1' ? 1 : 0);
	
	if ($colour) {
		if ($yn) {
			$r = $r ? '<span style="color: green;">ON</span>' : '<span style="color: red;">OFF</span>';
		} else {
			$r = $r ? '<span style="color: red;">ON</span>' : '<span style="color: green;">OFF</span>';			
		}
		
		return $r; 
	} else {
		return $r ? 'ON' : 'OFF';
	}
}




function iSecurityCheck($width='95%') {		
	$wrongSettingsTexts = array();
	
	if ( ini_get('magic_quotes_gpc') != '1' ) {
		$wrongSettingsTexts[] = 'PHP magic_quotes_gpc установлено в `OFF` вместо `ON`';
	}
	if ( ini_get('register_globals') == '1' ) {
		$wrongSettingsTexts[] = 'PHP register_globals установлено в `ON` вместо `OFF`';
	}
	if ( RG_EMULATION != 0 ) {
		$wrongSettingsTexts[] = 'Параметр Joomla! RG_EMULATION в файле globals.php установлен в `ON` вместо `OFF`<br /><span style="font-weight: normal; font-style: italic; color: #666;">`ON` - параметр по умолчанию - для совместимости</span>';
	}	
	
	if ( count($wrongSettingsTexts) ) {
		?>
		<div style="clear: both; margin: 3px; margin-top: 10px; padding: 5px 15px; display: block; float: left; border: 1px solid #cc0000; background: #ffffcc; text-align: left; width: <?php echo $width;?>;">
			<p style="color: #CC0000;">
				Следующие настройки PHP являются неоптимальными для <strong>БЕЗОПАСНОСТИ</strong> и их рекомендуется изменить:
			</p>			
			<ul style="margin: 0px; padding: 0px; padding-left: 15px; list-style: none;" >
				<?php
				foreach ($wrongSettingsTexts as $txt) {
					?>	
					<li style="min-height: 25px; padding-bottom: 5px; padding-left: 25px; color: red; font-weight: bold; background-image: url(../includes/js/ThemeOffice/warning.png); background-repeat: no-repeat; background-position: 0px 2px;" >
						<?php
						echo $txt;
						?>
					</li>
					<?php
				}
				?>
			</ul>
			<p style="color: #666;">
				Пожалуйста, проверьте <a href="http://www.joomla.org/security10" target="_blank" style="color: blue; text-decoration: underline">сообщения о Безопасности на официальном сервере Joomla!</a> на наличие дополнительной информации.
			</p>
		</div>
		<?php
	}
}


?>

