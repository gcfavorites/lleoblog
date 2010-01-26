<?
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$a=$_REQUEST["a"];
$data=intval($_REQUEST["data"]);

if($admin && $a == "delmusor") { msq_del(($_REQUEST["type"]=='l'?'dnevnik_link':'dnevnik_search'), array('n'=>e($_REQUEST["n"])) ); }

$bloksearch='';
$sql=ms("SELECT `search`,`poiskovik`,`count`,`n` FROM `dnevnik_search` WHERE `DateID`=$data ORDER BY `count` DESC","_a",0);
$nstatsearch=sizeof($sql);

foreach($sql as $p) {
        $dlink=$p['search']; if(strlen($dlink)>60) $dlink=substr($dlink,0,60-3)."...";
	$bloksearch .= "<br>".$p['count']." <b>'".$dlink."'</b> (".$p['poiskovik'].")";
	}

$bloklink='';
$nlimit=max(50,$nstatsearch);
$sql=ms("SELECT `link`,`count`,`n` FROM `dnevnik_link` WHERE `DateID`=$data ORDER BY `count` DESC LIMIT ".$nlimit,"_a",0);
$nstatlink=sizeof($sql);

foreach($sql as $p) {
        $dlink=maybelink($p['link']); if(strlen($dlink)>60) $dlink=substr($dlink,0,60-3)."...";
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
        $s=urldecode($e); if($s!=$e) $s=htmlspecialchars($s);
        if( ( strlen($s)/((int)substr_count($s,'Р')+0.1) ) < 11 ) return(iconv("utf-8",$GLOBALS['wwwcharset']."//IGNORE",$s));
        else return(trim($s));
}

// function otprav_sb($scr,$s) { global $_RESULT,$msqe; $_RESULT["modo"] = ScriptBefore($scr,$msqe.$s); $_RESULT["status"] = true; exit; }
// function ScriptBefore($script,$run) { return "loadScriptBefore('$script',\"".njs($run)."\");"; }
// function prejs($s) { return str_replace(array("&","\\","'",'"',"\n","\r"),array("&amp;","\\\\","\\'",'\\"',"\\n",""),$s); }

?>
