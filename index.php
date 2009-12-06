<?php
include "config.php";
include $include_sys."_autorize.php";

if($admin) { // включить сообщения об ошибках
	ini_set("display_errors","1");
	ini_set("display_startup_errors","1");
	ini_set('error_reporting', E_ALL);
	// error_reporting(E_ALL);
	// error_reporting = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR
	// error_reporting = E_ALL & ~E_USER_ERROR & ~E_USER_WARNING & ~E_USER_NOTICE
}

include $include_sys."_msq.php";
$_SCRIPT=array(); $_SCRIPT_ADD=array();
$_STYLE=array(); $_STYLE_ADD=array();
mystart();

// die( $filehost."<pre>".file_get_contents($filehost."config.php"));
// if(!$admin) die("admin error");
/*
if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй
if(!$admin) redirect($wwwhost."login/"); // посторонних - нахуй
blogpage();
// $_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."dnevnik.html"),
*/

$hashpage=rand(0,1000000); $hashpage=substr(broident($hashpage.$hashinput),0,6).'-'.$hashpage;

function blogpage() { global $_PAGE,$wwwhost,$login,$podzamok;

	STYLE_ADD($GLOBALS['httpsite'].$GLOBALS['www_design']."styles.css");

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."dnevnik.html"),
'prevnext'=>'',
'preword'=>'',
'preheader'=>'',
'calendar'=>'',
'counter'=>'',
'linkoff'=>"<a class=br href='".($_COOKIE['ctrloff']=='off'?$wwwhost."ctrl-on'>включить":$wwwhost."ctrl-off'>отключить")."</a>",
'coments'=>'',
'javascript'=>'',
'ajaxscript'=>'',

'prevlink'=>$wwwhost,
'nextlink'=>$wwwhost,
'uplink'=>$wwwhost,
'downlink'=>$wwwhost."contents/",

'www_design'=>$GLOBALS['www_design'],
'admin_name'=>$GLOBALS['admin_name'],
'httphost'=>$GLOBALS['httphost'],
'wwwhost'=>$wwwhost,
'signature'=>$GLOBALS['signature'],
'wwwcharset'=>$GLOBALS['wwwcharset'],

'hashpage'=>$GLOBALS['hashpage'],
'foto_www_preview'=>$GLOBALS['foto_www_preview'],
'foto_res_small'=>$GLOBALS['foto_res_small']

);

$_PAGE['logino'] = ($login ? "<p class=br><a href='".$wwwhost."logon?userinfo=".$login."'><img
src='".$GLOBALS['IS']['IMG']."' border=0>".$GLOBALS['IS']['USER0']."</a>".($podzamok?"<br><font color=red>подзамочный доступ</font><br><a
href='".$wwwhost."logon/?action=logoff'>разлогиниться</a>":"") :
"<p class=r><a href='".$wwwhost."logon?retpage=".urlencode($GLOBALS['mypage'])."'>залогиниться</a>");
}

list($path)=explode('?',$_SERVER["REQUEST_URI"]); $path=rtrim($path,'\/');
$pwwwhost=str_replace('/','\/',$wwwhost);

$months = explode(" ", " январь февраль март апрель май июнь июль август сентябрь октябрь ноябрь декабрь");
$months_rod = explode(" ", " января февраля марта апреля мая июня июля августа сентября октября ноября декабря");

// какие заметки доступны?
if($admin) $access=""; elseif($podzamok) $access="`Access` IN ('all','podzamok')"; else $access="`Access`='all'";
function WHERE($s='') { global $access;	if($s.$access=='') return ''; if($s=='' || $access=='') return "WHERE ".$s.$access; return "WHERE ".$s." AND ".$access; }

// ============== начали выяснять, какой модуль подцепить ==============

// рядовая заметка
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d\/\d\d.*)\.html/si", $path, $m)) { $Date = $m[1]; include("article.php"); exit; } // Заметка

// заметка месяца
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d)$/si", $path, $m)) { $Date = $m[1]; include("article.php"); exit; } // Заметка


