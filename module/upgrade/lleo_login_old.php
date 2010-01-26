<? // Перекачать старую базу логинов

//$s .= msq_del_pole("dnevnik_zapisi","DateDatetime","не трогайте!");
//$s .= msq_del_pole("dnevnik_zapisi","Prev","Оно нахуй не нужно, это была моя ошибка!");
//$s .= msq_del_pole("dnevnik_zapisi","Next","Оно нахуй не нужно, это была моя ошибка!");
//$s .= msq_add_pole("unic","DateDatetime","int(11) NOT NULL default '0'","для точного позиционирования по дням");
//$s .= msq_add_pole("unic","DateDatetime","int(11) NOT NULL default '0'","для точного позиционирования по дням");
//$s .= msq_add_index("dnevnik_zapisi","DateDatetime","(`DateDatetime`)","индекс нужен");
//$s .= msq_del_pole("dnevnik_zapisi","DateDate","удалить");
//$s .= msq_add_pole("dnevnik_zapisi","DateDate","int(11) NOT NULL default '0'","для точного позиционирования по дням");
//$s .= msq_add_index("dnevnik_zapisi","DateDate","(`DateDate`)","индекс нужен");
//$s .= msq_del_index("dnevnik_zapisi","DateDatetime","индекс не нужен"); // `combined` (`i1`,`i2`)

$action='Restore_Logins'; $Nskip=100;
if($PEST['action']==$action) { $admin_upgrade=true;

if(!ms("SELECT COUNT(*) FROM `unic`","_l",0)) {

ms("INSERT INTO `unic` (`id`, `login`, `openid`, `obr`, `lju`, `password`, `realname`, `mail`, `site`, `birth`, `admin`, `ipn`, `time_reg`, `timelast`, `capcha`) VALUES
(1, 'lleo.aha.ru', '', 'realname', 'lleo', '6e37bb2702e43103229efca68dd5d5d7', 'Leonid Kaganov', 'lleo@aha.ru', '', '1972-05-21', 'user', 1428094064, 1262122928, '2009-12-30 05:43:11', 'yes'),
(2, 'lleo-nokia', '', 'realname', 'lleo', '5102f08bc93d96b1937e7ee88b7d72ba', 'lleo nokia', '', '', '0000-00-00', 'user', 1428094064, 1262123015, '2009-12-30 01:47:47', 'yes'),
(3, 'lleo-opera', '', 'realname', '', '5102f08bc93d96b1937e7ee88b7d72ba', 'Леонид Каганов, opera', '', '', '0000-00-00', 'user', 1428094064, 1262123134, '2010-01-05 01:59:33', 'yes');
");

}


//==========================================================================================	
$pp=ms("SELECT * FROM `lleo`.`login` LIMIT ".intval($_GET['skip']).",".$Nskip,"_a",0);
if($pp!==false && sizeof($pp)) { $s .= admin_rereload($action,$Nskip,3);

	$s.= "<p>Всего для обработки: ".ms("SELECT COUNT(*) FROM `lleo`.`login`","_l")."<p class=br>";

foreach($pp as $p) {

$log=$p['login'];
//$log=strtolower($log);
$log=trim($log," \t\r\n");
$log=preg_replace("/^www./i","",$log);

$ara=array(
'lju'=>e($p['lju']),
'time_reg'=>e(strtotime($p['timereg'])),
'timelast'=>e($p['timelast']),
'realname'=>$p['realname'],
'mail'=>e($p['mail']),
'site'=>e($p['site']),
'birth'=>e($p['birth']),
'admin'=>e($p['admin']),
'password'=>e($p['password']) );

if(preg_match("/[^0-9a-z\-\_]/i",$log)) {
	$ara['openid']=e($log);
	$ara['obr']='openid';
} else {
	$ara['login']=e($log);
	$ara['obr']='login';
}

if(preg_match("/[^0-9a-z\-\_\.\/\~\=\@]/i",$log)) { $s.="<br><font color=red>Неверный логин: '".h($log)."'</font>"; 
logi('neverlog.txt',"-------------------\n".$log."\n".print_r($ara,1)); continue; }

$iii=ms("SELECT `id` FROM `unic` WHERE `login`='".e($log)."' OR `openid`='".e($log)."'","_l",0);

if($iii) { $s.="<br>".h($log)." - <font color=red>есть как номер ".$iii."</font>";
} else {
	msq_add('unic',$ara);
	$s.="<br>".h($log)." - <font color=green>update</font>";
	//$s.="<br><pre>".print_r($ara,1)."</pre>";
}

//die($s); 
// dier($p);

}

} else 	$s .= admin_redirect($mypage,0);
//==========================================================================================	
} elseif(

msq_table("`lleo`.`login`") and ms("SELECT COUNT(*) FROM `lleo`.`login`","_l") > ms("SELECT COUNT(*) FROM `unic`","_l")

) { $s .= admin_kletka('action',"перекачать старые логины в новый блог",$action); }


?>