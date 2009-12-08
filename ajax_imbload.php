<?

include "config.php";
include $include_sys."_autorize.php";
include $include_sys."_msq.php";
include $include_sys."_onetext.php";
include $include_sys."_modules.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$p=ms("SELECT `Access`,`Header`,`num`,`Body` FROM `dnevnik_zapisi` WHERE `Date`='".e($_REQUEST["Date"])."'","_1");

if($p===false) otprav("Error 404");

$_RESULT["Admin"] = $admin_name;
$_RESULT["Header"] = zamok($p["Access"]).$p["Header"];
$_RESULT["Body"] = obr_link(onetext($p["Body"]));
$_RESULT["status"] = true;
exit;


function otprav($s) { global $_RESULT,$msqe; $_RESULT["otvet"] = $msqe.$s; $_RESULT["status"] = true; exit; }


function obr_link($s) { 
	$s=preg_replace_callback("/(<img[^>]+src=[\'\"]*)((?!http)[^\'\"\s]+)/si","obr_link1",$s);
	$s=preg_replace_callback("/(<a[^>]+href=[\'\"]*)((?!http)[^\'\"\s]+)/si","obr_link1",$s);
	return $s;
}
function obr_link1($s) { return $s[1].$GLOBALS['httphost'].$s[2]; }


function zamok($d) {
        if($d=='all') return '';
        $z = "<img src=".$GLOBALS['www_design']."e/podzamok.gif>&nbsp;";
        if($d=='podzamok') return $z;
        return $z.$z;
}

?>
