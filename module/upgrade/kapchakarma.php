<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// ���������� ������ ���� ������������

$s .= msq_add_pole("unic","capchakarma","TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '�����-����� ������ �������'","�����-�����");

$action='kapchakarma'; $Nskip=100;
if(msq_pole("unic","capchakarma")!==false and $PEST['action']==$action) { $admin_upgrade=true;
//==========================================================================================	
$pp=ms("SELECT `id` FROM `unic` WHERE `capcha`='yes' AND `capchakarma`='0'","_a",0);
$s.="<p>����� ����������: ".sizeof($pp);
foreach($pp as $p) if(msq_update('unic',array('capchakarma'=>1),"WHERE `id`='".$p['id']."'")===false) idie('error!!!'.$GLOBALS['msqe']);
//==========================================================================================	
} else if(!ms("SELECT COUNT(*) FROM `unic` WHERE `capchakarma`!='0'","_l")) {
	$s .= admin_kletka('action',"���������� ����� �� �����-�����",$action);
}
?>