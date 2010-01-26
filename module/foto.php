<?php // Правки

if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй
if(!$admin) redirect($wwwhost."login/"); // посторонних - нахуй

blogpage();

$_PAGE["header"]=$_PAGE["title"]="Фотоальбом";

SCRIPTS("var wwwhost='".$wwwhost."';");
SCRIPT_ADD($www_design."fotoload.js");

include_once $GLOBALS['include_sys']."_foto.php"; // фотовывод

$s = "<center><div id='foton0'></div><script>mkload(0);</script>";

$sql = ms("SELECT `name`,`id`,`type`,`text` FROM `".$db_site."` WHERE `type`='photo' ORDER BY `datetime` DESC",'_a',0);

$s.="<div id='polefoto'>";
foreach($sql as $n=>$p) $s.=print_foto($p);
$s.="</div>";
die($s);

function print_foto($p) { $name=htmlspecialchars($p['name']); $id=intval($p['id']);

return "<div class='fotoa'>
<a href='' onclick='return foto(\"".$GLOBALS['foto_www_small'].$p['text']."\")'>
<img src='".$GLOBALS['foto_www_preview'].$p['text']."' hspace=5 vspace=5>
<div class='fotot'>".(strlen($name)?$name:"&lt;...&gt;")."</div></a></div>
";
}

?>
