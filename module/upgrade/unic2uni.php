<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����
// ���������� ������ ���� ������������

$action='UNIC2UNI'; $Nskip=5000;
if((1||msq_pole('uni','unic')===false) and
 //msq_table('`lleo`.`dnevnik_comments`') and 
$PEST['action']==$action) { $admin_upgrade=true;

if(intval($_GET['skip'])==0) {
	if(msq_pole('uni','unic')===false) {
		msq("ALTER TABLE `uni` ADD `unic` bigint(20) unsigned NOT NULL COMMENT '������ �����, ����� ������'");
	} else {
		if($_GET['forse']=='yes') { msq("TRUNCATE TABLE `uni`"); msq("TRUNCATE TABLE `unijur`"); }
		else die("������ ��� ���������. ����� ������� ������� � ��������� ��������, ����� �� ������� uni ���� unic.");
	}
}

// die('dd');

//==========================================================================================	
// ����� ������������ Date - num, ���� �� ����� ������ ��� �� ������
// 2010122611530000001
//    0000000010000000
//    1000000000000123 

// ����� �� ������� unic �����
$pp=ms("SELECT * FROM ".$GLOBALS['db_unic']." LIMIT ".intval($_GET['skip']).",".$Nskip,"_a");

if($pp!==false && sizeof($pp)) { // ���� ������
	$s .= admin_rereload($action,$Nskip,5); // 5 ������
	$s .= "<p>�������������� ".$_GET['skip']." ".$Nskip." (".ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic'],"_l").")<p class=br>";

	foreach($pp as $p) { // ��� ������� ��������

	if($p['openid']!='') $login=$p['openid']; // ���� ���� openid - �� ����� login
	elseif($p['login']!='') { // ����� - ������� ����� �����
		if(!preg_match("/[^0-9a-z\_\-]/s",$p['login'])) $login=$p['login']; // ���� �� ��� ������
		else $login=''; // ����� ����� ���
		}
	else $login=''; // ����� ���


	$uni='10000000'.sprintf("%08d",$p['id']); // ����� uni (���� �������� ����� - �������)

if($login!='') { // �������� � uni � unijur - ���� ����� ����

	// uni - ����� unic-����
	msq_add('uni',array(
	'login'=>e($login),
	'password'=>($p['password']!=''?e($p['password']):''),
	'mail1'=>e($p['mail']),
	'mail'=>e($p['mail']),
	'gsm1'=>'',
	'gsm'=>'',
	'realname'=>e($p['realname']),
	'birth'=>e($p['birth']),
	'mail_comment'=>e($p['mail_comment']),
	'aboutme'=>($p['lju']!=''?e("LJ: ".$p['lju']):''),
	'time_reg'=>$p['time_reg'],
	'ipn'=>$p['ipn'],
	'timelast'=>$p['timelast'],

	'unic'=>$p['id']
	));

	$uni=mysql_insert_id(); // ����� uni ������ �����

	$s.= " <font color=green>".$login."</font>";
} else 	$s.=" # ";

// ���� ���� ��������� ������� - �������� �� � unijur
if($p['capchakarma']>1 or $p['admin']!='user') { // ���� ���� ��������� ������� - �������� �� � unijur
	msq_add('unijur',array(
	'jur'=>1, // ��� ������� ����� 1 - ���� ���� �� ����
	'uni'=>$uni,
	'capchakarma'=>$p['capchakarma'],
	'dostup'=>$p['admin'],
	'abouthim'=>''
	));
}

}

} else 	$s .= admin_redirect($mypage,0);
//==========================================================================================	
} // elseif(msq_table('dnevnik_comments') and ms("SELECT COUNT(*) FROM `dnevnik_comments`","_l") > ms("SELECT COUNT(*) FROM `dnevnik_comm`","_l"))

if(1|| msq_pole('uni','unic')===false) $s .= admin_kletka('action',"��������� ���� UNIC � ����� ������",$action);

