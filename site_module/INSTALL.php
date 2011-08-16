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

//--------------------------------------------------------------------------------
// ФУНКЦИИ УПДЕЙТОВ

$GLOBALS['selectjs']="
i_selectmode='none';

i_get_selected=function(){ var ee,v,td1,td2,dir,p,e,c,tr=idd('i_selectfiles').getElementsByTagName('TR'), s='';
        for(var i=0;i<tr.length;i++){ p=tr[i]; td1=p.firstChild; td2=p.lastChild; if(td2==td1) continue;
        dir=td1.firstChild.innerHTML;
        ee=td2.getElementsByTagName('DIV'); for(var j=0;j<ee.length;j++){ e=ee[j];
                if(e.style.color=='green') {
                        var v=e.innerHTML.replace(/^<br>/g,'').replace(/\&nbsp;/g,' ').replace(/^ +(.+?) +$/g,'$1');
                        if(dir=='config.php:') v=v.replace(/^([^\=]+)\s*\=.*?$/g,'$1');
                        s=s+'\\n'+(dir!='/'?dir:'')+v;
                }
            }
        }
return s;
};

i_selectall=function(){ var ee,v,td1,td2,selo='',p,c,tr=idd('i_selectfiles').getElementsByTagName('TR');
        for(var i=0;i<tr.length;i++){ p=tr[i]; td1=p.firstChild; td2=p.lastChild; if(td2==td1) continue;
        ee=td2.getElementsByTagName('DIV'); for(var j=0;j<ee.length;j++){

        if(i_selectmode=='color') {
           if(selo=='') selo=(ee[j].style.color=='red'?'green':'red'); ee[j].style.color=selo;
           ee[j].style.textDecoration=(selo=='red'?'line-through':'none');
        }else{
           if(selo=='') selo=(ee[j].style.textDecoration=='none'?'line-through':'none'); ee[j].style.textDecoration=selo;
        }

            }
        }
};


i_submit=function(e){ var ee,v,td1,td2,dir,p,e,c,tr=idd('i_selectfiles').getElementsByTagName('TR'), s='';
	for(var i=0;i<tr.length;i++){ p=tr[i]; td1=p.firstChild; td2=p.lastChild; if(td2==td1) continue;
	dir=td1.firstChild.innerHTML;
	ee=td2.getElementsByTagName('DIV'); for(var j=0;j<ee.length;j++){ e=ee[j];
			var v=e.innerHTML.replace(/^<br>/g,'').replace(/\&nbsp;/g,' ').replace(/^ +(.+?) +$/g,'$1');
			if(dir=='config.php:') v=v.replace(/^([^\=]+)\s*\=.*?$/g,'$1');
			s=s+'<br>'+(dir!='/'?dir:'')+v+' | ';
			if(e.style.textDecoration=='none') { s=s+e.style.color; }
			else s=s+'------';
		}
	}
	ohelpc('asd','sda',s);
};

i_sett=function(e,t){ e.style.cursor='pointer'; e.style.textDecoration='none'; e.setAttribute('tiptitle',t);
	addEvent(e,'mouseover',function(){ idd('tip').innerHTML=this.getAttribute('tiptitle');
	posdiv('tip',mouse_x+10,mouse_y+10); });
	addEvent(e,'mouseout',function(){ zakryl('tip') } );
	addEvent(e,'mousemove',function(){ posdiv('tip',mouse_x+10,mouse_y+10) } );
}

go_install=function(){ var t,c,tr=idd('i_selectfiles').getElementsByTagName('TR');
	for(var i=0;i<tr.length;i++){ var p=tr[i]; var td1=p.firstChild; var td2=p.lastChild; if(td2==td1) continue;
		var dir=td1.firstChild; dir.onclick=function(){i_chand(this)}; i_sett(dir,'Invert selected files');
		var ee=td2.getElementsByTagName('DIV'); for(var j=0;j<ee.length;j++){ var x=ee[j];
			var l=x.innerHTML; var O=l.substring(0,1); l=l.substring(1,l.length);
			x.innerHTML='<br>'+l; x.onclick=function(){i_chan(this)}; x.style.display='inline';
				if(O=='U') { c='green'; t='update'; }
				else if(O=='D') { c='red'; t='del'; }
				else { c='magenta'; t='unk'; }
			x.style.color=c; i_sett(x,t);
			if(i_selectmode=='color') x.style.textDecoration=(c=='red'?'line-through':'none');
}}};

