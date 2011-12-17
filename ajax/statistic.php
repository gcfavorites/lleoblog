<?php
include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

$a=RE("a");
$data=RE0("data");

//====================== кто посетил ===========================================================================
if($a == "ktoposetil") {
	$vsego=intval(ms("SELECT COUNT(*) FROM `dnevnik_posetil` WHERE `url`='$data'","_l"));

	$pp=ms("
SELECT r.url,r.unic,a.login,a.openid,a.admin
FROM `dnevnik_posetil` AS r, ".$db_unic." AS a
WHERE r.url='$data' AND a.id=r.unic
".(RE('mode')=='full'?'':"AND (a.login != '' OR a.openid !='' OR a.realname != '' OR a.lju != '')")."
LIMIT 20000","_a");

$s=$s2='';
foreach($pp as $p) {
		if($p['realname']!='') $c=h($p['realname']);
		elseif($p['openid']!='') { list($c,)=explode('.',$p['openid'],2); $c="<div class=ll><img src=".$www_design."ico/livejournal.com.gif>".h($c)."</div>"; }
		elseif($p['login']!='') $c=h($p['login']);
		elseif($p['lju']!='') $c="<img src=".$www_design."ico/livejournal.com.gif>".h($p['lju']);
		else { $s2.="#".$p['unic'].", "; continue; }
	$c="<span onclick='kus(".$p['unic'].")'>$c</span>";
	if(($admin||$podzamok) and $p["admin"]=="podzamok") $s0.="$c, ";
	else $s.="$c, ";
}
if($s0!="") $s0="<big><b>".trim($s0,', ')."</b></big>, ";
otprav("helps('ktoposetil',\"<fieldset><legend>посетители страницы: $vsego</legend><small>".njs(trim($s0.$s.$s2,', '))."</small></fieldset>\");");
}

//===============================================================================================================











//=================================================================================================
if(isset($_REQUEST['onload'])) otprav(''); // все дальнейшие опции будут запрещены для GET-запроса
//=================================================================================================


if($admin && $a == "delmusor") { msq_del((RE('type')=='l'?'dnevnik_link':'dnevnik_search'), array('n'=>e(RE('n'))) ); }


// $a==loadstat

$bloksearch='';
$sql=ms("SELECT `search`,`poiskovik`,`count`,`n` FROM `dnevnik_search` WHERE `DateID`='".$data."' ORDER BY `count` DESC","_a",0);
$nstatsearch=sizeof($sql);

foreach($sql as $p) {
        $dlink=hh($p['search']); if(strlen($dlink)>60) $dlink=substr($dlink,0,60-3)."...";
	$bloksearch .= "<br>".$p['count']." <b>'".$dlink."'</b> (".hh($p['poiskovik']).")";
	}

$bloklink='';
$nlimit=max(50,$nstatsearch);
$sql=ms("SELECT `link`,`count`,`n` FROM `dnevnik_link` WHERE `DateID`='".$data."' ORDER BY `count` DESC LIMIT ".$nlimit,"_a",0);
$nstatlink=sizeof($sql);

foreach($sql as $p) {
        $dlink=hh(maybelink($p['link'])); if(strlen($dlink)>60) $dlink=substr($dlink,0,60-3)."...";
	$bloklink .= "<br>".$p['count']." <a href='".$p['link']."'>".$dlink."</a>";
}

if($bloklink.$bloksearch!='') $blockblock = "<table style='margin: 5pt; font-size: 10pt;'><tr valign=top align=left>
<td width=50%><center><i>заходы по ссылкам (".$nlimit." из ".$nstatlink."):</i></center>".$bloklink."</td>
<td width=50%><center><i>запросы с поисковиков:</i></center>".$bloksearch."</td>
</tr></table>";
else $blockblock = "пока отсутствует";

otprav("helps('statistic',\"<fieldset><legend>статистика посещений страницы</legend>".njs($blockblock)."</fieldset>\");");

//==================================================================================================================================

function maybelink($e) {
        $s=urldecode($e); if($s!=$e) $s=h($s);
        if( ( strlen($s)/((int)substr_count($s,'Р')+0.1) ) < 11 ) return(iconv("utf-8",$GLOBALS['wwwcharset']."//IGNORE",$s));
        else return(trim($s));
}

?>