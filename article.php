<?php // ����������� ������ � ��������� - ���� �������� � $Date

blogpage();

if(!isset($article)) { 
	$article=ms("SELECT * FROM `dnevnik_zapisi` ".WHERE("`Date`='".$Date."'"),"_1",$ttl);
	if($article===false) {
		list($Y,$m,$d)=explode('/',$Date);

		$_PAGE["title"] .= "����� ������� ���";
		$_PAGE["calendar"] = getCalendar($Y,$m);
		$Date2=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`Next`='' AND `Date` LIKE '____/__/%'")." LIMIT 1","_l",$ttl);
		$_PAGE["header"] = "<font color=red>������:</font> ����� ������� ���";
		die("�������, ������������ ������ ".$Date." �� ����������. ������ ����� �� ������� �� ����. �����, ��� ������� ��� �������.
����� ������ �� ����. ��������� ������� �������� ��������� <a href='".$wwwhost.$Date2.".html'>�����</a>.
��� ��������� ������� ����� ������:<p><center>".$_PAGE["calendar"]."</center>");
	}
}

include_once $include_sys."_antibot.php"; // �������� ����������
include_once $include_sys."_onetext.php"; // ��������� �������

$_PAGE["title"] .= $Date." ".($article['Header']!=''?$article['Header']:'');

list($article["Year"], $article["Mon"], $article["Day"]) = explode("/", $article['Date'],3); $article["Day"]=substr($article["Day"],0,2);

if(intval($article["Year"].$article["Mon"].$article["Day"]))
$article["DateTime"] = mktime(1, 1, 1, $article["Mon"], $article["Day"], $article["Year"]);

$_PAGE["header"] = zamok($article['Access']).$article["Day"]." ".$months_rod[intval($article["Mon"])]." ".$article["Year"]."<div id=Header>".$article["Header"]."</div>";

$_PAGE["calendar"] = ($article["Prev"].$article["Next"]!=''?getCalendar($article["Year"], $article["Mon"], $article["Day"]):'');
if($admin) $_PAGE["calendar"] = "<p><input TYPE=\"BUTTON\" VALUE=\"EDITOR\" onClick=\"window.location.href='".$wwwhost."editor/?Date=".$article["Date"]."' \"><p>".$_PAGE["calendar"];

//---------------------------------------------

	if($IS_USER) $opoznan=($IS_NAME?lladdru($IS_NAME):lladdru($IS_USER0));
	elseif($_COOKIE["CommentaryName"]) $opoznan = htmlspecialchars($_COOKIE["CommentaryName"]);
	elseif($lj_name) $opoznan=$lj_name;
	elseif($lju) $opoznan=ljaddru($lju);
	if(isset($opoznan)) $preword = "<div class=br>����������, ������� ".$opoznan." !<br>� ���, ��� �� ������� ��� �������.</div>";

function lladdru($l) { global $IS_IMG; return "<b><img src='$IS_IMG' border=0><font color=black>$l</font></b>"; }

// ���������� ����������

if($_SERVER["HTTP_REFERER"]!='' && !strstr($_SERVER["HTTP_REFERER"],$GLOBALS["httpsite"]) ) {
//include_once $include_sys."_poiskovik.php";
//include_once $include_sys."_linksearch.php";

if ($u[0]!="") {

$preword = ($opoznan?"����� �������, ".$opoznan."!<p>":"")."����� ����� ".htmlspecialchars($u[1])." ������ ������ ����
\"<b><u>".htmlspecialchars($u[0])."</u></b>\"? ��� �� �������� �������, ����� ���� �����. �������, ���� ����.
�� ���������� �� ������ ������� � ������ �������� ������ �������� (����).
���� �� �� ����� ������� ��, ��� �����, ������. �� ������ �������� � �������� ��� �������.";

if ($article["DateTime"] < time()-86400*30 ) $preword .= " ������ �����: ��� ����� ������ �������.";

if (ereg("������", $s[0])) $preword .= "<p>������ ������ \"������\". ����� - �������� ��������,
����-������� ��� � ������ ������. ���� � �������� � �������� ������ ������ � ����� - ��� �� ������, ��� ��
����� ����� �������! �� ���� ����� ������ ������ ������� ��� ��, � ��� � �����-�� ���������� � �������� -
��� �������� �� ��������� ��������� �����. � ��� � ���� ����� ��� ��� ����� - ���� �� �����, ��������. �������
� ���������, ������������� � ������� ���������, ������, � ��� ���������.";

} elseif ( ($_SERVER["HTTP_REFERER"]!="") && !(
strstr($_SERVER["HTTP_REFERER"],$GLOBALS["httpsite"]) ||
strstr($_SERVER["HTTP_REFERER"],"livejournal.com") )) {

//	$fromlink=maybelink(urldecode($_SERVER["HTTP_REFERER"]));

if(strstr($fromlink,'blogs.yandex.ru/entries')) $fromlink='������-���� (����, ���� ����� ��� �� � ��� ����������?)';

$preword .= "<p>�� ������ c <font color=green>".htmlspecialchars($fromlink)."</font> �� ������� ������ �������� �� ����� �����.";

if($article["DateTime"] < (time()-86400*30) ) $preword .= " ����� ������ �����, ����� ������.";

$preword .= " ��� ������ ������� �� �������� ��������� ������ �������� ����� �� ��������� (����).
� ���� ��� ��� ����, ����� ������ � ��������������, � ������ ���� �� ����: �������� ������������ ����
�������������. ���������� ����� ��� �������, �� ������ ��������, ��� ��� ������� �� ������� ��� ����������,
� ��� ������� �� ������� ��������������� �����. � ����� �� ����� ��������. ������� �� ���������.";
}


}

if($_GET['search']!='') $preword .= "<p>�������� ������������ � ���������� ���� \"<span class=search>".htmlspecialchars($_GET['search'])."</span>\"
<br>����� ���������� �������� � � ���������� ������, <a href='/".$web_path.$Date.".html'>����� ����</a>";

$preword = "<div class=preword>".$preword."</div>";



//11*86400 1000000

$pp=ms("SELECT `link`,`text`,`datetime` FROM `rekomenda` WHERE `datetime`>(NOW()- INTERVAL 1 DAY) ORDER BY `datetime` DESC","_a");
if(sizeof($pp)) {
	$coments.="<p><div style='font-size: 13px;'><b>��������, ������� ��� ����������� �� ��������� ���, ����������:</b>";
	foreach($pp as $p) { $coments.="<div>".$p['datetime'].": <a href=".$p['link'].">".$p["text"]."</a></div>"; }
	$coments.="</div>";
}


//===================================
// ��� ���� � �������������?
$premesage="";
$dopload="";

$comments_form=true; // �������� ����� ������ ������������
$comments_knopka=false; // �������� ������ �������� ������������
$comments_list=false; // ������� �������� ������������
$comments_screen=true;

$comments_timed=(
		$article["view_counter"] > $N_maxkomm // ���������� ���������� ���������
		|| $article["DateTime"] < time()-86400*$enter_comentary_days // ������� ������ �������
		?true:false); 

switch($article["Comment_view"]) { // Comment_view enum('on', 'off', 'rul', 'load', 'timeload')
	case 'on': $comments_knopka=false; $comments_list=true; break;
	case 'off': $comments_knopka=false; $comments_list=false; break;
	case 'rul': $comments_knopka=true; $comments_list=true; $load_comments_MS=" AND `rulit_master`='1'"; $dopload=" ���������"; break;
	case 'load': $comments_knopka=true; $comments_list=false; break;
	case 'timeload': $comments_knopka=$comments_timed; $comments_list=!$comments_timed; break;
	}

switch($article["Comment_write"]) { // Comment_write enum('on', 'off', 'friends-only', 'login-only', 'timeoff', 'login-only-timeoff')
	case 'on': $comments_form=true; break;
	case 'off': $comments_form=false; $coments.="<p>����������� � ���� ������� � ��������, ������� �� ���������."; break;
	case 'friends-only': $comments_form=$podzamok; if($podzamok) $coments.="<p>� ���� ������� �������� ����������� ����� ������ ������ (��������, ��)."; break;
	case 'login-only': $comments_form=($login?true:false); $coments.="<p>� ���� ������� �������� ���������� ����� ������ ������������.
������������ ����� <a href=".$wwwhost."logon/?retpage=".urlencode($wwwhost.$Date.".html").">�����</a>."; break;
	case 'timeoff': $comments_form=!$comments_timed; if(!$comments_form) $coments.="<p>����������� � ���� ������� ������������� �����������, ������ ��� ������ ������ ".$enter_comentary_days." ���� ��� ����� ��������� ��������� ".$N_maxkomm.". �� ���� ���-�� ������, �� ������ ������ �������� ��� ������: <a href=mailto:lleo@aha.ru>lleo@aha.ru</a>"; break;
	case 'login-only-timeoff': $comments_form=($login?!$comments_timed:false); if(!$comments_form) $coments.="<p>����������� � ���� ������� ���� �������� ��������� ������ ������������, �� ������������� ����������� � ���, ������ ��� ������ ������ ".$enter_comentary_days." ���� ��� ����� ��������� ��������� ".$N_maxkomm.". �� ���� ���-�� ������, �� ������ ������ �������� ��� ������: <a href=mailto:lleo@aha.ru>lleo@aha.ru</a>"; break;
	}

switch($article["Comment_screen"]) { // Comment_screen  enum('open', 'screen', 'friends-open')
	case 'open': $comments_screen=false; break;
	case 'screen': $comments_screen=true; if($comments_form) $coments.="<p>����������� � ���� ������� ���������� - ��� ����� ����� ������ ��� � ���."; break;
	case 'friends-open': $comments_screen=!$podzamok; if($comments_form && $podzamok) $coments.="<p>����������� � ���� ������� ����������, �� � ������ (� ����) ��� ����� �������."; break;
	}

if(strstr($_SERVER["HTTP_USER_AGENT"],'Yandex')) { // ������ �������
	$premesage.=''; $coments='';
	$comments_form=false; // ��������� ����������� - �� ���� (����� ������� ��������� �����������?)
	$comments_knopka=false; // �������� ������������ - �������� � �������� (������ �� ����� �������� ������, � ����� �� �������������)
	$comments_list=true;
	}

//===================================

$comans==''; if($_GET['id']==$sc) {
	if($_GET['com']=='ok') if(!$comments_screen) $comans="<p><font color=green size=4>�������, ��� ����������� ������� ��������.<br>��-�� ����������� ���� �� �������� �� �����, � � ������� 10 ������.</font><p>";
	else $comans="<p><font color=green size=4>���� ��������� ������� - ��� ������ ����� ������ ��� � ���. ��� ���� ������� ��������� ����������. ��������, � ��� ���������, ���� ����, ��� ���� � ���� ��������� ������� ������ ���������. � ����� ������ ������� �� ������.</font><p>";

	if($_GET['com']=='link') { $comans='<p><font color=red size=3>';

	$prichina=urldecode($_GET['prichina']); if($prichina!='') $comans.=$prichina;
	else $comans.='<b>������: ��� ����������� ����� ������������� �������!</b>

<p>� ������, �� ��� ������� (����������� ��� �� ����!), �� ���� ����� - ����� ������ ��� � ���. ������? ������ ��� � ���, ������,
����������� ������. ������ ��� ������ ������� �� ����, ��� ��� ���� �� ������. �� ������� �� ����� �����������, ���� ��������
���� �� ���� �� �������:

<p>1) ������ ������ ����� ����� ������ ��������� �� ���, ����� �������� � ���� ������ �������.
<p>2) ��� ���� ����, �� ������� ��������� ������, �� ������ ���� ������������ (���������� ������ � ������). �����������
���������, ���������� �����, ����� � ������ Web 2.0.
<p>3) ��� ���� � ���� �� ������ ��������� �����������, ��� ����� ������� (��� ��������) ���� ������������� ������ ��� ����������
�����-�� ���������� � ����� ������.

