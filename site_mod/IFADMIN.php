<?php

// Если админ - выдает один аргумент, если нет - второй
// {_IFADMIN: <p>Ваш пароль: 1223HsdnD! | Пшел нахуй, ты не админ! _}

function IFADMIN($e) {
	list($a,$b)=explode('|',$e);
	return ($GLOBALS['admin'] ? c($a) : c($b) );
}

?>
