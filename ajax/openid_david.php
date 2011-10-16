<?php // ������

// die('eeeeeeeeeeeeeeeee');

// define("THIS_URL",$GLOBALS['httpsite'].$GLOBALS['mypage']);

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


// $o="<blockquote>";

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

/*
$o.="
<script> openid_ifr_post=function(){ ohelpc('opid','OpenID <img src=\"+www_design+\"img/ajax.gif>',\"<iframe name='openid_ifr' width='\"+(getWinW()-200)+\"' height='\"+(getWinH()-200)+\"'></iframe>\"); ajaxon(); posdiv('ajaxgif',-1,-1); }; </script>

<form method='POST' action='".THIS_URL."' target='openid_ifr'>
��� Openid: <input type='text' name='openid_url' size='60'><input type='submit' value='Go!' onclick='openid_ifr_post()'>
<br>�����: <select name='mode'>
            <option value='1'>checkid_setup (����������������)</option>
            <option value='0'>checkid_immediate (����������������)</option>
        </select>
</form></blockquote>

";
*/

die($o);
?>