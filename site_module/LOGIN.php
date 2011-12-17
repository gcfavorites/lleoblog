<?php // Опенид

function LOGIN($e) { global $img,$dom,$dob,$name,$info,$site,$mail;
/*
	include $GLOBALS['include_sys']."json.php";
	$s='{"network":"openid","identity":"http:\/\/lleo.myopenid.com\/","uid":"32826133a37dcc5d84eac4b506bf7bb8","email":null,"nickname":null,"last_name":"dsdfsf","first_name":"lleo","bdate":"..","sex":"1","photo":"http:\/\/ulogin.ru\/img\/photo.png","photo_big":"","city":"","country":""}';
	$j=jsonDecode($s);
	return $s."<hr><pre>".print_r($j,1);
*/
$thispage=$GLOBALS['httpsite'].$GLOBALS['mypage'];

$conf=array_merge(array(
'redirect'=>false,
'page'=>"<script>
function mykeys(){hotkey=[];return;}page_onstart.push('hotkey_reset=mykeys;hotkey_reset();'); // отключить хоткеи
function mtoken(e){ this.location.href='".$thispage."?token='+e; }
</script>

<div style='position:absolute;width:1px;height:1px;overflow:hidden;left:-40px;top:0;opacity:0'><object id=kuki width=1 height=1 style='width:1px;height:1px;overflow:hidden;position:absolute;left:-400px;top:0;border:0;'><param name='movie' value='/dnevnik/design/kuki_ray.swf' /><embed src='/dnevnik/design/kuki_ray.swf' width=1 height=1 name=kuki type='application/x-shockwave-flash'></embed></object></div>

<div style='font-size:20pt;font-weight:bold;margin-bottom:20pt;'>два способа залогиниться</div>

<table border='0' cellspacing='10'>

<tr><td width=20%>
<p class='r'>Есть OpenID или аккаунт в социальных сетях?

</td><td>

<div id='uLogin'></div>
<script src='http://ulogin.ru/js/widget.js?display=panel"
."&callback=mtoken"
."&fields=&optional=real_identity,full_name,first_name,last_name,email,nickname,bdate,sex,photo,photo_big,city,country&providers=openid,livejournal,google,yandex,facebook,twitter,vkontakte,mailru,odnoklassniki&hidden=&redirect_uri="
// ."{urlencode_THIS_URL}"
.$GLOBALS['httpsite'].$GLOBALS['www_ajax']."ulogin_xd.html"
."'></script>

<p class=br>&copy; использован виджет проекта <a href='http://ulogin.ru/'>http://ulogin.ru/</a><p><br>


</td></tr>

<tr><td colspan=2><hr></td></tr>

<tr><td valign=top><div style='font-size:20pt'>Login</div>
<p class='r'>Заводили аккаунт на этом сайте?
Это наименее желательный способ: владелец сайта (если вы знакомы) не может быть уверен, что вы - это вы.
</td><td>

<div id='openid'><div id='openidotvet'></div></div>
<form name='openidnew' onsubmit=\"majax('login.php',{action:'openid_logpas',rpage:mypage,mylog:this.mylog.value,mypas:this.mypas.value});return false;\"'>
<table cellspacing='10'>
<tr><td><b>логин:</b></td><td><input name='mylog' value='' type='text' size='20'></td>
<td rowspan='2'><input style='padding:10px;' type='submit' value='LOGIN'></td>
</tr>
<tr><td><b>пароль:</b></td><td><input name='mypas' type='password' size='20'></td></tr>
</table></form>

<p class='r'>
Нет регистрации совсем? Можно завести аккаунт на этом сайте, заполнив поля (особенно логин и пароль) в <span class='l r' onclick=\"majax('login.php',{action:'openid_form'})\">личной карточке</span>.

</td></tr></table>
",
),parse_e_conf($e)); // onclick='openid_ifr_post()'

	ini_set("display_errors","1");
	ini_set("display_startup_errors","1");
	ini_set('error_reporting', E_ALL);

if(isset($_GET['token'])) {
	$s=file_get_contents('http://ulogin.ru/token.php?token='.$_GET['token'].'&host='.$_SERVER['HTTP_HOST']);
	include $GLOBALS['include_sys']."json.php"; $j=jsonDecode($s);

//	die("<pre>".h(print_r($j,1))."</pre><hr>".h($s));

	$ll=login_do($j);
}

//$user = json_decode($s, true);
//$user['network'] ? соц. сеть, через которую авторизовался пользователь
//$user['identity'] ? уникальная строка определяющая конкретного пользователя соц. сети
//$user['first_name'] ? имя пользователя
//$user['last_name'] ? фамилия пользователя

// =============================================================================
// =============================================================================
// =============================================================================
// =============================================================================
// =============================================================================

return mper($conf['page'],array('THIS_URL'=>$thispage,'urlencode_THIS_URL'=>urlencode($thispage)));
}


