<?php /* ��������� ���� META

{<b></b>_META: author Leonid Kaganov _<b></b>} - ����� ������ ��������� � ��������� �������� ����� 
��� ���� � ���������� �����������: &lt;meta name="author" content="Leonid Kaganov"&gt; ������������ ���������� ��������� ������ ������, ��������� ��� ����, ��������� � �����, ��������� �������� �� ������.

*/

function META($e) { list($n,$v)=explode(" ",$e,2);
//	$GLOBALS['_META'][]='<meta name="'.h(c($n)).'" content="'.h(c($v)).'">';
	$e='meta name="'.h(c($n)).'" content="'.h(c($v)).'"';
	$GLOBALS['_HEADD'][$e]=$e;
	return '';
}
?>