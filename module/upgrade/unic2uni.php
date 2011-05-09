<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// Перекачать старую базу комментариев

$action='UNIC2UNI'; $Nskip=5000;
if((1||msq_pole('uni','unic')===false) and
 //msq_table('`lleo`.`dnevnik_comments`') and 
$PEST['action']==$action) { $admin_upgrade=true;

if(intval($_GET['skip'])==0) {
	if(msq_pole('uni','unic')===false) {
		msq("ALTER TABLE `uni` ADD `unic` bigint(20) unsigned NOT NULL COMMENT 'старое говно, потом удалим'");
	} else {
		if($_GET['forse']=='yes') { msq("TRUNCATE TABLE `uni`"); msq("TRUNCATE TABLE `unijur`"); }
		else die("Работа уже проделана. Чтобы удалить таблицы и создавать повторно, сотри из тыблицы uni поле unic.");
	}
}

// die('dd');

//==========================================================================================	
// взять соответствия Date - num, чтоб по одной всякий раз не лазить
// 2010122611530000001
//    0000000010000000
//    1000000000000123 

// взять из таблицы unic кучку
$pp=ms("SELECT * FROM ".$GLOBALS['db_unic']." LIMIT ".intval($_GET['skip']).",".$Nskip,"_a");

if($pp!==false && sizeof($pp)) { // если естьчо
	$s .= admin_rereload($action,$Nskip,5); // 5 секунд
	$s .= "<p>Обрабатывается ".$_GET['skip']." ".$Nskip." (".ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic'],"_l").")<p class=br>";

	foreach($pp as $p) { // для каждого элемента

	if($p['openid']!='') $login=$p['openid']; // если есть openid - он будет login
	elseif($p['login']!='') { // иначе - логином будет логин
		if(!preg_match("/[^0-9a-z\_\-]/s",$p['login'])) $login=$p['login']; // если он без ошибки
		else $login=''; // иначе вапще нах
		}
	else $login=''; // вапще нах


	$uni='10000000'.sprintf("%08d",$p['id']); // новый uni (если окажется логин - заменим)

if($login!='') { // добавить в uni и unijur - если ЛОГИН есть

	// uni - новая unic-база
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

	$uni=mysql_insert_id(); // новый uni теперь такой

	$s.= " <font color=green>".$login."</font>";
} else 	$s.=" # ";

// если были админские пометки - добавить их в unijur
if($p['capchakarma']>1 or $p['admin']!='user') { // если были админские пометки - добавить их в unijur
	msq_add('unijur',array(
	'jur'=>1, // для журнала номер 1 - пока лишь он есть
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

if(1|| msq_pole('uni','unic')===false) $s .= admin_kletka('action',"перегнать базу UNIC в новый формат",$action);

/*

CREATE TABLE IF NOT EXISTS `dnevnik_comm` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `unic` bigint(20) unsigned NOT NULL COMMENT 'id автора',

CREATE TABLE IF NOT EXISTS `dnevnik_plusiki` (
  `unic` bigint(20) unsigned NOT NULL,
  `uni` bigint(20) unsigned NOT NULL,

CREATE TABLE IF NOT EXISTS `dnevnik_posetil` (
  `unic` bigint(20) unsigned NOT NULL,
  `uni` bigint(20) unsigned NOT NULL,

CREATE TABLE IF NOT EXISTS `golosovanie_golosa` (
  `unic` bigint(20) unsigned NOT NULL COMMENT 'id голосующего',
  `uni` bigint(20) unsigned NOT NULL COMMENT 'id голосующего',









--
-- Структура таблицы `jurs`
--
CREATE TABLE IF NOT EXISTS `jurs` (
  `jur` int(10) unsigned NOT NULL auto_increment COMMENT 'Номер журнала',
  `jurname` varchar(32) NOT NULL COMMENT 'Имя журнала',
  `admin` int(10) unsigned NOT NULL COMMENT 'Главный владелец',
   PRIMARY KEY (`jur`),
   KEY `jurname` (`jurname`(32))
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='База журналов' AUTO_INCREMENT=0 ;


-- --------------------------------------------------------
--
-- Структура таблицы `uni`
--
CREATE TABLE IF NOT EXISTS `uni` (
  `uni` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Личный номер из куки',
  `login` varchar(64) NOT NULL COMMENT 'vasya либо vasya@openid.site',
  `password` varchar(32) NOT NULL,
  `mail1` varchar(64) NOT NULL COMMENT 'mail при регистрации - нельзя сменить никогда',
  `mail` varchar(64) NOT NULL COMMENT 'действующий mail (изначально совпадает)',
  `gsm1` varchar(16) NOT NULL COMMENT 'мобильник при регистрации - нельзя сменить никогда',
  `gsm` varchar(16) NOT NULL COMMENT 'действующий мобильник (изначально совпадает)',
  `realname` varchar(128) NOT NULL COMMENT 'личное: имя',
  `birth` date NOT NULL COMMENT 'личное: дата рождения',
  `mail_comment` enum('1','0') NOT NULL default '1' COMMENT 'личное: отправлять ли комментарии на email?',
  `aboutme` varchar(2048) NOT NULL COMMENT 'личное: О себе',
  `time_reg` int(11) NOT NULL default '0' COMMENT 'время регистрации',
  `ipn` int(10) unsigned NOT NULL COMMENT 'ip при последнем редактировании личной карточки',
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'время последнего
   PRIMARY KEY (`uni`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='База регистраций' AUTO_INCREMENT=0 ;

--
-- Структура таблицы `unijur`
--
CREATE TABLE IF NOT EXISTS `unijur` (
  `jur` int(10) unsigned NOT NULL COMMENT 'Номер журнала',
  `uni` bigint(20) unsigned NOT NULL COMMENT 'Номер unic пользователя',
  `capchakarma` tinyint(3) unsigned NOT NULL default '0' COMMENT 'Капча-карма',
  `dostup` enum('user','podzamok','mudak','writer','admin') NOT NULL,
  `abouthim` varchar(2048) NOT NULL COMMENT 'О нем',
   PRIMARY KEY (`jur`,`uni`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Настройки посетителей для своего журнала' AUTO_INCREMENT=0 ;

-- --------------------------------------------------------
--
-- Структура таблицы `unic`
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
  `capchakarma` tinyint(3) unsigned NOT NULL default '0' COMMENT 'Капча-карма нового формата',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Логины посетителей' AUTO_INCREMENT=7528 ;

-- --------------------------------------------------------

1 000 000 000

*/
?>