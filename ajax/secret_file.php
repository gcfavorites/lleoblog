<?php
// ��� ��������� �� �����, � ������ �� ����� ��� ���� ������� �����
//
// ��� ����� ������ ��������� ����� (�������� � ������� {_SECRET_FILE: link _})
//
// ����� �������� ���, ���:
// �) �� ������ ������ �� ������� ����������
// (��� ����� ���������� �� � �����, ��� �������� ���� .htaccess, ���� ���������� ������� ����� ���� 'trololololo')
// �) ������ �� ����� ���� ������������ ���� ��������� �� ��������� - ��� ������� �� IP � ��������

include "../config.php";

if( $_GET['o'] != substr(md5($hashinput.$_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]),5,5) ) die('Error 404: �������');

$f=$filehost.str_replace('..','_',$_GET['file']);


// die(basename($file));

if(!file_exists($f)) die("File not found: ".$f);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($f).'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '.filesize($f));
ob_clean();
flush();
readfile($f);
exit;

?>
