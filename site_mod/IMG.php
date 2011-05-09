<?php /* Вставка фоток из фотоальбома

Указываем имя фотки в альбоме или url (автоопределение). Также можно поставить запятую и указать дополнительный аргумент выравнивание:
center - по центру экрана
left - дальнейший текст будет обтекать фотку слева
right -  дальнейший текст будет обтекать фотку справа

<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/1.jpg _}</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/2.jpg, center _}</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/3.jpg, left _} Все остальное будет огибать эту фотку слева.  Все остальное будет огибать эту фотку слева. Все остальное будет огибать эту фотку слева. Все остальное будет огибать эту фотку слева.  Все остальное будет огибать эту фотку слева. Все остальное будет огибать эту фотку слева.</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/4.jpg, right _} Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа. Все остальное будет огибать эту фотку справа.</div>

*/


function IMG($e) { list($e,$o)=explode(',',$e,2);
	$e=(strstr($e,'/')?$e:$GLOBALS['foto_www_small'].$e);

	if($o=='') return "<img src='$e' hspace=5 vspace=5 border=0>";
	$e=c($e); $o=c($o);
	if($o=='center') return "<center><img src='$e' hspace=5 vspace=5 border=1></center>";
	return "<img src='$e' hspace=5 vspace=5 border=1 align=$o>";
}


?>
