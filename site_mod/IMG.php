<?php /* ������� ����� �� �����������

��������� ��� ����� � ������� ��� url (���������������). ����� ����� ��������� ������� � ������� �������������� �������� ������������:
center - �� ������ ������
left - ���������� ����� ����� �������� ����� �����
right -  ���������� ����� ����� �������� ����� ������

<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/1.jpg _}</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/2.jpg, center _}</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/3.jpg, left _} ��� ��������� ����� ������� ��� ����� �����.  ��� ��������� ����� ������� ��� ����� �����. ��� ��������� ����� ������� ��� ����� �����. ��� ��������� ����� ������� ��� ����� �����.  ��� ��������� ����� ������� ��� ����� �����. ��� ��������� ����� ������� ��� ����� �����.</div>
<div style='clear:both'>{_IMG: http://lleo.aha.ru/blog/photo/4.jpg, right _} ��� ��������� ����� ������� ��� ����� ������. ��� ��������� ����� ������� ��� ����� ������. ��� ��������� ����� ������� ��� ����� ������. ��� ��������� ����� ������� ��� ����� ������. ��� ��������� ����� ������� ��� ����� ������. ��� ��������� ����� ������� ��� ����� ������.</div>

*/


function IMG($e) { list($e,$o)=explode(',',$e,2);
	$e=(strstr($e,'/')?$e:$GLOBALS['foto_www_small'].$e);

	if($o=='') return "<img src='$e' hspace=5 vspace=5 border=0>";
	$e=c($e); $o=c($o);
	if($o=='center') return "<center><img src='$e' hspace=5 vspace=5 border=1></center>";
	return "<img src='$e' hspace=5 vspace=5 border=1 align=$o>";
}


?>
