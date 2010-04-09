<?php

if(msq_pole('dnevnik_zapisi','Body')=="text") {

	$action='Body-64kb_to_16Mb'; if($PEST['action']==$action) { $admin_upgrade=true;
		msq("ALTER TABLE `dnevnik_zapisi` CHANGE `Body` `Body` MEDIUMTEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL COMMENT 'Текст заметки (до 16М)'");
		$s.="<p>Изменена база `dnevnik_zapisi`: теперь нет ограничения в 64кб текста";
	} else { $s .= admin_kletka('action',"Снять ограничение в 64кб для заметок?",$action); }

}

?>