// Корень => Последняя заметка ???
if($path."/" == $wwwhost) {
 	// Yandex заебал индексировать титул блога! Он же меняется все время! Блять, для кого robots.txt был написан?!
 	if( strstr($BRO,'Yandex') || $IP=='78.110.50.100') {
 	logi("yandex_nah.log","\n".date("Y/m/d H:i:s")." Yandex пошел нахуй");
 	redirect('http://lleo.aha.ru/na/?WWFuZGV4JSDy+yDt6PXz-yDt5SD36PLg5fj8IHJvYm90cy50eHQg6CDr5efl+Pwg6vPk4CDt5SDt4OTuLiDfIOTr-yDq7uPuIHJvYm90cy50eHQg7+jx4Os-JSDv8OXq8OD54Okg6O3k5erx6PDu4uDy-CDy6PLz6yDv5fDl4OTw5fHg9ujoIPLl7CDq7u3y5e3y7uwsIOru8u7w++kg7+4g7OXx8vMg7+Xw5eDk8OXx4Pbo6C4gx+Dl4eDrLCBZYW5kZXgsIPfl8fLt7uUg8evu4u4h');
 	}

	$last=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`Date` LIKE '____/__/%'")." ORDER BY `Date` DESC LIMIT 1","_l",$ttl);

	if($last=='') die("<p>Ошибка: нет последней записи! ".$o);
	redirect($wwwhost.$last.".html"); // на последнюю
	}

// Старый стиль именования
if(preg_match("/^".$pwwwhost."(\d\d\d\d)\-(\d\d)\-(\d\d)\.shtml/", $path, $m)) redirect($httphost.$m[1]."/".$m[2]."/".$m[3].".html");

// Содержание на месяц
if (preg_match("/^".$pwwwhost."(\d\d\d\d)\/(\d\d)\/?$/", $path, $m)) {
	$_PAGE["calendar"] = getCalendar($m[1], $m[2]);
	$_PAGE["title"] = $_PAGE["header"] = "Содержание дневника за ".$months[intval($m[2])]." ".$m[1]."-го";
	include($host_module."contents.php"); exit;
	}

// ===== подключение внешних модулей из директории /module/* ====
$mod_name=substr($path,strlen($wwwhost));
if(preg_match("/[^0-9a-z_\-\.]+/si",$mod_name)) idie("Error 404: wrong name \"<b>".htmlspecialchars($mod_name)."</b>\"");

// сначала в базе сайта
$text=ms("SELECT `text` FROM `site` ".WHERE("`name`='".e($mod_name)."' AND `type`='page'"),"_l",$ttl);
if($text!='') { $name=$mod_name; include("site.php"); exit; }

// затем в базе дневника
$article=ms("SELECT * FROM `dnevnik_zapisi` WHERE `Date`='".e($mod_name)."'","_1",$ttl);
if($article!=false) { $Date=$mod_name; include("article.php"); exit; }
// elseif($n>1) { idie("System Error: \"<b>".htmlspecialchars($mod_name)."</b>\" = ".$n); }

$mod=$host_module.$mod_name.".php"; if(file_exists($mod)) { include($mod); exit; }

