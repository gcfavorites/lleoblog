<?php // ������

	include "../config.php";
	include $include_sys."_autorize.php";

ini_set("display_errors","1"); ini_set("display_startup_errors","1"); ini_set('error_reporting', E_ALL); // ����

// define("THIS_URL",$GLOBALS['httpsite'].$GLOBALS['mypage']);


function dier1($a) {
	$e=explode("\n",print_r($a,1));
	$o=array(); foreach($e as $l) { if(str_replace(array("\r","\n","\t"," ","(",")","Array"),'',$l)!='') $o[]=h($l); }
	return implode("\n",$o);
}

if(isset($_POST['token'])) {
	$s=file_get_contents("http://loginza.ru/api/authinfo?token=".$_POST['token']);
// $s='{"identity":"http:\/\/www.facebook.com\/profile.php?id=100001073866092","provider":"http:\/\/www.facebook.com\/","uid":"100001073866092","name":{"full_name":"\u041b\u0435\u043e\u043d\u0438\u0434 \u041a\u0430\u0433\u0430\u043d\u043e\u0432","first_name":"\u041b\u0435\u043e\u043d\u0438\u0434","last_name":"\u041a\u0430\u0433\u0430\u043d\u043e\u0432"},"dob":"1972-21-05","email":"lleo@aha.ru","web":{"default":"http:\/\/lleo.aha.ru"},"photo":"https:\/\/graph.facebook.com\/100001073866092\/picture"}';

	include $include_sys."json.php";
	//include_once "../include_sys/json.php";

	$j=jsonDecode($s);

	logi('openid_david.txt',"\n\n\n".dier1($j));


$mail=(empty($j['email'])?'':$j['email']);
$img=(empty($j['photo'])?'':$j['photo']);
if(!empty($j['dob'])) {	$dob=strtotime($j['dob']); if($dob) $dob=date("Y-m-d",$dob); } else $dob='';
$site=(empty($j['web']['default'])?'':$j['web']['default']);
$info=trim((empty($j['identity'])?'':$j['identity']),'/');
$dom=preg_replace("/^.*?([^\.]+\.[^\.]+)$/s","$1",preg_replace("/www\./si",'',parse_url($info,PHP_URL_HOST)));

if(!empty($j['name']['full_name'])) $name=$j['name']['full_name'];
elseif(!empty($j['nickname'])) $name=$j['nickname'];
elseif(!empty($j['name']['first_name']) && isset($j['name']['last_name'])) $name=$j['name']['first_name'].' '.$j['name']['last_name'];
elseif(!empty($info)) { 
	$name=trim(parse_url($info,PHP_URL_PATH),'/');
	if($name=='') $name=preg_replace("/^(.*)\.[^\.]+\.[^\.]+$/s","$1",preg_replace("/www\./si",'',parse_url($info,PHP_URL_HOST)));
} else $name='###';


	$ll=login_do();


	die("<font color=green>success</font><p><pre>".dier1($j)."</pre>".$ll);
}

define("THIS_URL",'http://lleo.me/blog/ajax/openid_david.php');




/**** ������ ������� **************/

function openid_error($message) { $_SESSION["OpenID"]["status"] = $message; header("Location: ".THIS_URL); exit; }

// �������� �� ���� ������ ���� key=val&...
function makeQueryString($p) { $s=''; foreach($p as $k=>$v) $s.="&".urlencode($k)."=".urlencode($v); return substr($s,1); }

// ������������� � $url ������ ���� key=val&..., ��������� �� ���� $params
function appendParams($url,$p) {
	$s=makeQueryString($p); if(strpos($url,"?") !== false) return $url."&".$s; return $url."?".$s;
}

// �������, �������� � htmlspecialchars
function unh($arg) {
   if(is_string($arg))
	return preg_replace_callback('/&(amp|lt|gt|quot|apos|#(\d+)|#[xX]([a-fA-F\d]+));/', __FUNCTION__, $arg);

    $entities = array("amp" => "&", "lt" => "<", "gt" => ">", "quot" => "\"", "apos" => "'");
    if(isset($entities[$arg[1]])) return $entities[$arg[1]];
    // ������������ ������ ������� � ������ ������ 256
    if($arg[2]) $code = (int)$arg[2] & 0xFF;
    if($arg[3]) $code = hexdec($arg[3]) & 0xFF;
    if($code) return chr($code);
    return "?";
}

