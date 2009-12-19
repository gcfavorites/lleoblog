<?

$s .= msq_del_pole("dnevnik_zapisi","DateDatetime","удалить");
$s .= msq_add_pole("dnevnik_zapisi","DateDatetime","int(11) NOT NULL default '0'","для точного позиционирования по дням");
$s .= msq_add_index("dnevnik_zapisi","DateDatetime","(`DateDatetime`)","индекс нужен");

$s .= msq_del_pole("dnevnik_zapisi","DateDate","удалить");
$s .= msq_add_pole("dnevnik_zapisi","DateDate","int(11) NOT NULL default '0'","для точного позиционирования по дням");
$s .= msq_add_index("dnevnik_zapisi","DateDate","(`DateDate`)","индекс нужен");


// $s .= msq_del_index("dnevnik_zapisi","DateDatetime","индекс не нужен"); // `combined` (`i1`,`i2`)

$action='Reinspect_Date'; $Nskip=100;
if($PEST['action']==$action) { $admin_upgrade=true;
//==========================================================================================	
$p=ms("SELECT `num`,`Date` FROM `dnevnik_zapisi` WHERE `DateDatetime`=0 AND `Date` LIKE '____/__/__%' ORDER BY `Date` LIMIT ".$Nskip,"_a",0);
if($p!==false && sizeof($p)) {

	$s .= admin_rereload($action,$Nskip,200);

	foreach($p as $n=>$l) {
		$t=getmaketime($l['Date']);
		msq_update('dnevnik_zapisi',array('DateDate'=>$t[0],'DateDatetime'=>$t[1]),"WHERE `num`='".e($l['num'])."'");
		$s .= "<br>".($n+1+$skip).". <b>".$l['Date']."</b>=<i>".date("Y-m-d H:i:s",$t[1])."</i>".$msqe;
	}

}
//==========================================================================================	
} else { $s .= admin_kletka('action',"оптимизация даты",$action); }



function getmaketime($d) {
	preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d)(.*?)$/s",$d,$m);
	$d=$m[1]."-".$m[2]."-".$m[3];
	$t0=strtotime($d);
	if(preg_match("/^[\-_\s]*(\d\d)-(\d\d)/s",$m[4],$t)) $d .= " ".$t[1].":".$t[2];
	$t=strtotime($d);
	while(msq_exist('dnevnik_zapisi',"WHERE `DateDatetime`='$t'")) $t++;
	return array($t0,$t);
}


?>