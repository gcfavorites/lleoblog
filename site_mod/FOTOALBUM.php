<?php // Отображение всего фотоальбома или избранных
if(!isset($GLOBALS['admin_name'])) die("Error 404"); // неправильно запрошенный скрипт - нахуй

include_once $GLOBALS['include_sys']."_foto.php"; // фотовывод

function FOTOALBUM($e) { list($e,$s)=explode(':',$e,2); $e=c($e);

	if($e=="ALL") $in="`type`='photo'";

	elseif($e=="IN") {
		$in='';
		$p=explode("\n",$s); foreach($p as $l) { $l=c($l); if($l!='') $in.=",'".e($l)."'"; }
		$in=trim($in,',');
		$in="`name` IN (".$in.") AND `type`='photo'";
	}

	$sql=ms("SELECT `id`,`name`,`text` FROM `".$GLOBALS['db_site']."` WHERE ".$in,"_a",$ttl);
	$o="<center><table><td>"; 

	foreach($sql as $p) {
$o.="\n<center>
<div class=fotoa>
<a href onclick='return foto(\"".$GLOBALS["foto_www_small"].$p['text']."\")'>
<img src='{foto_www_preview}".$p['text']."' hspace=5 vspace=5>
<div class=fotot>".htmlspecialchars($p['name'])."</div></a></div>
</center>
";
	}

	$o.="</td></table></center>";

	return $o;
}

?>
