<?php // Перекачать старую базу логинов

if(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `ans`='enable' OR `ans`='disable'","_l",0)) {

$action='ans-rul-scr_convert'; if($PEST['action']==$action) { $admin_upgrade=true;

ms("ALTER TABLE `dnevnik_comm`
CHANGE `scr` `scr` ENUM('1','0') NOT NULL DEFAULT '0' COMMENT 'Общедоступность',
CHANGE `rul` `rul` ENUM('1','0') NOT NULL DEFAULT '0' COMMENT 'Особая пометка',
CHANGE `ans` `ans` ENUM('1','0') NOT NULL DEFAULT '1' COMMENT 'Разрешено ли принимать комментарии к нему?'
","_l",0);

} else { $s .= admin_kletka('action',"Исправить старые значения полей ans, scr и rul",$action); }

}

?>