/************************************/

// ��� �������� ������� ������
session_start();


// �����, �� ������� �������� ���� POST ��. � ����� �������
if($_POST) {

    $openidUrl = trim($_POST["openid_url"]);
    $checkMode = (int)$_POST["mode"] ? "checkid_setup" : "checkid_immediate";

    // ������ ����� ����� ������� �� ����������� ��� ���� � �������� ����� ������� (�, ���� �����, delegate-���)

    if(!$openidUrl) openid_error("������ url");
    // ���� �����, ���������� ��������...
    if(!preg_match('{^[a-z]+://}i', $openidUrl)) $openidUrl = "http://".$openidUrl;
    // ...� ��������� ����
	$openidUrl = rtrim($openidUrl,'/')."/";
    // ��������� ������ HTTP � HTTPS
    if(!preg_match('{^https?://}i', $openidUrl)) openid_error("������������ url");
    // �������� �������� �� ����� ������
    // �����������, ���� ��� CURL-�, �� ����� ������������ ����� ������ ��������

	$body=file_get_contents($openidUrl);

    if(!$body) openid_error("�� ������� �������� �������� ".h($openidUrl));

    // ��� ���������� ��� LINK-��������: openid.server � openid.delegate
    // ��������� ���� � ��-��������
    $serviceUrl = "";
    $delegateId = "";

    $body = preg_replace('/<body\b.*/i', '', $body);

    if(preg_match('/<link\b[^>]+?\brel=([\'"])openid\.server\1.*?>/i', $body, $m)) { $link=$m[0];
        if(preg_match('/\bhref=([\'"])(.*?)\1/i', $link, $m)) $serviceUrl=unh($m[2]);
    }
    if(!$serviceUrl) openid_error("openid.server �� ������");
    if(!preg_match('{^https?://}i',$serviceUrl)) openid_error("������������ url openid.server");

    if(preg_match('/<link\b[^>]+?\brel=([\'"])openid\.delegate\1.*?>/i', $body, $m)) { $link=$m[0];
        if(preg_match('/\bhref=([\'"])(.*?)\1/i', $link, $m)) $delegateId = unh($m[2]);
    }
    if(!$delegateId) $delegateId=$openidUrl;
    
    // ������������ openid.delegate �� ��������� -- ��� �� ������� ������ ��������

    $_SESSION["OpenID"] = array(
        "serviceUrl"    => $serviceUrl,
        "openidUrl"     => $openidUrl,
    );

    // �������� ����� �������
    // ���������� ������...

    $params = array(
        "openid.mode"       => $checkMode,
        "openid.identity"   => $delegateId,
        "openid.return_to"  => THIS_URL."?return",
        "openid.trust_root" => THIS_URL,
		"openid.sreg.required" => 'email,fullname',
		"openid.sreg.optional" => 'dob,gender,postcode,country,language,timezone'
    );

    // ��, ������������ ������� �� $serviceUrl � ������� �����������
    // ����� ��� ������� ����� �� ������ THIS_URL."?return"

    header("Location: ".appendParams($serviceUrl, $params));
    exit;
}



