<?php /* ������ ��� ����������� ������

���� �������� ������ ����������, � �������� 'podzamok' - �������� ����� ����� ������������ |, ���� ������� 'user' - �� ����� ����� |.

{_is_podzamok: ��������� ���: http://10.8.0.1/rrr.zip | ��������� ����� ���� �� �����������. _}
{_is_podzamok: ��� ����� �������� �������! | _}

*/

function is_podzamok($e) { 
        list($a,$b)=explode('|',$e,2);
	if(!$GLOBALS['podzamok']) return c($b);

	$a=c($a);
	if(strstr($a,"\n")) return "<div style=\"background-color:".$GLOBALS['podzamcolor']."\">"
."<img src='".$GLOBALS['www_design']."e/podzamok.gif'>&nbsp;$a.</div>";
	return "<span style=\"background-color:".$GLOBALS['podzamcolor']."\">$a</span>";

//        return ($GLOBALS['podzamok'] ? c($a) : c($b) );

//return ($GLOBALS['podzamok']?$e:''); 
}

?>
