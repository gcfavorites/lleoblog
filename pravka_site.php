<?php

include "config.php";
//include $include_sys."_autorize.php";
//mystart();



$serv=$_SERVER['DOCUMENT_ROOT'];
// $path='arhive';
$path='';
$countbasa='site_count';

include $include_sys."_msq.php";

function counter($lang) { $lang=e($lang); global $IP;
	if(!msq_exist($GLOBALS['countbasa'],"WHERE `lang`='$lang'")) msq_add($GLOBALS['countbasa'],array('lang'=>$lang,'count'=>1,'last_ip'=>$IP));
	else msq_update($GLOBALS['countbasa'],array('last_ip'=>$IP), ", `count`=`count`+1 WHERE `lang`='$lang' AND `last_ip`!='$IP'");
	return counter_get($lang);
}
function counter_get($lang) { $lang=e($lang); $p=ms("SELECT `count` FROM `".$GLOBALS['countbasa']."` WHERE `lang`='$lang'",'_1',0); return $p['count']; }
function counter_set($lang,$n) { global $IP; $lang=e($lang); msq_update($GLOBALS['countbasa'],array('last_ip'=>$IP,'count'=>intval($n) ), "WHERE `lang`='$lang'"); }

$name=$_SERVER['QUERY_STRING'];



if(!preg_match("/^([0-9a-z\._\-\/]*)\/([0-9a-z\._\-]+\.s?html?)(#?[0-9a-z\._\-]*)$/i",'/'.$name,$ur)) die("Error 404<p>File not found");
if(strstr($ur[1].$ur[2],'..')) die("<p><b>Error 404</b><p>No way for scriptkids");
//if(!isset($ur[2])) die('OMG!');

if(($s=file_get_contents($serv.$path.$ur[1].'/'.$ur[2]))===false || $s=='') die("Error 404<p>Not found: /$path".$ur[1].'/'.$ur[2]);

$count=counter($name);
/* // совсем устаревший кусок для совсем старых скриптов Зенона
	if($count==1) { $countfile=preg_replace("/(.+?)\/([^\/]+)$/i","$1/.$2.count",$serv.$path.$ur[1].'/'.$ur[2]);
		if(file_exists($countfile)) {
			$c=file($countfile); $count=rtrim($c[0]); counter_set($name,$count);
			file_put_contents($countfile.'.0',$count); chmod($countfile.'.0',0666);
		}
	}
*/

function shtml_include($p) { global $serv,$path,$ur;
if(($i=file_get_contents($serv.$path.$ur[1].'/'.$p[1]))===false || $i=='') $i="<p>Not found: /$path".$ur[1].'/'.$p[1];
return $i;
}

$s=preg_replace_callback("/<!--#include file=[\"']*([0-9a-z\.\/]+)[\"']*-->/si",'shtml_include',$s);
$s=str_replace('<!--#exec cgi="/cgi/textcount.pl"-->',$count,$s); /* Зеноновские счетчики */


// ===========
$scr="
<!-- script type=\"text/javascript\" language=\"JavaScript\" src=\"/sys/Js	HttpRequest.js\"></script -->
<script><!--

document.onkeydown = NavigateThrough;
function NavigateThrough (event) { if(!document.getElementById) return;
    if(window.event) event=window.event; if(event.ctrlKey)
        if( (event.keyCode ? event.keyCode : event.which ? event.which : null) ==  0x0D )
	{ setTimeout('pro_oshibku();',1); return false; } // через 1 милисекунду вызвать
}

function scount(str,s) { var i=0,c=0; while((i=str.indexOf(s,++i))>0) c++; return c; }

function pro_oshibku() { // код, отвечающий за обработку опечаток
  var body = document.getElementById('Body').innerHTML
  var opecha = (document.selection) ? document.selection.createRange().text : window.getSelection(); opecha += '';
  var l = opecha.length;
  if(!l) return; // пустое выделение
  if(l>256) { alert('Не многовато ли текста выбрано?\\nПопробуйте кусок поменьше:\\nслово, которое исправляете, и еще пару рядом.'); return; }
  var n = scount(body,opecha);
  if(n>1) { alert('Cтрок \"'+opecha+'\" в этом тексте '+n+'!\\nПопробуйте выделить более длинный кусок.'); return; }
  if(n<1) { alert('Система редактирования опечаток\\nработает в пределах всего текста,\\nно только если во фрагмент не попало html-тэгов:\\nэлементов верстки, абзацев, перевода строки,\\nтэгов, заменяющих кавычки и т.п.'); return; }

  var opechae=encodeURIComponent(opecha);
  var opechanew=prompt('\\nИсправьте как надо:\\n',opecha);

  if(opechanew && opechanew.length != 0 ) { var opechanewe=encodeURIComponent(opechanew);

    JsHttpRequest.query('/sys/pravka/ajax_pravka.php', { action: 'opechatka', data: '".$name."', text: opechae, textnew: opechanewe },
    function(responseJS, responseText) {
        if(responseJS.newbody) {
                var i = body.indexOf(opecha);
                var t1 = body.substring(0,i); // текст перед
                var t2 = body.substring(i+l,body.length); // текст после
                document.getElementById('Body').innerHTML = t1 + responseJS.newbody + t2;
        }
        if(responseJS.otvet) { alert(responseJS.otvet); }
    },true);
  }
}

--></script>

<a style='font-size: 10px; text-align:left; text-indent:0pt; margin-top:10pt; margin-bottom:0pt; margin-left:0pt; margin-right:0pt;'
href='/sys/pravka/pravka_about.htm'>Заметили опечатку? Выделите мышкой и нажмите Ctrl+Enter. Спасибо за помощь.</a>
<div id='Body'>
";

$s=preg_replace("/(<body[^>]*>)/si","$1\n".$scr,$s);
$s=str_ireplace('</body>',"\n</div></body>\n",$s);
$s=preg_replace_callback("/(>[^<]+<)/si","kawa",$s);
$s=preg_replace("/([\s>]+)\-([\s<]+)/si","$1".chr(151)."$2",$s); // длинное тире

die($s);

function kawa($p) { $s=$p[1];
        $s=preg_replace("/([A-Za-z\x80-\xFF.,?!])\"/s","$1\xBB",$s); // "$1&raquo;"
        $s=preg_replace("/\"([A-Za-z\x80-\xFF.])/s","\xAB$1",$s); // "&laquo;$1"
return $s; }

?>