<p>����� �������, ���� �� ������� ������� � ���������� ����������, ������������� ��� �� �������� - ������ ��������� �������.
��� ����������� � ������ ����������� (��������: �� ���� �����������!), �� ����� �������, ����� � ��� ������.

<p>� ��� ��� ������������, ���������� � ������ ������� ��������� ��� ������ ���� �����: ��������� ����� �� �� ���, ��� ���� ��� ������
�� ��������� ������������ ��� ��������� � ��������� ����� ��������, ������ ��� � ����� �������� � ����� ������ ������. ��� � ���������
����� �������������.';

$comans.='</font>';

}

}

// �����, ���� ���� ����� ������������ ������ � ����� �������, ��� � ��������� �� ������� ������ ���!
$idzan = intval(ms("SELECT COUNT(*) FROM `dnevnik_comments` WHERE `DateID`='".e($article["num"])."'"
.($podzamok||$admin?'':" AND (`metka`='open' OR `login`='".e($login)."' OR `speckod`='".e($sc)."')"), '_l',$ttl));

$CommentaryErrors = array();
if($_POST["action"]=="add_commentary" && $comments_form) { include_once $include_sys."get_comment.php"; } // ���� ������ �����������

//========================================================================================

$_PAGE["prevlink"] = ($article["Prev"]!=''?$httphost.$article["Prev"].".html":$mypage);
$_PAGE["nextlink"] = ($article["Next"]!=''?$httphost.$article["Next"].".html":$mypage);

