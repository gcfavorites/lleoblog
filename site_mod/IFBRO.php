<?php // ���� ������ � �������� - ������ ���� ��������, ���� ��� - ������

// {_ linux | ��� ������ | ��� �� ������ _}

function IFBRO($e) { list($l,$a,$b)=explode('|',$e);
	return ( stristr($GLOBALS['BRO'],c($l)) ? c($a) : c($b) );
}

?>
