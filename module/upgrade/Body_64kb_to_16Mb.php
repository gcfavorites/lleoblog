<?php

if(msq_pole('dnevnik_zapisi','Body')=="text") {

	$action='Body-64kb_to_16Mb'; if($PEST['action']==$action) { $admin_upgrade=true;
		msq("ALTER TABLE `dnevnik_zapisi` CHANGE `Body` `Body` MEDIUMTEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL COMMENT '����� ������� (�� 16�)'");
		$s.="<p>�������� ���� `dnevnik_zapisi`: ������ ��� ����������� � 64�� ������";
	} else { $s .= admin_kletka('action',"����� ����������� � 64�� ��� �������?",$action); }

}

?>