<?php // Авторизация пользователей

die("пока закрыто");

if(isset($_GET['JsHttpRequest']) or isset($_POST['JsHttpRequest']) or isset($_COOKIE['JsHttpRequest'])) { // если это был ajax-вызов

//============================================================= AJAX ===================================================

function setpole($s) { global $_RESULT,$name,$value,$unic;
	msq_update($GLOBALS['db_unic'],array(e($name)=>e($value)),"WHERE `id`='$unic'");
	$_RESULT["border"] = "2px solid green";
	$_RESULT["value"] = $value;
	$_RESULT["otvet"] = "<font color=green>$s<font>".$GLOBALS['msqe'];
	$_RESULT["status"] = true;
	exit;
}


function zsetpole($s) { global $name,$value,$unic; msq_update($GLOBALS['db_unic'],array(e($name)=>e($value)),"WHERE `id`='$unic'"); zsetpole0($s); }
function zsetpole0($s) { global $_RESULT,$name; $_RESULT["zname"]='div'.$name; $_RESULT["zotvet"] = $s; $_RESULT["status"] = true; exit; }

function errpole($s='') { global $_RESULT,$name,$value,$unic;
	$_RESULT["border"] = "2px solid red";
	$_RESULT["value"] = $value;
	$_RESULT["otvet"] = ($s!=''?"<font color=red>$s<font>":'');
	$_RESULT["status"] = true;
	exit;
}

require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$value=trim(strtr($_REQUEST["value"],"\r",''),"\n\t ");
$name=strtr($_REQUEST["name"],"\r\n\t ",'');

if($name=='mail') { if(mail_validate($value)) setpole("email записан"); else errpole("Послушай, никто тебя с ножом у горла не заставляет указывать email, верно? Он нужен, чтобы получать ответы на комментарии или восстановить забытый пароль. Но уж если заполняешь эту графу - напиши правильный."); }
if($name=='site') {
	if(site_validate($value)) setpole("site записан");
	elseif(site_validate('http://'.$value)) { $value='http://'.$value; setpole("site записан"); }
	else errpole("Это необязательная графа для заполнения. Если у тебя нет сайта или ты не желаешь его указывать - оставь ее пустой. Но писать всякую глупость сюда незачем.");
}
if($name=='birth') {
	list($y,$m,$d)=explode('-',$value); $value=sprintf("%04d-%02d-%02d",$y,$m,$d);
	if(intval($y)*intval($m)*intval($d)) setpole("день рождения записан: $value"); else errpole();
}


if(substr($name,0,7)=='capcha-') { list($name,$val)=explode('-',$name);
	include_once $GLOBALS['include_sys']."_antibot.php";
        if(!antibot_check($value,$val)) {
		$_RESULT["otvet"] = "<font color=red>Цифры с картинки указаны неверно!<font>";
		zsetpole0(capcha_input());
	} else { $value='yes'; zsetpole("не робот"); }
}

if($name=='login') {
	if(preg_match("/[^0-9a-z\-\_]/s",$value)) errpole("В логине допустимы только строчные латинские буквы, цифры, подчеркивание или минус.");
	if(strlen($value)>32) { $value=substr($value,0,32); errpole("Длина логина - не более 32 символов."); }
	$id=ms("SELECT `id` FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($value)."'","_l",0);
	if($id===false) setpole("Отныне твой логин - $value");
	if($id==$unic) setpole("Да, твой логин $value, и не надо выпендриваться.");
	errpole("Этот логин занят!");
}

if($name=='password') {
	// блять, так хочется тоже сделать проверку "этот пароль уже используется"... но понимаю, перебор :)
	// или сделать? да идите нахуй, сделаю! лови:
	$id=ms("SELECT `id` FROM ".$GLOBALS['db_unic']." WHERE `password`='".e($value)."'","_l",0);
	if($id!==false) errpole("Этот пароль уже занят! Придумай что-нибудь поинтереснее.");

	$value=md5($value.$hashlogin); zsetpole("Пароль установлен!");
}





setpole("$name записано: $value");


//============================================================= PROG ===================================================

} else {

if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// if(!$admin) redirect($wwwhost."login/"); // посторонних - нахуй
blogpage();

if($unic==0) die("У вас куки не включены. Или вы первый раз на этом сайте?");

$OLOLO=md5($IP.$BRO.$hashlogin);

$otvet='';

// OPENID
if(isset($_GET['openid_mode'])) { require($GLOBALS['include_sys'].'class.openid.v3.php'); // библиотечка openid


if($_GET['openid_mode'] == 'id_res'){ // Perform HTTP Request to OpenID server to validate key
        $openid = new SimpleOpenID;
        $openid->SetIdentity($_GET['openid_identity']);
        $openid_validation_result = $openid->ValidateWithServer();
        if ($openid_validation_result == true){                 // OK HERE KEY IS VALID
                $otvet="<font color=green>OPENID подтвержден: ".$_COOKIE['jopenid']."</font>";
		$ara=array('login'=>e($_COOKIE['jopenid']));
		if(isset($_GET['openid_sreg_email'])) $ara['mail']=e($_GET['openid_sreg_email']);
		if(isset($_GET['openid_sreg_fullname'])) $ara['realname']=e($_GET['openid_sreg_fullname']);
		if(isset($_GET['openid_sreg_dob'])) $ara['birth']=e($_GET['openid_sreg_dob']);
		msq_update($GLOBALS['db_unic'],$ara,"WHERE `id`='$unic'");
//		die('<pre>'.print_r($GLOBALS,1));
        }else if($openid->IsError() == true){                   // ON THE WAY, WE GOT SOME ERROR
                $error = $openid->GetError();
                $otvet="<font color=red>ERROR CODE: ".$error['code']."<br>ERROR DESCRIPTION: ".$error['description']."</font>";
        }else{  // Signature Verification Failed
                $otvet="<font color=red>INVALID AUTHORIZATION</font>";
        }
}

elseif($_GET['openid_mode'] == 'cancel'){ $otvet="<h1><font color=red>USER CANCELED REQUEST</font></h1>"; } // User Canceled your Request

elseif($_GET['ololo']=$OLOLO) {
        $openid = new SimpleOpenID;
        $openid->SetIdentity($_GET['openid_mode']);
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
}

}



STYLES('

.cf { clear:both; height:0; font-size: 1px; line-height: 0px; }
#openid .l0 { margin: 2pt; border: 1px dotted #ccc; }
#openid .l1 { float: left; text-align: left; width: 250pt; padding: 4px;}
#openid .l2 { float: left; text-align: left; width: 150pt; padding: 4px; border: 1px dotted white;}
#openid .l2:hover { float: left; text-align: left; width: 150pt; padding: 4px; border: 1px dotted red;}

#openid { border: 1px solid gray; display: inline; }
#openid LEGEND { 1.2em; font-weight: bold; color: #FF6200; padding-left: 5px; padding-right: 5px; }

#openid, #openid INPUT { font-family: "Trebuchet MS"; font-size: 12px; border: 1px solid gray; }
#openid SELECT { font-family: "Trebuchet MS"; font-size: 12px; }

#openid INPUT.openid_login { color: #000; padding-left: 18px; width: 220px; margin-right: 10px; }
#openid INPUT.openid_login2{ color: #000; padding-left: 18px; width: 120px; margin-right: 10px; }
');


SCRIPTS_mine();
SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js"); // подгрузить внешний скрипт
SCRIPTS("

function loginset(e) { var l = e.value;
	if( l.replace(/[^a-z0-9\\-\\_]/g,'') == l ) return poleset(e);
	zabil('otvet','<font color=green>проверяем OPENID \"'+l+'\"</font>');
	e.disabled=true; e.style.border='1px dashed red';
	var N=new Date(); N.setTime(N.getTime()+120000); document.cookie='jopenid='+l+';expires='+N.toGMTString()+';path=/;';
	location.replace('".$mypage."?openid_mode='+encodeURIComponent(l)+'&ololo=".$OLOLO."');
}

function login_openid(e) { var l=e.value;
        var l2=l.replace(/[^0-9a-z\\-\\_\\.\\/\\~\\=\\@]/g,''); if(l2!=l) {
                zabil('otvet','<font color=red>Спецсимволы, русские и заглавные буквы запрещены!</font>');
                e.value=l.replace(/[^0-9a-z\\-\\_\\.\\/\\~\\=\\@]/g,'');
                return;
        }

        var l2=l.replace(/[^0-9a-z\\-\\_]/g,''); if(l2!=l) {
                zabil('otvet','<font color=green>авторизация по openid</font>');
                document.getElementById('pass').style.display='none';
                return;
        }

        document.getElementById('pass').style.display='block';
        zabil('otvet','');
}

function poleset(e) { e.disabled=true; e.style.border='1px dashed red';
        JsHttpRequest.query('$mypage',{ name: e.name, value: e.value },
        function(responseJS, responseText) { if(responseJS.status) { 
		if(responseJS.otvet) zabil('otvet',responseJS.otvet); else zabil('otvet','');
		if(responseJS.zname) zabil(responseJS.zname,responseJS.zotvet);
		else {
			e.value=responseJS.value; 
			e.disabled=false;
			e.style.border=responseJS.border;
			// e.focus();
		}
	}},true);
}

function setbirth(y,m,d) { var e=document.getElementById('birth'); e.value=y.value+'-'+m.value+'-'+d.value; poleset(e); }
");



// if(preg_match("/[^0-9a-z\-\_\.\/\~\=\@]/",$p['login']))

function logpole($text,$name,$value,$size,$disable=0) { if(!$size) $size=max(strlen(h($value)),5);
return logpole0($text,"<input onchange='poleset(this)' type=text id=$name name=$name size=$size value='".h($value)."'".($disable?' disabled="disabled"':'').">",$name);
} function logpole0($text1,$text2,$name='') { return "<div class=l0><div class=l1>$text1</div><div".($name!=''?" id=div".$name:'')." class=l2>$text2</div><br class=cf></div>"; }


function select_data($Y,$M,$D) {
	$u='onchange="setbirth(this.form.y,this.form.m,this.form.d)" name';
	$a=array(''=>'---'); for($i=1;$i<=31;$i++) $a[$i]=sprintf("%02d",$i); $o=selecto('d',$D,$a,$u);
	$a=array(''=>'---'); for($i=1;$i<=12;$i++) $a[$i]=$GLOBALS['months_rod'][$i]; $o.=selecto('m',$M,$a,$u);
	$a=array(''=>'---'); for($i=(date('Y')-5);$i>1900;$i--) { $l=sprintf("%04d",$i); $a[$l]=$l; } $o.=selecto('y',$Y,$a,$u);
	return $o;
}

$p=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `id`='$unic'","_1");

$o.="<form><div align=center><div><fieldset id='openid'><legend>Личное дело номер $unic</legend>
<p class=br>зарегистрирован: <b>".date("Y-m-d H:i:s",$p['time_reg'])."</b><br>
".($p['lju']!=''?"<a href='http://".h($p['lju']).".livejournal.com'>".h($p['lju'])."</a> &nbsp; ":'').h(ipn2ip($p['ipn']))."
<div id='otvet' style='width: 400pt; text-align: justify;'>$otvet</div>
";

if($p['capcha']=='no') { include_once $GLOBALS['include_sys']."_antibot.php"; $o .= logpole0("Подтвердите, что вы не робот:",capcha_input(),'capcha' ); }
//if($p['login']=='') 
$o.=logpole0((strstr($p['login'],'.')?"openid:":"login:"),"<input onchange='loginset(this)' onkeyup='login_openid(this)' type=text id=login name=login size=15 value='".h($p['login'])."'>");
//else $o.=logpole0("login:",h($p['login']));

$o.="<div id=pass".(strstr($p['login'],'.')?" style='display: none'":"").">";
$o.=logpole("Пароль".($p['password']==''?'':' (есть, но можно сменить)').":",'password','',10);
$o.="</div>";

$o.=logpole("mail:",'mail',mail_validate($p['mail']),20);
$o.=logpole("site:",'site',site_validate($p['site']),25);
$o.=logpole("Имя (подписывать комментарии)",'realname',$p['realname'],25);
if($p['admin']!='user') $o.=logpole("доступ:",'admin',$p['admin'],0,true);

if($p['birth']=='0000-00-00') {
	list($p['bir_y'],$p['bir_m'],$p['bir_d'])=explode('-',h($p['birth']));
	$o.=logpole0("День рождения:<input type=hidden id=birth name=birth>",select_data($p['bir_y'],$p['bir_m'],$p['bir_d']));
} else $o.=logpole0("День рождения:",h($p['birth']));

$o.="</div></fieldset></div></form>";

die($o);
}

function capcha_input() {
return "<table><tr valign=center><td><input onchange='poleset(this)' size=".$GLOBALS['antibot_C']." type=text name='capcha-".antibot_make()."'></td><td>".antibot_img()."</td></tr></table>";
}

?>