if(isset($_GET["return"]) and $_SESSION["OpenID"]) {

    // �� ��������� � ������-�������
    // ���������, � ����� �����������...

    // ������, GET-��������� ����� ��� openid.something, �� PHP �� ��������������� � openid_something
    // ��� ��� �� �����������, ��� ��������� ���������� �� ������ ���, ��� � ������������

    if($_GET["openid_mode"] == "cancel") { // ���� ��� �������� �������� ��� ���������� � ����. ����� �����
        $_SESSION["OpenID"]["status"] = "� ���� �� �������� �������������:(";

    } elseif($_GET["openid_mode"] == "error") { // ��������� �����-�� ������. ���� �� � ���� ���� ����, �� ��� �� �����...
        $_SESSION["OpenID"]["status"] = "��������� ��������� ������: ".h($_GET["openid_error"]);

    } elseif($_GET["openid_mode"] == "id_res") { // �����-�� ��������� ��-���� �������

        if(isset($_GET["openid_user_setup_url"])) {
            // ���-�� ����� �� ���
            // ���� � ��� ���� ��� ���� �� ���� ���
            // ���� (��� ����� ��������� ������ � ������ checkid_immediate)
            // � ������� ��� ���������� �������� ��� ���������� �������������, 
            // � �� ��� URL, �� �������� ������� ������� �����, ����� ��� ��� ��� �� ��������
            // (� ������ checkid_setup ���� ��� �������� �� ���� ���)

            $setupUrl = $_GET["openid_user_setup_url"];
            // ������� �������� openid.post_grant=return, ����� ��� ������������� ������� ������� ����� ����������
            $setupUrl = appendParams($setupUrl, array("openid.post_grant" => "return"));
            $_SESSION["OpenID"]["status"] = "�������������� ������������� �� �������. ��� ����� ������������ �� ������ <a href=\"".h($setupUrl)."\">".h(substr($setupUrl,0,50))."...</a> � ������������ � ���, ��� �� �����, � ��� ���.";
            $_SESSION["OpenID"]["dontclear"] = true;
        } else {
            // �������, �� � �������
            // �� ��� ������ ����������� ���� ��������� �������
            // �.�. �� ������, �� ������� ��� ������ �������
            $params = array(
                "openid.mode"           => "check_authentication",
                "openid.assoc_handle"   => $_GET["openid_assoc_handle"],
                "openid.sig"            => $_GET["openid_sig"],
                "openid.signed"         => $_GET["openid_signed"],
            );
            foreach($_GET as $k=>$v) { if(strpos($k,'openid_')!==0) continue;
                $k=str_replace('openid_','openid.',$k); if(!isset($params[$k])) $params[$k]=$v;
            }

            // ���� ������� POST-������ � ����� �����������
            // �����������, ���� ��� CURL-�, �� ����� ������������ ����� ������ ��������
            $ch = curl_init();   
            curl_setopt($ch, CURLOPT_URL, $_SESSION["OpenID"]["serviceUrl"]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, makeQueryString($params));
            $result = curl_exec($ch);
            curl_close($ch);

            // ����� ����� � ���� ����� ���� key:val\n
            // ��������� ���...
            $vals = array();
            foreach(preg_split('/\n/', $result, -1, PREG_SPLIT_NO_EMPTY) as $pair) {
                list($k, $v) = explode(":", $pair, 2);
                $vals[$k] = $v;
            }

            // ��� ���������� ������ ���� �������� ������ ? lifetime
            if(
                isset($vals["lifetime"]) and (int)$vals["lifetime"] > 0 or
                isset($vals["is_valid"]) and $vals["is_valid"] == "true"
            ) {
		//openid_ok($_SESSION["OpenID"]["openidUrl"],$params);
                $_SESSION["OpenID"]["status"] = "�����������, �� � � ����� ���� <b>".h($_SESSION["OpenID"]["openidUrl"])."</b>!";
                $_SESSION["OpenID"]["parms"]=$params;
            } else {
                $_SESSION["OpenID"]["status"] = "���� �������� ������� ��������, ���� <b>".h($_SESSION["OpenID"]["openidUrl"])."</b> - ��� �� ��� ���.";
            }
        }
    }

    header("Location: ".THIS_URL);
    exit;
}


$o='';

