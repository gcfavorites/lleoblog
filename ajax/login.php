<?php // ����������� �������������

include "../config.php";

/*
if($msqe) { logi('login_msqe.txt',"\n\n".$msqe); die($msqe); }
$up=$unic_tot.'-'.md5($unic_tot.$newhash_user);
unset($jaajax); setcoo($uc,$up);

llog("LOGIN.PHP: restore kuka:$kuka uc:$uc unic:$unic_tot unicpass: $unicpass returnpage:$returnpage ");

die("<html><head><script language='JavaScript'>

var jog=false; function setIsReady(){
jog=(navigator.appName.indexOf('Microsoft')!=-1?window:document)['kuki'];
if(jog.flashcookie_read) fc_save('up','$up');
window.location='$returnpage';
}
function fc_save(n,v){ return (jog&&v!==false&&v!==null)?jog.flashcookie_save(n,v):false; }
var f5s=('localStorage' in window) && window['localStorage']!==null ? window['localStorage'] : false;
function f5_save(n,v) { return f5s?(f5s[n]=v):false; }

f5_save('up','$up');

setTimeout(\"window.location='$returnpage';\", 5000);
</script></head><meta http-equiv=refresh content=\"6;url='$returnpage'\"><body>
<img src='".$www_design."img/ajax.gif'> $returnpage wait...
".$jog_kuki."</body></html>");
	}
*/

