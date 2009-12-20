<?php // Если пришел комментарий

foreach($_POST as $k=>$v) $_POST[$k]=trim($v,"\t\n\r ");

	if($IS_USER) $_POST["Name"] = $IS_USER;
	if($_POST["Name"] == "") $CommentaryErrors[] = "Не указано имя. Зачем мне комментарии от анонимов?";
	if($_POST["Commentary"] == "") $CommentaryErrors[] = "Не написано самого комментария.";

//if($article['Date']=='2009-04-08' && $_POST["AntiBotCode"]!='&#26222;&#36890;&#35805;') { $CommentaryErrors[] = "Цифры с картинки указаны неверно"; } else
if(!$IS_USER && !antibot_check($_POST["code"],$_POST["hash"])) { $CommentaryErrors[] = "Цифры с картинки указаны неверно"; }

// Если не указан точный тип адреса, то попытаться его угадать
$a=$_POST["Address"]; $b=substr($a,0,7);
if($b!="http://" && $b!="mailto:") { // Пытаемся угадать мыловый адрес
	if(preg_match("/\A([0-9,a-z,_,\.]+\@[0-9,a-z,\-,\.]+\.[0-9,a-z]+)\Z/i", $a)) $a="mailto:".$a;
	// Пытаемся угадать URL
	elseif(preg_match("/\Awww\.[a-z0-9\-]+\.[a-z0-9\/\\\?\&\.\+\-_\#\%]+\Z/i", $a)) $a="http://".$a;
}

if(count($CommentaryErrors) == 0) {

if(!$login) {
	if($lju) { set_cookie("CommentaryName", $_POST["Name"], time()+86400*3, "/", "", 0, true); }
	else { set_cookie("CommentaryName", $_POST["Name"], time()+86400*365, "/", "", 0, true); }
		set_cookie("CommentaryAddress", $a, time()+86400*365, "/", "", 0, true);

	if(!$admin && str_ireplace($admin_name,"",$_POST["Name"]) != $_POST["Name"] )
		if($lj_name) $_POST["Name"]=$lj_name; elseif ($lju) $_POST["Name"]=$lju; else $_POST["Name"]="Мудачог";
}

//-----------------------------------------------------------

$l=$_POST["Commentary"]; if(!$IS_USER) $l.=$_POST["Name"];
$l=preg_replace("/p\.s\./si","",$l);
$spamik=(eregi("[a-z]+\.[a-z]+",$l) || strstr($_POST["Commentary"],'<') ? 1 : 0 );

/*
if(strstr($_POST['Commentary'].$_POST["Name"],'infectedmush')) { $spamik=true; $prichinto='Ваш комментарий не принят. Почему?
Да потому что ты малолетняя соска и спаммер, пиздуй отсюда нахуй, твое место <a href=http://lleo.aha.ru/na/>здесь</a>.';
redirect('http://lleo.aha.ru/na/');
}
*/

/*
if(strstr($_POST['Commentary'],'битаем')) { $spamik=true; $prichinto='Ваш комментарий не принят. Про фильм "Обитаемый остров"
мнения не принимаются, потому что это не тема сегодняшнего обсуждения. Вы задолбали бомбардировать мой дневник своим мнением про
"Обитаемый остров". Высказаться больше негде? Поищите форум или дневник, где это обсуждают. Надеюсь на понимание.'; }
*/

if( $comments_screen==true || $spamik ) $smetka='screen'; else $smetka='open';

$idza = intval(ms("SELECT `idza` FROM `dnevnik_comments` WHERE `Date`='".e($article["Date"])."' ORDER BY `idza` DESC LIMIT 1", '_l',0));

$idza++;

//        include_once('_geoip_all.php'); $a=geoip($_SERVER['REMOTE_ADDR']);

msq_add('dnevnik_comments',array(
	'Date'=>e($article["Date"]),
	'DateID'=>intval($article["num"]),
	'idza'=>$idza,
	'Name'=>e($_POST["Name"]),
	'Address'=>e($_POST["Address"]),
	'Commentary'=>e(	strtr($_POST["Commentary"],"\r",'')	),
	'IP'=>e($_SERVER["REMOTE_ADDR"]),
	'IPx'=>e($_SERVER["HTTP_X_FORWARDED_FOR"]),
	'DateTime'=>time(),
	'Answer_time'=>($smetka=='screen'?0:time()),
	'UserAgent'=>e($_SERVER["HTTP_USER_AGENT"]),
	'Guest_LJ'=>e($lju),
	'Guest_Name'=>e($lj_name),
	'speckod'=>e($sc),
//	'mudak'=>e($mudak),
	'metka'=>e($smetka),
	'whois_gorod'=>e($a['city']), //e(whois_gorod($a)),
	'whois_strana'=>e($a['country']), //e(whois_strana($a)),
	'whois_basa'=>e($a['basa']),
	'login'=>e($IS_USER)
	 )
); // забить в базу
//		ОТСЫЛАТЬ КОММЕНТАРИИ НА ЕМАЙЛ - НЕ НАДО ТЕПЕРЬ
//		commentMail(mysql_insert_id(),$spamik);
////		redirect($article["Day"].".html#comment_".$p["id"]);

	// очистить кэш картинок
	if($spamik) redirect($mypage."?com=link&id=".$sc.($prichinto!=''?'&prichina='.urlencode($prichinto):''));
	else redirect($mypage."?com=ok&id=".$sc);
	}

?>
