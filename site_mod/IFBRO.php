<?php // ���� ������ � �������� - ������ ���� ��������, ���� ��� - ������

// {_IFBRP: linux,nokia | ��� ������ ��� ����� | ��� �� ������ � �� ����� _}

function IFBRO($e) {
	list($l,$a,$b)=explode('|',$e);
	$p=explode(',',$l);
	foreach($p as $l) if(stristr($GLOBALS['BRO'],c($l))) return c($a);
	return c($b);
}

?>