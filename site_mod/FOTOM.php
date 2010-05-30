<?php // Отображение фотки

/*
STYLES("Всплывающее окно фотки","

.fotof{ float:left; text-align:center; border:1px solid black; }
.fotof:hover { border: 1px solid blue; }
.fotof a { color: #814c52; }
.fototf{ font-size: 10px; }

");
*/
// visibility: hidden; 

function FOTOM($e) {

//	$r="<center><img src='".$GLOBALS['wwwhost']."photo/".h($e).".jpg' border=1></center>";
//	if(strlen($e)!=7 || substr($e,2,1)!='-') return $r;
	list($y,$m,)=explode('/',$GLOBALS['article']['Date'],3); //if(!intval($y)||!intval($m)) return $r;

	return "<center><img src='".$GLOBALS['wwwhost'].$y.'/'.$m.'/'.h($e).".jpg' border=1></center>";

}

?>
