<?php // Рекомендованные сайты

include "../config.php"; // конфиг движка
include $include_sys."_autorize.php"; // авторизация посетителя, ну и библиотека аякс:
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

otprav("helps('random','Случайное число <b>".rand($_REQUEST["min"],$_REQUEST["max"])."</b>, вот!');");

// javascript:var%20o=(document.selection)?document.selection.createRange().text:window.getSelection();q=document.body;q.innerHTML='<center><a%20href=http://lleo.aha.ru/re.htm><img%20src=http://lleo.aha.ru/blog/re.php?l='+encodeURIComponent(location)+'&t='+encodeURIComponent(''+o)+'></a></center>'+q.innerHTML;void(0);

?>