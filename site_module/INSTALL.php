<?php // INSTALL

// if(!function_exists('h')) die("Error 404");

/*
Необходимы для старта:
	site_module/install
	- index.php
	- ajax/module.php
	include_sys/jrhttprequest?.php
	-? include_sys/_autorize.php
	- config.php
	- template/blank.html

сервер-матка:
	js/main.js
	design/JsHttpRequest.js
	css/blog.css
*/

// AD(); // только для админа

// $GLOBALS['admin']=1;

// idie('#'.$GLOBALS['admin']);

//----------------------------


$GLOBALS['selectjs']="
i_all=function(){
var p=idd('packs').getElementsByTagName('div');
for(var k=0,i=0;i<p.length;i++){ var dc=p[i].className.split(' ');
	if(typeof dc[1] != 'undefined' && dc[1].substring(0,2)=='ii'){ if(k==0) k=dc[1]=='ii1'?'ii0':'ii1'; i_d(p[i],k); }
	}
};

i_d=function(e,k){ var dc=e.className.split(' ');
	if(typeof k == 'undefined') dc[1]=dc[1]=='ii1'?'ii0':'ii1'; else dc[1]=k;
	e.className=dc.join(' ');
	var p=getElementsByClass(dc[0]);
	for(var i=1;i<p.length;i++) { var c=p[i].className.split(' ');
		if(dc[1]=='ii0') c[2]='ii0';
		else { if(c.length>2) { delete c[2]; c.length=2; } }
		p[i].className=c.join(' ');
	}
};

i_f=function(e){ var c=e.className.split(' ');
	if(c.length>2) { delete c[2]; c.length=2; } else c[2]='ii0';
	e.className=c.join(' ');
};
";
//--------------------------------------------------------------------------------

if(sizeof($_POST)!=0 && !empty($_POST['post_act'])) { $a=$_POST['post_act'];

	// безопасность: проверка ключа инсталляции
	$key=file_get_contents($GLOBALS['filehost'].'binoniq/instlog/install_key.php');
	$key=preg_replace("/^.+?\"([0-9a-z]{40})\".+?$/si","$1",$key);
	if($key!=$_POST['key']) die("ohelpc('install2','post',\"error key\");");

if($a=='check_pack') {
	$r=unserialize(urldecode($_POST['ara']));

	$s=''; $otstup=''; $lastdir='';

	foreach($r as $l) { list($file,$val)=explode(' ',$l,2);
	$f=explode(':',$file,3);
		if(sizeof($f)==1) { // это файл
			$ff=$GLOBALS['filehost'].$file;
			$filename=basename($file);
                        $dirname=dirname($file).'/';
			$otstup=str_repeat(' ',substr_count($file,'/')*10);

			if(!is_file($ff)) $ac='iia'; // add
			else {
				list($ftime,$fkey)=explode(' ',$val,2);
//				if($ftime==filemtime($ff)) continue; // все с файлом нормалек
				//else 
				$ac='iiu'; // upgrade
			}

			if($dirname!=$lastdir) { $s.="<div class=\"$dirname ii1\" onclick='i_d(this)'>".$dirname."</div>"; $lastdir=$dirname; }

			$s.="<div>".str_repeat(' ',strlen($dirname))
			."<span id='e_$file' class=\"$dirname $ac\" onclick='i_f(this)'>".$filename."</span></div>";

		} elseif($f[0]=='config') { // это конфиг
		} elseif($f[0]=='lang') { // это язык
		}
	}

	die($GLOBALS['selectjs']."ohelpc('install2','post',\"".njsn("<tt>$s</tt>")."\");");
}

// idie('1');
	$a=$_POST;
//	idie($_POST['ara']);
	if(isset($a['ara'])) $a['ara']=unserialize(urldecode($a['ara']));
	if(count($_FILES)>0) {
		foreach($_FILES as $n=>$FILE) if(is_uploaded_file($FILE["tmp_name"])){
		$a["file: `$n`"]=$FILE;
		}
		// idie('Files: '.count($_FILES));
	}
	dier($a);
}


function AD2() { if(!isset($_COOKIE["adm2"]) || $_COOKIE["adm2"]!=$GLOBALS['admin_hash1']) { idie('Admin only!'); } }

