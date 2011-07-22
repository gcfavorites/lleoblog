<?php // Авторизация пользователей

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

$num=intval($_REQUEST["num"]);
$a=$_REQUEST["a"];

//=================================== search ===================================================================
if($a=='header') { $search=$_REQUEST["search"]; $m=array();
foreach(ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` ".WHERE("`Header` LIKE '%".e($search)."%'")." ORDER BY `DateDatetime` DESC") as $p) 
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("helps('search',\"<fieldset id='commentform'><legend>Поиск записей для ".h($search)."</legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}

//=================================== tag ===================================================================
if($a=='tag') { $tag=ifu($_REQUEST["tag"]);
$m=array();
foreach(ms("SELECT z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`")." ORDER BY `DateDatetime` DESC") as $p)
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("helps('search',\"<fieldset id='commentform'><legend>Записи с тэгом <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."': ".sizeof($m)."</a></legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}

//=================================== tagpage ===================================================================
if($a=='tagpage') { $tag=ifu($_REQUEST["tag"]); $m=array();
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
$m=ms("SELECT `tag`, count(*) AS `n` FROM `dnevnik_tags` GROUP BY `tag` ORDER BY `n` DESC","_a");
$o=''; foreach($m as $n) $o.="<tr><td><div class=ll onclick=\"majax('search.php',{a:'tag',tag:'".h($n['tag'])."'})\">".h($n['tag'])."</div></td><td> &nbsp; ".$n['n']."</td></tr>";
otprav("helps('search',\"<fieldset id='commentform'><legend>Все тэги</legend><table>".njsn($o)."</table></fieldset>\"); posdiv('search',-1,-1);");
}

// if(isset($_REQUEST['onload'])) otprav(''); // все дальнейшие опции будут запрещены для GET-запроса

?>