<?php
// ������� ����� ��-����� �������-������

function SNAPSHOT($e) {

if(strstr($GLOBALS['BRO'],"SnapPreviewBot")) { 

	ob_end_clean();

	die("<html><body bgcolor=red><font size=6 color=black><p><br>"
.(rand(0,1)==0?"���� ����� ������� ������<br>��� � ����� ����� ��������<br>�� ������ ������� ����<br>� ��� � ��������"
		:"��� ���������� Snap-Shots<br>������������ ����-�")
."</body></html>");

}

}

?>