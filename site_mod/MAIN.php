<?php // Отображение статьи с каментами - дата передана в $Date

SCRIPTS_mine();

if($_SERVER['QUERY_STRING']=='logout') {

SCRIPTS("logout","function logout2() {
	if(!confirm(realname+' действительно хочет разлогиниться?')) return;
	helps('work',\"<fieldset>Сброшена авторизация: \"+realname+\"</fieldset>\"); posdiv('work',-1,-1);
	up='logout'; fc_save('up',up); f5_save('up',up); c_save(uc,up);
	realname='logout';
	zabil('myunic',realname);
	setTimeout(\"clean('work')\", 3000);
}
	page_onstart.push('logout2()');
");
}

/*

// восстановление кук с lleo.aha.ru
if(isset($_GET['redirect']) && isset($_GET['uc']) && isset($_GET['ip'])) {
        $uz=$_GET['uc']; $uzn=intval(substr($uz,0,strpos($uz,'-')));
        $un=$GLOBALS['unic'];
        if($_GET['ip']==$_SERVER["REMOTE_ADDR"] && upcheck($uz) && $uzn!=$un && getis_global($un)!==false) {

        setcoo($GLOBALS['uc'],$uz);
        logi('restore_lleo.aha.ru_unic.txt',"\n ".$GLOBALS['num']." restore:".$GLOBALS['unic']
." (old:".$un.") realname: ".$GLOBALS['realname']);

SCRIPTS("restore lleo.aha.ru","
var unic_rest_flag=1;
function restore_lleoaharu() {
        unic_rest_flag=1;
        up='".$uz."'; var upz=up;
        fc_save('up',upz); f5_save('up',upz); c_save(uc,upz);
        realname=\"".$GLOBALS['imgicourl']."\";
        helpc('work',\"<fieldset>Поздравляю!<p>Удалось восстановить вашу авторизацию: <b>".$GLOBALS['imgicourl']
."</b><br>(она слетела в момент переезда сайта).<p>Теперь сайт снова будет вас узнавать.</fieldset>\");
        zabil('myunic',realname);
        setTimeout(\"clean('work'); up=\"+upz+\"; fc_save('up',up); window.location='".$GLOBALS['mypage']."';\", 5000);
} page_onstart.push('restore_lleoaharu()');
");
}}

*/


function MAIN($e) { global $article;

list($article["Year"],$article["Mon"],$article["Day"])=explode("/",substr($article['Date'],0,10),3);
if(intval($article["Year"].$article["Mon"].$article["Day"]))
$article["DateTime"]=mktime(1,1,1,$article["Mon"],$article["Day"],$article["Year"]);

// [prevlink] [nextlink] [prevnext] - ссылки на соседние заметки
$article['Prev']=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`<'".e($article['DateDatetime'])."' AND `DateDatetime`!=0")." ORDER BY `DateDatetime` DESC LIMIT 1","_l");
$article['Next']=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`>'".e($article['DateDatetime'])."'")." ORDER BY `DateDatetime` LIMIT 1","_l");

return ''; }

//==============================================================================================

function PRAVKA($e) {

// SCRIPT_ADD($GLOBALS['www_js']."pravka_blog.js"); уже не надо, я все хозяйство (6 кб в main.js перенес)

SCRIPTS("text_scripts","
var ajax_pravka='".$GLOBALS['www_ajax']."ajax_pravka.php';
var dnevnik_data='".$GLOBALS['article']['Date']."';
var ctrloff=".($_COOKIE['ctrloff']=='off'?1:0).";
");

}


//==============================================================================================


function PREVNEXT($e='') { global $article,$wwwhost,$httphost,$mypage,$_PAGE;

$conf=array_merge(array(
	'prev'=>"<a href='{prevlink}'>&lt;&lt; предыдущая заметка</a>",
	'next'=>"<a href='{nextlink}'>следующая заметка &gt;&gt;</a>",
	'no'=>"&nbsp;",
	'template'=>"<center><table width=98% cellspacing=0 cellpadding=0><tr valign=top>
<td width=50%><font size=1>{prev}</font></td>
<td width=50% align=right><font size=1>{next}</font></td>
</tr></table></center>"
),parse_e_conf($e));

	$_PAGE["prevlink"] = ($article["Prev"]!=''?$httphost.$article["Prev"].".html":$mypage);
	$_PAGE["nextlink"] = ($article["Next"]!=''?$httphost.$article["Next"].".html":$mypage);

	$conf['prevlink']=($article["Prev"]==''?$conf['no']:mper($conf['prev'],array('prevlink'=>$wwwhost.$article["Prev"].".html")));
	$conf['nextlink']=($article["Next"]==''?$conf['no']:mper($conf['next'],array('nextlink'=>$wwwhost.$article["Next"].".html")));

	return mper($conf['template'],array('prev'=>$conf['prevlink'],'next'=>$conf['nextlink']));
}

//==============================================================================================
// [title] - заголовок html
function TITLE($e) { global $article; 

	if($e=='') $e="{site}: {date} {header}";

	$e=str_ireplace('{site}',$GLOBALS['admin_name'],$e);
	$e=str_ireplace('{date}',$article['Date'],$e);
	$e=str_ireplace('{header}',($article['Header']!=''?$article['Header']:''),$e);

	$GLOBALS['mytitle']=$e;

	return '';
}

function STATISTIC($e) { global $article;
$conf=array_merge(array(
	'majax'=>"majax('statistic.php',{a:'loadstat',data:'".$article['num']."'})",
	'template'=>"<div class=l onclick=\"{majax}\">статистика</div>"
),parse_e_conf($e));
	return mper($conf['template'],$conf);
}

//==============================================================================================
// [body] - обработка текста заметки
function TEXT($e) { global $article; include_once $GLOBALS['include_sys']."_onetext.php";
$conf=array_merge(array(
'template'=>"<div id='Body_{num}'>{text}</div>"
),parse_e_conf($e));

return mper($conf['template'],array('text'=>onetext($article),'num'=>$article['num']));
}

function PODZAMCOLOR() { $a=$GLOBALS['article']['Access']; if($a=='all') return "";
	return " style=\"background-color: ".$GLOBALS['podzamcolor']."\"";
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
function COUNTER($e) {
	// return $GLOBALS['article']["view_counter"]+1; // старый счетчик
	return "<span class=counter"
.($GLOBALS['memcache']?" onclick=\"this.onclick='';this.style.color='red';inject('counter.php?num="
.trim($GLOBALS['blogdir'],'/').'_'.$GLOBALS['article']['num']
."&ask=1&old=0');\"":'')
.">".get_counter($GLOBALS['article'])."</span>";
}

//==============================================================================================
function UNIC($e) { global $IS;

$conf=array_merge(array(
	'kuki'=>$GLOBALS['jog_kuki'],
	'logintxt'=>'login&nbsp;',
	'template'=>"<div id='loginobr' style='cursor:pointer; padding:2px; margin: 1px 10px 1px 10px; border:1px dotted #B0B0B0;' onclick=\"majax('login.php',{action:'openid_form'})\"><span style='font-size:7px;'>ваш логин:</span><div id='myunic' style='font-weight: bold; color: blue; font-size: 8px;'>{name}</div></div>{kuki}"
),parse_e_conf($e));

	$conf['name']=((isset($IS['user']) and isset($IS['obr']))?$GLOBALS['imgicourl']:'{logintxt}'.$GLOBALS['unic']);
	$conf['name']=preg_replace("/<a\s[^>]+>/s","",str_replace('</a>','',$conf['name']));

	return mper($conf['template'],$conf);

//	$s="<div id='loginobr' style='cursor: pointer; padding: 2px; margin: 1px 10px 1px 10px; border: 1px dotted #B0B0B0;' onclick=\"majax('login.php',{action:'openid_form'})\"><span style='font-size:7px;'>ваш логин:</span><div id=myunic style='font-weight: bold; color: blue; font-size: 8px;'>".$s."</div></div>".$GLOBALS['jog_kuki'];
	

//====================== restore unic 11 ================
/*
if( $GLOBALS['IS']['openid']=='' and $GLOBALS['IS']['password']=='' and isset($_COOKIE['unic11'])) {
	list($unic,$unicpass) = explode('-',$_COOKIE['unic11'],2); $unic=intval($unic);
	if($unicpass==md5($unic.$GLOBALS['hashlogin'])) {
                $ISE=getis($unic);
                if($ISE['openid']!='') $s.="
<div id='loginobr_unic11' style='padding: 2px; margin: 1px 10px 1px 10px; border: 1px dotted #B0B0B0;'>
<div id='openidotvet' class=br style='color: green'></div>
<p class=br><blink>стерлась авторизация!</blink></p>
<b>".h($ISE['openid'])."</b><br><input type=button value='Восстановить!' onclick=\"majax('login.php',{action:'oldlogin_form',login:'".h($ISE['openid'])."'})\">
</div>

";
//zabil('openidotvet','<div class=o>соединение...</div>');majax('login.php',{action:'openid_logpas',rpage:mypage,mylog:'".$ISE['openid']."',mypas:''});

                else if($ISE['password']!=''&&$ISE['login']!='') $s.="

<div id='loginobr_unic11' style='padding: 2px; margin: 1px 10px 1px 10px; border: 1px dotted #B0B0B0;'>
<div id='openidotvet' class=br style='color: green'></div>
<p class=br><blink>стерлась авторизация!</blink></p>
<b>".h($ISE['login'])."</b>
<br><input type=button value='Восстановить!' onclick=\"majax('login.php',{action:'oldlogin_form',login:'".h($ISE['login'])."'})\">
</div>

"; 

// <p class=br>пароль:</p><input type=text size=10 id='loginobr_unic11p'>
// <br><input type=button value='Восстановить!' onclick=\"zabil('openidotvet','<div class=o>соединение...</div>');majax('login.php',{action:'openid_logpas',rpage:mypage,mylog:'".$ISE['openid']."',mypas:idd('loginobr_unic11p').value});\">

        }
}
*/
//====================== restore unic 11 ================



//	return $s;
}

//==============================================================================================

// [another_in_date] - блок, показывающий остальные заметки за это число
function ANOTHER_DATE() { global $article; $s='';
    if($article['DateDate']) {
	$pp=ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` ".WHERE("`DateDate`='".$article['DateDate']."' AND `Date`!='".e($article['Date'])."'"),"_a");
	if($pp!==false && sizeof($pp)) {
	   foreach($pp as $p) $s.="<br><a href='".get_link($p['Date'])."'>".$p['Date'].($p['Header']!=''?" - ".$p['Header']:'')."</a>";
	   return "<div style='text-align: left; border: 2px dashed #ccc; margin: 10px 10px 20px 10px; padding: 10px;'><i>Другие записи за это число:</i>".$s."</div>";
	}
    }
return '';
}

//==============================================================================================

// [title] - заголовок html

// [Header] - заголовок на странице
function HEAD($e) { global $article;
return "<div class='header'"
.($article['Access']!='all'?" style=\"padding:10pt;background-color:".$GLOBALS['podzamcolor']."\">".zamok($article['Access'])
:">")
.$article["Day"]." ".$GLOBALS['months_rod'][intval($article["Mon"])]." ".$article["Year"]
.(empty($e)?ADMINSET():$e)
."<div id=Header_".$article['num'].($GLOBALS['admin']?" class=l onclick=\"majax('editor.php',{a:'editform',num:'".$article['num']."'})\"":'').">"
.($article["Header"]!=''?$article["Header"]:'(...)')
."</div></div>";
}


function HEADERS($e) { global $article,$admin;
$conf=array_merge(array(
'zamok_template'=>"{zamok}&nbsp;", // темплейт замка
'onclick_editor'=>'',
'num'=>$article["num"],
'Header'=>$article["Header"],
'empty_Header'=>'(...)',
'adminset'=>ADMINSET(),
'podzamstyle'=>" style='padding:10pt;background-color:{podzamcolor}'",
'template'=>"<div{onclick_editor} class='header' id='Header_{num}'>{Y}-{MONTH}-{D} {H}:{i}:{s}</div>"
// "<div style='display:inline' {podzamstyle}>{adminset} {zamok}{D} {MONTH} {Y} ? <span{onclick_editor} id=Header_{num}>
// {Header}</span></div>"
),parse_e_conf($e));

list($conf['UY'],$conf['UM'],$conf['UD'],$conf['H'],$conf['i'],$conf['s'])=explode(":",date("Y:m:d:H:i:s",$article['DateUpdate']));
list($conf['Y'],$conf['M'],$conf['D'])=($article['DateDatetime']!=0?array($article["Year"],$article["Mon"],$article["Day"]):array($conf['UY'],$conf['UM'],$conf['UD']));
$conf['MONTH']=$GLOBALS['months_rod'][intval($conf['M'])];

$conf['zamok']=mper($conf['zamok_template'],array('zamok'=>zamok($article['Access'])));
if(empty($conf['Header'])) $conf['Header']=$conf['empty_Header'];
$conf['podzamstyle']=($article['Access']!='all'?str_replace('{podzamcolor}',$GLOBALS['podzamcolor'],$conf['podzamstyle']):'');
if($admin) $conf['onclick_editor']=" onclick=\"majax('editor.php',{a:'editform',num:'".$article['num']."'})\"";
return mper($conf['template'],$conf);
}


function HEAD_D($e) { global $article;
	$s="<div class='header'>".zamok($article['Access']).$article["Day"]." ".$GLOBALS['months_rod'][intval($article["Mon"])]." ".$article["Year"]."</div>";
	if(!$GLOBALS['admin'] or $e!='1') return $s;
	else return "<div class=l onclick=\"majax('editor.php',{a:'editform',num:'".$article['num']."'})\">$s</div>";
}

function HEAD_N($e) { global $article;
	$s="<div class='header' id='Header_".$article['num']."'>".($article["Header"]!=''?$article["Header"]:'(...)')."</div>";
	if(!$GLOBALS['admin'] or $e!='1') return $s;
	else return "<div class=l onclick=\"majax('editor.php',{a:'editform',num:'".$article['num']."'})\">$s</div>";
}

function HEAD_TXT($e) { return $GLOBALS["article"]["Header"]; }


function MAY9() { global $article; //--- георгиевская ленточка ---
	$m9=intval(date("md")); 
	return ($m9>501 && $m9<515)?"<img src='".$GLOBALS['www_design']."img/9-may.jpg' align=right>":'';
//return (($m9>501 && $m9<515)?"<img style='position:absolute;right:4px;top:4px;z-index:0;'src='".$GLOBALS['www_design']."img/9-may.jpg'>":'');

}

//============
/*
function TAGS($e) { global $article; $s='';
//	dier(ms("SELECT `tag` FROM `dnevnik_tags` WHERE `num`='".$article['num']."' ORDER BY `tag`",$a));
foreach(ms("SELECT `tag` FROM `dnevnik_tags` WHERE `num`='".$article['num']."' ORDER BY `tag`","_a") as $l)
$s.="<div class=ll onclick=\"majax('search.php',{a:'tag',tag:'".$l['tag']."'})\">".$l['tag']."</div>, ";
$s=trim($s,', ');
if($s!='') return $s;
return $s;
}
*/
/*
//        $p=explode(',',$_REQUEST["mytags"]); 
//        $t=''; foreach(ms("SELECT DISTINCT `tag` FROM `dnevnik_tags`","_a") as $l) {
//        $l=$l['tag']; $t.="<span".(isset($tag[$l])?'':" class=l onclick='addtag(this)'").">$l</span>, "; } 
//otprav("	return ($article["cat"]!='')?"<p style='font-size: 10pt; margin-top:4px;'>Рубрика: <a style='font-size: 10pt;' href='".$httphost."blog?cat=".$article["cat"]."'>".$article["cat"]."</a></p>":'';}
*/

// function ADMINPANEL($e){}

?>