<?php

include "config.php";
include $include_sys."_autorize.php";
include $include_sys."_modules.php";
include_once $include_sys."blogpage.php"; // потом удалить!!!

/*
if(0&&$admin) die(
//.ini_get('memcache.default_port').
"<pre>".(
print_r(ini_get_all(),1)
)
);

if($_SERVER['QUERY_STRING']=='aaa') {

$s='';
for($i=32;$i<128;$i++) $s.="<br>".chr($i)." &#0".$i.";";

die($s);
}

   [memcache.default_port] => Array
        (
            [global_value] => 11211
            [local_value] => 11211
            [access] => 7
*/

$_SCRIPT=array(); $_SCRIPT_ADD=array();
$_STYLE=array(); $_STYLE_ADD=array();
mystart();

SCRIPTS("jog_kuki",$jog_scripts." function setIsReady() { c_rest('".$uc."'); c_rest('lju'); } ");

SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js"); // подгрузить аякс

$hashpage=rand(0,1000000); $hashpage=substr(broident($hashpage.$hashinput),0,6).'-'.$hashpage;


function ARTICLE_Date($Date) { global $article;
	$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($Date)."'"),"_1");
        if($article!==false) ARTICLE();

	$Date2=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `DateDatetime` DESC LIMIT 1","_l");
	idie("Заметка, датированная числом ".h($Date)." не существует. Скорее всего ее никогда не было. Может, она удалена или закрыта.
Последняя заметка дневника находится <a href='".$GLOBALS['wwwhost'].$Date2.".html'>здесь</a>. Также можно посмотреть
<a href='".$GLOBALS['wwwhost']."contents/'>оглавление</a>","HTTP/1.1 404 Not Found");
}

function ARTICLE() { global $_PAGE,$article,$file_template,$wwwhost,$REF,$httpsite;

	$REF=$_SERVER["HTTP_REFERER"]; if($REF!='' && substr($REF,0,strlen($httpsite))!=$httpsite) {
	        include_once $GLOBALS['include_sys']."_refferer.php"; $GLOBALS['linksearch']=refferer($REF,$article['num']);
	}

if(empty($article['template'])) $article['template']='blog';
$tmpl_name=$file_template.$article['template'].'.html';
if(($design=file_get_contents($tmpl_name))===false) idie('Template not found: #'.$tmpl_name);

$_PAGE=array();
$_PAGE['prevlink']=$wwwhost;
$_PAGE['nextlink']=$wwwhost;
$_PAGE['uplink']=$wwwhost;
$_PAGE['downlink']=$wwwhost."contents/";
$_PAGE['www_design']=$GLOBALS['www_design'];
$_PAGE['admin_name']=$GLOBALS['admin_name'];
$_PAGE['httphost']=$GLOBALS['httphost'];
$_PAGE['wwwhost']=$wwwhost;
$_PAGE['signature']=$GLOBALS['signature'];
$_PAGE['wwwcharset']=$GLOBALS['wwwcharset'];
$_PAGE['hashpage']=$GLOBALS['hashpage'];
$_PAGE['foto_www_preview']=$GLOBALS['foto_www_preview'];
$_PAGE['foto_res_small']=$GLOBALS['foto_res_small'];
$_PAGE['design']=modules($design);
exit;
}


list($path)=explode('?',$_SERVER["REQUEST_URI"]); $path=rtrim($path,'\/');
$pwwwhost=str_replace('/','\/',$wwwhost);

// ============== начали выяснять, какой модуль подцепить ==============

// рядовая заметка
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d\/\d\d.*)\.html/si", $path, $m)) ARTICLE_Date($m[1]);

// заметка месяца
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d)$/si", $path, $m)) ARTICLE_Date($m[1]); // Заметка

