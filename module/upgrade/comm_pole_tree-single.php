<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй


//$s .= msq_add_pole("dnevnik_zapisi","Comment_tree","enum('1','0') default '1'","настройка: комментарии древовидные/линейные");

// если не было - добавить в ans поле u (undef) и сделать его полем по умолчанию по всей базе
if(msq_pole('dnevnik_comm','ans')=="enum('1','0')") {

	msq("ALTER TABLE `dnevnik_comm` CHANGE `ans`
	`ans` ENUM('1','0','u') CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT 'u'
	COMMENT 'Разрешено ли принимать комментарии к нему?'");

	msq("UPDATE `dnevnik_comm` SET `ans`='u'");
	$s.="<p>Изменена база `dnevnik_comm`: ENUM('1','0'<b>,'u'</u>)";
}


//$s .= msq_del_pole("dnevnik_comm","ans","надо удалить");
//$s .= msq_add_pole("dnevnik_comm","ans","enum('1','0') default '1'","настройка: комментарии древовидные/линейные");


?>