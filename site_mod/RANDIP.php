<?php /* ����� ��������� ��������� � ����������� �� IP ����������

��� ������ ������ binoniq {_RANDIP:
� ����� �������� ������ �� ���������� �����, ��� � �����-�� �������
� ����� ��������� ���������� ������������ ������������� ������ ��������, ������������ ���������, ������� ������ ��������� ����������� �������� ����� � ������������� ����� ��� ������ �� 2.
������, ��� ��� ���� - �������� ���� ���������������� ������������� �������� (����� binoniq ��������� ����������������)
_}.

*/

function RANDIP($e) { $e=explode("\n",preg_replace("/\n+/s","\n",$e)); return c($e[abs($GLOBALS['IPN']%sizeof($e))]); }

?>