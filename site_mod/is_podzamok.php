<?php /* ������ ��� ����������� ������

���� �������� ������ ����������, � �������� 'podzamok' - �������� ����� ����� ������������ |, ���� ������� 'user' - �� ����� ����� |.

{_is_podzamok: ��������� ���: http://10.8.0.1/rrr.zip | ��������� ����� ���� �� �����������. _}
{_is_podzamok: ��� ����� �������� �������! | _}

*/

function is_podzamok($e) { 
        list($a,$b)=explode('|',$e,2);
        return ($GLOBALS['podzamok'] ? c($a) : c($b) );


//return ($GLOBALS['podzamok']?$e:''); 
}

?>
