<?php
function LAST() {
$LAST_skip = 5;

// $cat=$_GET['cat'];
// if(isset($cat)) { $where="`cat` = '".e($cat)."'"; $catpn .= "&cat=".urlencode($cat); }
// else { $where = "`DateDatetime`!=0"; $catpn = ''; }

$skip=intval($_GET['skip']);
$pp=ms("SELECT `Date`,`Body`,`template`,`autokaw`,`autoformat`,`Header`,`DateUpdate`,`Access`,`num`,`Comment_view` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!='0'")." ORDER BY `Date` DESC LIMIT ".$skip.",".($LAST_skip+1),"_a");

//  `Comment` enum('enabled','disabled','allways_on','screen','normalscreen') NOT NULL default 'enabled',
//  `autokaw` enum('auto','no') NOT NULL,
//  `autoformat` enum('no','p','pd') NOT NULL default 'no',
//  `template` varchar(32) NOT NULL default 'blog',


#$pp=ms("SELECT `Date`,`Body`,`Header`,`cat`,`DateUpdate`,`Access`,`num` FROM `dnevnik_zapisi` ".WHERE($where)." ORDER BY `Date` DESC LIMIT ".$skip.",".($LAST_skip+1),"_a");

#$s1="<html><head><title>Артем Павлов. Блог.</title></head><body></html>";

$next=$prev='<td></td><td></td>';
$n=sizeof($pp); if($n>$LAST_skip) { unset($pp[$n-1]);
	$next="<td align=left>&larr;&nbsp;<a href=".$mypage."?skip=".($skip+$LAST_skip).$catpn.">старые ".$LAST_skip."</a></td>";
}

$n=$skip-$LAST_skip; if($n>=0) {
	$prev="<td align=right><a href=".$mypage."?skip=".$n.$catpn.">новые ".$LAST_skip."</a>&nbsp;&rarr;</td>";
}

$s=$prevnext="<table width=100%><tr>".$next.$prev."</tr></table>";

foreach($pp as $p) {
	$GLOBALS['article']=$p;
	$link=get_link($p["Date"]); // полная ссылка на статью
		list($article['Year'], $article['Mon'], $article['Day']) = explode('/', $p['Date'], 3); $article["Day"]=substr($article["Day"],0,2);
		$Body=onetext($p); // обработать текст заметки как положено
		$Body=zamok($p['Access']).$Body; // добавить картинки подзамков
	$Header=($p["Header"]=='')?'(...)':$p["Header"];

//$idzan = intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='".e($p["num"])."'"
//.($podzamok||$admin?'':" AND (`metka`='open' OR `login`='".e($login)."' OR `speckod`='".e($sc)."')"), '_l',$ttl)); //количество комментариев

// http://bsdpro.ru/design/e2/ltgt.gif

$s .= "<div>
	<p><h2><a href=".$link.">".$Header."</a> <small>(".$article["Day"].".".$article["Mon"].".".$article["Year"].")</small>"
.($GLOBALS['admin']?" <img class=knop onClick=\"majax('editor.php',{a:'editform',num:'".$p['num']."',comments:(idd('commpresent')?1:0)})\" src=".$GLOBALS['www_design']."e3/color_line.png>":'')
."</h2>";
// $s .= ($p["cat"]!='')?"<p style='font-size: 10pt; margin-top:4px;'>Рубрика: <a style='font-size: 10pt;' href='".$httphost."blog?cat=".$p["cat"]."'>".$p["cat"]."</a></p>":'';
$s .= "<div>".$Body."</div>";

if($p['Comment_view']!='off') {
	$idzan = intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='".e($p["num"])."'", '_l'));
	$s .= "<p align=right><a style='font-size:10pt;' href=".$link."#comments>Добавить комментарий</a> <small>(сейчас ".$idzan." шт)</small></p>";
}

$s.="</div><hr width=100% color='#165596'>";
}

return $s.$prevnext;
}
?> 