// Корень => Последняя заметка ???
if($path."/" == $wwwhost) {
 	// Yandex заебал индексировать титул блога! Он же меняется все время! Блять, для кого robots.txt был написан?!
 	if( strstr($BRO,'Yandex') || $IP=='78.110.50.100') {
 	logi("yandex_nah.log","\n".date("Y/m/d H:i:s")." Yandex пошел нахуй");
 	redirect('http://lleo.aha.ru/na/?WWFuZGV4JSDy+yDt6PXz-yDt5SD36PLg5fj8IHJvYm90cy50eHQg6CDr5efl+Pwg6vPk4CDt5SDt4OTuLiDfIOTr-yDq7uPuIHJvYm90cy50eHQg7+jx4Os-JSDv8OXq8OD54Okg6O3k5erx6PDu4uDy-CDy6PLz6yDv5fDl4OTw5fHg9ujoIPLl7CDq7u3y5e3y7uwsIOru8u7w++kg7+4g7OXx8vMg7+Xw5eDk8OXx4Pbo6C4gx+Dl4eDrLCBZYW5kZXgsIPfl8fLt7uUg8evu4u4h');
 	}

	if(!empty($rootpage)) redirect($wwwhost.$rootpage); // если в конфиге установлен адрес заметки по умолчанию

	$last=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `Date` DESC LIMIT 1","_l",$ttl);
	if($last=='') {
	if(!msq_table('site') and !msq_table('dnevnik_zapisi')) redirect($wwwhost."admin"); // в админку, если по первому разу
	redirect($wwwhost."editor"); // в редактор, если записей нет
	} redirect($wwwhost.$last.".html"); // на последнюю
}

// Старый стиль именования
if(preg_match("/^".$pwwwhost."(\d\d\d\d)\-(\d\d)\-(\d\d)\.shtml/", $path, $m)) redirect($httphost.$m[1]."/".$m[2]."/".$m[3].".html");

// ===== подключение внешних модулей из директории /module/* ====
$mod_name=substr($path,strlen($wwwhost)); $mod_name=str_replace('..','.',$mod_name);
if(preg_match("/[^0-9a-z_\-\.\/]+/si",$mod_name)) idie("Error 404: wrong name \"<b>".h($mod_name)."</b>\"");

// сперва ищем в модулях
$mod=$host_module.$mod_name.".php"; if(file_exists($mod)) { include($mod); exit; }

// затем в базе site
$text=ms("SELECT `text` FROM `site` ".WHERE("`name`='".e($mod_name)."' AND `type`='page'"),"_l",$ttl);
if($text!='') { $name=$mod_name; include("site.php"); exit; }

// затем в базе дневника
$article=ms("SELECT * FROM `dnevnik_zapisi` WHERE `Date`='".e($mod_name)."'","_1",$ttl); if($article!==false && $article!='') ARTICLE();

// и если совсем ничего не нашлось
idie("Error 404: Page not found <b>".$httphost.h($mod_name)."</b>"
.($admin?"<p><a href='".$wwwhost."adminsite/?a=create&name=".urlencode($mod_name)."'>Создать эту страницу в базе `".$db_site."`?</a>":"")
);

// function urldata($d) { return $GLOBALS['wwwhost'].h($d).(substr($d,4,1).substr($d,7,1)=='//'?".html":''); }
/*
function mk_prevnest($prev,$next) { // БЛИТЬ ИДИТЕ ВСЕ НАХУЙ!!! НЕ ПОЛУЧАЕТСЯ У МЕНЯ С ВАШИМИ ЙОБАННЫМИ CSS!!! ГОРЕТЬ ИМ В АДУ!!!
$prev=($prev==''?'&nbsp;':"<font size=1>".$prev."</font>");
$next=($next==''?'&nbsp;':"<font size=1>".$next."</font>");
return "<center><table width=98% cellspacing=0 cellpadding=0><tr valign=top><td width=50%>$prev</td><td width=50% align=right>$next</td></tr></table></center>";
}
*/

//===============================================================================================================================
//===============================================================================================================================

