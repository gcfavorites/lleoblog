<?php // ����������� �������������
if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����
if(isset($_GET['version'])) die("lleoblog 2.0"); // �������� ������
// if(!$admin) redirect($wwwhost."login/"); // ����������� - �����
// blogpage();
// $_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."dnevnik.html"),

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."plain.html"),
'header'=>"�������� ������",
'title'=>"�������� ������",

'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'wwwcharset'=>$wwwcharset,
'signature'=>$signature
);


 //  ALTER TABLE `dnevnik_zapisi` ADD INDEX ( `Next` )  

$test_basa=array(


'login' => array("������ �����������","(
  `login` varchar(32) NOT NULL,
  `sc` varchar(32) NOT NULL,
  `timereg` datetime NOT NULL,
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `realname` varchar(128) NOT NULL,
  `mail` varchar(128) NOT NULL,
  `site` varchar(128) NOT NULL,
  `birth` date NOT NULL,
  `admin` enum('user','podzamok','comblock','mudak') NOT NULL,
  `podpiska` enum('dnevnik','lichnoe','sovet') NOT NULL,
  `img` varchar(36) NOT NULL,
  `password` varchar(32) NOT NULL,
  `count` bigint(20) NOT NULL default '0',
  `type` enum('login','openid') NOT NULL,
  `comdate` datetime NOT NULL,
  `comperdate` smallint(5) unsigned NOT NULL default '0',
  `text` text NOT NULL,
  PRIMARY KEY  (`login`)
)"),

'dnevnik_zapisi' => array("������� �����","(
  `Date` varchar(128) NOT NULL,
  `Header` varchar(255) NOT NULL default '',
  `Body` text NOT NULL,
  `Access` enum('all','podzamok','admin') NOT NULL,
  `Comment` enum('enabled','disabled','allways_on','screen','normalscreen') NOT NULL default 'enabled',
  `Comment_view` enum('on','off','rul','load','timeload') NOT NULL default 'timeload',
  `Comment_write` enum('on','off','friends-only','login-only','timeoff','login-only-timeoff') NOT NULL default 'timeoff',
  `Comment_screen` enum('open','screen','friends-open') NOT NULL default 'friends-open',
  `comments_order` enum('normal','rating','allrating') NOT NULL default 'normal',
  `DateUpdate` int(10) unsigned NOT NULL default '0',
  `view_counter` int(10) unsigned NOT NULL default '0',
  `last_view_ip` varchar(15) NOT NULL default '',
  `num` int(10) unsigned NOT NULL auto_increment,
  `Prev` varchar(64) NOT NULL default '' COMMENT '���������� �������',
  `Next` varchar(64) NOT NULL default '' COMMENT '��������� �������',
  UNIQUE KEY `num` (`num`),
  KEY `Date` (`Date`),
  KEY `Next` (`Next`)
)"),

'dnevnik_comments' => array("����������� �����������","(
  `id` int(10) unsigned NOT NULL auto_increment,
  `idza` int(10) NOT NULL default '0',
  `Date` varchar(128) NOT NULL,
  `Name` varchar(128) NOT NULL default '',
  `Address` varchar(128) NOT NULL default '',
  `Commentary` text NOT NULL,
  `Answer` text NOT NULL,
  `Answer_user` varchar(64) NOT NULL,
  `Answer_time` int(11) NOT NULL,
  `ans_spamit` int(10) NOT NULL default '0',
  `ans_rulit` int(10) NOT NULL default '0',
  `ans_lastIPsc` varchar(20) NOT NULL,
  `IP` varchar(15) NOT NULL,
  `IPx` varchar(15) NOT NULL,
  `DateTime` int(11) NOT NULL default '0',
  `UserAgent` varchar(1024) NOT NULL,
  `Guest_LJ` varchar(32) NOT NULL default '',
  `Guest_Name` varchar(128) NOT NULL default '',
  `speckod` varchar(32) NOT NULL default '',
  `mudak` enum('yes','no') NOT NULL default 'no',
  `metka` enum('open','screen') NOT NULL default 'open',
  `spamit` int(10) NOT NULL default '0',
  `rulit` int(10) NOT NULL default '0',
  `rulit_lastIPsc` varchar(20) NOT NULL,
  `rulit_master` enum('0','1') NOT NULL default '0',
  `login` varchar(128) NOT NULL,
  `whois_strana` varchar(128) NOT NULL,
  `whois_gorod` varchar(128) NOT NULL,
  `whois_basa` enum('whois','rus','world') NOT NULL default 'whois',
  `DateID` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `Date` (`Date`),
  KEY `DateTime` (`DateTime`),
  KEY `login` (`login`),
  KEY `speckod` (`speckod`),
  KEY `DateID` (`DateID`)
)"),

'dnevnik_link' => array("���������� ������ � ������ ������","(
  `n` bigint(20) NOT NULL auto_increment,
  `Date` varchar(10) NOT NULL,
  `link` varchar(2048) NOT NULL,
  `count` bigint(20) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`n`),
  KEY `Date` (`Date`),
  KEY `last_ip` (`last_ip`)
)"),

