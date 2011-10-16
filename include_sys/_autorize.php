<?php

// йбаный PHP
// php_flag register_globals off
// if(function_exists('ini_get')&&(ini_get('register_globals')==false)&&(PHP_VERSION<4.3))
// if(ini_get('register_globals')!=false) 
// foreach(array_merge($_GET,$_POST,$_REQUEST,$_COOKIE) as $n=>$l) unset(${$n});

// определим блог и прогоним нахуй www.
$MYHOST=substr($httpsite,7);
if($_SERVER["HTTP_HOST"]!=$MYHOST) { list($AC,$l,$s)=explode('.',$_SERVER["HTTP_HOST"],3);
if($AC=='www') {
	if(isset($redirect_www)) redirect((substr($_SERVER["HTTP_HOST"],4)==$MYHOST?$httpsite:str_replace('//','//'.$l.'.',$httpsite)).$_SERVER["REQUEST_URI"]);
	else $AC=$l;
}
}

//========================================================================================
//Некоторые ебанутые сборки PHP не имеют элементарных функций, мне придется их эмулировать
// Также надо будет сделать эмуляцию curl и iconv.

if(!function_exists('file_put_contents')) { function file_put_contents($url,$s) { $f=fopen($url,"w"); fputs($f,$s); fclose($f); } }
// ЕБАНУТЬСЯ!!!!!!
if(!function_exists('str_ireplace')){ function str_ireplace($a,$b,$s){ $t=chr(1); $h=strtolower($s); $n=strtolower($a);
 while(($pos=strpos($h,$n))!==FALSE){ $s=substr_replace($s,$t,$pos,strlen($a)); $h=substr_replace($h,$t,$pos,strlen($a)); }
 return str_replace($t,$b,$s);
}}

// Также надо прописать пермиссионс

function filechmod($f,$p=''){ if($p=='') $p=isset($GLOBALS['fchmod'])?$GLOBALS['fchmod']:0644; chmod($f,$p); }
function dirchmod($d,$p=''){ if($p=='') $p=isset($GLOBALS['dchmod'])?$GLOBALS['dchmod']:0755; chmod($d,$p); }
function fileput($f,$s) { $o=file_put_contents($f,$s); filechmod($f); return $o; }
function dirput($d) { $o=mkdir($d); dirchmod($d); return $o; }
function testdir($s) { $a=explode('/',rtrim($s,'/')); $s=''; for($i=0;$i<sizeof($a);$i++) { $s.='/'.$a[$i]; if(!is_dir($s)) dirput($s); } }
function getras($s){ $r=explode('.',$s); if(sizeof($r)==1) return ''; return strtolower(array_pop($r)); }
function rpath($l) { // $p=array_filter(explode(DIRECTORY_SEPARATOR,$l),'strlen');
  $l=str_replace("\\",'/',$l); $a=array();
  foreach(explode('/',$l) as $x){ if((''==$x&&!empty($a))||'.'==$x) continue; if('..'==$x) array_pop($a); else $a[]=$x; }
  return implode('/',$a);
}

//========================================================================================
$jaajax=strstr($_SERVER['REQUEST_URI'],'/ajax/')?1:0;

if(isset($_COOKIE["adm2"]) && $_COOKIE["adm2"]==$admin_hash1
|| isset($_COOKIE["adm"]) && $_COOKIE["adm"]==broident($admin_hash.$koldunstvo)
) { $admin=1; //-- авторизация админа
	if(!$jaajax) { // если не аякс - включить отладочные сообщения для админа
ini_set("display_errors","1"); ini_set("display_startup_errors","1"); ini_set('error_reporting', E_ALL); // включить сообщения об ошибках
// error_reporting(E_ALL);
// error_reporting = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR
// error_reporting = E_ALL & ~E_USER_ERROR & ~E_USER_WARNING & ~E_USER_NOTICE
}} else $admin=0;






#if(!$admin) die("На ремонте, извините.");










