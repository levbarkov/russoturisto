<?php


// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

if (!defined( '_JOS_FULLMENU_MODULE' )) {
	/** ensure that functions are declared only once */
	define( '_JOS_FULLMENU_MODULE', 1 );

global $reg;
class mosFullAdminMenu {
        /**
        * Show the menu
        * @param string The current user type
        */
        public static function show( $usertype='' ) {
                global $database;
                global $my;


/*		$query = "SELECT a.id, a.title, a.name"
                . "\n FROM #__sections AS a"
                . "\n WHERE a.scope = 'content'"
                . "\n GROUP BY a.id"
                . "\n ORDER BY a.ordering"
                ;
                $database->setQuery( $query );
                $sections = $database->loadObjectList(); */

                $menuTypes = menutypes();
                ?>
<div id="myMenuID"></div><?
global $reg;
/*
 * ДЛЯ МЕНЕДЖЕРОВ - СВОЕ МЕНЮ
 */
if (  $my->gid==23){
?><script language="JavaScript" type="text/javascript"><?
?>var myMenu=[<?php
?>['','Мои заказы','index2.php?ca=shopmanager',null,''],<?
?>_cmSplit,<?
?>[null,'Помощь','index2.php?ca=help',null,null],<?
?>_cmSplit,<?php
?>[null,'Выход','index2.php?ca=logout',null,null]<?
 ?>];<?
				
?>cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
</script><?
return;
} // меню менеджеров
?><script language="JavaScript" type="text/javascript"><?
?>var myMenu=[<?php
// Home Sub-Menu
?>[null,'Главная','index2.php',null,'Перейти на Главную страницу панели управления'],_cmSplit,<?php
// Site Sub-Menu
?>[null,'Сайт',null,null,'Управление основными возможностями системы',<?php
	?>['', 'Предпросмотр сайта', '<?php echo site_url; ?>/index.php', '_blank', 'Предпросмотр сайта'],<?php
	?>['','Шаблоны','index2.php?ca=themes',null,'Управление шаблонами'],<?php	
	?>['','Пользователи','index2.php?ca=users&task=view',null,'Управление пользователями'],<?php
	?>['','Конфигурация','index2.php?ca=config',null,'Управление конфигурацией'],<?php
	?>['','Тэги','index2.php?ca=tags&task=cfg',null,'Управление тэгами'],<?php
	?>['','Лог операций','index2.php?ca=adminlog',null,''],<?php
	?>['','<?=$reg['nopage_name'] ?>','index2.php?ca=nopage&task=cfg',null,''],<?php
	?>['','Модули',"index2.php?ca=modules",null,'Управление модулями'],<?php
	?>['', 'Информация о системе', 'index2.php?ca=sysinfo', null,'Системная информация'],<?
	?>['', 'Разблокировать все объекты', 'index2.php?ca=free', null,'Разблокировать все заблокированные объекты'],<?
?>],<?php
// Menu Sub-Menu
?>_cmSplit,<?
?>[null,'Меню',null,null,'Управление меню',<?php
if (1) { ?>['','Управление меню','index2.php?ca=menumanager',null,'Управление меню сайта'],_cmSplit,<?php 
}
foreach ( $menuTypes as $menuType ) {
?>['','<?php echo $menuType;?>','index2.php?ca=menus&menutype=<?php echo $menuType;?>',null,''],<?php
} ?>],_cmSplit,<?php
// Content Sub-Menu
?>[null,'Содержимое',null,null,'Управление структурой содержимого',<?php 
if (0) if (count($sections) > 0) { 
?>['','Содержимое по разделам',null,null,'Содержимое по разделам',<?php
foreach ($sections as $section) {
                                        $txt = addslashes( $section->title ? $section->title : $section->name );
?>['','<?php echo $txt;?>', null, null,'Раздел: <?php echo $txt;?>',<?
?>['', 'Содержимое в разделе: <?php echo $txt;?>', 'index2.php?ca=content&sectionid=<?php echo $section->id;?>',null,null],<?
?>['', 'Архив раздела: <?php echo $txt;?>', 'index2.php?ca=content&task=showarchive&sectionid=<?php echo $section->id;?>',null,null],<?
?>['', 'Добавить/изменить категории в разделе: <?php echo $txt;?>', 'index2.php?ca=categories&section=<?php echo $section->id;?>',null,'Добавление или изменение категории в разделе: <?php echo $txt;?>'],<?
?>['', 'Категории раздела: <?php echo $txt;?>', 'index2.php?ca=categories&section=<?php echo $section->id;?>',null, null],<?
?>],<?php } // foreach
?>],_cmSplit,<?php 
} 
?>['','Главная страница','index2.php?ca=frontpage',null,'Управление объектами содержимого, опубликоваными на главной странице сайта'],<?
?>['','Статичное содержимое','index2.php?ca=typedcontent',null,'Управление всеми статичными объектами содержимого сайта',<?
?>					['','Настройка','index2.php?ca=typedcontent&task=cfg',null,''],<?
?>],<?

?>['','<?=$reg['content_name'] ?>','index2.php?ca=content',null,'',<?
?>					['','<?=$reg['content_name'] ?>','index2.php?ca=content',null,''],<?
?>					['','Рубрики','index2.php?ca=icat&task=view',null,''],<?
?>					['','Архив','index2.php?ca=content&task=showarchive&sectionid=0',null,''],<?
?>					['','Настройка','index2.php?ca=contentcfg&task=cfg',null,''],<?
?>				],<?
?>],<?php
// Components Sub-Menu
if (1) {
?>_cmSplit,<?
?>[null,'Компоненты',null,null,'Управление компонентами',<?
?>['','<?=$reg['ex_name'] ?>','index2.php?ca=excat&task=view',null,'',<?
?>					['','Категории','index2.php?ca=excat&task=view',null,''],<?
?>					['','Товары','index2.php?ca=exgood&task=view',null,''],<?
?>					['','Настройка','index2.php?ca=excfg&task=cfg',null,''],<?
?>				],<?
?>['','<?=$reg['shop_name'] ?>','index2.php?ca=shop',null,'',<?
?>					['','Распределение заказов','index2.php?ca=shop',null,''],<?
?>					['','Мои заказы','index2.php?ca=shopmanager',null,''],<?
?>					['','Настройка','index2.php?ca=shopcfg&task=cfg',null,''],<?
?>				],<?
?>['','<?=$reg['ad_name'] ?>','index2.php?ca=adcat&task=view',null,'',<?
?>					['','Категории','index2.php?ca=adcat&task=view',null,''],<?
?>					['','Объявления','index2.php?ca=adgood&task=view',null,''],<?
?>					['','Настройка','index2.php?ca=adcfg&task=cfg',null,''],<?
?>				],<?
?>['','<? print $reg['file_name'] ?>','',null,'',<?
?>					['','Настройки','index2.php?ca=file&task=cfg',null,''],<?
?>                              ],<?
?>['','<? print $reg['exfoto_name'] ?>','index2.php?ca=exfoto&type=view',null,'',<?
?>					['','Настройки','index2.php?ca=exfoto&task=cfg',null,''],<?
?>],<?
?>['','<? print $reg['easybook_name'] ?>','index2.php?ca=easybook&task=view',null,'',<?
?>					['','Настройки','index2.php?ca=easybook&task=cfg',null,''],<?
?>],<?
?>['','<?=$reg['feedback_name'] ?>','index2.php?ca=feedback&task=view',null,'',<?
?>					['','Настройки','index2.php?ca=feedback&task=cfg',null,''],<?
?>				],<?
?>['','Banner','index2.php?ca=banners',null,''],<?
?>['','Голосование','index2.php?ca=poll&task=view',null,''],<?
?>['','<? print $reg['backlink_name'] ?>','index2.php?ca=backlinkcfg&task=cfg',null,''],<?
?>['','Календарь событий','index2.php?ca=eventcal',null,'',<?
	?>['','Управления событиями','index2.php?ca=eventcal',null,''],<?
	?>['','Управление категориями','index2.php?ca=eventcal&task=categories',null,''],<?
	?>['','Настройка','index2.php?ca=eventcal&task=config',null,''],<?
?>	],<?
?>['','<?=$reg['names_name'] ?>','index2.php?ca=names',null,'',<?
	?>['','Категории','index2.php?ca=names_prop',null,''],<?
	?>['','Список','index2.php?ca=names',null,''],<?
	?>['','Настройка','index2.php?ca=names&task=cfg',null,''],<?
?>	],<?

?>['','<?=$reg['easylist_name'] ?>','index2.php?ca=easylist&task=view',null,'',<?
?>					['','Настройки','index2.php?ca=easylist&task=cfg',null,''],<?
?>],<?
?>['','Проверка SMS-сервиса','index2.php?ca=mail2sms',null,''],<?
?>['','Сбор данных','index2.php?ca=cron&task=getdata',null,''],<?
?>['','Кабинет пользователя','index2.php?ca=cab&task=cfg',null,''],<?
?>['','<?=$reg['sitemap_name'] ?>','index2.php?ca=sitemap&task=viewmap',null,''],<?
?>],<?php
// Modules Sub-Menu
        } // if $installComponents
        // Messages Sub-Menu
?>_cmSplit,<?php
// Help Sub-Menu
?>[null,'<?=$reg['promo_name'] ?>','index2.php?ca=promo',null,null],<?
?>_cmSplit,<?php
?>[null,'Помощь','index2.php?ca=help',null,null],<?
?>_cmSplit,<?php
?>[null,'Выход','index2.php?ca=logout',null,null]<?
 ?>];<?
				
?>cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
</script>
<?php
        }

        /**
        * Show an disbaled version of the menu, used in edit pages
        * @param string The current user type
        */
        function showDisabled( $usertype='' ) {
                global $acl, $reg;

                $text = 'На этой странице меню не активно';
                ?>
                <div id="myMenuID" class="inactive"></div>
                <script language="JavaScript" type="text/javascript">
                var myMenu =
                [
                        [null,'<?php echo 'Меню отключенно в режиме редактирования. Нажмите: Сохранить, Применить Или Отмена '; ?>',null,null,'<?php echo $text; ?>']
                ];
                cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
                </script>
                <?php
		}
        }
}

$hide = intval( mosGetParam( $_REQUEST, 'hidemainmenu', 0 ) );

if ( $hide ) {
        mosFullAdminMenu::showDisabled( $my->usertype );
} else {
        mosFullAdminMenu::show( $my->usertype );
}
?>