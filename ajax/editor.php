<?php // Редактор заметки

include "../config.php";

function ANDC() { return ($GLOBALS['acn']?" AND `acn`='".$GLOBALS['acn']."'":''); }

if(isset($_POST['a'])){ include $include_sys."_autorize.php";
// - - -
if($_POST['a']=='ljpost') { if(!$podzamok) idie("NE PODZAMOK!");
	include_once $include_sys."ljpost.php"; // ето моя библиотечка ljpost
 	$lj=LJ_post($_POST['ljuser'],$_POST['ljpass'],wu($_POST['Header']),wu($_POST['s']),array('prop_opt_noemail'=>1));
	if($lj['success']!='OK') dier($lj."<p><br>",LL('ljpost:error')); // Ошибка!
	idie(LL('ljpost:postdone',$lj['url']),LL('ljpost:hsuccess'));

/*
	$p=$_POST; unset($p['a']); unset($p['names']); foreach($p as $n=>$l){ if($l=='default') unset($p[$n]); }
	$p=array_merge(makeopt(array(),1),$p);
	dier($p);

	$s=prepare_Body($p);
	// применить шаблон
	if($p['ljpost_template']!='') $s=str_replace(array('{text}','{url}'),array($s,get_link($p['Date'])),$t);

	include_once $include_sys."ljpost.php"; // ето моя библиотечка ljpost
 	$lj=LJ_post($admin_ljuser,$admin_ljpass,wu($p['Header']),wu($s),array('prop_opt_noemail'=>1));
	if($lj['success']!='OK') dier($lj."<p><br>",LL('ljpost:error')); // Ошибка!
	idie(LL('ljpost:postdone',$lj['url']),LL('ljpost:hsuccess'));
*/

}
// - - -
die('POST - end');
}

require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
if(isset($_REQUEST['onload'])) otprav(''); // все дальнейшие опции будут запрещены для GET-запроса
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

$autosave_count = 200; // 128; // через сколько нажатий кнопки автозапись

$num=RE0('num'); $idhelp='editor'.$num; $a=RE('a');

