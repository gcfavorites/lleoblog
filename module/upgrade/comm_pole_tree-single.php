<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����


//$s .= msq_add_pole("dnevnik_zapisi","Comment_tree","enum('1','0') default '1'","���������: ����������� �����������/��������");

// ���� �� ���� - �������� � ans ���� u (undef) � ������� ��� ����� �� ��������� �� ���� ����
if(msq_pole('dnevnik_comm','ans')=="enum('1','0')") {

	msq("ALTER TABLE `dnevnik_comm` CHANGE `ans`
	`ans` ENUM('1','0','u') CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT 'u'
	COMMENT '��������� �� ��������� ����������� � ����?'");

	msq("UPDATE `dnevnik_comm` SET `ans`='u'");
	$s.="<p>�������� ���� `dnevnik_comm`: ENUM('1','0'<b>,'u'</u>)";
}


//$s .= msq_del_pole("dnevnik_comm","ans","���� �������");
//$s .= msq_add_pole("dnevnik_comm","ans","enum('1','0') default '1'","���������: ����������� �����������/��������");


?>