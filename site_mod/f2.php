<?php // Отображение всего фотоальбома или избранных

include_once $GLOBALS['include_sys']."_foto.php"; // фотовывод

function f2($e) { 
$f=ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($e)."' AND `type`='photo'","_l",$ttl);
return "<img class=fotoa onclick='return foto(\"".$GLOBALS["foto_www_small"].$f."\")' src='{foto_www_preview}".$f."' hspace=5 vspace=5>";
}

?>
