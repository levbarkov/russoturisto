<?php
// запрет прямого доступа
$item = ggo(ggri('id'), '#__adgood');
print ($item->sefnamefullcat.'/'.$item->sefname.".html");
?>