$podzamok = ($admin?true:false);
$aharu = (strstr($_SERVER["HTTP_HOST"],'lleo.aha.ru')?true:false);
$lju=getlj(); if($lju!==false) setcoo("lju",base64_encode($lju));
$IP=$_SERVER["REMOTE_ADDR"]; $IPN=ip2ipn($IP); $BRO=hh($_SERVER["HTTP_USER_AGENT"]);
$REF=hh(isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:'');
$MYPAGE=str_replace(array('<','>',"'",'"'),array('%3C','%3E','%27','%22'),$_SERVER["REQUEST_URI"]); list($mypage) = explode('?',$MYPAGE.'?',2);
include $include_sys."_msq.php"; // все процедуры работы с MySQL
$months = explode(" ", " январь февраль март апрель май июнь июль август сентябрь октябрь ноябрь декабрь");
$months_rod = explode(" ", " января февраля марта апреля мая июня июля августа сентября октября ноября декабря");
$jog_kuki="<div style='position:absolute;width:1px;height:1px;overflow:hidden;left:-40px;top:0;opacity:0'><object id=kuki width=1 height=1 style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'><param name='movie' value='".$GLOBALS['www_design']."kuki_ray.swf' /><embed src='".$GLOBALS['www_design']."kuki_ray.swf' width=1 height=1 name=kuki type='application/x-shockwave-flash'></embed></object></div>";
$kuki6="<div style='position:absolute;width:1px;height:1px;overflow:hidden;left:-40px;top:0;opacity:0'><object id=kuki6 width=1 height=1 style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'><param name='movie' value='".$GLOBALS['www_design']."kuki6.swf' /><param name='allowScriptAccess' value='sameDomain' /><param name='loop' value='true' /><embed src='".$GLOBALS['www_design']."kuki6.swf' width='1' height='1' name='kuki6' allowScriptAccess='sameDomain' loop='true' type='application/x-shockwave-flash'></embed></object></div>";

// ===== АВТОРИЗАЦИЯ =====
if(!isset($autorizatio)) { // не работать с авторизацией при аяксе или вяном запрете (модуль restore_unic.php)
	$up=(isset($_REQUEST['up'])?$_REQUEST['up']:isset($_COOKIE[$uc])?$_COOKIE[$uc]:''); // взять куку авторизации
	if($up=='candidat') set_unic(); // был кандидатом, зашел второй раз? получи свой номер!
	else { // ошибка пароля или нет такого номера в базе - назначить кандидатом
		$unic=intval(substr($up,0,strpos($up,'-')));
		if( !$unic || !upcheck($up) || getis_global($unic)===false ) set_unic_candidat();
	}
}
// ============================= ДАЛЬШЕ ТОЛЬКО ФУНКЦИИ ==========================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================

function upcheck($up) { if(!strstr($up,'-')) return false; list($u,$p)=explode('-',$up,2); return ($p==md5($u.$GLOBALS['newhash_user'])?true:false); }
function upset($unic) { return $unic."-".md5($unic.$GLOBALS['newhash_user']); }

function llog($s) { global $aharu,$IP,$BRO,$MYPAGE; if(!$aharu) return; logi('autoriza.txt',"\n".h($s." ".$IP." | ".$BRO." | ".$MYPAGE)); }
function trevoga($s) { global $aharu,$IP,$BRO,$MYPAGE; if(!$aharu) return; logi('TREVOGA.txt',"\n".h($s." ".$IP." | ".$BRO." | ".$MYPAGE)); }

function getlj() { global $REF; // Определение ЖЖ-истов
if(isset($_COOKIE['lju'])&&$_COOKIE['lju']!='null'&&$_COOKIE['lju']!='undefined') return preg_replace("/[^0-9a-z\_\-]/si",'',base64_decode($_COOKIE['lju']));
if(strstr($REF,'/friends') && (preg_match("/\Ahttp\:\/\/(.+?)\.livejournal\.com\/friends/",$REF,$m) || preg_match("/\Ahttp\:\/\/users\.livejournal\.com\/(.+?)\/friends/",$REF,$m)))
return preg_replace("/[^0-9a-z\_\-]/si","",$m[1]);
return false;
}

function set_unic_candidat() { global $up,$unic,$uc,$podzamok,$imgicourl; $up='candidat'; $unic=0; $IS=array(); $podzamok=0; $imgicourl=$up; setcoo($uc,$up); }

