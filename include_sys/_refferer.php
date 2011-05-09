<?php

function refferer($ref,$DateID) { global $IPNUM,$unic;

if(!$unic || 0!=ms("SELECT COUNT(*) FROM `dnevnik_posetil` WHERE `unic`='$unic' AND `url`='$DateID'","_l",0)) return false;

	if(striplink($ref)) return false;

	$u=poiskovik($ref);

if($u[0]!="") { // если поиск - дополнить базу `dnevnik_search`
	if($GLOBALS['admin']) idie($ref);
	$n=intval(ms("SELECT `n` FROM `dnevnik_search` WHERE `DateID`='$DateID' AND `search`='".e($u[0])."'","_l",0));
	if($n) ms("UPDATE `dnevnik_search` SET count=count+1 WHERE `n`='$n'","_l",0); // + счетчик
	else msq_add("dnevnik_search",array("DateID"=>$DateID,"poiskovik"=>e($u[1]),"search"=>e($u[0]),"link"=>e($ref),"count"=>1));
} else { // иначе дополнить базу `dnevnik_link`
	$n=intval(ms("SELECT `n` FROM `dnevnik_link` WHERE `DateID`='$DateID' AND `link`='".e($ref)."'","_l",0));
	if($n) ms("UPDATE `dnevnik_link` SET count=count+1 WHERE `n`='$n'","_l",0);
	else msq_add("dnevnik_link",array("DateID"=>$DateID,"link"=>e($ref),"count"=>1));
}

return $u;
}

function striplink($l) {

if(strstr($l,$GLOBALS['admin_site'])) return true;
if(strstr($l,'www.google.') && strstr($l,'/reader/')) return true;
if(strstr($l,'.livejournal.com') && strstr($l,'/friends')) return true; // из френдленты
if(strstr($l,'yandex.ru/read.xml') or strstr($l,'yandex.ru/unread.xml') or strstr($l,'blogs.yandex.ru') or strstr($l,'lenta.yandex.ru')
)  return true; // €ндексовые читалки

/*

http://www.google.ru/reader/view/?hl=ru&tab=wy
http://www.google.ru/reader/view/
http://www.google.com/reader/view/?tab=my
http://www.google.com/reader/view/

if( ( strstr($l,'.livejournal.com') && strstr($l,'/friends') ) // из френдленты
//    || strstr($l,'yandex.ru/top/') // топы
//    || strstr($l,'blog.yandex.ru') // топы
//    || strstr($l,'blogs.yandex.ru') // топы
    || strstr($l,'yandex.ru/read.xml') // €ндексовые читалки
    || strstr($l,'yandex.ru/unread.xml') // €ндексовые читалки
    || strstr($l,'bloglines.com') // блоглайнс какой-то
    || strstr($l,'graveron.fatal.ru') // спаммер что ли?
    || ( strstr($l,'www.google.') && strstr($l,'/reader/') )  // google reader
) return true;
*/

return false;
}


// =======================================================================


