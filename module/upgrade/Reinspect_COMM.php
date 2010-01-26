<?

$action='Reinspect_COMM'; $Nskip=500;
if($PEST['action']==$action) { $admin_upgrade=true;

include_once $GLOBALS['include_sys']."_onecomm.php";
SCRIPTS_mine();
SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js"); // подгрузить внешний скрипт

//==========================================================================================	
$pp=ms("SELECT `Parent`,`DateID`,`id` FROM `dnevnik_comm` WHERE `Parent`!=0 LIMIT ".intval($_GET['skip']).",$Nskip","_a");
if($pp!==false && sizeof($pp)) { $reval=2;

$s.= "<p>Всего для обработки: ".ms("SELECT COUNT(*) FROM `dnevnik_comm`","_l");

foreach($pp as $p) {

	if(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `DateID`='".e($p['DateID'])."' AND `id`='".e($p['Parent'])."'","_l")!=1) {
		$s .= "<br><font color=red>".$p['id']." ".$p['DateID']." ".$p['Parent']."</font> ".$GLOBALS['msqe'];

		$p1=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='".e($p['id'])."'","_1");
		$s .= str_replace('{comment_otstup}','0',comment_one($p1));
		$reval=60;
	}

//	$s .= "<br><font color=green>".$p['id']." ".$p['DateID']." ".$p['Parent']."</font> ".$GLOBALS['msqe'];
//		msq_update('dnevnik_zapisi',array('DateDate'=>$t[0],'DateDatetime'=>$t[1]),"WHERE `num`='".e($l['num'])."'");
//		$s .= "<br>".($n+1+$skip).". <b>".$l['Date']."</b>=<i>".date("Y-m-d H:i:s",$t[1])."</i>".$msqe;
}

$s .= admin_rereload($action,$Nskip,$reval);

} else 	$s .= admin_redirect($mypage,0);
//==========================================================================================	
} else { $s .= admin_kletka('action',"поиск потерянных комментариев",$action); }

?>