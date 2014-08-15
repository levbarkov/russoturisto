<?php
// запрет прямого доступа
$item = ggo(ggri('id'), '#__icat');
print ($item->sefnamefull.'/'.$item->sefname);
?>