<?

// $s .= msq_del_pole("dnevnik_zapisi","wwwwwww","��� �������� �����, � �� ������ ������ ���");
// $s .= msq_add_pole("dnevnik_zapisi","wwwwwww","enum('no','p','pd') default 'no'","��� �������� ����� �� ����� �������!");

$s .= msq_add_pole("dnevnik_zapisi","autoformat","enum('no','p','pd') default 'no'","���������� ������������������ �������");
$s .= msq_add_pole("dnevnik_zapisi","autokaw","enum('auto','no')","����� ����� ����� ��������� ������������� ������� � ����");
$s .= msq_add_pole("dnevnik_zapisi","count_comments_open","int(10) unsigned default '0'","��� ������� � ������������ ������");
$s .= msq_del_pole("dnevnik_zapisi","include","���� include ������ �� �����, �� ������� �� ������� ������� II ���������");

$s .= msq_add_pole("dnevnik_zapisi","template","varchar(32) NOT NULL default 'blog'","�������� ������� �������� � ������ �������");


/*
$action='go';
$timesec=10;
$id='tiktime';

SCRIPTS("var tiktimen=".$timesec.";
function tiktime(id) { document.getElementById(id).innerHTML = tiktimen--;
setTimeout(\"tiktime('\" + id + \"')\", 1000);
}");


if($_GET['action']==$action) {
	$GLOBALS['admin_upgrade']=true;
	$s .= admin_kletka('action',"<font color=red>������ �� �������! �������� ����������� ����:
<span id='".$id."'><script>tiktime('".$id."')</script></span>! ����: <blink>$skip</blink></font>");
	$path = $mypage."?skip=".($skip+1)."&action=".$action;
	$s .= "<noscript><meta http-equiv=refresh content=\"".$timesec.";url=\"".$path."\"></noscript><script> setTimeout(\"location.replace('".$path."')\", ".($timesec*1000)."); </script>";
} else {
	$s .= admin_kletka('action',"����� ������ ������ ����",'go');
}

*/


?>