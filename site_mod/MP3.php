<?php /* Вставить проигрыватель с mp3

Указывается линк на файл (абсолютная ссылка или относительная). Если затем после разделителя | указано mp3, то рядом рисуется ссылка на файл для скачивания.

{_MP3: http://lleo.aha.ru/dnevnik/img/2008/05/Nikolo_Digalo-The_Little_Man.mp3 | mp3  _}

*/

function MP3($e) { global $www_design;

	list($e,$f)=explode('|',$e,2);
	$f=c($f);
	$e=c($e);
	$eu=urlencode($e);

return "<center>"
.($f=='mp3'?"<table><tr valign=center><td>":'')
."<object type='application/x-shockwave-flash' data='".$www_design."mp3player.swf?src=".$eu."' align='middle' height='18' width='125'>"
."<param name='movie' value='".$www_design."mp3player.swf?src=".$eu."'></object>"
.($f=='mp3'?"</td><td><a class=br href='".$e."'>mp3</a></td></tr></table>":'')
."</center>";

}

?>