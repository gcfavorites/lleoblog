<?php /* ���������� ������ �������

����� ����, ��� ������������ ������ ������ � ������������ ����� (������ - ����� � ������ ������), �� ������ ������� ����������.

{_PASSWORD: kreotif123
������� �����
_}
*/

SCRIPTS("page_onstart.push('hotkey_reset=function(){}; hotkey=[];');"); // ��������� ������

function PASSWORD($e) { list($pass,$e)=explode("\n",$e,2);
	if($_POST['password']==c($pass)) return c($e);
	if(isset($_POST['password'])) sleep(5);
	return "<center><table border=1 cellspacing=0 cellpadding=40><tr><td align=center>
<form method=post action=".$GLOBALS['mypage'].">������ ��� ���� ��������:
<br><input type=text size=20 name=password>&nbsp;<input type=submit value='�����'></form>
</td></tr></table></center>
";

}
?>
