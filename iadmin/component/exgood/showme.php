<?php
// запрет прямого доступа
$item = ggo(ggri('id'), '#__exgood');
print ($item->sefnamefullcat.'/'.$item->sefname.".html");
?>