function set_unic() { if($GLOBALS['jaajax']) return; global $IS,$uc,$IPN,$unic,$lju,$up,$podzamok,$imgicourl; $unic=0;
if(msq_add($GLOBALS['db_unic'],array('ipn'=>$IPN,'lju'=>e($lju),'time_reg'=>time()))===false) { trevoga("DB ADD FALSE!!!!"); return; }
	$unic=mysql_insert_id(); if(!$unic) { trevoga("mysql_insert_id():".$unic); die('unic=0 '.$GLOBALS['msqe']); }
		$up=upset($unic); $IS=array(); $podzamok=0; $imgicourl=$unic;
	setcoo($uc,$up);
}

function WHERE($s='',$z='') { // какие заметки доступны?
	$a=($GLOBALS['admin']?"":$z.($GLOBALS['podzamok']?"`Access` IN ('all','podzamok')":"`Access`='all'"));
	if($s.$a=='') return ''; if($s=='' || $a=='') return "WHERE ".$s.$a; return "WHERE ".$s." AND ".$a;
}


function getis_global($unic) { global $IS;
if(stristr($GLOBALS['BRO'],'blogtest')) { 
	$GLOBALS['unic']=$unic=666;
	$GLOBALS['up']=upset($unic);
	$IS=array( // тестовый логин
	'id'=>666,
	'login'=>'pupkin',
//	'login'=>'',
//	'openid'=>'vasya.pupkin.ru',
	'openid'=>'',
	'obr'=>'realname',
	'lju'=>'pupkin',
//	'password'=>md5('666'.$GLOBALS['newhash_user']),
	'password'=>'',
	'realname'=>'System Test Login','mail'=>$GLOBALS['admin_mail'],'site'=>'http://lleo.aha.ru/na',
	'birth'=> strtotime("1972-05-21"),'admin'=>'user', // 'user','podzamok','admin','mudak'
	'ipn'=>ip2ipn('10.8.0.1'),'time_reg'=>time(),'timelast'=>time(),'capcha'=>'no', // 'yes','no'
	'capchakarma'=>0
); $GLOBALS['admin']=$GLOBALS['podzamok']=false; }

else $IS=getis($unic); if($IS!==false) {
	$GLOBALS['unic']=$unic;
	$GLOBALS['podzamok']=$GLOBALS['admin']||$IS['admin']=='podzamok'?true:false;
	$GLOBALS['imgicourl']=(!empty($IS['imgicourl'])?$IS['imgicourl']:'#'.$unic);
	}
return $IS;
}

function getis($unic) {
	$is=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `id`='$unic'","_1"); if($is!==false) {
		$is=array_merge($is,get_ISi($is));
		$is['imgicourl']=h($is['user']);
		if(isset($is['url'])) $is['imgicourl']="<a href='http://".h($is['url'])."'>".$is['imgicourl']."</a>";
		if(isset($is['ico'])) $is['imgicourl']="<img src='".h($is['ico'])."'>".$is['imgicourl'];
		if($is['admin']=='podzamok' || $is['admin']=='admin') $is['imgicourl']=zamok('podzamok').$is['imgicourl'];
	}
	return $is;
}

function get_ISi($is) {
	if($is['obr']=='realname' and $is['realname']!='') return array('user'=>$is['realname']);
	if($is['obr']=='login' and $is['login']!='') return array(
		'url'=>$GLOBALS['blog_name']."/user/".$is['login'],
		'user'=>$is['login'],
//			'DOMAIN'=>$GLOBALS['blog_name'],
//			'ico'=>$GLOBALS['www_ico']."favicon.ico"
);
	$log=$is['openid']; if($log=='') return array('user'=>'#'.$is['id'],'user_noname'=>'noname');
	if(preg_match("/^([^\/]+).*\/([^\/]+)$/",$log,$l)) { $user=$l[2]; $dom=$l[1]; $root=$dom; }
	elseif (preg_match("/^([^\. ]+)\.(.*)$/",$log,$l)) { $user=$l[1]; $dom=$l[2]; $root=$log; }

	return array(
		'url'=>$log,
		'user'=>$user,
//		'DOMAIN'=>$dom,
		'ico'=>get_IS_IMGi($dom,$root)
	);
}

