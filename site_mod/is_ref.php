<?php /* ���� ������ �� �������� � ������������� refferer

���� �������� ������ ��������� �� ��������� ������ (������ ��� �� ������ ����� ������� ������, ����� ��� ������) - �������� ����� ����� ������������ |, ���� �� ����� - �� ����� ����� |.

{_is_ref: http://eushestakov.f5.ru ��������-����� | ...���������� ����� �������... _}

*/

function is_ref($e) { 
	list($ref,$e)=explode(' ',$e,2); list($a,$b)=explode('|',$e,2);
	return ( strstr($_SERVER['HTTP_REFERER'],c($ref)) ? c($a) : c($b));
}

?>