setTimeout(go_install,500);

i_chand=function(e){ var p=e.parentNode.nextSibling.getElementsByTagName('DIV'); for(var i=0;i<p.length;i++) i_chan(p[i]) };

i_chan=function(e){ var s=0;
  if(i_selectmode=='color') { if(e.style.color=='red') { e.style.color='green'; s=1; } else e.style.color='red'; }
  else{
	if(e.style.textDecoration=='none') e.innerHTML=e.innerHTML.replace(/^<br>(.+?)$/g,'<br>&nbsp;&nbsp;$1&nbsp;&nbsp;');
	else{ s=1; e.innerHTML=e.innerHTML.replace(/\&nbsp;/g,' ').replace(/^<br> +(.+?) +$/g,'<br>$1'); }
  }
  e.style.textDecoration=(s?'none':'line-through');
};
";


function UPDATE_testkey($key){ // безопасность: проверка ключа инсталляции
	$f=$GLOBALS['filehost'].'binoniq/instlog/install_key.php'; $k=file_get_contents($f); unlink($f);
	return ( preg_replace("/^.+?\"([0-9a-z]{40})\".+?$/si","$1",$k) != $key ? 0:1);
}

function UPDATE_select($rrr,$pack) { $r=unserialize($rrr); // return "<pre>".print_r($r,1);

	$rp=get_pack_r($pack);

	return "<pre>$pack<hr>".print_r($rp,1);

	$s="<table><tr><td><input type='button' onclick='i_submit()' value='INSTALL'>";
	$otstup=''; $lastdir='';

	// 1. рассортировать данные
	$Uconf=array(); // тут будут конфиговые переменные
	$Ulang=array(); // тут будут языковые переменные
	$Ufile=array(); // тут будут файлы
	foreach($r as $n=>$l) { list($file,$val)=explode(' ',$l,2); unset($r[$n]);
		if(strstr($file,':')) { // конфиг или язык
			list($tt,$ff)=explode(':',$file,2);
			if($tt=='config') { $Uconf[$ff]=$val; continue; }
			if($tt=='lang') { $Ulang[$ff]=$val; continue; }
		}
		$Ufile[$file]=$val;
	}

//=========================================================
	// 1. Что с конфигом?
	// config:msq_login $msq_login = ""; // "lleo";
	$con=file_get_contents('config.php'); preg_match_all("/\n\s*".'\$'."([0-9a-z\_\-]+)\s*\=\s*([^\n]+)/si",$con,$m);
	$con=array(); foreach($m[1] as $i=>$n) $con[$n]=$m[2][$i];
	$s.="</td></tr></table><table><tr valign=top><td><b>config.php:</b></td><td>";
	foreach($Uconf as $n=>$v) { if(isset($con[$n])) unset($con[$n]); else $s.="<div>".'A'.'$'.$n."</div>"; }
	foreach($con as $n=>$l) { $s.="<div>".'D'.'$'.$n."=".h($l)."</div>"; } // предлагается удалить
	unset($con);
//=========================================================
	// 2. Что с языком?
	// lang:fido/ru:Comments:empty_comm Comments:empty_comm	А где же комментарий?
	$lan=array(); $allan=array();
	foreach($Ulang as $n=>$v) { list($ll,$per)=explode(':',$n,2); $allan[$ll]='';

		if(!isset($lan[$ll])) { $lan[$ll]=array(); // закачать сразу язык шоб не париться
		$nf=$GLOBALS['filehost'].'binoniq/lang/'.$ll.".lang";
		if(is_file($nf)&&($li=file($nf))!='') foreach($li as $c) {
			list($cn,$cv)=explode("\t",$c,2); if(($cn=trim($cn))=='') continue; $lan[$ll][$cn]=trim($cv);
		}}

		if(isset($lan[$ll][$per])) { unset($lan[$ll][$per]); continue; }
		$allan[$ll].="<div>".'A'.$per."</div>";
	}
	foreach($lan as $ll=>$arper) foreach($arper as $cn=>$cv) $allan[$ll].="<div>".'D'.$cn." = ".h($cv)."</div>"; // предлагается удалить

	foreach($allan as $ll=>$oo) $s.="</td></tr></table><table><tr valign=top><td><b>LANG:$ll:</b></td><td>".$oo;
 
//=========================================================
	// 3. Что с файлами?
	foreach($Ufile as $f=>$d) {
		$fhost=$GLOBALS['filehost'].$f; // физический файл
		$fname=basename($f); // его имя
		$fdir=dirname($f).'/'; if($fdir=='./') $fdir='/'; // имя папки

		if($fdir!=$lastdir){
			$s.="</td></tr></table><table><tr valign=top><td><b>$fdir</b></td><td>";
			$lastdir=$fdir;
			// <div class=\"$dirname ii1\" onclick='i_d(this)'>".$dirname."</div>";
		}

		if(is_file($fhost)) {
				$o='D';
		} else $o='U';
		$s.="<div>".$o.$fname."</div>";
	}
//=========================================================

	return "<div id='i_selectfiles'>$s</td></tr></table></div>";
}


