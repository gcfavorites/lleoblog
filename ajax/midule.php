<?php // тестовое решение

include "../config.php";
include $include_sys."_autorize.php";

$ajax=2;
// Start OB handling early.
//$gz=md5(microtime().getmypid());
//ini_set('error_prepend_string',ini_get('error_prepend_string').$gz);
//ini_set('error_append_string',ini_get('error_append_string').$gz);
//ob_start(array(&$this, "_obHandler"));

// Error_Reporting(E_ALL & ~E_NOTICE);

// ini_set("display_errors","1"); ini_set("display_startup_errors","1"); ini_set('error_reporting', E_ALL); if($sdfsdfa==$fggfrb){}

// session_start(); ob_start("_obHandler"); header("Content-Type: text/html; charset=".$GLOBALS['wwwcharset']);
// function cc($s) { return str_replace(array('&','<','>',"\n"),array('&amp;','&lt;','&gt;',"&#10;"),$s)."\n"; }
// function _obHandler($bu) {
// $s=''; foreach($GLOBALS['_RESULT'] as $n=>$l) $s.=$n.'='.cc($l);
// if($bu!='') $s.='bu='.cc($bu); $s=trim($s,"\n");
// return "<html><pre id='1'>$s</pre><script>var r=window.location.hash.split('|');window.top.postMessage('IJAX|'+r[2]+'|'+document.getElementById(1).innerHTML,'http://'+r[1]);</script></html>";
// }
// die('ss<>fs');
// die('eee');

$mod=preg_replace("/[^a-z]/si",'',RE('mod'));

$file=$site_mod.$mod.'.php'; if(!file_exists($file)) {
	$file=$site_module.$mod.'.php'; if(!file_exists($file)) idie("Module not found: $mod");
} include_once($file);
if(!function_exists($mod.'_ajax')) idie("Function not found: ".$mod."_ajax");
die("ajaxoff(); ".call_user_func($mod.'_ajax'));

?>
