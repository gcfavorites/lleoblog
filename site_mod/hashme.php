<?php // hashdata v2

include $GLOBALS['include_sys']."_hashdata2.php";

function hashme($e) { //return $e;
//	if(substr($e,0,1)=='#') 
//return hashflash($e);
	return hashdata($e," u".$GLOBALS['unic']." t".time()." ");
}

//$metka="Vot takoi tekstik ja zakodiroval. IP=10.8.0.100"; // ������ ������� �����
//$text=file_get_contents("santa.html"); // ����� �����
//print hashflash($text); // �������� ��� ������� ��� ������ � ���� ������
//$text2 = hashdata($text,$metka); // ������������ ����� � �����
//print datahash($text2); // ������ ����� �� ������

?>