//----------------------------
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 

if(sizeof($_POST)!=0 && !empty($_POST['post_act'])) { $a=$_POST['post_act'];
	// include_once($GLOBALS['include_sys'].'install_update.php');

	if(!UPDATE_testkey($_POST['key'])) die("ohelpc('install2','post',\"error key\");"); // безопасность: ключ инсталляции

if($a=='check_pack') { // выбор файлов для инсталляции
	$s=UPDATE_select(urldecode($_POST['ara']),strtr($_POST['pack'],'+',' '));
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
	$serv=fileget_save($dir."servers.txt","http://lleo.me/blog Beta
http://lleo.me/dnevnik Stable
http://lleo.me Super Stable
http://binoniq.net Server Stable");

	$select_serv=fileget_save($dir."my_server.txt","http://lleo.me/blog\n+basic");

// ЗЕНОНЗАЕБАЛ!!!!!11
if( ($l=str_replace('lleo.aha.ru','lleo.me',$serv)) != $serv) { fileput($dir."servers.txt",$l); $serv=$l; }
if( ($l=str_replace('lleo.aha.ru','lleo.me',$select_serv)) != $select_serv) { fileput($dir."my_server.txt",$l); $select_serv=$l; }
// ЗЕНОНЗАЕБАЛ!!!!!11

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
	$maj'install_check',s:idd('servs').value,pack:s});
};
ohelpc('install','Select server',\"".njsn($s)."\");";
//	return "zabil('mesto_otvet',\"<div class=r>".njs(nl2br(print_r($r,1)))."</div>\");";
//	mijax(idd('servs').value+'/ajax/midule.php',{mod:'INSTALL',a:'install_check',s:idd('servs').value,pack:s});
//	alert(idd('servs').value+'/ajax/midule.php?mod=INSTALL&a=install_check&s='+idd('servs').value+'&pack='+encodeURIComponent(s));
//
}

