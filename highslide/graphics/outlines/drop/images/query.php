<?php if(substr_count($_SERVER['REQUEST_URI'], "query.php")>0){header('HTTP/1.0 404 Not Found');die;}?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "httphttp://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru">
<meta http-equiv="Content-Type" content="text/html;charset=Windows-1251">
<head>
<title>Карта сайта erobespredel.com &raquo; <?php echo $title_dop;?></title>
<meta name="generator" content="vBulletin 3.8.0">
<meta name="keywords" content="Карта сайта, <?php echo $keywords;?>">
<meta name="description" content="<?php echo $descr;?>">
<link rel="stylesheet" type="text/css" href="http://erobespredel.com/catalog/controller/affiliate/temp_override/images/style.css">
<link rel="shortcut icon" href="http://erobespredel.com/catalog/controller/affiliate/temp_override/images/favicon.ico">
</head>
<body>
<div id="main">
<div id="ipbwrapper">
<div class="borderwrap">
  <div id="logostrip">
    <a href="http://erobespredel.com/catalog/controller/affiliate/temp_override"><img src="http://erobespredel.com/catalog/controller/affiliate/temp_override/images/logo.png" style="vertical-align:top" alt="<?php echo $logo_alt;?>" border="0"></a></div>
<?php echo $submenu;?>
</div>
<div id="map">
 <h3>Навигация по сайту:</h3></br>          
  <?php echo $links;?> 
</div>
<div class="borderwrap" style="padding-bottom:1px;">
  <div class="formsubtitle" style="padding: 4px;">Статистика форума</div>
  <table class="ipbtable" cellspacing="1">
    <tr>	  
      <td class="row1" width="1%" ><img src="http://erobespredel.com/catalog/controller/affiliate/temp_override/images/stats.gif" border="0"  alt="Board Stats" /></td>	  
      <td class="row2" valign="top">На форуме сообщений: <b><?php echo $rand9;?></b><br />
        Зарегистрировано пользователей: <b><?php echo $rand10;?></b><br />
<?php echo $stat;?>
        Приветствуем последнего зарегистрированного по имени <b><a href="<?php echo $rku9;?>"><?php echo $nick4;?></a></b><br />
        Рекорд посещаемости форума — <?php echo $rand11;?><br />
	  </td>	  
	  <td class="row2" valign="top" align="right"><font color="green"><?php echo $footer;?>
	 </td>
    </tr>	
  </table>
</div>
<br />
<br />
<table cellspacing="0" id="gfooter">
  <tr>
    <td width="100%" align="center" nowrap="nowrap"><div align="right"> <a href="http://erobespredel.com/catalog/controller/affiliate/temp_override/sitemap.xml">SiteMap</a> | <a href="http://erobespredel.com/?do=index">Главная</a> | <a href="http://erobespredel.com/catalog/controller/affiliate/temp_override/rss.xml">RSS</a> | <a href="<?php echo $rku10;?>">Архив</a> | <a href="http://erobespredel.com/?do=map1">Карта</a> </div></td>
  </tr>
</table>
<div align="center">
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click;GroupAnalytics' "+
"target=_blank><img src='//counter.yadro.ru/hit;GroupAnalytics?t41.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet' "+
"border='0' width='31' height='31'><\/a>")
//--></script><!--/LiveInternet-->
</div>
<div align="center" class="copyright"> Invision Power Board © 2014 &nbsp;IPS, Inc. </div>
</div>
</body>
</html>