/*

CREATE TABLE IF NOT EXISTS `dnevnik_comm` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `unic` bigint(20) unsigned NOT NULL COMMENT 'id ������',

CREATE TABLE IF NOT EXISTS `dnevnik_plusiki` (
  `unic` bigint(20) unsigned NOT NULL,
  `uni` bigint(20) unsigned NOT NULL,

CREATE TABLE IF NOT EXISTS `dnevnik_posetil` (
  `unic` bigint(20) unsigned NOT NULL,
  `uni` bigint(20) unsigned NOT NULL,

CREATE TABLE IF NOT EXISTS `golosovanie_golosa` (
  `unic` bigint(20) unsigned NOT NULL COMMENT 'id �����������',
  `uni` bigint(20) unsigned NOT NULL COMMENT 'id �����������',









--
-- ��������� ������� `jurs`
--
CREATE TABLE IF NOT EXISTS `jurs` (
  `jur` int(10) unsigned NOT NULL auto_increment COMMENT '����� �������',
  `jurname` varchar(32) NOT NULL COMMENT '��� �������',
  `admin` int(10) unsigned NOT NULL COMMENT '������� ��������',
   PRIMARY KEY (`jur`),
   KEY `jurname` (`jurname`(32))
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='���� ��������' AUTO_INCREMENT=0 ;


-- --------------------------------------------------------
--
-- ��������� ������� `uni`
--
CREATE TABLE IF NOT EXISTS `uni` (
  `uni` bigint(20) unsigned NOT NULL auto_increment COMMENT '������ ����� �� ����',
  `login` varchar(64) NOT NULL COMMENT 'vasya ���� vasya@openid.site',
  `password` varchar(32) NOT NULL,
  `mail1` varchar(64) NOT NULL COMMENT 'mail ��� ����������� - ������ ������� �������',
  `mail` varchar(64) NOT NULL COMMENT '����������� mail (���������� ���������)',
  `gsm1` varchar(16) NOT NULL COMMENT '��������� ��� ����������� - ������ ������� �������',
  `gsm` varchar(16) NOT NULL COMMENT '����������� ��������� (���������� ���������)',
  `realname` varchar(128) NOT NULL COMMENT '������: ���',
  `birth` date NOT NULL COMMENT '������: ���� ��������',
  `mail_comment` enum('1','0') NOT NULL default '1' COMMENT '������: ���������� �� ����������� �� email?',
  `aboutme` varchar(2048) NOT NULL COMMENT '������: � ����',
  `time_reg` int(11) NOT NULL default '0' COMMENT '����� �����������',
  `ipn` int(10) unsigned NOT NULL COMMENT 'ip ��� ��������� �������������� ������ ��������',
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '����� ����������
   PRIMARY KEY (`uni`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='���� �����������' AUTO_INCREMENT=0 ;

--
-- ��������� ������� `unijur`
--
CREATE TABLE IF NOT EXISTS `unijur` (
  `jur` int(10) unsigned NOT NULL COMMENT '����� �������',
  `uni` bigint(20) unsigned NOT NULL COMMENT '����� unic ������������',
  `capchakarma` tinyint(3) unsigned NOT NULL default '0' COMMENT '�����-�����',
  `dostup` enum('user','podzamok','mudak','writer','admin') NOT NULL,
  `abouthim` varchar(2048) NOT NULL COMMENT '� ���',
   PRIMARY KEY (`jur`,`uni`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='��������� ����������� ��� ������ �������' AUTO_INCREMENT=0 ;

-- --------------------------------------------------------
--
-- ��������� ������� `unic`
--

CREATE TABLE IF NOT EXISTS `unic` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(32) NOT NULL,
  `openid` varchar(64) NOT NULL COMMENT 'openid',
  `obr` enum('realname','login','openid') NOT NULL default 'realname',
  `lju` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `realname` varchar(128) NOT NULL,
  `mail` varchar(64) NOT NULL,
  `mail_checked` enum('1','0') NOT NULL default '0',
  `mail_comment` enum('1','0') NOT NULL default '1',
  `site` varchar(128) NOT NULL,
  `birth` date NOT NULL,
  `admin` enum('user','podzamok','admin','mudak') NOT NULL,
  `ipn` int(10) unsigned NOT NULL,
  `time_reg` int(11) NOT NULL default '0',
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `capcha` enum('yes','no') NOT NULL default 'no',
  `capchakarma` tinyint(3) unsigned NOT NULL default '0' COMMENT '�����-����� ������ �������',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='������ �����������' AUTO_INCREMENT=7528 ;

-- --------------------------------------------------------

1 000 000 000

*/
?>