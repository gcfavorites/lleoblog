<?php

//-- авторизаци€ админа
if(broident($admin_hash.$koldunstvo)!=$_COOKIE["adm"]) $admin=0; else {	$admin = 1;
	// включить сообщени€ об ошибках
        ini_set("display_errors","1");
        ini_set("display_startup_errors","1");
        ini_set('error_reporting', E_ALL);
        // error_reporting(E_ALL);
        // error_reporting = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR
        // error_reporting = E_ALL & ~E_USER_ERROR & ~E_USER_WARNING & ~E_USER_NOTICE
}

// if(!$admin) die("Ќа ремонте, извините.");

$podzamok = ($admin?true:false);

// ======================= ќпределение ∆∆-истов =========================================
$lju='';
if(isset($_COOKIE['lju'])) $lju=preg_replace("/[^0-9a-z\_\-]/si",'',base64_decode($_COOKIE['lju']));
elseif(isset($_SERVER["HTTP_REFERER"]) and strstr($_SERVER["HTTP_REFERER"],'/friends')) {
	if ( (preg_match("/\Ahttp\:\/\/(.+?)\.livejournal\.com\/friends/", $_SERVER["HTTP_REFERER"], $m)) ||
	     (preg_match("/\Ahttp\:\/\/users\.livejournal\.com\/(.+?)\/friends/", $_SERVER["HTTP_REFERER"], $m)) )
	{ $lju=preg_replace("/[^0-9a-z\_\-]/si","",$m[1]); setcookie("lju", base64_encode($lju), time()+86400*365, "/", "", 0); }
}
// ======================= ќпределение ∆∆-истов =========================================

$IP = $_SERVER["REMOTE_ADDR"]; $IPNUM=ip2ipn($IP);
$BRO = $_SERVER["HTTP_USER_AGENT"];
$MYPAGE=$_SERVER["REQUEST_URI"];
list($mypage) = explode('?',$MYPAGE.'?',2);
include $include_sys."_msq.php";
$uc='unic11';

if(isset($_COOKIE[$uc])) { // если кука $uc установлена
	$unic=$_COOKIE[$uc]; if($unic=='candidat') set_unic(); // был кандидатом, зашел второй раз? получи свой номер!
	else {
		list($unic,$unicpass) = explode('-',$unic,2); $unic=intval($unic); // прочитать куку авторизации
		if($unicpass!=md5($unic.$hashlogin)) set_unic_candidat(); // неверный пароль? странно. ну... назначим снова кандидатом.
		else { // авторизаци€ пройдена успешно
			$IS=getis($unic); $imgicourl=$IS['imgicourl'];
			if($admin && !empty($_GET['test'])) { // дл€ отладки
				$admin=0;
				$unic=intval($_GET['test']);
				$IS=ms("SELECT * FROM `unic` WHERE `id`='$unic'","_1"); // dier($IS);
				}
		}
	}
} else set_unic_candidat(); // куки пусты? выставить куку 'candidat', номер не давать, в базу не вносить

if(!isset($imgicourl) or $imgicourl=='') $imgicourl='#'.$unic;


function set_unic_candidat() { global $unic,$uc; $unic=0; setcookie($uc, 'candidat', time()+86400*365, "/", "", 0); }
function set_unic() { global $uc,$IPNUM,$unic,$hashlogin,$lju;
	$ara=array('ipn'=>$IPNUM,'lju'=>e($lju),'time_reg'=>time());


	if(msq_add('unic',$ara)===false) return false;
	$unic=mysql_insert_id(); if(!$unic) die('unic=0 '.$GLOBALS['msqe']);
	setcookie($uc, $unic.'-'.md5($unic.$hashlogin), time()+86400*365, "/", "", 0);
//	setcookie('obr','', time()-86400*365, "/", "", 0);
}

// какие заметки доступны?
$access=($admin?"":($podzamok?"`Access` IN ('all','podzamok')":"`Access`='all'"));
function WHERE($s='') { global $access; if($s.$access=='') return ''; if($s=='' || $access=='') return "WHERE ".$s.$access; return "WHERE ".$s." AND ".$access; }

// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// ==============================================================================
// www.livejournal.com/users/_nik_


