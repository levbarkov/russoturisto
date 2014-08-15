<?php

require_once("wrapper.php");
/*
$com = new comments("ex", $database, $reg);

$params = Array
(
"parent" => "20",
"name" => "tester",
"mail" => "mail@mail.ru", 
"text" => "<div>'DROP TABLE'</div> \"BOLT\"",
"ip" => $_SERVER['REMOTE_ADDR']
);

//print $com->set($params);
//$res = $com->getChildren(18);
$res = $com->get(16, 3, 10);

//ggr($res);
//$user = $com->user(2851);

ggr($res);
*/
?>
<table border = 1 cellpadding = 5 cellspacing = 0>
<tr><th>&nbsp;</th><th>15</th><th>16</th><th>19</th><th>28</th><th>31</th><th>47</th></tr>
<tr><th>15</th><td>0 </td><td>1 </td><td>1 </td><td>1 </td><td>1 </td><td> 1</td></tr>
<tr><th>16</th><td>0 </td><td>0 </td><td>1 </td><td>1 </td><td>1 </td><td> 1</td></tr>
<tr><th>19</th><td>0 </td><td>0 </td><td>0 </td><td>1 </td><td>1 </td><td> 1</td></tr>
<tr><th>28</th><td> 0</td><td>0 </td><td>0 </td><td>0 </td><td>1 </td><td> 1</td></tr>
<tr><th>31</th><td> 0</td><td>0 </td><td>0 </td><td>0 </td><td>0 </td><td> 1</td></tr>
<tr><th>47</th><td> 0</td><td>0 </td><td>0 </td><td>0 </td><td> 0</td><td>0 </td></tr>
</table>
<br />
<b> ( n - 1 ) * n / 2 </b>

<?

$items = Array(16,19,28,31,47,15);
//$items = Array(16, 31, 15);

$rel = new relation($reg);
$rel->set($items);

$arr = $rel->get(31);
ggr($arr);
//$rel->set($items);


?>