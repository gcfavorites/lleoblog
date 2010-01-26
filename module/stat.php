<?php // Установили движок, ура!
if(!isset($admin_name)) die("Error 404");

$blogs="blogs.txt";

$l=file_get_contents($host_log.$blogs);

if(!isset($_GET['link'])) die("<pre>$l</pre>");

$link=htmlspecialchars($_GET['link']);
$a=explode("\n",$l); foreach($a as $n=>$l) list($a[$n])=explode(' ',$l,2);

if(in_array($link,$a)) redirect($httpsite.$www_design."lleo/congratulation1.png");

// сходить и проверить, правда ли
list($version,$name)=explode("\n",file_get_contents($link."admin?version"));
list($v1)=explode(" ",$version,2); if($v1!='lleoblog') die('Error');

logi($blogs,$link.($name!=''?" ".$name:'')."\n");

redirect($httpsite.$www_design."lleo/congratulation.png");

?>