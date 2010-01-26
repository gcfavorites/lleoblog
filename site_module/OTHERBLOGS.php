<?php

function OTHERBLOGS($e) {

if($GLOBALS['httpsite'] == "http://lleo.aha.ru") { // только для сайта блогодвижка, остальным этот модуль удалить
$l=file_get_contents($GLOBALS['host_log']."blogs.txt"); $m=explode("\n",$l); $s=$a='';
foreach($m as $l) { $l=h($l); if($l!='') { list($link,$admname)=explode(' ',$l,2);
	$a.= "<br><a href='$link'>".($admname!=''?$admname:$link)."</a>";
}} if($a!='') $s .= "<div style='text-align: left; font-size:11px;'><p>другие блоги:<br>$a</div>";
}

return $s;

}

?>