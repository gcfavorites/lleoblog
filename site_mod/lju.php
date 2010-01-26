<?php // ЖЖ определялка

function lju($e) { global $lju; if($lju=='') return ''; else return str_replace('$lju',$lju,$e); }

?>
