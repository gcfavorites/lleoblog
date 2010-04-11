<?php // протокол IMBLOAD - отдача заметки поаяксу любому пользователю
include "config.php";
include $include_sys."_autorize.php";
// include $include_sys."_msq.php";
include $include_sys."_onetext.php";
include $include_sys."_modules.php";

if($_GET['mode']=='oembed') {
	$p=get_zametka($_GET['date']); if($p===false) idie("Error 404");
	send_oembed($p);
}

if($_GET['mode']=='xml') {
	$p=get_zametka($_GET['date']); if($p===false) idie("Error 404");
	send_xml($p);
}


// а иначе ajax
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
$p=get_zametka($_REQUEST["Date"]); if($p===false) otprav_ajax("Error 404");
send_ajax($p);

exit;


function get_zametka($Date) {
	$p=ms("SELECT `DateDate`,`Access`,`Header`,`num`,`Body` FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($Date)."'"),"_1");
	if($p===false) return false;
	$p["Admin"] = $GLOBALS["admin_name"];
	$p["Header"] = zamok($p["Access"]).$Date.($p["Header"]?" - ".$p["Header"]:'');
	$p["Body"] = obr_link(blogtags(onetext($p)));
	$p["Date"] = $Date;
	$p["url"] = $GLOBALS["httphost"].$p['Date'].($p['DateDate']?".html":'');
	return $p;
}


function send_ajax($p) { $GLOBALS['_RESULT']=array(
	"Admin"=>$p["Admin"],
	"Header"=>$p["Header"],
	"Body"=>$p["Body"],
	"status"=>true
	);
	exit;
}

function send_oembed($p) {
	header("Content-Type: application/json; charset='".$GLOBALS['wwwcharset']."'");
	die('{"provider_name": "lleoblog",'
	.'"provider_url": "'.s($GLOBALS["httphost"]).'",'
	.'"author_name": "'.s($p["Admin"]).'",'
	.'"author_url": "'.s($p['url']).'",'
	.'"title": "'.s($p["Header"]).'",'
	.'"html": "'.s($p["Body"]).'",'
	.'"height": 1024,'
	.'"width": 768,'
	.'"version": "1.0",'
	.'"type": "link"}');
}



function send_xml($p) { global $wwwcharset;
	header("Content-Type: text/xml; charset='".$wwwcharset."'");
	die('<?xml version="1.0" encoding="'.$wwwcharset.'" standalone="yes"?>'
	.'<oembed><version>1.0</version>'
	.'<provider_name>lleoblog</provider_name>'
	.'<provider_url>'.htmlspecialchars($GLOBALS["httphost"]).'</provider_url>'
	.'<author_name>'.htmlspecialchars($p["Admin"]).'</author_name>'
	.'<author_url>'.htmlspecialchars($p["url"]).'</author_url>'
	.'<title>'.htmlspecialchars($p["Header"]).'</title>'
	.'<type>link</type>'
	.'<html>'.htmlspecialchars($p["Body"]).'</html>'
	.'</oembed>');
}

function s($s) { return addcslashes(
strtr($s,"\n\r","  ")
,"\\\'\"{}"); }

function otprav_ajax($s) { global $_RESULT,$msqe; $_RESULT["otvet"] = $msqe.$s; $_RESULT["status"] = true; exit; }

function blogtags($s) { global $httpsite; return str_replace(
	array('{foto_www_preview}','{foto_www_small}'),
	array($httpsite.$GLOBALS['foto_www_preview'],$httpsite.$GLOBALS['foto_www_small']),
$s); }

function obr_link($s) { 
	$s=preg_replace_callback("/(<img[^>]+src=[\'\"]*)((?!http)[^\'\"\s]+)/si","obr_link1",$s);
	$s=preg_replace_callback("/(<a[^>]+href=[\'\"]*)((?!http)[^\'\"\s]+)/si","obr_link1",$s);
	$s=preg_replace_callback("/(onclick=\'return\sfoto\(\")((?!http)[^\s\"]+)/si","obr_link1",$s);
	return $s;
} function obr_link1($s) { return $s[1].$GLOBALS['httpsite'].$s[2]; }

?>
