<?php // Авторизация пользователей
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$num=intval($_REQUEST["num"]);
$a=$_REQUEST["a"];

//=================================== editpanel ===================================================================
if($a=='header') { $search=$_REQUEST["search"];

$pp=ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` WHERE `Header` LIKE '%".e($search)."%' ORDER BY `DateDatetime` DESC");

$s="<small>";
foreach($pp as $p) $s.="<br>".$p['Date']." - <a href='".get_link($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>";
$s.="</small>";

$s="helps('search',\"<fieldset id='commentform'><legend>Поиск записей для ".h($search)."</legend>".njsn($s)."</fieldset>\");";
otprav($s);
}

?>