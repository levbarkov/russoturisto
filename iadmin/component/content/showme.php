<?php
// запрет прямого доступа
$item = ggo(ggri('id'), '#__content');
print ($item->sefnamefullcat.'/'.$item->sefname.".html");
?>