<?php

# �������������� ���������

# ��� ������ ������ (��� ������ �� ��������! ��� ����������� ���������� ������ ���� ������, ����� ������ ��� ������, ��� �������!)
	$admin_hash="";

# �������
	$httpsite = "http://lleo.aha.ru";
	$blogdir = "blog/";

# ��������� mysql
	$msq_host = "localhost";
	$msq_login = "lleo";
	$msq_pass = "f56DfY8e3";
	$msq_basa = "lleo";
	$msq_charset = "cp1251";

# ��� �����
	$admin_name = "������ �������";
	$admin_mail = "lleo@aha.ru";
	$blog_name = "lleo.aha.ru";
	$admin_site = "lleo.aha.ru"; // ��� ����� ������ �� ������� - ���� ����� �������� �� ����, � ����

$signature = "&copy; ".$admin_name."&nbsp;&nbsp;<a href='mailto:".$admin_mail."'>".$admin_mail."</a>";

# ������ ��� ����������� ������ (�����, �� �����)
	$koldunstvo = "���� ���� ���� ���� ���� �������� ������";
	$hashinput = "���� �� �� ����� ������ �������, �� ��������� � ��������"; // ������ ��� ������ ������� ������ ��������
	$hashlogin = "����������������"; // ���������� ������ ����� - ���� ��������, ��������� ��� ������������ �����


# memcache - �� ��������� ��������, ������� ������ ��� �������, ���� �� � ��� ����������
$memcache = false;


# ��������� RSS �����
$RSSZ_skip = 10; // ������� �������� �� ��� � RSS �������
$RSSC_skip = 30; // ������� �������� �� ��� � RSS ������������
$RSSZ_mode = 0; // 0 - �������� ������ ����� � rss, 1 - ������ ��������� �����

# �����, ��� ������� ������ ���������� �����
# $host = "/home/lleo/www/";
$host = rtrim($_SERVER["DOCUMENT_ROOT"],'/').'/';
$wwwhost = "/".$blogdir; // ����� ����� ������������ ������� ������� (/dnevnik/)
$httphost = $httpsite.$wwwhost; // ������ ����� ����� (http://lleo.aha.ru/dnevnik/)
$filehost = $host.$blogdir; // ���������� ����� ����� �� �������
$wwwtmp = $wwwhost."tmp/"; // ����� ��������� ������
$hosttmp = $filehost."tmp/"; // ���� �� ��������� ������
$cookie_site = ""; // �� ����� ������ ��������� ����

# ��������� ������������� ��� ���������
$host_log = $filehost."log/"; // ���� ����� �������� ����
$wwwcharset = "windows-1251"; // ��������� ������� �����
$syscharset = "koi8-r"; // ��������� �������� ������� �������
$include_sys = $filehost."include_sys/"; // ����������, ��� ����� ��������� ������
$include_blog = $filehost."include_blog/"; // ����������, ��� ����� ������ �����
$host_design = $filehost."design/"; // ����������, ��� ����� ������
$www_design = $wwwhost."design/"; // ����������, ��� ����� ������
$host_module = $filehost."module/"; // ����������, ��� ����� ������
$www_ico = $wwwhost."design/ico/"; // ����������, ��� ����� ������


############ ������ ###########################################################################
$db_pravka='pravki'; // ��� ������� MySQL, ���� ���������� ������.
$pravka_paranoid=true; // true - ���������� � ���� ���� ����������� ������, ����� ����� ����� ���� ��������
$pravki_npage=50; // ������� ���������� ������ �� ������ �������� ��� �������� ������

############ ������ ###########################################################################
$db_login="login"; // ��� ���� �������
#########################################################################################

$enter_comentary_days = 7; // ����� ����, ����� ������� ��������
$N_maxkomm = 20000; // ����� ��������� ����������� ��������������


############ ������� ###########################################################################
$antibot_pic = $include_sys."antibot_pic/";        // ���� �� ����� � ����������
$antibot_cash = "antibot_cash/";
$antibot_file = $hosttmp.$antibot_cash; // ���� �� ����� � ��������� �������
$antibot_www = $wwwtmp.$antibot_cash; // ����� ����� � ��������� ������� ��� ����
$antibot_C = 3;   // ������� ����� ��������
$antibot_W = 18;   // ������ � ������ ��������
$antibot_H = 20;
$antibot_add2hash = $_SERVER["REMOTE_ADDR"].$hashinput;    // ��������� ��� ����
$antibot_deltime = 60*60; // ������� ������ �������� ����� 1 ���


### ����-�������� ##############################################################################
$db_site = "site";
$site_mod = $filehost."site_mod/"; // ����������, ��� ����� ������������ ������ ������ �����

### �������� � ������� #########################################################################
$fileget_tmp = $hosttmp."get/";

#gb = content
# ���� �������
$db_login = "login";



### �������� ����� #########################################################################
// $foto_orig  ������������ ������� �� ��������� - ������ ������������ ����� (small)
$foto_small="photo/";  // ���� ����� �������� ������������ ���������� �����
$foto_preview="photo/pre/"; // ���� ����� �������� ��������� ���������� �����


$foto_file_small=$filehost.$foto_small;
$foto_www_small=$wwwhost.$foto_small;
$foto_res_small=600;
$foto_qality_small=85;

$foto_file_preview=$filehost.$foto_preview;
$foto_www_preview=$wwwhost.$foto_preview;
$foto_res_preview=100;
$foto_qality_preview=70;

$foto_ttf=$host_design."ttf/MTCORSVA.TTF";
$foto_logo=chr(169)." ".chr(171)."���� ������� �������� http://lleo.aha.ru/blog/".chr(187); // �������������� ������� �����
/* chr(160) nbsp chr(169) copy chr(151) mdash chr(171) chr(187) ltgt */
