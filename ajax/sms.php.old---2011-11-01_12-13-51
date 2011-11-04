<?php
include "../config.php";
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

function dowith($o) { global $from;


	$Date='2010/12/24'; //2010/12/23

	$DateID=intval(ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'","_l"));
	if(!$DateID) die('Date error!');

	$scr=0;
	$l=preg_replace("/[0-9a-z\.\_\-\:\/\\]+\.jpg\n/si",' ',$o);
	$l=preg_replace("/p\.s/si",'',$l);
	if(preg_match("/[a-z]\.[a-z]/si",$l)) $scr=1;

	if(!preg_match("/^\+\d{9,}$/s",$from)) die("\nFROM: $from - disabled");

      $ara=array(
                'Text'=>kw($o),
                'Mail'=>'',
                'Name'=>$from,
                'group'=>0,
                'IPN'=>0,
                'BRO'=>'SMS',
                'DateID'=>$DateID,
                'unic'=>0xFFFFFFFF, //9636180819,
                'Time'=>time(),
                'scr'=>$scr,
                'Parent'=>0 );

	msq_add('dnevnik_comm',arae($ara)); $newid=mysql_insert_id();
        cache_rm(comment_cachename($DateID)); // сбросить кэш коментов этой записи

	die("\n DATE n=".$DateID." newid=".$newid."\n\n".print_r($ara,1));

}



$smsdir="user/sms/";

	$text=$_GET['text'];
	$pass=$_GET['pass'];
	$data=$_GET['data'];
	$from=$_GET['from'];
	if($pass!=md5("poganka".$text.$from.$data)) die('ERROR CHECKSUMM');
	$data=date("Y-m-d H:i:s",strtotime("20".$data));

if(preg_match("/^Для Вас новое MMS\s+(.*?)\s*Смотреть: http\:\/\/mms\.beeline\.ru\/show\/(\d+)\s/s",uw($text),$m))
{ $mms=$m[2];
$o=(trim(wk($m[1]))!=''?"[b]".wk($m[1])."[/b]\n":'');
//=======================================================
$logpas="j_username=9636180819&j_password=313313&x=46&y=10";

$s=poost("https://mms.beeline.ru/security_check",$logpas); // логинимся
$s=poost("http://mms.beeline.ru/mmsView/load/".$mms); // читаем

$k=0; while(substr($s,0,12)!='HTTP/1.1 200' and (++$k)<10) { // перелогиниваемся и читаем заново
	$s=poost("https://mms.beeline.ru/security_check",$logpas);
	$s=poost("http://mms.beeline.ru/mmsView/load/".$mms);
}

$mmsobjs=array();
$mmstxts=array();

if(preg_match_all("/<div class=\"name\"><a href=\"(\/attachments\/[^\"]+)\"/si",$s,$m)) {
	$mmsdir=$smsdir.str_replace("+","",$from)."/";
	foreach($m[1] as $l) {
		if(stristr($l,'.jpg')) { $p=poost("http://mms.beeline.ru".$l,"",0);

// --- если корректный jpeg ---
$img=imagecreatefromstring($p); if($img !== false) {
	$foto_replace_resize=1; require_once $include_sys."_fotolib.php"; $itype=2;
	$imgs=obrajpeg_sam($img,$GLOBALS['fotouser_x'],imagesx($img),imagesy($img),$itype,"SMS: ".$from);
	imagedestroy($img);

	// создать папку, если надо
	if(!is_dir($filehost.$mmsdir)) { mkdir($filehost.$mmsdir); chmod($filehost.$mmsdir,0777); }
	// создать имя, остерегаясь перезаписи существующего
	$bfilename=date("Y-m-d_H_i_s",strtotime($data)); $filename=$bfilename.'.jpg';
	$k=0; while(is_file($filehost.$mmsdir.$filename)) $filename=$bfilename."_".(++$k).".jpg";
	// записать фотку на диск
	closeimg($imgs,$filehost.$mmsdir.$filename,$itype,$GLOBALS['fotouser_q']);
	$mmsobjs[]=$httphost.$mmsdir.$filename;
	}
	// ---------------------------

	} elseif(stristr($l,'.txt')) { $p=poost("http://mms.beeline.ru".$l,"",0); $mmstxts[]=uk($p); }

	}
}
$max=max(sizeof($mmstxts),sizeof($mmsobjs));
for($i=0;$i<$max;$i++) $o.=(isset($mmstxts[$i])?$mmstxts[$i]."\n":'').(isset($mmsobjs[$i])?$mmsobjs[$i]."\n":'');
//=======================================================
} else { $o=uk($text)."\n"; }






$o=trim($o,"\n\r");
$o=str_replace("\r","",$o);

echo "OK
SERVER_ANSWER:\n\n";
dowith($o);
die("\n\nok\n\n".$o);

//---------------------------------------------------------------------------------
function poost($url,$post='',$heads=1) { global $last_url,$last_id,$last_cookie;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.4) Gecko/2008102920 AdCentriaIM/1.7 Firefox/3.0.4");
if($post!='') {
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
}
	curl_setopt($ch, CURLOPT_COOKIE, $last_cookie);
	curl_setopt($ch, CURLOPT_REFERER, $last_url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	curl_setopt($ch, CURLOPT_HEADER, $heads); // 1
	$result = curl_exec($ch);
	echo curl_error($ch);
if($heads) {
	$last_cookie0=trim(preg_replace("/^.*?Set-Cookie: (JSESSIONID=[0-9,A-F]+).*?$/s","$1",$result));
	if(!strstr($last_cookie0,"HTTP")) $last_cookie=$last_cookie0;
	$last_url=$url;
}
	return $result;
}
//---------------------------------------------------------------------------------
?>
