<?php // Опенид

// die('eeeeeeeeeeeeeeeee');

// define("THIS_URL",$GLOBALS['httpsite'].$GLOBALS['mypage']);

define("THIS_URL",'http://lleo.me/blog/ajax/openid_david.php');

function h($s) { return htmlspecialchars($s); }

/**** РАЗНЫЕ ФУНКЦИИ **************/

function openid_error($message) { $_SESSION["OpenID"]["status"] = $message; header("Location: ".THIS_URL); exit; }

// собирает из хэша строку типа key=val&...
function makeQueryString($p) { $s=''; foreach($p as $k=>$v) $s.="&".urlencode($k)."=".urlencode($v); return substr($s,1); }

// присобачивает к $url строку типа key=val&..., собранную из хэша $params
function appendParams($url,$p) {
	$s=makeQueryString($p); if(strpos($url,"?") !== false) return $url."&".$s; return $url."?".$s;
}

// функция, обратная к htmlspecialchars
function unh($arg) {
   if(is_string($arg))
	return preg_replace_callback('/&(amp|lt|gt|quot|apos|#(\d+)|#[xX]([a-fA-F\d]+));/', __FUNCTION__, $arg);

    $entities = array("amp" => "&", "lt" => "<", "gt" => ">", "quot" => "\"", "apos" => "'");
    if(isset($entities[$arg[1]])) return $entities[$arg[1]];
    // обрабатываем только символы с кодами меньше 256
    if($arg[2]) $code = (int)$arg[2] & 0xFF;
    if($arg[3]) $code = hexdec($arg[3]) & 0xFF;
    if($code) return chr($code);
    return "?";
}

/************************************/

// для удобства заводим сессию
session_start();


// форму, из которой приходит этот POST см. в конце скрипта
if($_POST) {

    $openidUrl = trim($_POST["openid_url"]);
    $checkMode = (int)$_POST["mode"] ? "checkid_setup" : "checkid_immediate";

    // первым делом нужно сходить по подсунутому нам урлу и вытащить адрес сервиса (и, если нужно, delegate-урл)

    if(!$openidUrl) openid_error("пустой url");
    // если нужно, дописываем протокол...
    if(!preg_match('{^[a-z]+://}i', $openidUrl)) $openidUrl = "http://".$openidUrl;
    // ...и финальный слэш
	$openidUrl = rtrim($openidUrl,'/')."/";
    // разрешаем только HTTP и HTTPS
    if(!preg_match('{^https?://}i', $openidUrl)) openid_error("некорректный url");
    // получаем страницу по этому адресу
    // естественно, если нет CURL-а, то можно использовать любой другой механизм

	$body=file_get_contents($openidUrl);

    if(!$body) openid_error("не удалось получить страницу ".h($openidUrl));

    // нас интересуют два LINK-элемента: openid.server и openid.delegate
    // действуем тупо и по-простому
    $serviceUrl = "";
    $delegateId = "";

    $body = preg_replace('/<body\b.*/i', '', $body);

    if(preg_match('/<link\b[^>]+?\brel=([\'"])openid\.server\1.*?>/i', $body, $m)) { $link=$m[0];
        if(preg_match('/\bhref=([\'"])(.*?)\1/i', $link, $m)) $serviceUrl=unh($m[2]);
    }
    if(!$serviceUrl) openid_error("openid.server не найден");
    if(!preg_match('{^https?://}i',$serviceUrl)) openid_error("некорректный url openid.server");

    if(preg_match('/<link\b[^>]+?\brel=([\'"])openid\.delegate\1.*?>/i', $body, $m)) { $link=$m[0];
        if(preg_match('/\bhref=([\'"])(.*?)\1/i', $link, $m)) $delegateId = unh($m[2]);
    }
    if(!$delegateId) $delegateId=$openidUrl;
    
    // правильность openid.delegate не проверяем -- она на совести автора страницы

    $_SESSION["OpenID"] = array(
        "serviceUrl"    => $serviceUrl,
        "openidUrl"     => $openidUrl,
    );

    // получили адрес сервиса
    // подготовим запрос...

    $params = array(
        "openid.mode"       => $checkMode,
        "openid.identity"   => $delegateId,
        "openid.return_to"  => THIS_URL."?return",
        "openid.trust_root" => THIS_URL,
		"openid.sreg.required" => 'email,fullname',
		"openid.sreg.optional" => 'dob,gender,postcode,country,language,timezone'
    );

    // всё, перекидываем клиента на $serviceUrl с нужными параметрами
    // ждать его обратно будем по адресу THIS_URL."?return"

    header("Location: ".appendParams($serviceUrl, $params));
    exit;
}


