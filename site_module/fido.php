<?php // Golded

global $omsg_new,$omsg_read,$db_fid,$db_fido,$db_fido_num,$db_fidopoints,$db_fidopodpiska,$db_fidomy,
$fido_node,$fido_dostup_default,$fido_msg_rows,$fido_msg_cols,$fido_point,$fido_myaddr,$fido_myname,$fidodir,
$httpfidodir,$fido_msg_new_default,$fido_msg_reply_default,$fido_nmes,
$www_design,$filehost,$httphost,$unic,$admin,$podzamok,$realname;

$realname=$GLOBALS['IS']['realname'];

$fido_node=parse_config('address'); // "2:5020/313";

$omsg_new=$www_design."e3/ledgreen.png";
$omsg_read=$www_design."e3/ledyellow.png";

$searchtag=($_GET['search']?",search:'".strtr($_GET['search'],"\"'","--")."'":"");

$db_fid=parse_config('set sqldb='); if($db_fid!='') $db_fid="`$db_fid`."; // fido
$db_fido=$db_fid.'`'.parse_config('db_fido').'`'; // fidoecho
$db_fido_num=$db_fid.'`'.parse_config('db_fido_num').'`'; // fidoecho_num
$db_fidopoints=$db_fid.'`'.parse_config('db_fidopoints').'`'; // fidopoints
$db_fidopodpiska=$db_fid.'`'.parse_config('db_fidopodpiska').'`'; // fidopodpiska
$db_fidomy=$db_fid.'`'.parse_config('db_fidomy').'`'; // `fidomy`

$fido_dostup_default=parse_config('fido_dostup_default'); // write
$fido_msg_rows=parse_config('fido_msg_rows'); // 20
$fido_msg_cols=parse_config('fido_msg_cols'); // 80
$fido_nmes=parse_config('fido_nmes'); // 6

/*
$db_fid="`fido`";
$db_fido=$db_fid.".`fidoecho`";
$db_fido_num=$db_fid.".`fidoecho_num`";
$db_fidopoints=$db_fid.".`fidopoints`";
$db_fidopodpiska=$db_fid.".`fidopodpiska`";
$db_fidomy=$db_fid.".`fidomy`";

$fido_dostup_default='write';
$fido_msg_rows=20;
$fido_msg_cols=80;
$fido_nmes=6;
*/

if(($p=intval(ms("SELECT `point` FROM $db_fidopoints WHERE `unic`='$unic'","_l")))!=0
) { $fido_point=$p; $fido_myaddr=$fido_node.'.'.$p; }
else $fido_point=$fido_myaddr=0;

$fido_myname=c($GLOBALS['IS']['realname']); if($fido_myname=='') $fido_myname="#".$unic;

$fidodir=$filehost."fido/";
$httpfidodir=$httphost."fido/";

$fido_msg_new_default="������, ������� {to}!\n\n{body}\n\n\n---\n� ���������, {realname}\n";
$fido_msg_reply_default="�����������, {to}!\n\n��� {date} {to} ({addr}) �����:\n\n{body}\n\n\n---\n� ���������, {realname}\n";

// idie("http://lleo.aha.ru/fido#".urlencode('area://ru.ftn.develop/?msgid=2:5020/313 03FDC37F'));

/*
$ara=('AREA'=>'$echo',
MSGID
REPLYID
SUBJ
FROMNAME
TONAME
FROMADDR
TOADDR
NUMBER
BODY
RAZMER
DATETIME
RECIVDATE
ATTRIB 	int(11) 	
*/

setmysql_utf();

function setmysql_utf() { global $msq_charset;
   $msq_charset='utf8';
   mysql_query("SET NAMES $msq_charset");
   mysql_query("SET @@local.character_set_client=$msq_charset");
   mysql_query("SET @@local.character_set_results=$msq_charset");
   mysql_query("SET @@local.character_set_connection=$msq_charset");
}

function ddump($s) { for($o='',$i=0;$i<min(1024,strlen($s));$i++) { $l=$s[$i];
$o.="<font color=green>".h($l)."</font>".sprintf("%03d", ord($l)).($i%20?" ":"<br>"); } return "<tt>$o</tt>";
}

function phfitorun($s) { global $SRC_PATH, $CONFIG_FILE, $DEBUG, $BASE_DIR, $MODULE, $COLOUR,$fidodir;
	$dd=getcwd(); chdir($fidodir); require_once('phfito.php');
	$str=split(' ','phfito '.$s);
	run_phfito_run(sizeof($str), $str); $s=ob_get_contents();
	chdir($dd); return $s;
}

function fido_sendmail($echo,$fromuser,$touser,$fromaddr,$toaddr,$subject,$body,$replyid) {
	$echo=strtolower($echo);
	if(is_file($GLOBALS['filehost']."fido/phfito.lock")) idie('phito.lock!<br>��������� ����� 10 ������.');
	global $SRC_PATH, $CONFIG_FILE, $DEBUG, $BASE_DIR, $MODULE, $COLOUR;

	$DEBUG=1;

	$body=str_replace("\n","\r",$body); 
//	$body=str_replace("\n",chr(13),$body);

if($echo=='netmail') {
	setmysql_utf();
// idie('1');
	$ara=array(
		'AREAN'=>area2arean($echo),
		'SUBJ'=>e(wu($subject)),
		'MSGID'=>e($fromaddr." ".substr(md5($fromaddr.$body.rand(0,32000)),0,8)),
		'REPLYID'=>e($replyid),
		'FROMNAME'=>e(wu($fromuser)),
		'TONAME'=>e(wu($touser)),
		'FROMADDR'=>e($fromaddr),
		'TOADDR'=>e($toaddr),
		'NUMBER'=>intval(ms("SELECT COUNT(*) FROM ".$GLOBALS['db_fido']." WHERE `AREAN`='".area2arean($echo)."'","_l",0)),
		'BODY'=>e(chr(1)."CHRS: CP866 2\r".($replyid!=''?chr(1)."REPLY: ".$replyid."\r":'').wu($body)),
		'RAZMER'=>strlen($body),
		'DATETIME'=>e(date("Y-m-d H:i:s")),
		'RECIVDATE'=>e(date("Y-m-d H:i:s")),
		'ATTRIB'=>0
	);
	msq_add($GLOBALS['db_fido'],$ara);
	$GLOBALS['new_id']=mysql_insert_id();


//	dier($ara);
	$attrib=0x00000101;
} else $attrib=0;

if($replyid==0) $replyid=false; else $body=chr(1)."REPLY: ".$replyid."\r".$body;
	$dd=getcwd(); chdir($GLOBALS['fidodir']); require_once('phfito.php');
	$s=xinclude('S_config.php');
	$s.=xinclude('P_phfito.php');
	$s.=aks_init('Phfito', $CONFIG_FILE, array(), array());
	$s.=Tosser_init();
	$s.=PostMessage($echo,wa($fromuser),wa($touser),$fromaddr,$toaddr,wa($subject),wa($body),$attrib,$replyid);
	$s.=Tosser_done();
	$s.=aks_done();

/*
$g=fopen($GLOBALS['filehost']."/fido/00000000fidomail_test.log","a+"); fputs($g,"\n\n-----------------------------
echo='$echo'

from='$fromuser'

to='$touser'

fa='$fromaddr'

ta='$toaddr'

SUBJ='$subject'

BODY='$body'

att='$attrib'

repl='$replyid'
"); fclose($g);
*/
	chdir($dd);

$g=fopen("_fidomail_test_.log","a+"); fputs($g,"\n\n-----------------------------\n".$k); fclose($g);

return $s; // $s=ob_get_contents(); idie('s='.$s);
}

// function fido_toss() { return phfitorun('-t'); } // -ts

$GLOBALS['fidokluge']=0;

