<?php // ����������� �������������

if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����
// if(!$admin) redirect($wwwhost."login/"); // ����������� - �����
blogpage();

idie("������ ������� �������� ��������. ������, ����� ����� �����, ���� � ���������. �����? ��, �����, ����� ������ ����... :)");


$_PAGE["header"]=$_PAGE["title"]="����� �������";

include_once($_SERVER["DOCUMENT_ROOT"]."/sys/class_antibot.php");
$anti_bot = new AntiBot();
$anti_bot->pics_path = $_SERVER["DOCUMENT_ROOT"]."/sys/antibot";
$anti_bot->pics_tmp_path = $_SERVER["DOCUMENT_ROOT"]."/".$web_path."antibot";
$anti_bot->pics_tmp_web_path = "/".$web_path."antibot";
$anti_bot->sumbolC = 5;
$anti_bot->add2hash = $_SERVER["REMOTE_ADDR"];
$antibot_ph=$anti_bot->GetPic();

$retpage='http://'.$_SERVER["HTTP_HOST"].htmlspecialchars($_GET['retpage']);

$o='
<style>

#errors { font-family: "Trebuchet MS"; font-size: 12px;
border: 1px solid red; display: inline; background-color: #FDD; margin-bottom: 5px; padding: 20px; }
#errors LEGEND { 1.2em; font-weight: bold; color: red; padding-left: 5px; padding-right: 5px; }

#openid { border: 1px solid gray; display: inline; }
#openid, #openid INPUT { font-family: "Trebuchet MS"; font-size: 12px; border: 1px solid gray; }
#openid SELECT { font-family: "Trebuchet MS"; font-size: 12px; }
#openid LEGEND { 1.2em; font-weight: bold; color: #FF6200; padding-left: 5px; padding-right: 5px; }
#openid INPUT.openid_login { color: #000; padding-left: 18px; width: 220px; margin-right: 10px; }
#openid INPUT.openid_login2{ color: #000; padding-left: 18px; width: 120px; margin-right: 10px; }

#openid .lna { margin-top: 7px; } /*float: right;*/
#openid INPUT.llo,SELECT.llo,OPTION.llo { width: 220px; }

#openid A{ color: silver; }
#openid A:hover{ color: #5e5e5e; }

</style>';

include_once($_SERVER["DOCUMENT_ROOT"]."/sys/openid/class.openid.php");

$mysrc='http://'.$_SERVER["HTTP_HOST"].'/dnevnik/logon/';

$errauto=array();

$action_login="�����";
$action_login_openid="������������ �� OpenID";
$action_login_new="������������������ �� �����";

function valvar($n,$l,$preg='^eRrOr$',$errmes='error') { global $errauto;
if($l=='') return '';
if(preg_match("/".$preg."/",$l)) return $l;
$errauto[$n]=$errmes."<br>��������, � ������ ����������?";
return htmlspecialchars($l);
}

$l='newlog'; $$l=valvar($l,$_POST[$l],'^[0-9a-z\-\_]+$',
'����� ����� �������� �� <b>��������</b> ��������� ����, ����, ������������� � ������. ��� �� ��������.');
$$l=strtolower($$l);

$l='log'; $$l=valvar($l,$_POST[$l],'^[0-9a-z\-\_]+$',
'����� ����� �������� �� �������� ��������� ����, ����, ������������� � ������. ��� �� ��������.');
$$l=strtolower($$l);

$l='openid_url'; $$l=valvar($l,$_POST[$l],'^[0-9a-z\.\:\/\-\_]+$',
'OPenID - ��� ����������� ����, ��� �� ����������. �������� ����� ���������, ��� ������ ��������.');
$$l=strtolower($$l);

$l='realname'; $$l=valvar($l,$_POST[$l],'^[0-9a-zA-Z\-\_�������������������������������������Ũ��������������������������\. ]+$',
'� ������ �� ���������������. ��������� ������� � ��������� �����, ������, �����, �����, ������������� � �����.');

$l='mail'; $$l=valvar($l,$_POST[$l],'^[0-9a-zA-Z_\-\.]+@[0-9a-z_\-]+\.[0-9a-z_\-\.]+$',
'����� ��� � ����� � ����� �� ���������� ��������� email. �� ���� ���������� - �������� ����������.');
$$l=strtolower($$l);

