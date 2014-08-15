<?php
// запрет прямого доступа
$item = ggo(ggri('id'), '#__excat');
print ($item->sefnamefull.'/'.$item->sefname);
?>