<?php // ����������� �������������

include "../config.php";
include $include_sys."_autorize.php";

//$IS=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `id`='$unic'","_1",0); if($IS===false) idie('error');
//$IS=array_merge($IS,get_ISi($IS));

//================================= OPENID ��������  =========================================================================
if(isset($_GET['openid_mode'])) {
	require($GLOBALS['include_sys'].'class.openid.v3.php'); // ����������� openid

	if($_GET['openid_mode'] == 'id_res'){ // Perform HTTP Request to OpenID server to validate key
        $openid = new SimpleOpenID;
        $openid->SetIdentity($_GET['openid_identity']);
        $openid_validation_result = $openid->ValidateWithServer();
        if ($openid_validation_result == true){ // OK HERE KEY IS VALID

	// ========= �� ������������, � �� ������ ������? ==========

	// ������� ���������� � �������� �������� � ����������� openid
	$dat=ms("SELECT `text` FROM `unictemp` WHERE `unic`='$unic'","_l",0); if($dat===false) die('error');
	msq("DELETE FROM `unictemp` WHERE `unic`='$unic'");
	$dat=unserialize($dat);

	$openid=h($dat['openid']); $openid=preg_replace("/^www./i","",$openid); // ����� ��� ��� ��������

	// ����������� ����� ����� ( $IS - � ��� ���� )
	$f=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `openid`='".e($openid)."'","_1",0);
	if($f===false) { // ��� - ������ � ���� ���: �������� unic ������� � �������/�������� ������

		$ara=array('openid'=>e($openid)); // ������ ����� ������ ������ � ������� �����������
		if($IS['mail']='' and $_GET['openid_sreg_email']!='') $ara['mail']=e($_GET['openid_sreg_email']);
		if($IS['realname']='' and $_GET['openid_sreg_fullname']!='') $ara['realname']=e($_GET['openid_sreg_fullname']);
		if($IS['birth']='' and $_GET['openid_sreg_dob']!='') $ara['birth']=e($_GET['openid_sreg_dob']);
		msq_update($GLOBALS['db_unic'],$ara,"WHERE `id`='$unic'");

		print "ololo";
		setcookie('obr',base64_encode(h($openid)), time()+86400*365, "/", "", 0);
		redirect($dat['rpage']);

	} else { // �� - ����� ���� � ����

		if($IS['password']=='' and $IS['openid']=='') { // ������ ����� � ����� � �������

		// ���� �� �����
		// ���� �� �����
		// ���� �� �����
		// ���� �� �����
		// ���� �� �����

		ms("DELETE FROM ".$GLOBALS['db_unic']." WHERE `id`='$unic'","_1",0); // � �������

		} // ����� �� �������, ������ ����������

		// ��-������ ����������� UNIC �� �����
		$ara=array();
		if($f['mail']='' and $_GET['openid_sreg_email']!='') $ara['mail']=e($_GET['openid_sreg_email']);
		if($f['realname']='' and $_GET['openid_sreg_fullname']!='') $ara['realname']=e($_GET['openid_sreg_fullname']);
		if($f['birth']='' and $_GET['openid_sreg_dob']!='') $ara['birth']=e($_GET['openid_sreg_dob']);
		if(sizeof($ara)) msq_update($GLOBALS['db_unic'],$ara,"WHERE `openid`='".e($openid)."'"); // ��������� �������, ���� �����

$kuka=$f['id'].'-'.md5($f['id'].$hashlogin);

//	location.replace('".h($dat['rpage'])."');

die("<html><head>
<script language='JavaScript'>
".$jog_scripts."
function setIsReady() {
	fc_save('".$uc."','".$kuka."');
	c_save('".$uc."','".$kuka."');
	c_save('obr','".base64_encode(h($openid))."');
	window.location='".h($dat['rpage'])."';
}
setTimeout(\"window.location='".h($dat['rpage'])."';\", 5000);
</script>
</head>
<meta http-equiv=refresh content=\"6;url='".h($dat['rpage'])."'\">
<body>
<img src='".$www_design."img/ajax.gif'> ".h($dat['rpage'])." wait...
".str_replace('{www_design}',$www_design,$jog_kuki)."
</body></html>");

	}



        }else if($openid->IsError() == true) { // ON THE WAY, WE GOT SOME ERROR
                $error = $openid->GetError();
                $otvet="<font color=red>ERROR CODE: ".$error['code']."<br>ERROR DESCRIPTION: ".$error['description']."</font>";
        }else{ // Signature Verification Failed
                $otvet="<font color=red>INVALID AUTHORIZATION</font>";
        }
} elseif($_GET['openid_mode'] == 'cancel'){ $otvet="<h1><font color=red>USER CANCELED REQUEST</font></h1>"; } // User Canceled your Request

die($otvet);
}
//================================= OPENID ��������  =========================================================================

require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");
$a=$_REQUEST["action"];

// ======= ��������� ������ ====================================================

function setpole($s,$p='') { global $name,$value,$unic;
	if(msq_update($GLOBALS['db_unic'],array(e($name)=>e($value)),"WHERE `id`='$unic'")===false)
		otprav("zabil('openidotvet','<div class=e>������: ".$GLOBALS['msqe']."</div>');");
	otprav("zabil('openidotvet','<div class=o>$s</div>'); ".$p);
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
	if(intval(ms("SELECT COUNT(*) FROM ".$GLOBALS['db_unic']." WHERE `password`='".e($value)."'","_l")!=0)) {
		errpole("���� ������ ��� ���-�� �����. �������� ������."); }
	setpole("������� ��� ������:<br>&nbsp;&nbsp;`".h($value)."`","zabil('ozpassword','������ ����������');");
}

setpole(h($name)." ��������: ".h($value));

}




//========================================================================================================================
if($a=='getinfo') { // unic ������ �������� ������

	$is=getis(intval($_REQUEST['unic']));

//if($admin) {
//	otprav("alert(\"".njs(intval($_REQUEST['unic']))."\")");
//	otprav("alert(\"".njs(h(print_r($is,1)))."\")");
//}

	$s="<div id=openidotvet></div>".$is['imgicourl'];

if($admin) {
	$s.="<form name='openiddelo'>";
	$s.= "<div class=l0><div class=l1>������:</div><div class=l2>".$is['admin']."</div><br class=q /></div>";
	$s.= "<div class=l0><div class=l1>������:</div><div class=l2>".selecto('admin',h($is['admin']),array('user'=>'user','podzamok'=>'podzamok','admin'=>'admin'),"class='in' onchange='polesend(this)' name")."</div><br class=q /></div>";
	$s.="<input style='display:none' type=submit value='go'></form>";

	if($is['capcha']=='no') $s.="<div class=l0><div class=l1>�����:</div><div class=l2>����� �� �������</div><br class=q /></div>";
	if($is['ipn']!=0) $s.="<div class=l0><div class=l1>ip:</div><div class=l2><a href='http://yandex.ru/yandsearch?text=%22".ipn2ip($is['ipn'])."%22'>".ipn2ip($is['ipn'])."</a></div><br class=q /></div>";
	if($is['obr']) $s.="<div class=l0><div class=l1>�������:</div><div class=l2>".h($is[$is['obr']])."</div><br class=q /></div>";
}

if($is['lju']) $s.="<div class=l0><div class=l1>livejournal:</div><div class=l2><a href='http://".h($is['lju']).".livejournal.com'>".h($is['lju'])."</a></div><br class=q /></div>";
if($is['login']) $s.="<div class=l0><div class=l1>login:</div><div class=l2>".h($is['login'])."</div><br class=q /></div>";

// $m=explode('.',$IP); $lpass=''; foreach($m as $l) $lpass.=chr(32+$l);
$pa='01234567890abcdefghijklmnopqrstuvwxyz_01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ'; $lpass=''; for($i=rand(4,14);$i>0;$i--) $lpass.=$pa[rand(0,strlen($pa)-1)];
if($is['password']) $s.="<div class=l0><div class=l1>������:</div><div class=l2>".$lpass."</div><br class=q /></div>";
if($is['openid']) $s.="<div class=l0><div class=l1>openid:</div><div class=l2><a href='http://".h($is['openid'])."'>".h($is['openid'])."</a></div><br class=q /></div>";
if($is['mail']) $s.="<div class=l0><div class=l1>mail:</div><div class=l2>".($admin?" <a href='mailto:".h($is['mail'])."'>".h($is['mail'])."</a>":"�������")."</div><br class=q /></div>";
if($is['site']) $s.="<div class=l0><div class=l1>site:</div><div class=l2><a href='".h($is['site'])."'>".h($is['site'])."</a></div><br class=q /></div>";
if($is['realname']) $s.="<div class=l0><div class=l1>���:</div><div class=l2>".h($is['realname'])."</div><br class=q /></div>";
if($is['birth']!='0000-00-00') $s.="<div class=l0><div class=l1>���� ��������:</div><div class=l2>".h($is['birth'])."</div><br class=q /></div>";
$s.="<div class=l0><div class=l1>�����������:</div><div class=l2>".date("Y-m-d H:i:s",$is['time_reg'])."</div><br class=q /></div>";

// �������� ��� �����������

$s="helps('loginopenid',\"<fieldset id='openid'><legend>������ �������� ".$is['id']."</legend>".$s."</fieldset>\");";

otprav_sb('openid_editform.js',$s);
}

// ======= �������� ����� openid ============================================================================================
if($a=='openid_form') { $s="<div id=openidotvet></div>";

$numlo='';

if($IS['user']) {

$numlo=" ".$unic;

$s.="<form name='openiddelo' onsubmit='return polesend_all();'>
<div class=l0>";
$s.="�����: ".$imgicourl;
$s.="<p>��� ��� ������� ��� openid? <span onclick=\\\"majax('login.php',{action:'oldlogin_form'})\\\" class=l>����� ��� ����!</span>";
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
<div class=l1>".($IS['openid']==''?"������":"�������")." openid:</div>
<div class=l2 id='d1'><input name='openid' class=in type=text onkeyup='this.value=login_validate(this,0)' onchange='openid_go(this.value)' value='".h($IS['openid'])."'></div>
<br class=q /></div>

<div class=l0>
<div class=l1>".($IS['mail']==''?"������� ":"")."mail:</div>
<div class=l2><input name='mail' class=in type=text onchange='polesend(this)' onkeyup='this.value=mail_validate(this)' value='".mail_validate($IS['mail'])."'></div>
<br class=q /></div>


<div class=l0>
<div class=l1>site:</div>
<div class=l2><input name='site' class=in type=text onkeyup='this.value=site_validate(this)' onchange='polesend(this)' value='".site_validate($IS['site'])."'></div>
<br class=q /></div>

<div class=l0>
<div class=l1>��� ��� ���:</div>
<div class=l2><input name='realname' class=in type=text onkeyup='this.value=realname_validate(this)' onchange='polesend(this)' value='".h($IS['realname'])."'></div>
<br class=q /></div>

<div class=l0>
<div class=l1>�������������</div>
<div class=l2>
<select class='in' onchange='polesend(this)' name='obr'>
".($IS['openid']!=''?"<option value='openid'".($IS['obr']=='openid'?' selected=\"selected\"':'').">openid</option>":'')."
".(($IS['password']!='' and $IS['login']!='')?"<option value='login'".($IS['obr']=='login'?' selected=\"selected\"':'').">login</option>)":'')."
".($IS['realname']!=''?"<option value='realname'".($IS['obr']=='realname'?' selected=\"selected\"':'').">realname</option>)":'')."
</select>"
// .selecto('obr',h($IS['obr']),array('openid'=>'openid','login'=>'login','realname'=>'realname'),"class='in' onchange='polesend(this)' name")
."</div>
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

}