if($a=='install_edit_pack') { // форма редактирования пакета или создания нового (name='')
	// include_once($GLOBALS['include_sys'].'install_update.php');
	$name=RE('name'); $s="<table><tr><td>";

	$p=array(); if($name!='' && ($r=file($dir."instpack/".$name.".pack"))!==false) {
		foreach($r as $l) { $m=explode(' ',$l); $p[$m[0]]=array($m[1],$m[2]); } // [0] => template/adminpanel.htm 1303587256 d866bd70d3d53450fd3b82243d32fe36
	}

	//-----
	$lastdir=''; foreach(get_dfiles() as $l) { list($file,$ftime,$fkey)=explode(' ',$l,3);
	$fhost=$GLOBALS['filehost'].$file; // физический файл
	$fname=basename($file); // его имя
	$fdir=dirname($file).'/'; if($fdir=='./') $fdir='/'; // имя папки
	if($fdir!=$lastdir){ $s.="</td></tr></table><table><tr valign=top><td><b>$fdir</b></td><td>"; $lastdir=$fdir; }

	if(isset($p[$file])) $o='U'; else $o='D';
	$s.="<div>".$o.$fname."</div>";
	}
	$s="<div id='i_selectfiles'>$s</td></tr></table></div>";
	//-----

$subm="<input type='button' value='Save' onclick='i_packsave()'>"
."&nbsp; &nbsp; <span class='ll' onclick=\"i_selectall()\">select</span>"
."&nbsp; &nbsp; <span class='ll' onclick=\"packdel()\">delete</span>";

	return $GLOBALS['selectjs']."i_selectmode='color';

packdel=function(){ if(confirm('Delete pack `".$name.".pack`?')) majax('module.php',{mod:'INSTALL',a:'install_pack_del',name:idd('newpack_name').value}); };

i_packsave=function(){
	majax('module.php',{mod:'INSTALL',a:'install_pack_save',s:i_get_selected(),name:idd('newpack_name').value});
};

ohelpc('pack','Edit pack: $name',\"".njsn(
($name==''?"<b>name: </b><input type='text' value='' size='10' maxlength='20' id='newpack_name'>":
"<input type='hidden' value='$name' id='newpack_name'>")
.$subm
."<div id='packs'><tt>$s</tt></div>$subm")."\");";
}

if($a=='install_pack_del') { // удаление пакета
	$name=RE('name'); unlink($dir."instpack/".$name.".pack");
	return "clean('pack'); zabil('mypacks',\"".njsn(get_my_pack($dir))."\"); salert('Pack <b>$name</b> deleted!',1000);";
}

if($a=='install_pack_save') { // приемка создания нового пакета majax('module.php',{mod:'INSTALL',a:'install_pack_save',s:s,name:idd('newpack_name').value});
	$name=preg_replace("/[^0-9a-z\_\-\.]+/s",'',strtolower(RE('name'))); if(empty($name)) return "idd('newpack_name').value='$name'; idie('Name error! Only: 0-9a-z_-.');";
	$s=''; $r=get_dfiles_r(); foreach(explode("\n",trim(RE('s'),"\n")) as $l) {
		if(isset($r[$l])) { $time=$r[$l][0]; $md5=$r[$l][1]; }
		else { $ras=getras($l); $time=filemtime($l); $md5=calcfile_md5($l,$ras); }
		$s.="$l $time $md5\n";
	}
	if($s=='') return "salert('Empty pack!',1000);";
	testdir($dir."instpack"); fileput($dir."instpack/".$name.".pack",$s);
	return "clean('pack'); zabil('mypacks',\"".njsn(get_my_pack($dir))."\"); salert('Pack <b>$name</b> saved!',1000);";
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
	$key=createkey(); // сформировать ключ
	// return "majax('".$ser."/ajax/module.php',{mod:'INSTALL',a:'install_far_check',url:'".$GLOBALS['httphost']."',pack:'".implode(' ',$w)."',key:'$key'})";
	return "mijax('".$ser."/ajax/midule.php',{mod:'INSTALL',a:'install_far_check',url:'".$GLOBALS['httphost']."',pack:'".implode(' ',$w)."',key:'$key'})";
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
		$r=array(); foreach(file($f) as $l) { $l=trim($l,"\n\r\t "); if(!strstr($l,"\t")) continue;
		list($per,$val)=explode("\t",$l,2);
		$r[]="lang:".$la.":$per ".trim($val,"\r\n\t ");
	}
	return $r;
}

if($a=='install_far_check') { // отправить запрос на проверку
	$pack=trim(RE('pack')); $r=get_pack_r($pack);
	return POST_file('',RE('url')."install",array('post_act'=>'check_pack','pack'=>$pack,'key'=>RE('key'),'ara'=>serialize($r)));
}

if($a=='install_test') { // инсталляция POST_file($filepath,$url,$fields,$port=80,$scheme='http');
	$r=get_dfiles_r();
	dier($r);

	$t=POST_file(array(
$GLOBALS['filehost']."re.png",
$GLOBALS['filehost']."re.php",
$GLOBALS['filehost']."install.zip",
$GLOBALS['filehost']."gg.zip"
),'http://lleo.me/blog/install',array('post_act'=>'do','aaa'=>'123','key'=>'rrr'));
	idie($t);
}


}
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//function sr($s){ return "<font color=red>$s</font>"; }
//function sg($s){ return "<font color=green>$s</font>"; }

function getras($s){ $r=explode('.',$s); if(sizeof($r)==1) return ''; return array_pop($r); }

// высчитать кс файла со всеми вычетами и проверками
function calcfile_md5($l,$ras) { $o=file_get_contents($l); if($ras=='php') $o=preg_replace("/[\n\r]+\/\*\s*lleo\s*\*\/[^\n\r]+/si","\n",$o); return md5($o); }

