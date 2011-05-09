<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

$action='Reinspect_COMM2'; $Nskip=500;
if($PEST['action']==$action) { $admin_upgrade=true;

	include_once $GLOBALS['include_sys']."_onecomm.php";
	SCRIPTS_mine();	SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js"); // подгрузить внешний скрипт
	STYLE_ADD($GLOBALS['www_css']."blog.css");

//==========================================================================================	

$pp=ms("SELECT
t1.id,t1.unic,t1.DateID,t1.Name,t1.Mail,t1.Text,t1.Parent,t1.Time,t1.IPN,t1.BRO,t1.whois,t1.scr,t1.rul,t1.ans,
t1.group,t1.golos_plu,t1.golos_min
FROM `dnevnik_comm` as t1 left join `dnevnik_comm` as t2
on t1.Parent=t2.id 
WHERE t1.parent!='0' and t2.id is null","_a");

foreach($pp as $p) {
		$s .= str_replace('{comment_otstup}','0',comment_one($p,mojno_comment($p),0));
//		$reval=60;
		}
// $s .= admin_rereload($action,$Nskip,$reval);
} // else 	$s .= admin_redirect($mypage,0);
//==========================================================================================	
// } 
else {
	$p=ms("SELECT COUNT(*) FROM `dnevnik_comm` as t1 left join `dnevnik_comm` as t2 on t1.Parent=t2.id 
WHERE t1.parent!='0' and t2.id is null","_l");
	if($p) $s .= admin_kletka('action',"поиск потерянных комментариев: $p",$action);
}

?>