if(!empty($_SESSION["OpenID"]["status"])) {
if(empty($_SESSION["OpenID"]["parms"])) $o.="<font color=red>".$_SESSION["OpenID"]["status"]."</font><p>&nbsp;<p>";
else {
	$j=$_SESSION["OpenID"]["parms"];
	$info=trim($_SESSION["OpenID"]["openidUrl"],'/');
	logi('openid_david.txt',"\n\n\n".dier1(array_merge(array('openid'=>$info),$j)));

	$s=$_SESSION["OpenID"]["status"];


//	$dom=preg_replace("/^.*?([^\.]+\.[^\.]+)$/s","$1",preg_replace("/www\./si",'',parse_url($info,PHP_URL_HOST)));
	$name=trim(parse_url($info,PHP_URL_PATH),'/');
	if($name=='') $name=preg_replace("/^(.*)\.[^\.]+\.[^\.]+$/s","$1",preg_replace("/www\./si",'',parse_url($info,PHP_URL_HOST)));

	$ll=login_do();

//!isset($_SESSION["OpenID"]["dontclear"])&&!$_SESSION["OpenID"]["dontclear"]) 
	$_SESSION["OpenID"]=null;
	//else $_SESSION["OpenID"]["dontclear"]=false;

	die("<p style='color: #900'>info: <b>".h($info)."</b><br>dom: <b>".h($dom)."</b><br>name: <b>".h($name)."</b><p>".$s."</p><pre>".dier1($j)."</pre>".$ll);
}}

//<script> openid_ifr_post=function(){ ohelpc('opid','OpenID <img src=\"+www_design+\"img/ajax.gif>',\"<iframe name='openid_ifr' width='\"+(getWinW()-200)+\"' height='\"+(getWinH()-200)+\"'></iframe>\"); ajaxon(); posdiv('ajaxgif',-1,-1); }; </script>


die($o."
��� ������� ������������:

<form method='POST' action='".THIS_URL."'>
<p>&nbsp;<p>&nbsp;<p>1. OpenID: <input type='text' name='openid_url' value='lleo.me' size='40'>
mode: <select name='mode'><option value='1'>auto</option><option value='0'>imm</option></select>
<input type='submit' value='LOGIN' onclick='openid_ifr_post()'>
</form>

<script src='http://loginza.ru/js/widget.js' type='text/javascript'></script>

<p>&nbsp;<p>&nbsp;<p>2. <a href='https://loginza.ru/api/widget?token_url=".urlencode(THIS_URL)."' class='loginza'>"
."� ������� LOGINZA (Facebook,Google,Mail.ru � �.�.)</a>

");

// if(!function_exists('h')) { function h($s) { return htmlspecialchars($s); } }

/*
function openid_ok($openid,$j) {
	logi('openid_david.txt',"\n\n".dier1($j));
	if(!$_SESSION["OpenID"]["dontclear"]) $_SESSION["OpenID"]=null;	else 
	$_SESSION["OpenID"]["dontclear"]=false;
	die("<p style='color: #900'>".$openid."</p><pre>".dier1($j)."</pre>");
}
*/

function login_do() { global $img,$dom,$dob,$name,$info,$site,$mail;
$x="\n\n<!-- $info --><table style='width: 80%; border: 1px dashed rgb(255,0,0); padding: 20px; margin-left: 50px; margin-right: 50px; background-color: rgb(255,252,223);'><tr><td>"
.(empty($img)?'':"<img src='".h($img)."' align='right' hspace='20'>")
."<img src='http://".h($dom)."/favicon.ico'><b>".h($name).(empty($dom)?'':" / ".h($dom))."</b>"
."\ninfo: <a href='".h($info)."'>".h($info)."</a>"
//.(empty($mail)?'':"\nmail: <a href='mailto:".h($mail)."'>".h($mail)."</a>")
.(empty($mail)?'':"\nmail: <i>�������</i>")
.(empty($site)?'':"\nsite: <a href='".h($site)."'>".h($site)."</a>")
.(empty($dob)?'':"\nbirth: ".h($dob))
."</td></tr></table>";

$d=ms("SELECT `Body` FROM `dnevnik_zapisi` WHERE `Date`='2011/11/01'","_l",0);

if(!strstr($d,"<!-- $info -->")) {
	$d=str_replace("<a name='tut'></a>","<a name='tut'></a>".$x,$d);
	msq_update('dnevnik_zapisi',array('Body'=>e($d)),"WHERE `Date`='2011/11/01'");
}

return "<script>this.parent.salert('<font color=green>success</font>',500);this.parent.location.href='http://lleo.me/blog/2011/11/01.html?random='+Math.random(0,20000)+'#tut';</script>";
}

?>