function get_IS_IMGi($dom,$root) { global $www_ico;
	if($dom=='lleo.aha.ru') return; // $www_ico."fav.ico";
//	if($log=='lleo.aha.ru' || $log=='lleo') return $www_ico."favicon.ico";
	if($dom=='livejournal.com' || $dom=="users.livejournal.com") return; // $www_ico."lj.gif";
	if($dom=='myopenid.com') return; // $www_ico."myopenid.ico";
	if($dom=='blogspot.com') return; // $www_ico."blogspot.ico";
	if($dom=='openid.yandex.ru') return $www_ico."yandex.ico";
	if($dom=='ya.ru') return; // $www_ico."ya.ico";
	if($dom=='moikrug.ru') return; // $www_ico."moikrug.ico";
	return "http://google.com/s2/favicons?domain=".$root;
}

function broident2($add) { return sha1($add); }
function broident($add) { return md5($_SERVER["HTTP_USER_AGENT"].$_SERVER["HTTP_ACCEPT_LANGUAGE"].$_SERVER["HTTP_ACCEPT_ENCODING"].$_SERVER["HTTP_ACCEPT_CHARSET"].$add); } // $_SERVER["HTTP_ACCEPT"] аПЮСГЕП уПНЛ - лсдюй.

function otprav($s) { global $_RESULT,$msqe; $_RESULT["modo"] = ($msqe?
"helps('mysql_error',\"<fieldset><legend>mysql_error</legend>"
.njs("<div style='font-size: 11px; text-align: left;'>".$msqe."</div>")."</fieldset>\");"
:$s); $_RESULT["status"] = true; exit; }
function njs($s) { return str_replace(array("\\","'",'"',"\n","\r"),array("\\\\","\\'",'\\"',"",""),$s); }
function njsn($s) { return str_replace(array("\\","'",'"',"\n","\r"),array("\\\\","\\'",'\\"',"\\n",""),$s); }
function oalert($s) { otprav("alert(\"".njs($s)."\")"); }
//function prejs($s) { return str_replace(array("&","\\","'",'"',"\n","\r"),array("&amp;","\\\\","\\'",'\\"',"\\n",""),$s); }
function otprav_sb($scr,$s) { otprav("loadScriptBefore('$scr',\"".njs($s)."\");"); }

function dier($a,$t='') { idie($t."<pre>".nl2br(h(print_r($a,1)))."</pre>"); } // отладочная процедурка

function idie($s,$h='') { // если это был аякс - выдать аякс-окно

	if(!empty($GLOBALS['ajax'])) {
		if($h=='') $h="Fatal error: ".h($GLOBALS['mypage']);
		otprav("helpc('idie',\"<fieldset><legend>".$h."</legend><div style='text-align: left;'><small>".njs($s)."</small></div></fieldset>\")");
	}
	ob_end_clean(); 
	if($h) header($h);
	die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$GLOBALS['wwwcharset']."\" /><title>Error</title></head><body>".$s."</body></html>");
}

function mystart() {
	Error_Reporting(E_ALL & ~E_NOTICE);
	session_start();
	ob_start("onPostPage");
	header("Content-Type: text/html; charset=".$GLOBALS['wwwcharset']);
}

function onPostPage($buffer) { global $_PAGE,$_SCRIPT,$_SCRIPT_ADD,$_STYLE,$_HEADD;
        if(!isset($_PAGE) || $_PAGE['design']=='') return $buffer;
	$s = str_replace("{body}",$_PAGE['body'].$buffer,$_PAGE["design"]);

	$myscript=''; // прописать скрипты
	foreach($_SCRIPT as $n=>$l) $myscript.="\n\n// --- ".$n." ---\n".$l."\n// --- / ".$n." ---\n";
	if($myscript!='') $myscript="<script language='JavaScript'>\n".c0($myscript)."\n</script>";
	foreach($_SCRIPT_ADD as $l) $myscript .= "\n<script type='text/javascript' language='JavaScript' src='".$l."'></script>"; //.$myscript;

	if(isset($GLOBALS['mytitle'])) if(stristr($s,'<title>')) $s=preg_replace("/<title>.*?<\/title>/si","<TITLE>".$GLOBALS['mytitle']."</TITLE>",$s);
	else $s=str_ireplace("</head>","<TITLE>".c0($GLOBALS['mytitle'])."</TITLE>\n</head>", $s);

	$mystyle=implode("\n",$_STYLE); if($mystyle!='') $mystyle="<style type='text/css'>\n".c0($mystyle)."\n</style>";
	if(sizeof($_HEADD)) $mystyle.="\n<".implode(" />\n<",$_HEADD)." />"; // добавить в head

	$s=str_ireplace("</head>",$myscript."\n".$mystyle."\n</head>",$s); // добавить в head

	unset($_PAGE["design"]); unset($_PAGE["body"]);
        foreach($_PAGE as $k=>$v) $s=str_replace("{".$k."}",$v,$s);
	return $s;
}