//================================= OPENID ��������  =========================================================================
// ������������� email
if(isset($_GET['action'])&&$_GET['action']=='mailconfirm') { include_once $include_sys."_autorize.php";
$s="<p>���������� ����� ��� ����� ���������, ������� ����������������.";
if(!$unic) idie("unic error!".$s);
if($IS['mail']!=$_GET['mail']) idie("������ email.".$s);
if($_GET['pass']!=md5($_GET['mail'].$unic.$hashlogin.$newhash_user)
&&$_GET['pass']!=md5($_GET['mail'].$hashlogin.$newhash_user)
) idie("Email confirm error!".$s);
if(msq_update($GLOBALS['db_unic'],array('mail_checked'=>'1'),"WHERE `id`='$unic'")===false) idie("MySQL error! �� ��� �� � ������ �� ����...");
die("<html><head><script language='JavaScript'>setTimeout(\"window.location='$httphost';\", 10000)</script>
<meta http-equiv=refresh content=\"11;url='$httphost'\"></head><body>
<p><h1><font color=green>Your e-mail is successfully confirmed!</font></h1></body></html>");
}
// ������������� email

require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include_once $include_sys."_autorize.php"; // ������ JsHttpRequest, ����� autorize
$a=RE('action');

if($a=='getinfo') { // unic ������ �������� ������
	$un=intval($_REQUEST['unic']);
	if(!$un) idie('unic=0');
	$is=getis($un);

	$s="<small><div id=openidotvet></div>".$is['imgicourl']."<table>";

if($admin) {
	$s.= dva_pole("������:",selecto('admin',h($is['admin']),array('user'=>'user','podzamok'=>'podzamok','admin'=>'admin','mudak'=>'mudak'),"class='in' onchange='majax(\"login.php\",{action:\"dostup\",unic:\"".$is['id']."\",value:this.value})' name"));

$karmi=array('0'=>'���','1'=>'����','2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,'10'=>10,'11'=>11,'12'=>12,'13'=>13,'14'=>14,
'15'=>15,'16'=>16,'17'=>17,'18'=>18,'20'=>20,'25'=>25,'30'=>30,'40'=>40,'50'=>50,'60'=>60,'80'=>80,'100'=>100,'150'=>150,'255'=>255);

	$s.= dva_pole("�����:",selecto('capchakarma',h($is['capchakarma']),$karmi,
"class='in' onchange='majax(\"login.php\",{action:\"karma\",unic:\"".$is['id']."\",value:this.value})' name"));

//	if($is['capcha']=='no') $s.=dva_pole("�����:","����� �� �������");
	if($is['ipn']!=0) $s.=dva_pole("ip:","<a href='http://yandex.ru/yandsearch?text=%22".ipn2ip($is['ipn'])."%22'>".ipn2ip($is['ipn'])."</a>");
	if($is['obr']) $s.=dva_pole("�������:",h($is[$is['obr']]));

//$pa='01234567890abcdefghijklmnopqrstuvwxyz_01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ'; $lpass=''; for($i=rand(4,14);$i>0;$i--) $lpass.=$pa[rand(0,strlen($pa)-1)];
if($is['password']) $s.=dva_pole("������:",'*****'); //$lpass
}

if($is['img']) $s.="<img src='".$is['img']."' align='right'>";
if($is['lju']) $s.=dva_pole("livejournal:","<a href='http://".h($is['lju']).".livejournal.com'>".h($is['lju'])."</a>");
if($is['login']) $s.=dva_pole("login:",h($is['login']) );
if($is['openid']) $s.=dva_pole("openid:","<a href='".h($is['openid'])."'>".h($is['openid'])."</a>");
if($is['mail']) $s.=dva_pole("mail:",($admin?" <a href='mailto:".h($is['mail'])."'>".h($is['mail'])."</a>":"�������") );
if($is['site']) $s.=dva_pole("site:","<a href='".h($is['site'])."'>".h($is['site'])."</a>");
if($is['realname']) $s.=dva_pole("���:",h($is['realname']) );
if($is['birth']!='0000-00-00') $s.=dva_pole("���� ��������:",h($is['birth']) );
$s.=dva_pole("�����������:",date("Y-m-d H:i:s",$is['time_reg']) );
$nko=ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `unic`='".e($is['id'])."'","_l");
$s.=dva_pole("������������:",($nko?"<span class=l onclick=\"majax('okno.php',{a:'hiscomment',id:'".$is['id']."',n:'$nko'})\">$nko</span>":$nko));
// �������� ��� �����������

otprav("helps('userinfo',\"<fieldset><legend>������ �������� ".$is['id']."</legend>".njs($s)."</fieldset>\");
posdiv('userinfo',-1,-1);");
}

// ======= ������� �� ������������� email ============================================================================================
if($a=='mail_check'){
	include_once $include_sys."_sendmail.php"; send_mail_confirm($IS['mail'],$IS['realname']);
	idie("������ �� ".h($IS['mail'])." ���������.<br>��������� �����.");
}

// ======= �������� ����� openid ============================================================================================
if($a=='openid_form') { $s="<div id=openidotvet></div>";

$numlo='';

// idie('###'.$IS['id']."@@@".$unic);
//  $IS['user']

if(intval($unic)) {



$numlo=" ".$unic;

$s.="<form name='openiddelo' onsubmit='return polesend_all();'>
<div class=l0>";
$s.="�����: ".$imgicourl;
//$s.="<p>��� ��� ������� ��� openid? <span onclick=\\\"majax('login.php',{action:'oldlogin_form'})\\\" class=l>����� ��� ����!</span>";
$s.="<p>���������������: <b>".date("Y-m-d H:i:s",$IS['time_reg'])."</b><br>";
if($IS['lju']!='') $s.="<a href='http://".h($IS['lju']).".livejournal.com'>".h($IS['lju'])."</a> &nbsp; ";
if($IS['ipn']!=0) $s.=ipn2ip($IS['ipn']);
$s.="</div>";

if($IS['capcha']=='no') { include_once $include_sys."_antibot.php";
$s.="<div class=l0>
<div class=l1>� �� �����:</div>
<div class=l2 id='ozcapcha'><table><tr valign=center><td><input onkeyup='polese(this)' onchange='polesend(this)'
maxlength=".$GLOBALS['antibot_C']." class='capcha' type=text name='capcha-"
.antibot_make()."'></td><td>".antibot_img()."</td></tr></table></div>
<br class=q /></div>";
}



$s.="
<div class=l0>
<div class=l1>login:</div>
<div class=l2 id='ozlogin'>"
.($IS['login']!=''?"<b>".h($IS['login'])."</b>"
:"<input name='login' class=in type=text onkeyup='this.value=login_validate(this,0)' onchange='polesend(this)' value='".h($IS['login'])."'>"
)."</div>
<br class=q /></div>

<div class=l0>
<div class=l1>".($IS['login']==''?"������":"�������")." ������:</div>
<div class=l2 id='ozpassword'><input name='password' class=in onchange='polesend(this)' onkeyup='polese(this)' type=text value=''></div>
<br class=q /></div>

<div class=l0>
<div class=l1>openid:</div>
<div class=l2>".h($IS['openid'])."</div>
<br class=q /></div>

<div class=l0>
<div class=l1>".($IS['mail']==''?"������� ":
($IS['mail_checked']==1?"<font color=green>confirmed </font>":"<font color=red>wait </font>")
)."mail:</div>
<div class=l2><input name='mail' class=in type=text onchange='polesend(this)' onkeyup='this.value=mail_validate(this)' value='".mail_validate($IS['mail'])."'></div>
<br class=q /></div>

".($IS['mail_checked']==0?"
<div class=l0>
<div class=l1>����������� mail:</div>
<div class=l2><div class=ll onclick='majax(\\\"login.php\\\",{action:\\\"mail_check\\\"})'>��������� ������</div></div>
<br class=q /></div>":""
)."

<div class=l0>
<div class=l1>������ �� mail:</div>
<div class=l2><select class='in' onchange='polesend(this)' name='mail_comment'>
<option value='1'".($IS['mail_comment']=='1'?' selected=\"selected\"':'').">��������</option>
<option value='0'".($IS['mail_comment']=='0'?' selected=\"selected\"':'').">�� ��������</option>
</select></div>
<br class=q /></div>

<div class=l0>
<div class=l1>��� ��� ���:</div>
<div class=l2><input name='realname' class=in type=text onkeyup='this.value=realname_validate(this)' onchange='polesend(this)' value='".h($IS['realname'])."'></div>
<br class=q /></div>
";

if($admin) $s.= "<div class=l0>
<div class=l1>������:</div>
<div class=l2>".selecto('admin',h($IS['admin']),array('user'=>'user','podzamok'=>'podzamok','admin'=>'admin'),"class='in'
onchange='polesend(this)' name")."</div>
<br class=q /></div>";
elseif($IS['admin']!='user') $s.="<div class=l0>
<div class=l1>������:</div>
<div class=l2>".$IS['admin']."</div>
<br class=q /></div>";

$s.="<div class=l0>
<div class=l1>���� ��������:</div><div class=l2 id='ozbirth'>";

if($IS['birth']=='0000-00-00') { list($Y,$M,$D)=explode('-',h($IS['birth']));
   $s.="<input type=hidden id='birth' name='birth'>";
	$u="style='font-size: 9px; border: 1px solid #ccc;' onchange='setbirth(this.form.y,this.form.m,this.form.d)' name";
	$a=array(''=>'---'); for($i=1;$i<=31;$i++) $a[$i]=sprintf("%02d",$i); $s.=selecto('d',$D,$a,$u);
	$a=array(''=>'---'); for($i=1;$i<=12;$i++) $a[$i]=$GLOBALS['months_rod'][$i]; $s.=selecto('m',$M,$a,$u);
	$a=array(''=>'---'); for($i=(date('Y')-5);$i>1900;$i--) { $l=sprintf("%04d",$i); $a[$l]=$l; } $s.=selecto('y',$Y,$a,$u);
} else $s.="<b>".h($IS['birth'])."</b>";
$s.="</div><br class=q /></div>";

$s.="<input type=submit value='go'></form>";

} else $s.="unic: ".$unic." r: <pre>".h(print_r($IS,1))."</pre>";

$s="helpc('loginopenid',\"<fieldset id='openid'><legend>�����".h($numlo)."</legend>".njsn($s)."</fieldset>\");";

// otprav($s);

// idie('#1##'.$IS['id']."@@@".$unic);

otprav_sb('openid_editform.js',$s);
}


