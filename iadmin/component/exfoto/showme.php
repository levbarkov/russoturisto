<?php
// запрет прямого доступа
$item = ggo(ggri('id'), '#__exfoto');
print ($item->sefnamefull.'/'.$item->sefname);
?>