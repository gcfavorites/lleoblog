<?php /* �������

������� �������� ������

{_TABLE:
�������� | ����� | ���� | ����������
������ | 1 | 28.6 | 12121
������� | 2 | 25.6 | 12���121
���������� | 3 | 24.6 | 12���121
������ | 4 | 28.6 | 12121
�������� | 5 �������| 27 | 12121
_}
*/

function TABLE($e) {
	$s="<center><table border=1 cellspacing=0 cellpadding=10>";
	$p=explode("\n",$e);
		foreach($p as $l) { $l=c($l); $s.="<tr><td>".preg_replace("/\s*\|\s*/s","</td><td align=right>",$l)."</td></tr>"; }
	return $s."</table></center>";
}
?>
