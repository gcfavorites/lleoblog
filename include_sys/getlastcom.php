<?php // �����������

function getlastcom(){ global $lim,$admin,$mode,$lastcom,$ncom; if(isset($lastcom)) return;

$namecomlast="comlast";
$admin_comment_last=$GLOBALS['hosttmp'].$namecomlast.".txt"; // ���� ����� ������������, �� ������ ����������� ����� ��� ����������
$lim=50;

// ����� ������-������ ������
$mode=$_GET['mode'];

$l=intval(strtotime(preg_replace("/(\d\d\d\d-\d\d-\d\d)_(\d\d)-(\d\d)-(\d\d)/si","$1 $2:$3:$4",$_GET['lastcom'])));
//if(!$l) $l=intval($admin?fileget($admin_comment_last):$_COOKIE[$namecomlast]);
//if(!$l) $l=intval($admin?fileget($admin_comment_last):$_COOKIE[$namecomlast]);

if($_GET['ncom']=='prev') $l=-$l;

if(!$l && !$mode){ if($admin) $l=intval(fileget($admin_comment_last)); else $l=intval($_COOKIE[$namecomlast]); }

// �������������, ���� ���� ������� ������, ����� ������� ���� � ������ �����
if(!$l) { $lastcom=time(); $ncom='-'; if(!$mode) $s .= '�� ��� �������? ����� ��� ��������� '.$lim; }
elseif($l<0) { $lastcom=-$l; $ncom='-'; } else { $lastcom=$l; $ncom=''; }



if(!$mode) { // ��������� ��������� ���������
	$l=$ncom.$lastcom;
	setcoo($namecomlast,$l);
	if($admin) if(!fileput($admin_comment_last,$l)) $er[]="�� ������� �������� ���� <b>$admin_comment_last</b>, ��������� ����� ����� �� ������"; else chmod($admin_comment_last,0666);
}
}
//	include_once $GLOBALS['include_sys']."getlastcom.php"; getlastcom();
?>