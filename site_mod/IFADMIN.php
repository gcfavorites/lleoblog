<?php

// ���� ����� - ������ ���� ��������, ���� ��� - ������
// {_IFADMIN: <p>��� ������: 1223HsdnD! | ���� �����, �� �� �����! _}

function IFADMIN($e) {
	list($a,$b)=explode('|',$e);
	return ($GLOBALS['admin'] ? c($a) : c($b) );
}

?>
