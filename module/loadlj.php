<?php if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй
if(!$admin) redirect($wwwhost."login/"); // посторонних - нахуй

//$admin_ljuser='lleo';

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."plain.html"),
'header'=>"lleo.lj - КАЧАЛКА КЭША ЯНДЕКСА",
'title'=>"lleo.lj - КАЧАЛКА КЭША ЯНДЕКСА",

'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'wwwcharset'=>$wwwcharset,
'signature'=>$signature
);

if(!isset($admin_ljuser)) idie("Ошибка: не указано имя пользователя livejournal!
<br>Добавьте в свой config.sys эту строчку, пример: <b>\$admin_ljuser=\"lleo\";</b>");

$numdoc=100;
$all=array();
$into=(isset($_GET['into'])?intval($_GET['into']):0);


if($_GET['mode']=='comments') get_ya_comments();

print "<p><b>Качаем записи: этап #$into</b>
<p>Ничего не надо трогать! Работа продолжится через 2 секунды - страница сама перезагрузится!
<p>";

//$p=ms("SELECT `Date`,`Body`,`num` FROM `dnevnik_zapisi` LIMIT ".$into.",1","_1"); if($p===false) die("<p><br>Работа закончена!");
// ms("SELECT COUNT(*) FROM `dnevnik_comments` WHERE `Date`='".e($Date)."'", '_l',0)+1),
// exit;

$url="http://blogs.yandex.ru/search.xml?rd=0&spcctx=doc&ft=blog&server=livejournal.com&author=".$admin_ljuser."&numdoc=".$numdoc."&full=1&p=".($into++);
$syandex=uw(file_get($url));
$syandex=preg_replace("/<div class=\"b-item Ppb-c-ItemMore SearchStatistics-item\"[^>]+>.*?/si","-\001-",$syandex);
$arsyandex=explode("-\001-",$syandex); unset($arsyandex[0]);

foreach($arsyandex as $lyandex) { $ara=get_one_ya($lyandex);
	$Date=$ara['Date'];

	if(isset($all[$Date]) and $ara['Header']==$all[$Date]['Header'] and $ara['Body']==$all[$Date]['Body'] ) {
		print "<br><font color=red>дубль $Date - не вносим в базу!</font>";
	} else {
		if(isset($all[$Date])) {
			print "<br>дубль $Date:<p><pre>".print_r($ara,1)."</pre><hr>Было:<p><pre>".print_r($all[$Date],1)."</pre>";
			$i=0; while(isset($all[ $Date."_".(++$i) ])){} $Date=$Date."_".$i;
			$ara['Date']=$Date;
		}
	$all[$Date]=$ara;
	msq_add_update('dnevnik_zapisi',$ara,'Date');

	print "<br>".$GLOBALS['cached'].(sizeof($all)).". <font color=green>".$ara['Date']."</font>".($ara['Header']!=''?" - ".$ara['Header']:"");

	}
	
}

$n=''; foreach($all as $a=>$b) $n.=" ".$a; $n=md5($n);



if(sizeof($arsyandex)==$numdoc and $n!=$_GET['lastmd5']) { $path="$mypage?into=$into&lastmd5=".$n; } else { $path="$mypage?mode=comments"; }

die("<p><a href='$path'>$path</a> stop=$stop all=".sizeof($all)."
<noscript><meta http-equiv=refresh content=\"5;url=\"".$path."\"></noscript>
<script> setTimeout(\"location.replace('".$path."')\", 2000);  </script>");





function get_one_ya($lyandex) { $r=array(); // качать записи
	if(!preg_match("/<ul class=\"info b-hlist b-hlist-middot\"><li>([^<>]+)<\/li>/si",$lyandex,$m)) { die("не найдена дата!"); }
		preg_match("/(\d+) ([^ ]+) (\d+), (\d+):(\d+)/si",$m[1],$d); $da=array_keys($GLOBALS['months_rod'],$d[2]);
		$datet=sprintf("%04d-%02d-%02d %02d:%02d", $d[3],$da[0],$d[1],$d[4],$d[5]);
	$Dname=e(str_replace(array('-',':',' '),array('/','-','_'),$datet));


	if(!preg_match("/<div class=\"long\s+ItemMore-Description\"><div class=\"b-text\"><div>(.*?)<\/div><\/div><div class=\"links\">/si",$lyandex,$m)) { idie("Не найден text!"); }
	$text=govnolink($m[1]);

	if(!preg_match("/<h3 class=\"title\s*\"><a href=\"([^>\"\'\s]+)\"[^>]+target=\"_blank\">(.*?)<\/a><\/h3>/si",$lyandex,$m)) { idie("Не найден head!"); }
	$link=govnolink($m[1]);
	$head=govnolink($m[2]);str_ireplace(array('<wbr />','<wbr/>','<wbr>','</wbr>'),'',$link);

        $t=getmaketime($Dname);

	return array(
'Date'=>e($Dname),
'Header'=>e($head),
'Body'=>e($text."<hr><a href='$link'>$link</a>"),
'Access'=>'all',
'Comment'=>'allways_on',
'Comment_view'=>'on',
'Comment_write'=>'friends-only',
'Comment_screen'=>'open',
'comments_order'=>'normal',
'autoformat'=>'no',
'autokaw'=>'no',
'DateUpdate'=>time(),
'DateDate'=>$t[0],
'DateDatetime'=>$t[1]
);

}


function getmaketime($d) {
        preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d)(.*?)$/s",$d,$m);
        $d=$m[1]."-".$m[2]."-".$m[3];
        $t0=strtotime($d);
        if(preg_match("/^[\-_\s]*(\d\d)-(\d\d)/s",$m[4],$t)) $d .= " ".$t[1].":".$t[2];
        $t=strtotime($d);
        while(msq_exist('dnevnik_zapisi',"WHERE `DateDatetime`='$t'")) $t++;
        return array($t0,$t);
}


