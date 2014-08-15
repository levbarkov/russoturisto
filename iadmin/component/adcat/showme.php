<?php
// запрет прямого доступа
$item = ggo(ggri('id'), '#__adcat');
print ($item->sefnamefull.'/'.$item->sefname);
?>