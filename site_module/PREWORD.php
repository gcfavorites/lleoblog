<?php

function PREWORD($e) { global $IS,$REF; $s='';

	$opoznan=$IS['imgicourl']; if(substr($opoznan,0,1)=='#') $opoznan='';

if(isset($GLOBALS['linksearch'])) {

	$u0=$GLOBALS['linksearch'][0];
	$u1=$GLOBALS['linksearch'][1];

if($u0!='') { // ���� ������ �� ����������

$preword = ($opoznan?"����� �������, ".$opoznan."!<p>":"")."����� ����� ".h($u1)." ������ ������ ����
\"<b><u>".h($u0)."</u></b>\"? ��� �� �������� �������, ����� ���� �����. �������, ���� ����.
�� ���������� �� ������ ������� � ������ �������� ������ �������� (����).
���� �� �� ����� ������� ��, ��� �����, ������. �� ������ �������� � �������� ��� �������.";

if($article["DateTime"] < time()-86400*30 ) $s .= " ������ �����: ��� ����� ������ �������.";

if(ereg("������", $u0)) $s .= "<p>������ ������ \"������\". ����� - �������� ��������, � �� ������������. ���� � �������� � �����
�������� �����-�� ������ � �����, � �� �� ������ ����� �� ���� ������, �� ��������� ��� �������: ����� ������ ������ �������.
� �� ���� �������, ����� � ������ ����� ��� �����-�� ����� - � ������������� �� �������� �������������� �������, ��������.
������� � ���������, ������������� � �������, � ��� ���������.";

} elseif ( !( strstr($REF,$GLOBALS["httpsite"]) || strstr($REF,"livejournal.com") )) { // ��� ���� ������ �� ������
	$fromlink=h(urldecode($_SERVER["HTTP_REFERER"]));
	if(strstr($fromlink,'blogs.yandex.ru/entries')) $fromlink='������-���� (����, ���� ����� ��� �� � ��� ����������?)';

$s .= "<p>�� ������ c <font color=green>".h($fromlink)."</font> �� ������� ������ �������� �� ����� �����.";

if($GLOBALS['article']["DateTime"] < (time()-86400*30) ) $s .= " ����� ������ �����, ����� ������.";

$s .= " ��� ������ ������� �� �������� ��������� ������ �������� ����� �� ��������� (����).
� ���� ��� ��� ����, ����� ������ � ��������������, � ������ ���� �� ����: �������� ������������ ����
�������������. ���������� ����� ��� �������, �� ������ ��������, ��� ��� ������� �� ������� ��� ����������,
� ��� ������� �� ������� ��������������� �����. � ����� �� ����� ��������. ������� �� ���������.";
}

} elseif($opoznan!='') $s.="<div class=br>����������, ������� ".$opoznan." ! � ������ �������, ����� �� �������� �������� ��� �������.</div>";

if($_GET['search']!='') $s .= "<p>�������� ���������� � ���������� ���� \"<span class=search>".h($_GET['search'])."</span>\",
<a href='".$GLOBALS['mypage']."'>������������� � ���������� �����</a>";


return "<div class='preword'>$s</div>";

}

?>