//=================================== load ===================================================================
// <div id='buka' class='ll' onclick="majax('editor.php',{a:'load',id:this.id,Date:'2011/11/02'})">click</div>
if($a=='load') {
	if($num) { $x='num'; $v=$num; } else { $x='Date'; $v=e(RE('Date')); }
	if(strstr($v,'#')) list($v,$aname)=explode('#',$v,2);

if(($p=ms("SELECT `Date`,`Body`,`Header`,`opt` FROM `dnevnik_zapisi` ".WHERE("`$x`='$v'"),"_1"))===false) idie(LL('ljpost:notfound').h(" ".$num." ".RE('Date'))); // Такой заметки нет!
	$p=mkzopt($p);


if(!empty($aname)) {
	$quick=RE0('quick');
	if(!$quick) $p['Body']=prepare_Body($p); // если quick=0 (по умолчанию) - то обработать до

	/// БЛЯТЬ ХУЙ ЗНАЕТ ПОЧЕМУ НЕ РАБОТАЕТ ПЕРВЫЙ ВАРИАНТ СТРОКИ:
//	if(preg_match("/<a\s+name=[\'\"]*".preg_quote($aname)."[\'\"]*>(.*?)(<a\s+name=|$)/si",$s,$m)) $s=$m[1];
	if(preg_match("/<a\s+name=[\'\"]*".preg_quote($aname)."[\'\"]*>(.*?)<a\s+name=/si",$p['Body'].'<a name=',$m))
		$p['Body']=$m[1]; else idie('Error reading #'.h($aname));

	if($quick) $p['Body']=prepare_Body($p); // если quick=1 - обработать после
	
} else $p['Body']=prepare_Body($p);

//	idie(h($s));
	otprav("
zabil('".RE('id')."',\"".njs($p['Body'])."\");
".(($idhead=RE('idhead'))!=''?"zabil('".RE('idhead')."',\"".$p['Header']."\");":'')."
");
}
//=================================== ljpost ===================================================================
// - - - - -
if($a=='ljpost') { AD();
	if(empty($admin_ljuser) or empty($admin_ljpass)) idie(LL('ljpost:notlogpas'));
	if(!$num) otprav("salert(\"".LL('ljpost:err0')."\",1000)"); // Сперва надо заметку сохранить!
	if(($p=ms("SELECT `Date`,`Body`,`Header`,`opt` FROM `dnevnik_zapisi` WHERE `num`='$num'","_1",0))===false)
		idie(LL('ljpost:notfound')); // Такой заметки нет!
	$p=mkzopt($p);
	$s=prepare_Body($p);

	// применить шаблон
	if(($t=ms("SELECT `text` FROM `site` WHERE `name`='ljpost_template'","_l"))!==false)
		$s=str_replace(array('{text}','{url}'),array($s,get_link($p['Date'])),$t);

	if(isset($server_matka)) {
otprav("
ohelpc('ljpost_post','LJ-post','<iframe width=500 height=200 id=iframeljpost name=iframeljpost></iframe>');
postToIframe({a:'ljpost',ljuser:\"".$admin_ljuser."\",ljpass:\"".$admin_ljpass."\",Header:\"".njsn($p['Header'])
."\",s:\"".njsn($s)."\"},'".$server_matka."/ajax/editor.php','iframeljpost');
");
}

	include_once $include_sys."ljpost.php"; // ето моя библиотечка ljpost
 	$lj=LJ_post($admin_ljuser,$admin_ljpass,wu($p['Header']),wu($s),array('prop_opt_noemail'=>1),
(empty($admin_flat)?"http://www.livejournal.com/interface/flat":$admin_flat) // http://lj.rossia.org/interface/flat
);
	if($lj['success']!='OK') dier($lj,"<p><br>".LL('ljpost:error')."<br>Date: ".date("Y-m-d- H:i:s")); // Ошибка!
	idie(LL('ljpost:postdone',$lj['url']),LL('ljpost:hsuccess'));
}
//=================================== nocomment ===================================================================
if($a=='nocomment') { AD();
	if(($po=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='$num'","_1",0))===false) idie('false');
	$po=mkzopt($po); $l=$po['Comment_write'];

	$src=$www_design."e3/";
	if($po['Comment_write']=='off') { $src.='ledgreen.png'; $po['Comment_write']='on'; }
	else { $src.='ledred.png'; $po['Comment_write']='off'; }

	msq_update('dnevnik_zapisi',array('opt'=>e(ser($po))),"WHERE `num`='$num'");
	otprav("idd('knopnocomment_".$num."').src='$src'");
}
//=================================== tags ===================================================================
if($a=='tags') {
	$p=explode(',',$_REQUEST["mytags"]); $tag=array(); foreach($p as $l) { $l=c($l); if($l!='') $tag[$l]=1; }
	$t=''; foreach(ms("SELECT DISTINCT `tag` FROM `dnevnik_tags`","_a",0) as $l) { $l=$l['tag'];
		//dier($tag);
		$t.="<span".($tag[$l]!=1?'':" style='color:grey'")." class=l onclick='addtag(this)'>$l</span>, ";
// color:graystyle.color
//className=''
	} $t=trim($t,', '); if($t=='') otprav('');
otprav("
addtag=function(e){
	var t=idd('tags_".$idhelp."');
	var a=t.value.replace(/^[\s,]+|[\s,]+$/g,'').replace(/\s*,\s*/gi,',').split(',');
	var s=e.innerHTML;
	var p=in_array(s,a); if(p!==false) { a.splice(p,1); e.style.color='blue'; } else { a.push(s); e.style.color='grey'; }
	a.sort();
	t.value=a.join(', ').replace(/^[\s,]+/g,'');
	ch_edit_pole(t,$num);
}

helps('alltags_".$idhelp."',\"<fieldset id='commentform'><legend>Тэги заметки $num</legend>".njsn($t)."</fieldset>\");
posdiv('alltags_".$idhelp."',-1,-1);
");
}


//=================================== help ===================================================================
if($a=='help') {
	$mod=$_REQUEST["mod"]; $mod=str_replace('..','',$mod);
	// if(c($mod)=='')
	$modfile=$filehost."site_mod/".$mod.".php";
	$s=file_get_contents($modfile);

	if(!preg_match("/\/\*(.*?)\*\//si",$s,$m)) idie("Для модуля <b>$mod</b> еще не написано справки, пинайте автора.");
	$s=c($m[1]);
	if(preg_match("/^([^\n]+)\n(.*?)$/si",$s,$m)) { $head=$m[1]; $s=c($m[2]); }
	if(preg_match("/(.*?)\n([^\n]*\{\_.*?)$/si",$s,$m)) { $s=c($m[1]); $prim=c($m[2]); }


	include $include_sys."_modules.php";
	$prim2=modules($prim);

	idie("<table width=600><td><center><b>$head</b></center><p>".nl2br($s)."
<p><i>например:</i><p>".nl2br(h($prim))."
<p><i>и получаем:</i><div style='border: 1px dashed #ccc'>".nl2br($prim2)."</div>

</td></table>","about: ".$mod.".php");
}

//=================================== loadhelp ===================================================================
if($a=='loadhelp') {
	$name=$_REQUEST["name"];
	include($file_template."help.php");
	include $include_sys."_modules.php";
	$s=modules($s);
	otprav("helps('editor-help',\"<fieldset id='commentform'><legend>Справка: редактор</legend><div style='width: 750px'>".njs($s)."</div></fieldset>\");");
}
//=================================== loadhelp ===================================================================

if($a=='bigfotoedit') { AD(); $i=RE0('i'); $p=RE0('p'); otprav("
send_bigfotoedit=function(){majax('editor.php',{a:'bigfotoedit_send',img:idd('bigfot".$p.'_'.$i."').href,num:".RE0('num').",i:$i,p:$p,txt:idd('message').value})};

helps('opechatku',\"<table border='0' cellspacing='0' cellpadding='0'><tr valign=top><td rowspan=2>"
."<textarea class='pravka_textarea' id='message' class='t' cols=\"+textarea_cols+\" rows=\"+page(s)+\">\""
."+vzyal('bigfottxt').replace(/<br>/gi,'\\n').replace(/<p>/gi,'\\n\\n').replace(/&quot;/gi,'\\\"').replace(/&lt;/gi,'<')"
.".replace(/&gt;/gi,'>')+\"</textarea>"
."<br><input type='button' style='font-size:6px;' value='Ctrl+Enter' onclick='send_bigfotoedit()'>"
."</td></tr><tr><td align=right valign=center>"
."<div class=fmn onclick=\\\"insert_n(idd('message'));\\\"></div>"
."<div class=fmcopy onclick=\\\"ti('message','\\251{select}')\\\"></div>"
."<div class=fmmdash onclick=\\\"ti('message','".chr(151)."{select}')\\\"></div>"
."<div class=fmltgt onclick=\\\"ti('message','\\253{select}\\273')\\\"></div></td></tr></table>\");
helps_cancel('opechatku',function(){clean('opechatku')});
idd('message').focus();
setkey('enter','ctrl',send_bigfotoedit,false);
");
}

if($a=='bigfotoedit_send') { AD(); $img=RE('img'); $num=RE0('num');
	$txt=str_replace(array("\n",'"'),array('<br>','&quot;'),h(RE('txt')));
	$body=ms("SELECT `Body` FROM `dnevnik_zapisi` WHERE `num`='$num'","_l",0);
		if(substr_count($body,$img)!=1) { $img=substr($img,strlen($httpsite));
		if(substr_count($body,$img)!=1) { $img=array_pop(explode('/',$img));
		if(substr_count($body,$img)!=1) idie('IMG not found'); }}

//	file_put_contents('__oldbody.txt',$body);

	$body=preg_replace("/(\s*".preg_quote($img,'/').")(.*?)(\n|_\})/s","$1 ".c($txt)."$3",$body);

	msq_update('dnevnik_zapisi',array('Body'=>e($body)),"WHERE `num`='$num'");

	$s="var txt=\"".njs($txt)."\";
zabil('bigfottxt',txt);
zabil('bigfott".RE0('p')."_".RE0('i')."',txt);
clean('opechatku');"; otprav($s);
	idie("img: $img www: ".$txt."<hr>".nl2br(h($body)));
}





// === test ===
if($a=='test') { AD(); 

/*
$s='';
	if(count($_FILES)>0) foreach($_FILES as $FILE) if(is_uploaded_file($FILE["tmp_name"])) {

        	$fname=h($FILE["name"]);
		$s.="<p> LOADED: $fname";

	} else { $s.=print_r($_FILES,1); }
*/

	idie(nl2br(h(print_r($_FILES,1))));

}



//=================================== editpanel ===================================================================
if($a=='foto') { AD(); 

// <script>onload = function() { tree("root") }</script>
// <p>My photo <span onclick='tree(\"root\")'>albums</span>:

$s="<div id='ooo'></div>
<ul class='Container' id='root'>
  <li class='Node IsRoot IsLast ExpandClosed'>
    <div class='Expand'></div>
    <div class='Content'>photo</div>
    <ul class='Container'>
    </ul>
  </li>
</ul>
";

otprav(	"
	loadScript('tree.js');
	loadCSS('tree.css');
	helps('foto',\"<fieldset id='commentform'><legend>фотоальбом</legend><div  style='width: 750px'>".njs($s)."</div></fieldset>\");
	tree('root');
");

}
//=================================== editpanel ===================================================================
if($a=='findreplace') { AD(); $id=RE('id');

$js="
var e=idd('$id'),ee=e.value.substring(e.selectionStart,e.selectionEnd);
idd('findreplace_fro').value=ee; idd('findreplace_rep').value=ee; idd('findreplace_rep').focus();

findreplace_view=function(i){
	if(i!=idd('findreplace_c').checked) { zabil('findreplace_f','FIND:'); zabil('findreplace_t','REPLACE:'); }
	else { zabil('findreplace_f','Find:'); zabil('findreplace_t','Replace:'); }
};

findreplace_go=function(){
idd('$id').value=idd('$id').value.replace(new RegExp(idd('findreplace_fro').value,'g'+(idd('findreplace_c').checked?'i':'')),idd('findreplace_rep').value);
clean('findreplace');
};
";


	$s="<table border=0><tr><td id='findreplace_f' style='width:5em;'>Find:</td><td><input id='findreplace_fro' type='text' value='' size=50></td></tr>
<tr><td id='findreplace_t'>Replace:</td><td><input id='findreplace_rep' type='text' value='' size=50></td></tr>
<tr><td><input id='findreplace_c' onmouseover='findreplace_view(1)' onmouseout='findreplace_view(0)' title='Case sensitive' type='checkbox'></td>
<td><input type='button' value='Go' onclick='findreplace_go()'></td></tr>
</table>";
	otprav("ohelpc('findreplace','Find/Replace',\"".njs($s)."\");".$js);
}


//=================================== editpanel ===================================================================
if($a=='loadpanel') { AD(); $idhelp=$_REQUEST['idhelp'];
	$id=$idhelp."_Body"; include($file_template."panel_editor.php");
	otprav("zabil('".h($idhelp."p")."','".njs($panel)."'); idd('".$id."').focus();");
}
//=================================== move ===================================================================
if($a=='savemove') { ADMA(); // $Date=$_REQUEST['DateOld']; $idhelp='move';

	$New=RE('DateNew');
	$Old=RE('DateOld');
	if($New=='' or $Old=='') idie("Неверная дата!");
	if(preg_match("/[^a-z0-9_-\/]+/si",$New)) idie("Неверное имя: '".h($New)."'");
	if($New==$Old) idie("Одинаковые?");

	if(intval("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($_REQUEST['DateNew'])."'".ANDC(),"_l",0))
		idie("Заметка с датой ".h($New)." уже существует!");

	$t=getmaketime($New);
	if($admin) msq_update('dnevnik_zapisi',array('Date'=>e($New),'DateUpdate'=>time(),'DateDate'=>$t[0],'DateDatetime'=>$t[1] ),"WHERE `Date`='".e($Old)."'".ANDC());

	redirect(get_link($Date)); // на нее и перейти
}

// ===================
if($a=='move') { ADMA(); $Date=RE('Date');

$s="<input type='hidden' id='move_DateOld' name='DateOld' value='".h($Date)."'><span style='border: 1px dotted #ccc'>".h($Date)."</span>
&mdash; <input class=t type='text' id='move_DateNew' name='DateNew' value='".h($Date)."' maxlength='128' size='20'>
<input type=submit value='Move' onclick=\"majax('editor.php',{a:'savemove',DateOld:idd('move_DateOld').value,DateNew:idd('move_DateNew').value})\">";

$s="
	helps('move',\"<fieldset id='commentform'><legend>Перенос заметки ".h($p['Date'])."</legend>".njsn($s)."</fieldset>\");
	idd('move_DateNew').focus();
";

otprav($s);
}
//=================================== fileimport ===================================================================

if($a=='fileimport') { AD(); $file=RE('id');$Date=$file;

	// взять файл
	if(!is_file($filehost.$file)) otprav('');
	$s=file_get_contents($filehost.$file);
	


	// подогнать кодировку
	$cp=preg_replace("/^.*<meta\shttp-equiv=[\'\"]Content-Type[\'\"][^>]+charset=([0-9a-z\-]+).*$/si","$1",$s);
	if($cp!=$s && $cp!=$wwwcharset) $s=iconv($cp,$wwwcharset."//IGNORE",$s);

	// убрать говны
	$s=trim(str_replace("\r","",$s));
	$s=preg_replace("/<html.*?>/si","",$s);
	$s=preg_replace("/<body.*?>/si","",$s);

	// попробовать найти заголовок
	if(($Header=preg_replace("/^.*<title>([^<>\n]+)<\/title>.*$/si","$1",$s))==$s) $Header=$wwwhost;
	$s=preg_replace("/<title>.*?<\/title>/si","",$s);

	$s=str_ireplace(array("</html>","</body>","</head>","<head>"),"",$s);

	$opt=array("autoformat"=>"no","template"=>"blank");

// своя обработка









//	idie("codepage='$cp'");
//    [Access] => all
//    [opt] => a:5:{s:12:"Comment_view";s:3:"off";s:7:"autokaw";s:2:"no";s:10:"autoformat";s:2:"no";s:8:"template";s:5:"blank";s:13:"Comment_media";s:3:"all";}

	if(($c=ms("SELECT `count` FROM `lleo`.`site_count` WHERE `lang`='".e($wwwhost.$file)."'",'_l',0))===false) $c=0;

	$p=array(
//		'acn'=>$acn,
		'view_counter'=>$c,
		'Date'=>$Date,
		'Header'=>$Header,
		'Body'=>$s,
//		'num'=>0,
		'opt'=>ser($opt)
	);

	msq_add('dnevnik_zapisi',arae($p));
	$num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'","_l",0);
	if(!$num) idie("Error!");
	$p['num']=$num; $idhelp='editor'.$num; 
	// переименовать файл в *.old
	rename($filehost.$file,$filehost.$file.'.old');

        edit_textarea($p,RE("clo")===false?'':"clean('".e(RE("clo"))."');");
}
//=================================== новую заметку ===================================================================
#if($a=='createform') { AD(); $num=0; $idhelp='editor0'; $Date=h(RE('Date'));
#	edit_textarea(
#		array('Header'=>RE('header'),'Body'=>RE('body'),'num'=>0),
#		RE("clo")===false?'':"clean('".e(RE("clo"))."');"
#	);
#} 
//=================================== новую заметку ===================================================================
if($a=='newform') { $acn=ADMA();
	$i=0; while(ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'".ANDC(),"_l",0)!=0) { $Date=date("Y/m/d").'_'.sprintf("%02d", ++$i); }
	// $hid=RE('hid');
	$num=0; $idhelp='editor0';
	edit_textarea(
		array('Header'=>'','Body'=>'','num'=>0,'acn'=>RE0('acn')),
		RE("clo")===false?'':"clean('".e(RE("clo"))."');"
	);
} 
//=================================== запросили форму ===================================================================
if($a=='editform_new') { $acn=ADMA();
	$loc=rpath(substr(RE('loc'),strlen($httphost)));
	if(($p=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($loc)."'".ANDC(),"_l",0))!==false) idie("Already exist: ".h($loc));
	if(is_file($site_module.strtoupper($loc).".php")) $Body='{_'.strtoupper($loc).':_}'; else $Body='';
	$p=array('Access'=>'admin','DateUpdate'=>time(),'Date'=>e($loc),'Body'=>$Body,'acn'=>$acn);
	msq_add('dnevnik_zapisi',$p);
	$num=mysql_insert_id();
	$a='editform';
}

if($a=='editform') { $acn=ADMA();
	if($num) $p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `num`='$num'".ANDC(),"_1",0);
	else { $p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `Date`='".e(RE('Date'))."'".ANDC(),"_1",0); $num=$p['num']; }
	if($p===false) idie("Отсутствует заметка #$num ".RE('Date')
."<p><div class='ll' onclick=\"majax('editor.php',{a:'editform_new',loc:window.location.href})\">Создать?</div>");
	// $p=mkzopt($p);
	// dier($p);
	edit_textarea($p,$s);
}
//====================================

function edit_textarea($p,$majax='') { global $Date, $www_design,$idhelp,$filehost,$autosave_count,$num,$zopt_a,$editor_width,$editor_height; $s='';

$acn=$p['acn'];

if(!$num) {
	if(empty($Date)) { $Date=RE('Date'); if(empty($Date)) $Date=date("Y/m/d"); }
	$s.="<input class='t' type='text' name='Date' id='".$idhelp."_Date' value='".h($Date)."' maxlength='128' size='20'><br>";
}

$s.=njsn("
<img alt='".LL('Editor:newz')."' class=l onclick=\"majax('editor.php',{acn:'$acn',a:'newform',hid:hid,clo:'".$idhelp."'})\" src='".$www_design."e3/filenew.png' alt='new'>
<img alt='".LL('Editor:change_data')."' class=l onclick=\"majax('editor.php',{acn:'$acn',a:'move',Date:'".h($p['Date'])."'})\" src='".$www_design."e3/redo.png' alt='move'>
<img alt='".LL('Editor:delz')."' class=l onclick=\"if(confirm('".LL('confirm_del')."')) majax('editor.php',{acn:'$acn',a:'delete',num:$num});\" src='".$www_design."e3/remove.png' alt='delete'>
<div id='".$idhelp."p' style='display:inline'><img alt='".LL('Editor:show_panel')."' class=l onclick=\"majax('editor.php',{acn:'$acn',a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'></div>

<div>
<input id='".$idhelp."_head' onchange='ch_edit_pole(this,$num)' class='t' type='text' name='Header' value='".h($p['Header'])."' maxlength='255'")
." style='width:\"+get_edit_width()+\"px'>"
."<br><textarea onkeydown=\\\"keydownc(this,$num)\\\" class='t' id='".$idhelp."_Body' name='Body' "
."style='width:\"+get_edit_width()+\"px; height:\"+get_edit_height()+\"px'>"
.njsn(h($p["Body"])."</textarea>
</div>

<div class=r>
");

// $admincolors=array(array('admin','ledred.png'),array('podzamok','ledyellow.png'),array('all','ledgreen.png'));

$s.=njsn(ADMINSET($p));


$opt=unser($p['opt']); ksort($opt);
if(sizeof($opt)<sizeof($zopt_a)) $s.="<div id='".$idhelp."_extopt' style='margin-left:16px;display:inline;margin-right:16px'><img src='".$www_design."e3/system.png' alt='".LL('Editor:settings')."'"
." onclick=\\\"majax('editor.php',{acn:$acn,a:'settings_panel',num:$num})\\\"></div>";

$s.="<div id='".$idhelp."_extautopost' style='display:inline;margin-right:16px'><img src='".$www_design."e3/mail_forward.png' alt='".LL('Editor:autopost')."'"
." onclick=\\\"majax('editor.php',{acn:$acn,a:'autopost_panel',num:$num})\\\"></div>"

."<div style='display:inline;vertical-align:top;' class='br'>".LL('Editor:sym',"<span id='".$idhelp."_nsym'>".strlen($p['Body'])."</span>")."</div>";

if(!empty($GLOBALS['admin_ljuser'])&&!empty($GLOBALS['admin_ljpass'])) $s.=
"<div style='display:inline;margin-right:16px;margin-left:16px'><img src='".$www_design."e3/post-entry.gif' alt='".LL('Editor:ljpost')."'"
." onclick=\\\"if(confirm('".LL('ljpost:confirm')."')) majax('editor.php',{acn:$acn,a:'ljpost',num:$num})\\\"></div>";

$s.=pokaji_opt($opt,0);

// -- тэги --------------
$tt=ms("SELECT `tag` FROM `dnevnik_tags` WHERE `num`='$num' ORDER BY `tag`","_a",0);
$t=''; foreach($tt as $l) $t.=$l['tag'].', '; $t=trim($t,', ');
$s.=njsn("<div class=r>"
."<span alt='".LL('Editor:tags_alt')."'"
." class=l onclick=\"majax('editor.php',{acn:$acn,a:'tags',num:$num,mytags:idd('tags_".$idhelp."').value})\">".LL('Editor:tags')."</span>&nbsp;"
."<input onchange='ch_edit_pole(this,$num)' class='t' type='text' name='tags' id='tags_".$idhelp."' value='".h($t)."' ")
."style='width:\"+get_edit_width()+\"px'></div>";
//-----------------------

$s.=njsn("<div><input title='".LL('shift+Enter')."' type='button' value='".LL('Save')."' onclick='save_and_close()'></div>");


// спасибо iland_slc за советы
$s="
get_edit_width=function(){ return Math.min(Math.floor(95*getWinW()/100),".(isset($editor_width)?$editor_width:999999)."); };
get_edit_height=function(){ return Math.min(Math.floor(90*getWinH()/100),".(isset($editor_height)?$editor_height:999999)."); };

ljpostpost=function(){
	ohelpc('ljpost_post','LJ-post','<iframe width=500 height=200 id=iframeljpost name=iframeljpost></iframe>');
	var ara=get_pole_ara('".$idhelp."'); if(ara===false) return ara;
	ara['a']='ljpost';
	postToIframe(ara,'http://lleo.me/blog/ajax/editor.php','iframeljpost');
}

if(f5s||jog) {
interval_clipboard=function(e){
	if(!idd(e+'_Body')) { eval('clearInterval(intervalID_'+e+')'); return; }

	var m=f_read('clipboard_mode'); if(m=='') return;

	if(m=='Copy link') {
		ti(e+'_Body',\"<a href='\"+f_read('clipboard_link')+\"'>{select}\"+f_read('clipboard_text')+\"</a>\");
	} else if(m=='plain') {
		ti(e+'_Body',f_read('clipboard_text'));
	} else alert('unknown mode: '+m);

	f_save('clipboard_mode','');
}; var intervalID_".$idhelp."=setInterval(\"interval_clipboard('".$idhelp."')\",1000);
}

save_and_close=function(){
	var ara=get_pole_ara('".$idhelp."'); if(ara===false) return ara;
	ara['a']='polesend_all'; ara['num']=".$num.";
	ara['acn']='".$p['acn']."';
	majax('editor.php',ara);
	if(idd('alltags_".$idhelp."')) clean('alltags_".$idhelp."');
	return false;
};

var keydowncount=0;

ch_edit_pole=function(e,num){ if(typeof e.defaultValue=='undefined' || e.value!=e.defaultValue){ edit_polesend(e.name,e.value,num,0); e.defaultValue=e.value;}};
edit_polesend=function(n,v,num,clo){ majax('editor.php',{a:'polesend',name:n,val:v,num:num,clo:clo,acn:'$acn'}); };

keydownc=function(e,num){
keydowncount++; if(keydowncount>".$autosave_count."){ 
	zabil('".$idhelp."_nsym',idd('".$idhelp."_Body').value.length);
keydowncount=0; edit_polesend(e.name,e.value,num,1); } };

/*majax_err=1;*/
helpc('".$idhelp."',\"<fieldset id='commentform'><legend>Заметка ".h($p['Date'])."</legend>".$s."</fieldset>\");
idd('".$idhelp."_Body').focus();

setkey('esc','',function(e){ var e=idd('".$idhelp."_Body'); if(e.value==e.defaultValue || confirm('".LL('exit_no_save')."')) clean('".$idhelp."'); },false);
setkey('enter','ctrl',save_and_close,false);
";

otprav($s);
}


//----------- autopost panel --------------
if($a=='autopost_panel') { AD();
//	$opt=unser(ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='$num'","_l",0));
//	$opt2=mkzopt($opt); ksort($opt2);
//	foreach($opt as $n=>$l) unset($opt2[$n]);
	otprav("zabil('".$idhelp."_extautopost',\"<br><fieldset><legend>autopost</legend>Under constructions</fieldset><p>\");");
}





//----------- setting panel --------------
if($a=='settings_panel') { $acn=ADMA();
	$opt=unser(ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='$num'".ANDC(),"_l",0));
	$opt2=mkzopt($opt); ksort($opt2);
	foreach($opt as $n=>$l) unset($opt2[$n]);
	otprav("zabil('".$idhelp."_extopt',\"".pokaji_opt($opt2)."\");");
}
//----------- setting panel --------------

if($a=='ch_dostup') { $acn=ADMA(); /*global $admincolors;*/ // смена доступа к заметке
	$d=array_pop(explode('/',RE('d')));
	foreach($admincolors as $n=>$l) { if($l[1]==$d) { $k=$admincolors[(++$n)%3];
		msq_update('dnevnik_zapisi',array('Access'=>$k[0]),"WHERE `num`='$num'".ANDC());
		if($k[0]=='all') { $pad=0; $col='transparent'; } else { $pad=10; $col=$GLOBALS['podzamcolor']; }
		otprav("
doclass('".$num."_adostup',function(e){e.src='".$GLOBALS['www_design']."e3/".$k[1]."'});
var e=idd('Body_".$num."'); if(e){ e.style.padding='".$pad."pt'; e.style.backgroundColor='".$col."'; }
		");
		}
	}
	idie("error");
}
//=================================== удаление заметки ===================================================================

if($a=='delete') { $acn=ADMA();
	// а его ли это num?
	if(!msq("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `num`='$num'".ANDC(),'_l',0)) idie('Fuxk!');
	msq("DELETE FROM `dnevnik_zapisi` WHERE `num`='$num'".ANDC()); // удалить запись
	msq("DELETE FROM `dnevnik_comm` WHERE `DateID`='$num'"); // удалить к ней все комментарии
	msq("DELETE FROM `dnevnik_posetil` WHERE `url`='$num'"); // удалить статистику ее посетителей
	msq("DELETE FROM `dnevnik_link` WHERE `DateID`='$num'"); // удалить статистику заходов по ссылкам
	msq("DELETE FROM `dnevnik_search` WHERE `DateID`='$num'"); // удалить статистику заходов с поисковиков
	redirect($httphost);
}
//=================================== запросили форму ===================================================================
if($a=='polesend_all') { $acn=ADMA(); $e=explode(' ',trim(RE('names')));

	if($num) $p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `num`='$num'".ANDC(),"_1",0); else $p=array('opt'=>'');
	$opt=unser($p['opt']);
	foreach($opt as $n=>$l) if(!isset($zopt_a[$n])) unset($opt[$n]); // удалить некондиционные метки

	unset($e['Body']); foreach($e as $name) { $val=str_replace("\r",'',RE($name));
		if($name=='tags') { save_tags(RE($name)); continue; }
		if(!isset($zopt_a[$name])) { $p[$name]=$val; continue; }
		// опция
		if($val=='default'
 or $zopt_a[$name][1]=='s' && ( c($val)=='' or $val==$zopt_a[$name][0]) // дефолтная строка
)
		{ if(isset($opt[$name])) unset($opt[$name]); } else { $opt[$name]=$val; }
	}

	$p['DateUpdate']=time();
	$p['opt']=ser($opt); // опции

	// Body
	$l=str_replace("\r",'',RE('Body'));
	$po=mkzopt($p); if($po["autokaw"]!="no") $l=ispravkawa($l); // если разрешено обработать кавычки и тире
	$p['Body']=$l;

	// save
	if($num) msq_update('dnevnik_zapisi',arae($p),"WHERE `num`='$num'".ANDC());
	else { //== новую заметку =====
		$d=c($p['Date']);
		if(preg_match("/[^0-9a-z\-\_\.\/]+/si",$d) or empty($d) ) {
$d=preg_replace("/[^0-9a-z\-\_\.\/]+/si",'',$d);
otprav("
idd('".$idhelp."_Date').value=\"".$d."\";
salert(\"".njs(LL('Editor:wrong_data',$httphost))."\")"
);
		}
		$t=getmaketime($d);
		if(0!=ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($d)."'".ANDC(),"_l",0)) {
			$r=0; while(0!=ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($d.'_'.(++$r))."'".ANDC(),"_l",0)){}
otprav("idd('".$idhelp."_Date').value=\"".h($d.'_'.$r)."\";
salert(\"".LL('Editor:new_exist',get_link($d))."\");
");
		}
		$p=array_merge($p,array('acn'=>$acn,'Access'=>'admin','DateUpdate'=>time(),'DateDate'=>$t[0],'DateDatetime'=>$t[1]));
		msq_add('dnevnik_zapisi',arae($p));
		redirect(get_link($d)); // перейти
	}
	// idie("#".mysql_insert_id());

	if(RE('clo')==0) $s.="clean('".$idhelp."');"; else $s="salert('".LL('saved')."',500);";
	$p=mkzopt($p);
	include_once $include_sys."_onetext.php";
	include_once $include_sys."_modules.php";
	$s.="zabil('Body_$num',\"".njs(onetext($p))."\"); zabil('Header_$num',\"".njs($p['Header'])."\");";
	otprav($s);
}
// -------------------
if($a=='polesend') { $acn=ADMA(); $val=RE('val'); $name=RE('name');
	$val=str_replace("\r",'',$val);

	if($name=='tags') { save_tags($val); otprav(''); }

	if($name=='Body') {
		if($num==0) { put_last_tmp($val); otprav(''); } else { del_last_tmp(); } // сохранять в tmp текст для новых
		$p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `num`='$num'".ANDC(),"_1",0); $p=mkzopt($p);
		if($p["autokaw"]!="no") $val=ispravkawa($val); // если разрешено обработать кавычки и тире
		msq_update('dnevnik_zapisi',array('Body'=>e($val),'DateUpdate'=>time()),"WHERE `num`='$num'".ANDC());
		include_once $include_sys."_onetext.php";
		include_once $include_sys."_modules.php";
		$p['Body']=$val; $s=onetext($p);
		$s="idd('Body_$num').innerHTML=\"".njs($s)."\"; salert('".LL('saved')."',500);
idd('Body".$num."_Body').focus();";
		// if(RE('clo')==0) $s.="clean('".$idhelp."');";
		otprav($s);
	}

	if($name=='' or $num==0) otprav(''); //idie('Неверные данные!');

	if(isset($zopt_a[$name])) { // это опция?
		$opt=unser(ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='$num'".ANDC(),"_l",0));
		if($val=='default'
or $zopt_a[$name][1]=='s' && ( c($val)=='' or $val==$zopt_a[$name][0]) // дефолтная строка
) { // по дефолту
			if(isset($opt[$name])) unset($opt[$name]); // если было - сбросить
			else otprav("salert('".LL('saved')."',200)"); // "salert(\"not change: ".h($name)."='".h($val)."'\",500)"); // если и не было - выйти
		} else $opt[$name]=$val;
		$ara=array('opt'=>e(ser($opt)));
	} else { $ara=array(e($name)=>e($val));	}

	$ara['DateUpdate']=time();
	msq_update('dnevnik_zapisi',$ara,"WHERE `num`='$num'".ANDC());

	if($name=='Header') otprav("idd('Header_".$num."').innerHTML=\"".njs($val)."\"");
	if($name=='Body') otprav('');
	otprav("salert('".LL('saved')."',200)"); // otprav("salert(\"".h($name)."='".h($val)."'\",500)");
}
// -------------------
function save_tags($val) { global $msqe,$num;
	if(!$num) return; // не записывать для нулевой заметки
	msq("DELETE FROM `dnevnik_tags` WHERE `num`='$num'"); // удалить все тэги этой заметки
	$p=explode(',',$val); foreach($p as $l) { $l=c($l); if($l!='') msq_add('dnevnik_tags',array('num'=>$num,'tag'=>e(h($l)))); }
	if(stristr($msqe,'Duplicate')) $msqe=''; // ошибка дублей - не ошибка
}

function pokaji_opt($opt,$def=1) { global $num,$zopt_a; $s=''; $i=0;
	foreach($opt as $n=>$v) { if(!isset($zopt_a[$n])) continue; $l=$zopt_a[$n];
		if($def) $val=($v!=$l[0]?$v:'default'); else $val=$v;
	$s.=($i++?"<br>":'').LL('zopt:'.$n)." : ";


	if($n=='template') {
		// выяснить о модулях
		$inc=glob($GLOBALS['filehost']."template/*.html"); $ainc=array('default'=>'&mdash;'); foreach($inc as $l) { $l=preg_replace("/^.*?\/([^\/]+)\.html$/si","$1",$l); $ainc[$l]=$l; }
		$s.=selecto('template',$val,$ainc,"class='r' onchange='ch_edit_pole(this,$num)' name");
	} else { $m=explode(' ',$l[1]);
		if($m[0]=='s') $s.="<input type='text' maxlength='".$l[2]."' size='".min($l[2],64)."' name='".$n."' class='r' onchange='ch_edit_pole(this,$num);' value=\\\"".h(isset($opt[$n])?$opt[$n]:'')."\\\">";
		else {
			$a=array('default'=>'&mdash;'); foreach($m as $j) $a[$j]=LL('zopt:'.$n.':'.$j);
			$s.= selecto($n,$val,$a,"class='r' onchange='ch_edit_pole(this,$num)' name");
			$s.= " &nbsp; ".LL('zopt:default')." &laquo;".LL('zopt:'.$n.':'.$l[0])."&raquo;";
		}
	}
	}

return ($s==''?'':"<br><fieldset><legend>options</legend>$s</fieldset><p>");
}


//===============
function prepare_Body($p) { global $httphost,$httpsite,$include_sys;
	$GLOBALS['article']=$p;
	include_once $include_sys."_modules.php";
        $s=modules($p['Body']); // процедуры site
	// произвести автоформатирование
	if($p['autoformat']!='no') $s=str_replace(array("\n\n","\n"),($p['autoformat']=='p'?array("<p>","<br>"):array("<p class=pd>","<p class=d>")),"\n\n".str_replace("\n ","\n<p class=z>","\n".$s));

$s=str_ireplace(array( // заменить классы на стили
'<p class=d>',
'<p class=pd>',
'<p class=name>',
'<p class=podp>',
'<p class=z>',   
'<p class=epigraf>',
'<p class=epigrafp>'
),array(
'<p style="text-align:justify;text-indent:5%;margin-top:0pt;margin-bottom:0pt;">',
'<p style="text-align:justify;text-indent:5%;margin-top:2%;margin-bottom:0pt;">',
'<p style="text-indent:0pt;margin-top:4%;margin-bottom:6%;text-align:center;font-weight:bold;font-size:150%;">',
'<p style="text-indent:0pt;margin-top:30pt;margin-bottom:12%;text-align:right;font-style:italic;">',
'<p style="text-indent:0pt;margin-top:4%;margin-bottom:4%;text-align:center;font-weight:bold;font-size:100%;">',
'<p style="text-indent:0pt;text-align:justify;margin-top:10pt;margin-bottom:0pt;margin-right:4%;margin-left:60%;font-size:80%;">',
'<p style="text-indent:0pt;text-align:right;margin-top:0pt;margin-bottom:4%;margin-right:4%;margin-left:60%;font-size:80%;font-style:italic;">'
),$s);

	$mydir=$httphost.substr($p['Date'],0,(strlen($p['Date'])-strlen(strrchr($p['Date'],"/")))+1);
        $s=preg_replace("/(<[^>]+src\=[\'\"]*)(\/)/si","$1".$httpsite."/",$s); // картинки поставить на места
        $s=preg_replace("/(<[^>]+src\=[\'\"]*)([^>\s\:]{6})/si","$1".$mydir."$2",$s); // картинки поставить на места

return $s;
}

?>