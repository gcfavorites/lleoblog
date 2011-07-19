<?php // Работа с фотоальбомом

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

if(!$admin) idie('not admin');

//idie('l');

$hid=intval($_REQUEST["hid"]);
$a=$_REQUEST["a"];

$lastphoto_file=$hosttmp."lastphoto.txt";
$fileset=$foto_file_small."_fotoset.dat";

//=================================== album ===================================================================
if($a=='album') {

$s="
<div id=treehelp class=br>&nbsp;</div>
<div>
<img onmouseover=\"treeh('Удалить выделенные')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/remove.png' class=knop onclick=\"var i=vzyal('treese'); if(i && confirm('Delete '+i+' files?')) majax('foto.php',{a:'albumdel',sel:treeselected})\">&nbsp;
<img onmouseover=\"treeh('...это пока не работает...')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/filenew.png' class=knop id='#new#'>&nbsp;
<img onmouseover=\"treeh('Снять все выделение')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/list-remove.png' class=knop onclick='treeremove()'>&nbsp;
<img onmouseover=\"treeh('Выделить все, что в раскрытых папках')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/list-add.png' class=knop onclick='treeadd()'>&nbsp;
<img onmouseover=\"treeh('Увеличить масштаб просмотра')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/viewmagp.png' class=knop onclick='treeiconp()'>&nbsp;
<img onmouseover=\"treeh('Уменьшить масштаб просмотра')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/viewmagm.png' class=knop onclick='treeiconm()'>&nbsp;
<img onmouseover=\"treeh('Повернуть выделенные на 270')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/rotate_left.png' class=knop onclick=\"if(confirm('Повернуть на 270?')){ openwait(); majax('foto.php',{a:'rotate',degree:270,sel:treeselected}); }\">&nbsp;
<img onmouseover=\"treeh('Повернуть выделенные на 90')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/rotate_right.png' class=knop onclick=\"if(confirm('Повернуть на 90?')){ openwait(); majax('foto.php',{a:'rotate',degree:90,sel:treeselected}); }\">&nbsp;
<img onmouseover=\"treeh('Повернуть выделенные на 180')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/blend.png' class=knop onclick=\"if(confirm('Повернуть на 180?')){ openwait(); majax('foto.php',{a:'rotate',degree:180,sel:treeselected}); }\">&nbsp;
<img onmouseover=\"treeh('Удалить превью #')\" onmouseout=\"treeh('&nbsp;')\" class=knop src='".$www_design."e3/edit-clear.png' onclick=\"if(confirm('Delete previews in '+treefolder+'?')) { openwait(); majax('foto.php',{a:'delpre',sel:treeselected,dir:treefolder}); }\">&nbsp;
<img onmouseover=\"treeh('Обрезать превью 100x100 #')\" onmouseout=\"treeh('&nbsp;')\" class=knop src='".$www_design."e3/crop.png' onclick=\"if(confirm('Cut selected previews 100x100 in '+treefolder+'?')) { openwait(); majax('foto.php',{a:'pre100x100',sel:treeselected,dir:treefolder}); }\">&nbsp;
<img onmouseover=\"treeh('Выйти, вставив картинки в заметку #')\" onmouseout=\"treeh('&nbsp;')\" src='".$www_design."e3/finish.png' class=knop onclick='treefinish()'>&nbsp;
<span class=r id=treese>0</span>
</div>
<div id='/' class='ExpandClose' onclick='treeonclick(event,1)' onDblClick='treeonclick(event,2)'><ul class='Container'></ul></div>";

$opendir=''; // 2010/05'; 
$alb=albumdir('/');

otprav_sb('tree.js',"
	loadCSS('tree.css');
	treeselected={};
	helps('fotoalbum',\"<fieldset><legend>фотоальбом</legend>".njs($s)."</fieldset>\");
	".$alb."

treeh=function(s){
if(s.indexOf('#')!=-1) {
	var i=vzyal('treese'); if(i=='0') i='(папка <u>'+treefolder+'</u>)'; else i='('+i+'&nbsp;шт)';
	s=s.replace(/\\#/g,i);
} zabil('treehelp',s);
};

treem=function(e){
zabil('treehelp',e.src.replace(/^.{".(strlen($httphost)-1)."}(.*?\\/)pre\\/([^\\/]+)\$/g,'\$1\$2').replace(/\\?\d+\$/g,''));
};

treeallimgicon=function(n){ var findimg=[];
var pp=idd('/').getElementsByTagName('LI'); for(var i in pp) { if(isNaN(i)) continue;
var e=pp[i].getElementsByTagName('DIV')[1].getElementsByTagName('IMG');	for(var k in e) { if(e[k].id) findimg.push(e[k]); }
} for(var i in findimg) { var e=findimg[i]; e.style.width=n+'px'; e.style.height=n+'px'; if(n>150) e.src=e.src.replace(/\/pre\//g,'/'); }
};

openwait=function(){ helpc('wait','<fieldset>workind... <img src='+www_design+'img/ajax.gif></fieldset>'); };

treeiconp=function(){ if(treeicon<1000) treeicon+=10; treeallimgicon(treeicon); };
treeiconm=function(){ if(treeicon>10) treeicon-=10; treeallimgicon(treeicon); };

/* clickdel=function(){ var s=''; for(var i in treeselected) s='\\n'+i+' ('+treeselected[i]+')'+s; alert(s);}; */

treefinish=function(){ var a='',b='';
	for(var i in treeselected)
		if(treeselected[i]=='img') a=a+'\\n{_FOTOM: '+wwwhost+i+' _}';
		else b=b+'\\n'+wwwhost+i;
	var s=a+b;
	if(s.length){ f5_save('clipboard_text',s); f5_save('clipboard_mode','plain'); }
	clean('fotoalbum');
};

treeremove=function(){ for(var i in treeselected) idd(i).style.border='2px solid transparent'; treeselected={};treepr();};

setkey('E','',function(e){ var e; for(var i in treeselected) e=i; majax('foto.php',{a:'treeact',id:e}); },false);

treeadd=function(){
	var folderclo=1;
	var se={};
	var pp=idd('/').getElementsByTagName('LI'); if(!pp.length) return;
	var closelist=[]; for(var i in pp) { if(isNaN(i)) continue; var p=pp[i];

	if(in_array(p,closelist)) continue;

	if(p.id!=''&&p.id!=undefined) {	if(treehasClass(p,'ExpandClosed')) {
		var pp1=p.getElementsByTagName('LI'); for(var j in pp1) { closelist.push(pp1[j]); }
		} else { folderclo=0; } continue;
	}

	var id=false;

	var pp1=p.getElementsByTagName('DIV'); if(pp1.length) for(var j in pp1){ var p1=pp1[j]; 
		if(p1.id!=undefined && p1.id!=''){ se[p1.id]=0; break; } 
	}

	if(id==false){ var pp1=p.getElementsByTagName('IMG'); for(var j in pp1) if(pp1[j].id) se[pp1[j].id]='img'; }
	
	}

for(var v in se) { var i=se[v]; if(folderclo || v.indexOf('/')!=-1) { idd(v).style.border='2px dotted red';
if(!treeselected[v]) treeselected[v]=i;
}}
treepr();
};

treen=function(){ var i=0; for(var k in treeselected) i++; return i; };
treepr=function(){ zabil('treese',treen()); };

function treeonclick(event,dbl){ event=event||window.event; var e=event.target||event.srcElement;
	if(treehasClass(e,'Expand')) e=e.parentNode;
	else while((e.id == '' || e.id == undefined) && e.parentNode != undefined) e=e.parentNode;
		if(e.isLoaded||e.getElementsByTagName('LI').length){ treetoggleNode(e); return; }
		if(treehasClass(e,'ExpandClosed')) { treeload(e.id); return; }
		if(dbl==2 || event.shiftKey) { majax('foto.php',{a:'treeact',id:e.id}); return; }

		if(!treeselected[e.id]) { treeselected[e.id]=(e.tagName=='IMG'?'img':1); e.style.border='2px dotted red'; 
if(admin && treen()==1) { treeid=e.id;

if(e.tagName=='IMG') var t=\"".njsn("
<img title='Удалить' src='".$www_design."e3/remove.png' class=knop onclick=\"if(treen()==1 && confirm('Delete?')) majax('foto.php',{a:'albumdel',sel:treeselected})\">&nbsp;
<img title='Повернуть на 270' src='".$www_design."e3/rotate_left.png' class=knop onclick=\"if(treen()==1 && confirm('Повернуть на 270?')){ openwait(); majax('foto.php',{a:'rotate',degree:270,sel:treeselected}); }\">&nbsp;
<img title='Повернуть на 90' src='".$www_design."e3/rotate_right.png' class=knop onclick=\"if(treen()==1 && confirm('Повернуть на 90?')){ openwait(); majax('foto.php',{a:'rotate',degree:90,sel:treeselected}); }\">&nbsp;
<img title='Повернуть на 180' src='".$www_design."e3/blend.png' class=knop onclick=\"if(treen()==1 && confirm('Повернуть на 180?')){ openwait(); majax('foto.php',{a:'rotate',degree:180,sel:treeselected}); }\">&nbsp;
<img title='Вставить в заметку' src='".$www_design."e3/finish.png' class=knop onclick='if(treen()==1) treefinish()'>&nbsp;
")."\";

else { var t=\"".njsn("
<img title='Delete' src='".$www_design."e3/remove.png' class=knop onclick=\"if(treen()==1 && confirm('Delete?')) majax('foto.php',{a:'albumdel',sel:treeselected})\">&nbsp;
<img title='View' src='".$www_design."e3/blend.png' class=knop onclick=\"majax('foto.php',{a:'treeact',id:treeid});\">&nbsp;
<img title='Edit' src='".$www_design."e3/kontact_journal.png' class=knop onclick=\"majax('foto.php',{a:'edit_text',file:treeid});\">&nbsp;
")."\";

if(treeid.replace(/\.s*html*$/g)!=treeid) t=t+\"".njsn("
<img title='Import from file to blog' src='".$www_design."e3/finish.png' class=knop onclick=\"if(treen()==1 && confirm('Import file and rename to *.old?')) majax('editor.php',{a:'fileimport',id:treeid})\">&nbsp;
")."\";

}


helps('fotooper',\"<fieldset><legend>/\"+treeid+\"</legend>\"+t+\"</fieldset>\");

} else clean('fotooper');

}
			else { delete(treeselected[e.id]); e.style.border='2px solid transparent'; clean('fotooper'); }
	treepr();
}
");
}

//=================================== editpanel ===================================================================
if($a=='uploadform') {

$fotoset=get_fotoset();
$kuda=$fotoset['dir'];

//if(is_file($lastphoto_file)) $kuda=trim(file_get_contents($lastphoto_file)); else $kuda='';
// if($kuda='') $kuda='/';

$sendfoto="\\\"majax('foto.php',{a:'upload',hid:'$hid',kuda:'$kuda',num:'".intval($_REQUEST["num"])."'"
.",file1:idd('fotou1_$hid')"
.",file2:idd('fotou2_$hid')"
.",file3:idd('fotou3_$hid')"
.",file4:idd('fotou4_$hid')"
.",file5:idd('fotou5_$hid')"
.",file6:idd('fotou6_$hid')"
."})\\\"";

$o="
var fileinputsize=20;
ch_fileinputsize=function(e){ var n=e.value.length; if(n<=fileinputsize) return; fileinputsize=n; e.size=n; };

helps('foto_$hid',\"<fieldset><legend>закачиваем новое фото в папку ".$wwwhost."<span class='kudafoto'>".$kuda."</span></legend>"
."<div class=ll style='font-size: 13px' onclick=\\\"clean('foto_$hid'); majax('foto.php',{a:'album'})\\\"><span class='kudafoto'>".$kuda."</span></div> <a href=\\\"javascript:majax('foto.php',{a:'formfotoset'})\\\" class=br>настройки</a><br>"
."<form enctype='multipart/form-data'>"
    ."<input name=file1 type=file id='fotou1_$hid' onchange='ch_fileinputsize(this)'>"
."<br><input name=file2 type=file id='fotou2_$hid' onchange='ch_fileinputsize(this)'>"
."<br><input name=file3 type=file id='fotou3_$hid' onchange='ch_fileinputsize(this)'>"
."<br><input name=file4 type=file id='fotou4_$hid' onchange='ch_fileinputsize(this)'>"
."<br><input name=file5 type=file id='fotou5_$hid' onchange='ch_fileinputsize(this)'>"
."<br><input name=file6 type=file id='fotou6_$hid' onchange=$sendfoto>"
."<br><input type=button onclick=$sendfoto value='SEND'>"
."</form></fieldset>\");";

// если сменилась дата - предложить поменять на новую
$newd=date("Y/m"); if($newd!=$fotoset['dir'] && !is_dir($filehost.$newd)) { list($newy,)=explode("/",$newd);
$o.="if(confirm('Сменился календарный месяц".(is_dir($filehost.$newy)?'':" (и год!)").",\\nсоздать новую папку $newd?')) {
majax('foto.php',{a:'saveset',X:'".h($fotoset['X'])."',x:'".h($fotoset['x'])."',Q:'".h($fotoset['Q'])."',q:'"
.h($fotoset['q'])."',dir:'$newd',logo:'".h($fotoset['logo'])."'});
}";

}
otprav($o);

}
//=================================== editpanel ===================================================================








if(isset($_REQUEST['onload'])) otprav(''); // все дальнейшие опции будут запрещены для GET-запроса







//=================================== работа с нодами альбома ===================================================================

if($a=='saveset') {

	$X=RE0('X'); if($X<10 or $X>1600) idie('Не паясничай, выставь ширину человеческую.');
	$x=RE0('x'); if($x<5 or $x>500) idie('Не паясничай, выставь ширину превью человеческую.');
	$Q=RE0('Q'); $q=RE0('q');
	if($q<50 or $q>98 or $Q<50 or $Q>98) idie('Качество имеет смысл делать в пределах 50-95%');
	$dir=trim(RE('dir'),'/');

	// создать директории
	$m=explode('/',$dir); $a=$filehost; foreach($m as $l) { $l=str_replace("..","_",$l); $a.=$l."/"; if(!is_dir($a)) dirput($a); }

	$logo=$_REQUEST['logo'];

	if(fileput($fileset,serialize(array('X'=>$X,'x'=>$x,'q'=>$q,'Q'=>$Q,'dir'=>$dir,'logo'=>$logo))) ===false)
	idie("Ошибка записи $fileset!");

	otprav("clean('fotoset')");
}

//=================================== работа с нодами альбома ===================================================================
if($a=='formfotoset') { $idhelp='fotoset';

$fotoset=get_fotoset();

//  <tr><td>закачать новую картинку:</td><td><div class=l onclick=\"majax('foto.php',{a:'uploadform',hid:hid})\">здесь</div></td></tr>

$s="<table>
<tr><td>ширина картинки:</td><td><input id='fotoset_X' size=4 type=text name='X' value='".h($fotoset['X'])."'>px</td></tr>
<tr><td>качество картинки:</td><td><input id='fotoset_Q'size=3 type=text name='Q' value='".h($fotoset['Q'])."'>%</td></tr>
<tr><td>ширина превью:</td><td><input id='fotoset_x' size=4 type=text name='x' value='".h($fotoset['x'])."'>px</td></tr>
<tr><td>качество превью:</td><td><input id='fotoset_q' size=3 type=text name='q' value='".h($fotoset['q'])."'>%</td></tr>
<tr><td>папка:</td><td>".$wwwhost."<input id='fotoset_dir' size=15 type=text name='dir' value='".h($fotoset['dir'])."'></td></tr>
<tr><td>подпись:</td><td><input id='fotoset_logo' size=25 type=text name='logo' value='".h($fotoset['logo'])."'></td></tr>
</table>
<input type=submit value='Save' onclick=\"edit_savefotoset()\">";

$s="
edit_savefotoset=function(){
	var ara={a:'saveset'};
	var nara=['X','x','Q','q','dir','logo']; for(var l in nara) { l=nara[l]; ara[l]=idd('fotoset_'+l).value; }
	majax('foto.php',ara);
	zabilc('kudafoto',idd('fotoset_dir').value);
};

helps('fotoset',\"<fieldset><legend>Настройки фото</legend>".njsn($s)."</fieldset>\");
idd('fotoset_X').focus();
";

otprav($s);
}
//=================================== работа с нодами альбома ===================================================================
if($a=='albumgo') { otprav(albumdir(RE('id'))." treeallimgicon(treeicon);"); }
//=================================== setdir ===================================================================
if($a=='setdir') { otprav("helps('foto_$hid',\"<fieldset><legend>выбираем папку</legend>??</fieldset>\");"); }

if($a=='savedir') { $dir=$_REQUEST["dir"]; $dir=h(preg_replace("/\.+/s",'.',$dir));
	fileput($lastphoto_file,$dir);
	otprav("clean('foto'); majax('foto.php',{a:'uploadform'});");
}
//=================================== album-del ===================================================================
if($a=='albumdel') { AD();
	$s='';
	foreach($_REQUEST['sel'] as $l=>$n) { $f=$filehost.$l;
		if(!is_file($f)) idie('Not found: '.h($l));
		if(!is_ras_image($l)) idie(h($l).' - is not image!<br>(under construction, sorry)');
		$x=predir($f,'mic/'); if(is_file($x)) unlink($x);
		$x=predir($f,'pre/'); if(is_file($x)) unlink($x);
		unlink($f);
		$s.="clean('".$l."');";
	}
	otprav($s);
}
//==================================================================================================
if($a=='lostfoto') {
	$num=intval(str_replace('editor','',RE('idhelp'))); if(!$num) idie('Error 0');
	$Date=ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `num`=".$num,"_l"); if($Date===false) idie('Error 1');
	list($y,$m,)=explode('/',$Date,3); if(!intval($y)||!intval($m)) idie('Error 2');
	$p=glob($filehost.$y."/".$m."/*.jpg");
	$s=''; foreach($p as $l) {
		preg_match("/([^\/]+)\.jpg$/si",$l,$e); $e=$e[1];
		if(!ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date` LIKE '".e($y)."/".e($m)."/%' AND `Body` LIKE '%".e($e)."%'","_l"))
		$s.="\\n{_FOTOM: $e _}";
	}

	otprav("
		var v=idd('editor".$num."_Body');
		if(v) v.value=v.value+'".$s."';
	");
}
//=================================== treeact ===================================================================









// Далее - процедуры фотообработки

require_once $include_sys."_fotolib.php";















if($a=='createpreview') { if(!$admin) idie('admin only!');
	$id=preg_replace("/\.+/s",'.',$_REQUEST['id']);
	$l=$filehost.$id.'/';
		$m=array(); $p=glob($l.'*'); foreach($p as $x){ if(is_ras_image($x)) $m[basename($x)]=1; } // вот шо надо
		if(is_dir($l.'pre')) { $p=glob($l.'pre/*'); foreach($p as $x) unset($m[basename($x)]); }
	if(!sizeof($m)) idie('Error 04');

	$pre=$l.'pre'; if(!is_dir($pre)) { mkdir($pre); chmod($pre,0777); } // создать там папку для превьюшек

	$fotoset=get_fotoset(); // взять настройки

	foreach($m as $x=>$n) {
		obrajpeg($l.$x, $l.'pre/'.$x,$fotoset['x'],$fotoset['q']); // сделать превьюшку
		$s.="idd('".$id."/".$x."').src='".$wwwhost.$id."/pre/".$x."';"; // заменить на экране превьюшками
	}
	otprav($s."clean('wait');");
}

//=================================== treeact ===================================================================
if($a=='delpre') { if(!$admin) idie('admin only!');
	$dir=$_REQUEST['dir']; if(strstr($dir,'..')) idie("Ошибка. Хакерствуем, бля?");

	if(!sizeof($_REQUEST['sel'])) { $sl=$filehost.$dir.'pre/'; $p=glob($sl.'*'); }
	else { $p=array(); foreach($_REQUEST['sel'] as $l=>$n) { if($n=='img') $p[]=$filehost.predir($l,'pre/'); } }

	$s=''; foreach($p as $x) if(is_ras_image($x)) { if(strstr($x,'..')) idie("Ошибка. Хакерствуем, бля?");
		if(is_file($x)){ $s.="idd('".predir_id($x,$filehost)."').src='".$www_design."e3/foto.png';"; unlink($x); }
	}
	otprav($s."treeremove();clean('wait');");
}

if($a=='pre100x100') { if(!$admin) idie('admin only!');
	$rand=rand(0,100000);
	$fotoset=get_fotoset();
	$dir=$_REQUEST['dir']; if(strstr($dir,'..')) idie("Ошибка. Хакерствуем, бля?");

	if(!sizeof($_REQUEST['sel'])) { $sl=$filehost.$dir.'pre/'; $p=glob($sl.'*'); }
	else { $p=array(); foreach($_REQUEST['sel'] as $l=>$n) { if($n=='img') $p[]=$filehost.predir($l,'pre/'); } }

	$s=''; foreach($p as $x) if(is_ras_image($x)) { if(strstr($x,'..')) idie("Ошибка. Хакерствуем, бля?");
		if(is_file($x)){
		$s.="var i='".predir_id($x,$filehost)."'; idd(i).src=idd(i).src.replace(/\\?.*?$/g,'')+'?".$rand."';";
		pre100x100($x,$fotoset['q']);
		}
	}
	otprav($s."treeremove();clean('wait');");
}

//=================================== rotate ===================================================================
if($a=='rotate') {
	$s=''; $degree=intval($_REQUEST['degree']);
	$rand=rand(0,100000);
	$fotoset=get_fotoset();

	foreach($_REQUEST['sel'] as $l=>$n) { $f=$filehost.$l;
		if(!is_file($f)) idie('Not found: '.h($l));
		if(!is_ras_image($l)) idie(h($l).' - is not image!<br>(under construction, sorry)');
			rotatejpeg($f,$degree,$fotoset['Q']);
			$x=predir($f,'pre/'); if(is_file($x)) rotatejpeg($x,$degree,$fotoset['q']);
			$s.="idd('$l').src=idd('$l').src.replace(/\\?.*?$/g,'')+'?".$rand."';";
	}
	otprav($s."clean('wait');");
}
//=================================== treeact ===================================================================
if($a=='treeact') { if(!$admin) idie('admin only!');

	$l=preg_replace("/\.+/s",'.',$_REQUEST['id']);

	if(stristr($l,'config.php')) AD(); // idie('Disable!');

if(is_ras_image($l)) {
	if(!is_file($filehost.predir($l,'pre/'))) {
		otprav("if(confirm('Create previews for this folder?')) { helps('wait',\"<fieldset>workind... <img src=\"+www_design+\"img/ajax.gif></fieldset>\"); posdiv('wait',-1,-1); majax('foto.php',{a:'createpreview',id:'".h(dirname($l))."'});}");
	}

	otprav("var s='".$httphost.$l."'; if(idd('bigfoto')&&idd('bigfotoimg').src==s) clean('bigfoto'); else bigfoto(s+'?".rand(0,10000)."')");
}

else otprav("
starteditor=function(){majax('foto.php',{a:'edit_text',file:'".h($l)."'})};
setkey('esc','');
helpc('fotoset',\"<fieldset><legend><img class='knop' src='".$www_design."e3/kontact_journal.png' title='Edit this (press `E`)'"
." onclick='starteditor()'> ".h($wwwhost.$l)."</legend><table id='fotoset_c' style='width:\"+(getWinW()-100)+\"px'><tr><td>".njsn(str_replace('&nbsp;',' ',highlight_file($filehost.$l,1)))."</td></tr></table></fieldset>\");
setkey(['E','У','у'],'',starteditor,false);
/*idd('fotoset_c').onclick=function(e){ e=e||window.event;clean('fotoset'); }*/
");
}
//=================================== editpanel ===================================================================
if($a=='edit_text') {
	$file=RE('file');
otprav("
save_and_close=function(){save_no_close();clean('fotoset')};
save_no_close=function(){ if(idd('edit_text').value==idd('edit_text').defaultValue) return salert('".LL('save_not_need')."',500);
majax('foto.php',{a:'save_file',file:'".h($file)."',text:idd('edit_text').value});
idd('edit_text').defaultValue=idd('edit_text').value;
};
helpc('fotoset',\"<fieldset><legend>Edit: ".h($wwwhost.$file)."</legend><table><tr><td>"
."<textarea style='width:\"+(getWinW()-100)+\"px;height:\"+(getWinH()-100)+\"px;' id='edit_text'>".h(njsn(file_get_contents($filehost.$file)))."</textarea>"
."<br><input title='".LL('ctrl+Enter')."' type='button' value='".LL('Save+exit')."' onclick='save_and_close()'> <input title='".LL('shift+Enter')."' type='button' value='".LL('Save')."' onclick='save_no_close()'>"
."</td></tr></table></fieldset>\");
idd('edit_text').focus();
setkey('esc','',function(e){ if(idd('edit_text').value==idd('edit_text').defaultValue || confirm('".LL('exit_no_save')."')) clean('fotoset'); },false);
setkey('enter','ctrl',save_and_close,false);
setkey('enter','shift',save_no_close,false);
setkey('tab','shift',function(){ti('edit_text','\\t{select}')},false);
");
}

if($a=='save_file'){ AD(); fileput($filehost.RE('file'),RE('text')); otprav("salert('".LL('saved')."',500)"); }

//=================================== editpanel ===================================================================
if($a=='upload') {

$num=intval($_REQUEST["num"]);

$fotoset=get_fotoset();

$foto_file_small=$filehost.$fotoset['dir'].'/';
$foto_www_small=$wwwhost.$fotoset['dir'].'/';
	if(!is_dir($foto_file_small)) { mkdir($foto_file_small); chmod($foto_file_small,0777); }
$foto_file_preview=$filehost.$fotoset['dir'].'/pre/';
$foto_www_preview=$wwwhost.$fotoset['dir'].'/pre/';
	if(!is_dir($foto_file_preview)) { mkdir($foto_file_preview); chmod($foto_file_preview,0777); }

$s='';
// $ts='';

if(count($_FILES)>0) foreach($_FILES as $FILE) if(is_uploaded_file($FILE["tmp_name"])){ $fname=h($FILE["name"]);

	if(!preg_match("/\.jpe*g$/si",$fname)) idie("Это разве фотка?");
	if(preg_match("/^\./si",$fname)) idie("Имя с точки?");
	if(strstr($fname,'..')) idie("Ошибка. Хакерствуем, бля?");


	//--- фотоальбом Nokia ---
	if(preg_match("/^(\d\d)(\d\d)(\d{4})(\d+)\.jpg/si",$fname,$m) && $m[3]."/".$m[2]==$fotoset['dir']) {
		$fname=$m[1]."-".$m[4].".jpg";
	}
	//--- фотоальбом Nokia ---

	if(is_file($foto_file_small.$fname)) { $s.="<td><img onclick=\\\"foto('".$foto_www_small.$fname."')\\\" src='".$foto_www_preview.$fname."'><div class=br><font color=red>".h($fname)."</font></div></td>"; }
	else {
		obrajpeg($FILE["tmp_name"],$foto_file_small.$fname,$fotoset['X'],$fotoset['Q'],$fotoset['logo']);
		obrajpeg($foto_file_small.$fname,$foto_file_preview.$fname,$fotoset['x'],$fotoset['q']);
		$s.="<td><img onclick=\\\"foto('".$foto_www_small.$fname."')\\\" src='".$foto_www_preview.$fname."'><div class=br>".h($fname)."</div></td>";
		if($num) $ts.="\\n{_FOTOM: ".str_replace('.jpg','',$fname)." _}";
	     }
	} 

if($s=='') idie("Ошибка 2! ".nl2br(h(print_r($_FILES,1))));


if($num && $ts!='') $ts="
	var v=idd('editor".$num."_Body'); if(v){
		v.value=v.value+'$ts';
		edit_polesend('Body',idd('editor".$num."_Body').value,".$num.");
	}
";


otprav("

foto=function(f){
	helps('bigfoto',\"<img onclick=\\\"clean('winfoto')\\\" src='\"+f+\"'>\");
	idd('bigfoto').style.top = mouse_y+'px'; //(getWinH()-imgy)/2+getScrollW()
	idd('bigfoto').style.left = (getWinW()-".$foto_res_small.")/2+getScrollH()+'px';
};

$ts

helps('foto_$hid',\"<table><tr align=center>$s</tr></table>\");

");

}

//==================================================================================================

function get_fotoset() {
	$fotoset=unserialize(file_get_contents($GLOBALS['fileset'])); if($fotoset===false) $fotoset=array();
	if(!intval($fotoset['X'])) $fotoset['X']=$GLOBALS['foto_res_small'];
	if(!intval($fotoset['Q'])) $fotoset['Q']=$GLOBALS['foto_qality_small'];
	if(!intval($fotoset['x'])) $fotoset['x']=$GLOBALS['foto_res_preview'];
	if(!intval($fotoset['q'])) $fotoset['q']=$GLOBALS['foto_qality_preview'];
	if(!isset($fotoset['dir'])) $fotoset['dir']='';
	if(!isset($fotoset['logo'])) $fotoset['logo']=$GLOBALS['foto_logo'];
	return $fotoset;
}

//===========================================================================================================
function albumdir($id) { global $filehost,$wwwhost,$www_design;
	$id=preg_replace("/\.+/s",'.',$id);
	$urln=rtrim($filehost.$id,'/').'/*'; $a=glob($urln); // взять файлы
	$sl=strlen($filehost); // длина базы имени

	$dirs=$imgs=$files=''; foreach($a as $n=>$l) { $nn=substr($l,$sl); $bn=basename($l);

		if(is_dir($l)) { // все папки
			if($bn!='pre') $dirs.="['$nn/','<div class=ll>$bn</div>',1],";

		} else // все картинки
			if(is_ras_image($l)) {
//			if(is_file(predir($l,'mic/'))) $pic=$wwwhost.predir(substr($l,$sl),'mic/'); // mic preview
//			else
			if(is_file(predir($l,'pre/'))) $pic=$wwwhost.predir(substr($l,$sl),'pre/'); // pre preview
			else $pic=$www_design."e3/foto.png"; // no preview
			$imgs.="<img src=\"$pic\" onmouseover=\"treem(this)\" class=treef id=\"$nn\"> ";

		} else { // все остальное
			 $files.="['$nn','<div id=\"$nn\" class=le>$bn</div>',0],";
		}

	}
return "
	treeonLoaded([".$dirs.($imgs!=''?"['','".$imgs."',0],":'').rtrim($files,',')."],'$id');
	treeshowLoading(false,'$id');
";
}

function is_ras_image($l){ return in_array(substr(strrchr($l,'.'),1),array('jpg','jpeg','gif','JPG','JPEG','GIF','png','PNG')); }
function predir($l,$pre){ $bn=basename($l); $bd=dirname($l).'/'; if($bd=='./') $bd=''; return $bd.$pre.$bn; }
function predir_id($l,$fh){ return preg_replace("/^.{".strlen($fh)."}(.*?)\/[^\/]+(\/[^\/]+)$/si","$1$2",$l); }

?>
