<?
// ������� ����� ��-����� �������-������

if(strstr($BRO,"SnapPreviewBot")) { 
	$s = (rand(0,1)==0
		?"���� ����� ������� ������<br>��� � ����� ����� ��������<br>�� ������ ������� ����<br>� ��� � ��������";
		:"��� ���������� Snap-Shots<br>������������ ����-�")

	ob_end_clean();

	die("<html><body bgcolor=red><font size=6 color=black><p><br>".$s."</body></html>");
}

?>