function poiskovik($urlo) {

	if(strstr($urlo,$GLOBALS['httphost'])) return false;

$u=parse_url($urlo); // $_SERVER["HTTP_REFERER"];
parse_str($u['query'],$outr);

// GOOGLE
if (strstr($u["host"],"google.") && !strstr($u["host"],"/reader/") ) {
	$s[2]=urldecode($outr['q']." ".$outr['as_epq']);


 //." ".$outr['btnG']);
	$s[0]=iconv('utf-8','windows-1251//IGNORE',$s[2]);
	$s[1]="Google";
} // http://www.google.ru/search?as_q=+&complete=1&hl=ru&newwindow=1&num=50&btnG=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA+%D0%B2+Google&
//as_epq=%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D1%83+%D0%BF%D1%80%D0%B8%D0%B2%D0%B8%D0%BD%D1%82%D0%B8%D0%BB+%D0%B7%D0%B0%D0%B1%D0%B0%D0%B2%D0%BD%D0%B8%D1%81%D1%82%D1%83%D1%8E&as_oq=%D0%BD%D0%B5+%D0%BF%D0%BE%D0%BB%D1%83%D1%87%D0%B0%D0%B5%D1%82%D1%81%D1%8F&as_eq=&lr=&cr=&as_ft=i&as_filetype=&as_qdr=all&as_occt=any&as_dt=i&as_sitesearch=&as_rights=&safe=off
//http://www.google.com/reader/view/ - RSS-рассылка

// YANDEX-search
elseif (strstr($u["host"].$u["path"],"yandex.ru/yandsearch")) {
	$s[2]=urldecode($outr['text']);
	$s[0]=$s[2];
	$s[1]="Yandex";
} 

// YANDEX-yandpage
elseif (strstr($u["host"].$u["path"],"yandex.ru/yandpage")) {
	parse_str(urldecode($outr['qs']),$outr2);
	$s[2]=urldecode($outr2['text']);
	$s[0]=iconv('koi8-r','windows-1251//IGNORE',$s[2]);
	$s[1]="Yandex-page";
} 

// RAMBLER
elseif (strstr($u["host"],"rambler.ru")) {
	$s[2]=urldecode($outr['words']." ".$outr['old_q']);
	$s[0]=$s[2]; 
	 $k_koi=strlen(str_replace("-","",strtr($searchtext," √’Ћ≈ќ«џЁЏ»я∆ў„Ѕ–“ѕћƒ÷№—ё”Ќ…‘Ў¬ј","--------------------------------")));
	 $k_win=strlen(str_replace("-","",strtr($searchtext,"кгхлеозыэъи€жщчбртпмдцьсюунйфшва","--------------------------------")));
	 if ($k_koi < $k_win) $s[0]=iconv('cp1251','koi8-r//IGNORE',$s[0]);
	$s[1]="Rambler";
} 

// GO.MAIL.RU
elseif (strstr($u["host"].$u["path"],"go.mail.ru/search")) {
	$s[2]=urldecode($outr['q']." ".$outr['old_q']);
	$s[0]=$s[2];
	$s[1]="Go.mail.ru";
} 

// MSN
elseif (strstr($u["host"].$u["path"],"search.msn.com")) {
	$s[2]=urldecode($outr['q']." ".$outr['old_q']);
	$s[0]=$s[2];
	$s[1]="msn";
}  //  http://search.msn.com/results.aspx?q=%D0%BF%D0%BE%D0%BB%D1%83%D1%87%D0%B5%D0%BD%D0%B8%D0%B5+%D0%BF%D0%BE%D0%B2%D0%B0%D1%80%D0%B5%D0%BD%D0%BD%D0%BE%D0%B9+%D1%81%D0%BE%D0%BB%D0%B8&form=QBRE

// YAHOO
elseif (strstr($u["host"],"search.yahoo.com")) {
	$s[2]=urldecode($outr['p']);
	$s[0]=$s[2];
	$s[1]="yahoo";
	if($outr['ei']=='UTF-8') $s[0]=iconv("utf-8","cp1251//IGNORE",$s[0]);
}  //http://search.yahoo.com/search;_ylt=A0geu7e3IMJHbZgABa9XNyoA?p=%D1%81%D0%B8%D0%BB%D0%B0+%D0%B5%D1%81%D1%82%D1%8C+%D1%83%D0%BC%D0%B0+%D0%BD%D0%B5+%D0%BD%D0%B0%D0%B4%D0%BE+%D0%BA%D0%BD%D1%8F%D0%B7%D1%8C+%D0%B2%D0%BB%D0%B0%D0%B4%D0%B8%D0%BC%D0%B8%D1%80&y=Search&fr=yfp-t-501&ei=UTF-8

// LIVE.COM
elseif (strstr($u["host"],"search.live.com")) {
	$s[2]=urldecode($outr['q']);
	$s[0]=$s[2];
	$s[1]="live.com";
} // http://search.live.com/results.aspx?srch=105&FORM=AS5&q=%d0%a4%d0%be%d1%82%d0%ba%d0%b8+%d0%b8%d0%b7+%d0%b3%d0%b5%d1%80%d0%bc%d0%b0%d0%bd%d0%b8%d0%b8


// NIGMA
elseif (strstr($u["host"],"nigma.ru")) {
	$s[2]=urldecode($outr['s']);
	$s[0]=$s[2];
	$s[1]="nigma.ru";
} //http://www.nigma.ru/index.php?s=%D1%84%D0%B8%D0%BB%D1%8C%D0%BC+%D0%92%D0%BE%D1%81%D0%BF%D0%BE%D0%BC%D0%B8%D0%BD%D0%B0%D0%BD%D0%B8%D1%8F+%D0%BE+%D0%B1%D1%83%D0%B4%D1%83%D1%89%D0%B5%D0%BC&t=web&gl=1&yh=1&ms=1&yn=1&rm=1&av=1&ap=1&nm=1&lang=all


$s[2]=trim(htmlspecialchars($s[2]));
$s[0]=trim(htmlspecialchars($s[0]));

// тупа€ бл€дь диагностика UTF
$s[0]=maybelink($s[2]);

return $s;
}

function maybelink($e) {
	$s=urldecode($e); if($s!=$e) $s=htmlspecialchars($s);
	if( ( strlen($s)/((int)substr_count($s,'–')+0.1) ) < 11 ) return(iconv("utf-8",$GLOBALS['wwwcharset']."//IGNORE",$s));
	else return(trim($s));
}

?>