function obrabody2($s,$name) { global $fidokluge;
	$s=trim($s,"\t\n\r ");
	$a=explode(chr(13),$s);
	$p=explode(' ',preg_replace("/\s+/s",' ',$name));
	$pr=substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1).(isset($p[2])?substr($p[2],0,1):''):'').'> ';
	foreach($a as $n=>$c) { $c=trim($c,"\n\r\t ");
		if(substr($c,0,6)==chr(1).'PATH:') { if(!$fidokluge) { unset($a[$n]); continue; } $c=h($pr.substr($c,1)); }
		elseif(substr($c,0,8)=='SEEN-BY:') { if(!$fidokluge) { unset($a[$n]); continue; } $c=h($pr.$c); }
		elseif(substr($c,0,1)==chr(1)) { if(!$fidokluge) { unset($a[$n]); continue; } $c=h($pr.substr($c,1)); }
		elseif(stristr(substr($c,0,10),'* Origin:')) $c=h($pr.$c);
		elseif(substr($c,0,3)=='---') $c=h($pr.$c);
		elseif(strstr(substr($c,0,5),'>'))  $c=h(ltrim(preg_replace("/^([^>]+>)/s","$1>",$c)));
		elseif($c!='') $c=h($pr.$c);
		$a[$n]=$c;
	} $s=implode("\n",$a);
	return uw($s);
}


function obrabody1($s) { //global $fidokluge; 

$a=explode(chr(13),$s);

$fidokluge=1;

	foreach($a as $n=>$c) {
		$c=str_replace('/','#l/l/e/o#',$c);
		if(substr($c,0,6)==chr(1).'PATH:') { if(!$fidokluge) { unset($a[$n]); continue; } $c="<font color=red>".h(substr($c,1))."</font>"; }
		elseif(substr($c,0,8)=='SEEN-BY:') { if(!$fidokluge) { unset($a[$n]); continue; } $c="<font color=red>".h($c)."</font>"; }
		elseif(substr($c,0,1)==chr(1)) { if(!$fidokluge) { unset($a[$n]); continue; } $c="<font color=red>".h(substr($c,1))."</font>"; }
		elseif(stristr(substr($c,0,10),'* Origin:')) $c="<font color=magenta>".h($c)."</font>";
		elseif(substr($c,0,3)=='---') $c="<font color=magenta>".h($c)."</font>";
		elseif(strstr(substr($c,0,5),'>')) $c="<font color=green>".h($c)."</font>";
		else $c=h($c);

		$c=rtrim(preg_replace("|([ ])#l/l/e/o#(.*?)#l/l/e/o#([ \.\!\?\,\n])|s","$1<i>/$2/</i>$3",$c." "));
		$c=str_replace('#l/l/e/o#','/',$c);
		$c=rtrim(preg_replace("/([ ])\*(.*?)\*([ \.\!\?\,$]*)/s","$1<b>*$2*</b>$3",$c." "));
//		$c=preg_replace("/([ ])\_(.*?)\_([ \.\!\?\,$\n])/s","$1<u style='font-size:120%;'>$2</u>$3",$c);
		$c=rtrim(preg_replace("/([ ])_(.*?)_([ \.\!\?\,$\n])/s","$1<u>_$2_</u>$3",$c." "));

// 1. �� ������������ "�����������" ��� FIDONET (� ��� usenet) ��������� *����������* /������/ _������������_

		$a[$n]=$c;
	} $s=implode("<br>",$a); return uw($s);
}

//function aw($s) { return(iconv("cp866","windows-1251//IGNORE",$s)); }
function wa($s) { return(iconv("windows-1251","cp866//IGNORE",$s)); }