function SCRIPTS_mine() { SCRIPTS("mine","
var hashpage='".$GLOBALS['hashpage']."';
var wwwhost='".$GLOBALS['wwwhost']."';
var admin=".($GLOBALS['admin']?1:0).";
var www_design='".$GLOBALS['www_design']."';
var mypage='".$GLOBALS['httpsite'].$GLOBALS['mypage']."';
var uc='".$GLOBALS['uc']."';
var comnum=0;
var hid=0;

var mHelps={};

function idd(id) { return document.getElementById(id); }
function zabil(id,text) { idd(id).innerHTML = text; }
function vzyal(id) { return idd(id).innerHTML; }
function zakryl(id) { idd(id).style.display='none'; }
function otkryl(id) { idd(id).style.display='block'; }


function isHelps() { for(var k in mHelps) if(mHelps[k]==1) return k; return false; }

function print_r(a) { var s=''; for(var k in a) { var v=a[k]; s='\\n'+k+'='+v+s; } return s; }
// alert(print_r(a));

function clean(id) { 
if(mHelps[id]) mHelps[id]=0;
if(idd(id)) { zakryl(id); setTimeout(\"var s=idd('\"+id+\"'); if(s) s.parentNode.removeChild(s);\", 40); } }

var JSload={};
function loadScript(src){ src='".$GLOBALS['www_js']."'+src;
	if(JSload[src]=='load') return;
        var s = document.createElement('script');
        s.setAttribute('type', 'text/javascript');
        s.setAttribute('charset', '".$GLOBALS['wwwcharset']."');
        s.setAttribute('src', src);
        // IE crashes on using appendChild before the head tag has been closed.
        var head = document.getElementsByTagName('head').item(0);
        head.insertBefore(s, head.firstChild);
	ajaxon();
}
function loadScriptBefore(src,runtext){
	if(JSload[src]=='load') return eval(runtext); if(JSload[src]) return; JSload[src]=runtext; loadScript(src);
}

function loadCSS(src){ src='".$GLOBALS['www_css']."'+src;
	if(JSload[src]=='load') return; JSload[src]='load';
	var headID = document.getElementsByTagName('head')[0];
	var s = document.createElement('link');
	s.type = 'text/css';
	s.rel = 'stylesheet';
	s.href = src;
	s.media = 'screen';
	headID.appendChild(s);
	ajaxon();
}

function mkdiv(id,cont,cls,paren){ if(idd(id)) { idd(id).innerHTML=cont; idd(id).className=cls; return; }
        var div = document.createElement('DIV'); div.className = cls; div.id = id; div.innerHTML = cont; div.style.display='none';
        if(paren==undefined) paren=document.body; paren.insertBefore(div, paren.lastChild);
}

function mkdiv2(id, cont, cls, parent, relative) { if(idd(id)) { idd(id).innerHTML=cont; idd(id).className=cls; return; }
	var div = document.createElement('DIV'); div.className=cls; div.id=id; div.innerHTML=cont; div.style.display='none';
	r=relative.nextSibling; if(r) parent.insertBefore(div,r); else parent.appendChild(div);
}

function posdiv(id,x,y) { // позиционирование (с проверкой на вылет)
	otkryl(id);
        var e=idd(id); var d=document.body;
        var W=d.clientWidth; var H=d.clientHeight;
        var w=e.clientWidth; var h=e.clientHeight;

//if(admin && id != 'ajaxgif') {
//        var w=e.clientWidth; var h=e.clientHeight;
//	e.innerHTML=e.innerHTML+'<table bgcolor=white><td><b><big>W=' + W + ' w=' + w + '<br>H=' + H + ' h=' + h +'</big></b></td></table>';
//}

        if(x+w>W) x=W-w; if(x<0) x=0; 
	if((y+h)>H) y=H-h; if(y<0) y=0;
        e.style.top=y+'px'; e.style.left=x+'px';

}

var hmov = {};

// onmousedown='hmov[this.id]=1' onmouseup='hmov[this.id]=0' onmousemove='doMove(event,this)'>



function addEvent(e,evType,fn) {
	if(e.addEventListener) { e.addEventListener(evType,fn,false); return true; }
	else if (e.attachEvent) { var r = e.attachEvent('on' + evType, fn); return r; }
	else { e['on' + evType] = fn; }
}

function helps(id,s) {

	s=s+\"<div onclick=\\\"clean('\"+id+\"')\\\" class='can' title='cancel'></div>\";

if(!idd(id)) {

// if(admin) alert('a='+isHelps()+' '+print_r(mHelps));

	mHelps[id]=1;

	mkdiv(id,\"<div class='corners'><div class='inner'><div class='content' id='\"+id+\"_body' align=left>\"+s+\"\
</div></div></div>\",'popup');

//mkdiv(id,\"<div class='inner'><div class='content' id='\"+id+\"_body'>\"+s+\"\
//<div onclick=\\\"clean('\"+id+\"')\\\" class='can' title='cancel'></div></div>\
//<div class='tl'></div><div class='tr'></div></div><div class='bl'></div><div class='br'></div>\",'popup'); 
//     if(id=='proverka_okna'){ var e=idd(id); 
//     
//     addEvent(e,'mousedown', function(e){ if(!e) e=window.event; this.style.cursor='move';
//     hmov[this.id]={x: e.clientX ? e.clientX : e.layerX, y: e.clientY ? e.clientY : e.layerY };
//     e.preventDefault();
//     });
//     
//     addEvent(e,'mouseup', function(e){if(!e) e=window.event;hmov[this.id]=0;} );
//     // addEvent(e,'mouseout', function(){if(hmov[this.id]){hmov[this.id]=0;}} );
//     addEvent(e,'mousemove', function(e){if(!e) e=window.event;var i=this.id;
//     if(hmov[i]) {
//     	this.style.left = parseFloat(this.style.left)+e.clientX-hmov[i].x+'px';
//     	this.style.top = parseFloat(this.style.top)+e.clientY-hmov[i].y+'px';
//     	hmov[i].x=e.clientX;
//     	hmov[i].y=e.clientY;
//     	e.preventDefault();
//     }
//     } );
//     }

	hid++;
	posdiv(id,mouse_x,mouse_y);

} else zabil(id+'_body',s);
}

// координаты мыши
var mouse_x=0, mouse_y=0;

document.onclick = function(e){ if(!e) e=window.event;
  if(e.pageX || e.pageY) { mouse_x=e.pageX; mouse_y=e.pageY; }
  else if (e.clientX || e.clientY) {
    mouse_x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
    mouse_y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
  }
};

function ajaxon(){ var id='ajaxgif'; mkdiv(id,\"<img src='".$GLOBALS['www_design']."img/ajax.gif'>\",'popup'); posdiv(id,mouse_x,mouse_y);}
function ajaxoff(){ clean('ajaxgif'); }

function ajax(name,value,func) { ajaxon();
JsHttpRequest.query('".$GLOBALS['www_ajax']."'+name,value,function(responseJS,responseText){if(responseJS.status){ajaxoff();eval(func);}},true);
}

function majax(url,a) { ajax(url,a,'if(responseJS.modo) eval(responseJS.modo);'); }


function setOpacity(e, n) { var o=getOpacityProperty(); if(!e || !o) return;
	if(o=='filter') { n *= 100; // Internet Exploder 5.5+
	// Если уже установлена прозрачность, то меняем её через коллекцию filters, иначе добавляем прозрачность через style.filter
		var oAlpha = e.filters['DXImageTransform.Microsoft.alpha'] || e.filters.alpha;
		if(oAlpha) oAlpha.opacity=n;
		else e.style.filter += 'progid:DXImageTransform.Microsoft.Alpha(opacity='+n+')'; // чтобы не затереть другие фильтры +=
	} else e.style[o]=n; // Другие браузеры
}

function getOpacityProperty() {
	if(typeof document.body.style.opacity == 'string') return 'opacity'; // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
	else if(typeof document.body.style.MozOpacity == 'string') return 'MozOpacity'; // Mozilla 1.6 и младше, Firefox 0.8 
	else if(typeof document.body.style.KhtmlOpacity == 'string') return 'KhtmlOpacity'; // Konqueror 3.1, Safari 1.1
	else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) return 'filter'; // IE 5.5+
	return false;
}


function getScrollW(){ return (document.documentElement.scrollTop || document.body.scrollTop); }
function getScrollH(){ return (document.documentElement.scrollLeft || document.body.scrollLeft); }
function getWinW(){ return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth; }
function getWinH(){ return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight; }

"); }

?>