// ======= �������� ����� openid ============================================================================================
if($a=='oldlogin_form') {

$login=preg_replace("/[^0-9a-z\.\_\-\@]/si",'',$_REQUEST["login"]);

$s="<div id=openidotvet></div>
<form name='openidnew' onsubmit='return login_go(this.mylog.value,this.mypas.value);'>
<div class=l0>
<div class=l1>login ��� openid:</div>
<div class=l2 id='d1'><input name='mylog' class=in type=text onkeyup='this.value=login_validate(this,1)' value='".$login."'></div>
<br class=q /></div>

<div class=l0 id=openidpass>
<div class=l1>password:</div>
<div class=l2 id='d1'><input name='mypas' class=in type=password></div>
<br class=q /></div>

<input type=submit value='go'>
</form>
";

$s="helps('loginopenid',\"<fieldset id='openid'><legend>������� ���� ������� �����</legend>".$s."</fieldset>\");";
if(strstr($login,'.')) $s.="zakryl('openidpass');";

otprav_sb('openid_editform.js',$s);
}


if(isset($_REQUEST['onload'])) otprav(''); // ��� ���������� ����� ����� ��������� ��� GET-�������


// ======= ��������� ������ ====================================================

function setpole($s,$p='') { global $name,$value,$unic;
	if($name=='undefined'||$value=='undefined') otprav("zabil('openidotvet','')");
	if(msq_update($GLOBALS['db_unic'],array(e($name)=>e($value)),"WHERE `id`='$unic'")===false)
		otprav("zabil('openidotvet','<div class=e>������: ".$GLOBALS['msqe']."</div>');");

	if($name=='mail') { include_once $include_sys."_sendmail.php"; send_mail_confirm($IS['mail'],$IS['realname']); }

	otprav("zabil('openidotvet','<div class=o>$s".($name=='mail'?"<br>������ ������":"")."</div>'); ".$p);
}