if(isset($_GET["return"]) and $_SESSION["OpenID"]) {

    // мы вернулись с опенид-сервиса
    // посмотрим, с каким результатом...

    // кстати, GET-параметры имеют вид openid.something, но PHP их переименовывает в openid_something
    // так что не удивляйтесь, что параметры называются не совсем так, как в спецификации

    if($_GET["openid_mode"] == "cancel") { // юзер сам запретил сообщать нам информацию о себе. имеет право
        $_SESSION["OpenID"]["status"] = "С нами не захотели разговаривать:(";

    } elseif($_GET["openid_mode"] == "error") { // произошла какая-то ошибка. вряд ли в этом наша вина, но тем не менее...
        $_SESSION["OpenID"]["status"] = "Произошла внезапная ошибка: ".h($_GET["openid_error"]);

    } elseif($_GET["openid_mode"] == "id_res") { // какой-то результат всё-таки получен

        if(isset($_GET["openid_user_setup_url"])) {
            // что-то пошло не так
            // либо у нас таки нет прав на этот урл
            // либо (это может случиться только в режиме checkid_immediate)
            // у сервиса нет разрешения выдавать нам информацию автоматически, 
            // и он даёт URL, по которому следует послать юзера, чтобы тот сам нам всё разрешил
            // (в режиме checkid_setup юзер сам попадает на этот урл)

            $setupUrl = $_GET["openid_user_setup_url"];
            // добавим параметр openid.post_grant=return, чтобы нас автоматически вернули обратно после разрешения
            $setupUrl = appendParams($setupUrl, array("openid.post_grant" => "return"));
            $_SESSION["OpenID"]["status"] = "Автоматическая идентификация не удалась. Вам нужно проследовать по адресу <a href=\"".h($setupUrl)."\">".h(substr($setupUrl,0,50))."...</a> и определиться с тем, что мы можем, а что нет.";
            $_SESSION["OpenID"]["dontclear"] = true;
        } else {
            // кажется, всё в порядке
            // но для полной уверенности надо проверить подпись
            // т.к. мы глупые, то поручим это самому сервису
            $params = array(
                "openid.mode"           => "check_authentication",
                "openid.assoc_handle"   => $_GET["openid_assoc_handle"],
                "openid.sig"            => $_GET["openid_sig"],
                "openid.signed"         => $_GET["openid_signed"],
            );
            foreach($_GET as $k=>$v) { if(strpos($k,'openid_')!==0) continue;
                $k=str_replace('openid_','openid.',$k); if(!isset($params[$k])) $params[$k]=$v;
            }

            // надо послать POST-запрос с этими параметрами
            // естественно, если нет CURL-а, то можно использовать любой другой механизм
            $ch = curl_init();   
            curl_setopt($ch, CURLOPT_URL, $_SESSION["OpenID"]["serviceUrl"]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, makeQueryString($params));
            $result = curl_exec($ch);
            curl_close($ch);

            // ответ придёт в виде строк вида key:val\n
            // разбираем его...
            $vals = array();
            foreach(preg_split('/\n/', $result, -1, PREG_SPLIT_NO_EMPTY) as $pair) {
                list($k, $v) = explode(":", $pair, 2);
                $vals[$k] = $v;
            }

            // нас интересует только один параметр ответа ? lifetime
            if(
                isset($vals["lifetime"]) and (int)$vals["lifetime"] > 0 or
                isset($vals["is_valid"]) and $vals["is_valid"] == "true"
            ) {
                $_SESSION["OpenID"]["status"] = "Поздравляем, Вы и в самом деле <b>".h($_SESSION["OpenID"]["openidUrl"])."</b>!";
                $_SESSION["OpenID"]["parms"]=$params;
            } else {
                $_SESSION["OpenID"]["status"] = "Либо цифровая подпись протухла, либо <b>".h($_SESSION["OpenID"]["openidUrl"])."</b> - это не Ваш урл.";
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
Ваш Openid: <input type='text' name='openid_url' size='60'><input type='submit' value='Go!' onclick='openid_ifr_post()'>
<br>Метод: <select name='mode'>
            <option value='1'>checkid_setup (человекопонятный)</option>
            <option value='0'>checkid_immediate (автоматопонятный)</option>
        </select>
</form></blockquote>

";
*/

die($o);
?>