<?php
/*\
  /  This is a part of PhFiTo (aka PHP Fido Tosser)
 //  Copyright (c) Alex Kocharin, 2:50/13
///
///  This program is distributed under GNU GPL v2
///  See docs/license for details
//
/    $Id: C_mysql.php,v 1.8 2007/10/14 08:29:54 kocharin Exp $
\*/

if (!function_exists('xinclude')) die('error');

class C_mysqlbase {
	var $db;
	var $table;
	var $echo;
	var $id;
	var $extended_syntax;
	var $allscan_present;

	function C_mysqlbase() {
		$this->extended_syntax = false;
		$this->allscan_present = false;
	}

	function OpenBase($addr) {
		$this->_decodeaddr($addr);
		$this->_connectdb();
		if (!$this->id) return 0;
		if (!mysql_select_db($this->db,$this->id)) {
			$this->CloseBase();
			return 0;
		}
		$res = mysql_query('SELECT NUMBER FROM '.$this->table.' LIMIT 1;',$this->id);
		if ($res):
			mysql_free_result($res);
			return 1;
		else:
			$this->CloseBase();
			return 0;
		endif;
	}

	function CreateBase($addr)
	{
		$this->_decodeaddr($addr);
		$this->_connectdb();
		mysql_select_db($this->db,$this->id);

		$res = mysql_query("
CREATE TABLE IF NOT EXISTS `fidoecho` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `new` enum('1','0') collate utf8_bin NOT NULL default '0',
  `AREAN` int(10) NOT NULL,
  `MSGID` varchar(50) character set armscii8 collate armscii8_bin default NULL,
  `REPLYID` varchar(50) character set armscii8 collate armscii8_bin default NULL,
  `SUBJ` varchar(72) character set utf8 collate utf8_unicode_ci default NULL,
  `FROMNAME` varchar(36) character set utf8 collate utf8_unicode_ci default NULL,
  `TONAME` varchar(36) character set utf8 collate utf8_unicode_ci default NULL,
  `FROMADDR` varchar(50) character set ascii collate ascii_bin default NULL,
  `TOADDR` varchar(50) character set ascii collate ascii_bin default NULL,
  `NUMBER` int(10) unsigned default NULL,
  `BODY` longtext character set utf8 collate utf8_unicode_ci,
  `RAZMER` int(10) unsigned default NULL,
  `DATETIME` datetime default NULL,
  `RECIVDATE` datetime default NULL,
  `ATTRIB` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `AREAN` (`AREAN`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=0 ;
",$this->id);


		$res = mysql_query("
CREATE TABLE IF NOT EXISTS `fidoecho_num` (
  `echonum` int(10) NOT NULL auto_increment,
  `echo` varchar(80) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`echonum`),
  KEY `echo` (`echo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;
",$this->id);


		$res = mysql_query("
CREATE TABLE IF NOT EXISTS `fidopodpiska` (
  `i` int(10) unsigned NOT NULL auto_increment COMMENT 'просто ключ',
  `point` int(10) NOT NULL COMMENT 'поинт',
  `echonum` int(10) unsigned NOT NULL COMMENT 'номер эхи',
  PRIMARY KEY  (`i`),
  KEY `point` (`point`,`echonum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Таблица подписок поинтов' AUTO_INCREMENT=0 ;
",$this->id);


		$res = mysql_query("
CREATE TABLE IF NOT EXISTS `fidopoints` (
  `unic` bigint(20) NOT NULL COMMENT 'unic по базе движка',
  `point` int(10) NOT NULL COMMENT 'Номер поинта (2:5020/313.point)',
  `dostup` enum('read','write','writeall') collate utf8_unicode_ci NOT NULL default 'read' COMMENT 'Уровень доступа к эхам',
  `msg_new` text collate utf8_unicode_ci NOT NULL,
  `msg_reply` text collate utf8_unicode_ci NOT NULL,
  `datereg` int(10) unsigned NOT NULL COMMENT 'дата регистрации',
  `datelast` int(10) unsigned NOT NULL COMMENT 'дата последнего обращения',
  PRIMARY KEY  (`unic`),
  KEY `point` (`point`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
",$this->id);

		$res = mysql_query("
CREATE TABLE IF NOT EXISTS `fidomy` (
  `i` bigint(20) unsigned NOT NULL auto_increment,
  `point` int(10) NOT NULL,
  `arean` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `metka` enum('new','read') collate utf8_unicode_ci NOT NULL default 'new',
  PRIMARY KEY  (`i`),
  KEY `point` (`point`,`arean`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='личные сообщения пользователей' AUTO_INCREMENT=0 ;
",$this->id);

		$res = mysql_query("
CREATE TABLE IF NOT EXISTS `dupes` (
  `area` varchar(80) collate utf8_unicode_ci NOT NULL,
  `msgid` varchar(255) collate utf8_unicode_ci NOT NULL,
  `crc` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  KEY `area` (`area`(80)),
  KEY `crc` (`crc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
",$this->id);







        $e=mysql_error(); if($e!='') die("<p><font color=red>$e</font>");

		$this->_disconnectdb();
		return $res;
	}

	function _decodeaddr($addr) {
		if (strcmp(substr($addr,0,1),'/')==0) $addr = substr($addr,1);
		$this->db = strtok($addr,'/');
		$this->table = strtok('/');
		$this->echo = strtok('/');
		$this->table = preg_replace('/[^a-z0-9_]/i','_',$this->table);
	}

	function _connectdb() {
		GLOBAL $CONFIG;
		$host = isset($CONFIG->Vars['mysqlhost'])?$CONFIG->Vars['mysqlhost']:'localhost';
		$user = isset($CONFIG->Vars['mysqluser'])?$CONFIG->Vars['mysqluser']:'';
		$pass = isset($CONFIG->Vars['mysqlpass'])?$CONFIG->Vars['mysqlpass']:'';
		if ($pass): $this->id = mysql_connect($host,$user,$pass,true);
		elseif ($user): $this->id = mysql_connect($host,$user,ini_get("mysql.default_password"),true);
		else: $this->id = mysql_connect($host,ini_get("mysql.default_user"),ini_get("mysql.default_password"),true);
		endif;
// lleo
	$GLOBALS['msq_charset']='utf8';
	mysql_query("SET NAMES ".$GLOBALS['msq_charset']);
	mysql_query("SET @@local.character_set_client=".$GLOBALS['msq_charset']);
	mysql_query("SET @@local.character_set_results=".$GLOBALS['msq_charset']);
	mysql_query("SET @@local.character_set_connection=".$GLOBALS['msq_charset']);

	$GLOBALS['db_fido'] = $CONFIG->Vars['db_fido'];
	$GLOBALS['db_fido_num'] = $CONFIG->Vars['db_fido_num'];
	$GLOBALS['db_fidopoints'] = $CONFIG->Vars['db_fidopoints'];
	$GLOBALS['db_fidopodpiska'] = $CONFIG->Vars['db_fidopodpiska'];
	$GLOBALS['db_fidomy'] = $CONFIG->Vars['db_fidomy'];
// lleo
	}

	function _disconnectdb() { mysql_close($this->id); }
	
	function GetNumMsgs() {
		$q = mysql_query('SELECT NUMBER FROM '.$this->table.
//			(strcmp($this->echo,'')==0?'':' WHERE AREA = "'.$this->echo.'"'),
			(strcmp($this->echo,'')==0?'':' WHERE AREAN = "'.get_arean($this->echo,$this->id).'"'),
			$this->id);
		$result = mysql_num_rows($q);
		mysql_free_result($q);
		return $result;
	}

	function ReadMsgHeader($msg) {
//		$q = mysql_query('SELECT DATETIME,RECIVDATE,FROMADDR,TOADDR,FROMNAME,TONAME,SUBJ,ATTRIB'.
		$q = mysql_query('SELECT DATETIME,RECIVDATE,FROMADDR,TOADDR,FROMNAME,TONAME,SUBJ,ATTRIB,BODY'.
			' FROM '.$this->table.
//			(strcmp($this->echo,'')==0?'':' WHERE AREA = "'.$this->echo.'"').
			(strcmp($this->echo,'')==0?'':' WHERE AREAN = "'.get_arean($this->echo,$this->id).'"').
			' ORDER BY NUMBER LIMIT 1'.($msg==1?'':' OFFSET '.($msg-1))
			,$this->id);
		$arr = mysql_fetch_row($q);
		mysql_free_result($q);
		$result = new C_msgheader;
		$result->WDate = Mysql_date_to_unix($arr[0]);
		$result->ADate = Mysql_date_to_unix($arr[1]);
		$result->FromAddr = $arr[2];
		$result->ToAddr = $arr[3];
		$result->From = $arr[4];
		$result->To = $arr[5];
		$result->Subj = $arr[6];

// lleo ---
$chr=parse_chr($arr[8]); if($chr) {
	$m=iconv("utf-8",$chr."//IGNORE",$result->From); if($m!==false) $result->From=$m;
	$m=iconv("utf-8",$chr."//IGNORE",$result->To); if($m!==false) $result->To=$m;
	$m=iconv("utf-8",$chr."//IGNORE",$result->Subj); if($m!==false) $result->Subj=$m;
}
// lleo ---

		$result->Attrs = $arr[7];
		return $result;
	}

	function SetAttr($msg,$attrs) {
		$q = mysql_query("SELECT NUMBER FROM {$this->table}".
//			(strcmp($this->echo,'')==0?'':' WHERE AREA = "'.$this->echo.'"').
			(strcmp($this->echo,'')==0?'':' WHERE AREAN = "'.get_arean($this->echo,$this->id).'"').
			' ORDER BY NUMBER LIMIT 1'.($msg==1?'':' OFFSET '.($msg-1))
			,$this->id);
		if (!$q) return 0;
		$num = mysql_result($q,0);
		mysql_free_result($q);

		if (mysql_query("UPDATE {$this->table} SET ATTRIB = $attrs WHERE".
//			(strcmp($this->echo,'')==0?'':' AREA = "'.$this->echo.'" AND')." NUMBER = $num",$this->id)):
			(strcmp($this->echo,'')==0?'':' AREAN = "'.get_arean($this->echo,$this->id).'" AND')." NUMBER = $num",$this->id)):
				return 1;
		else:
			return 0;
		endif;
	}

	function ReadMsgBody($msg) {
		$q = mysql_query('SELECT BODY FROM '.$this->table.
//			(strcmp($this->echo,'')==0?'':' WHERE AREA = "'.$this->echo.'"').
			(strcmp($this->echo,'')==0?'':' WHERE AREAN = "'.get_arean($this->echo,$this->id).'"').
			' ORDER BY NUMBER LIMIT 1'.($msg==1?'':' OFFSET '.($msg-1))
			,$this->id);
		$result = mysql_fetch_row($q);
		mysql_free_result($q);

// lleo ---
$chr=parse_chr($message); if($chr) {
	$m=iconv("utf-8",$chr."//IGNORE",$message); if($m!==false) $message=$m;
}
// lleo ---

//		return $result[0];
		return $message;
	}

	function CloseBase() { $this->_disconnectdb(); }
	function DeleteMsg($num) { return false; }
	function PurgeBase() { return false; }
	function DeleteBase($path) { return false; }

	function WriteMessage($header,$message) {
		$msgid = '';
		$reply = '';
		$line = strtok($message,"\r");
		while($line):
			if (strcmp(substr($line,0,1),chr(1))==0):
				if (!$msgid && (strcasecmp(substr($line,1,7),'MSGID: ')==0)):
					$msgid = trim(substr($line,8));
				elseif (!$reply && (strcasecmp(substr($line,1,7),'REPLY: ')==0)):
					$reply = trim(substr($line,8));
				endif;
			else:
				break;
			endif;
			$line = strtok("\r");
		endwhile;
		
//		if ($res = mysql_query("SELECT MAX(NUMBER) AS max_id FROM ".$this->table.(strcmp($this->echo,'')==0?'':' WHERE AREA = "'.$this->echo.'"'),$this->id)):
		if ($res = mysql_query("SELECT MAX(NUMBER) AS max_id FROM ".$this->table.(strcmp($this->echo,'')==0?'':' WHERE AREAN = "'.get_arean($this->echo,$this->id).'"'),$this->id)):
			if (mysql_num_rows($res)): $num = mysql_result($res,0) + 1;
			else: $num = 1;
			endif;
			mysql_free_result($res);
		else:
			$num = 1;
		endif;

// lleo ---

$chr=parse_chr($message); if($chr) {
	$m=iconv($chr,"utf-8//IGNORE",$message); if($m!==false) $message=$m; else $message=chr(1)."iconv_ERROR: $chr".chr(13).$message;
	$m=iconv($chr,"utf-8//IGNORE",$header->Subj); if($m!==false) $header->Subj=$m;
	$m=iconv($chr,"utf-8//IGNORE",$header->From); if($m!==false) $header->From=$m;
	$m=iconv($chr,"utf-8//IGNORE",$header->To); if($m!==false) $header->To=$m;
}

GLOBAL $CONFIG;
$arean=get_arean($this->echo,$this->id);

// lleo ---

		mysql_query('
INSERT INTO '.$this->table.' (AREAN, MSGID, REPLYID, SUBJ, FROMNAME, TONAME, FROMADDR, TOADDR, NUMBER, BODY, RAZMER, DATETIME, RECIVDATE, ATTRIB)
 VALUES ('
// .'\''.mysql_real_escape_string
// mysql_real_escape_string($this->echo).'\','. // AREA
.'\''.mysql_real_escape_string($arean).'\','. // AREAN
'\''.mysql_real_escape_string($msgid).'\','. // MSGID
'\''.mysql_real_escape_string($reply).'\','. // REPLYID
'\''.mysql_real_escape_string($header->Subj).'\','. // SUBJ
'\''.mysql_real_escape_string($header->From).'\','. // FROMNAME
'\''.mysql_real_escape_string($header->To).'\','. // TONAME
'\''.mysql_real_escape_string($header->FromAddr).'\','. // FROMADDR
'\''.mysql_real_escape_string($header->ToAddr).'\','. // TOADDR
'\''.mysql_real_escape_string($num).'\','. // NUMBER
'\''.mysql_real_escape_string($message).'\','. // BODY
'\''.strlen($message).'\','. // RAZMER
'\''.mysql_real_escape_string(date('Y-m-d H:i:s',($header->WDate?$header->WDate:time()))).'\','. // DATETIME
'\''.mysql_real_escape_string($header->ADate?(date('Y-m-d H:i:s',$header->ADate)):'0000-00-00 00:00:00').'\','. // RECIVDATE
'\''.mysql_real_escape_string($header->Attrs).'\''. // ATTRIB
');',$this->id);

// lleo ---

$GLOBALS['new_id']=mysql_insert_id();

// Ну и когда мы разместили в базу, запустим всех роботов, что были в папке robots/

$ara=array(
	'msqid'=>$this->id,
	'id'=>$GLOBALS['new_id'],
	'area'=>$this->echo,
	'arean'=>$arean,
	'msgid'=>$msgid,
	'reply'=>$reply,
	'subj'=>$header->Subj,
	'from'=>$header->From,
	'to'=>$header->To,
	'froma'=>$header->FromAddr,
	'toa'=>$header->ToAddr,
	'number'=>$num,
	'body'=>$message,
	'datetime'=>($header->WDate?$header->WDate:0), // DATETIME
	'date'=>($header->ADate?$header->ADate:0), // RECIVDATE
	'attrib'=>$header->Attrs
); foreach(glob("robots/*.php") as $robot) {
	$r=preg_replace("/^robots\/(.+?)\.php$/si","$1",$robot);
	if(!function_exists("robot_".$r)) include_once($robot);
	if(call_user_func("robot_".$r,$ara)===false) return;
}
// lleo ---

	}
}

function get_arean($s,$id) { $t=$GLOBALS['db_fido_num']; $se=mysql_real_escape_string($s);
	if(!isset($GLOBALS['areans'])) $GLOBALS['areans']=array();
	if(isset($GLOBALS['areans'][$s])) return $GLOBALS['areans'][$s];
		$sql=mysql_query("SELECT `echonum` FROM $t WHERE `echo`='$se'",$id);
        if($sql !== false && mysql_num_rows($sql)>=1) { $arean=mysql_result($sql,0,0); }
	else {
		$sql=mysql_query("INSERT INTO $t (echo) VALUES ('$se')",$id); $arean=mysql_insert_id();
//	$g=fopen("echo_create.txt","a+"); fputs($g,"\n $se = $arean"); fclose($g);
		$e=mysql_error(); if($e!='') { print "SQL error: ".mysql_error(); return false; }
	}
	$GLOBALS['areans'][$s]=$arean;
	return $arean;
}


function parse_chr($s) {
	$c=(preg_match("/[\n\r]".chr(1)."CHRS:\s*([a-zA-Z0-9\-]+)\s/s",$s,$m))?strtoupper($m[1]):'CP866';

$perekod=array(
	'UTF-8'=>'UTF8',
	'LATIN'=>'CP866',
	'LATIN-1'=>'CP866',
	'LATIN-2'=>'CP866',
	'LATIN-5'=>'CP866',
	'CP-866'=>'CP866',
	'CP437'=>'CP866',
	'ALT'=>'CP866',

	'ASCII'=>'CP866',
	'DUTCH'=>'CP866',
	'FINNISH'=>'CP866',
	'FRENCH'=>'CP866',
	'CANADIAN'=>'CP866',
	'GERMAN'=>'CP866',
	'ITALIAN'=>'CP866',
	'NORWEIG'=>'CP866',
	'PORTU'=>'CP866',
	'SPANISH'=>'CP866',
	'SWEDISH'=>'CP866',
	'SWISS'=>'CP866',
	'UK'=>'CP866',

	'IBMPC'=>'CP866'
); if(isset($perekod[$c])) $c=$perekod[$c];
	if(stristr($c,'FIDO')) $c='CP866';
	return ($c=='UTF8'?false:$c);
}
?>
