<?php /* ������ ������ �� ��������� ����

��� ����������� �������������� ������� (� ������� ����� ������) �����-������ ����� ��� ����� ������. � ��� �������� ���� .htaccess � ���� ���������� ������� ����� ���� 'trololololo', ����� ����� ����� �� ����������� �� ���-������. �������� �� ����� ����� ������ �� ������ ������, ������ �� ���� �� ��������� ������������, ��������� ��� ������� �� IP � ��������.

���� ���� ������ �������� ����� ����������� |, � �� - ��������� ����� a, �� ��������� ������, ���� ��� - ������ ���������� ��� �����, �� ����������� ������ ������.

���� ������ ��������� ���������, �� ������ ����� ���, ������� �� ������ ������� �� ����:

{_SECRET_FILE: tmp/secret_file/santa.doc | a _}

*/

function SECRET_FILE($e) { global $hashinput,$IP,$BRO;

	list($e,$a)=explode('|',$e,2);
	$e=c($e);

	$url=$GLOBALS['www_ajax']."secret_file.php?o=".substr(md5($hashinput.$IP.$BRO),5,5)."&file=".urlencode($e);

	if(c($a)!='a') return $url;


	$m=explode('/',$e); $m=$m[sizeof($m)-1];
	return "<a href='".$url."'>".h($m)."</a>";

}

?>