function getis($unic) {
	$IS=ms("SELECT * FROM `unic` WHERE `id`='$unic'","_1");	// if($admin) dier($IS);
	if($IS) { $IS=array_merge($IS,get_ISi($IS));
		$IS['imgicourl']=h($IS['user']);
		if(isset($IS['url'])) $IS['imgicourl']="<a href='http://".h($IS['url'])."'>".$IS['imgicourl']."</a>";
		if(isset($IS['ico'])) $IS['imgicourl']="<img src='".h($IS['ico'])."'>".$IS['imgicourl'];
		if($IS['admin']=='podzamok' || $IS['admin']=='admin') $IS['imgicourl']=zamok('podzamok').$IS['imgicourl'];
		return $IS;
	}
}


function get_ISi($is) {

	if($is['obr']=='realname' and $is['realname']!='') return array('user'=>$is['realname']);

	if($is['obr']=='login' and $is['login']!='') return array(
			'url'=>$GLOBALS['blog_name']."/user/".$is['login'],
			'user'=>$is['login'],
//			'DOMAIN'=>$GLOBALS['blog_name'],
//			'ico'=>$GLOBALS['www_ico']."favicon.ico"
	);
	
	$log=$is['openid']; if($log=='') return array('user'=>'anonimouse');
	if(preg_match("/^([^\/]+).*\/([^\/]+)$/",$log,$l)) { $user=$l[2]; $dom=$l[1]; $root=$dom; }
	elseif (preg_match("/^([^\. ]+)\.(.*)$/",$log,$l)) { $user=$l[1]; $dom=$l[2]; $root=$log; }
	//else die($is);

	return array(
		'url'=>$log,
		'user'=>$user,
//		'DOMAIN'=>$dom,
		'ico'=>get_IS_IMGi($dom,$root) );
}

