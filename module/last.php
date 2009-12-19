<?php // Последние записи

blogpage();

if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй

include_once $include_sys."_onetext.php"; // обработка заметки
include_once $include_sys."_antibot.php"; 

#$subst1=array("{foto_www_preview}");
#$subst2=array($foto_www_preview);

$LAST_skip = 3;
//$RSSZ_mode = 1;

#header("Content-Type: text/xml; charset='".$wwwcharset."'");

$skip=intval($_GET['skip']);

$pp = ms("SELECT `Date`,`Body`,`Header`,`DateUpdate`,`Access`,`num` FROM `dnevnik_zapisi` ".WHERE("`Date` LIKE '____/__/%'")." ORDER BY `Date` DESC LIMIT ".$skip.",".($LAST_skip+1),"_a");

$next=$prev='';
$n=sizeof($pp); if($n>$LAST_skip) { unset($pp[$n-1]);
	$next="<td align=right><a href=".$mypage."?skip=".($skip+$LAST_skip).">старые ".$LAST_skip."&nbsp;&gt;&gt;</a></td>";
}

$n=$skip-$LAST_skip; if($n>=0) {
	$prev="<td align=left><a href=".$mypage."?skip=".$n.">&lt;&lt;&nbsp;новые ".$LAST_skip."</a></td>";
}

$s=$prevnext="<table width=100%><tr>".$prev.$next."</tr></table>";

#$s1="<html><head><title>Артем Павлов. Блог.</title></head><body></html>";

$lastupdate=0; foreach($pp as $p) {
	$lastupdate = max($lastupdate,$p["DateUpdate"]);
	$link=$httphost.$p["Date"].".html"; // полная ссылка на статью
		list($article['Year'], $article['Mon'], $article['Day']) = explode('/', $p['Date'], 3); $article["Day"]=substr($article["Day"],0,2);
		$Body=onetext($p); // обработать текст заметки как положено
		$Body=zamok($p['Access']).$Body; // добавить картинки подзамков
	$Header=$p["Header"];

$idzan = intval(ms("SELECT COUNT(*) FROM `dnevnik_comments` WHERE `Date`='".e($p["Date"])."'"
.($podzamok||$admin?'':" AND (`metka`='open' OR `login`='".e($login)."' OR `speckod`='".e($sc)."')"), '_l',$ttl)); //количество комментариев

$s .= "<div>
	<p><b><big><a href=".$link.">".$Header."</a></big></b>
	<br /><small>(".$article["Day"]." ".$months_rod[intval($article["Mon"])]." ".$article["Year"].")</small></p>
	<div>".$Body."</div>
	<p align=right><a href=".$link."#comments>Добавить комментарий</a> <small>(".$idzan." шт.)</small></p>
</div>
<hr width=100% color=green>
";
}

#$s .= "</body></html>";
$_PAGE["title"] = "блог";
$_PAGE["header"] = "<b>Последние записи.</b>";
$_PAGE["body"] = $s.$prevnext;



?>