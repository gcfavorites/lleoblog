<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

// logi("rssc.log","\n".date("Y-m-d H:i:s")." - ".$_SERVER["REMOTE_ADDR"]." ".$_SERVER["HTTP_USER_AGENT"]);

header("Content-Type: text/xml; charset=".$wwwcharset);

$skip=intval($_GET['skip']);


// взять соответствия Date - num, чтоб по одной всякий раз не лазить
$e=ms("SELECT `num`,`Date` FROM `dnevnik_zapisi`","_a",$ttl_longsite);
	$d=array(); foreach($e as $l) $d[$l['num']]=get_link($l['Date']); unset($e);

$pp=ms("SELECT `id`,`Text`,`Name`,`Parent`,`Time`,`DateID`
FROM `dnevnik_comm` ".($podzamok?'':"WHERE `scr`='0' OR `unic`='$unic'")." ORDER BY `Time` DESC LIMIT ".$skip.",".$RSSC_skip."",'_a',0);

$s="<?xml version='1.0' encoding='".$wwwcharset."'?>
<rss version='2.0' xmlns:ya='http://blogs.yandex.ru/yarss/'>

<channel>
	<link>".$httphost."</link>
	<generator>LLeoBlog 1.0:comments</generator>
"; //  <lastBuildDate></lastBuildDate>

$s.="	<ya:more>".$httpsite.$mypage."?skip=".($skip+$RSSC_skip)."</ya:more>
	<category>ya:comments</category>
";

foreach($pp as $p) {
	$post=$d[$p['DateID']];
	$link=$post."#".$p['id'];

$s .= "\n<item>
	<guid isPermaLink='true'>".$link."</guid>
	<ya:post>".$post."</ya:post>
".($p['Parent']!=0?"        <ya:parent>".$post."#".$p['Parent']."</ya:parent>":'')."
	<pubDate>".date("r", $p['Time'])."</pubDate>
	<author>".h(strtr($p['Name'],"\r",""))."</author>
	<link>".$link."</link>
	<title></title>
	<description>".h(strtr($p['Text'],"\r",""))."</description>
</item>\n";

}

$s .= "\n</channel>\n\n</rss>\n";

die($s);
// die($s1.date("r",$lastupdate).$s);

?>