function dier1($a) {
	$e=explode("\n",print_r($a,1));
	$o=array(); foreach($e as $l) { if(str_replace(array("\r","\n","\t"," ","(",")","Array"),'',$l)!='') $o[]=h($l); }
	return implode("\n",$o);
}

//===============================================================================
function login_do($j) { // global $img,$dom,$dob,$name,$info,$site,$mail;

	$birth=(isset($j['bdate'])?$j['bdate']:'');
	$birth=strtotime($birth); if($birth) $birth=date("Y-m-d",$birth); else $birth='';

	$mail=(!empty($j['email'])?$j['email']:'');
	if(!preg_match("/^[a-z0-9\-\_\.]+\@[a-z0-9\-\_\.]+\.[a-z]{2,10}$/si",$mail)) $mail='';

	if(empty($j['identity'])) idie("Identity fatal error!");

	if(!empty($j['real_identity'])) { $a=strlen($j['real_identity']); $b=strlen($j['identity']);
		if(substr($j['real_identity'],0,min($a,$b))!=substr($j['identity'],0,min($a,$b)))
		$openid=$j['real_identity']; else $openid=($a>$b?$j['real_identity']:$j['identity']);
	} else $openid=$j['identity'];

	$openid=trim($openid,'/'); if(!strstr($openid,':')) $openid="http://".$openid;

$uf='http://ulogin.ru/img/photo.png'; // вот это не надо говно нам
	$img=(!empty($j['photo_big'])&&$j['photo_big']!=$uf?$j['photo_big']:
!empty($j['photo'])&&$j['photo']!=$uf?$j['photo']:'');

	$site=(empty($j['web']['default'])?'':$j['web']['default']);

	// порт приписки вычислим
	preg_match("/^(.*?)([^\.]+\.[^\.]+)$/s",preg_replace("/www\./si",'',parse_url($openid,PHP_URL_HOST)),$m);
	$port=$m[2];
	//$liboname=trim(basename('/'.$m[1]),'.');

	// сразу поищем в нашей базе
	$p=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `openid`='".e($openid)."'","_1",0);

//	if($p['openid']=='http://samposebem.livejournal.com') $p['img']='';

	// и если надо - скачаем урл на фотку и имя
	if(empty($p['img']) /*&& empty($img)*/ && $port=='livejournal.com' // то мы знаем, где это говно брать
	&& ($l=fileget($openid."/data/foaf"))!==false) {
		if(preg_match("/<foaf\:name>(.+?)<\/foaf\:name>/si",$l,$m)) $j['nickname']=uw($m[1]);
		if(preg_match("/<foaf\:img\s+rdf\:resource=\"(.+?)\"/si",$l,$m)) $img=$m[1];
	}

	$realname=( //!empty($j['openid.sreg_fullname'])?$j['openid.sreg_fullname']:
		     !empty($j['full_name'])?$j['full_name']:
	 	      !empty($j['nickname'])?$j['nickname']:'');
	if($realname=='') {
		if(!empty($j['first_name'])&&!empty($j['last_name'])) $realname=$j['first_name'].' '.$j['last_name'];
		elseif(($realname=trim(parse_url($openid,PHP_URL_PATH),'/'))!=''){}
		else $realname=preg_replace("/^(.*)\.[^\.]+\.[^\.]+$/s","$1",preg_replace("/www\./si",'',parse_url($openid,PHP_URL_HOST)));
	}

	// [openid.sreg_gender]=&gt;M
	// logi('openid_david.txt',"\n\n\n".dier1(array_merge(array('openid'=>$info),$j)));

if(empty($img)) $img=$p['img'];

$x="\n\n<!-- $openid --><table style='width: 80%; border: 1px dashed rgb(255,0,0); padding: 20px; margin-left: 50px; margin-right: 50px; background-color: rgb(255,252,223);'><tr><td>"
.(empty($img)?'':"<img src='".h($img)."' align='right' hspace='20'>")
."<img src='http://".h($port)."/favicon.ico'><b>".h($realname).(empty($port)?'':" / ".h($port))."</b>"
."<br>info: <a href='".h($openid)."'>".h($openid)."</a>"
//.(empty($mail)?'':"\nmail: <a href='mailto:".h($mail)."'>".h($mail)."</a>")
.(empty($mail)?'':"<br>mail: <i>записан</i>")
.(empty($site)?'':"<br>site: <a href='".h($site)."'>".h($site)."</a>")
.(empty($dob)?'':"<br>birth: ".h($dob))
."</td></tr></table>";

//$d=ms("SELECT `Body` FROM `dnevnik_zapisi` WHERE `Date`='2011/11/01'","_l",0);
//if(1||!strstr($d,"<!-- $openid -->")) {
//	$d=str_replace("<a name='tut'></a>","<a name='tut'></a>".$x,$d);
//	msq_update('dnevnik_zapisi',array('Body'=>e($d)),"WHERE `Date`='2011/11/01'");
//}
/*
$js="this.parent.salert('<font color=green>success</font>',500);
this.parent.location.href='http://lleo.me/blog/2011/11/01.html?random='+Math.random(0,20000)+'#tut';
*/

$o='';


if($p===false) { // НЕТ, ТАКОГО ЕЩЕ У НАС НЕ БЫЛО
	$o.="<h1><font color=green>New Login</font></h1>";
	//Тогда нам надо оставить unic прежним, но вписать/заменить в нем поле `openid`, заодно недостающие
	$ara=array('openid'=>$openid,'realname'=>$realname,'birth'=>$birth,'img'=>$img);
	if(!empty($p['mail'])) $ara['mail']=$mail;
	msq_update($GLOBALS['db_unic'],arae($ara),"WHERE `id`='".$GLOBALS['unic']."'");
	$p=get_ISi($ara);
	$js='';
	// Процедура закончена, openid установили, unic не меняем.
} else { // В БАЗЕ ПРИСУТСТВУЕТ
	$o.="<h1><font color=green>in base: ".$p['id']."</font></h1>";
		$unic_tot=$p['id']; // По-любому меняем unic на старый, вот на этот.
		$up=$unic_tot.'-'.md5($unic_tot.$GLOBALS['newhash_user']);
	// добить недостающими
	$ara=array();
		if(empty($p['birth'])&&!empty($birth)) $ara['birth']=$birth;
//		if(empty($p['realname'])&&!empty($realname)) 
			$ara['realname']=$realname;
		if(empty($p['mail'])&&!empty($mail)) $ara['mail']=$mail;
		if(empty($p['img'])&&!empty($img)) $ara['img']=$img;
	if(sizeof($ara)) msq_update($GLOBALS['db_unic'],arae($ara),"WHERE `id`='".$p['id']."'");
	setcoo($GLOBALS['uc'],$up);
	$p=get_ISi($p);
	$js="
this.parent.up='$up';
this.parent.fc_save('up',this.parent.up);
this.parent.f5_save('up',this.parent.up);
this.parent.c_save(this.parent.uc,this.parent.up,1);
this.parent.salert(\"Login restore: ".$p['imgicourl']." (#".$unic_tot.")\",1000);
";
}

if($GLOBALS['msqe']) { logi('NEW_login_msqe.txt',"\n\n".$GLOBALS['msqe']); die($GLOBALS['msqe']); }

die("<script>
this.parent.realname=\"".njsn($p['imgicourl'])."\";
this.parent.zabilc('myunic',this.parent.realname);"
//.($GLOBALS['admin']?'':"setTimeout(\"this.parent.clean('logz')\",1000);")
."setTimeout(\"this.parent.clean('logz')\",1000);"
.$js."</script>
".$o."<font color=green>success</font><p>$x<p><pre>".dier1($j)."</pre>");
}


//function loadlj_img($openid,$port,$img) {
/*
	if(empty($img) && $port=='livejournal.com' && ($l=fileget("http://".rpath($name).".livejournal.com/data/foaf"))!==false) {
	if(preg_match("/<foaf\:name>(.+?)<\/foaf\:name>/si",$l,$m)) $name=$m[1];
	if(preg_match("/<foaf\:img\s+rdf\:resource=\".+?\"/si",$l,$m)) $img=$m[1];
	}
*/
//}


// Notice: A session had already been started - ignoring session_start() in /var/www/blog/site_module/LOGIN.php on line 121

//================================================
/*
function loginslil($unic,$unic_tot) { if($unic==$unic_tot) return; // если один и тот же
	$e=$GLOBALS['msqe'];
	$ara=array('unic'=>e($unic_tot)); $a="WHERE `unic`='".e($unic)."'";
	msq_update('dnevnik_comm',$ara,$a);
	msq_update('dnevnik_plusiki',$ara,$a);
	msq_update('dnevnik_posetil',$ara,$a);
	msq_update('golosovanie_golosa',$ara,$a);
	ms("DELETE FROM ".$GLOBALS['db_unic']." WHERE `id`='$unic'","_1",0); // и удаляем
	$GLOBALS['msqe']=$e;

//	$po=ms("SELECT * FROM ".$GLOBALS['db_unic']." WHERE `id`='".e($unic)."'","_1",0);
//	$ara=array(); // сохраним все старое добро
//	if($p['mail']=='' and $po['mail']!='') $ara['mail']=$po['mail'];
//	if($p['site']=='' and $po['site']!='') $ara['site']=$po['site'];
//	if($p['realname']=='' and $po['realname']!='') $ara['realname']=$po['realname'];
//	if($p['birth']=='' and $po['birth']!='') $ara['birth']=$po['birth'];
//	if($p['lju']=='' and $po['lju']!='') $ara['lju']=$po['lju'];
//	if(sizeof($ara)) msq_update($GLOBALS['db_unic'],$ara,"WHERE `id`='$new'");
}
*/
?>