<?php
// ��� ��������� �� �����, � ������ �� ����� ��� ���� ������� �����
//
// ��� ����� ������ ��������� ����� (�������� � ������� {_SECRET_FILE: link _})
//
// ����� �������� ���, ���:
// �) �� ������ ������ �� ������� ����������
// (��� ����� ���������� �� � �����, ��� �������� ���� .htaccess, ���� ���������� ������� ����� ���� 'trololololo')
// �) ������ �� ����� ���� ������������ ���� ��������� �� ��������� - ��� ������� �� IP � ��������

$mimetypes=array(
'jpg'=>'image/jpg',
'jpeg'=>'image/jpg',
'gif'=>'image/gif',
'png'=>'image/png',
'bmp'=>'image/bmp',

'mp3'=>'audio/mp3',
'wav'=>'audio/wav',

'mid'=>'audio/midi',
'txt'=>'text/plain'
);

include "../config.php";

if( $_GET['o'] != md5($hashinput.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_GET['file']) ) die('Error 404: �������');

$f=$filehost.str_replace('..','_',$_GET['file']);

$mime=$mimetypes[strtolower(preg_replace("/^(.*\.)([^\.]+)$/si","$2",basename($f)))];
if(empty($mime)) $mime='application/octet-stream';

if(!file_exists($f)) die("File not found: ".$f);

header('Content-Description: File Transfer');
header('Content-Type: '.$mime);
//header('Content-Disposition: attachment; filename="'.basename($f).'"');
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
