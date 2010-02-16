<?php // ≈сли строка в браузере - выдает один аргумент, если нет - второй

// {_IFBRP: linux,nokia | это линукс или ноки€ | это не линукс и не ноки€ _}

function IFBRO($e) {
	list($l,$a,$b)=explode('|',$e);
	$p=explode(',',$l);
	foreach($p as $l) if(stristr($GLOBALS['BRO'],c($l))) return c($a);
	return c($b);
}

?>
