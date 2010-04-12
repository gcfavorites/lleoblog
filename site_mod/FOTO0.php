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

var bigfoto_onload=1;

function bigfoto_pos(){
	ajaxoff();
	e=idd('bigfotoimg');
	posdiv('bigfoto',-1,-1);
	var H=(getWinH()-20); if(e.height>H) { e.height=H; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
	var W=(getWinW()-50); if(e.width>W) { e.width=W; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
}

function bigfoto(e) {
	ajaxon();
	bigfoto_onload=1;
	setTimeout(\"if(bigfoto_onload) bigfoto_pos();\", 2000);
	helps('bigfoto',\"<img id='bigfotoimg' onclick=\\\"clean('bigfoto')\\\" onload=\\\"bigfoto_onload=0;bigfoto_pos()\\\" src='\"+e.href+\"'>\",1);
	return false;
}
");

// posdiv('bigfoto',-1,-1);

function FOTO0($e) { // list($e,$s)=explode(':',$e,2); $e=c($e);
	$epre=preg_replace("/^(.*?)\/([^\/]+)$/si","$1/pre/$2",$e);
	return "<a href='".$e."' onclick='return bigfoto(this)'><img src='".h($epre)."' border=0></a>";
}


?>