'dnevnik_search' => array("���������� ��������� �������","(
  `n` bigint(20) NOT NULL auto_increment,
  `Date` varchar(10) NOT NULL,
  `poiskovik` varchar(32) NOT NULL,
  `link` varchar(2048) NOT NULL,
  `search` varchar(2048) NOT NULL,
  `count` bigint(20) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`n`),
  KEY `Date` (`Date`),
  KEY `last_ip` (`last_ip`),
  KEY `link` (`link`(1000))
)"),

'golosovanie2_result' => array("�����������: ����������","(
  `name` varchar(32) NOT NULL,
  `n` bigint(20) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`name`)
)"),

'golosovanie2_golosa' => array("�����������: ����������","(
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `sc` varchar(32) NOT NULL,
  `ipipx` varchar(31) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
)"),

'pravki' => array("������ �����","(
  `id` int(10) unsigned NOT NULL auto_increment,
  `Date` varchar(255) NOT NULL,
  `DateTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `sc` varchar(32) NOT NULL,
  `login` varchar(128) NOT NULL,
  `ipbro` varchar(255) NOT NULL,
  `lju` varchar(128) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Mail` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `textnew` text NOT NULL,
  `stdprav` text NOT NULL,
  `Answer` text NOT NULL,
  `metka` enum('new','submit','discard') NOT NULL default 'new',
  PRIMARY KEY  (`id`),
  KEY `Date` (`Date`),
  KEY `metka` (`metka`),
  KEY `sc` (`sc`)
)"),

'site' => array("������� �����","(
  `id` int(10) unsigned NOT NULL auto_increment,
  `Access` enum('all','podzamok','admin') NOT NULL default 'admin' COMMENT '��������� �������',
  `name` varchar(1024) NOT NULL,
  `type` enum('page','design','news','pageplain','photo') NOT NULL default 'page',
  `text` text NOT NULL,
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
)")
);



$s='';


//======================================================================================
// ������ ������

$s .= "<p class=z>�����";

$f_logout = "<form name='logout' method='POST' action='".$mypage."'><input type='submit' name='logout' value='�������������'></form>";
$f_login = "<form name='login' method='POST' action='".$mypage."'>������: <input type='text' name='login' size='10'>&nbsp;<input type='submit' value='������������'></form>";

if(!preg_match("/^[0-9abcdef]{32}$/",$admin_hash)) { // �������� ������

if($_POST['pass']!='') {

	$s.= "<p>��� ������ ������������, ������� � <b>config.conf</b>: <p><center><font color=green>\$admin_hash=\"".md5($_POST['pass'].$koldunstvo)."\";</font>
<p>".$f_login."</center>";

	} else {

$s.="<p>� ����� config.conf ���������� \$admin_hash ������ ��� ������ �������. ������� �� ����������� ����� ��� ������ ��� ������. �������� ������:
<center>
<form name=hash method=POST action=".$mypage.">
<input type=text size=10 name=pass value='".htmlspecialchars($_POST['pass'])."'>
<input type='submit' name='create' value='������������'>
</form>
</center>";

}

} else { // ������ ����������

	if($admin) { // ���������?
		if(isset($_POST['logout'])) { // ������ �����������?
			$admin=false;
			set_cookie("adm","logoff", time()-100, "/", "", 0, true);

			$s .= "<p><center><font color=green>�������������!</font> &nbsp; ".$f_login."</center>";
			} else { $s.= "<p><center>".$f_logout."</center>"; }
	} else { // ����������?
		if(isset($_POST["login"])) { // �������� ����������?
			if(md5($_POST["login"].$koldunstvo) == $admin_hash) { // ������ ������?
				$admin=true;
				set_cookie("adm", broident($admin_hash.$koldunstvo), time()+86400*365, "/", "", 0, true);
				$s .= "<p><center><font color=green>������������!</font> &nbsp; ".$f_logout."</center>";
			} else { // ������ ��������?
				logi("login.log","\n".date("Y/m/d h:i:s").": (".$lju." ".$sc." ".$IP." ".$BRO.")"); sleep(5);
				$s .= "<p><center><font color=red>�������� ������!</font>
<br>����� �������������� ������, ���� �������� ���������� \$admin_hash=\"\"; � config.php<p>".$f_login."</center>";
			}
		} else { $s.= "<p><center>".$f_login."</center>"; }
	}
}


