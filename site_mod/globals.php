<?php /* ���� �������� ����� ����������

����� ������ ����������� ������ ��� ������ � �������:

{_globals: article num _}

*/

function globals($e) { $a=explode(" ",$e);
	$l=$GLOBALS; foreach($a as $i) { $i=trim($i); if(!isset($l[$i])) return 'false'; $l=$l[trim($i)]; }
	return $l;
}

?>