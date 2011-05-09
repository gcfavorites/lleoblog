<?php

function TES_ajax() { $a=RE('a');

	return "alert(1);";

}


function TES($e) {

return mail_validate($e);


$sq="SELECT z.`num`
 FROM `dnevnik_zapisi` as z
 LEFT JOIN `dnevnik_posetil` as p
 ON z.`num`=p.`url`
 WHERE p.`unic`='2' AND p.`url` IS NULL
 LIMIT 10";



$pp=ms($sq,"_a");
// dier($pp,$sq." #".$GLOBALS['msqe']);

return $s;
}
?> 
