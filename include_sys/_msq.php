<?php

if(!$admin) { $ttl=60; } else {
	$MYPAGE_MD5 = md5($MYPAGE);
	$ttl=(isset($_COOKIE['MYPAGE']) and $MYPAGE_MD5==$_COOKIE['MYPAGE']?0:60); 
	set_cookie('MYPAGE', $MYPAGE_MD5, time(), "/", "", 0, true);
}

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

// if(!isset($memcache)) cache_init();
$msqe=''; // сюда пишем ошибки
ms_connect(); // соединиться с базой - эта процедура в _autorize.php

function ms_connect() { if(isset($GLOBALS['ms_connected'])) return;

   mysql_connect($GLOBALS['msq_host'], $GLOBALS['msq_login'], $GLOBALS['msq_pass']) or idie("<p>Ошибка соединения с MySQL!
Исправьте в config.php строки:<ul> \$msq_host = '".$GLOBALS['msq_host']."';
<br>\$msq_login = '".$GLOBALS['msq_login']."';
<br>\$msq_pass = [...]
</ul>");
   mysql_select_db($GLOBALS['msq_basa']) or idie("<p>Хорошие новости! Во-первых, движок поднялся. Что уже чудо. Во-вторых, что еще
чудеснее, обнаружен MySQL и с ним установлено успешное соединение!
Теперь плохая новость: отсутствует база&nbsp;<b>`".$GLOBALS['msq_basa']."`</b>. Это не проблема, подойдет любая другая, лишь бы движку
было где создать свои таблицы. Если есть какая-то база, ее имя надо вписать в config.php, где сейчас:
<b>\$msq_basa = '".$GLOBALS['msq_basa']."';</b>");

   mysql_query("SET NAMES ".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_client=".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_results=".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_connection=".$GLOBALS['msq_charset']);

   $GLOBALS['ms_connected']=true;
}

function e($s) { return mysql_real_escape_string($s); }
function msq_exist($tb,$u) { return ms("SELECT COUNT(*) FROM $tb $u","_l",0); }
//function msqn($sql) { return mysql_num_rows($sql); }

function msq_add($tb,$ara) {
        $a=$b=''; foreach($ara as $n=>$m) { $a.="`$n`,"; $b.="'$m',"; } $a=trim($a,','); $b=trim($b,',');
        $s = "INSERT INTO $tb ($a) VALUES ($b)";
        return msq($s);
}

function msq_update($tb,$ara,$u='') {
        $a=''; foreach($ara as $n=>$m) $a.="`$n`='$m',"; $a=trim($a,',');
        $s="UPDATE $tb SET $a $u";
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
	$s="DELETE FROM $tb WHERE $a $u";
	return msq($s);
}

function msq($s) { global $msqe;
	$sql=mysql_query($s);
	$e=mysql_error(); if($e!='') $msqe .= "<p><font color=green>mysql_query(\"$s\")</font><br><font color=red>$e</font>";
	return($sql);
}

function msq_pole($tb,$pole) { // проверить, существует ли такое поле в таблице $tb
        $pp=ms("SHOW COLUMNS FROM ".e($tb)."","_a",0); foreach($pp as $p) if($p['Field']==$pole) return $p['Type'];
	return false;
}

function msq_table($pole) { // проверить, существует ли такая таблица
        $ppp=ms("SHOW TABLES","_a",0); if($ppp!==false) foreach($ppp as $pp) if(sizeof($pp)) foreach($pp as $p) if($p==$pole) return true;
        return false;
}

function msq_index($tb,$pole) { // проверить, существует ли такой индекс
        $pp=ms("SHOW INDEX FROM $tb","_a",0); if($pp!==false) foreach($pp as $p) if($p['Column_name']==$pole) return true;
        return false;
}

//function tos($e) { return str_replace(array("\\","'",'"',"\n","\r"),array("\\\\","\\'",'\\"',"\\n",""),$e); }

function ms($query,$mode='_a',$ttl=666) { $s = false; $magic='@'.$GLOBALS['blogdir']; if($ttl==666) $ttl=$GLOBALS['ttl'];

	if($ttl < 0) { cache_rm($mode.$magic.$query); return true; } // сбросить кэш
	elseif ($ttl > 0) {  $result=cache_get($mode.$magic.$query); if(false!==$result) {
//		$GLOBALS["_PAGE"]["msq"].="<img src=".$GLOBALS['www_design']."yes.gif onmouseover=\"msqq('".tos($query)."')\"> ";
		$GLOBALS['ms_ttl']='cache';
		return $result; }
	}

//		$GLOBALS["_PAGE"]["msq"].="<img src=".$GLOBALS['www_design']."no.gif onmouseover=\"msqq('".tos($query)."')\"> ";
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

//function my_memcache_connect() {}

// function cache_init() { global $memcache; $memcache = new Memcache; $memcache->connect('localhost', 11211) or $memcache=false; }
function cache_set($k,$v,$e) { global $memcache; if(!$memcache) return false; return $memcache->set($k,$v,0,$e); }
function cache_get($k) { global $memcache; if(!$memcache) return false; return $memcache->get($k); }
function cache_rm($k) { global $memcache; if(!$memcache) return false; return $memcache->delete($k); }
?>