function INSTALL_ajax() { $a=RE('a');
//=========================================================================
if($a=='login') { // залогиниться
	if($GLOBALS['admin_hash1']==broident2(c(RE('v')).$GLOBALS['koldunstvo1'])) return "
c_save('adm2','".$GLOBALS['admin_hash1']."');
window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
	sleep(5); return "salert('Wrong password',2000)";
}

if($a=='setup_password') { // инсталлировать пароль
// если установлен пароль и он неисправный, проверить залогиненность и послать нахуй, если чо не так
if(isset($GLOBALS['admin_hash1']) && preg_match("/^[0-9a-z]{40}$/",$admin_hash1)) AD2();
	if(preg_match("/^[0-9a-z]{40}$/",$GLOBALS['admin_hash1'])) return "salert('Error 666')";
	if(!isset($GLOBALS['koldunstvo1'])) config_change('koldunstvo1',hash_generate()); // если не было - создать
	$pass=RE('v');
	if($pass!=c($pass)) return "salert('Don`t use space in 1 or last letter!',4000);";
	$pass=c($pass);	if($pass=='') return "salert('Where the password?!',2000);";
	config_change('admin_hash1',broident2($pass.$GLOBALS['koldunstvo1']));
	return "salert('Password: $pass',2000); window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
}

AD2();
//=========================================================================

// dier($_REQUEST,1);

if($a=='testmod') { // проверка модуля
	$m=RE('module');
		$mod=$GLOBALS['host_module']."install/".$m; include_once($mod);
		$r=installmod_init();
		if($r!=strtr($r,"\n<>",'---')) $s="
			clean('module__$m');
			zabil('mesto_otvet',vzyal('mesto_otvet')+\"<hr color='red'>".njsn($r)."\");
		";
		else {
			$s='';
			if($r!==false) $s.= "zabil('module__$m',\"<input type=button style='font-size:8px;' value='$r' onclick=\\\"dodo('$m',0,0,0)\\\">\");";
			else $s.="clean('module__$m');";
		}
	$s.="check_mod_do();";
	return $s;
}

if($a=='do') { // запуск модуля
	global $skip,$allwork,$time,$o,$delknopka,$script; $o=$delknopka=$script='';
	$time=RE0('time'); $skip=RE0('skip'); $allwork=RE0('allwork');
	$m=RE('module'); $mod=$GLOBALS['host_module']."install/".$m; include_once($mod);
	if(installmod_init()===false) return "clean('module__$m'); salert('not nessesary',2000);";
	if(!$allwork) { $allwork=(function_exists('installmod_allwork')?installmod_allwork():0); }
        $r=installmod_do();
                $script=(empty($script)?'':$script);
			if(intval($r)==0 and $r!==0) $o=$r;
	                $o=($o==''?'':"zabil('mesto_otvet',\"".njs($o)."\");");
        	        $delknopka=(isset($delknopka)?"clean('module__$m');":'');
                if($r===0) return $script."clean('percent');".$o.$delknopka;
                if(intval($r)==0) return $script."clean('percent');".$o.$delknopka;
                return $script.$o."
var z=(idd('percent')?0:1);
helps('percent',\"<fieldset><legend>$m &nbsp; &nbsp; \"+parseInt((100/$allwork)*$skip)+\"% <span class='timet'>
if(z) posdiv('percent',-1,-1);
dodo('$m',$allwork,$time,$r);";
}


//------------ для формы editfile ------------------
if($a=='edit_file'){ $file=RE('file'); return "save_and_close=function(){save_no_close();clean('editor')};
save_no_close=function(){ if(idd('edit_text').value==idd('edit_text').defaultValue) return salert('save_not_need',500);
majax('module.php',{mod:'INSTALL',a:'save_file',file:\"".njs($file)."\",text:idd('edit_text').value});
idd('edit_text').defaultValue=idd('edit_text').value;
};

ohelpc('editor','Edit: ".h($file)."',\"<table><tr><td>"
."<textarea style='width:\"+(getWinW()-100)+\"px;height:\"+(getWinH()-100)+\"px;' id='edit_text'>"
.h(njsn(file_get_contents($file)))."</textarea>"
."<br><input title='ctrl+Enter' type='button' value='Save+exit' onclick='save_and_close()'> <input title='shift+Enter' type='button' value='Save' onclick='save_no_close()'>"
."</td></tr></table>\");
idd('edit_text').focus();

setkey('esc','',function(e){ if(idd('edit_text').value==idd('edit_text').defaultValue || confirm('exit no save?')) clean('editor'); },false);
setkey('enter','ctrl',save_and_close,false);
setkey('enter','shift',save_no_close,false);
setkey('tab','shift',function(){ti('edit_text','\\t{select}')},false);
";
}
if($a=='save_file'){ fileput(RE('file'),RE('text')); return "salert('saved',500)"; }

//------------ login ------------------
if($a=='logout') { // разлогиниться
	return "c_save('adm2',''); c_save('adm',''); window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
}

if($a=='passchange') { // поменять пароль (спросить старый сперва)
	return "
helpc('oldpass',\"<fieldset><legend>Change Password</legend>".njsn(
"Old password: <input type='text' size='15' id='old_pass' value=''>"
."<br>New password: <input type='text' size='15' id='new_pass' value=''>"
."<br><input type='button' value='Setup' onclick=\"majax('module.php',{mod:'INSTALL',a:'passchange_',old:idd('old_pass').value,new:idd('new_pass').value})\">"
)."</fieldset>\");
";
}

if($a=='passchange_') { // поменять пароль (спросить старый сперва)
	if($GLOBALS['admin_hash1']!=broident2(c(RE('old')).$GLOBALS['koldunstvo1'])) {
		 sleep(1); return "clean('oldpass'); salert('Wrond old password!',4000);";
	}
	$pass=c(RE('new'));
	config_change('admin_hash1',broident2($pass.$GLOBALS['koldunstvo1']));
	return "salert('Password: $pass',2000); window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
}


//------------ install ------------------
function fileget_save($file,$s='') {
	$a=explode('/',$file); $file=array_pop($a); $dir=implode('/',$a)."/";
	if(($o=file_get_contents($dir.$file))===false || $o==''&&$s!='') {
		testdir($dir); fileput($dir.$file,$s);
		if(file_get_contents($dir.$file)!=$s) idie("Cann't save: ".h($dir.$file)."<p>Check permissions.");
		return $s;
	} return $o;
}


$maj="majax('module.php',{mod:'INSTALL',a:";
$dir=$GLOBALS['filehost'].'binoniq/instlog/';

if($a=='install') { // инсталляция
	$serv=fileget_save($dir."servers.txt","http://lleo.aha.ru/blog Beta
http://lleo.aha.ru/dnevnik Stable
http://lleo.aha.ru Super Stable
http://binoniq.net Server Stable");

	$select_serv=fileget_save($dir."my_server.txt","http://lleo.aha.ru/blog\n+basic");
	$select_serv=explode("\n",$select_serv);

	$o=array(); foreach(explode("\n",$serv) as $l) { $l=trim($l,"\n\r\t "); if($l=='') continue;
		list($ser,$ver)=explode(' ',$l,2); $o[$ser]=$ver.': '.$ser;
	}

	$s="server: ".selecto('servs',$select_serv[0],$o,"onchange=\"zabil('epacks','');"
."mijax(this.value+'/ajax/midule.php',{mod:'INSTALL',a:'install_get_packs'});"
."\" id")."
<br><input type='button' value='Check Update' onclick='servselect(this)'>
<input type='button' value='Clean *.old' onclick=\"$maj'install_clean',s:idd('servs').value})\">
<input type='button' value='Back' onclick=\"$maj'install_back',s:idd('servs').value})\">
<input type='button' value='TEST' onclick=\"$maj'install_test',s:idd('servs').value})\">
<div id='epacks' style='margin: 20px; border: 1px dotted #ccc'>";

unset($select_serv[0]);
foreach($select_serv as $l) { $w=substr($l,1);
	$s.="<div><input class='cb' name=\"$w\" type='checkbox'".($l[0]=='+'?' checked':'').">$w</div>";
}
$s.="</div>";
	$s.="<div id='mypacks' style='font-size: 11px; margin: 20px; border: 1px dotted red'>".get_my_pack($dir)."</div>";

	return "
servselect=function(e){ var s='',e=getElementsByClass('cb');
	for(var i=0;i<e.length;i++) s+=' '+(e[i].checked?'+':'-')+e[i].name;
	if(s=='') { alert('Select packet'); return; }
	$maj'install_check',s:idd('servs').value,pack:s})
};

ohelpc('install','Select server',\"".njsn($s)."\");";
//	return "zabil('mesto_otvet',\"<div class=r>".njs(nl2br(print_r($r,1)))."</div>\");";
}

if($a=='install_edit_pack') { // форма редактирования пакета или создания нового (name='')
	$name=RE('name'); $s=''; $otstup=''; $lastdir='';

	$p=array(); if($name!='' && ($r=file($dir."instpack/".$name.".pack"))!==false) {
		foreach($r as $l) { $m=explode(' ',$l); $p[$m[0]]=array($m[1],$m[2]); } // [0] => template/adminpanel.htm 1303587256 d866bd70d3d53450fd3b82243d32fe36
	}

	foreach(get_dfiles() as $l) { list($file,$ftime,$fkey)=explode(' ',$l,3);
		$ff=$GLOBALS['filehost'].$file;
		$filename=basename($file);
		$dirname=dirname($file).'/';
		$otstup=str_repeat(' ',substr_count($file,'/')*10);
		if($name=='') $ac='iia'; // add
		else $ac=(isset($p[$file])?'iia':'iia ii0');
		
		if($dirname!=$lastdir) { $s.="<div class=\"$dirname ii1\" onclick='i_d(this)'>".$dirname."</div>"; $lastdir=$dirname; }
		$s.="<div>".str_repeat(' ',strlen($dirname))
		."<span id='e_$file' class=\"$dirname $ac\" onclick='i_f(this)'>".$filename."</span></div>";
	}

$subm="<input type='button' value='Save' onclick='packsave()'>"
."&nbsp; &nbsp; <span class='ll' onclick=\"i_all()\">select</span>"
."&nbsp; &nbsp; <span class='ll' onclick=\"packdel()\">delete</span>";

	return $GLOBALS['selectjs']."

packdel=function(){ if(confirm('Delete pack `".$name.".pack`?')) majax('module.php',{mod:'INSTALL',a:'install_pack_del',name:idd('newpack_name').value}); };
packsave=function(){ var p=idd('packs').getElementsByTagName('span'); var s='';
for(var i=0;i<p.length;i++){ if(p[i].className.split(' ').length<=2) s=s+'\\n'+p[i].id.substring(2); }
majax('module.php',{mod:'INSTALL',a:'install_pack_save',s:s,name:idd('newpack_name').value});
};

ohelpc('pack','Edit pack',\"".njsn(
($name==''?"<b>name: </b><input type='text' value='' size='10' maxlength='20' id='newpack_name'>":
"<input type='hidden' value='$name' id='newpack_name'>")
.$subm
."<div id='packs'><tt>$s</tt></div>$subm")."\");";
}

if($a=='install_pack_del') { // удаление пакета
	$name=RE('name'); unlink($dir."instpack/".$name.".pack");
	return "clean('pack'); zabil('mypacks',\"".njsn(get_my_pack($dir))."\"); salert('Pack <b>$name</b> deleted!',1000);";
}

if($a=='install_pack_save') { // приемка создания нового пакета
// majax('module.php',{mod:'INSTALL',a:'install_pack_save',s:s,name:idd('newpack_name').value});
$name=preg_replace("/[^0-9a-z\_\-\.]+/s",'',strtolower(RE('name')));
if(empty($name)) return "idd('newpack_name').value='$name'; idie('Name error! Only: 0-9a-z_-.');";
$pp=explode("\n",trim(RE('s'),"\n")); $p=array(); foreach($pp as $l) $p[$l]=1;
$s=''; foreach(get_dfiles() as $l) { list($file,$ftime,$fkey)=explode(' ',$l,3); if(isset($p[$file])) $s.=$l."\n"; }
if($s=='') return "salert('Empty pack!',1000);";

//idie("$name<p>$s");

file_put_contents($dir."instpack/".$name.".pack",$s);
return "clean('pack');
zabil('mypacks',\"".njsn(get_my_pack($dir))."\");
salert('Pack <b>$name</b> saved!',1000);
";
}

// прислать по-бырому список доступных пакетов на этой станции
if($a=='install_get_packs') { // выслать список пакетов
	$s=''; foreach(glob($dir."instpack/*.pack") as $l) { $l=basename($l,'.pack');
		$s.="<div><input class='cb' name=\"$l\" type='checkbox'>$l</div>";
	} return "zabil('epacks',\"".njsn($s)."\")";
}

// принять запрос на инсталляцию пакетов
if($a=='install_check') { // инсталляция
	$ser=RE('s'); $pack=RE('pack');
	$e=explode(' ',$pack); $w=array(); foreach($e as $l){ if($l[0]=='+') $w[]=substr($l,1); }
	fileput($dir."my_server.txt",$ser.strtr($pack,' ',"\n"));
	$key=sha1(hash_generate()); // сформировать ключ
	fileput($dir."install_key.php",'<?php die("Error 404"); $key="'.$key.'"; ?>');
	return "
majax('".$ser."/ajax/module.php',{mod:'INSTALL',a:'install_far_check',url:'".$GLOBALS['httphost']."',pack:'".implode(' ',$w)."',key:'$key'})";
}

// обработка конфига
function getconf($l){ $r=array();
	$a=file($l); unset($a[0]); unset($a[sizeof($a)]);
	foreach($a as $l) { $l=trim($l);
		if($l=='' || preg_match("/^\s*(#|\/\/)/s",$l)) continue;
		$per=preg_replace("/^\s*".'\$'."([a-z0-9\_]+).*?$/si","$1",$l); if($per==$l) continue;
		$r[]="config:$per $l";
	}
	return $r;
}

// обработка языка
function getlang($f){ $la=$GLOBALS['filehost'].'binoniq/lang/'; $nla=strlen($la); if(substr($f,0,$nla)!=$la) return array();
		$la=substr($f,$nla); $la=substr($la,0,strlen($la)-5);
		$r=array(); foreach(file($f) as $l) { $l=trim($l); if(!strstr($l,"\t")) continue;
		list($per,$val)=explode("\t",$l,2);
		$r[]="lang:".$la.":$per $l";
	}
	return $r;
}

/*
// скан по всем файлам в низжелащей папке
function scanvdir($dir,$mask='*',$r='') { if($r=='') $r=array();
	$a=glob(rtrim($dir,'/').'/'.$mask);
	foreach($a as $l) { if(is_dir($l)) $r=scanvdir($l,$mask,$r); else $r[]='#'.$l; }
	return $r;
}
*/

if($a=='install_far_check') { // отправить запрос на проверку
	$r=array();
	foreach(explode(' ',trim(RE('pack'))) as $l) $r=getpack($l,$r);
	$o=$r;
	foreach($o as $n=>$l) { list($l,)=explode(' ',$l,2); $url=$GLOBALS['filehost'].$l;
		if($l=='config.php.tmpl') { $r=array_merge(getconf($url),$r); } // обрабюотать конфиг
		if(getras($l)=='lang') { $r=array_merge(getlang($url),$r); unset($r[$n]); } // обрабюотать язык
	}
	return POST_file('',RE('url')."install",array('post_act'=>'check_pack','key'=>RE('key'),'ara'=>serialize($r)));
}

/*
//	idie(nl2br("size: ".sizeof($r)."\n\n".implode("\n",$r)));
//	$s=print_r(),1); // ''; foreach(glob($inst) as $l) $s.=$l;
//	$select_serv=fileget_save($dir."my_server.txt","http://lleo.aha.ru/blog");
// fileput($dir.$file,$s);	

	return "ohelpc('install','Select packets',\"".njsn($s)."\");";

	$key=RE('key');
	$url=rtrim(RE('url'),'/')."/install";
	idie("Ja: ".$GLOBALS['httphost']." key=$key<br>url=$url");
	$ser=RE('s'); fileput($dir."my_server.txt",$ser);
	$key=hash_generate();
	fileput($dir."install_key.php",'<?php die("Error 404"); $key="'.$key.'"; ?>');
	return "majax('".$ser."/ajax/module.php',{mod:'INSTALL',a:'check',url:'".$httphost."',key:'".$key."'});";
*/


if($a=='install_test') { // инсталляция

//	POST_file($filepath,$url,$fields,$port=80,$scheme='http');
	$t=POST_file(array(
$GLOBALS['filehost']."re.png",
$GLOBALS['filehost']."re.php",
$GLOBALS['filehost']."install.zip",
$GLOBALS['filehost']."gg.zip"
),'http://lleo.aha.ru/blog/install',array('post_act'=>'do','aaa'=>'123','key'=>'rrr'));
//	sendFile('http://lleo.aha.ru/blog/install',,$path, $filePath, $fileName, $fileField, $fields = );

	idie($t);
// eeeeeeeeeeeee
}


}
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
function sr($s){ return "<font color=red>$s</font>"; }
function sg($s){ return "<font color=green>$s</font>"; }

function getras($s){ $r=explode('.',$s); if(sizeof($r)==1) return ''; return array_pop($r); }

// высчитать кс файла со всеми вычетами и проверками
function calcfile_md5($l,$ras) {
	$txt=file_get_contents($l);
	if($ras=='php') $txt=preg_replace("/[\n\r]+\/\*\s*lleo\s*\*\/[^\n\r]+/si","\n",$txt);
	return md5($txt);
}

// взять данные по пакету (если basic - то просканировать)
function getpack($pack,$e) {
	if($pack=='basic') $r=get_dfiles(); // подсчитать суммы
	else { $r=array(); $s=file($GLOBALS['filehost']."binoniq/instlog/instpack/".$pack.".pack");
		foreach($s as $l) { list($name,$time,$md5)=explode(' ',trim($l));
			$l=$GLOBALS['filehost'].$name; $tim=filemtime($l);
			if($time!=$tim) $md5=calcfile_md5($l,getras($l));
			$r[]="$name $tim $md5";
		}
	}
	$s=implode("\n",$r);
	$dir=$GLOBALS['filehost'].'binoniq/instlog/instpack/'; testdir($dir); // проверить папку для кэшиков
	fileput($dir.$pack.".pack",$s); // сохранить список

	foreach($r as $n=>$l) { if(in_array($l,$e)) unset($r[$n]); } // выкинуть дубли
	return array_merge($e,$r);
}

function get_dfiles() { global $stop,$veto_dir,$md5mas,$filehostn,$filehost,$allmd5change;
	$stop=1000;
	if(!isset($filehostn)) $filehostn=strlen($filehost);

	$dir=$GLOBALS['filehost']."binoniq/instlog/"; testdir($dir);

	$md5mas=array(); if(($s=file_get_contents($dir."all_md5.tmp"))===false) $allmd5change=1; // понадобится запись
	else { $allmd5change=0; $md5mas=unserialize($s); }

	if(!isset($basic)) {
		$s=file($dir."system_dir.txt"); // unset($s[0]); unset($s[sizeof($s)]);
		$basic=array(); foreach($s as $l) { $l=trim($l); if($l!='' && substr($l,0,1)!='#') $basic[$l]=1; }
	}

	$r=array(); foreach($basic as $l=>$c) $r=array_merge($r,get_dfiles2($l));

	if($allmd5change) fileput($dir."all_md5.tmp",serialize($md5mas)); // понадобилась запись
	return $r;
}


function get_dfiles2($files) { global $stop,$veto_dir,$md5mas,$filehostn,$filehost,$allmd5change;
	if(!--$stop) die('stop error');
	$r=array();
	$a=$filehost.$files; if(is_file($a)) $a=array($a); else $a=glob($a."/*");
	
	foreach($a as $n=>$l) { if(is_dir($l)) continue;
		$ras=getras($l);

		if(!in_array($l,$veto_dir) and $ras!='old' and $ras!='off' and !preg_match("/($|\/)pre\//s",$name) ) {
			$time=filemtime($l);
			$name=c(substr($l,$filehostn));
			if(!isset($md5mas[$name]) || $md5mas[$name][0]!=$time) { $md5=calcfile_md5($l,$ras);
				$md5mas[$name]=array($time,$md5); $allmd5change=1; // и понадобится перезапись
			} else $md5=$md5mas[$name][1];

			$r[]="$name $time $md5";
		}
	        unset($a[$n]); 
	}

        foreach($a as $l) { $name=c(substr($l,$filehostn));
		if(!isset($veto_dir[$name])) $r=array_merge($r,get_dfiles2($name));
	}

        return $r;
}
//=========================================================================

// РАБОТА С КОНФИГОМ

// добавить в конфиг
function config_add($name,$value){ if(($s=config_get())===false) return $s;
	return config_put(preg_replace("/\n\s*\?>\s*$/s","\n\$".$name."=\"".$value."\"; // added ".date("Y-m-d")."\n?>\n",$s));
}
// удалить из конфига
function config_del($name){ if(($s=config_get())===false) return $s;
	return config_put(preg_replace("/\n(\s*[\$]".$name."\s*=[^\n]+)/s","\n// deleted ".date("Y-m-d").": $1",$s));
}
// изменить в конфиге (если не было - то добавить)
function config_change($name,$value){ if(($s=config_get())===false) return $s;
	if(!isset($GLOBALS[$name])) return config_add($name,$value);
	return config_put(preg_replace("/([\n\r]+\s*[\$]".$name."\s*=\s*)[\'\"][^\'\"]*[\'\"]\s*;([^\n]*)/si","$1\"".$value."\";$2",$s));
}
function config_get(){ $f=config_name(); if(($s=file_get_contents($f))===false) return false; return $s; }
function config_put($s){ $f=config_name(); fileput($f,$s); }
function config_name(){ global $ajax,$filehost;
	if(isset($filehost)) return $filehost."config.php";
	if($ajax) return "../config.php";
	return "config.php";
}

// сгенерировать hash-строку
function rando($x,$y){ $s=''; $k=10;
	while((--$k)&&!strlen($s)){ if(($g=fopen("/dev/random","rb"))===false) break; $s=fgets($g); fclose($g); }
	if(!strlen($s)) { // /dev/random не сработал, вернуть традиционным образом
		list($t,)=explode(" ",microtime()); mt_srand($t+mt_rand()); $a=mt_rand(0,$y)+$t;
	} else { for($f=1,$a=$j=0;$j<min(strlen($s),3);$j++,$f*=256) $a+=ord($s[$j])*$f; }
	return $x+$a%($y-$x);
}

function hash_generate(){
	$A='ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz01234567890';
	for($s='',$i=0,$n=strlen($A);$i<128;$i++) $s.=$A[rando(0,$n)]; return $s; //convert_uuencode($s);
}

// РАБОТА С ТАБЛИЦАМИ

// изменить поле в таблице
function msq_change_pole($table,$pole,$s){ if(msq_pole($table,$pole)!==false) msq("ALTER TABLE `".$table."` CHANGE `$pole` `$pole` $s"); }
// добавить поле таблицы
function msq_add_pole($table,$pole,$s){ if(msq_pole($table,$pole)===false) msq("ALTER TABLE `".$table."` ADD `".$pole."` ".$s." NOT NULL"); }
// удалить поле из таблицы
function msq_del_pole($table,$pole){ if(msq_pole($table,$pole)!==false) msq("ALTER TABLE `".$table."` DROP `".$pole."`"); }
// добавить ИНДЕКС в таблицу
function msq_add_index($table,$pole,$s){ if(msq_pole($table,$pole)!==false && !msq_index($table,$pole)) msq("ALTER TABLE `".$table."` ADD INDEX `".$pole."` ".$s); }
// удалить ИНДЕКС из таблицы
function msq_del_index($table,$pole){ if(msq_index($table,$pole)) msq("ALTER TABLE `".$table."` DROP INDEX `".$pole."`"); }
// создать таблицу
function msq_add_table($s){ msq($s); }
// удалить таблицу
function msq_del_table($table){ if(msq_table($table)) msq("DROP TABLE `".$table."`"); }

//======================================================================================
// похвастаться успешной установкой
function admin_pohvast() { return "<center><div id=soobshi><input type=button value='Похвастаться успешной установкой' onclick=\"document.getElementById('soobshi').innerHTML = '<img src=http://lleo.aha.ru/blog/stat?link={httphost}>';\"></div></center>"; }

//======================================================================================
// логины админа
function admin_login() { global $mypage,$koldunstvo,$admin,$admin_hash1; $s='';

// неверный или пустой пароль
if(!preg_match("/^[0-9a-z]{40}$/",$admin_hash1)) return "<font color='red'>Admin's password is not setup! Setup it now! Please, try to be first :)</font>
<p>Create admin password: <input type='text' name='admin_pass' size='15' id='admin_pass' onchange=\"idd('submitpass').click()\" value=''> <input id='submitpass' type='button' value='Setup' onclick=\"majax('module.php',{mod:'INSTALL',a:'setup_password',v:idd('admin_pass').value})\">";

// админ - разлогиниться (или поменять пароль)
if(
// $admin
isset($_COOKIE["adm2"]) && $_COOKIE["adm2"]==$GLOBALS['admin_hash1']
) return "<input type='submit' value='Logout' onclick=\"majax('module.php',{mod:'INSTALL',a:'logout'})\">
<p><input class='br' type='submit' value='Change Admin Password' onclick=\"majax('module.php',{mod:'INSTALL',a:'passchange'})\">";

// не залогинен

return "Admin password: <input type='text' size='15' name='admin_pass' id='admin_pass' onchange=\"idd('submitpass').click()\"> <input id='submitpass' type='button' value='Login' onclick=\"majax('module.php',{mod:'INSTALL',a:'login',v:idd('admin_pass').value})\">";
}

//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
function INSTALL($e) { $s=$im='';

if($GLOBALS['admin']) {

STYLES("mod","
.mod {font-size:11px;}

.iid,.iia,.iiu,.ii1,.ii0 { cursor:pointer; }

.ii0 { text-decoration: line-through; color: #999999; background-color: #dddddd; }

.iid { color: red; }
.iia { color: green; }
.iiu { color: magenta; }

"); // .ii0,.ii1 { font-weight: bold; }

        $upgrade=glob($GLOBALS['host_module']."install/*.php");
        foreach($upgrade as $l) { $m=array_pop(explode('/',$l));
		$im.="'$m',";
		$s.="<div class='mod' id='module__$m'>".$m."</div>";
	}

SCRIPTS("mod","
var install_modules_n=0;
function check_mod_do() { if(typeof install_modules[install_modules_n] == 'undefined') { install_modules_n=0; return; }
	var m=install_modules[install_modules_n++];
	zabil('module__'+m,'<img src='+www_design+'img/ajax.gif>'+vzyal('module__'+m));
	majax('module.php',{mod:'INSTALL',a:'testmod',module:m});
}
var install_modules=[".trim($im,',')."];

var timestart;
function dodo(m,allwork,time,skip) {
	if(skip) {
		var timenow = new Date();
		var t=timenow.getTime()-timestart.getTime();
		var e=parseInt((t/skip)*allwork)-t;
		zabilc('timet',' &nbsp; &nbsp; &nbsp; осталось: '+pr_time(e)+' сек');
	} else { timestart = new Date(); }
	majax('module.php',{mod:'INSTALL',a:'do',module:m,allwork:allwork,time:time,skip:skip});
}

function pr_time(t) { var N=new Date(); N.setTime(t); var s=pr00(N.getUTCSeconds());
	if(N.getUTCMinutes()) s=pr00(N.getUTCMinutes())+':'+s;
	if(N.getUTCHours()) s=pr00(N.getUTCHours())+':'+s;
	return s;
} function pr00(n){return ((''+n).length<2?'0'+n:n)}


page_onstart.push('check_mod_do()');

");

// $s.="<p><input type='button' value='check' onclick='check_mod_do()'>";
}


SCRIPTS("page_onstart.push('hotkey_reset=function(){}; hotkey=[];');"); // запретить хоткеи

return "<table width=100% style='border: 1px dotted red'>
<tr valign=top>
	<td>
		<p><input type='button' value='INSTALL' onclick=\"majax('module.php',{mod:'INSTALL',a:'install'})\"><p>
		<div id='mesto_module'>$s</div>
	</td>
	<td width='100%'><div id='mesto_otvet'>".admin_login()."</div></td>
</tr></table>";

}


function testdir($s) { $a=explode('/',rtrim($s,'/')); $s=''; for($i=0;$i<sizeof($a);$i++) { $s.='/'.$a[$i]; if(!is_dir($s)) dirput($s); } }


//////////

// turn sendFile($url['host'],$url['port'],$to,$b,basename($b), "oo", $ara);


//==================================================================================================
// процедура передачи данных и файлов через POST-запрос по старинке без всяких там уебищных CURL-библиотек
// $filePath - полное имя (с путем) файла для передачи или массив имен файлов для передачи (если файлы не передаются - '')
// $urla - адрес запроса, напр. http://lleo.aha.ru/blog/install
// $ara - массив переменных POST, напр: array('action'=>'do','key'=>'1','user'=>123)
// возвравщает ответ сервера или, если ошибка, строку, начинающуюся с 'ERROR:'
function POST_file($filePath,$urla,$ara,$port=80,$scheme='http',$charset='Windows-1251') {
	if(gettype($filePath)!='array') $filePath=array($filePath);
	$url=array_merge(array('scheme'=>$scheme,'port'=>$port),parse_url($urla));
	$bu="---------------------".substr(md5($filePath.rand(0,32000)),0,10); $r="\r\n"; $ft=$r.'--'.$bu.'--'.$r;

	// данные
	$dat=''; if(count($ara)) foreach($ara as $n=>$v) $dat.='--'.$bu.$r.'Content-Disposition: form-data; name="'.$n.'"'.$r.$r.urlencode($v).$r;

	$len=strlen($dat); // общая длина

	$files=array(); $k=0; foreach($filePath as $l) { if(empty($l)) continue;
		if(!is_file($l)) return "ERROR: file not found '$l'";
		$fh='--'.$bu.$r
		.'Content-Disposition: form-data; name="file'.(++$k).'"; filename="'.urlencode(basename($l)).'"'.$r
		.'Content-Type: '.$charset.$r
		.$r;

		$len+=strlen($fh.$ft)+filesize($l);
		$files[$l]=$fh;
	}

	$headers="POST ".$urla." HTTP/1.0".$r
	."Host: ".$url['host'].$r
	."Referer: ".$url['host'].$r
	."Content-type: multipart/form-data, boundary=".$bu.$r
	."Content-length: ".$len.$r
	.$r
	.$dat;

	// открыть хост
	if(!$fp=fsockopen($url['host'],$url['port'])) return "ERROR: can't open url ".$url['host'].":".$url['port'];
	// запихнуть заголовок и POST-массив
	if(fputs($fp,$headers)===false) return "ERROR: can't send #1";

	if(count($files)) foreach($files as $l=>$fh) { // позапихивать файлы
		if(fputs($fp,$fh)===false) return "ERROR: can't send #2";
		// открыть файл и запихнуть его
		if(($fp2=fopen($l,"rb"))===false) return "ERROR: can't open file '".$l."'";
		while(!feof($fp2)) if(fputs($fp,fgets($fp2,1024*100))===false) return "ERROR: can't send #4";
		fclose($fp2);
		// запихнуть заключительный хедер
		if(fputs($fp,$ft)===false) return "ERROR: can't send #5";
	}

	// и получить ответ
	$s=''; while(!feof($fp)) $s.=fgets($fp,4096); fclose($fp);
	if($s=='') return "ERROR: NO RESPONSE";
	list($h,$t)=explode($r.$r,$s,2); return $t;
}
//==================================================================================================

function get_my_pack($dir) { $s="my: "; // если есть своя папка с пакетами
	if(is_dir($dir.'instpack')) foreach(glob($dir.'instpack/*.pack') as $l) { $w=basename($l); $s.="<span class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'install_edit_pack',name:'".preg_replace("/\.pack$/s",'',$w)."'})\" style='margin-left:20px'>$w</span>&nbsp; "; }
	$s.="<span title='Create my inctallpack!' class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'install_edit_pack',name:''})\" style='margin-left:20px'>new</span>"
."<span title='System dir' class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'edit_file',file:'".$dir."system_dir.txt'})\" style='margin-left:20px'>system_dir</span>";
	return $s;
}



?>