$l='pod'; $a=$_POST['pod'];
	if($a=='') $a='dnevnik';
	if(	in_array($a,array('dnevnik','lichnoe','techno'))	) $$l=$a; else $errauto[$l]='������� ���������� "pod"';


//############################################################################

if($admin && isset($_GET['update'])) { $m=$_POST;
	$user=htmlspecialchars($m['login']); unset($m['login']);
	if(!sizeof($m)) die("��� ������!");
	if($user=='' || ms("SELECT `login` FROM `$db_login` WHERE `login`='".e($user)."'","_l",0) === false ) die("��� ������ ������������: '".$user."'");
	$ara=array('login'=>e($user)); foreach($m as $l=>$v) $ara[e($l)]=e($v);
	msq_update($db_login,$ara,"WHERE `login`='".$ara['login']."'"); // �������
	redirect($myscr."?userinfo=".$user);
	exit;
}

//############################################################################

if($podzamok && isset($_GET['list'])) {

$p=ms("SELECT `login`,`birth`,`mail`,`count`,`timelast`	FROM `$db_login` WHERE `admin`='podzamok' ORDER BY `timelast` DESC","_a",0);

if(sizeof($p)) {

	$s.="<p><b>������ ������, ������� ����������� ������: ".sizeof($p)."</b><p>";

	$admin_color=array('admin'=>'#FF9090','user'=>false,'podzamok'=>'#9090FF','comblock'=>'#90FF90');

$s=''; foreach($p as $l) {

        list($timelast,$lasttime)=explode(' ',$l['timelast']);
        switch($timelast) {
                case date('Y-m-d'): { $timelast='<font color=red>'.$lasttime.'</font>'; break; }
                case date('Y-m-d',time()-60*60*24): { $timelast='<font color=green>�����</font>'; break; }
                case date('Y-m-d',time()-2*60*60*24): { $timelast='<font color=green>���������</font>'; break; }
                case date('Y-m-d',time()-3*60*60*24): { $timelast='<font color=green>3 ���</font>'; break; }
                case date('Y-m-d',time()-4*60*60*24): { $timelast='<font color=green>4 ���</font>'; break; }
                case date('Y-m-d',time()-5*60*60*24): { $timelast='<font color=green>5 ����</font>'; break; }
                case date('Y-m-d',time()-6*60*60*24): { $timelast='<font color=green>6 ����</font>'; break; }
                case date('Y-m-d',time()-7*60*60*24): { $timelast='<font color=green>7 ����</font>'; break; }
                case date('Y-m-d',time()-8*60*60*24): { $timelast='<font color=green>8 ����</font>'; break; }
                case date('Y-m-d',time()-9*60*60*24): { $timelast='<font color=green>9 ����</font>'; break; }
                case date('Y-m-d',time()-10*60*60*24): { $timelast='<font color=green>10 ����</font>'; break; }
                case date('Y-m-d',time()-11*60*60*24): { $timelast='<font color=green>11 ����</font>'; break; }
                case date('Y-m-d',time()-12*60*60*24): { $timelast='<font color=green>12 ����</font>'; break; }
                case date('Y-m-d',time()-13*60*60*24): { $timelast='<font color=green>13 ����</font>'; break; }
                case date('Y-m-d',time()-14*60*60*24): { $timelast='<font color=green>14 ����</font>'; break; }
        }

        $is=get_IS($l['login']);
        $bg=($bg=='#FFFFE0'?'#FFE0FF':'#FFFFE0');
        $bg2=($admin_color[$l['admin']]?$admin_color[$l['admin']]:$bg);
        $s.="<tr bgcolor='".$bg2."'>

<td align=center>".$timelast."</td>

<td><font size=2><img src='".$is['IMG']."' alt=' (".$is['ROOT'].") '><a href='http://"
.($is['DOMAIN']=='lleo.aha.ru'?'lleo.aha.ru/user/':'').$login."'>"
."<a href=http://".htmlspecialchars($l['login']).">".htmlspecialchars($l['login'])."</a></font></td>
<td><a href='/dnevnik/logon/?userinfo=".$l['login']."'>info</a></td>
<td align=center>".($l['birth']!='0000-00-00'?htmlspecialchars($l['birth']):"&nbsp;")."</td>
<td>".($l['mail']!=''?"<a href='mailto:".htmlspecialchars($l['mail'])."'>".htmlspecialchars($l['mail'])."</a>":"&nbsp;")."</td>
<td align=right>".$l['count']."</td>
</tr>";

        } if($s!='') $s.="<p><center><table class=br cellspacing=0 cellpadding=2>".$s."</table></center>";
}

$body .= $s; exit;
}

