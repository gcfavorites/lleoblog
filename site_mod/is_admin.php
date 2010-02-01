<?php // только для админа

function is_admin($e) { return ($GLOBALS['admin']?$e:''); }

?>