// взять данные по пакету $pack (если ALL - то просканировать всё) и добавить к массиву $e
function getpack($pack,$e) { global $filehost; $save=0;
	$dir=$filehost."binoniq/instlog/instpack/"; testdir($dir); // проверить папку для кэшиков
	if($pack=='ALL') $r=get_dfiles(); // подсчитать суммы
	else { $r=array(); $s=file($dir.$pack.".pack");
		foreach($s as $l) { list($name,$time,$md5)=explode(' ',trim($l));
			$l=$filehost.$name; $tim=filemtime($l);
			if($time!=$tim) { $md5=calcfile_md5($l,getras($l)); $save=1; } // исправить
			$r[]="$name $tim $md5";
		}
	}
	if($save) fileput($dir.$pack.".pack",implode("\n",$r)); // сохранить пакет, если были изменения
	foreach($r as $n=>$l) { if(in_array($l,$e)) unset($r[$n]); } // выкинуть дубли
	return array_merge($e,$r);
}

// ПОЛУЧИТЬ МАССИВ ПО ВСЕМ ФАЙЛАМ ДВИЖКА (которые разрешены в system_dir.txt)
function get_dfiles() { global $stop,$md5mas,$vetomas,$filehostn,$filehost,$allmd5change; $stop=1000;
	if(!isset($filehostn)) $filehostn=strlen($filehost);
	$dir=$GLOBALS['filehost']."binoniq/instlog/"; testdir($dir);
	// взять $md5mas - массив данных по всему движку
	$md5mas=array(); $allmd5change=1; if(($s=file_get_contents($dir."all_md5.tmp"))!==false) { $allmd5change=0; $md5mas=unserialize($s); }
	// взять $vetomas - массив данных по всему движку
	$vetomas=array(); if(($s=file_get_contents($dir."system_veto.txt"))!==false) { $s=file($dir."system_veto.txt"); foreach($s as $l) { $l=trim($l); if($l!='' && substr($l,0,1)!='#') $vetomas[]=$l; } }
	// взять $all - массив данных по всему движку
	$all=array(); $s=file($dir."system_dir.txt"); foreach($s as $l) { $l=trim($l); if($l!='' && substr($l,0,1)!='#') $all[]=$l; }
	// обработать по одному
	$r=array(); foreach($all as $l) $r=array_merge($r,get_dfiles2($l));
	// подзаписать изменения, если были
	if($allmd5change) fileput($dir."all_md5.tmp",serialize($md5mas));
	return $r;
}

function get_dfiles2($files) { global $stop,$md5mas,$vetomas,$filehostn,$filehost,$allmd5change; if(!--$stop) die('stop error');
	$r=array(); $a=$filehost.$files; if(is_file($a)) $a=array($a); else { $b=glob($a."/*"); 
$h=$a."/.htaccess"; if(is_file($h)) $b[]=$h; $a=$b;
}
	// сперва окучить файлы
	foreach($a as $n=>$l) { if(is_dir($l)) continue; $name=c(substr($l,$filehostn));
		$ras=getras($l); if(!in_array($name,$vetomas) && $ras!='old' && $ras!='off') { $time=filemtime($l);
			if(isset($md5mas[$name]) && $md5mas[$name][0]==$time) $md5=$md5mas[$name][1]; // без изменений
			else { $md5=calcfile_md5($l,$ras); $md5mas[$name]=array($time,$md5); $allmd5change=1; }
			$r[]="$name $time $md5";
		}
	        unset($a[$n]); 
	}
	// затем окучить папки
        foreach($a as $l) { if(!in_array($l,$vetomas)) { $name=c(substr($l,$filehostn)); $r=array_merge($r,get_dfiles2($name)); } }
        return $r;
}