function errpole($s,$p='') { global $name,$value; otprav("zabil('openidotvet','<div class=o>$s</div>'); ".$p); }

if($a=='polesend') {
	$name=strtr($_REQUEST["name"],"\r\n\t ",'');
	$value=trim(strtr($_REQUEST["value"],"\r",''),"\n\t ");

	if($name=='mail') {
		if(mail_validate($value)) setpole("mail �������");
		if($value=='') setpole("mail �����");
		errpole("�������� ������ email.");
	}

	if($name=='site') {
        	if(site_validate($value)) setpole("site �������");
	        elseif(site_validate('http://'.$value)) { $value='http://'.$value; setpole("site �������"); }
	        else errpole("��� �������������� �����. ���� ��� ����� - ������ ������. �� ������ �������� �������.");
	}

	if($name=='birth') {
        	list($y,$m,$d)=explode('-',$value); $value=sprintf("%04d-%02d-%02d",$y,$m,$d);
	        if(intval($y)*intval($m)*intval($d)) setpole("���� �������� �������: ".h($value)); else errpole();
	}

	if(substr($name,0,7)=='capcha-') { include_once $GLOBALS['include_sys']."_antibot.php";
		list($name,$val)=explode('-',$name);
	        if(!antibot_check($value,$val)) {
			errpole("����� ������� �������!","zabil('ozcapcha',\"<table><tr valign=center><td><input onkeyup='polese(this)' onchange='polesend(this)' class='capcha' maxlength=".$GLOBALS['antibot_C']." type=text name='capcha-".antibot_make()."'></td><td>".antibot_img()."</td></tr></table>\");");
	        } else {
			$value='yes';
			setpole("�� �����"," zabil('ozcapcha','����: �� �����');");
		}
	}

	if($name=='login') {
	        if(preg_match("/[^0-9a-z\-\_]/s",$value)) errpole("� ������ ��������� ������ �������� ��������� �����, �����, ������������� ��� �����.");
	        if(strlen($value)>32) { $value=substr($value,0,32); errpole("����� ������ - �� ����� 32 ��������."); }
	        $id=ms("SELECT `id` FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($value)."'","_l",0);
	        if($id===false) setpole("������ ���� ����� - ".h($value));
	        if($id==$unic) setpole("��, ���� ����� ".h($value).", � �� ���� ��������������.");
	        errpole("���� ����� �����!");
	}

	if($name=='password') { $value=md5($value.$GLOBALS['hashlogin']);
// �����, ��� ������� ���� ������� �������� "���� ������ ��� ������������"... �� �������, ������� :)
// ��� �������? �� ����� �����, ������! ����:
//		if(intval(ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic']." WHERE `password`='".e($value)."'","_l")!=0)) {
//			errpole("���� ������ ��� ���-�� �����. �������� ������."); }
		setpole("������� ��� ������:<br>&nbsp;&nbsp;`".h($value)."`","zabil('ozpassword','������ ����������');");
	}

setpole(h($name)." ��������: ".h($value));

}

function dva_pole($a,$b) { return "<tr><td><small>$a</small></td><td><small>$b</small></td></tr>"; }

//========================================================================================================================
if($a=='dostup') { // ����� �������
	if(!$admin) idie("�� �� �����.");
	ms("UPDATE ".$GLOBALS['db_unic']." SET `admin`='".e($_REQUEST['value'])."' WHERE `id`='".intval($_REQUEST['unic'])."'","_l",0);
	otprav("zabil('openidotvet','<font size=1 color=green>������� ������: ".h($_REQUEST['value'])."</font>')");
}

if($a=='karma_delmud') { AD(); // ������� �������� ������
	$u=RE0('unic');
	$a=ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `unic`='$u'","_l",0);
	ms("DELETE FROM `dnevnik_comm` WHERE `unic`='$u'","_l",0);
	otprav("salert('$a comments deleted',500)");
}

if($a=='karma') { // ����� �����
	if(!$admin) idie("�� �� �����.");
	$karma=RE0('value'); $u=RE0('unic');
	ms("UPDATE ".$GLOBALS['db_unic']." SET `capchakarma`='$karma' WHERE `id`='$u'","_l",0);

	$s="zabil('openidotvet','<font size=1 color=green>�������� �����: $karma</font>');";
	if($karma>=50) {
		$nkom=ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `unic`='$u'","_l",0);
		$s.="if(confirm('".LL('login:delmud',$nkom)."')) majax('login.php',{action:'karma_delmud',unic:$u});";
	}
	otprav($s);
}

// ======== ������ ������ ������ ===================

if($a=='openid_logpas') {
	$mylog=RE('mylog'); $mypas=RE('mypas');
	if(preg_match("/[^0-9a-z\-\_\.\/\~\=\@]/si",$mylog)) otprav("zabil('openidotvet','<div class=e>�������� ������� � ������</div>');");

	if(($p=ms("SELECT `id`,`password` FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($mylog)."'","_1",0))===false
	) idie('User `'.h($mylog).'` not found!');

	if(md5($mypas.$GLOBALS['hashlogin']) != $p['password']) {
		llog("error_LOGIN.PHP: log: `".h($mylog)."`, pas: `".h($mypas)."` (`".substr($p['password'],0,5)."[...]`) ");
		sleep(5); otprav("zabil('openidotvet','<div class=e>������ ��������</div>');");
	}

	if(getis_global($p['id'])===false) idie('Error #118');
	$up=upset($p['id']);

	llog("LOGIN.PHP: LOGIN unic:".h($unic)." loginobr:".h($loginobr)."");

        otprav("
		up='$up'; c_save(uc,up,1); fc_save('up',up); f5_save('up',up);
		realname=\"".$imgicourl."\";
		helps('work',\"<fieldset>������������ ".$imgicourl."</fieldset>\"); posdiv('work',-1,-1);
		zabil('openidotvet',\"<div class=o>".$imgicourl."!</div>\");
		zabilc('myunic',realname);
		setTimeout(\"clean('work'); clean('loginopenid'); clean('loginobr_unic11');\",1000);
	");
}

?>