function redirect($path='/',$code=301) {
	if(isset($GLOBALS['ajax'])&&$GLOBALS['ajax']) otprav("window.location='$path';");
        if(!headers_sent()) {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$path,TRUE,$code); // навсегда: 301
                exit;
        }
	die("<noscript><meta http-equiv=refresh content=\"0;url=\"".$path."\"></noscript><script>location.replace(\"".$path."\")</script>");
}

function logi($f,$s,$a="a+") { $n=$GLOBALS["host_log"].$f; $l=fopen($n,$a); fputs($l,$s); fclose($l); chmod($n,0666); }
function add_get() { if(sizeof($_GET)==0) return ''; $s='?';foreach($_GET as $a=>$b) if($b!='') $s.="$a=".urlencode($b)."&"; return trim($s,'&'); }
function page($l,$c=50) { $m=split("\n",$l); $i=0; foreach($m as $t) if(strlen($t)<$c) $i++; else $i=$i+1+(floor(strlen($t)/$c)); return($i); }
function uw($s) { return(iconv("utf-8","windows-1251//IGNORE",$s)); }
function uk($s) { return(iconv("utf-8","koi8-r//IGNORE",$s)); }
function wu($s) { return(iconv("windows-1251","utf-8//IGNORE",$s)); }
function ku($s) { return(iconv("koi8-r","utf-8//IGNORE",$s)); }
function kw($s) { return(iconv("koi8-r","windows-1251//IGNORE",$s)); }
function wk($s) { return(iconv("windows-1251","koi8-r//IGNORE",$s)); }
function selecto($n,$x,$a,$t='name') { if($x==='0'||intval($x)) $x=intval($x);
	$s="<select ".$t."='".$n."'>";
	foreach($a as $l=>$t) $s.="<option value='$l'".($x===$l?' selected':'').">".$t."</option>";
	return $s."</select>"; }

function kawa($p) { $s=$p[1];
        $s=preg_replace("/([A-Za-z\x80-\xFF.,?!])\"/s","$1\xBB",$s); // "$1&raquo;"
        $s=preg_replace("/\"([A-Za-z\x80-\xFF.])/s","\xAB$1",$s); // "&laquo;$1"
        return $s;
}

function ispravkawa($s) {
	$s=preg_replace_callback("/(>[^<]+<)/si","kawa","<>$s<>");
	$s=preg_replace("/([\s>]+)\-([\s<]+)/si","$1".chr(151)."$2","<>$s<>"); // длинное тире
	return str_replace('<>','',$s);
}

function nekawa($s) { return strtr($s,"\xBB"."\xAB".chr(151),'""-'); }


function setcoo($n,$v,$t=0) { if(!$GLOBALS['jaajax']) setcookie($n,$v,($t?$t:time()+86400*365),"/","",0); }

/*
function set_cookie($Name,$Value='',$MaxAge=0,$Path='',$Domain='',$Secure=false,$HTTPOnly=false) {
	if(isset($GLOBALS['cookie_method_old'])) { setcookie($Name, $Value, $MaxAge, $Path, $Domain, 0); return; }
	header('Set-Cookie: ' . rawurlencode($Name) . '=' . rawurlencode($Value)
		.(empty($MaxAge) ? '' : '; Max-Age=' . $MaxAge)
		.(empty($Path)   ? '' : '; path=' . $Path)
		.(empty($Domain) ? '' : '; domain=' . $Domain)
		.(!$Secure       ? '' : '; secure')
		.(!$HTTPOnly     ? '' : '; HttpOnly'), false);
}
*/

