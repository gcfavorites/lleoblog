<?php

$ttl=($admin?0:10);

/*
ПОЛЕЗНЫЕ ПРИМЕРЫ

include_once $_SERVER['DOCUMENT_ROOT']."/dnevnik/_msq.php"; msq_open('lleo');

	$ara=array();
	$ara['name']=e($name);
	$ara['sc']=e($sc);
	$ara['ipipx']=e($_SERVER['REMOTE_ADDR'].' '.$_SERVER['HTTP_X_FORWARDED_FOR']);
	$ara['value']=e($value);

if(!msq_exist($db_,"WHERE `name`='".$ara['name']."' AND (`sc`='".$ara['sc']."' OR `ipipx`='".$ara['ipipx']."')"))
msq_add($db_,$ara);

$n=intval(msqn(msq("SELECT `value` FROM `$db_` WHERE `name`='$name' AND `value`='$l'")));

msq_update($tb,$ara,"WHERE `name`='lleo'");

msq_add_update($db_,array('name'=>$name,'text'=>implode("\n",$o)),'name');

msq_del($tb,$ara,$u='')
*/

if(!isset($memcache)) cache_init();
$msqe=''; // сюда пишем ошибки
ms_connect(); // соединиться с базой - эта процедура в _autorize.php

function e($s) { return mysql_real_escape_string($s); }
function msq_exist($tb,$u) { return msqn(msq("SELECT * FROM `$tb` $u")); }
function msqn($sql) { return mysql_num_rows($sql); }

function msq_add($tb,$ara) {
        $a=$b=''; foreach($ara as $n=>$m) { $a.="`$n`,"; $b.="'$m',"; } $a=trim($a,','); $b=trim($b,',');
        $s = "INSERT INTO `$tb` ($a) VALUES ($b)";
        return msq($s);
}

function msq_update($tb,$ara,$u='') {
        $a=''; foreach($ara as $n=>$m) $a.="`$n`='$m',"; $a=trim($a,',');
        $s="UPDATE `$tb` SET $a $u";
        return msq($s);
}

function msq_add_update($tb,$ara,$key='id') { $keys=explode(' ',$key);
	$u='WHERE '; foreach($keys as $k) $u .= "`$k`='".$ara[$k]."',"; $u=str_replace(',',' AND ',trim($u,','));
	if(!msq_exist($tb,$u)) $s=msq_add($tb,$ara);
	else { foreach($keys as $k) unset($ara[$k]); $s=msq_update($tb,$ara,$u); }
	return $s;
}

function msq_del($tb,$ara,$u='') {
	$a=''; foreach($ara as $n=>$m) $a.="`$n`='$m',"; $a=trim($a,',');
	$s="DELETE FROM `$tb` WHERE $a $u";
	return msq($s);
}

function msq($s) { global $msqe;
	$sql=mysql_query($s);
	$e=mysql_error(); if($e!='') $msqe .= "<p><font color=green>mysql_query(\"$s\")</font><br><font color=red>$e</font>";
	return($sql);
}


function ms($query,$mode='_a',$ttl=0) {	$s = false; $magic='@';

	if($ttl < 0) { cache_rm($mode.$magic.$query); return true; } // сбросить кэш
	elseif ($ttl > 0) {  $result=cache_get($mode.$magic.$query); if(false!==$result) { $GLOBALS['ms_ttl']='cache'; return $result; } }

	$GLOBALS['ms_ttl']='new';
	$sql = @msq($query);

	if($sql === false) { print "SQL error: ".mysql_error(); return false; }

	if ($mode == '_a') { $s = array(); while ($p = mysql_fetch_assoc($sql)) $s[]=$p; }
	elseif ($mode == '_1') { if(mysql_num_rows($sql)>=1) $s = mysql_fetch_assoc($sql); else $s=false; }
	elseif ($mode == '_l') { if(mysql_num_rows($sql)>=1) $s = mysql_result($sql,0,0); else $s=false; }
	else { $s=array(); while($p=mysql_fetch_assoc($sql)) $s[$p[$mode]]=$p; }

	if($ttl > 0) { cache_set($mode.$magic.$query, $s, $ttl); }

	return $s;
}

function cache_init() { global $memcache; $memcache = new Memcache; $memcache->connect('localhost', 11211) or $memcache=false; }
function cache_set($k,$v,$e) { global $memcache; if(!$memcache) return false; return $memcache->set($k,$v,0,$e); }
function cache_get($k) { global $memcache; if(!$memcache) return false; return $memcache->get($k); }
function cache_rm($k) { global $memcache; if(!$memcache) return false; return $memcache->delete($k); }
?>