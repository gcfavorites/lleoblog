<?php

function installmod_init() { if(msq_pole('dnevnik_zapisi','Body')!="text") return false;
	return "����� ����������� 64�� ��� �������";
}

function installmod_do() {
	msq("ALTER TABLE `dnevnik_zapisi` CHANGE `Body` `Body` MEDIUMTEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL COMMENT '����� ������� (�� 16�)'");
	return "�������� ���� `dnevnik_zapisi`: ������ ��� ����������� � 64�� ������";
}

?>