function get_dfiles_r(){ // взять файлы в удобном формате
	$r=array(); foreach(get_dfiles() as $l) { list($f,$time,$md5)=explode(' ',$l,3); $r[$f]=array($time,$md5); }
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
function rando($x,$y){ $s='';
//	$k=10; while((--$k)&&!strlen($s)){ if(($g=fopen("/dev/random","rb"))===false) break; $s=fgets($g); fclose($g); }
	if(!strlen($s)) { // /dev/random не сработал, вернуть традиционным образом
		list($t,)=explode(" ",microtime()); mt_srand($t+mt_rand()); $a=mt_rand(0,$y)+$t;
	} else { for($f=1,$a=$j=0;$j<min(strlen($s),3);$j++,$f*=256) $a+=ord($s[$j])*$f; }
	return $x+$a%($y-$x);
}

function hash_generate(){ // idie('5');
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
function admin_pohvast() { return "<center><div id=soobshi><input type=button value='Похвастаться успешной установкой' onclick=\"document.getElementById('soobshi').innerHTML = '<img src=http://lleo.me/blog/stat?link={httphost}>';\"></div></center>"; }

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

/*
//==================================================================================================
// процедура передачи данных и файлов через POST-запрос по старинке без всяких там уебищных CURL-библиотек
// $filePath - полное имя (с путем) файла для передачи или массив имен файлов для передачи (если файлы не передаются - '')
// $urla - адрес запроса, напр. http://lleo.me/blog/install
// $ara - массив переменных POST, напр: array('action'=>'do','key'=>'1','user'=>123)
// возвравщает ответ сервера или, если ошибка, строку, начинающуюся с 'ERROR:'
function POST_file($filePath,$urla,$ara,$port=80,$scheme='http',$charset='Windows-1251') {

unset($ara['ara']);

// return print_r($ara,1);

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

return '1234 '.$headers;

	// и получить ответ
	$s=''; while(!feof($fp)) $s.=fgets($fp,4096); fclose($fp);
	if($s=='') return "ERROR: NO RESPONSE";

return $s;

	list($h,$t)=explode($r.$r,$s,2); return $t;
}
*/

//==================================================================================================
// процедура передачи данных и файлов через POST-запрос по старинке без всяких там уебищных CURL-библиотек
// $filePath - полное имя (с путем) файла для передачи или массив имен файлов для передачи (если файлов нет - '')
// $urla - адрес запроса, напр. http://lleo.aha.ru/blog/install
// $ara - массив переменных POST, напр: array('action'=>'do','key'=>'1','user'=>123)
// возвращает ответ сервера или, если ошибка, строку, начинающуюся с 'ERROR:'
function POST_file($filePath,$urla,$ara,$port=80,$scheme='http',$charset='Windows-1251') {
        if(gettype($filePath)!='array') $filePath=array($filePath);
        $url=array_merge(array('scheme'=>$scheme,'port'=>$port),parse_url($urla));
        $bu="---------------------".substr(md5($filePath.rand(0,32000)),0,10); $r="\r\n"; $ft=$r.'--'.$bu.'--'.$r;

        // данные
        $dat=''; if(count($ara)) foreach($ara as $n=>$v) $dat.='--'.$bu.$r.'Content-Disposition: form-data; name="'.$n
.'"'.$r.$r.urlencode($v).$r;

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

        $headers="POST ".$url['path']." HTTP/1.0".$r
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
        list($h,$t)=explode($r.$r,$s,2);

        // обработка переноса
        if(stristr($h,'301 Moved Permanently')) {
                return POST_file($filePath,preg_replace("/^.+Location: ([^\s]+).*$/si","$1",$h),$ara);
        }

return $t;
}
//==================================================================================================
//==================================================================================================

function get_my_pack($dir) { $s="my: "; // если есть своя папка с пакетами
	if(is_dir($dir.'instpack')) foreach(glob($dir.'instpack/*.pack') as $l) { $w=basename($l); $s.="<span class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'install_edit_pack',name:'".preg_replace("/\.pack$/s",'',$w)."'})\" style='margin-left:20px'>$w</span>&nbsp; "; }
	$s.="<span title='Create my inctallpack!' class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'install_edit_pack',name:''})\" style='margin-left:20px'>new</span>"
."<span title='System dir' class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'edit_file',file:'".$dir."system_dir.txt'})\" style='margin-left:20px'>system_dir</span>";
	return $s;
}

function createkey() { $key=sha1(hash_generate()); // сформировать ключ
	fileput($GLOBALS['filehost']."binoniq/instlog/install_key.php",'<?php die("Error 404"); $key="'.$key.'"; ?>');
	return $key;
}

function get_pack_r($pack) {
	$r=array(); foreach(explode(' ',$pack) as $l) $r=getpack($l,$r); // взять все указанные пакеты
	return $r;
	$o=$r; foreach($o as $n=>$l) { list($l,)=explode(' ',$l,2); $url=$GLOBALS['filehost'].$l;
		if($l=='config.php.tmpl') { $r=array_merge(getconf($url),$r); } // обработать конфиг
		if(getras($l)=='lang') { $r=array_merge(getlang($url),$r); unset($r[$n]); } // обработать язык, сам не слать
	} return $r;
}

?>