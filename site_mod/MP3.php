<?php /* ¬ставить проигрыватель с mp3

”казываетс€ линк на файл (абсолютна€ ссылка или относительна€). ≈сли затем после разделител€ | указано mp3, то р€дом рисуетс€ ссылка на файл дл€ скачивани€.

{_MP3: http://lleo.aha.ru/dnevnik/img/2008/05/Nikolo_Digalo-The_Little_Man.mp3 | mp3  _}

*/

function MP3($e) { global $www_design;

	list($e,$f)=explode('|',$e,2);
	$f=c($f);
	$e=c($e);
	$eu=urlencode($e);

$pl=$www_design."mp3playerns.swf?file=".$eu; // autoplay=yes& бл€ть не работает

return "<center>"
.($f=='mp3'?"<table><tr valign=center><td>":'')
."<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' type='application/x-shockwave-flash' data='$pl' wmode='transparent' align='middle' height='20' width='300'>"
."<param name='movie' value='$pl' />"
."<param name='wmode' value='transparent' />"
."<param name='flashvars' value='file=".$eu."' />"
."<embed type='application/x-shockwave-flash' src='$pl' wmode='transparent' height='20' width='300' loop='false'></embed>"
."</object>"
.($f=='mp3'?"</td><td><a class=br href='".$e."'>mp3</a></td></tr></table>":'')
."</center>";

}

?>