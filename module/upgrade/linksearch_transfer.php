<? // Перекачать старую базу комментариев

// `DateID` int(10) unsigned NOT NULL,

$done1=(msq_pole("dnevnik_link","DateID") and ms("SELECT COUNT(*) FROM `dnevnik_link` WHERE `DateID`=0","_l",0)==0);
$done2=(msq_pole("dnevnik_link","DateID") and ms("SELECT COUNT(*) FROM `dnevnik_link` WHERE `DateID`=0","_l",0)==0);

if($done1) $s .= msq_add_pole("dnevnik_link","DateID","int(10) unsigned NOT NULL","переход на другую систему");
if($done2) $s .= msq_add_pole("dnevnik_search","DateID","int(10) unsigned NOT NULL","переход на другую систему");

if($done1 and msq_pole("dnevnik_link","mypage")) $s .= msq_del_pole("dnevnik_link","mypage","поле mypage уже не нужно, все скопировано, предлагаю удалить");
if($done1 and msq_pole("dnevnik_search","mypage")) $s .= msq_del_pole("dnevnik_search","mypage","поле mypage уже не нужно, все скопировано, предлагаю удалить");

$action='LinkSearch_Transfer'; $Nskip=100;
if((!$done1 or !$done2) and $PEST['action']==$action) { $admin_upgrade=true;

//==========================================================================================	
//$basa='dnevnik_search';
$basa='dnevnik_link';

$pp=ms("SELECT * FROM `$basa` WHERE `DateID`=0 LIMIT $Nskip","_a",0);
if($pp!==false && sizeof($pp)) { $s .= admin_rereload($action,$Nskip,5);

$s.= "<p>Всего для обработки: ".ms("SELECT COUNT(*) FROM `$basa` WHERE `DateID`=0","_l")."<p class=br>";

foreach($pp as $p) {
	$url=$p['mypage'];
	$w=$GLOBALS['wwwhost'];
	if(substr($url,0,strlen($w))!=$w) idie("<br>error: <font color=red>".h($url)."</font>");
		$url=substr($url,strlen($w));
		$dat=substr($url,0,strlen('0000/00/00'));
		if(strtotime(strtr($dat,'/','-')) != 0) $url=str_replace('.html','',$url);
		$num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($url)."'","_l");
		if($num===false) idie('error!');
	$s.="<br>".h($url)." номер # ".$num;
	$ara=array(); $ara['DateID']=$num;
	if(msq_update($basa,$ara,"WHERE `n`='".$p['n']."'")===false) idie('error!!!'.$GLOBALS['msqe']);
}


} else {

$basa='dnevnik_search';

$pp=ms("SELECT * FROM `$basa` WHERE `DateID`=0 LIMIT $Nskip","_a",0);

if($pp!==false && sizeof($pp)) { $s .= admin_rereload($action,$Nskip,5);

$s.= "<p>Всего для обработки: ".ms("SELECT COUNT(*) FROM `$basa` WHERE `DateID`=0","_l")."<p class=br>";

foreach($pp as $p) {
	$url=$p['mypage'];
	$w=$GLOBALS['wwwhost'];
	if(substr($url,0,strlen($w))!=$w) idie("<br>error: <font color=red>".h($url)."</font>");
		$url=substr($url,strlen($w));
		$dat=substr($url,0,strlen('0000/00/00'));
		if(strtotime(strtr($dat,'/','-')) != 0) $url=str_replace('.html','',$url);
		$num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($url)."'","_l");
		if($num===false) idie('error!');
	$s.="<br>".h($url)." номер # ".$num;
	$ara=array(); $ara['DateID']=$num;
	if(msq_update($basa,$ara,"WHERE `n`='".$p['n']."'")===false) idie('error!!!'.$GLOBALS['msqe']);
}

} else $s .= admin_redirect($mypage,0);

}

//==========================================================================================	
} elseif(!$done1 or !$done2) { $s .= admin_kletka('action',"перекачать старую базу комментариев в новый формат",$action); }


?>