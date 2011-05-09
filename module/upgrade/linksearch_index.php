<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

$s .= msq_add_index("dnevnik_link","DateID","(`DateID`)","индекс нужен");
$s .= msq_add_index("dnevnik_link","last_ipn","(`last_ipn`)","индекс нужен");

$s .= msq_add_index("dnevnik_search","DateID","(`DateID`)","индекс нужен");
$s .= msq_add_index("dnevnik_search","last_ipn","(`last_ipn`)","индекс нужен");

?>