<?php // ����������� � �������

// ���� �� ������� cron - ��������� ��� � ������ ������ �����������, ������ ��� ����
if(!is_file($GLOBALS['cronfile']) or (time()-filemtime($GLOBALS['cronfile'])) > 60*60 ) include_once("cron.php");

// �� �������� ����� �������, �� ���� �� �������. ��� ���������: $name,$text,$mail, � ����� ���� $scr=1 - �� �� ����� �����

if(!$GLOBALS['admin']) { // ��� �����������, �� �� ��� ������

	// 1. ������ ������������� �������� �����!
	if(strstr($name,$GLOBALS['admin_name'])) $name="������� ����� #".$GLOBALS['unic'];

	// 2. ���� ����������� ����� ����� ����� ���������� ������� - ��� 99% ������! � ������ - ��� 90% ����!
	$l=preg_replace("/p\.s/si",'',$text.$name); // ���� ���� ���� ����������: 'P.S.'
	if(preg_match("/[a-z]\.[a-z]/si",$l) or strstr($l,'<')) $scr=1; // ������ ���!

	// ������ �����? ������ ���� ���!
	if(stristr($text,'lleo.aha.ru/na')) redirect('http://lleo.aha.ru/na/');


if(stristr($text,'���')) idie("����� '���' ������ ��������� ����!"); // �� ������� ���� � ������ ������������ �������

if(stristr($text,'jquery')) idie("<table width=500><td>� �������� ��������� � ����� �������� jQuery! ����� ��������! ����. ��� ����� ���� ������������ ������ ��������, ��������� � ����� <a href='".$GLOBALS['wwwhost']."install.php?load=include_sys/spamoborona.php&mode=view'>include_sys/spamoborona.php</a>, �� ������ �������� �� ��� ������ ����� � �������� ����� ������.</td></table>");


}


?>