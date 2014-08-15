<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg, $iseoname;
if (  $sefname1!=$reg['ex_seoname']  )	return;

$tsefname = $sefname;
$icontent = array();
preg_match('#^[^/]+(.*\.html)$#', $tsefname, $match);
if ($match[1] != ''){	// необходимо вывести содержимое статьи
    $uri = $match[1];
    $parent = 0;
    for($i=0; $i<10; $i++) {
        preg_match('#^/([^/]+)(.*)$#', $uri, $match);
        $uri_part = $match[1];
        $uri = $match[2];
        if (strpos($uri_part, '.html') !== false) {
            $uri = $uri_part;
            break;
        }
        if (strlen($uri_part) == 0)
            break;
        $category = ggsql ("select id from #__excat where `sefname` = '{$uri_part}' and `parent` = {$parent} and `publish` = 1");
        if ($category === null || $category === false)
            break;
        $parent = $category[0]->id;
    }
    if ($uri == 'mylist.html') {
        $_REQUEST['c'] = 'ex';
        $_REQUEST['task'] = 'excomp';
        $_REQUEST['id'] = $parent;
        rewrite_option();
        $seoresult = true;
    }

    if ($uri == 'shop.html') {
        $_REQUEST['c'] = 'ex';
        $_REQUEST['task'] = 'viewtrush';
        $_REQUEST['id'] = $parent;
        rewrite_option();
        $seoresult = true;
    }
    if ($uri == 'visa.html') {
        $_REQUEST['c'] = 'ex';
        $_REQUEST['task'] = 'visa';
        $_REQUEST['id'] = $parent;
        rewrite_option();
        $seoresult = true;
    }
    if ($uri == 'thank.html') {
        $_REQUEST['c'] = 'ex';
        $_REQUEST['task'] = 'thank';
        $_REQUEST['id'] = $parent;
        rewrite_option();
        $seoresult = true;
    }

    $goodsefname = substr($uri_part, 0, strlen($uri_part) - 5);
    $icontent = ggsql("select id from #__exgood where sefname='{$goodsefname}' and parent = {$parent} and `publish` = 1");
    if($icontent[0]->id === null && $seoresult==false) header('HTTP/1.0 404 Not Found');
    if (count($icontent) > 0) {
        $_REQUEST['c'] = 'ex';
        $_REQUEST['task'] = 'view';
        $_REQUEST['id'] = $icontent[0]->id;
        rewrite_option();
        $seoresult = true;
    }
    return;
}

preg_match('#^[^/]+(.*)$#', $tsefname, $match);
$uri = $match[1];
$parent = 0;
for($i=0; $i<10; $i++) {
    preg_match('#^/([^/]+)(.*)$#', $uri, $match);
    $uri_part = $match[1];
    $uri = $match[2];
    if (strlen($uri_part) == 0)
        break;
    $category = ggsql ("select id from #__excat where sefname = '{$uri_part}' and parent={$parent} and `publish` = 1");
    if ($category === null || $category === false)
        break;
    $parent = $category[0]->id;
}
if($parent===null) header('HTTP/1.0 404 Not Found');
$_REQUEST['c'] = 'ex';
$_REQUEST['task'] = 'excat';
$_REQUEST['id'] = $parent;
rewrite_option();
$seoresult = true;
