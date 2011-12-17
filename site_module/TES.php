<?php

function TES_ajax() { $a=RE('a');
	return "alert(1);";
}

function parss($s,$r1='{_',$r2='_}'){ $d=0; $stop=0; while(++$stop<10) {
	if(($i=strpos($s,$r1))===false || ($j=strpos($s,$r2))===false) return false;
	if(($k=strpos(substr($s,2),$r1))===false || $j<$k) return array($d+$i,$j+2);
	$s=substr($s,$k+2); $d+=$k+2;
} return false;
}



function TES($s) {
	if(($a=parss($s))!==false) return substr($s,$a[0],$a[1]);
	else return 'false';
}
/*



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
*/
?> 
