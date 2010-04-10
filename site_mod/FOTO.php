<?php // Отображение всего фотоальбома или избранных

/*
STYLES("Всплывающее окно фотки","

.fotof{ float:left; text-align:center; border:1px solid black; }
.fotof:hover { border: 1px solid blue; }
.fotof a { color: #814c52; }
.fototf{ font-size: 10px; }

");
*/

// visibility: hidden; 

SCRIPTS("bigfoto","
function bigfoto(e) {
ajaxon();
helps('bigfoto',\"<img onload=\\\"ajaxoff();posdiv('bigfoto',-1,mouse_y-20)\\\" onclick=\\\"clean('bigfoto')\\\" src='\"+e.href+\"'>\",1);
return false;
}
");

function FOTO($e) { // list($e,$s)=explode(':',$e,2); $e=c($e);
	$epre=preg_replace("/^(.*?)\/([^\/]+)$/si","$1/pre/$2",$e);
	return "<a href='".$e."' onclick='return bigfoto(this)'><img src='".h($epre)."' border=0></a>";
}

?>