//===============================================================================================
//===============================================================================================
//===============================================================================================
//===============================================================================================
//===============================================================================================
//===============================================================================================
//===============================================================================================

if(isset($_GET['userinfo'])) {
	$user=htmlspecialchars($_GET['userinfo']);

	if($user=='' || ms("SELECT `login` FROM `$db_login` WHERE `login`='".e($user)."'","_l",0) === false ) die("��� ������ ������������: '".$user."'");

$_PAGE['body'] = "<p>� ��������, ��� ��������� ������� ������������ <img src='".$www_design."img/myblog.ico'><b>".$user."</b>. �� ���
���� �������: ������ ��������� ���������� � ������������. �����-������ ����� ����� ����������� ������ �������, ��� ������������ ������
������������� ���� ������ � ��������� ��� �������� � �������� (� �������� ������ ������� �� ������ ��������� (��� ����), � ����� ������
� ����������� (��� ������������������ �� �� �������).";

$is=ms("SELECT `sc`,`timereg`,`timelast`,`realname`,`mail`,`site`,`birth`,`admin`,`podpiska`,`img`,`count`,`type`
FROM `$db_login` WHERE `login`='".e($user)."'","_1",0);

$_PAGE['body'] .= "

<p><center><table style='border-collapse: collapse; border: 1px solid red; margin: 20pt;' bgcolor=#fffff0 border=1 cellpadding=20><td>

<p><b><big>".$user."</big></b>
<br>���������������: ".$is['timereg']."
".($is['realname']?"<br>���: <b>".$is['realname']."</b>":'')."
".($is['site']?"<br>����: <a href='".$is['site']."'>".$is['site']."</a>":'')."
<br>���� ��������: <b>".$is['birth']."</b>
<br>��������: <b>".$is['podpiska']."</b>
<br>��������� �����: <b>".$is['timelast']."</b>
<br>�������: <b>".$is['count']."</b>

</td></table></center>";




if($admin) { 


$_PAGE['body'] .= "

<form action='".$myscr."?update' method='post'>
<input type='hidden' name='login' value='".$user."'>

<p><center><table style='border-collapse: collapse; border: 1px solid red; margin: 20pt;' bgcolor=#fffff0 border=1 cellpadding=20><td>
".($is['mail']?"<br>mail: <b>".$is['mail']."</b>":'')."
<br>������� �������: ".selecto('admin',$is['admin'],array(
			'user'=>'user',
			'podzamok'=>'podzamok',
			'comblock'=>'comblock',
			'mudak'=>'mudak') )."
</td></table>

<p><input type='submit' value='��������'>

</form>
</center>


";


// $_PAGE['body'] .= "<p><pre>".print_r($is,1)."</pre>";

}

$_PAGE['body'] .= "<p>���������� <a href=/dnevnik/comments?mode=one&user=".urlencode($user)."&sc=".urlencode($is['sc']).">��� �����������</a>, ����������� $user";


exit;
}

elseif ($_GET['action'] == 'logoff') { // �������������
	logoff();
	redirect($retpage);
}

