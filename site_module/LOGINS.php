<?php // Комментарии

include_once $GLOBALS['include_sys']."getlastcom.php"; getlastcom();

function riss($l) {
if(($o=trim(parse_url($l,PHP_URL_PATH),'/'))=='')
	$o=substr((preg_match("/^(.*)\.[^\.]+\.[^\.]+$/s",$l,$m)?$m[1]:$l),7);
$f="http://".preg_replace("/^.*\.([^\.]+\.[^\.]+)$/s","$1",parse_url($l,PHP_URL_HOST))."/favicon.ico";
return "<img src='$f'><b>$o</b>";
}

function LOGINS() { global $lim,$admin,$mode,$lastcom,$ncom;

//$lastcom=strtotime("2011-01-01");

$mytime=time();

$pp=ms("SELECT `id`,`login`,`openid`,`lju`,`realname`,`mail`,`site`,`admin`,`time_reg`,`capchakarma`
FROM ".$GLOBALS['db_unic']."
WHERE `time_reg`>=".e($lastcom)." AND (`login`!='' OR `openid`!='') ORDER BY `time_reg`","_a");

$s='';
foreach($pp as $p) {
if($p['openid']=='') continue;

$l=$p['openid']; if($l=='') continue; $l="http://".h(rtrim($l,'/'));

$s.="<div>"
//.$p['login']." "
.riss($l)." <a href='$l'>$l</a></div>";
}

return $s;

//: enum('user','podzamok','admin','mudak') NOT NULL
//: int(11) NOT NULL default '0'
//capcha: enum('yes','no') NOT NULL default 'no'
//capchakarma: tinyint(3) unsigned NOT NULL default '0'


}

?>