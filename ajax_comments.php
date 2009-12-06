<?
include "config.php";
include $include_sys."_autorize.php";
include $include_sys."_msq.php";
include $include_sys."_onecomment.php";
// include_once("_podsveti.php");
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");


$_REQUEST["text"]=strtr($_REQUEST["text"],"\r",''); // заебали эти виндузовые \r


$onecomment_info=false;

function vbazurul($namex,$namelast) { global $IPsc,$admin;
	$q="UPDATE `dnevnik_comments` SET ".$namex."=".$namex."+1, ".$namelast."='".$IPsc."' WHERE `id`='".e($_REQUEST["id"])."'";
	if(!$admin) $q .= " AND `".$namelast."`!='".e($IPsc)."' AND `IP`!='".e($_SERVER["REMOTE_ADDR"])."' AND `speckod`!='".e($GLOBALS['sc'])."'";
	msq($q);
	otprav_add('normal',0);
}

function otprav($s) { global $_RESULT,$msqe; $_RESULT["otvet"] = $msqe.$s; $_RESULT["status"] = true; exit(); }
function get_one() { return(ms("SELECT * FROM `dnevnik_comments` WHERE `id`='".e($_REQUEST["id"])."'","_1",0)); }

function update_one($name,$data) { global $msqe,$BRO;
	$ara = array($name=>e($data));

	if($name=='Answer') {
		if(strstr($BRO,'NokiaE90')) $ugen='Nokia E90';
		elseif(strstr($BRO,'Linux i686')) $ugen='ALT-Linux';
		else $ugen=substr($BRO,0,64);

		if(ms("SELECT `Answer` FROM `dnevnik_comments` WHERE `id`='".e($_REQUEST["id"])."'","_l",0) == '')
		$ara = array_merge($ara,array('Answer_user'=>e($ugen),'Answer_time'=>time()));
	}

	msq_update("dnevnik_comments",$ara,"WHERE `id`='".e($_REQUEST["id"])."'");
     	if($msqe!='') otprav($msqe);
	otprav_add('normal',0);
}


function otprav_add($mode,$nams) { global $www_design,$httphost; $dop=0;
	$p = get_one();
	if($mode=='normal') $s = onecomment_ans($p,$_REQUEST["i"]); else $s = onecomment($p,$_REQUEST["i"]);

	if($mode=='textarea') {
$etid="document.getElementById('".$p['id'].'_text'."')";

$datass=date('Y/m/');

$dop = "
<table><tr><td rowspan=2>
<TEXTAREA id='".$p['id']."_text' class=t cols=50 rows=".max(page(htmlspecialchars($p[$nams])),10).">".htmlspecialchars($p[$nams])."</TEXTAREA>
</td><td valign=top>
<input value='SEND >>' class='t' onclick=\"javascript:comment(document.getElementById('".$p['id']."_text').value,'".$p['id']."','".$_REQUEST["i"]."','change_".$nams."')\" type='button'>

<p><a onClick=\"pins($etid,'<p class=pd>','');\"><img border=1 src=".$www_design."/e/pd.gif></a>
<a onClick=\"pins($etid,'<p class=d>','');\"><img border=1 src=".$www_design."e/d.gif></a>
<a onClick=\"pins($etid,'".chr(160)."','');\"><img border=1 src=".$www_design."e/nbsp.gif></a>
<a onClick=\"pins($etid,'".chr(169)."','');\"><img border=1 src=".$www_design."e/copy.gif></a>
<a onClick=\"pins($etid,'".chr(151)."','');\"><img border=1 src=".$www_design."e/mdash.gif></a>
<a onClick=\"pins($etid,'".chr(171)."','".chr(187)."');\"><img border=1 src=".$www_design."e/ltgt.gif></a>

<br>
<a onClick=\"pins($etid,'<b>','</b>');\"><img border=1 src=".$www_design."e/bold.gif></a>
<a onClick=\"pins($etid,'<i>','</i>');\"><img border=1 src=".$www_design."e/italic.gif></a>
<a onClick=\"pins($etid,'<s>','</s>');\"><img border=1 src=".$www_design."e/strikethrough.gif></a>
<a onClick=\"pins($etid,'<u>','</u>');\"><img border=1 src=".$www_design."e/underline.gif></a>
<a onClick=\"pins($etid,'\\n<center>','</center>');\"><img border=1 src=".$www_design."e/justifycenter.gif></a>

<br>
<a onClick=\"pins2($etid,'<img src=".$www_design."e/lj.gif style=\'vertical-align: middle;\'><a href=http://','.livejournal.com>','</a>');\"><img border=1 src".$www_design."e/ljuser.gif></a>
<a onClick=\"pins($etid,'<p><center><img src=".$httphost.$datass."',' border=1></center>');\"><img border=1 src=".$www_design."e/image.gif></a>
<a onClick=\"pins2($etid,'<a href=','>','</a>');\"><img border=1 src=".$www_design."e/link.gif></a>
<a onClick=\"pins2($etid,'\\n\\n<p><center><object width=320 height=240><param name=movie value=\'".$httphost.$datass."','.swf\'></param><param name=wmode value=transparent></param><embed src=\'".$httphost.$datass."','.swf\' type=\'application/x-shockwave-flash\' wmode=transparent width=320 height=240></embed></object></center>');\"><img border=1 src=".$www_design."e/ljvideo.gif></a>
<a onClick=\"pins($etid,'\\n<blockquote style=\'border: 1px dashed rgb(255,0,0); padding: 20px; margin-left: 50px; margin-right: 50px; background-color: rgb(255,252,223);\'>','</blockquote>');\"><img border=1 src=".$www_design."e/tableb_1.gif></a>

<br>
<a onClick=\"pins($etid,'\\n<table style=\'border-collapse: collapse; border: 1px solid red; margin: 20pt;\'\\nbgcolor=#fffff0 border=1 cellpadding=20><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e/tableb_r.gif></a>
<a onClick=\"pins($etid,'\\n<table bgcolor=#fff0ff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e/tableb1.gif></a>
<a onClick=\"pins($etid,'\\n<table bgcolor=#f0ffff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e/tableb2.gif></a>
<a onClick=\"pins($etid,'\\n<table bgcolor=#fffff0 border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e/tableb3.gif></a>
<a onClick=\"pins($etid,'\\n<pre style=\'border: 0.01mm solid rgb(0,0,0); padding: 4px; line-height: 100%; font-family: monospace; background-color: rgb(255,255,255);\'>','</pre>');\"><img border=1 src=".$www_design."e/tableb_pre.gif></a>

</td></tr>
<tr><td valign=bottom>
<a href=\"javascript:comment('','".$p['id']."','".$_REQUEST["i"]."','nochange')\" class=br>close</a>
</td></tr></table>";

$s .= "<div class=ct>".$dop."</div>";
}
	otprav($s);
}