function file_get($f,$c=true) {	if(!$GLOBALS['cache_get'] or !is_dir($GLOBALS['fileget_tmp'])) return file_get_contents($f);
	$n=preg_replace("/[^0-9a-zA-Z_\-\.\~]+/si","#", str_replace("http://","",$f) );
	if(strlen($n)<100) $n=$GLOBALS['fileget_tmp'].$n.".dat"; else $n=$GLOBALS['fileget_tmp'].md5($n).".dat";
	if(file_exists($n)) { if(!$c) return unlink($n); return file_get_contents($n); }
	$x=file_get_contents($f); file_put_contents($n,$x); chmod($n,0666); return $x;
}

function zamok($d) {
        if($d=='all'||$d=='user') return '';
        $z = "<img src=".$GLOBALS['www_design']."e/podzamok.gif>&nbsp;";
        if($d=='podzamok') return $z;
        if($d=='mudak') return '-';
        if($d=='admin') return $z.$z;
	return '';
}

function h($s) { return htmlspecialchars($s); }
function hh($s) {
	if(stristr(substr($s,0,10),'javascript')) $s="jаvаsсriрt".substr($s,10);
	return str_replace(
		array('&','"',"'",'<','>',"\t","\r","\n"),
		array('&amp;','&quot;','&#039;','&lt;','&gt;','\t','\r','\n'),$s);
}
function c($s) { return trim($s,"\n\r\t \'\""); }
function c0($s) { return trim($s,"\n\r\t "); }

function ip2ipn($s){ if(($i=ip2long($s))<0) $i+=4294967296; return $i; }
// $m=explode('.',$s,4); return $m[0]*16777216+$m[1]*65536+$m[2]*256+$m[3]; 

function ipn2ip($i){ return long2ip($i); }
//return long2ip(-(4294967296-$us32str));
//$a=$i%256; if($a<0) $a+=256; $i=floor($i/256); $b=$i%256;$i=floor($i/256); $c=$i%256;$i=floor($i/256); $d=$i%256;
//return "$d.$c.$b.$a";

function mail_validate($s) { 
	$s=preg_replace("/[^0-9a-z_\-\.\@]+/si",'',$s);
	return (preg_match("/^[0-9a-z_\-\.]+\@[0-9a-z\-\.]+\.[0-9a-z]{2,10}$/si", $s) ? $s : false);
}
function site_validate($s) { return (preg_match("/^([a-z]+:\/\/|(www\.))[a-z][a-z0-9_\.\-]*\.[a-z]{2,6}[\[a-zA-Z0-9!#\$\%\&\(\)\*\+,\-\.\/:;=\?\@]*$/i",$s)? $s : false); }
function get_link($Date) { list($y,$m,$d)=explode("/",substr($Date,0,10),3); if($y*$m*$d) return $GLOBALS['httphost'].$Date.".html";	return $GLOBALS['httphost'].$Date; }
function get_link_($Date) { list($y,$m,$d)=explode("/",substr($Date,0,10),3); if($y*$m*$d) return $GLOBALS['wwwhost'].$Date.".html";	return $GLOBALS['wwwhost'].$Date; }

function getmaketime($d) {
        if(!preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d)(.*?)$/s",$d,$m)) return array(0,0);
        $d=$m[1]."-".$m[2]."-".$m[3];
        $t0=strtotime($d);
        if(preg_match("/^[\-_\s]*(\d\d)-(\d\d)/s",$m[4],$t)) $d .= " ".$t[1].":".$t[2];
        $t=strtotime($d);
        while(msq_exist('dnevnik_zapisi',"WHERE `DateDatetime`='$t'")) $t++;
        return array($t0,$t);
}

function get_counter($p) { // $p['view_counter']
	if(isset($GLOBALS['article']["counter"])) return $GLOBALS['article']["counter"];
        $c=intval(ms("SELECT COUNT(*) FROM `dnevnik_posetil` WHERE `url`='".intval($p['num'])."'","_l"));
        if($GLOBALS['old_counter']) $c+=$p["view_counter"];
        $article["counter"]=$c;
	cache_set('count_'.trim($GLOBALS['blogdir'],'/').'_'.intval($p['num']),$c,600); // записать в memcache
        return $c;
}