function get_IS_IMGi($dom,$root) { global $www_ico;
/*
a[href *=".livejournal.com"] {
a[href *="lleo.aha.ru"] {
a[href *=".blogspot.com"] {
a[href *=".moikrug.ru"] {
a[href *=".myopenid.com"] {
a[href *=".ya.ru"] {
*/
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

function broident($add) { return md5($_SERVER["HTTP_USER_AGENT"].$_SERVER["HTTP_ACCEPT"].$_SERVER["HTTP_ACCEPT_LANGUAGE"].$_SERVER["HTTP_ACCEPT_ENCODING"].$_SERVER["HTTP_ACCEPT_CHARSET"].$add); }

function dier($a) { idie(nl2br(h(print_r($a,1)))); } // отладочна€ процедурка

function idie($s) { 

// если это был а€кс - выдать а€кс-окно
if(!empty($GLOBALS['ajax'])) {
	list($u)=explode('?',$_SERVER['REQUEST_URI'],2);
	otprav("helps('idie',\"<fieldset><legend>Fatal error: ".h($u)."</legend><div style='font-size: 11px; text-align: left;'>".njs($s)."</div></fieldset>\");");
}

ob_end_clean(); die("<html><head>
\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$GLOBALS['wwwcharset']."\" />
\t<title>Error</title>
</head><body>".$s."</body></html>"); }

function mystart() {
	Error_Reporting(E_ALL & ~E_NOTICE);
	session_start();
	ob_start("onPostPage");
	header("Content-Type: text/html; charset=".$GLOBALS['wwwcharset']);
}

function onPostPage($buffer) { global $_PAGE,$_SCRIPT,$_SCRIPT_ADD,$_STYLE,$_STYLE_ADD;
        if(!isset($_PAGE) || $_PAGE['design']=='') return $buffer;

	$s = str_replace("{body}",$_PAGE['body'].$buffer,$_PAGE["design"]);

	// прописать скрипты
	$myscript=''; foreach($_SCRIPT as $n=>$l) $myscript.="\n\n// --- ".$n." ---\n".$l."\n// --- / ".$n." ---\n";
	if($myscript!='') $myscript="<script language='JavaScript'>\n".$myscript."\n</script>";
	foreach($_SCRIPT_ADD as $l) $myscript = "<script type='text/javascript' language='JavaScript' src='".$l."'></script>\n".$myscript;
	$s=str_replace("{myscript}", $myscript, $s);

	// прописать стили
	foreach($_STYLE_ADD as $l) {
		$l=str_replace('{www_css}',$GLOBALS['file_css'],$l);
		if(($e=file_get_contents($l))===false) return 'file not found: '.h($l);
		$e=preg_replace("/\/\*.*?\*\//si",'',$e); $_STYLE[$l]=$e; }

//return '<pre>'.h(print_r($_STYLE,1))."</pre>";

//	return "#<pre>"; //.print_r($_STYLE_ADD,1);

	$mystyle=''; foreach($_STYLE as $n=>$l) $mystyle.="\n\n/*** ".$n." ***/\n".$l."\n/*** / ".$n." ***/\n";
	if($mystyle!='') $mystyle="<style type='text/css'>\n".$mystyle."\n</style>";
//	foreach($_STYLE_ADD as $l) $mystyle="<link href='".$l."' rel='stylesheet' type='text/css' />\n".$mystyle;
	$s=str_replace("{mystyle}", $mystyle, $s);

	unset($_PAGE["design"]); unset($_PAGE["body"]);
//	$s=file_get_contents($GLOBALS['host_design'].$_PAGE['design']); unset($_PAGE["design"]); $_PAGE['body'] .= $buffer;
        foreach($_PAGE as $k=>$v) { $s = str_replace("{".$k."}", $v, $s); }



	return $s;
}

//	if(strstr($s,'{POST-')) $s=preg_replace_callback("/{POST-(.+?)}/s","ret_post",$s);
//	if(strstr($s,'{GET-')) $s=preg_replace_callback("/{POST-(.+?)}/s","ret_post",$s);
//function ret_post($t) { return htmlspecialchars($_POST[$t[1]]); }
//function ret_get($t) { return htmlspecialchars($_GET[$t[1]]); }

function redirect($path = "/") {
	if($GLOBALS['ajax']) otprav("window.location='$path'");
        if(!headers_sent()) {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$path,TRUE,301); // навсегда!
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
function selecto($n,$x,$a,$t='name') { $s="<select ".$t."='".$n."'>"; foreach($a as $l=>$t) $s.="<option value='$l'".($x==$l?' selected':'').">".$t; return $s."</select>"; }

function kawa($p) { $s=$p[1];
        $s=preg_replace("/([A-Za-z\x80-\xFF.,?!])\"/s","$1\xBB",$s); // "$1&raquo;"
        $s=preg_replace("/\"([A-Za-z\x80-\xFF.])/s","\xAB$1",$s); // "&laquo;$1"
        return $s;
}

function set_cookie($Name,$Value='',$MaxAge=0,$Path='',$Domain='',$Secure=false,$HTTPOnly=false) {

if(isset($GLOBALS['cookie_method_old'])) { setcookie($Name, $Value, $MaxAge, $Path, $Domain, 0); return; }

header('Set-Cookie: ' . rawurlencode($Name) . '=' . rawurlencode($Value)
.(empty($MaxAge) ? '' : '; Max-Age=' . $MaxAge)
.(empty($Path)   ? '' : '; path=' . $Path)
.(empty($Domain) ? '' : '; domain=' . $Domain)
.(!$Secure       ? '' : '; secure')
.(!$HTTPOnly     ? '' : '; HttpOnly'), false);
}

function file_get($f,$c=true) {	if(!$GLOBALS['cache_get']) return file_get_contents($f);
	$n=preg_replace("/[^0-9a-zA-Z_\-\.\~]+/si","#", str_replace("http://","",$f) );
	if(strlen($n)<200) $n=$GLOBALS['fileget_tmp'].$n.".dat"; else $n=$n=$GLOBALS['fileget_tmp'].md5($n).".dat";
	if(file_exists($n)) { if(!$c) return unlink($n); return file_get_contents($n); }
	$x=file_get_contents($f); file_put_contents($n,$x); chmod($n,0666); return $x;
}

function zamok($d) {
        if($d=='all') return '';
        $z = "<img src=".$GLOBALS['www_design']."e/podzamok.gif>&nbsp;";
        if($d=='podzamok') return $z;
        return $z.$z;
}

function h($s) { return htmlspecialchars($s); }
function ip2ipn($s){ $m=explode('.',$s,4); return $m[0]*16777216+$m[1]*65536+$m[2]*256+$m[3]; }
function ipn2ip($i){ $a=$i%256;$i=floor($i/256);$b=$i%256;$i=floor($i/256);$c=$i%256;$i=floor($i/256);$d=$i%256; return "$d.$c.$b.$a"; }

function mail_validate($s) { return
(preg_match("/^[0-9a-z_\.]+\@[0-9a-z\-\.]+\.[0-9a-z]+$/i", $s) ? $s : false);
//(preg_match("/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])*(\.([a-z0-9])([-a-z0-9_-])([a-z0-9])+)*$/i", $s) ? $s : false);
}

function site_validate($s) { return
(preg_match("/^([a-z]+:\/\/|(www\.))[a-z][a-z0-9_\.\-]*\.[a-z]{2,6}[\[a-zA-Z0-9!#\$\%\&\(\)\*\+,\-\.\/:;=\?\@]*$/i",$s)? $s : false);
// (preg_match("/^([a-zA-Z]+:\/\/|(www\.))([a-z][a-z0-9_\.\-]*[a-z]{2,6})([a-zA-Z0-9!#\$\%\&\(\)\*\+,\-\.\/:;=\?\@\[a-zA-Z0-9\/\.\&\%\;\=])([\s<,:\.\%\&\;\)\!\?\=0-9a-z])$/i",$s)? $s : false);
}


function get_link($Date) {
        list($y,$m,$d)=explode("/",substr($Date,0,10),3); if(intval($y)*intval($m)*intval($d))
        return $GLOBALS['httphost'].$Date.".html"; return $GLOBALS['httphost'].$Date;
}


$months = explode(" ", " €нварь февраль март апрель май июнь июль август сент€брь окт€брь но€брь декабрь");
$months_rod = explode(" ", " €нвар€ феврал€ марта апрел€ ма€ июн€ июл€ августа сент€бр€ окт€бр€ но€бр€ декабр€");

// ==================== куки ================================================================================================
$jog_scripts="
function c_rest(name) {

	var f=fc_read(name); var c=c_read(name);

	if(f != null && f != '') {
		c_save(name,f);
		fc_save(name,f); 

		var e=location.href;
		if(f!=c && name=='".$uc."' && e == e.replace(/i-snova-zdravstvyite/g,'') ) { location.href=e+'?i-snova-zdravstvyite'; }
	}

//	if( c != '' && c != 'candidat') {  } var c=c_read(name);
}

function c_save(name,v) { var N=new Date(); N.setTime(N.getTime()+(v==''?-1:3153600000000)); document.cookie=name+'='+v+';expires='+N.toGMTString()+';path=/;'; }
function c_read(name) { a=' '+document.cookie+';'; var c=a.indexOf(' '+name+'='); if(c==-1) return ''; a=a.substring(c+name.length+2); return a.substring(0,a.indexOf(';'))||''; }
function swf(a){ if(navigator.appName.indexOf('Microsoft') != -1) return window[a]; else return document[a]; }
function fc_read(name){ if(swf('kuki').flashcookie_read){ return swf('kuki').flashcookie_read(name); }}
function fc_save(name,v){ if(swf('kuki').flashcookie_save){ swf('kuki').flashcookie_save(name,v); }}
";

$jog_kuki="<div style='position: absolute;width:1px;height:1px;overflow:hidden;left:-40px;top:0;opacity:0'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' id='kuki' width='1' height='1' codebase='http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab' style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'><param name='movie' value='{www_design}kuki_ray.swf' /><embed src='{www_design}kuki_ray.swf' width='1' height='1' name='kuki' type='application/x-shockwave-flash' pluginspage='http://www.adobe.com/go/getflashplayer'></embed></object></div>";


function otprav($s) { global $_RESULT,$msqe; $_RESULT["modo"] = ($msqe?
"helps('mysql_error',\"<fieldset><legend>mysql_error</legend>"
.njs("<div style='font-size: 11px; text-align: left;'>".$msqe."</div>")."</fieldset>\");"
:$s); $_RESULT["status"] = true; exit; }
function njs($s) { return str_replace(array("\\","'",'"',"\n","\r"),array("\\\\","\\'",'\\"',"",""),$s); }
function njsn($s) { return str_replace(array("\\","'",'"',"\n","\r"),array("\\\\","\\'",'\\"',"\\n",""),$s); }
function oalert($s) { otprav("alert(\"".njs($s)."\")"); }

// SCRIPTS("jog_kuki",$jog_scripts." function setIsReady() { c_rest('".$uc."'); c_rest('lju'); }");
// ==================== куки ================================================================================================


function getmaketime($d) {
        if(!preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d)(.*?)$/s",$d,$m)) return array(0,0);
        $d=$m[1]."-".$m[2]."-".$m[3];
        $t0=strtotime($d);
        if(preg_match("/^[\-_\s]*(\d\d)-(\d\d)/s",$m[4],$t)) $d .= " ".$t[1].":".$t[2];
        $t=strtotime($d);
        while(msq_exist('dnevnik_zapisi',"WHERE `DateDatetime`='$t'")) $t++;
        return array($t0,$t);
}

?>
