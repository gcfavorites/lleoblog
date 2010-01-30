<?php // Отображение статьи с каментами - дата передана в $Date

// include_once $GLOBALS['include_sys']."text_scripts.php"; // включить библиотеку

SCRIPTS_mine();

function MAIN($e) { global $article;

list($article["Year"],$article["Mon"],$article["Day"])=explode("/",substr($article['Date'],0,10),3);
if(intval($article["Year"].$article["Mon"].$article["Day"]))
$article["DateTime"]=mktime(1,1,1,$article["Mon"],$article["Day"],$article["Year"]);

// [prevlink] [nextlink] [prevnext] - ссылки на соседние заметки
$article['Prev']=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`<'".e($article['DateDatetime'])."' AND `DateDatetime`!=0")." ORDER BY `DateDatetime` DESC LIMIT 1","_l");
$article['Next']=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`>'".e($article['DateDatetime'])."' AND `DateDatetime`!=0")." ORDER BY `DateDatetime` LIMIT 1","_l");

return ''; }


//==============================================================================================

function PRAVKA($e) {

SCRIPTS("text_scripts","
var ajax_pravka='".$GLOBALS['wwwhost']."ajax_pravka.php';
var dnevnik_data='".$GLOBALS['article']['Date']."';
var ctrloff=".($_COOKIE['ctrloff']=='off'?1:0).";

// mkdiv('helper',\"<div class='corners'><div class='inner'><div class='content'><div id='helper_body'><textarea id='message'></textarea></div></div></div></div>\",'popup');

");


//  style='display:none;'





}

//==============================================================================================


function PREVNEXT($e) { global $article,$wwwhost,$httphost,$mypage,$_PAGE;
//	$_PAGE['prevlink']=$wwwhost.$article["Prev"].".html";
//	$_PAGE['nextlink']=$wwwhost.$article["Next"].".html";
$_PAGE["prevlink"] = ($article["Prev"]!=''?$httphost.$article["Prev"].".html":$mypage);
$_PAGE["nextlink"] = ($article["Next"]!=''?$httphost.$article["Next"].".html":$mypage);

	$prev=($article["Prev"]==''?'&nbsp;':"<a href=".$wwwhost.$article["Prev"].".html>&lt;&lt; предыдущая заметка</a>");
	$next=($article["Next"]==''?'&nbsp;':"<a href=".$wwwhost.$article["Next"].".html>следующая заметка &gt;&gt;</a>");

//return "<p>".$article["Prev"]."<p>".$article["Next"]."";

return "
<center><table width=98% cellspacing=0 cellpadding=0><tr valign=top>
<td width=50%><font size=1>$prev</font></td>
<td width=50% align=right><font size=1>$next</font></td>
</tr></table></center>";

}

//==============================================================================================
// [title] - заголовок html
function TITLE($e) { global $article; return $article['Date']." ".($article['Header']!=''?$article['Header']:''); }

function STATISTIC($e) { global $article; return "<div class=l onclick=\"majax('statistic.php',{a:'loadstat',data:'".$article['num']."'})\">статистика</div>"; }

//==============================================================================================
// [body] - обработка текста заметки
function TEXT($e) {
	include_once $GLOBALS['include_sys']."_onetext.php";
	global $article; return "<div id='Body'>".onetext($article)."</div>";
}

//==============================================================================================
function OEMBED($e) { return '
<link rel="alternate" type="application/json+oembed" href="'.$httphost
."ajax_imbload.php?mode=oembed&date=".urlencode($GLOBALS['article']['Date']).'" />
<link rel="alternate" type="application/xml+oembed" href="'.$httphost
."ajax_imbload.php?mode=xml&date=".urlencode($GLOBALS['article']['$Date']).'" />
'; }

//==============================================================================================
// [counter] - счетчик на странице
function COUNTER($e) { return $GLOBALS['article']["view_counter"]+1; }

//==============================================================================================
function UNIC($e) { global $IS;

	if(isset($IS['user']) and isset($IS['obr'])) $s=$GLOBALS['imgicourl'];
	else $s='login&nbsp;'.$GLOBALS['unic'];

	$s=preg_replace("/<a\s[^>]+>/s","",$s);
	$s=str_replace('</a>','',$s);

	return "<div id=loginobr style='cursor: pointer; padding: 2px; margin: 1px 10px 1px 10px; border: 1px dotted #B0B0B0;' onclick=\"majax('login.php',{action:'openid_form'})\"><span style='font-size:7px;'>ваш логин:</span><div style='font-weight: bold; color: blue; font-size: 8px;'>".$s."</div></div>".$GLOBALS['jog_kuki'];

}

//==============================================================================================

// [another_in_date] - блок, показывающий остальные заметки за это число
function ANOTHER_DATE($e) { global $article; $s='';
	if($article['DateDate']) {
	$pp=ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` ".WHERE("`DateDate`='".$article['DateDate']."' AND `Date`!='".e($article['Date'])."'"),"_a");
	if($pp!==false && sizeof($pp)) foreach($pp as $p) $s.="<br><a href='".$wwwhost.$p['Date']."'>".$p['Date'].($p['Header']!=''?" - ".$p['Header']:'')."</a>";
	return "<div style='text-align: left; border: 2px dashed #ccc;'><i>Другие записи за это число:</i>".$s."</div>";
	}
	return '';
}

//==============================================================================================

// [title] - заголовок html

// [Header] - заголовок на странице
function HEAD($e) { global $article;
	return "<div class='header'>".zamok($article['Access']).$article["Day"]." ".$GLOBALS['months_rod'][intval($article["Mon"])]." ".$article["Year"]
."<div id=Header".(($GLOBALS['admin']

)?" class=l onclick=\"majax('editor.php',{a:'editform',num:'".$article['num']."'})\"":'').">".($article["Header"]!=''?$article["Header"]:'(...)')."</div></div>";
}

?>