// работа с объектами tmp в записях и комменатриях
function get_last_tmp() { $s=ms("SELECT `text` FROM `unictemp` WHERE `unic`='".intval($GLOBALS['unic'])."'","_l",0); return ($s===false?'':$s); }
function del_last_tmp() { msq("DELETE FROM `unictemp` WHERE `unic`=".intval($GLOBALS['unic']).""); }
function put_last_tmp($s) { msq_add_update('unictemp',array('unic'=>intval($GLOBALS['unic']),'text'=>e($s)),'unic'); }

function mk_prevnest($prev,$next) { // БЛИТЬ ИДИТЕ ВСЕ НАХУЙ!!! НЕ ПОЛУЧАЕТСЯ У МЕНЯ С ВАШИМИ ЙОБАННЫМИ CSS!!! ГОРЕТЬ ИМ В АД
$prev=($prev==''?'&nbsp;':"<font size=1>".$prev."</font>");
$next=($next==''?'&nbsp;':"<font size=1>".$next."</font>");
return "<center><table width=98% cellspacing=0 cellpadding=0><tr valign=top><td width=50%>$prev</td><td width=50% align=right>$next</td></tr></table></center>";
}


// =====================================================================================
function search_podsveti_body($a) {
        $a=preg_replace_callback("/>([^<]+)</si","search_p_body",'>'.$a.'<');
        $a=ltrim($a,'>'); $a=rtrim($a,'<');
        return $a;
} function search_p_body($r) {
return '>'.str_ireplace2_body($_GET['search'],"<span class=search>","</span>",$r[1]).'<'; }


function str_ireplace2_body($search,$rep1,$rep2,$s){ $c=chr(1); $nashlo=array(); $x=strlen($search);
        $SEARCH=strtolower2_body($search);
        $S=strtolower2_body($s);
        while (($i=strpos($S,$SEARCH))!==false){
                $nashlo[]=substr($s,$i,$x);
                $s=substr_replace($s,$c,$i,$x);
                $S=substr_replace($S,$c,$i,$x);
        } foreach($nashlo as $l) $s=substr_replace($s,$rep1.$l.$rep2,strpos($s,$c),1);
        return $s;
}

function strtolower2_body($s){
$s=strtr($s,'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЬЪЭЮЯ','абвгдеёжзийклмнопрстуфхцчшщыьъэюя'); // русские в строчные
$s=strtr($s,'авсенкмортху','abcehkmoptxy'); // русские какие похожи - в латинские
$s=strtolower($s); // латинские в строчные
return $s;
}
// =====================================================================================
function ifu($s){ $l=uw($s); return ($s==wu($l)?$l:$s); }

function RE($s) { return isset($_REQUEST[$s])?$_REQUEST[$s]:false; }
function RE0($s) { return isset($_REQUEST[$s])?intval($_REQUEST[$s]):false; }
function AD() { if(!$GLOBALS['admin']) idie('Только для админа.'); }


function LLoad($lang) { global $langbasa;
	$a=file($GLOBALS['filehost'].'/module/lang/'.$lang.'.txt');
	if(!isset($langbasa)) $langbasa=array();
	foreach($a as $l) { if(!strstr($l,"\t")) continue;
		list($c,$s)=explode("\t",$l,2);
		$langbasa[c($c)]=trim($s,"\t\r\n");
	}
}

// LL('tufta','123') //tufta	Привет, номер {1}!
function LL($n,$ara=false){ global $mylang,$langbasa; if(!isset($langbasa)) LLoad(isset($mylang)?$mylang:'ru');
        $s=$langbasa[$n]; if($ara===false) return $s;
	if(gettype($ara)!='array') $ara=array($ara);
	foreach($ara as $n=>$l) {
                $s=str_replace('{'.$n.'}',$l,$s);
                if(strstr($s,'{'.$n.'?')) { $k="\\".($l?'1':'2'); $s=preg_replace("/\{".$n."\?([^|]*)\|(.*?)\|\}/s",$k,$s); }
        }
        return $s;
}



