<?php // �������� IMBLOAD - ������ ������� ������� ������ ������������
include "../config.php";
include $include_sys."_autorize.php";
// include $include_sys."_msq.php";
include $include_sys."_onetext.php";
include $include_sys."_modules.php";


if(isset($_GET['mode'])) {
	$p=get_zametka($_GET['date']); if($p===false) idie("Error 404: ");
	if($_GET['mode']=='oembed') send_oembed($p);
	if($_GET['mode']=='xml') send_xml($p);
}

// � ����� ajax
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
	$p=get_zametka($_REQUEST["Date"]); if($p===false) otprav_ajax("Error 404");
	send_ajax($p);
exit;

/*
Notice</b>:  Undefined index:  search in <b>/bhome/part1/01/lleo/www/blog/include_sys/_onetext.php</b> on line <b>22</b>
Notice</b>:  Undefined index:  mode in <b>/bhome/part1/01/lleo/www/blog/include_sys/_onetext.php</b> on line <b>25</b>
Notice</b>:  Undefined index:  autoformat in <b>/bhome/part1/01/lleo/www/blog/include_sys/_onetext.php</b> on line <b>40</b><br />\n<br /
Notice</b>:  Undefined index:  autoformat in <b>/bhome/part1/01/lleo/www/blog/include_sys/_onetext.php</b> on line <b>40</b><br />
*/

function get_zametka($Date) {
	$p=ms("SELECT `opt`,`DateDate`,`Access`,`Header`,`num`,`Body` FROM `dnevnik_zapisi` ".WHERE("`Date`='".e($Date)."'"),"_1");
	$p=mkzopt($p);
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
	.'<provider_url>'.h($GLOBALS["httphost"]).'</provider_url>'
	.'<author_name>'.h($p["Admin"]).'</author_name>'
	.'<author_url>'.h($p["url"]).'</author_url>'
	.'<title>'.h($p["Header"]).'</title>'
	.'<type>link</type>'
	.'<html>'.h($p["Body"]).'</html>'
	.'</oembed>');
}

function s($s) { return addcslashes(strtr($s,"\n\r","  "),"\\\'\"{}"); }

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
