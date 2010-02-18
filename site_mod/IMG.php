<?php // Вывод фотки в линке

function IMG($e) { list($e,$o)=explode(',',$e,2);

if($o=='') return "<img src='".$GLOBALS['foto_www_small']."$e' hspace=5 vspace=5 border=0>";
$e=c($e); $o=c($o);
if($o=='center') return "<center><img src='".$GLOBALS['foto_www_small']."$e' hspace=5 vspace=5 border=0></center>";
return "<img src='".$GLOBALS['foto_www_small']."$e' hspace=5 vspace=5 border=0 align=$o>";

}


?>
