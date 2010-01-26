<?php

logi("rssc.log","\n".date("Y-m-d H:i:s")." - ".$_SERVER["REMOTE_ADDR"]." ".$_SERVER["HTTP_USER_AGENT"]);

header("Content-Type: text/xml; charset=".$wwwcharset);

$skip=intval($_GET['skip']);

$p=ms("SELECT * FROM `dnevnik_comments` WHERE `metka`='open' ORDER BY `Answer_time` DESC LIMIT ".$skip.",".$RSSC_skip."",'_a',0);

$s="<?xml version='1.0' encoding='".$wwwcharset."'?>
<rss version='2.0' xmlns:ya='http://blogs.yandex.ru/yarss/'>

<channel>
	<link>".$httphost."</link>
	<generator>LLeoBlog 1.0:comments</generator>
"; //  <lastBuildDate></lastBuildDate>

$s.="	<ya:more>".$MYPAGE."?skip=".($skip+$RSSC_skip)."</ya:more>
	<category>ya:comments</category>
";

foreach($p as $l) {
	$link=$l['Date'].".html";
	$comlink=$httphost.$link."#c".$l['id'];

$s .= "\n<item>
	<guid isPermaLink='true'>".$comlink."</guid>
	<ya:post>".$httphost.$link."</ya:post>
	<pubDate>".date("r", $l['DateTime'])."</pubDate>
	<author>".htmlspecialchars(strtr($l['Name'],"\r",""))."</author>
	<link>".$comlink."</link>
	<title></title>
	<description>".htmlspecialchars(strtr($l['Commentary'],"\r",""))."</description>
</item>\n";

if($l['Answer']!='') { if($l['Answer_user']=='') $l['Answer_user']=$admin_name;

$s .= "\n<item>
	<guid isPermaLink='true'>".$comlink."</guid>
	<ya:post>http://anton.example.com/post100.html</ya:post>
	<ya:parent>".$comlink."</ya:parent>
	<pubDate>".date("r",$l['Answer_time'])."</pubDate>
	<author>".$l['Answer_user']."</author>
	<link>".$comlink."</link>
	<title></title>
	<description>".htmlspecialchars(strtr($l['Answer'],"\r",""))."</description>
</item>\n";

}

}

$s .= "\n</channel>\n\n</rss>\n";

die($s);
// die($s1.date("r",$lastupdate).$s);


?>
