<?php

function installmod_init() { if(msq_index('site','name')===1) return false;

//	$pp=ms("SHOW INDEX FROM `dnevnik_zapisi`","_a",0);
//	dier($pp);

	return "Добавить первичный индекс в `site`"; // .msq_index1('dnevnik_zapisi','num');

}

function installmod_do() {
	msq_del_index('site','name');
	msq("ALTER TABLE `site` ADD PRIMARY KEY(`name`)");
	return "Изменена таблица `site`, добавлен индекс";
}

?>