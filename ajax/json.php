<?php // ������
ini_set("display_errors","1"); ini_set("display_startup_errors","1"); ini_set('error_reporting', E_ALL); // ����

/*
function uXXX($t) {
return "|".iconv("utf-8","windows-1251//IGNORE",chr("0x".$t[1]))."|";
$s=chr(base_convert(substr($t[1],0,2),16,10)).chr(base_convert(substr($t[1],2),16,10));
return iconv("utf-8","windows-1251//IGNORE","|$s|");
return iconv("utf-8","windows-1251//IGNORE",chr(base_convert($t[1],16,10)));
}
*/

<?php
 function jdecoder($json_str) {
     $cyr_chars = array (
         '\u0430' => '�', '\u0410' => '�',
         '\u0431' => '�', '\u0411' => '�',
         '\u0432' => '�', '\u0412' => '�',
         '\u0433' => '�', '\u0413' => '�',
         '\u0434' => '�', '\u0414' => '�',
         '\u0435' => '�', '\u0415' => '�',
         '\u0451' => '�', '\u0401' => '�',
         '\u0436' => '�', '\u0416' => '�',
         '\u0437' => '�', '\u0417' => '�',
         '\u0438' => '�', '\u0418' => '�',
         '\u0439' => '�', '\u0419' => '�',
         '\u043a' => '�', '\u041a' => '�',
         '\u043b' => '�', '\u041b' => '�',
         '\u043c' => '�', '\u041c' => '�',
         '\u043d' => '�', '\u041d' => '�',
         '\u043e' => '�', '\u041e' => '�',
         '\u043f' => '�', '\u041f' => '�',
         '\u0440' => '�', '\u0420' => '�',
         '\u0441' => '�', '\u0421' => '�',
         '\u0442' => '�', '\u0422' => '�',
         '\u0443' => '�', '\u0423' => '�',
         '\u0444' => '�', '\u0424' => '�',
         '\u0445' => '�', '\u0425' => '�',
         '\u0446' => '�', '\u0426' => '�',
         '\u0447' => '�', '\u0427' => '�',
         '\u0448' => '�', '\u0428' => '�',
         '\u0449' => '�', '\u0429' => '�',
         '\u044a' => '�', '\u042a' => '�',
         '\u044b' => '�', '\u042b' => '�',
         '\u044c' => '�', '\u042c' => '�',
         '\u044d' => '�', '\u042d' => '�',
         '\u044e' => '�', '\u042e' => '�',
         '\u044f' => '�', '\u042f' => '�',
  
         '\r' => '',
         '\n' => '<br />',
         '\t' => ''
     );
  
     foreach ($cyr_chars as $key => $value) {
         $json_str = str_replace($key, $value, $json_str);
     }
     return $json_str;
 }
  
 echo jdecoder("\u0412\u044b \u043d\u0435 \u043c\u043e\u0436\u0435\u0442\u0435 \u043f\u0440\u0438\u043a\u0440\u0435\u043f\u0438\u0442\u044c \u0444\u0430\u0439\u043b \u0434\u0430\u043d\u043d\u043e\u0433\u043e \u0442\u0438\u043f\u0430"); 
?> 