function fido_ajax() { $a=RE('a');

global $omsg_new,$omsg_read,$db_fid,$db_fido,$db_fido_num,$db_fidopoints,$db_fidopodpiska,$db_fidomy,$db_unic,
$fido_node,$fido_dostup_default,$fido_msg_rows,$fido_msg_cols,$fido_point,$fido_myaddr,$fido_myname,$fidodir,
$httpfidodir,$fido_msg_new_default,$fido_msg_reply_default,$fido_nmes,
$www_design,$filehost,$httphost,$unic,$admin,$podzamok,$realname;

if($a=='admin_clean_lock') {
	$f=$filehost."fido/phfito.lock";
	unlink($f);
	return "salert('������: `$f`',1000)";
}

//=========================================================================
if($a=='about_help') { // � �������
$o="����� ���� ������ ������ ��������� ������-���� ����, ��������� �� �������� PHP. ���� ��������� ��������� �
�������� � ���� ���-������, ���-���� � ���-������. ��� ����� ��������� ��������� ���������� ������ ��������,
���������� PHP-������ PhFito. ���� ���-���� ������� <a href=http://vds.lushnikov.net/wfido/>����������</a> ���� ��������,
�� ������� �������������� � � �������� ����� �������.

<p>���� ������ ��������� �� ������ ����������. �� ��������� ��������� ����� �������� � ��������� ������.

<p>���������� ������� ����� ������ ������� <a href='mailto:lleo@aha.ru'>lleo@aha.ru</a> ������� �� ������� � �������:
������ ��������, ����� ���������, ���� ������� (�������, ��� �������� � ������ �� JS), � ����� ����, ��� ����������
� ���������� - �������, ������������, ��������, ��������� � ��������� ���������� ���
<a href='http://lleo.aha.ru/fido#area:ru.ftn.develop'>RU.FTN.DEVELOP</a>



<p><br>������� ���������� �� ��������. ����� ������� �������� � ���������. �� ������ ������ �������� �������:

<br>������� �����/���� - ������� ���
<br>������� ������/����� - ������� �������
<br>R - �������� �� ���������
<br>N - ����� ��������� � ���
<br>M - �������� ������
<br>DEL - ������� �������

<p><br>������� ���������� �� ����.

<br>��� ������ ������� ����� ���?
<br>�������� ����� �������� ������ � ����� ���, ������� �� �� ��������, � � ������ ������ �� ������� 'create'.
<br>����� ��������� ������� ��������� �������� � ���� Admin - Uplink Echo, ����� ��������� �� ����� ��� ������ �������.

";

return "
helps('about_help',\"<fieldset><legend>FIDO: About v 1.1</legend>".njsn($o)."</fieldset>\");
posdiv('about_help',-1,-1); idd('about_help').focus();
";
}

if($a=='joinme') { // ���� ������

if($GLOBALS['IS']['openid']!='' or ($GLOBALS['IS']['login']!='' and $GLOBALS['IS']['password']!='')) {
	if($realname=='' or preg_replace("/[^a-z0-9\-\_\. ]+/si","",$realname)!=$realname) {
$o="� ���� ����������� ��������� � ������ �������� ����
<br><b>realname</b> (\"��� ��� ���\").
<p>����� �� ���� ������� � FIDO, ��� ������ �������� ������
<br>�� ��������� ���� ������ ��������, ����, �������, �����,
<br>������ ��� �������������. ������� � ���������� ���������
<br>�� ������ ���� �������. ������: Vasyly Pupkin
<p>������� ���� �������� � �������: <span class=l onclick=\"majax('login.php',{action:'openid_form'})\">".$GLOBALS['imgicourl']."</span>";
	} else 
if(($rr=ms("SELECT `point` FROM $db_fidopoints WHERE `unic`='$unic'","_l"))!='')
$o="� ���� ��� ���� ����� <b>".$fido_node.".".$rr."</b>";
else { $o="
������, $realname!

<p>������ ���� ����� �� ".($podzamok?5:20)." �� 32768:
<center>".$fido_node.".<input id='fidoin_p' size=4 type=text></center>

<p class=r>������� � ���� ����, � �������, ���:
".str_replace("\n","<br>","

1. ���� �������� ��������� �������������� ����� ������,
��� ������� ��������� ������� ���������, ����� �� ��� �����.
��������� ���� ��������� ��������, ����� ������� ���������
�� \"��\", ���������� ���� � �������������.

2. �������� �� �������� ���-��������� ��������� ���� �������,
����������� �������� � ���� ����� ��������, ���� ���� ����� ��
����������� ������� � �����������, �������� �� ���������.

3. � ���� ���������� ��� ���� �������: ������� (������ ������)
� �������������� - �������, ��� ������, ������������ �����������,
����� ���� �����������. ��� �������������� ���������� �������,
������������� �� ������������. ����������� ���������� ������ �
�������������� �� ��� ���, ���� �� ��������� ������� (��� ������
����������� ������������) � �� ����� �������, ��� ����� ���������.

4. � ���� ���� �� ���������� ����������� - ����� ����� �����, �
���������� ������ ����� ���� ��������. ��� ���� ���������������
�� ��������� ������ ����� ���� (�������� ����). ����� �������,
���� �� ��������� ������� ��������������, �� ������������ ����,
��� ��������� ����. ���������� ������ ���������.

<input id='fidoin_ch' type=checkbox> � ��� �������

<center><input type=button value='������������������' onclick=\""
."majax('module.php',{mod:'fido',a:'joinme_reg',ch:idd('fidoin_ch').checked?1:0,p:idd('fidoin_p').value})\"></center>");
}
} else $o="
����� ���� �������� ��������� ����� �� ���� �������,
<br>���������� ������ ������������ � ������. ��� ��� ������?
<br>��� ������, ��� ����� ������� ������� ������ � �������,
<br>������� �� �� ��������, ���� ���� �������� ���������.

<p>���� ���� Openid �� ������ ����� (��� ��� ��� ����� �����),
<br>����� ������������������ ��� Openid, ������� <span onclick=\"majax('login.php',{action:'oldlogin_form'})\" class='l'>����</span>.

<p>���� ���, ������ ���� ������� �������� <span class=l onclick=\"majax('login.php',{action:'openid_form'})\">".$GLOBALS['imgicourl']."</span>
<br>� ������� ����: '�����' (������ ��������� �������� �����),
<br>'������', 'email' � '���' (��� � ������� ���������� �������,
<br>��� ���� ����� ��� ����������� � ����).

<p>������� ���� ������, ����� ������������.
";

return "
helps('joinme',\"<fieldset><legend>FIDO: Join me!</legend>".njsn($o)."</fieldset>\");
posdiv('joinme',-1,-1); idd('joinme').focus();
";

}

if($a=='joinme_reg') { // �����������

$r='';
$p=RE0('p');

if(RE('ch')!=1) $o="��� ��� ����������� ������ ��� ��������� ���������� �������<br>� ������� �������, ��� ���������, � ��� ����.";
elseif(!$unic) $o="���-�� ����� ���� �� �����������.<br>�������, �������� �� � �������� ���� � ���������� ������.";
elseif(!$p) $o="����� ����� ���������.";
elseif(!$admin and (($podzamok && $p<5) or $p<20)) $o="������� ��������� �����, ����� �����������.";
elseif(!$admin and $p>32768) $o="��, �� ���� ����� �������.";
elseif($fido_point) $o="� ���� ��� ���� ����� <b>".$fido_node.".".$rr."</b>";
elseif(ms("SELECT COUNT(*) FROM $db_fidopoints WHERE `point`='$p'","_l")) $o="����� $p �����.<br>����� ����� <span class=l onclick=\"majax('module.php',{mod:'fido',a:'pointlist'})\">���������� ������</span> ������� �������.";
else {

	msq_add($db_fidopoints,array(
	'point'=>$p,
	'unic'=>$unic,
	'dostup'=>($podzamok?'write':$fido_dostup_default),
	'datereg'=>time(),
	'datelast'=>0,
	'msg_new'=>wu(str_replace('{realname}',$realname,$fido_msg_new_default)),
	'msg_reply'=>wu(str_replace('{realname}',$realname,$fido_msg_reply_default))
	));

	return "clean('joinme');
setTimeout(\"window.location='$httpfidodir';\", 5000);
salert(\"����������! ���� ����� � ���� ����:<p><center><b>".$fido_node.".".$p."</b></center>\",5000);
posdiv('salert',-1,-1);";
}

return "
helps('joinme_reg',\"<fieldset><legend>FIDO: Join me!</legend>".njsn($o)."</fieldset>\");
posdiv('joinme_reg',-1,-1); idd('joinme_reg').focus();
";

}


if($a=='pointlist') { // ���������

$pp=ms("SELECT p.`point`,u.`realname` FROM $db_fidopoints as p LEFT JOIN $db_unic as u ON p.`unic`=u.`id`","_a");

$o="<table>";
foreach($pp as $p) $o.="<tr><td>".$fido_node.".".$p['point']."</td><td>".$p['realname']."</td></tr>";
$o.="</table>";

return "
helps('pointlist',\"<fieldset><legend>FIDO: ������ ������� $fido_node</legend>".njsn($o)."</fieldset>\");
posdiv('pointlist',-1,-1); idd('pointlist').focus();
";
}


if($a=='settings_echo') { // ���������
	$pp=ms("SELECT `echo`,`echonum` FROM $db_fido_num ORDER BY `echo`","_a");
	$dd=ms("SELECT `echonum` FROM $db_fidopodpiska WHERE `point`='$fido_point'","_a",0);
	$d=array(); foreach($dd as $p) $d[$p['echonum']]=1;

	$o="<table>";
	foreach($pp as $u=>$p) { if(isset($d[$p['echonum']])) { $arean=$p['echonum'];
		$o.="<tr><td>".h(strtoupper($p['echo']))."</td><td id='chp_".$arean."'><input onchange=\"chanpod(this.checked?1:0,$arean)\" type=checkbox checked></td></tr>";
		unset($pp[$u]);
	}}

	foreach($pp as $p) { $e=$p['echo']; if($e=='netmail') continue; $arean=$p['echonum'];
		$o.="<tr><td>".h(strtoupper($e))."</td><td id='chp_".$arean."'><input onchange=\"chanpod(this.checked?1:0,$arean)\" type=checkbox></td></tr>";
	}
	$o.="</table><center><input class=r type=button value='������ ������' onclick=\"clean('settings')\"></center>";

/*
$o="<table>"; foreach($pp as $p) { $e=$p['echo']; if($e=='netmail') continue; $arean=$p['echonum'];
	$o.="<tr><td>".h(strtoupper($e))."</td><td id='chp_".$arean."'><input onchange=\"chanpod(this.checked?1:0,$arean)\" type=checkbox".(isset($d[$arean])?" checked":'')."></td></tr>";
} $o.="</table><center><input class=r type=button value='������ ������' onclick=\"clean('settings')\"></center>";
*/

return "
chanpod=function(i,n) { majax('module.php',{mod:'fido',a:'change_podpiska',n:n,i:i}); }

helps('settings',\"<fieldset><legend>FIDO: $fido_myaddr �������� �� ���</legend>".njsn($o)."</fieldset>\");
posdiv('settings',-1,-1); idd('settings').focus();
";
}

if($a=='omsg_read') { // ������ "���������"
	if(!$fido_point) return '';
	msq_update($db_fidomy,array('metka'=>'read'),"WHERE `point`='$fido_point' AND `id`='".RE0('id')."'");
	$arean=RE0('arean');
	$new=msgn_new($arean);
	$o="zabil('omsgn".$arean."',$new);";
	if(!$new) $o.="idd('omsr".$arean."').src='$omsg_read';";
	return $o;
}

if($a=='allmsg_read') { // ���: �������� ��� �����������
	$arean=RE0('arean'); if(!$arean || !$fido_point) return '';
	ms("UPDATE $db_fidomy SET `metka`='read' WHERE `point`='$fido_point' AND `arean`='$arean'","_l");
	return "
idd('omsr".$arean."').src='$omsg_read';
var a=ebasa[mya]; for(var i in a) { if(isNaN(i)) continue; a[i].m=0; }
echotype();
";
}

if($a=='msg_del') { // 1 ���������: �������
/*
ebasa[mya].i=0; - ������� ����� � ������� ������ ������
ebasa[mya].b=0; - ������� �������
ebasa[mya].len=0; - �������� �����
ebasa[mya].st=0; - ���� N, �� ������� ��� N ����� ����� �������, ���� �� ����������
ebasa[mya].en=0; - ���� 1, �� �� �������� ����� ������
*/

$id=RE0(id); $arean=RE0('arean'); if(!$arean || !$fido_point) return '';
msq("DELETE FROM $db_fidomy WHERE `point`='$fido_point' AND `id`='$id'");

	return "
".rescan_n($arean)."
var a=cphash(ebasa[mya]);
ebasa[mya]=[];

for(var i in a) { if(isNaN(i) || a[i].id==$id) continue; pushid(a[i]); }
ebasa[mya].i=a.i; ebasa[mya].b=a.b; ebasa[mya].len=(a.len-1); ebasa[mya].st=a.st; ebasa[mya].en=a.en;

echotype();

if(ebasa[mya].en==0 && (ebasa[mya].i+2+fido_nmes) > ebasa[mya].len) {
setTimeout(\"majax('module.php',{mod:'fido',a:'loadarea',arean:mya,lastid:\"+ebasa[mya][ebasa[mya].len-1].id+\"},'echotype()')\",50);
}
";
}
//                 var lastid=ebasa[mya][ebasa[mya].len-1].id;
//		salert('load del nado mya='+mya+' lastid:'+lastid,2500);
// /*	salert('load del: '+(ebasa[mya].i+2+fido_nmes)+' len:'+ebasa[mya].len,2500); */


if($a=='allmsg_new') { // ���: �������� ��� �����
	$arean=RE0('arean'); if(!$arean || !$fido_point) return '';
	ms("UPDATE $db_fidomy SET `metka`='new' WHERE `point`='$fido_point' AND `arean`='$arean'","_l");
return "idd('omsr".$arean."').src='$omsg_new';
var a=ebasa[mya]; for(var i in a) { if(isNaN(i)) continue; a[i].m=1; } echotype();";
}

if($a=='allmsg_del') { // ���: ������� ���
	$arean=RE0('arean'); if(!$arean || !$fido_point) return '';
	msq("DELETE FROM $db_fidomy WHERE `point`='$fido_point' AND `arean`='$arean'");
return "idd('omsr".$arean."').src='$omsg_read';
zabil('omsga".$arean."',0);
zabil('omsgn".$arean."',0);
ebasa[mya]=[]; ebasa[mya].i=0; ebasa[mya].b=0; ebasa[mya].len=0; ebasa[mya].st=0; ebasa[mya].en=0; echotype();";
}

if($a=='allmsg_restore') { // ���: ������������
	$arean=RE0('arean'); if(!$arean || !$fido_point) return '';
	$pp=ms("SELECT `id` FROM $db_fido WHERE `AREAN`='$arean'","_a");
	if(!sizeof($pp)) return "salert('��������� � ���� �� �������',2000)";
	msq("DELETE FROM $db_fidomy WHERE `point`='$fido_point' AND `arean`='$arean'"); // ��������
	foreach($pp as $p) msq_add($db_fidomy,array('id'=>$p['id'],'point'=>$fido_point,'arean'=>$arean,'metka'=>'new'));
	return "idd('omsr".$arean."').src='$omsg_new';
ebasa[mya]=[]; ebasa[mya].i=0; ebasa[mya].b=0; ebasa[mya].len=0; ebasa[mya].st=0; ebasa[mya].en=0;
salert('�������������: ".sizeof($pp)."',2000);
".rescan_n($arean)."
echotype();
setTimeout(\"charea($arean,'echotype()')\",50);
";
}

if($a=='loadareas') { // ������� ���� �������

	$z=($fido_point && !RE('all')); // ����� �� ���?
	$a=array();
	if($z) { // ������ ��� ������
		$pp=ms("SELECT p.`echonum`,e.`echo`
			FROM $db_fidopodpiska as p INNER JOIN $db_fido_num as e ON p.`echonum`=e.`echonum`
			WHERE p.`point`='$fido_point'");
		$pp[]=array('echonum'=>area2arean('netmail'),'echo'=>'netmail'); // � �������� �������
		foreach($pp as $l) $a[$l['echonum']]=$l['echo'];
	}

	if(!sizeof($a) || (RE('allif') && !array_search(RE('allif'),$a))) {
		// ���� ��� ��� ������ ��� ��������� ����������� ��� � ������ �� ����
		$pp=ms("SELECT `echonum`,`echo` FROM $db_fido_num");
		foreach($pp as $l) $a[$l['echonum']]=$l['echo']; // ������ $a - echonum->echo
		$z=0;
	}

	$s=$a; sort($s); // ������ $s - n->echo
	$k=array_search('netmail',$s); if($k!==false) unset($s[$k]);
	if($z) $s=array_merge(array('netmail'),$s);

	$o='';
	$are="are={";
	$aren="aren=[";
	foreach($s as $area) { $arean=array_search($area,$a);

if($z) { $all=msgn_all($arean); $new=msgn_new($arean); } else { $all=msgn_all($arean,1); $new=-1; }

	$o.="\n<div id='a".$arean."' class='fidoa' onclick='charea($arean)'>"
	.($z?"<img id='omsr".$arean."' src='".($new?$omsg_new:$omsg_read)."'>&nbsp;":'')
	.h(strtoupper($area))." "
	."<span class=br id='omsga".$arean."'>$all</span>"
	.($new<0?'':"/<span class=br id='omsgn".$arean."'>$new</span>")
	."</div>";
$aren.="$arean,";
$are.="$arean:'$area',";
}

	return "
areasmode=".($z?0:1).";"
.rtrim($are,',')."};"
.rtrim($aren,',')."];"
."zabil('fidoarea',\"".njsn($o)."\");
setmya(aren[0]);
fidoselect('fidoarea');
";
}

if($a=='change_podpiska') { // ���������
	$arean=RE0('n'); if(!$arean) return '';
	$i=ms("SELECT `i` FROM $db_fidopodpiska WHERE `point`='$fido_point' AND `echonum`='$arean'","_l",0);
	if(RE('i')==1) { // �����������
		if($i!==false)  return '';
		msq_add($db_fidopodpiska,array('point'=>$fido_point,'echonum'=>$arean));
	} else { // ����������
		if($i===false) return '';
		msq("DELETE FROM $db_fidopodpiska WHERE `i`='$i'","_l",0); // ������ �� ��������
		msq("DELETE FROM $db_fidomy WHERE `point`='$fido_point' AND `arean`='$arean'"); // �������� ������
	}
	return "idd('chp_".$arean."').style.border='1px dotted #cfcfcf';";
}

if($a=='settings_podp') { // ���������
	if(!$fido_point) idie("�� ���� �� ����� ����");

	$p=ms("SELECT `msg_new`,`msg_reply` FROM $db_fidopoints WHERE `point`='$fido_point'","_1",0);
	$p['msg_new']=uw($p['msg_new']); $p['msg_reply']=uw($p['msg_reply']);
	if(trim($p['msg_new'])=='') $p['msg_new']=str_replace('{realname}',$realname,$fido_msg_new_default);
	if(trim($p['msg_reply'])=='') $p['msg_reply']=str_replace('{realname}',$realname,$fido_msg_reply_default);

$o="
<p>������ ������ ���������:
<div><textarea id='m_msg_new' cols='".$fido_msg_cols."' rows='".min(10,$fido_msg_rows)."'>".$p['msg_new']."</textarea></div>

<p>������ ������:
<div><textarea id='m_msg_reply' cols='".$fido_msg_cols."' rows='".min(10,$fido_msg_rows)."'>".$p['msg_reply']."</textarea></div>

<div><input type=button value='���������' onclick=\"majax('module.php',{mod:'fido',a:'settings_change',msg_new:idd('m_msg_new').value,msg_reply:idd('m_msg_reply').value})\"></div>";

return "
helps('settings',\"<fieldset><legend>FIDO: ������ ������� $fido_node</legend>".njsn($o)."</fieldset>\");
posdiv('settings',-1,-1); idd('settings').focus();
";
}

if($a=='settings_change') { // ������� ���������� ���������
	msq_update($db_fidopoints,array('msg_new'=>e(wu(RE('msg_new'))),'msg_reply'=>e(wu(RE('msg_reply')))),"WHERE `point`='$fido_point'");
	return "clean('settings')";
}


if($a=='toss') { // ��������� �����
	if(is_file($filehost."fido/phfito.lock")) return "salert('phito.lock!<br>��������� ����� 10 ������.',2000)";
	$s=phfitorun('-t'); // -ts
	ob_clean(); return "zabil('fidomsg',\"<div class=br>".njs(nl2br($s))."</div>\");
	majax('module.php',{mod:'fido',a:'loadareas'});";
}

if($a=='send') { // �������� �����
	$s=file_get_contents($httpfidodir."bink.php?send");
	return "zabil('fidomsg',\"".njsn($s)."\")";
}

if($a=='admin_del_tables') { AD();
//	$area=array(); foreach(ms("SHOW TABLES FROM ".$GLOBALS['db_fid'],"_a") as $p) $area[]=$p['Tables_in_fido'];
//	foreach($area as $l) msq("DROP TABLE ".$GLOBALS['db_fid'].".`".e($l)."`");
	return "salert('����� ������: ".sizeof($area)."',1000)";
}

// ��������
//if($a=='admin_del_dupes') { AD();
//	$dupes=glob($filehost."fido/dupes/*");
//	foreach($dupes as $l) unlink($l);
//	return "salert('������� dupes: ".sizeof($dupes)."',1000)";
//}

if($a=='admin_copy_file') { AD();
//	$f="2011-01-08_11-10-44_28c51000.sa0";
	$f="32393000.su0";
	$fn=substr($f,strlen($f)-12);
	$file=$filehost."fido/in/dup/".$f;
	$filen=$filehost."fido/in/".$fn;
	copy($file,$filen);

	if(!is_file($file)) idie("File not found: ".$file);
	return "salert('���������� ����: `$file` - `$filen`',5000)";
}

if($a=='admin_copy_allfiles') { AD();
	$pp=glob($filehost."fido/in/dup/*");
	$s=''; foreach($pp as $l) {
		$fn=substr($l,strlen($f)-12);
		$filen=$filehost."fido/in/".$fn;
		if(is_file($filen)) {
			$s.="<font color=red>File exist: $fn</font><br>"; }
		else { copy($l,$filen); $s.="<font color=green>$fn</font><br>"; }
	}
	return "salert('����������� �����<br>$s',50000)";
}

// �������� ����� (�� ���������)
if($a=='select_froma') { AD(); if(!$fido_point) return; return "
selfromap=function(e){zabil('m_fido_froma',e.innerHTML);clean('Select_From_Address');};
helpc('Select_From_Address',\"<fieldset><legend>FIDO: Select From Address</legend>".njsn("
<div class=l0 onclick=selfromap(this)>".$fido_node."</div>
<div class=l0 onclick=selfromap(this)>".$fido_myaddr."</div>")."</fieldset>\")";
}

// �������� ��� ��� ������
if($a=='select_area') { if(!$fido_point) return;
	$area=RE('area');

	if(RE('o')) $a=ms("SELECT DISTINCT `echo` FROM $db_fido_num");
	else $a=ms("SELECT a.`echo` FROM $db_fidopodpiska AS p INNER JOIN $db_fido_num AS a ON a.`echonum`=p.`echonum`
WHERE p.`point`='$fido_point'");

	$s=""; foreach($a as $l) { $l=h(strtoupper($l['echo']));
		$s.="<div".($l==$area?'':" class=l onclick='asel(this)'").">$l</div>";
	}

	if(!RE('o')) $s.="<p><div class=l0 onclick=\"majax('module.php',{mod:'fido',a:'select_area',area:'".h($area)."',o:1})\"><img src='".$www_design."e3/expand_plus.gif'>more</div>";
 	elseif($admin) $s.="<p><div class='l0 r' onclick=\"majax('module.php',{mod:'fido',a:'select_area_new'})\">&lt;Create&gt;</div>";

	return "
asel=function(e){ e=e.innerHTML.replace(/<.*?>/g,''); zabil('m_select_area',e); clean('Select_Area'); };
helpc('Select_Area',\"<fieldset><legend>FIDO: Select Area</legend>".njsn($s)."</fieldset>\")";
}

// ������� ����� ��� (������ �����)
if($a=='select_area_new') { AD();
	$s="<input id='m_fido_newarea' type='text' size=20 value=''>&nbsp;&nbsp;<input type='submit' value='Create' onclick='asel()'>";
return "
asel=function(){ var a=idd('m_fido_newarea').value.toUpperCase();
if(a.replace(/[^a-z0-9\\.\\$\\&\\-\\_]/gi,'')!=a) { alert('��������� ������ ������� 0-9a-z&\$-_.'); return false; }
zabil('m_select_area',a); clean('Select_Area');
};
helpc('Select_Area',\"<fieldset><legend>FIDO: Create New Area</legend>".njsn($s)."</fieldset>\");
idd('m_fido_newarea').focus();
";
}


if($a=='fidopost') { AD();
	$area=RE('area'); // "PVT.LLEO";
	$from=RE('from'); // h($fido_myname); // "<input id='ssm_fido_from' type='text' size=20 value='aaa'>";
	$froma=$fido_myaddr;
	$to="<input id='m_fido_to' type='text' size=20 value='All'>";
	$subj=RE('subj'); $subj=strtr(strip_tags($subj),"\xBB"."\xAB".chr(151),'""-');
		$subj=preg_replace("/&lt;.*?&gt;/si",'',$subj);
	$text=RE('text'); $text=strtr(strip_tags($text),"\xBB"."\xAB".chr(151),'""-');
		$text=preg_replace("/&lt;.*?&gt;/si",'',$text);
	$replyid="";

	return write_message_okno("������� � FIDO",$area,$from,$froma,$to,$toa,$subj,$text,$replyid,0);

$aa=ms("SELECT DISTINCT `echo` FROM $db_fido_num","_a",0);

$s=''; foreach($aa as $a) { $l=$a['echo'];
		$bb=ms("SELECT `echo`,`echonum` FROM $db_fido_num WHERE `echo`='".e(strtr($l,'.','_'))."'
OR  `echo`='".e(strtr($l,'-','_'))."'
OR  `echo`='".e(strtr($l,'$','_'))."'
OR  `echo`='".e(strtr($l,'&','_'))."'","_a",0);
		if(sizeof($bb)>1) foreach($bb as $n=>$b) {
			$s.="<br>$n $l : ".$b['echo']." ".$b['echonum']."/".$bb[0]['echonum'];
		if($bb[0]['echonum']!=$b['echonum']) {
			$s.="<br>UPDATE $db_fido SET `AREAN` = '".$bb[0]['echonum']."' WHERE `AREAN`='".$b['echonum']."'";
			ms("UPDATE $db_fido SET `AREAN` = '".$bb[0]['echonum']."' WHERE `AREAN`='".$b['echonum']."'","_l");
		}
		}
 }

idie($s);
dier($aa);
}


//============================================================================

if($a=='replytext') { // ������� ������� � ���������
	$m=explode(" ","area subj to toa from froma replyid text"); foreach($m as $l) $$l=RE($l);

	mojno_write($area,$toa); // �� ���� ����� ���������
	if($froma!=$fido_myaddr && !($admin && $froma==$fido_node)) idie('�����, �����?'); // �������� ������

	$text=str_replace(array('\n','\r',"\r"),array("\n",'',''),$text);
	$pp=array(0=>array(
		'DATETIME'=>date("Y-m-d H:i:s"),
		'BODY'=>chr(1)."CHRS: CP866 2\r".($replyid!=''?chr(1)."REPLY: ".$replyid."\r":'').wu(str_replace("\n","\r",$text)),
		'NUMBER'=>0,
		'SUBJ'=>wu($subj),
		'FROMNAME'=>wu($from),
		'TONAME'=>wu($to),
		'FROMADDR'=>$froma,
		'TOADDR'=>$toa
	));

$arean=area2arean($area); // ����� ������ ��� ����� ������� ������� ��� � ���� �� ����������
$zaebalo="zabil('omsga".$arean."',".(msgn_all($arean)+1)."); zabil('omsgn".$arean."',".(msgn_new($arean)+1).");";

	if(is_file($GLOBALS['filehost']."fido/phfito.lock")) idie('phito.lock!<br>��������� ����� 10 ������.');
	$s=fido_sendmail($area,$from,$to,$froma,$toa,$subj,$text,$replyid);
// idie('1='.h($s));
	$s.=file_get_contents($httpfidodir."bink.php?send"); // � ����� ��������
	$s.=file_get_contents($httpfidodir."bink.php?mail"); // � ����� ��������
	$pp[0]['id']=$GLOBALS['new_id'];


$o="clean('fido_message'); clean('fido_message_send');";
if(RE0('dei')) $o.="if(typeof echotype != 'undefined') {
		if(idd('fidomsg')) zabil('fidomsg',\"".njsn($s)."\");
		".loadmsg($pp,"unshift")."
		".$zaebalo."
		echotype();
	}";
return $o; //."alert(\"".njsn($s)."\");";

}

if($a=='reply') { // �������� �� ���������
	$area=e(RE('area')); $id=RE0('id');
	$p=ms("SELECT `DATETIME`,`MSGID`,`BODY`,`SUBJ`,`FROMNAME`,`FROMADDR` FROM $db_fido WHERE `id`='$id'","_1");
	$from=h($fido_myname); $froma=h($fido_myaddr); $to=h(uw($p['FROMNAME'])); $toa=h(uw($p['FROMADDR']));
	$body=trim(obrabody2($p['BODY'],$p['FROMNAME']),"\n\t\r ");
if(!$fido_point) $text=str_replace('{realname}',$realname,$fido_msg_reply_default);
else $text=uw(ms("SELECT `msg_reply` FROM $db_fidopoints WHERE `point`='$fido_point'","_l",0));
$text=str_replace(array('{to}','{addr}','{date}','{body}'),array($to,$toa,$p['DATETIME'],$body),$text);
	return write_message_okno('Reply message',$area,$from,$froma,$to,$toa,"RE: ".uw($p['SUBJ']),$text,$p['MSGID']);
}



if($a=='newmsg') { // �������� ����� ���������
	$area=e(RE('area')); $from=h($fido_myname); $froma=h($fido_myaddr);
	if(!$fido_point) $text=str_replace('{realname}',$realname,$fido_msg_new_default);
	else $text=uw(ms("SELECT `msg_new` FROM $db_fidopoints WHERE `point`='$fido_point'","_l",0));
$text=str_replace(array('{to}','{addr}','{date}','{body}'),array(($area!='netmail'?'All':''),'',''),$text);
	if($area=='netmail') {
		$to="<input id='m_fido_to' type='text' size=20 value='Sysop'>";
		$toa="<input id='m_fido_toa' type='text' size=15 value='".$fido_node."'>";
	} else { $to="<input id='m_fido_to' type='text' size=20 value='All'>"; $toa=""; }
	return write_message_okno('New message',$area,$from,$froma,$to,$toa,uw($p['SUBJ']),$text,0);
}

/*
	������� �����
if($a=='newarea') { if(!$GLOBALS['admin']) idie('�� �� �����'); // ������� ����� ���
	$area=e($_REQUEST['area']); $from=h($GLOBALS['fido_myname']); $froma=h($GLOBALS['fido_myaddr']);
	$text=uw(ms("SELECT `msg_new` FROM ".$GLOBALS['db_fidopoints']." WHERE `point`='".$GLOBALS['fido_point']."'","_l",0));
	$text=str_replace(array('{to}','{addr}','{date}','{body}'),array(($area!='netmail'?'All':''),'',''),$text);
	$to="All"; $toa=""; $area="<input id='m_fido_newarea' type='text' size=20 value=''>";
	return write_message_okno('New area',$area,$from,$froma,$to,$toa,$p['SUBJ'],$text,0);
}
*/



// -- Uplink Echo | javascript:majax("module.php",{mod:"fido",a:"uplinkarea",arean:mya});
if($a=='uplinkarea') { AD(); $arean=RE0('arean'); $area=arean2area($arean); // ��������� ��� �� �������
		if($area=='netmail') idie("Netmail � ��� � �������");

// EchoArea Pvt.Lleo fido/fidoecho/pvt.lleo -g A
// EchoMode Pvt.Lleo 2:5020/1519:l
	// ��������� conf/areas
		$file=$filehost."fido/".parse_config('includeareas');
		$pp=parse_config('route',1); $uplink=false;
		foreach($pp as $l) { $e=explode(' ',$l); if(c($e[1])=='*') { $uplink=c($e[0]); break; } }
		if($uplink===false) idie("� ������� �� ������� ������ �������� ������� ����:<p><tt>Route x:xxxx/xxx *</tt>");

		if(($s=file_get_contents($file))===false) idie("�� ������ ���� `areas`!<br>".$file);
		if(!preg_match("/[\n\r]+\s*EchoArea\s+".preg_quote($area)."\s+[^\n\r]+/si","\n".$s)) idie("���-�� � �� �������� ��� <b>".h($area)."</b>");
		if(preg_match("/[\n\r]+\s*EchoMode\s+".preg_quote($area)."\s+/si","\n".$s)) idie("������ ������ ����.");
		$stroka="EchoMode ".$area." ".$uplink.":l";
		$s=preg_replace("/([\n\r]+\s*EchoArea\s+".preg_quote($area)."\s+[^\n\r]+)/si","$1\n".$stroka,$s);
		if(file_put_contents($file,trim($s))===false) idie("�� ������� �������� `areas`!<br>".$file);
		return "salert('��������� ������:<p><b><tt>$stroka</tt></b>',10000);";
}


if($a=='delarea') { AD(); $arean=RE0('arean'); $area=arean2area($arean); // ����� ���
		if($area=='netmail') idie("� �� �� ����� �����, Netmail �������?");
	// 1. ����� � ���� ��������
		msq("DELETE FROM $db_fidopodpiska WHERE `echonum`='$arean'");
	// 2. ����� ��� ������ � ����
		msq("DELETE FROM $db_fidomy WHERE `arean`='$arean'");
	// 3. ����� ��� ������� � ������� ����
		msq("DELETE FROM $db_fido WHERE `AREAN`='$arean'");
	// 4. ������� ��� �� ���� ��
		msq("DELETE FROM $db_fido_num WHERE `echonum`='$arean'");
	// 5. ������� ��� �� ����� conf/areas
		$file=$filehost."fido/".parse_config('includeareas');
		if(($s=file_get_contents($file))===false) idie("�� ������ ���� `areas`!<br>".$file);
		$o=preg_replace("/[\n\r]+\s*EchoArea\s+".preg_quote($area)."\s+[^\n\r]+/si",'',"\n".$s);
		$o=preg_replace("/[\n\r]+\s*EchoMode\s+".preg_quote($area)."\s+[^\n\r]+/si",'',"\n".$o);
		if($o!=$s) { if(file_put_contents($file,trim($o))===false) idie("�� ������� �������� `areas`!<br>".$file); }

	return "
delete are[$arean];
lastaren=0; aren0=[]; for(var i in aren) { if(lastaren<0) lastaren=i; if(aren[i]!=$arean) aren0.push(aren[i]); esle lastaren=-1;
} aren=aren0; setpolozarea(lastaren);
clean('a$arean');
salert('����������� <b>".strtoupper(h($area))."</b> ������� � ���� ���������<br>�� ������ ��� ���������� � �������!',10000);";
}


/*
ebasa[mya].i=0; - ������� ����� � ������� ������ ������
ebasa[mya].b=0; - ������� �������
ebasa[mya].len=0; - �������� �����
ebasa[mya].st=0; - ���� N, �� ������� ��� N ����� ����� �������, ���� �� ����������
ebasa[mya].en=0; - ���� 1, �� �� �������� ����� ������
*/

if($a=='charea') {
	$arean=RE0('arean'); if(!$arean) $arean=area2arean(RE('area'),1); if(!$arean) idie("arean='$arean' area=".h($area)."");
	$area=RE('area'); if(!$area) $area=arean2area($arean);
	$id=RE('id');

	if($fido_point && empty($id)) { // ��������� �������������
		$id=ms("SELECT * FROM $db_fidomy WHERE `point`='$fido_point' AND `arean`='$arean' AND `metka`='new' ORDER BY `id` DESC LIMIT 1","_1");
		$id=$id['id'];
	}

	return "
setmya($arean);
if(typeof ebasa[mya] == 'undefined') { ebasa[mya]=[]; ebasa[mya].i=0; ebasa[mya].b=0; ebasa[mya].len=0; ebasa[mya].st=0; ebasa[mya].en=0; }
".get_messages($area,$arean,$id)."
".($id?"ebasa[mya].st=".ms("SELECT COUNT(*) FROM $db_fido WHERE `AREAN`='$arean' AND `id`>'$id'","_l").";":'')."
echotype();
".(RE('nomsg')?'':"setHash('area:'+are[$arean]);")."
fidoselect('fidoarea');
";
}

if($a=='loadarea') {
	$arean=RE0('arean');
	$area=RE('area'); if(!$area) $area=arean2area($arean);
	$id=RE0('lastid');
	$pre=RE('pre')?1:0;
	$o=get_messages($area,$arean,$id,$pre);
	if($pre) $o.="echotype();";
	return $o;
}

} // ajax

//---------------------------------------------------------------

function msgn_all($arean,$all=0) { global $fido_point,$db_fido,$db_fidomy;
if(!$fido_point||$all) return ms("SELECT COUNT(*) FROM $db_fido WHERE `AREAN`='$arean'","_l",0);
return ms("SELECT COUNT(*) FROM $db_fidomy WHERE `point`='$fido_point' AND `arean`='$arean'","_l",0);
}

function msgn_new($arean,$all=0) { global $fido_point,$db_fido,$db_fidomy;
if(!$fido_point||$all) return 0;
return ms("SELECT COUNT(*) FROM $db_fidomy WHERE `point`='$fido_point' AND `metka`='new' AND `arean`='$arean'","_l",0);
}

function get_messages($area,$arean,$id,$pre=0) { global $fido_point,$fido_myaddr,$fido_point,$db_fidomy,$db_fido,$fido_point,$fido_nmes;

$msq0="SELECT `DATETIME`,`BODY`,`id`,`SUBJ`,`FROMNAME`,`TONAME`,`FROMADDR`,`TOADDR` FROM $db_fido WHERE `AREAN`='$arean' ";
$msqid=($id?" AND `id`".($pre?'>':'<=')."'$id'":'')." ";
$msqord=($pre?"":" DESC")." LIMIT $fido_nmes";

	if($area=='netmail') {
		if(!$fido_point) return "salert('Netmail - ������ ��� ������� �������',5000)";
		// �������� �����, �� ������ ���� ��� ����
		$pp=ms($msq0."AND (`FROMADDR`='$fido_myaddr' OR `TOADDR`='$fido_myaddr')".$msqid."ORDER BY `id`".$msqord);
	} else {

	if($fido_point && RE('all')!=1) $pp=ms("SELECT i.`metka`,p.`DATETIME`,p.`BODY`,p.`id`,p.`SUBJ`,p.`FROMNAME`,p.`TONAME`,p.`FROMADDR`,p.`TOADDR`
 FROM $db_fidomy as i INNER JOIN $db_fido AS p ON i.`id`=p.`id`
 WHERE i.`point`='$fido_point' AND i.`arean`='$arean'
 ".($id?" AND i.`id`".($pre?'>':'<=')."'$id'":'')." ORDER BY i.`id`".$msqord);
	else $pp=ms($msq0.$msqid."ORDER BY `id`".$msqord); // �������� �� ����� ����

// dier($_REQUEST);

	}

	$o=loadmsg($pp,$pre);
	if(!$pre && sizeof($pp)<$fido_nmes) $o.="ebasa[mya].en=1;"; // �� �������� �����, ������ �� �����������

//	$o.="salert('load: ".sizeof($pp)." all: '+ebasa[mya].len,2000);";

	return $o;
}
//----------------------------------------------------------------

function loadmsg($pp,$pre=0) { $eba='';
	include_once($GLOBALS['include_sys']."_obracom.php");
	foreach($pp as $p) {

	$toname=h(uw($p['TONAME'])); if($toname==$GLOBALS['fido_myname']) $toname="<span style='background-color:#fcc;'>".h($toname)."</span>"; else $toname=h($toname);
	$fromname=h(uw($p['FROMNAME']));
	$fromaddr=h(uw($p['FROMADDR']));
	$toaddr=($p['TOADDR']=='0'?'&nbsp;':h(uw($p['TOADDR'])));
	$subj=h(uw($p['SUBJ']));
	$body=obrabody1($p['BODY']);

	$body=str_replace('&gt;','|gt;|',$body); $body=AddBB($body); $body=str_replace('|gt;|','&gt;',$body);
	$body="\n$body\n"; $body=hyperlink($body);
	$body=trim($body,"\n\r\t ");

	$metka=($p['metka']=='new'?1:0);

	if(RE('search')) { $_GET['search']=RE('search'); // ��������� ���������� ����
		$body=search_podsveti_body($body);
		$fromname=search_podsveti_body($fromname);
		$fromaddr=search_podsveti_body($fromaddr);
		$toname=search_podsveti_body($toname);
		$toaddr=search_podsveti_body($toaddr);
		$subj=search_podsveti_body($subj);
	}
	$eba.=($pre?'unshift':'push').'id({id:'.$p['id'].',d:"'.$p['DATETIME'].'",f:"'.njsn($fromname).'",fa:"'.njsn($fromaddr)
		.'",t:"'.njsn($toname).'",ta:"'.njsn($toaddr)
		.'",s:"'.njsn($subj).'",b:"'.njsn($body).'",m:'.$metka.'});';
	}

return $eba;
}

// =====================================================================================
// =====================================================================================
// =====================================================================================
// =====================================================================================
// =====================================================================================
// =====================================================================================
// =====================================================================================
// =====================================================================================
function fido($e) {

STYLES("���� �����","
	.fidoa { font-size: 11px; cursor: pointer; color: blue;font-family: monospace;}
	.fidoa:hover { color: violet; font-family: monospace;}

	.fidm { border: 1px solid #���; font-family: monospace; font-size: 13px; cursor: pointer; color: blue;}
	.fidm:hover { border: 1px solid #cfcfcf; color: violet; font-family: monospace;}

	.fidome { border: 1px solid #333333; background-color: blue; }

.fidobody {
 white-space: pre-wrap; /* css-3 */
 white-space: -moz-pre-wrap; /* Mozilla */ 
 white-space: -pre-wrap; /* Opera 4-6 */ 
 white-space: -o-pre-wrap; /* Opera 7 */ 
 word-wrap: break-word; /* Internet Explorer 5.5+ */ 
font-family: monospace; font-size: 16px; font-color: #4F4F4F;
}
");

SCRIPTS("
var fido_point=".$GLOBALS['fido_point'].";
var omsg_new='".$GLOBALS['omsg_new']."';
var omsg_read='".$GLOBALS['omsg_read']."';
var fido_nmes=".$GLOBALS['fido_nmes'].";
var getsearch='".($_GET['search']?strtr($_GET['search'],"\"'","--"):'')."';
");
SCRIPT_ADD($GLOBALS['www_js']."fido.js");

$pan="<table width=100% border=0>";
	for($i=0;$i<$GLOBALS['fido_nmes'];$i++) $pan.="<tr id='pan".$i."'class='fidm' style='border: 1px solid transparent;'><td>".(!i?$panel:"&nbsp;")."</td></tr>";
$pan.="</table>";

$pixtolb=200;

if($GLOBALS['admin']) { $dupes=glob($GLOBALS['filehost']."fido/dupes/*"); $panel='- Admin
-- Erase Tables ('.sizeof($area).') | javascript:if(confirm("�����?")) majax("module.php",{mod:"fido",a:"admin_del_tables"})
-- Clean Dupes ('.sizeof($dupes).') | javascript:if(confirm("�����?")) majax("module.php",{mod:"fido",a:"admin_del_dupes"})
-- Copy Bundle | javascript:if(confirm("�����?")) majax("module.php",{mod:"fido",a:"admin_copy_file"})
-- Copy All | javascript:if(confirm("�����?")) majax("module.php",{mod:"fido",a:"admin_copy_allfiles"})
-- Delete Echo | javascript:if(confirm("������� ��� "+are[mya]+"?")) majax("module.php",{mod:"fido",a:"delarea",arean:mya});
-- Uplink Echo | javascript:majax("module.php",{mod:"fido",a:"uplinkarea",arean:mya});
'.(is_file($GLOBALS['filehost']."fido/phfito.lock")?'DEL LOC FILE | javascript:if(confirm("�����?")) majax("module.php",{mod:"fido",a:"admin_clean_lock"})':'')
.'- Test | javascript:majax("module.php",{mod:"fido",a:"admin_test"})
'; } else $panel='';

$menu="
<table height='24' width='100%' background='".$GLOBALS['www_design']."silkway/silkway_menu.gif' border='0' cellpadding='0' cellspacing='0'><tr><td align='center'>{_MENU:

FIDO | http://lleo.aha.ru/fido
".($GLOBALS['fido_point']?"- ��� ��� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"loadareas\",all:1})
- ������ ��� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"loadareas\"})
- ��������� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"pointlist\"})
":"")."
$panel

".($GLOBALS['fido_myaddr']?
"���
- �������� ��� ����������� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"allmsg_read\",arean:mya})
- �������� ��� ����� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"allmsg_new\",arean:mya})
- ������� ��� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"allmsg_del\",arean:mya})
- ������������ �� ���� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"allmsg_restore\",arean:mya})

���������<br>".$GLOBALS['fido_myaddr']." | javascript:majax(\"module.php\",{mod:\"fido\",a:\"settings_echo\"})
- ����������� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"settings_echo\"})
- ���������� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"settings_podp\"})
"
:"���� ������! | javascript:majax(\"module.php\",{mod:\"fido\",a:\"joinme\"})")."

Toss | javascript:majax(\"module.php\",{mod:\"fido\",a:\"toss\"})

� ������� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"about_help\"})
- ������ ���� | javascript:majax(\"module.php\",{mod:\"fido\",a:\"about_help\"})
- ������ ����� | http://lleo.aha.ru/blog/lleoblog

<input style='color: rgb(119, 119, 119);' size='7' value='�����' onclick=\"majax('okno.php',{a:'fidosearch'})\" type='text'> | javascript:majax(\"okno.php\",{a:\"fidosearch\"})

_}</td></tr></table>";

// <div id='buka' style='border: 1px dotted red; color: green;'>test</div>
return $menu."<table width=100% border=0><td><div style=\"width: 100%; margin: 0 auto; text-align: left; padding: 0; position:relative;\">"

."<div style=\"float: left; width: ".$pixtolb."px; padding: 0; overflow-y: hidden; overflow: auto;\">
<div id='fidoarea' onclick=\"fidoselect(this.id)\" style='padding: 5px;'></div>
</div>"

."<div style=\"margin: 0 0 0 ".$pixtolb."px; padding: 0; border-left: #cfcfcf 2px solid; border-collapse:collapse;\">"

."<div id='echotags' onclick=\"fidoselect(this.id)\" style=\"padding-left: 10px; border: 3px dotted red;\">$pan</div>"

//."<div id='fidolist' onclick=\"fidoselect(this.id)\" style=\"padding-left: 10px; border: 3px dotted transparent;\">$panel</div>"

."<div style=\"height: 2px; width: 100%; border-top: #cfcfcf 2px solid;\"></div>"

."<div onclick=\"fidoselect('echotags')\" style=\"padding-left: 10px; border: 3px dotted transparent;\" id='fidomsg'></div>"

."</div><br class=q /></div></td></table>
";
}

function area2arean($area,$new=0) { global $areans,$db_fido_num; if(!isset($areans)) $areans=array();
// idie($area);
        if(isset($areans[$area])) return $areans[$area];
        $arean=ms("SELECT `echonum` FROM $db_fido_num WHERE `echo`='".e($area)."'","_l");
        if(!$arean && !$new) { msq_add($db_fido_num,array('echo'=>e($area))); $arean=mysql_insert_id(); }
        $areans[$area]=$arean; return $arean;
}

function arean2area($arean) { global $areans,$db_fido_num; if(!isset($areans)) $areans=array();
	$area=array_search($arean,$areans); if($area!==false) return $area;
	$area=ms("SELECT `echo` FROM $db_fido_num WHERE `echonum`='".intval($arean)."'","_l");
        if($area!==false) $areans[$area]=$arean; return $area;
}

function mojno_write($area,$toa) {
	if($GLOBALS['fido_point']) { // ������
		if($area=='netmail') return; // ������� ����� �����
		$dostup=ms("SELECT `dostup` FROM ".$GLOBALS['db_fidopoints']." WHERE `point`='".$GLOBALS['fido_point']."'","_l");
		if($dostup=='writeall') return; // ��������� ������ �� ��� ���?
		if($dostup=='read') idie("��������� ������ ������ �����������<br>� ������ �������");
		$f=$GLOBALS['fidodir']."conf/disabled_write_area.txt"; if(!is_file($f)) return;
		$area=strtoupper($area);
		foreach(file($f) as $l) { if($area==trim(strtoupper($l))) idie("� ����������� <b>".h(strtoupper($area))."</b><br>������ ��������� ���������."); }
		return;
	} // ������� ����������: ������ ������� �����
	if($area=='netmail' and $toa==$GLOBALS['fido_node']) return;
	idie("����� �������� ������ Netmail<br>� ������ �� ����� ������: ".$GLOBALS['fido_node']);
}

function write_message_okno($zag,$area,$from,$froma,$to,$toa,
$subj,$text,$replyid,$dei=1) { mojno_write($area,$toa); return "
fido_reply_send=function(){ 

majax('module.php',{mod:'fido',a:'replytext',dei:$dei,
	area:vzyal('m_select_area'),from:'$from',
	froma:(idd('m_fido_froma')?vzyal('m_fido_froma'):'$froma'),
	to:(idd('m_fido_to')?idd('m_fido_to').value:\"$to\"),
	toa:(idd('m_fido_toa')?idd('m_fido_toa').value:\"$toa\"),
	subj:idd('m_fido_subj').value,
	text:idd('m_fido_text').value,
	replyid:'$replyid'});
helpc('fido_message_send',\"<fieldset><legend>FIDO: sending...</legend>���� ��������...<img src='\"+www_design+\"img/ajax.gif'></fieldset>\");
};
	helpc('fido_message',\"<fieldset><legend>FIDO: $zag</legend>".njsn(
"<div><b><tt>AREA: <span alt='������� ������ ���' id='m_select_area' class=ll0 onclick=\"majax('module.php',{mod:'fido',a:'select_area',area:'".h($area)."'})\">".h(strtoupper($area))."</span></b></tt></div>
<div><b><tt>FROM: </b> $from ".($GLOBALS['admin']&&$GLOBALS['fido_point']!=0?
"<span alt='������ �����' id='m_fido_froma' class=ll0 onclick=\"majax('module.php',{mod:'fido',a:'select_froma',v:this.innerHTML})\">$froma</span>"
:$froma)."</tt></div>
<div><b><tt>TO:&nbsp;&nbsp; </b> $to $toa</tt></div>
<div><b><tt>SUBJ: </b><input id='m_fido_subj' type='text' size=80 value=\"".h($subj)."\"></div>
<div><textarea id='m_fido_text' cols='".$GLOBALS['fido_msg_cols']."' rows='".$GLOBALS['fido_msg_rows']."'>".$text."</textarea></div>
<div><input type='button' value='SEND' onclick='fido_reply_send()'></div>"
)."</fieldset>\");
	idd('m_fido_text').focus();
setkey('enter','ctrl',fido_reply_send,false);
";
}

// ����������� �� ���������: http://lleo.aha.ru/fido#area:n5020.bone|id:1208

function netmail_mojno(){ global $admin,$fido_myaddr,$fido_node;
	return "(`FROMADDR`='$fido_myaddr' OR `TOADDR`='$fido_myaddr'"
	// � �����, ����� �� ������� �� �� ���, ������ ����� ������ ������� ����
	.($admin?" OR `FROMADDR`='$fido_node' OR `TOADDR`='$fido_node'":'')
	.")";
}

function rescan_n($arean) { return "
zabil('omsga".$arean."',".msgn_all($arean).");
zabil('omsgn".$arean."',".msgn_new($arean).");
"; }

function parse_config($name,$all=0) {
	if(!isset($GLOBALS['config_phfito'])) {
		$configname="fido/conf/config"; $file=$GLOBALS['filehost'].$configname;
		if(($s=file_get_contents($file))===false) idie("�� ������ ���� ������ PhFito: ".$configname);
		$GLOBALS['config_phfito']=$s;
	}
	$name=str_replace(" ",'\s',c(rtrim($name,'=')));

	if($all && preg_match_all("/[\n\r]+\s*".$name."[\s\=]+([^\n\r\#]+)/si","\n".$GLOBALS['config_phfito']."\n",$m)
	) return $m[1];
	if(preg_match("/[\n\r]+\s*".$name."[\s\=]+([^\n\r\#]+)/si","\n".$GLOBALS['config_phfito']."\n",$m)
	) return c($m[1]);
	idie("� ������� PhFito ��� ���������� `$name`");
}

?>