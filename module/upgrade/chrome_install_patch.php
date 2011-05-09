<?php // браузер Хром - мудак, из-за его закидонов посыпалась вся авторзация
if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

$f=file_get_contents('install.php');
if(strstr($f,'.$'.'_SERVER["HTTP_ACCEPT"].')) {
	$c=file_put_contents('install.php',str_replace('.$'.'_SERVER["HTTP_ACCEPT"].','.',$f));
	$s .= "Old version install.php patch ".($c===false?" - ERROR WRITE FILE install.php!":" - <font color=green>DONE!</font>");
}

?>