function get_comm_button($num,$dopload='',$kn=0) { global $comments_on_page,$podzamok,$comments_pagenum,$idzan,$idzan1;

	if(!isset($idzan)) $idzan=get_idzan($num);
	if(!isset($idzan1)) $idzan1=($idzan?get_idzan1($num):0);
	if(!$idzan) return ''; // комментов нету
 	$pages=($comments_on_page?ceil($idzan1/$comments_on_page)-1:0); // число страниц комментов
	if(!$pages && !$kn) return ''; // если всего 1 и это не кнопка подгрузки - выйти ни с чем

	// нарисовать кнопку (если страница всего 1) или фразу о количестве комментов
	$o=LL(($pages?'comm:nobutton':'comm:button'),array(
       	        'dopload'=>$dopload,'podzamok'=>$podzamok,'idzan'=>$idzan,
               	'majax'=>"onClick=\"majax('comment.php',{a:'loadcomments',dat:$num,page:0})\""
        ));

	// если страниц много - вывести кнопочки
	if($pages) for($i=0;$i<=$pages;$i++) $o .= LL('comm:k',array(
		'u'=>((isset($comments_pagenum)||!$kn) && $i==$comments_pagenum),
                'majax'=>"onClick=\"majax('comment.php',{a:'loadcomments',dat:$num,page:".($i)."})\"",
		'n'=>$i+1	));

	return $o;
}

function get_idzan($num) { return intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='$num'"
.($GLOBALS['podzamok']?'':" AND `scr`='0'"),'_l')); }

function get_idzan1($num) { return intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='$num' AND `Parent`='0'"
.($GLOBALS['podzamok']?'':" AND `scr`='0'"),'_l')); }


// ======================= zopt ==========================
$zopt_a=array(
        // 'comments_order'=>array('','normal rating allrating'),
        'include'=>array('','s',40),
        'Comment_foto_logo'=>array(chr(169)." ".chr(171)."{name}: ".$httpsite.chr(187),'s',64),
        'Comment_foto_x'=>array('600','s',6),
        'Comment_foto_q'=>array('75','s',6),
        'Comment_media'=>array('all','all no my'),
//$zopt_fotouser_x=600; // для фоток пользователей: максимальная ширина
//$zopt_fotouser_q=75; // для фоток пользователей: качество 72...95
//$zopt_fotouser_logo=chr(169)." ".chr(171)."{name}: ".$httpsite.chr(187); // подпись, {name} заменяется на имя
//        'Comment'=>array('enabled','enabled disabled allways_on screen normalscreen'),
        'Comment_view'=>array('on','on off rul load timeload'),
        'Comment_write'=>array('on','on off friends-only login-only timeoff login-only-timeoff'),
        'Comment_screen'=>array('open','open screen friends-open'),
        'Comment_tree'=>array('1','1 0'),
        'autoformat'=>array('p','no p pd'),
        'template'=>array('blog','s',32),
        'autokaw'=>array('auto','auto no')
);

foreach($zopt_a as $n=>$l) if(isset(${'zopt_'.$n})) $zopt_a[$n][0]=${'zopt_'.$n};

$admincolors=array(array('admin','ledred.png'),array('podzamok','ledyellow.png'),array('all','ledgreen.png'));
// ======================= zopt ==========================

function ADMINSET($p='') { if(!$GLOBALS['admin']) return '';
	if(gettype($p)!='array') $p=$GLOBALS['article'];
        foreach($GLOBALS['admincolors'] as $l) if($l[0]==$p['Access']) break;
        return "<img alt='".LL('Editor:dostup')."' onclick=\"majax('editor.php',{a:'ch_dostup',d:this.src,num:".$p['num']."})\" class='".$p['num']."_adostup' src='".$GLOBALS['www_design']."e3/".$l[1]."'>";
}

function mkzopt($p) { $o=unser($p['opt']); // сделать из $p массив опций и вернуть его
	foreach($GLOBALS['zopt_a'] as $n=>$l) { if(!isset($o[$n])) $o[$n]=$l[0]; }
	return array_merge($p,$o);
}

function makeopt($r,$i=0) { // создать массив opt из заданного массива и дефолта
	$opt=array(); foreach($GLOBALS['zopt_a'] as $n=>$l) { if(isset($r[$n]) && $r[$n]!='default') $opt[$n]=$r[$n]; 
	elseif($i) $opt[$n]=$l[0];
	} return $opt;
}

function unser($p){ return empty($p)?array():unserialize($p); }
function ser($p){ return sizeof($p)?serialize($p):''; }

// function getsite($s) { return ($s=ms("SELECT `text` FROM `site` WHERE `name`='".e($s)."'","_l"))!==false?$s:'{text}'; }

?>