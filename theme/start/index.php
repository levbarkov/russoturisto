<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- ТЕГИ TITLE META И FAVICON -->
        <? verstka::insite_header(); ?>

        <? css("/theme/start/css/carcass.css") ?>
        <? css("/theme/start/css/content.css") ?>

        <!-- ЗАГРУЖАЕМ ОБЯЗАТЕЛЬНЫЕ JAVASCRIPT БИБЛИОТЕКИ И СТИЛИ INSITE
             В САМОМ КОНЦЕ ФУНКЦИИ ЗАГРУЖАЕМ СТИЛИ, JAVASCRIPT БИБЛИОТЕКИ КОМПОНЕНТА (их описание лежит в файле: head.php в директории компонента) -->
        <? verstka::insite_main_styles_jscript(); ?>

        <?
                js("/includes/js/swfobject.js");        // для удобной загрузки flash-объектов
                js("/theme/start/home.js");             // основные функции шаблона

                // КОРЗИНА И ИЗБРАННОЕ(СПИСОК СРАВНЕНИЯ)
                $mycart->shopkeeperEnable=0;	// не грузить библиотеки SHOP KEEPER (окно с вводом количества, которое потом улетает в корзину)
                $mycart->java_init();
                $mylist->java_init();
        ?>
        <script type="text/javascript">
                $(document).ready(function() {
                        $("a.fancy").fancybox	({	'titlePosition'		: 'inside'	/* 'overlayOpacity'	: 0.87,	'overlayColor'		: '#000'  */
                                                });
                });
        </script>
    </head>
    <body>
        <div id="tpl-alloverwrap">
        <table id="tpl-carcass">
            <tr>
                    <td id="tpl-carcass-top">
                            <div class="tpl-spacer"></div>
                            <table id="tpl-carcass-inner">
                                    <tr>
                                            <td id="tpl-carcass-top-left">
                                                    <div id="tpl-header-logo" ><?
                                                        /*
                                                         * function ims($pos, &$params=NULL)
                                                         * вызов модуля по его названию или имени файла
                                                         * $pos - название модуля или имя файла ( /modules/имя_файла.php )
                                                         * $params - массив с дополнительными параметрами, таким образом мы можем передавать неограниченное количество параметров
                                                         * поскольку передаем указатель, то модуль может изменять или добавлять новые значения в массив $params
                                                         */
                                                        ims('myplace'); ?></div><? ims('menu');	if (  $reg['c']=='ex'  ) ims ('trash');	?></td>
                                            <td id="tpl-carcass-top-right">
                                                    <div id="tpl-header"><? $myform = new insiteform(); ?>
                                                            <form id="tpl-header-search-form" method="post" action="/search/"><div><input id="tpl-header-search-form-input" class="input_gray" name="isearch" <? $myform->make_java_text_effect('tpl-header-search-form-input', 'input_light'); ?> title="поиск" value="поиск" /><input type="submit" style="height:5px; width:5px; font-size:7px" /></div></form>
                                                            <div id="tpl-header-phones">
                                                                <div><span class="tpl-hang"><small>(391)</small>&nbsp;<span class="tpl-italic-clear"></span></span>214-99-72</div>
                                                            </div>
                                                            <div id="tpl-header-links">
                                                                <div><a href="javascript: ins_ajax_open('/?c=write_us_ajax&4ajax=1', 400, 509); void(0);" class="js-feedback-trigger">Написать нам</a></div>
                                                            </div>
                                                            <? ims ('mylist'); ?>
                                                            <div class="tpl-clear"></div>
                                                    </div>
                                                    <div id="content"><?
                                                            if (  $my->id  ){ ?><span class="cnt-emed"><a href="/cab">Мой кабинет</a> &nbsp;&nbsp;&nbsp; <a href="javascript: ins_ajax_logout(); void(0);">Выход</a></span><? }
                                                            else 			{ ?><span class="cnt-emed"><a href="javascript: ins_ajax_open('/?4ajax_module=login', 400, 280); void(0);">Вход</a> &nbsp;&nbsp;&nbsp; <a href="javascript: ins_ajax_open('/?4ajax_module=login&task=register', 680, 400); void(0);">Регистрация</a></span><? } ?>
                                                            <div id="cnt-top-products">
                                                                <div class="main-content-wrapper"><? ipathway(); ?><? ib(); ?></div>
                                                            </div>
                                                    </div>							</td>
                                    </tr>
                            </table>
                            <? if (  $reg['c']=='frontpage'  ) {
                                                        /*
                                                         * function ims($pos, &$params=NULL)
                                                         * вызов модуля по его названию или имени файла
                                                         * $pos - название модуля или имя файла ( /modules/имя_файла.php )
                                                         * $params - массив с дополнительными параметрами, таким образом мы можем передавать неограниченное количество параметров
                                                         * поскольку передаем указатель, то модуль может изменять или добавлять новые значения в массив $params
                                                         */
                                                        ims('catalogue_toggle');  ?>
                                                        <table id="home-additional">
                                                            <tr>
                                                                <td id="home-additional-news"><?
                                                                        /*
                                                                         * function ims($pos, &$params=NULL)
                                                                         * вызов модуля по его названию или имени файла
                                                                         * $pos - название модуля или имя файла ( /modules/имя_файла.php )
                                                                         * $params - массив с дополнительными параметрами, таким образом мы можем передавать неограниченное количество параметров
                                                                         * поскольку передаем указатель, то модуль может изменять или добавлять новые значения в массив $params
                                                                         */
                                                                        ims('news'); ?>
                                                                        <div class="home-additional-news-item">
                                                                                <h4><a href="/news/?rss=1">RSS-канал</a></h4>
                                                                        </div>
                                                                        <div class="home-additional-news-item"><br /></div>

                                                                </td>
                                                                <td id="home-additional-brands">
                                                                    <h3><a href="/brands/">Связь</a></h3>

                                                                    <!-- ФОРМА ОТПРАВКИ СООБЩЕНИЙ ICQ, ВСЯ ПРОБЛЕМА В ТОМ ЧТО ПРИ ЧАСТОЙ РАССЫЛКЕ СООБЩЕНИЙ ID БЛОКИРУЕТ САМ ICQ -->
                                                                    <!-- БЛОКИРОВКУ МОЖНО ОБОЙТИ ПРИ ИСПОЛЬЗОВАНИИ БОТА ICQ -->
                                                                    <div class="home-additional-news-item">
                                                                        <div style="background:url(/theme/start/img/licq.png); background-position:center left; background-repeat:no-repeat; width:128px; height:128px; padding-left:60px; padding-top:40px; vertical-align:top;">
                                                                            <form <? ctrlEnter (submit) ?> name="icq_sender" action="/icq">
                                                                                <table width="140" border="0" cellspacing="0" cellpadding="0" align="left" >
                                                                                    <tr><td><input <? $myform->make_java_text_effect('icq_uin', 'input_light'); ?> class="input_ajax input_width input_gray" type="text" value="ICQ ID" title="ICQ ID" name="icq_uin" id="icq_uin" style="width:140px; background:url(/theme/start/img/white1x1.png);" /></td></tr>
                                                                                    <tr><td><textarea <? $myform->make_java_text_effect('icq_text', 'input_light'); ?>  class="textarea_ajax input_width input_gray"  cols="35" rows="3" name="icq_text" id="icq_text" title="Сообщение:" style="width:140px; background:url(/theme/start/img/white1x1.png); height:50px;" >Сообщение:</textarea></td></tr>
                                                                                    <tr><td align="right" style="text-align:right"><a href="javascript: document.icq_sender.submit(); void(0);">отправить</a><br /><?=ctrlEnterHint () ?></td></tr>
                                                                                </table>
                                                                            </form>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ФОРМА ОТПРАВКИ SMS СООБЩЕНИЙ ЧЕРЕЗ ПОЧТУ -->
                                                                    <!-- БЛОКИРОВКУ МОЖНО ОБОЙТИ ПРИ ИСПОЛЬЗОВАНИИ БОТА ICQ -->
                                                                    <div class="home-additional-news-item">
                                                                            <div style="background:url(/theme/start/img/sms.png); background-position:center left; background-repeat:no-repeat; width:128px; height:128px; padding-left:60px; padding-top:40px; vertical-align:top;">
                                                                                    <form <? ctrlEnter (submit) ?> name="mail2sms_sender" action="/mail2sms">
                                                                                            <table width="140" border="0" cellspacing="0" cellpadding="0" align="left" >
                                                                                                    <tr><td><?
                                                                                                            ?><table cellpadding="0" cellspacing="0" width="140px">
                                                                                                                    <tr>
                                                                                                                            <td width="40"><input <? $myform->make_java_text_effect('sms_tel1', 'input_light'); ?> class="input_ajax input_width input_gray" type="text" maxlength="3" value="904" title="904" name="sms_tel1" id="sms_tel1" style="width:100%; background:url(/theme/start/img/white1x1.png);" /></td>
                                                                                                                            <td width="100"><input <? $myform->make_java_text_effect('sms_tel2', 'input_light'); ?> class="input_ajax input_width input_gray" type="text" maxlength="8" value="210-9659" title="210-9659" name="sms_tel2" id="sms_tel2" style="width:100%; background:url(/theme/start/img/white1x1.png);" /></td>
                                                                                                                    </tr>
                                                                                                            </table><?
                                                                                                      ?></td></tr>
                                                                                                    <tr><td align="right" style="text-align:right"><select class="input_ajax input_width input_gray" style="width:84px; padding:0; margin:0; width:100%;  float:right;  background:url(/theme/start/img/white1x1.png);" name="operator">
                                                                                                                    <option value="etk">ЕТК</option>
                                                                                                                    <option value="megafon_sibir">Megafon-Сибирь</option>
                                                                                                                    <option value="beeline">Beeline</option>
                                                                                                                    <option value="mts">MTC</option>
                                                                                                            </select></td></tr>
                                                                                                    <tr><td><textarea <? $myform->make_java_text_effect('sms_text', 'input_light'); ?>  class="textarea_ajax input_width input_gray"  cols="35" rows="3" name="sms_text" id="sms_text" title="Сообщение:" style="width:140px; background:url(/theme/start/img/white1x1.png); height:40px;" >Сообщение:</textarea></td></tr>
                                                                                                    <tr><td  align="right" style="text-align:right"><a href="javascript: document.mail2sms_sender.submit(); void(0);">отправить</a><br /><?=ctrlEnterHint () ?></td></tr>
                                                                                            </table>
                                                                                    </form>
                                                                            </div>
                                                                    </div>


                                                                </td>
                                                                <td id="home-additional-logos"><?
                                                                    ?><table cellpadding="0" cellspacing="0" width="80%" align="center">
                                                                        <tr><td><h3><a href="/brands/">Брэнды</a></h3></td></tr><?
                                                                                                                                /*
                                                                                                                                 * function ims($pos, &$params=NULL)
                                                                                                                                 * вызов модуля по его названию или имени файла
                                                                                                                                 * $pos - название модуля или имя файла ( /modules/имя_файла.php )
                                                                                                                                 * $params - массив с дополнительными параметрами, таким образом мы можем передавать неограниченное количество параметров
                                                                                                                                 * поскольку передаем указатель, то модуль может изменять или добавлять новые значения в массив $params
                                                                                                                                 */ ?>
                                                                        <tr><td><? $params->propid=1; ims('names', $params); ?></td></tr>
                                                                        <tr><td><h3><a href="/brands/">Технологии</a></h3></td></tr>
                                                                        <tr><td><? $params->propid=2; ims('names', $params); ?></td></tr>
                                                                        <tr><td><h3><a href="/brands/">Сервисы</a></h3></td></tr>
                                                                        <tr><td><? $params->propid=3; ims('names', $params); ?></td></tr>

                                                                    </table><?
                                                                ?></td>
                                                            </tr>
                                                        </table>
                            <? } else { ?><div style="display:block; height:14px;">&nbsp;</div><? } ?>				</td>
            </tr>
            <tr>
                <td id="tpl-carcass-bottom" colspan="2">
                    <div id="tpl-footer-stroke"></div>
                    <div id="tpl-footer">
                        <div id="tpl-footer-right"><a id="cnt-imm" href="http://krasinsite.ru/?c=insite"><span id="cnt-imm-logo"></span>Среда разработки&hellip;<br /><span id="cnt-imm-href">CMS Insite</span></a></div>
                        <div class="tpl-footer-left">
                                <p><a href="/copyright/">&copy;</a> 2010 <? verstka::secret_login('ООО'); ?> </span> &laquo;КрасИнсайт&raquo;. Система управления &mdash; Insite</p>
                        </div>
                        <div class="tpl-footer-left"><div class="tpl-footer-address-wrapper"><address><small>(391)</small> 295-55-91</address></div>
                                <p>Примеры попапов: <a href="javascript: ins_ajax_open('/popups/tema1.html&4ajax=1&clean', 0, 0); void(0);">Тема 1</a></p>
                        </div>
                        <div class="tpl-footer-left"><div class="tpl-footer-address-wrapper"><address><small><? printf ("Время создания страницы %f секунд", get_page_time()); ?></small> </div>
                                </p>
                        </div>
                        <div class="tpl-clear"></div>
                    </div>
                    <?
                    /*
                     * function ims($pos, &$params=NULL)
                     * вызов модуля по его названию или имени файла
                     * $pos - название модуля или имя файла ( /modules/имя_файла.php )
                     * $params - массив с дополнительными параметрами, таким образом мы можем передавать неограниченное количество параметров
                     * поскольку передаем указатель, то модуль может изменять или добавлять новые значения в массив $params
                     */
                    ims('library'); ?></td>
            </tr>
        </table>
        </div>
        <!-- ЗАГРУЖАЕМ КОД СТАТИСТИКИ САЙТА -->
        <? verstka::insite_footer(); ?>
    </body>
</html>