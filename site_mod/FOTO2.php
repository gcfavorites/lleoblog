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

STYLES("блоки design.ru","
.thmbns {margin: -3em 0 0 -2em; text-align:center;}
.thmbn {text-decoration:none; display: -moz-inline-box; display:inline-block; vertical-align:top; text-align:left; margin:3em 0 0 2em;}
.thmbn .rth {width:10em; float:left;}
");

function FOTO2($e) { // list($e,$s)=explode(':',$e,2); $e=c($e);
	$pp=explode("\n",$e);
	$s=''; foreach($pp as $p) { $p=c($p); if($p=='') continue;
		list($img,$txt)=explode(" ",$p,2); $img=c($img); $txt=c($txt);
		$epre=preg_replace("/^(.*?)\/([^\/]+)$/si","$1/pre/$2",$img);

		$s.="<ins class='thmbn'><div class='rth'>"
		."<a href='".h($img)."' onclick='return bigfoto(this)'><img src='".h($epre)."' border=0></a>"
		."<div class=r>".($txt!=''?$txt:'')."</div>"
		."</div></ins>";
	}
	return "<div class=thmbns>$s</div>";
}

?>