function jsonDecode($json) {
	$json = str_replace('\\/','/',$json);

//	$json=preg_replace_callback("/\\\\u([0-9a-f]{4})/si","uXXX",$json);
//require_once "../include_sys/JsHttpRequest.php";
//    $json=str_replace('\\u','%u',$json);
//	$json=_ucs2EntitiesDecode($json);

      $json = str_replace(array("\\\\", "\\\""), array("&#92;", "&#34;"), $json);
      $parts = preg_split("@(\"[^\"]*\")|([\[\]\{\},:])|\s@is", $json, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
      foreach($parts as $index => $part) {
          if(strlen($part) == 1) {
              switch ($part) {
                  case "[": case "{": $parts[$index] = "array("; break;
                  case "]": case "}": $parts[$index] = ")"; break;
                  case ":": $parts[$index] = "=>"; break;   
                  case ",": break;
                  default: return null;
              }
          }
          else if((substr($part,0,1) != '"') || (substr($part,-1,1) != '"')) return null;
      }
      $json = str_replace(array("&#92;", "&#34;", "$"), array("\\\\", "\\\"", "\\$"), implode("", $parts));
      return eval("return $json;");
  }


// error_reporting(E_ALL);
// error_reporting = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR
// error_reporting = E_ALL & ~E_USER_ERROR & ~E_USER_WARNING & ~E_USER_NOTICE

//die('eeeeeeeeeeeeeeeee');

//$o="<pre>".print_r($_GET,1)."</pre>";


//successArray ( [token] => 6fed69864d5575f0edf0a71d6ddc0d06 )

//$xx='{"identity":"https:\/\/www.google.com\/accounts\/o8\/id?id=AItOawl-5JFpsxQqspiHpVmMdogLsfNpff8BPoo","provider":"https:\/\/www.google.com\/accounts\/o8\/ud","language":"ru","email":"lleo.kaganov@gmail.com","name":{"last_name":"Kaganov","first_name":"Leonid","full_name":"Leonid Kaganov"},"address":{"home":{"country":"RU"}},"uid":"103926980431102214659"}';


// define("THIS_URL",$GLOBALS['httpsite'].$GLOBALS['mypage']);

if(1||isset($_POST['token'])) {

//-------------------------------------------------

//	$s=file_get_contents("http://loginza.ru/api/authinfo?token=".$_POST['token']);
$s='{"identity":"http:\/\/www.facebook.com\/profile.php?id=100001073866092","provider":"http:\/\/www.facebook.com\/","uid":"100001073866092","name":{"full_name":"\u041b\u0435\u043e\u043d\u0438\u0434 \u041a\u0430\u0433\u0430\u043d\u043e\u0432","first_name":"\u041b\u0435\u043e\u043d\u0438\u0434","last_name":"\u041a\u0430\u0433\u0430\u043d\u043e\u0432"},"dob":"1972-21-05","email":"lleo@aha.ru","web":{"default":"http:\/\/lleo.aha.ru"},"photo":"https:\/\/graph.facebook.com\/100001073866092\/picture"}';
	$j=jsonDecode($s);

	die('<font color=green>success</font>'.print_r($_POST,1)
."<p>".$s."<hr><pre>".print_r($j,1)."</pre>
<script>this.parent.salert('<font color=green>success</font>',500);</script>
"
);
}

define("THIS_URL",'http://lleo.me/blog/ajax/openid_david.php');

function h($s) { return htmlspecialchars($s); }


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



// if(isset($_GET["loginza"])) { die('###'.$_GET["loginza"]); } //else die(print_r($_GET,1));


// $o="<blockquote>";

$o="<pre>".print_r($_GET,1)."</pre>";



if($_SESSION["OpenID"]["status"]) {

$k="\n\n".$_SESSION["OpenID"]["openidUrl"].": ".$_SESSION["OpenID"]["status"];

	$o.="<p style='color: #900'>".$_SESSION["OpenID"]["status"]."</p>";
$o.="<p><center><table border=1 cellspacing=0 cellpadding=0>";
foreach($_SESSION["OpenID"]["parms"] as $n=>$l) { $o.="<tr><td>".$n."&nbsp;</td><td>&nbsp;".$l."&nbsp;</td></tr>"; $k.="\n\t\t\t".$n."=`".$l."`"; }
$o.="</table></center><p><br>";

// logi('openid_test.txt',$k);

        if(!$_SESSION["OpenID"]["dontclear"]) $_SESSION["OpenID"]=null;
	else $_SESSION["OpenID"]["dontclear"]=false;
}


//<script> openid_ifr_post=function(){ ohelpc('opid','OpenID <img src=\"+www_design+\"img/ajax.gif>',\"<iframe name='openid_ifr' width='\"+(getWinW()-200)+\"' height='\"+(getWinH()-200)+\"'></iframe>\"); ajaxon(); posdiv('ajaxgif',-1,-1); }; </script>

$o.="

<form method='POST' action='".THIS_URL."'>
��� Openid: <input type='text' name='openid_url' value='lleo.me' size='60'><input type='submit' value='Go!' onclick='openid_ifr_post()'>
<br>�����: <select name='mode'>
            <option value='1'>checkid_setup (����������������)</option>
            <option value='0'>checkid_immediate (����������������)</option>
        </select>
</form></blockquote>

";

die($o
.'<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
<a href="https://loginza.ru/api/widget?token_url='.urlencode(THIS_URL)
.'" class="loginza">��������� ����� loginza</a>'
);
?>