$s.="<p class=z>MySQL

<form name=create method=POST action=".$mypage.">
<p><center><table border=1 cellspacing=0 cellpadding=10>";

foreach($test_basa as $table=>$a) { $b='';
	$name=$a[0];

	if($_POST['create']==$table) {

		if($admin) {
			$sql="CREATE TABLE `".$msq_basa."`.`".$table."` ".$a[1]."
			ENGINE = MyISAM DEFAULT CHARSET = ".$msq_charset." COMMENT = '".e($name)."';";
			ms($sql,"_l",0); $b="<font color=green>create!</font> &nbsp;";
			$s.=$msqe;
		}
	}

	if(!msq_table($table)) $a=$b.($admin?"<font color=red>�������: </font> &nbsp; <input type='submit' name='create' value='".$table."'>":"���������� ����� �������");
	else { $a=ms("SELECT COUNT(*) FROM `".$table."`","_l",0); $a="<font color=green>���������: </font>".$a; }

	$s .= "<tr>
		<td><b>".$table."</b></td>
		<td>".$a."</td>
	</tr>";

}


$s.="</table></center></form>";

$s.="<center><p><div id=soobshi><input type=button value='������������ �������� ����������' onclick=\"document.getElementById('soobshi').innerHTML = '<img src=http://lleo.aha.ru/blog/stat?link={httphost}>';\"></div></center>";

$s.="<p><center>
<form name=create method=POST action=".$mypage.">
";



$upgrade=glob($host_module."upgrade/*.php");

list($UGET)=explode(" ",$_POST['upgrade'].$_GET['upgrade'],2);

foreach($upgrade as $l) { $U=preg_replace("/^.*?\/([^\/\.]+)\.php$/si","$1",$l);
	$s.="<h2>$U</h2><p>";
	$UPGR=($UGET==$U?1:0);
	include_once $l;
}


// $_PAGE['body'] = $s;
print $s."</form></center>";

function upgr_warning($l,$mes) {
	print "<br><b>$l</b>: $mes";

	$GLOBALS['s'].="<table border=1 cellspacing=0 cellpadding=10><tr valign=center><td>$mes</td>
<td><input type='submit' name='upgrade' value='$l ���������?'></td></tr></table>";

}

function msq_pole($tb,$pole) {
        $pp=ms("SHOW COLUMNS FROM `$tb`","_a",0); if(sizeof($pp)) foreach($pp as $p) if($p['Field']==$pole) return true;
        return false;
}

function msq_table($pole) {
        $ppp=ms("SHOW TABLES","_a",0); if(sizeof($ppp)) foreach($ppp as $pp) if(sizeof($pp)) foreach($pp as $p) if($p==$pole) return true;
        return false;
}


function upgrade_redirect($l) {
	print "<p><font color=magenta>����� 5 ������ ����� �������������� �������������...</font>
<noscript><meta http-equiv=refresh content=\"5;url='".$GLOBALS['mypage']."?upgrade=$l%20refresh'></noscript>";
}


// �������� ���� � ����
function msq_add_pole($table,$pole,$znachenie,$text) { global $U,$UPGR;
        if(!msq_pole($table,$pole)) if($UPGR) {
                msq("ALTER TABLE `".$table."` ADD `".$pole."` ".$znachenie." NOT NULL");
                print "<p><b>$U</b>:<font color=magenta>� `$table` ��������� ���� `$pole` ($text)</font> ".$msqe;
        } else {
                upgr_warning($U,"���������� �������� ���� `$pole` � ������� `$table` ($text)");
        }
}

// ������� ���� �� ����
function msq_del_pole($table,$pole,$text) { global $U,$UPGR;
        if(msq_pole($table,$pole)) if($UPGR) {
                msq("ALTER TABLE `".$table."` DROP `".$pole."`");
                print "<p><b>$U</b>:<font color=magenta> �� `$table` ������� ���� `$pole` ($text)</font> ".$msqe;
        } else {
                upgr_warning($U,"���������� ������� ���� `$pole` �� ������� `$table` ($text)");
        }
}


?>