//##################################################################


$a=$_REQUEST["action"];

if($a == "load_comments") { // загрузить простыню
	$_RESULT["prostynya"] = "<font size=2>".load_comments($_REQUEST['DateID'],$_REQUEST["order"])."</font>";
	$_RESULT["status"] = true;
	exit;
}

if ( $a == 'rulit'  // камент плюс!
	|| $a == 'spamit' // камент минус!
	|| $a == 'ans_rulit' // lleo плюс!
	|| $a == 'ans_spamit' // lleo минус!
	) vbazurul($a,'ans_lastIPsc'); // запомнить последнего голосовавшего (увы, пока вот так бесхитростно)

if ($admin && $a == "del") { global $msqe; msq_del("dnevnik_comments",array("id"=>intval($_REQUEST["id"]))); otprav($msqe); } // удалить id
//if ($admin && $a == "ed_rul") otprav_add('textarea','rulit'); // редактировать плюсы
//if ($admin && $a == "ed_spam") otprav_add('textarea','spamit'); // редактировать минусы
if ($admin && $a == "rulm_on") update_one('rulit_master','1'); // установить признак "комментарий особо ценен"
if ($admin && $a == "rulm_off") update_one('rulit_master','0'); // сбросить признак "комментарий особо ценен"
if ($admin && $a == "ans") otprav_add('textarea','Answer'); // вызвать на редактирование ответ на комментарий
if ($admin && $a == "edit") otprav_add('textarea','Commentary'); // вызвать на редактирование присланного комментария
//-----------------------------------------------------------
if ($admin && $a == "change_Answer" ) { // написан(изменен) ответ на комментарий
	include $include_sys."_mail_answer.php"; mail_answer($_REQUEST["id"],$_REQUEST["text"]); // сперва отправить mail автору
	update_one('Answer',$_REQUEST["text"]); // занести в базу
}
//-----------------------------------------------------------
// if ($admin && $a == "change_rulit") update_one('rulit',$_REQUEST["text"]);
if ($admin && $a == "change_rulit_master") update_one('rulit_master',$_REQUEST["text"]);
if ($admin && $a == "change_Commentary") update_one('Commentary',$_REQUEST["text"]);
if ($admin && $a == "nochange") otprav_add('normal',0); // закрыть без изменений
if ($a == "info") { $onecomment_info=true; otprav_add('normal',0); } // показать информацию автора комментария
if ($admin && $a == "screen") update_one('metka',$_REQUEST["action"]); // скрыть комментарий
if ($admin && $a == "open") { // открыть комментарий (и записать время открытия - чтобы он появился в новой RSS комментариев)
	if(ms("SELECT `Answer_time` FROM `dnevnik_comments` WHERE `id`='".e($_REQUEST["id"])."'","_l",0) == 0) // если время открытия нет
        msq_update("dnevnik_comments",array('Answer_time'=>time()),"WHERE `id`='".e($_REQUEST["id"])."'"); // записать время открытия
	update_one('metka',$_REQUEST["action"]); // сделать видимым
}
//##################################################################
if($a == "load_stat" || $a == "delmusor" ) {
	include $include_sys."_maybelink.php";
	$blockblock='';

if($admin && $a == "delmusor") { 
	$blockblock.='<p>удаляем: '.$_REQUEST["type"].$_REQUEST["n"]."<p>";
	$basaname=($_REQUEST["type"]=='l'?'dnevnik_link':'dnevnik_search');
	$p=ms("SELECT * FROM `".$basaname."` WHERE `n`='".e($_REQUEST["n"])."'","_1",0); print $msqe;
	$blockblock .= "(".$p['n'].": <b>".htmlspecialchars($p['search'])."</b><br>".((strlen($p['link'])>80)?(substr(htmlspecialchars($p['link']),0,80)):(htmlspecialchars($p['link']))).")<p>";
	// и удалить
        msq_del($basaname, array('n'=>e($_REQUEST["n"])) ); print $msqe;
}

$bloksearch='';
$sql=ms("SELECT `search`,`poiskovik`,`count`,`n` FROM `dnevnik_search` WHERE `Date`='".e($_REQUEST["data"])."' ORDER BY `count` DESC","_a",0); print $msqe;
$nstatsearch=sizeof($sql);

foreach($sql as $p) {
        $dlink=$p['search']; if(strlen($dlink)>60) $dlink=substr($dlink,0,60-3)."...";
	if($admin) $bloksearch .= "<br><a href=\"javascript:delmusor('".$_REQUEST["data"]."','s','".$p['n']."');\">del</a> ".$p['count']." <b>'".$dlink."'</b> (".$p['poiskovik'].")";
        else $bloksearch .= "<br>".$p['count']." <b>'".$dlink."'</b> (".$p['poiskovik'].")";
        }

$bloklink='';
$nlimit=max(20,$nstatsearch);
$sql=ms("SELECT `link`,`count`,`n` FROM `dnevnik_link` WHERE `Date`='".e($_REQUEST["data"])."' ORDER BY `count` DESC LIMIT ".$nlimit,"_a",0); print $msqe;
$nstatlink=sizeof($sql);

foreach($sql as $p) {
        $dlink=maybelink($p['link']); if(strlen($dlink)>60) $dlink=substr($dlink,0,60-3)."...";
	if($admin) $bloklink .= "<br><a href=\"javascript:delmusor('".$_REQUEST["data"]."','l','".$p['n']."');\">del</a> ".$p['count']." <a href='".$p['link']."'>".$dlink."</a>";
        else $bloklink .= "<br>".$p['count']." <a href='".$p['link']."'>".$dlink."</a>";
}

$blockblock .= "<center><table style='border-collapse: collapse; border: 1px solid red; margin: 2pt;' border=1>
<tr><td colspan=2 align=center><span class=br>статистика заходов на эту страницу (ведется с 24/02/2008)</i>";

if($bloklink.$bloksearch!='') $blockblock .= "</span></td></tr><tr valign=top>
<td width=50%><div class=br><center><i>заходы по неожиданным ссылкам (топ ".$nlimit." из ".$nstatlink."):</i></center>".$bloklink."</div></td>
<td width=50%><div class=br><center><i>запросы с поисковиков:</i></center>".$bloksearch."</div></td>
</tr></table><p>";
else $blockblock .= " - пока отсутствует</span></td></tr>";

$_RESULT["stat"] = "<a href='javascript:close_stat();'>закрыть</a><p>".$blockblock;
$_RESULT["status"] = true;
}

?>
