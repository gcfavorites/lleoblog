<?php /* ���� �� ���� �������� �������

���� ���������� ��� �� ��� �� ���� �������� - �������� ����� ����� ������������ |, ���� ��� ����� - ����� ����� |.

{_is_pervonah: �� ����� �������? | ������ ���� ������! _}

*/

function is_pervonah($e) {
	// return "`".$GLOBALS['page_pervonah']."`";
	list($a,$b)=(strstr($e,'|')?explode('|',$e,2):array($e,''));
	return ( empty($GLOBALS['page_pervonah']) ? c($a) : c($b));
}

?>