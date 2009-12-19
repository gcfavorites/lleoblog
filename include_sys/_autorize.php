<?php
//-- авторизация админа
$admin = (broident($admin_hash.$koldunstvo)==$_COOKIE["adm"] ? 1:0 );
$login = false;
$podzamok = ($admin?true:false);

// if(!$admin) die("admin error");

//-- личный код посетителя
$sc=$_COOKIE["sc"]; if(!preg_match("/^[0-9abcdef]{32}$/",$sc)) { $sc=md5('жопа сраная'.time()); set_cookie("sc", $sc, time()+86400*365*5, "/", "", 0, true); }
// ======================= Определение ЖЖ-истов =========================================
$lju=preg_replace("/[^0-9a-z\_\-]/si",'',base64_decode($_COOKIE['lju']));
if (!$lju)
if ( (preg_match("/\Ahttp\:\/\/(.+?)\.livejournal\.com\/friends/", $_SERVER["HTTP_REFERER"], $m)) ||
     (preg_match("/\Ahttp\:\/\/users\.livejournal\.com\/(.+?)\/friends/", $_SERVER["HTTP_REFERER"], $m)) )
{ $lju=preg_replace("/[^0-9a-z\_\-]/si","",$m[1]); set_cookie("lju", base64_encode($lju), time()+86400*365, "/", "", 0, true); }
// ======================= Определение ЖЖ-истов =========================================

$IP = $_SERVER["REMOTE_ADDR"];
$BRO = $_SERVER["HTTP_USER_AGENT"];
$MYPAGE=$_SERVER["REQUEST_URI"];
list($mypage) = explode('?',$MYPAGE.'?');

$IPsc=mysql_escape_string($IP.'-'.substr($GLOBALS['sc'],0,4));

if($_COOKIE['pas']==broident($_COOKIE['log'].$hashlogin)) {
   $login=htmlspecialchars($_COOKIE['log']);

	ms_connect(); // соединиться с базой

   // mysql_query("UPDATE `$db_login` SET `count`=`count`+1 WHERE `login`='".mysql_escape_string($login)."'"); // посчитать

   $LOGIN=mysql_fetch_assoc(mysql_query(
"SELECT `mail`,`realname`,`admin`,`type`,`podpiska` FROM `$db_login` WHERE `login`='".mysql_escape_string($login)."'"));
	print mysql_error();

   if($LOGIN['admin']=='podzamok') $podzamok=true;
   $IS=get_IS($login); // foreach($IS as $n=>$l) ${"IS_$n"}=$l;
}


function get_IS($log) { global $blog_name;
	$log=preg_replace("/^www\./",'',$log);
	if(preg_match("/^([^\/]+).*\/([^\/]+)$/",$log,$l)) { $user0=$l[2]; $dom=$l[1]; $root=$dom; }
	elseif (preg_match("/^([^\. ]+)\.(.*)$/",$log,$l)) { $user0=$l[1]; $dom=$l[2]; $root=$log; }
	else { $user0=$log; $dom=$root=$blog_name; }
	$IS=array();
	$IS['USER']=$log;
	$IS['USER0']=$user0;
	$IS['DOMAIN']=$dom;
	$IS['IMG']=get_IS_IMG($dom,$log,$root);
	return $IS;
}


function get_IS_IMG($dom,$log,$root) { global $www_ico;
	if($dom=='lleo.aha.ru') return $www_ico."fav.ico";
	if($log=='lleo.aha.ru' || $log=='lleo') return $www_ico."favicon.ico";
	if($dom=='livejournal.com' || $dom=="users.livejournal.com") return $www_ico."lj.gif";
	if($dom=='myopenid.com') return $www_ico."myopenid.ico";
	if($dom=='blogspot.com') return $www_ico."blogspot.ico";
	if($dom=='openid.yandex.ru') return $www_ico."yandex.ico";
	if($dom=='ya.ru') return $www_ico."ya.ico";
	if($dom=='moikrug.ru') return $www_ico."moikrug.ico";
	return "http://google.com/s2/favicons?domain=".$root;
}

function broident($add) { return md5($_SERVER["HTTP_USER_AGENT"].$_SERVER["HTTP_ACCEPT"].$_SERVER["HTTP_ACCEPT_LANGUAGE"].$_SERVER["HTTP_ACCEPT_ENCODING"].$_SERVER["HTTP_ACCEPT_CHARSET"].$add); }