idie("Error 404: Module not found \"<b>".htmlspecialchars($mod_name)."</b>\"

".($admin?"<p><a href='".$wwwhost."adminsite/?a=create&name=".urlencode($mod_name)."'>Создать эту страницу в базе `".$db_site."`?</a>":"")."

");

/*
if($path == $wwwhost."rss.xml") { $_PAGE["template"]=false; include($host_module."rss.php"); exit; } // RSS-канал
if($path == $wwwhost."whatis") { makehead("Правила этого дневника"); include("mod_whatis.php"); exit; }
if($path == $wwwhost."whatis_pravka") { makehead("Что такое система автоматической правки?"); include("mod_whatis_pravka.php"); exit; }
if($path == $wwwhost."contents") { makehead("Содержание дневника"); include("mod_contents.php"); exit; }
if($path == $wwwhost."rating") { makehead("Рейтинг дневника");	include("mod_rating.php"); exit; }
if($path == $wwwhost."comments") { makehead("листалка комментариев"); include("mod_comments.php"); exit; }
if($path == $wwwhost."pravki") { makehead("листалка правок"); include("mod_pravka.php"); exit;	}
if($path == $wwwhost."ctrl-off") { set_cookie('ctrloff','off',time()+86400*365,"/",$cookie_site,0); redirect($wwwhost); } // откл стрелки
if($path == $wwwhost."ctrl-on") { set_cookie('ctrloff','no',time()+86400*365,"/",$cookie_site, 0); redirect($wwwhost); } // вкл стрелки
if($path == $wwwhost."admin") { makehead("Админка"); include("admin.php"); exit; }
if($path == $wwwhost."logon") { makehead("Сектор авторизации и логинов"); include("mod_logon.php"); exit; }
if($path == $wwwhost."rssc.xml") { $template=false; include("mod_rssc.php"); exit; } // RSS-канал комментариев
*/
/* del */ // if($path == $wwwhost."dsbw.xml") { $template = false; include("mod_dsbw.php"); exit; } // RSS-канал

function makehead($s) { global $_PAGE;
	$_PAGE["calendar"] = "<a href=".$GLOBALS["wwwhost"].">в дневник</a>";
	$_PAGE["title"] = $_PAGE["header"] = $s;
	$_PAGE["counter"] = "";
	return;
}

function getCalendar($year, $mon, $day = false) { global $admin, $wwwhost, $months, $podzamok; $s = "";

	if(intval($year)==0) return '';

	$ttl=($admin?0:$GLOBALS["ttl"]*10); // для календаря - десятикратное время пребывания в кэше

	$m = mktime(1, 1, 1, $mon, 1, $year); // старт месяца
	$k = date("w",$m)-1; if($k<0) $k=6; // день недели первого числа месяца
	$end = date("t",$m); // дней в этом месяце
	$now = date("Y/m/d"); // сегодняшняя дата

	// выбрать существующие заметки месяца
	$sql = ms("SELECT `Date`,`Access`,`Prev`,`Next` FROM `dnevnik_zapisi` ".WHERE("`Date` LIKE '".$year."/".$mon."/__%'")." ORDER BY `Date`","_a",$ttl);
	$a=array(); foreach($sql as $p) $a[$p['Date']]=$p['Access'];

	$Prev=$sql[0]['Prev']; if($Prev!='') $Prev="<a href='".$wwwhost.$Prev.".html'>&lt;&lt;</a>";
	elseif($admin) $Prev="<a href='".$wwwhost.date("Y/m",$m-60*60*24)."'>&lt;&lt;</a>";
	$Next=$sql[sizeof($sql)-1]['Next']; if($Next!='') $Next="<a href='".$wwwhost.$Next.".html'>&gt;&gt;</a>";
	elseif($admin) $Next="<a href='".$wwwhost.date("Y/m",$m+$end*60*60*24)."'>&gt;&gt;</a>";
	
$s .= "<table border=0 cellspacing=0 cellpadding=1>
<tr><td class=cld_top>".$Prev."</td><td colspan=5 align=center class=cld_top>".$months[intval($mon)]." ".intval($year)."</td><td align=right class=cld_top>".$Next."</td></tr>
<tr><td class=cld_days>ПН</td><td class=cld_days>ВТ</td><td class=cld_days>СР</td><td class=cld_days>ЧТ</td><td class=cld_days>ПТ</td><td class=cld_red><b>СБ</b></td><td class=cld_red><b>ВС</b></td></tr>";


//$s=''; for($i=0;$i<10;$i++) $s.="<br>".(date("w",$i*60*60*24)); die($s);
// $a=0; if(--$a) die("1"); else die("0");
#1 2 3 4 5 6 0
//$a=array(); $a[0]='1'; if(($x=$a[0])) die('true'); else die('false');
//die("#######<pre>".print_r($a,1));


	if($k) { $s.="<tr>"; for($i=0;$i<$k;$i++) $s.="<td class=".($i>4?"cld_red":"cld").">&nbsp;</td>"; } // проставить пустые клетки

	for($i=1; $i<=$end; $i++) {
		if(!$k) $s .= "<tr>";
		$d=sprintf("%04d/%02d/%02d",$year,$mon,$i);
		$style=($d==$now?" style='background-color: #FFFFa0; border: red solid 1px;'":'');
		$di=$i;
		if(!($x=$a[$d])) { if($admin) $di="<a class=cld_ed href='".$wwwhost."editor/?Date=".urlencode($d)."'>".$i."</a>";
		} else {
			if($x=='podzamok') $di="<s>".$di."</s>";
			elseif($x=='admin') $di="<s><b>".$di."</b></s>";
			$di="<a href='".$wwwhost.$d.".html'>".$di."</a>";
		}
		$s .= "<td class=".($k>4?"cld_red":"cld").$style.">".$di."</td>";
		if($k==6) $s .= "</tr>"; if(++$k>6) $k=0;
	}

return $s."</table>";
}

function ljaddr($lju) { if(!$lju) return '';
return "http://".($lju==trim($lju,"_-")?$lju.".livejournal.com":"users.livejournal.com/".$lju)."/";
}

function ljaddru($lju) { if(!$lju) return '';
return "<img src=http://stat.livejournal.com/img/userinfo.gif style=\"vertical-align: center;\"><a href=".ljaddr($lju).">".$lju."</a>";
}




function urldata($d) { return $GLOBALS['wwwhost'].htmlspecialchars($d).(strstr($d,'/')?".html":''); }


function mk_prevnest($prev,$next) { // БЛИТЬ ИДИТЕ ВСЕ НАХУЙ!!! НЕ ПОЛУЧАЕТСЯ У МЕНЯ С ВАШИМИ ЙОБАННЫМИ CSS!!! ГОРЕТЬ ИМ В АДУ!!!
$prev=($prev==''?'&nbsp;':"<font size=1>".$prev."</font>");
$next=($next==''?'&nbsp;':"<font size=1>".$next."</font>");
return "<center><table width=98% cellspacing=0 cellpadding=0><tr valign=top><td width=50%>$prev</td><td width=50% align=right>$next</td></tr></table></center>";
}

function c($s) { return trim($s,"\n\r\t \'\""); }
function SCRIPTS($s,$l=0) { if(!$l) $GLOBALS['_SCRIPT'][]=$s; else $GLOBALS['_SCRIPT'][$s]=$l; }
function STYLES($s,$l=0) { if(!$l) $GLOBALS['_STYLE'][]=$s; else $GLOBALS['_STYLE'][$s]=$l; }
function SCRIPT_ADD($s) { $GLOBALS['_SCRIPT_ADD'][$s]=$s; }
function STYLE_ADD($s) { $GLOBALS['_STYLE_ADD'][$s]=$s; }

// ==============================================================================================
// повызывать все процедуры в цикле

function modules($s) { $s_old=''; $stop=100; while($s!=$s_old && --$stop) {
        $s_old=$s; $s=preg_replace_callback("/\{_(.*?)_\}/s","module",$s);
        }
        return $s;
}

function module($t) { $s=$t[1]; // подцепить модули

        if(strstr($s,':')) { // подсключаемый модуль
                list($mod,$arg)=explode(':',$s,2); $mod=c($mod);

                if(!function_exists($mod)) {
                        $mod=str_replace('..','',$mod); // так просто
                        $modfile=$GLOBALS['site_mod'].$mod.".php";
                        if(!file_exists($modfile)) idie("Module error: ".htmlspecialchars($modfile));
                        include_once($modfile);
                        if(!function_exists($mod)) idie("Нет такой функции: ".htmlspecialchars($mod));
                }
                return call_user_func($mod,c($arg));
        }

        // иначе - просто вынуть из базы
        $p=ms("SELECT `id`,`text`,`type` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($s)."'","_1",$ttl);
        $o=$p['text'];

        if($p['type']=='news') { // для новостей - своя текстовая обработка
                $o=str_replace(array("\n\n","\n"),array("<p>","<br>"),"\n\n".$o);
                $o=preg_replace_callback("/(>[^<]+<)/si","kawa",$o);
                $o=preg_replace("/([\s>]+)\-([\s<]+)/si","$1".chr(151)."$2",$o); // длинное тире
                $o="<div id='".$p['id']."'>".$o."</div>";
        }

        if(preg_replace("/\{_(SCRIPT\:|STYLE\:|SCRIPT_ADD\:|STYLE_ADD\:).*?_\}/si",'',c($o))=='') return '';
        return "<!--".$p['id']."-->".$o."<!--/".$p['id']."-->";
}

function SCRIPT($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_SCRIPT'][c($n)]=addm(c($s)); return ''; }
function STYLE($s) { list($n,$s)=explode(':',$s,2); $GLOBALS['_STYLE'][c($n)]=addm(c($s)); return ''; }
function addm($e) { return (strstr($e,"\n")?$e:ms("SELECT `text` FROM `".$GLOBALS['db_site']."` WHERE `name`='".e($e)."'","_1",$ttl)); }
?>
