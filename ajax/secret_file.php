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
include_once $GLOBALS['include_sys']."_files.php"; // �������� � �������

$file=$_GET['file'];

if( $_GET['o'] != md5($hashinput.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$file) ) die('Error 404: �������');

Exit_SendFILE(realpath($filehost.$file));

?>
