<?php // ����������� �������������
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$num=intval($_REQUEST["num"]);
$a=$_REQUEST["a"];

//=================================== search ===================================================================
if($a=='header') { $search=$_REQUEST["search"]; $m=array();
foreach(ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` ".WHERE("`Header` LIKE '%".e($search)."%'")." ORDER BY `DateDatetime` DESC") as $p) 
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("helps('search',\"<fieldset id='commentform'><legend>����� ������� ��� ".h($search)."</legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}

//=================================== tag ===================================================================
if($a=='tag') { $tag=$_REQUEST["tag"]; $m=array();
foreach(ms("SELECT z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`")." ORDER BY `DateDatetime` DESC") as $p)
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"; }
otprav("helps('search',\"<fieldset id='commentform'><legend>������ � ����� <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."'</a></legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}


//=================================== tagpage ===================================================================
if($a=='tagpage') { $tag=$_REQUEST["tag"]; $m=array();
	include $include_sys."_onetext.php";
	include $include_sys."_modules.php";
foreach(ms("SELECT z.`Body`,z.`Date`,z.`Header` FROM `dnevnik_zapisi` AS z, `dnevnik_tags` AS t ".WHERE("t.`tag`='".e($tag)."' AND z.`num`=t.`num`")." ORDER BY `DateDatetime` DESC") as $p)
{ $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>"
."<p>".onetext($p);
}
otprav("helps('search',\"<fieldset id='commentform'><legend>���s��� � ����� <a onclick='majax(\\\"search.php\\\",{a:\\\"tagpage\\\",tag:\\\"".h($tag)."\\\"})'>'".h($tag)."'</a></legend>".njsn("<small>".implode("<br>",$m)."</small>")."</fieldset>\"); posdiv('search',-1,-1);");
}

?>