<?php // ����������� ������ � ��������� - ���� �������� � $Date

$text=trim($_POST['text']," \n\r\t"); $text=str_replace("\r",'',$text);
$name=trim($_POST['name']," \n\r\t"); $name=str_replace(array("\n","\t","\r"),array(' ',' ',''),$name);
$mail=mail_validate($_POST['mail']);
$num=intval($_POST['num']); if(!$num) idie("����.<p>".h($text));

$s .= "<b>�������� �����������:</b>
<form method=post action='".$wwwhost."module/get_comment_from_loh_noJS'>
<input type=hidden name=num value='$num'>
<table>
<tr><td>���:</td><td><input class=t type=text name='name' size=40 maxlength=128 value='".h($name)."'></td></tr>
<tr><td>Mail ��� ������ (�����):</td><td><input class=t type=text name='mail' size=40 maxlength=128 value='".h($mail)."'></td></tr>
<table><tr><td colspan=2>
<textarea class=t onFocus='document.onkeydown='return true';' onBlur='document.onkeydown=NavigateThrough;' name='text' cols=60 rows=7>".h($text)."</textarea>
</td></tr>
</table><br><input class=t type=submit value='���������'>
</form>";

die($s);

/*














function COMENTS($e) { global $article, $podzamok, $load_comments_MS, $enter_comentary_days, $N_maxkomm;
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
	case 'rul': $comments_knopka=true; $comments_list=true; $load_comments_MS=" AND `rul`='1'"; $dopload=" ���������"; break;
	case 'load': $comments_knopka=true; $comments_list=false; break;
	case 'timeload': $comments_knopka=$comments_timed; $comments_list=!$comments_timed; break;
	}

switch($article["Comment_write"]) { // Comment_write enum('on', 'off', 'friends-only', 'login-only', 'timeoff', 'login-only-timeoff')
	case 'on': $comments_form=true; break;
	case 'off': $comments_form=false; $coments.="<p>����������� � ���� ������� � ��������, ������� �� ���������."; break;
	case 'friends-only': $comments_form=$podzamok; if($podzamok) $coments.="<p>� ���� ������� �������� ����������� ����� ������ ������ (��������, ��)."; break;
	case 'login-only': $comments_form=($login?true:false); $coments.="<p>� ���� ������� �������� ���������� ����� ������ ������������.
������������ ����� <a href=".$wwwhost."login/?retpage=".urlencode($wwwhost.$Date.".html").">�����</a>."; break;
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

$s='';

if($comments_form) { // ��������� �������� �����������

if($GLOBALS['admin']) {

//	include_once $include_sys."_antibot.php"; // �������� ����������

$s .= "<b>�������� �����������:</b>
<form method=post action='{wwwhost}module/get_comment_from_loh_noJS'>
<input type=hidden name=num value=".$article['num'].">
<table>
<tr><td>���:</td><td><input class=t type=text name='name' size=40 maxlength=128 value='".h($IS['USER'])."'></td></tr>
<tr><td>Mail ��� ������ (�����):</td><td><input class=t type=text name='mail' size=40 maxlength=128 value='".h($IS['MAIL'])."'></td></tr>
<table><tr><td colspan=2>
<textarea class=t onFocus='document.onkeydown='return true';' onBlur='document.onkeydown=NavigateThrough;' name='text' cols=60 rows=7>".h($_POST["text"])."</textarea>
</td></tr>
</table><br><input class=t type=submit value='���������'>
</form>";

}

$s.= "<div class=l style='font-weight: bold; margin: 20pt; font-size: 16px;' onclick=\"majax('comment.php',{a:'comform',id:0,lev:0,comnu:comnum,dat:".$article['num']."});\">�������� �����������</div>";

if ( $article["DateTime"] > time() ) $s .= "<blockquote style='border: 3px dotted rgb(255,0,0); padding: 2px;'><font size=2>������� ���������� ������� ������, � ��� ������ ������, ��� ������� ��� ������, � �������� �������� ����������.</font></blockquote>";


}

// �����, ���� ���� ����� ������������ ������ � ����� �������, ��� � ��������� �� ������� ������ ���!
$idzan = intval(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='".e($article["num"])."'".($podzamok?'':" AND `scr`='0'"), '_l'));

if($idzan) { // ���� ������ ���� �����������

if($comments_list) { // ������� �������� ����������
	include_once $GLOBALS['include_sys']."_onecomm.php"; $pro = load_comments($article["num"],$article["comments_order"]);
} elseif($comments_knopka) // ���������� �� ������
	$pro = "<input TYPE='BUTTON' VALUE=' ������ �����������".$dopload." (".($podzamok?"�����":"��������")." ".$idzan." ��) ' onClick=\"majax('comment.php',{a:'loadcomments',dat:".$article['num']."})\">";
}

return $s."<div id=0>$pro<div></div></div>";

}
*/
?>