function idie($s) { ob_end_clean(); die("<html><head>
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
	if($myscript!='') $myscript="<script language=JavaScript>\n".$myscript."\n</script>";
	foreach($_SCRIPT_ADD as $l) $myscript = "<script type='text/javascript' language='JavaScript' src='".$l."'></script>\n".$myscript;
	$s=str_replace("{myscript}", $myscript, $s);

	// прописать стили
	foreach($_STYLE_ADD as $l) { $e=file_get_contents($l); $e=preg_replace("/\/\*.*?\*\//si",'',$e); $_STYLE[$l]=$e; }

//	return "#<pre>"; //.print_r($_STYLE_ADD,1);

	$mystyle=''; foreach($_STYLE as $n=>$l) $mystyle.="\n\n/*** ".$n." ***/\n".$l."\n/*** / ".$n." ***/\n";
	if($mystyle!='') $mystyle="<style type='text/css'>\n".$mystyle."\n</style>";
//	foreach($_STYLE_ADD as $l) $mystyle="<link href='".$l."' rel='stylesheet' type='text/css' />\n".$mystyle;
	$s=str_replace("{mystyle}", $mystyle, $s);

	unset($_PAGE["design"]); unset($_PAGE["body"]);
//	$s=file_get_contents($GLOBALS['host_design'].$_PAGE['design']); unset($_PAGE["design"]); $_PAGE['body'] .= $buffer;
        foreach($_PAGE as $k=>$v) { $s = str_replace("{".$k."}", $v, $s); }

	if(strstr($s,'{POST-')) $s=preg_replace_callback("/{POST-(.+?)}/s","ret_post",$s);
	if(strstr($s,'{GET-')) $s=preg_replace_callback("/{POST-(.+?)}/s","ret_post",$s);

	return $s;
}



function ret_post($t) { return htmlspecialchars($_POST[$t[1]]); }
function ret_get($t) { return htmlspecialchars($_GET[$t[1]]); }

// $_GET["$1"]

function redirect($path = "/") {
        if (!headers_sent()) {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$path,TRUE,301); // навсегда!
                exit();
        } else {
                print "
<noscript>
\t<meta http-equiv=refresh content=\"0;url=\"".$path."\">
</noscript>
<script>
\t// Redirect
\tlocation.replace(\"".$path."\")
</script>
";
        }
}

function logi($f,$s,$a="a+") { $l=fopen($GLOBALS["host_log"].$f,$a); fputs($l,$s); fclose($l); }
function add_get() { if(sizeof($_GET)==0) return ''; $s='?';foreach($_GET as $a=>$b) if($b!='') $s.="$a=".urlencode($b)."&"; return trim($s,'&'); }
function page($l,$c=50) { $m=split("\n",$l); $i=0; foreach($m as $t) if(strlen($t)<$c) $i++; else $i=$i+1+(floor(strlen($t)/$c)); return($i); }
function uw($s) { return(iconv("utf-8","windows-1251//IGNORE",$s)); }
function wu($s) { return(iconv("windows-1251","utf-8//IGNORE",$s)); }
function kw($s) { return(iconv("koi8-r","windows-1251//IGNORE",$s)); }
function wk($s) { return(iconv("windows-1251","koi8-r//IGNORE",$s)); }
function selecto($n,$x,$a,$t='name') { $s="<select ".$t."='".$n."'>"; foreach($a as $l=>$t) $s.="<option value='$l'".($x==$l?' selected':'').">".$t; return $s."</select>"; }

function kawa($p) { $s=$p[1];
        $s=preg_replace("/([A-Za-z\x80-\xFF.,?!])\"/s","$1\xBB",$s); // "$1&raquo;"
        $s=preg_replace("/\"([A-Za-z\x80-\xFF.])/s","\xAB$1",$s); // "&laquo;$1"
        return $s;
}

function set_cookie($Name,$Value='',$MaxAge=0,$Path='',$Domain='',$Secure=false,$HTTPOnly=false) {

if($GLOBALS['cookie_method_old']) { setcookie($Name, $Value, $MaxAge, $Path, $Domain, 0); return; }

header('Set-Cookie: ' . rawurlencode($Name) . '=' . rawurlencode($Value)
.(empty($MaxAge) ? '' : '; Max-Age=' . $MaxAge)
.(empty($Path)   ? '' : '; path=' . $Path)
.(empty($Domain) ? '' : '; domain=' . $Domain)
.(!$Secure       ? '' : '; secure')
.(!$HTTPOnly     ? '' : '; HttpOnly'), false);
}


function ms_connect() { if(isset($GLOBALS['ms_connected'])) return;

   mysql_connect($GLOBALS['msq_host'], $GLOBALS['msq_login'], $GLOBALS['msq_pass']) or idie("<p>Ошибка соединения с MySQL!
Исправьте в config.php строки:<ul> \$msq_host = '".$GLOBALS['msq_host']."';
<br>\$msq_login = '".$GLOBALS['msq_login']."';
<br>\$msq_pass = [...]
</ul>");
   mysql_select_db($GLOBALS['msq_basa']) or idie("<p>Хорошие новости! Во-первых, движок поднялся. Что уже чудо. Во-вторых, что еще
чудеснее, обнаружен MySQL и с ним установлено успешное соединение!
Теперь плохая новость: отсутствует база&nbsp;<b>`".$GLOBALS['msq_basa']."`</b>. Это не проблема, подойдет любая другая, лишь бы движку
было где создать свои таблицы. Если есть какая-то база, ее имя надо вписать в config.php, где сейчас: <b>\$msq_basa = '".$GLOBALS['msq_basa']."';</b>");

   mysql_query("SET NAMES ".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_client=".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_results=".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_connection=".$GLOBALS['msq_charset']);

	$GLOBALS['ms_connected']=true;
}

// /bhome/part1/01/lleo/www/bachilo/tmp/get/blogs.yandex.ru#search.xml#post#http#3A#2F#2Fbachilo.livejournal.com#2F280851.html#3C#2Fa#3E#3Cbr#3E#3Ca#href#3Dhttp#3A#2F#2Fblogs.yandex.ru#2Fsearch.xml#3Fpost#3Dhttp#253A#252F#252Fbachilo.livejournal.com#252F280851.html#26amp#3Bft#3Dcomments#3E#EA#EE#EC#EC#E5#ED#F2#E0#F0#E8#E5#E2#3A#9#ft#comments#rd#0#spcctx#doc#full#1#numdoc#100#p#0.dat
function file_get($f) {	//$n=$GLOBALS['fileget_tmp'].md5($f).".dat";
	$n=preg_replace("/[^0-9a-zA-Z_\-\.\~]+/si","#", str_replace("http://","",$f) );
	if(strlen($n)<200) $n=$GLOBALS['fileget_tmp'].$n.".dat"; else $n=$n=$GLOBALS['fileget_tmp'].md5($n).".dat";
	if(file_exists($n)) return file_get_contents($n);
	$x=file_get_contents($f); file_put_contents($n,$x); return $x;
}

function dier($a) { die('<pre>'.htmlspecialchars(print_r($a,1))); } // отладочная процедурка

?>