$_PAGE["prevnext"] = mk_prevnest(
	($article["Prev"]!=''?"<a href=".$wwwhost.$article["Prev"].".html>&lt;&lt; ���������� �������</a>":''),
	($article["Next"]!=''?"<a href=".$wwwhost.$article["Next"].".html>��������� ������� &gt;&gt;</a>":'')
);



$_PAGE["counter"] = $article["view_counter"]+1;


if($comments_form) { // ��������� �������� �����������

	$coments .= "<h3 id=\"new_comment\">�������� �����������</h3>\n";
	$coments .= $comans;

	if ( $article["DateTime"] > time() ) $coments .= "<blockquote style='border: 3px dotted rgb(255,0,0); padding: 2px;'><font size=2>������� ���������� ������� ������, � ��� ������ ������, ��� ������� ��� ������, � �������� �������� ����������.</font></blockquote>";

	if (count($CommentaryErrors)) {
		$coments .= "<div style=\"border: solid red 2px; padding: 5px; background-color: #FDD; margin-bottom: 10px;\">����������� �� ��������.<ul>";
		foreach ($CommentaryErrors as $ErrStr) $coments .= "<li>".$ErrStr."</li>\n";
		$coments .= "</ul></div>\n";
	}

	// ���: $imechko="";
	if ($_POST["Name"]) $imechko=$_POST["Name"];
	elseif ($_COOKIE["CommentaryName"]) $imechko=$_COOKIE["CommentaryName"];
	elseif ($lj_name) $imechko=$lj_name;
	elseif ($lju) $imechko=$lju;
	// Email ��� URL: $adresok="";
	if ($_POST["Address"]) $adresok=$_POST["Address"];
	elseif ($_COOKIE["CommentaryAddress"]) $adresok=$_COOKIE["CommentaryAddress"];

	$etid="document.getElementById('Commentary')";


$coments .= "

<form method=post action=\"".$_SERVER["REQUEST_URI"]."#new_comment\">
<input type=hidden name=action value='add_commentary'>

<table>
<tr><td>���:</td><td>".($IS_USER?
"<img src='".$IS_IMG."'><b>".$IS_USER0."</b>":
"<input class=t type=text name=Name size=40 maxlength=128 value=\"".htmlspecialchars($imechko)."\">
<div class=br>� ����� ����� - <a href='".$wwwhost."logon?retpage=".urlencode($_SERVER['REDIRECT_URL'])."#Commentary'>������������ �� OpenID ��� �����������������</a></div>
")."
</td></tr>"

//."<tr><td>IP:</td><td>".$ip." (".$a.")</td></tr>"

.
($IS_MAIL!=''?"<input type=hidden name=Address value=\"".htmlspecialchars($IS_MAIL)."\">"
: "<tr><td><small>Email ��� ������ (�����):</td><td><input class=t type=text name=Address size=40 maxlength=128 value=\"".htmlspecialchars($adresok)."\"></td></tr>")
.
" <tr><td colspan=2>

<table><tr><td>

<textarea onFocus=\"document.onkeydown='return true';\" onBlur=\"document.onkeydown=NavigateThrough;\"
class=t id='Commentary' name=Commentary cols=60 rows=7>".htmlspecialchars($_POST["Commentary"])."</textarea>
</td><td>

<p>
<a onClick=\"pins($etid,'".chr(160)."','');\"><img border=1 src=".$www_design."e/nbsp.gif></a>
<a onClick=\"pins($etid,'".chr(169)."','');\"><img border=1 src=".$www_design."e/copy.gif></a>
<a onClick=\"pins($etid,'".chr(151)."','');\"><img border=1 src=".$www_design."e/mdash.gif></a>
<a onClick=\"pins($etid,'".chr(171)."','".chr(187)."');\"><img border=1 src=".$www_design."e/ltgt.gif></a>
<br>
<a onClick=\"pins($etid,'[b]','[/b]');\"><img border=1 src=".$www_design."e/bold.gif></a>
<a onClick=\"pins($etid,'[i]','[/i]');\"><img border=1 src=".$www_design."e/italic.gif></a>
<a onClick=\"pins($etid,'[s]','[/s]');\"><img border=1 src=".$www_design."e/strikethrough.gif></a>
<a onClick=\"pins($etid,'[u]','[/u]');\"><img border=1 src=".$www_design."e/underline.gif></a>
<br>

</td></tr></table>


</td></tr>

<tr><td colspan=2 align=center>

<table><tr valign=center>"

.($IS_USER?"":"<td valign=center><td>����� � ��������:</td>
<td><input type=hidden name=hash value='".antibot_make()."'>".antibot_img()."</td>
<td><input class=t type=text name=code size=".$antibot_C." maxlength=".$antibot_C."></td>")

."<td><input class=t type=submit value='���������'></td>
</tr></table></td></tr>
</table>
</form><p>&nbsp;
";

}

$nstatlink = '?'; // mysql_num_rows(mysql_query("SELECT `n` FROM `dnevnik_link` WHERE `Date`='".mysql_escape_string($article["Date"])."'"));
$nstatsearch = '?'; //mysql_num_rows(mysql_query("SELECT `n` FROM `dnevnik_search` WHERE `Date`='".mysql_escape_string($article["Date"])."'"));

SCRIPTS("
	function zabil(id,text) { document.getElementById(id).innerHTML = text; }
	function vzyal(id) { return document.getElementById(id).innerHTML; }
	function zakryl(id) { document.getElementById(id).style.display='none'; }
	function otkryl(id) { document.getElementById(id).style.display='block'; }

function close_stat() { zabil('stat',\"<a href=javascript:load_stat('".$article["Date"]."');>����������</a>\"); }

function load_stat(data) { zabil('stat', \"<center>�������� ����������...</center>\");
	JsHttpRequest.query('".$wwwhost."ajax_comments.php', { action: 'load_stat', data: data },
	function(responseJS, responseText) { if (responseJS.status) {
	zabil('stat',responseJS.stat); }},true);
}");


include_once $include_sys."text_scripts.php"; // �������� ����������

$_PAGE["coments"] = "<div id='stat' class=br><a href=\"javascript:load_stat('".$article["Date"]."');\">����������</a></div>".$coments;

// --- � ������ ���� �������� �������� ---

if($idzan) { // ���� ������ ���� �����������

if($comments_list) { // ������� �������� ����������
	include_once $include_sys."_onecomment.php";
	$_PAGE["coments"] .= "\n\n<p><div id='prostynya'>";
	$_PAGE["coments"] .= load_comments($article["num"],$article["comments_order"]);
}

if($comments_knopka) { // ���������� �� ������

if(!$comments_list) $_PAGE["coments"] .= "\n\n<p><div id='prostynya'>";

SCRIPTS("\nfunction load_comments(comments_order) { zabil('prostynya','<center>���� ��������...</center>');
JsHttpRequest.query('".$wwwhost."ajax_comments.php',
{ action: 'load_comments', DateID: '".$article["num"]."', order: comments_order },
function(responseJS, responseText) { if (responseJS.status) { 
	zabil('prostynya', '".$premesage."' + responseJS.prostynya );
	var s = document.URL.split('#c'); if(s[1]) window.location.href = window.location.href;
	}},true);}
var s = document.URL.split('#c'); if(s[1]) load_comments('normal');\n");

$_PAGE["coments"] .= "
<center><br><input TYPE='BUTTON' VALUE=' ������ �����������".$dopload." (".($podzamok?"�����":"��������")." ".$idzan." ��) ' onClick=\"load_comments('".$article['comments_order']."'); \">
</center>
</div>
"; //<p><font size=1><a href=\"javascript:load_comments('allrating'); \">������ ����������� �� ��������</a></font>
}

}


$_PAGE["body"] .= '<div id="Body">'.onetext($article).'</div>';

?>
