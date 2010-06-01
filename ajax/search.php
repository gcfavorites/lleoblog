<?php // Авторизация пользователей
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$num=intval($_REQUEST["num"]);
$a=$_REQUEST["a"];

//=================================== search ===================================================================
if($a=='header') { $search=$_REQUEST["search"]; $m=array();
foreach(ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` ".WHERE("`Header` LIKE '%".e($search)."%'")." ORDER BY `DateDatetime` DESC") as $p) 
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("helps('search',\"<fieldset id='commentform'><legend>Поиск записей для ".h($search)."</legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}

//=================================== tag ===================================================================
if($a=='tag') { $tag=$_REQUEST["tag"]; $m=array();
foreach(ms("SELECT z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`")." ORDER BY `DateDatetime` DESC") as $p)
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("helps('search',\"<fieldset id='commentform'><legend>Записи с тэгом <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."'</a></legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}

//=================================== tagpage ===================================================================
if($a=='tagpage') { $tag=$_REQUEST["tag"]; $m=array();
	include $include_sys."_onetext.php";
	include $include_sys."_modules.php";
foreach(ms("SELECT z.`Body`,z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`")." ORDER BY `DateDatetime` DESC") as $p)
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"
."<p>".onetext($p);
}
otprav("helps('search',\"<fieldset id='commentform'><legend>Записи с тэгом <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."'</a></legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}

//=================================== alltag ===================================================================
if($a=='alltag') {
$mm=ms("SELECT DISTINCT `tag` FROM `dnevnik_tags` ORDER BY COUNT(`tag`)","_a");
$a=array(); foreach($mm as $m) $a[$m['tag']]=ms("SELECT COUNT(*) FROM `dnevnik_tags` WHERE `tag`='".e($m['tag'])."'","_l"); arsort($a);
$o=''; foreach($a as $l=>$n) $o.="<tr><td><div class=ll onclick=\"majax('search.php',{a:'tag',tag:'".h($l)."'})\">".h($l)."</div></td><td> &nbsp; $n</td></tr>";
otprav("helps('search',\"<fieldset id='commentform'><legend>Все тэги</legend><table>".njsn($o)."</table></fieldset>\"); posdiv('search',-1,-1);");
}

?>