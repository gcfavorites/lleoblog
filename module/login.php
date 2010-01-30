<?php // Авторизация пользователей

die("пока закрыто");

if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй
blogpage();

$otvet='';

if(isset($_POST['login'])) { $l = $_POST['login'];

	if(strstr($l,'.')) { // openid

	require($GLOBALS['include_sys'].'class.openid.v3.php'); // библиотечка openid

	setcookie('jopenid', $l, time()+120, "/", "", 0);

        $openid = new SimpleOpenID;
        $openid->SetIdentity($l);
        $openid->SetTrustRoot('http://' . $_SERVER["HTTP_HOST"]);
        $openid->SetRequiredFields(array('email','fullname'));
//        $openid->SetOptionalFields(array('dob','gender','postcode','country','language','timezone'));
	$openid->SetOptionalFields(array('dob'));
        if ($openid->GetOpenIDServer()){
                $openid->SetApprovedURL($httpsite.$mypage); // Send Response from OpenID server to this script
                $openid->Redirect(); // This will redirect user to OpenID Server
	        exit;
        }else{
                $error = $openid->GetError();
                $otvet="<font color=red>ERROR CODE: ".$error['code']."<br>ERROR DESCRIPTION: ".$error['description']."</font>";
        }

} else {

	$p=ms("SELECT `password`,`id` FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($l)."'","_1",0);
	$unicnew=$p['id'];

	if(md5($_POST['password'].$hashlogin) == $p['password']) {
	msq("DELETE FROM ".$GLOBALS['db_unic']." WHERE `id`='".e($unic)."' AND `password`=''"); print $msqe; // удалить ненужный более логин

		$unicnew=$p['id'];
		$kuka=$unicnew.'-'.md5($unicnew.$hashlogin);
		setcookie($uc, $kuka, time()+86400*365, "/", "", 0);

		SCRIPTS("function swf(a){ if(navigator.appName.indexOf('Microsoft') != -1) return window[a]; else return document[a]; }
function setIsReady() {	if(swf('kuki').flashcookie_save) swf('kuki').flashcookie_save('".$uc."','".$kuka."');
".($_POST['retpage']!=''?"setTimeout(\"location.replace('".h($_POST['retpage'])."')\", 2000);":"")."}");
		$o.="<div style='position: absolute;width:1px;height:1px;overflow:hidden;left:-40px;top:0;opacity:0'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' id='kuki' width='1' height='1' codebase='http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab' style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'><param name='movie' value='{www_design}kuki_ray.swf' /><embed src='{www_design}kuki_ray.swf' width='1' height='1' name='kuki' type='application/x-shockwave-flash' pluginspage='http://www.adobe.com/go/getflashplayer'></embed></object></div>";

		$p=ms("SELECT `realname`,`login` FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($l)."'","_1",0);

		die($o."<p>Вы залогинились как ".h($l)."!");
	} else {
		print "Ошибка!";
	}
}

} else if(isset($_GET['openid_mode'])) {
	require($GLOBALS['include_sys'].'class.openid.v3.php'); // библиотечка openid

	if($_GET['openid_mode'] == 'id_res'){ // Perform HTTP Request to OpenID server to validate key
        $openid = new SimpleOpenID;
        $openid->SetIdentity($_GET['openid_identity']);
        $openid_validation_result = $openid->ValidateWithServer();
        if ($openid_validation_result == true){                 // OK HERE KEY IS VALID
		$ara=array('login'=>e($_COOKIE['jopenid']));
		if(isset($_GET['openid_sreg_email'])) $ara['mail']=e($_GET['openid_sreg_email']);
		if(isset($_GET['openid_sreg_fullname'])) $ara['realname']=e($_GET['openid_sreg_fullname']);
		if(isset($_GET['openid_sreg_dob'])) $ara['birth']=e($_GET['openid_sreg_dob']);
		msq_update($GLOBALS['db_unic'],$ara,"WHERE `id`='$unic'");
                die("<font color=green>Вы залогинены по Openid как ".$_COOKIE['jopenid']."</font>");
        }else if($openid->IsError() == true){                   // ON THE WAY, WE GOT SOME ERROR
                $error = $openid->GetError();
                $otvet="<font color=red>ERROR CODE: ".$error['code']."<br>ERROR DESCRIPTION: ".$error['description']."</font>";
        }else{  // Signature Verification Failed
                $otvet="<font color=red>INVALID AUTHORIZATION</font>";
        }
	} elseif($_GET['openid_mode'] == 'cancel'){ $otvet="<h1><font color=red>USER CANCELED REQUEST</font></h1>"; } // User Canceled your Request
}


SCRIPTS_mine();

SCRIPTS("
function login_openid(e) { var l=e.value;
	var l2=l.replace(/[^0-9a-z\\-\\_\\.\\/\\~\\=\\@]/g,'');	if(l2!=l) {
		zabil('otvet','<font color=red>Спецсимволы, русские и заглавные буквы запрещены!</font>');
		e.value=l.replace(/[^0-9a-z\\-\\_\\.\\/\\~\\=\\@]/g,'');
		return;
	}

	var l2=l.replace(/[^0-9a-z\\-\\_]/g,''); if(l2!=l) {
		zabil('otvet','<font color=green>логинимся по openid</font>');
		document.getElementById('pass').style.display='none';
		return;
	}

	document.getElementById('pass').style.display='block';
	zabil('otvet','');
}

");

STYLES('

.cf { clear:both; height:0; font-size: 1px; line-height: 0px; }
#openid .l0 { margin: 2pt; border: 1px dotted #ccc; }
#openid .l1 { float: left; text-align: left; width: 250pt; padding: 4px;}
#openid .l2 { float: left; text-align: left; width: 150pt; padding: 4px; border: 1px dotted white;}
#openid .l2:hover { float: left; text-align: left; width: 150pt; padding: 4px; border: 1px dotted red;}

#openid { border: 1px solid gray; display: inline; }
#openid LEGEND { 1.2em; font-weight: bold; color: #FF6200; padding-left: 5px; padding-right: 5px; }
');

$o.="<form action='$mypage' method=POST><input type=hidden name=retpage value='".h($_GET['retpage'].$_POST['retpage'])."'>
<div align=center><div><fieldset id='openid'><legend>Логин</legend>
<div id=otvet>$otvet</div>
".logpole0("login или openid:","<input type=text size=10 onkeyup='login_openid(this)' name=login value='".$_POST['login']."'>")."
<div id=pass>".logpole0("password:","<input type=text size=10 name=password value='".$_POST['password']."'>")."</div>
<input type=submit value='login'>
</div></fieldset></div></form>";

die($o);

function logpole0($text1,$text2,$name='') { return "<div class=l0><div class=l1>$text1</div><div".($name!=''?" id=div".$name:'')." class=l2>$text2</div><br class=cf></div>"; }

?>