//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================

function get_ya_comments() { global $into,$numdoc,$mypage;

	$ppp=(isset($_GET['ppp'])?intval($_GET['ppp']):0);

	$p=ms("SELECT `Header`,`Date`,`Body`,`num` FROM `dnevnik_zapisi` LIMIT ".$into.",1","_1"); if($p===false) die("<p><br>Работа закончена!");

	print "<p>Ничего руками не трогай! Работа продолжается каждые 2 секунды - страницы сами перезагружаются!";

	$Body=$p['Body']; $num=$p['num']; $Date=$p['Date']; $Header=$p['Header'];

	$nnn=ms("SELECT COUNT(*) FROM `dnevnik_zapisi`", '_l',2);

	print "<p><a href='".$GLOBALS['wwwhost']."$Date'>$Date".($Header!=''?' - '.$Header:'')."</a>
<br><b>$into/$nnn: заметка #$num, шаг #$ppp</b><p>";

	if(!preg_match("/<hr><a href=[^>]+>(.*?)<\/a>$/si",$Body,$m)) die("Неправильный формат записи! <p>".$Body);
	$link=govnolink($m[1]);

	$url="http://blogs.yandex.ru/search.xml?post=".urlencode($link)."&ft=comments&rd=0&spcctx=doc&full=1&numdoc=".$numdoc."&p=".$ppp;

	print "<p>Качаем <a href='$url'>комментарии к этой заметке из Яндекса</a>";

	$syandex=uw(file_get($url));

	$syandex=preg_replace("/<div class=\"b-item Ppb-c-ItemMore SearchStatistics-item\"[^>]+>.*?/si","-\001-",$syandex);
	$arsyandex=explode("-\001-",$syandex); unset($arsyandex[0]);

if(sizeof($arsyandex)) foreach($arsyandex as $lyandex) {
	$ara=get_one_ya_c($lyandex,$Date,$num,$url);
	$unic=$ara['Name']."#".$ara['DateTime'];
	if(isset($all[$unic]) and $ara['Name']==$all[$unic]['Name'] and $ara['Commentary']==$all[$unic]['Commentary'] ) {
		print "<hr><font color=red>Нашелся полный дубль коментария $unic, не вносим в базу!</font>"; // <p><pre>".print_r($ara,1)."</pre><hr>Было:<p><pre>".print_r($all[$unic],1)."</pre>"; exit;
		} else {
			if(isset($all[$unic])) {
				// print "<hr>Нашелся дубль $unic:<p><pre>".print_r($ara,1)."</pre><hr>Было:<p><pre>".print_r($all[$unic],1)."</pre>";
				$i=0; while(isset($all[ $unic."_".(++$i) ])){} $unic=$unic."_".$i;
			}
		$all[$unic]=$ara;
		msq_add_update('dnevnik_comments',$ara,"Name DateTime"); // забить в базу
		}
}

$n=''; if(sizeof($all)) { foreach($all as $a=>$b) $n.=" ".$a; $n=md5($n); }
if(sizeof($arsyandex)==$numdoc and $n!=$_GET['lastmd5']) { $path="$mypage?mode=comments&ppp=".(++$ppp)."&into=$into&lastmd5=".$n; }
else { $path="$mypage?mode=comments&into=".(++$into); }

die("<p>$path, вытянули комментариев: ".sizeof($all)."
<noscript><meta http-equiv=refresh content=\"5;url=\"".$path."\"></noscript>
<script> setTimeout(\"location.replace('".$path."')\", 2000);  </script>");
}


function bredie($s,$lyandex,$url) { global $mypage;
	file_get($url,0);

	$path="$mypage?mode=comments&ppp=".$_GET['ppp']."&into=".$_GET['into']."&repeat=".(++$_GET['repeat']);

	die($s." ".$lyandex."

<p>Ошибка, но мы попробуем перезугрузиться через 15 секунд!
<noscript><meta http-equiv=refresh content=\"15;url=\"".$path."\"></noscript>
<script> setTimeout(\"location.replace('".$path."')\", 15000);  </script>");

}

function govnolink($s) { return str_ireplace(array('<wbr />','<wbr/>','<wbr>','</wbr>'),'',$s); }

function get_one_ya_c($lyandex,$Date,$num,$url) {

	if(!preg_match("/<span class=\"icon\"><img src=\"([^\">]+)\"/si",$lyandex,$m)) { /* idie("Не найдена иконка"); */ }
	else $img=str_replace("&amp;prefix=small","&prefix=normal",govnolink($m[1]));

	if(!preg_match("/<li>комментарий <a href=\"([^\">]+)\">([^<>\s]+)<\/a> в журнале/si",$lyandex,$m)) { /* idie("Не найден автор!");*/ }
	$autor=($m[2]!=''?govnolink($m[2]):'anonymouse');
	$autor_link=govnolink($m[1]);

	if(!preg_match("/<ul class=\"info b-hlist b-hlist-middot\"><li>([^<>]+)<\/li><li>комментарий/si",$lyandex,$m)) { bredie("Не найдено время!",$lyandex,$url); }

	preg_match("/(\d+) ([^ ]+) (\d+), (\d+):(\d+)/si",$m[1],$d); $da=array_keys($GLOBALS['months_rod'],$d[2]);
	$datet=sprintf("%04d-%02d-%02d %02d:%02d", $d[3],$da[0],$d[1],$d[4],$d[5]);


	if(!preg_match("/<div class=\"long\s+ItemMore-Description\"><div class=\"b-text\"><div>(.*?)<\/div><\/div><div class=\"links\">/si",$lyandex,$m)) { bredie("Не найден текст!",$lyandex,$url); }
	$text=govnolink($m[1]);

	if(!preg_match("/<h3 class=\"title *\"><a href=\"([^>\"]+)\"[^>]+>(.*?)<\/a><\/h3>/si",$lyandex,$m)) { bredie("Не найден заголовок!",$lyandex,$url); }
	$headlink=govnolink($m[1]);
	$head=govnolink($m[2]);
	if($headlink==$head) $head='';

	$text=str_ireplace(array('<p>','<br>','<br />','<br/>',"\r"),array("\n\n","\n","\n","\n",''),$text);

	$text=preg_replace("/<img\s+src=[\"\']*([^\'\"\s>]+)[\'\"]*\s*\/*>/si","\n$1\n",$text);
	$text=str_ireplace('<span class="ljuser" style="white-space: nowrap;"><img alt="[info]" height="17" src="http://stat.livejournal.com/img/userinfo.gif" style="vertical-align: bottom; border: 0; padding-right: 1px;" width="17" />','',$text);
	$text=str_ireplace('&gt;','>',$text);


	$head=str_ireplace(array('<p>','<br>','<br />','<br/>',"\r"),array("\n\n","\n","\n","\n",''),$head);

	$text=(isset($img)?$img." ":'').($head!=''?"<b>$head</b>\n\n":'').$text;

print "<p><table border=1><tr>
<td><img src='$img'><br><a href='$autor_link'>$autor</a></td>
<td><b>$head</b> $datet
<p>$text
<p class=br><a href='$comlink'>$comlink</a>
</td></tr></table>";

	return array(
        'Date'=>e($Date),
        'DateID'=>intval($num),
        'idza'=>intval(ms("SELECT COUNT(*) FROM `dnevnik_comments` WHERE `Date`='".e($Date)."'", '_l',0)+1),
	'whois_strana'=>e($headlink),
        'Name'=>e($autor),
        'Address'=>e($autor_link),
        'Commentary'=>e($text),
        'DateTime'=>strtotime($datet),
        'Answer_time'=>0,
        'Guest_LJ'=>e($autor),
        'Guest_Name'=>e($autor_link),
        'metka'=>'open');
}

?>
