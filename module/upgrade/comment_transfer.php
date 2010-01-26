<? // Перекачать старую базу комментариев

if(msq_table("dnevnik_comments")) {

$action='Comment_Transfer'; $Nskip=5000;
if($PEST['action']==$action) { $admin_upgrade=true;

//==========================================================================================	
$pp=ms("SELECT * FROM `dnevnik_comments` ORDER BY `DateTime` LIMIT ".intval($_GET['skip']).",".$Nskip,"_a",0);
if($pp!==false && sizeof($pp)) { $s .= admin_rereload($action,$Nskip,5);

$s.= "<p>Всего для обработки: ".ms("SELECT COUNT(*) FROM `dnevnik_comments`","_l")."<p class=br>";

foreach($pp as $p) {

$ara=array();
$ara['DateID']=e($p['DateID']);
$ara['unic']=0;
$ara['Name']=e($p['Name']);
$mail=preg_replace("/^\s*mailto:/si","",$p['Address']);
$mail=mail_validate($mail);
$ara['Mail']=e($mail);
$ara['Text']=e(strtr(trim($p['Commentary']."\n\t "),"\r",""));
$ara['IPN']=ip2ipn($p['IP']);
$ara['Parent']=0;
$ara['Time']=e($p['DateTime']);
$ara['BRO']=e($p['UserAgent']);
$ara['scr']=e($p['metka']);
$ara['golos_plu']=e($p['rulit']);
$ara['golos_min']=e($p['spamit']);
$ara['rul']=($p['rulit_master']==1?'yes':'no');
$ara['ans']='enable';
$ara['whois']=e($p['whois_gorod']."\001".$p['whois_strana']);

if(msq_add('dnevnik_comm',$ara)===false) idie('error!!!'.$GLOBALS['msqe']);
// mysql_insert_id();
// die($id);
// eee

if($p['Answer']!='') {

$ara=array();
$ara['DateID']=e($p['DateID']);
$ara['unic']=1;
$ara['Name']=e($GLOBALS['admin_name']);
$ara['Text']=e(strtr(trim($p['Answer']."\n\t "),"\r",""));
$ara['golos_plu']=e($p['ans_rulit']);
$ara['golos_min']=e($p['ans_spamit']);
$ara['Time']=e($p['Answer_time']);
$ara['Mail']=e($GLOBALS['admin_mail']);
$ara['IPN']=0;
$ara['Parent']=mysql_insert_id();
$ara['BRO']=e($p['Answer_user']);
$ara['scr']=($p['metka']=='open'?1:0);
$ara['rul']=0;
$ara['ans']='enable';
$ara['whois']=e("Москва, Чертаново\001Россия");

if(msq_add('dnevnik_comm',$ara)===false) idie('error!!!'.$GLOBALS['msqe']);

}


$s.="<br>".date("Y-m-d H:i:s",$p['DateTime']).": ".h($ara['Name']);
// <pre>".h(print_r($ara,1))."</pre>";


}

} else 	$s .= admin_redirect($mypage,0);
//==========================================================================================	
} elseif(

	ms("SELECT COUNT(*) FROM `dnevnik_comments`","_l") > ms("SELECT COUNT(*) FROM `dnevnik_comm`","_l")

) { $s .= admin_kletka('action',"перекачать старую базу комментариев в новый формат",$action); }

}
?>