if($IS_USER) $o.="<p>�� ���������� ��� <b><img src='$IS_IMG' border=0>$IS_USER</b>, � ���� ����� ������ ����������. � ������� ����� ������ ����
������ ����� ����������� ������ �������, ��� �� ������� ������������� ���� ������, �������� ��������� �������� � �������� (�
�������� ������ ������� �� ������ ��������� (��� ����), � ����� ������ � ����������� (��� ������������������ �� �� �������).";


elseif ($_POST['action'] == $action_login_openid) { // Get identity from user and redirect browser to OpenID Server
	$openid = new SimpleOpenID;
	$openid->SetIdentity($_POST['openid_url']);
	$openid->SetTrustRoot('http://'.$_SERVER["HTTP_HOST"]);
	$openid->SetRequiredFields(array('email','fullname'));
	$openid->SetOptionalFields(array('dob','gender','postcode','country','language','timezone'));
	if ($openid->GetOpenIDServer()){
		$openid->SetApprovedURL($mysrc."?retpage=".urlencode($_GET['retpage'])."&loginil=".urlencode($_POST['openid_url'])); // Send Response from OpenID server to this script
		$openid->Redirect(); // This will redirect user to OpenID Server
		exit;
	}else{
		$error = $openid->GetError();
		$errauto[]="ERROR CODE: ".$error['code'];
		$errauto[]="ERROR DESCRIPTION: ".$error['description'];
	}
}

elseif($_GET['openid_mode'] == 'id_res'){ // Perform HTTP Request to OpenID server to validate key
	$openid = new SimpleOpenID;
	$openid->SetIdentity($_GET['openid_identity']);
	$openid_validation_result = $openid->ValidateWithServer();
	if ($openid_validation_result == true){ // OK HERE KEY IS VALID

		$openid_user=$openid->OpenID_Standarize($_GET['loginil']);

	        $ara=array();
	        $ara['timereg']=date("Y-m-d H:i:s");
	        $ara['login']=e($openid_user);
	        $ara['type']='openid';
	        $ara['text']=e(print_r($_GET,1));

	        $ara['sc']=e($sc);

		$x=( $_GET['openid_sreg_fullname'] ? uw($_GET['openid_sreg_fullname']) : $_COOKIE['CommentaryName'] );
	        $ara['realname']=e(htmlspecialchars($x));

		$x=( $_GET['openid_sreg_email'] ? uw($_GET['openid_sreg_email']) : str_replace('mailto:','',$_COOKIE['CommentaryAddress']) );
	        $ara['mail']=e(htmlspecialchars($x));

		$x=$_GET['openid_sreg_dob'];
	        $ara['birth']=e(htmlspecialchars($x));

		if(!msq_exist($db_login,"WHERE `login`='".$ara['login']."'")) msq_add($db_login,$ara);
		logon($openid_user,1);
		redirect($retpage);
//		echo "VALID = ". $openid->OpenID_Standarize($_GET['loginil']); exit;


	}else if($openid->IsError() == true){ // ON THE WAY, WE GOT SOME ERROR
		$error = $openid->GetError();
		$errauto[]="ERROR CODE: ".$error['code'];
		$errauto[]="ERROR DESCRIPTION: ".$error['description'];
	}else{ // Signature Verification Failed
		$errauto[]="INVALID AUTHORIZATION";
	}
}

elseif ($_GET['openid_mode'] == 'cancel'){ // User Canceled your Request
		$errauto[]="USER CANCELED REQUEST";
}


//===================================================================================================

elseif ($_POST['action'] == $action_login){ // ����� �� lleo.aha.ru
	$p=ms("SELECT `password` FROM `$db_login` WHERE `login`='".e($log)."'","_1",0);
	if($p===false) $errauto[]="������ ������������ �� ����������.";
	elseif( $p['password'] != md5($_POST['pas'].$GLOBALS['hashlogin']) ) $errauto['pas']="������������ ������.";
	if(!sizeof($errauto)) { logon($log); redirect($retpage); }
}

elseif ($_POST['action'] == $action_login_new){ // ������� ������ ������������ �� lleo.aha.ru
	if($newlog=='') $errauto['newlog']="����� ���� ���������. ��� �� ������. ��������� �������, ����� - ��� �������!";
	if($_POST['newpas']=='') $errauto['newpas']="���������� ������, ��� �� ������.";
	if($_POST['newpas2']!=$_POST['newpas']) $errauto['newpas2']="������ ������ ��� ���� ��-�������. ������������.";
	if(!$anti_bot->CheckCode(intval($_POST['AntiBotCode']))) $errauto['newpas2']="������� ������� ����� � ��������.";

	if(!sizeof($errauto)) {

        $ara=array();
        $ara['login']=e($newlog);
        $ara['sc']=e($sc);
        $ara['timereg']=date("Y-m-d H:i:s");
        $ara['realname']=e($realname);
        $ara['mail']=e($mail);
        $ara['site']=e($site);
        $ara['birth']=e(  sprintf("%04d",intval($bir_y)).'-'.sprintf("%02d",intval($bir_m)).'-'.sprintf("%02d",intval($bir_d))  );
        $ara['podpiska']=e($pod);
        $ara['password']=md5($_POST['newpas'].$GLOBALS['hashlogin']);
        $ara['type']='login';

	if(msq_exist($db_login,"WHERE `login`='".$ara['login']."'")) {
			$errauto[]="����� ������������ ��� ����������.";
//			msq_update($db_login,$ara,"WHERE `login`='".$ara['login']."'");
			}
	else msq_add($db_login,$ara);


	logon($newlog,0);
	redirect($retpage);

//	$o.="##########################".$msqe;

	}
}



//====================================================================================================

if(sizeof($errauto)) {
	$o.="<div align=center><fieldset id='errors'><legend>������</legend><ul>";
        foreach($errauto as $l) $o.="<li>".$l."</li>\n";
	$o.="</ul></fieldset></div>\n<p>\n";
}















if($IS_USER) $o.="<center><p class=br><a href='$myscr?action=logoff".($_GET['retpage']!=''?"&retpage=".urlencode($_GET['retpage']):'')."'>�������������</a></center>";

























if(!$IS_USER && (!isset($_POST['action'])||$_POST['action']==$action_login_openid)) {

$o.="<form name='login_openid' action='$myscr' method='post' onsubmit='this.login.disabled=true;'>";

if(!isset($_POST['action'])) $o.="
<p>���� �� ������ ���������� �� ������ �������� (lj, ya.ru, moikrug � �.�.), � ������� �� ���������� � ��������� �����������,
� ������ �������������� �� ��������� OpenID ��� ������������ ������� ������� (��������: "
.($lju!=''?htmlspecialchars($lju):'vasya-pupkin').".livejournal.com).
� ��� �����, � � ���� ������, ��� �� ������������� �������� ���� ��������:";

if($_POST['openid_url']!='') $u=" value='".htmlspecialchars($_POST['openid_url'])."'";
elseif($_COOKIE['log']!=''&&$_COOKIE['openid']==1) $u=" value='".htmlspecialchars($_COOKIE['log'])."'";
elseif($lju!='') $u=" value='".htmlspecialchars($lju).".livejournal.com'";
else $u='';

$o.="<div align=center><fieldset id='openid'><legend>OpenID</legend>
<div><input type='text' name='openid_url' class='openid_login'".$u.">
<input type='submit' name='action' value='".$action_login_openid."'></div>
</fieldset></div>";

$o.="</form>";

}


if(!$IS_USER && !isset($_POST['action'])||$_POST['action']==$action_login) {

$o.="<form name='login_mylogin' action='$myscr' method='post' onsubmit='this.login.disabled=true;'>";

if(!isset($_POST['action'])) $o.="<p>���� OpenID-���������� � ��� ���, �� �� �����-�� �������� ������ ����������� �� ���� �����,
�� ������������ �����:";

if($_POST['log']!='') $u=" value='".htmlspecialchars($_POST['log'])."'";
elseif($_COOKIE['log']!=''&&$_COOKIE['openid']!=1) $u=" value='".htmlspecialchars($_COOKIE['log'])."'";
else $u='';

$o.="<div align=center><fieldset id='openid'><legend>����� / ������</legend>
<div><input type='text' name='log' class='openid_login2'".$u.">
<input type='password' name='pas' class='openid_login2'".(isset($_POST['pas'])?" value='".htmlspecialchars($_POST['pas'])."'":"").">
<input type='submit' name='action' value='".$action_login."'></div></fieldset></div>";

$o.="</form>";

}


if(!$IS_USER && (!isset($_POST['action'])||$_POST['action']==$action_login_new)) {

$o.="<form name='login_mysite' action='$myscr' method='post' onsubmit='this.login.disabled=true;'>";

if(!isset($_POST['action'])) $o.="<p>���� � ��� ���� �� OpenID-����������, �� ������ ������� ������ �� ���� �����, �� ������� ��
����� ������:";
$o.="<div align=center><fieldset id='openid'><legend>����������� �� �����</legend>
<p class='lna'>login (������ ��������� �������): <input type='text' name='newlog' class='llo' value='".htmlspecialchars($newlog)."'>
<p class='lna'>�������� ��� (����� �� ���������): <input type='text' name='realname' class='llo' value='".($realname!=''?htmlspecialchars($realname):(isset($_COOKIE['CommentaryName'])?htmlspecialchars($_COOKIE['CommentaryName']):""))."'>
<p class='lna'>������: <input type='text' name='newpas' class='llo' value='".htmlspecialchars($_POST['newpas'])."'>
<p class='lna'>�������� ������: <input type='text' name='newpas2' class='llo' value='".htmlspecialchars($_POST['newpas2'])."'>
<p class='lna'>Email (��� ������� �� �����������): <input type='text' name='mail' class='llo' value='".($mail!=''?htmlspecialchars($mail):(isset($_COOKIE['CommentaryAddress'])?htmlspecialchars(str_replace('mailto:','',$_COOKIE['CommentaryAddress'])):""))."'>
<p class='lna'>�������� �������� ��� ����: <input type='text' name='site' class='llo' value='".(isset($_POST['site'])?htmlspecialchars($_POST['site']):"")."'>

<p class='lna'>���� ��������:
<select name='bir_y'><option value=''".($_POST['bir_y']==''?" selected":"").">---</option>";


for($i=(date('Y')-5);$i>1900;$i--) { $y=sprintf("%04d",$i); $o.="<option value='$y'".($_POST['bir_y']==$y?" selected":"").">$y</option>"; }
$o.="</select><select name='bir_m'><option value=''".($_POST['bir_m']==''?" selected":"").">---</option>";
$m=explode(' ','������ ������� ���� ������ ��� ���� ���� ������ �������� ������� ������ �������');
foreach($m as $i=>$l) { $y=sprintf("%02d",($i+1)); $o.="<option value='$y'".($_POST['bir_m']==$y?" selected":"").">$l</option>"; }
$o.="</select><select name='bir_d'><option value=''".($_POST['bir_d']==''?" selected":"").">---</option>";
for($i=1;$i<=31;$i++) { $y=sprintf("%02d",$i); $o.="<option value='$y'".($_POST['bir_d']==$y?" selected":"").">$y</option>"; }
$o.="</select>

<p class='lna'>������� �������� � ��������: <select name='pod' class='llo'>
	<option value='dnevnik'".($_POST['pod']=='dnevnik'?" selected":"").">������������� ������</option>
	<option value='lichnoe'".($_POST['pod']=='lichnoe'?" selected":"").">� ������ (�������) ������</option>
	<option value='techno'".($_POST['pod']=='techno'?" selected":"").">� ����������� ������� (�����)</option>
</select>
<p>

<center><table><tr>
        <td>".$anti_bot->ImgTag()."</td>
        <td><input class=t type=text name='AntiBotCode' size='".$anti_bot->sumbolC."' maxlength='".$anti_bot->sumbolC."'></td>
        <td><input type='submit' name='action' value='".$action_login_new."'></td>
</tr></table></center>

</div></fieldset></div>

<p>���������� �� ������� ���� ������: �� ���� ������� ������������ �� ��, � ������ ���, �� �������� ������������ ������
����������, ���� �� ��� ��������. � ������� � �������� �������� ������ ��� �������������� ������ ������ � �������������
���������� ��������� �����, �� ������� ����� ����� ��������� ����� ��������������� ������ ������ ��������. �� ���� �����
���, � ����� � ���� ����� �� ��� �����, � �� ���� � ������������.";



$o.="</form>";

}

if(isset($_GET['retpage'])) {

$o.="<center><p class=br>����� �� �������������� ��������� �� �������� <a href='$retpage'>$retpage</a>, ������ ������</center>";
}







$_PAGE['body'] = $o;

function logon($log,$openid=0) { 
	set_cookie("log", $log, time()+86400*365, "/", "", 0, true);
	set_cookie("pas", broident($log.$GLOBALS['hashlogin']), time()+86400*365, "/", "", 0, true);
	set_cookie("openid", $openid, time()+86400*365, "/", "", 0, true);
}

function logoff() { 
	set_cookie("pas", 'logoff', time()+86400*365, "/", "", 0, true);
}

?>