$s="helps('loginopenid',\"<fieldset id='openid'><legend>�����".$numlo."</legend>".$s."</fieldset>\");";

otprav_sb('openid_editform.js',$s);
}




// ======= �������� ����� openid ============================================================================================
if($a=='oldlogin_form') {

$s="<div id=openidotvet></div>
<form name='openidnew' onsubmit='return login_go(this.log.value,this.pas.value);'>
<div class=l0>
<div class=l1>login ��� openid:</div>
<div class=l2 id='d1'><input name='log' class=in type=text onkeyup='this.value=login_validate(this,1)' value='\"+logintext+\"'></div>
<br class=q /></div>

<div class=l0 id=openidpass>
<div class=l1>password:</div>
<div class=l2 id='d1'><input name='pas' class=in type=password></div>
<br class=q /></div>

<input type=submit value='go'>
</form>
";

$s="helps('loginopenid',\"<fieldset id='openid'><legend>������� ���� ������� �����</legend>".$s."</fieldset>\");";

otprav_sb('openid_editform.js',$s);
}




// ======== ������ ������ ������ ===================
if($a=='openid_logpas') {

	$log=$_REQUEST["log"]; $pas=$_REQUEST["pas"];
	if(preg_match("/[^0-9a-z\-\_\.\/\~\=\@]/si",$log)) otprav("zabil('openidotvet','<div class=e>�������� ������� � ������</div>');");

//================================= OPENID ================================================================================
if(preg_match("/[A-Z\.\/\~\=\@]/s",$log)) {

	require($GLOBALS['include_sys'].'class.openid.v3.php'); // ����������� openid

	// ���������, ��� �� ��������� � ���� ��� ���������
	msq_add_update('unictemp',array('unic'=>$unic,'text'=>e(serialize(array(
'openid'=>e($log),
'rpage'=>e($_REQUEST['rpage']),
'text'=>e($_REQUEST['text'])
))) ),'unic');

        $openid = new SimpleOpenID;
        $openid->SetIdentity($log);
        $openid->SetTrustRoot('http://'.$_SERVER["HTTP_HOST"]);
        $openid->SetRequiredFields(array('email','fullname'));
	$openid->SetOptionalFields(array('dob')); // 'dob','gender','postcode','country','language','timezone'
        if ($openid->GetOpenIDServer()){
                $openid->SetApprovedURL($httpsite.$mypage); // Send Response from OpenID server to this script
                $redirect_to = $openid->GetRedirectURL();
		otprav("window.location='".trim($redirect_to," \t\n\r&")."';");
        }else{
                $error = $openid->GetError();

		if($error['code']=='OPENID_NOSERVERSFOUND') $otvet="�������� �����!";
		else $otvet="ERROR CODE: ".$error['code']; // "<br>ERROR DESCRIPTION: ".$error['description'];
		otprav("zabil('openidotvet','<div class=e>$otvet</div>');");
        }

}
//================================= ����� ================================================================================
// ���� ��� ��� ������� ����� �����

	$p=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($log)."'","_1");

	if(md5($pas.$GLOBALS['hashlogin']) != $p['password'])
		otprav("zabil('openidotvet','<div class=e>�������� ������, ������ ���</div>');");

	$obr=h($p['realname']?$p['realname']:$p['login']);
	$kuka=$p['id'].'-'.md5($p['id'].$hashlogin);

	$IS=array_merge($p,get_ISi($p));
	$loginobr="<img src='".h($IS['ico'])."'><a href='http://".h($IS['url'])."'>".h($IS['user'])."</a>";

	otprav("
zabil('openidotvet',\"<div class=o>".$loginobr."!</div>\");
zabil('loginobr',\"".$loginobr."\");
c_save(uc,'".$kuka."'); fc_save(uc,'".$kuka."'); c_save('obr','".base64_encode($obr)."');
setTimeout(\"clean('loginopenid')\", 700);
");
//=================== ��������������� ��������� ==========================================================================
}

function otprav_sb($scr,$s) { global $_RESULT,$msqe; $_RESULT["modo"] = ScriptBefore($scr,$msqe.$s); $_RESULT["status"] = true; exit; }
function ScriptBefore($script,$run) { return "loadScriptBefore('$script',\"".njs($run)."\");"; }
function prejs($s) { return str_replace(array("&","\\","'",'"',"\n","\r"),array("&amp;","\\\\","\\'",'\\"',"\\n",""),$s); }

//================================================

function loginslil($p,$unic) { $new=$p['id']; // ��� ��� ����� ������� ������ �������...

//������� ������! ������� ��������:
//1. ���������� �� ���� ����� � ������� � �� openid. � ���� ��� ����: LOGIN (+password) � OPENID
//2. ������������� ����� �� ����� 1) ��� openid, 2) ��� login, 3) ��� realname - ��� ���� OBR
//3. ���� ������� ��������� ������� ��� ��������, �� ��������� ������:
// �) ���� ���� ����� ��� ��� - ���� �� �������! ������ �����������!
// �) ���� ����� (�������) ��� �� ���� - �������� ��� ������ � ���� ������
//4. ���� ��������� ������, �� ��� ���� �� ������ �� ������ �� ���������:
// �) ���� ������ �� ������������ - ������� ��� ���
// �) ���� ������������ - ���������� �� ����, ��� ������ �������, ���� ��� ������


// ���� ���� ���� �����: 
//	if($p['mail']=='' and $po['mail']!='') $ara['mail']=$po['mail'];
//	if($p['site']=='' and $po['site']!='') $ara['site']=$po['site'];
//	if($p['realname']=='' and $po['realname']!='') $ara['realname']=$po['realname'];
//	if($p['birth']=='' and $po['birth']!='') $ara['birth']=$po['birth'];

	if($new==$unic) return $p; // ���� ���� � ��� ��

	$po=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `id`='".e($unic)."'","_1",0);

	$ara=array(); // �������� ��� ������ �����
	if($p['mail']=='' and $po['mail']!='') $ara['mail']=$po['mail'];
	if($p['site']=='' and $po['site']!='') $ara['site']=$po['site'];
	if($p['realname']=='' and $po['realname']!='') $ara['realname']=$po['realname'];
	if($p['birth']=='' and $po['birth']!='') $ara['birth']=$po['birth'];
	if($p['lju']=='' and $po['lju']!='') $ara['lju']=$po['lju'];
	// if(sizeof($ara)) msq_update($GLOBALS['db_unic'],$ara,"WHERE `id`='$new'");

	// msq("UPDATE `������� ������` SET `unic`='$new' WHERE `unic`='".e($unic)."'"); // ������������� ����
	// msq("UPDATE `������� ������` SET `unic`='$new' WHERE `unic`='".e($unic)."'"); // ������������� ����
	// msq("UPDATE `������� ������` SET `unic`='$new' WHERE `unic`='".e($unic)."'"); // ������������� ����
	// msq("UPDATE `������� ������` SET `unic`='$new' WHERE `unic`='".e($unic)."'"); // ������������� ����

	// msq("DELETE FROM ".$GLOBALS['db_unic']." WHERE `id`='".e($unic)."'"); // ������� �������� ����� �����

	return $p;
}

?>