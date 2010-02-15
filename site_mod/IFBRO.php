<?php // ≈сли строка в браузере - выдает один аргумент, если нет - второй

// {_ linux | это линукс | это не линукс _}

function IFBRO($e) { list($l,$a,$b)=explode('|',$e);
	return ( stristr($GLOBALS['BRO'],c($l)) ? c($a) : c($b) );
}

?>
