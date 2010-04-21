<?php // Авторизация пользователей
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");


$autosave_count = 200; // 128; // через сколько нажатий кнопки автозапись

$num=intval($_REQUEST["num"]); $idhelp='editor'.$num;
$a=$_REQUEST["a"];


//function nl2br2($s) { return str_replace(array("\n\n","\n"),array("<p>"," "),$s); }

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
<p><i>например:</i><p>".nl2br($prim)."
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





if(!$admin) idie("Надо залогиниться админом!");




// === test ===
if($a=='test') {

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
if($a=='foto') {

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
if($a=='loadpanel') { $idhelp=$_REQUEST['idhelp'];
	$id=$idhelp."_textarea"; include($file_template."panel_editor.php");
	otprav("idd('".h($idhelp."p")."').innerHTML='".njs($panel)."'; loadScript('pins.js'); idd('".$id."').focus();");
}
//=================================== move ===================================================================
if($a=='savemove') { // $Date=$_REQUEST['DateOld']; $idhelp='move';

	$New=$_REQUEST['DateNew'];
	$Old=$_REQUEST['DateOld'];
	if($New=='' or $Old=='') idie("Неверная дата!");
	if($New==$Old) idie("Одинаковые?");

	if(intval("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($_REQUEST['DateNew'])."'","_l",0))
		idie("Заметка с датой ".h($New)." уже существует!");

	$t=getmaketime($New);
	if($admin) msq_update('dnevnik_zapisi',array('Date'=>e($New),'DateUpdate'=>time(),'DateDate'=>$t[0],'DateDatetime'=>$t[1] ),"WHERE `Date`='".e($Old)."'");

	redirect(get_link($Date)); // на нее и перейти
}

// ===================

if($a=='move') { $Date=$_REQUEST['Date'];

$s="<input type='hidden' id='move_DateOld' name='DateOld' value='".h($Date)."'><span style='border: 1px dotted #ccc'>".h($Date)."</span>
&mdash; <input class=t type='text' id='move_DateNew' name='DateNew' value='".h($Date)."' maxlength='128' size='20'>
<input type=submit value='Move' onclick=\"majax('editor.php',{a:'savemove',DateOld:idd('move_DateOld').value,DateNew:idd('move_DateNew').value})\">";

$s="
	helps('move',\"<fieldset id='commentform'><legend>Перенос заметки ".h($p['Date'])."</legend>".njsn($s)."</fieldset>\");
	idd('move_DateNew').focus();
";

otprav($s);
}

//=================================== новую заметку ===================================================================
if($a=='savenew') {

$Date=$_REQUEST['Date']; if($Date=='') idie("Ошибка: неверная дата.");

$t=getmaketime($Date);

if($_REQUEST["autokaw"]=="true") { $_REQUEST["autokaw"]='no';
	$_REQUEST["Body"]=ispravkawa($_REQUEST["Body"]); // если разрешено обработать кавычки и тире
} else $_REQUEST["autokaw"]='auto';

$ara=array(
	'Date'		=> e($Date),
	'DateDate'	=> $t[0],
	'DateDatetime'	=> $t[1],
	'Header'	=> e($_REQUEST['Header']),       	
	'Body'		=> e($_REQUEST['Body']),
	'Access'	=> e($_REQUEST['Access']),
	'autoformat'	=> e($_REQUEST['autoformat']),
	'autokaw'	=> e($_REQUEST['autokaw']),
	'template'	=> e(($_REQUEST['template']!=''?$_REQUEST['template']:'blog')),
	'Comment_view'	=> (strstr(file_get_contents($filehost."template/".str_replace('..','.',$_REQUEST['template']).".html"),'{_COMENTS:')?e($_REQUEST['Comment_view']):'off'),
	'Comment_write'	=> e($_REQUEST['Comment_write']),
	'Comment_screen'=> e($_REQUEST['Comment_screen']),
	'DateUpdate'=>time()
);

if(intval(ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".$ara['Date']."'","_l",0))) idie("Запись с этим названием уже есть!");

if($admin) msq_add('dnevnik_zapisi',$ara);

redirect(get_link($Date)); // на нее и перейти
}


//=================================== новую заметку ===================================================================
if($a=='newform') {

if(isset($_REQUEST['Date'])) $Date=h($_REQUEST['Date']); else $Date=date("Y/m/d");

$i=0;
while(ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'","_l",0)!=0) { $Date=date("Y/m/d").'_'.sprintf("%02d", ++$i); }

$hid=intval($_REQUEST['hid']); if(!$hid) idie('Ошибка в движке: Лео, ты забыл установить hid!');
$idhelp='editnew'.$hid;

$p=unserialize(file_get_contents($GLOBALS['hosttmp'].'zapisi.set')); // взять настройки по умолчанию

// idie('---'.nl2br(h(print_r($p,1))));

$s="
<input class=t type='text' id='".$idhelp."_Date' name='Date' value='".h($Date)."' maxlength='128' size='20'>

<div id='".$idhelp."p' style='display:inline'><img class=l onclick=\"majax('editor.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'></div>

<br><input class=t type='text' id='".$idhelp."_Header' name='Header' value='".h($p["Header"])."' maxlength='255' size=".$GLOBALS['editor_cols']."> <span class=br>".strlen($p['Body'])." букв</span>
<br><textarea onkeydown=\"keydownc('Body',this.value,$num)\" class=t id='".$idhelp."_Body' cols=".$GLOBALS['editor_cols']." rows=".$GLOBALS['editor_rows'].">".h($p["Body"])."</textarea>

<div class=r>доступ: ".selecto('Access',$p['Access'],array('admin'=>"никому",'podzamok'=>"подзамок",'all'=>"всем"),"class=r id='".$idhelp."_Access' name")."
автоформат: ".selecto('autoformat',$p['autoformat'],array('p'=>"p/br",'no'=>"нет",'pd'=>"class=pd"),"class=r id='".$idhelp."_autoformat' name")."
не менять кавычки: <input type=checkbox id='".$idhelp."_autokaw' name=autokaw".($p["autokaw"]=='no'?" checked":"").">";

// выяснить о модулях
$inc=glob($filehost."template/*.html"); $ainc=array(''=>'- нет -'); foreach($inc as $l) { $l=preg_replace("/^.*?\/([^\/]+)\.html$/si","$1",$l); $ainc[$l]=$l; }
$s.="template: ".selecto('template',$p['template'],$ainc,"class=r id='".$idhelp."_template' name");

// нужны опции комментов
if(strstr(file_get_contents($filehost."template/".$p['template'].".html"),'{_COMENTS:')) 
$s.="<br>
Комментарии показывать: ".selecto('Comment_view',$p['Comment_view'],array('timeload'=>"поначалу",'load'=>"кнопку",'off'=>"нет",'rul'=>"важные",'on'=>"все"),
"class=r id='".$idhelp."_Comment_view' name")."
принимать: ".selecto('Comment_write',$p['Comment_write'],array('timeoff'=>"поначалу",'on'=>"вечно от всех",'off'=>"нет",'friends-only'=>"вечно от друзей",'login-only'=>"вечно от логинов",'login-only-timeoff'=>"поначалу от логинов"),
"class=r id='".$idhelp."_Comment_write' name")."
открывать: ".selecto('Comment_screen',$p['Comment_screen'],array('open'=>"всех",'friens-open'=>"друзей",'screen'=>"скрывать"),
"class=r id='".$idhelp."_Comment_screen' name")."
тип: ".selecto('Comment_tree',$p['Comment_tree'],array('1'=>"форум",'0'=>"гостевая"),
"class=r onchange='ch_edit_pole(this,$num)' name");

$s.="<br><input type=submit value='Save' onclick=\"edit_savenew('".$idhelp."')\">"; // и кнопку!

// сортировка: ".selecto('comments_order',$p['comments_order'],array('normal'=>"нет",'allrating'=>"сборная",'rating'=>"тупая") )."

$s="hid++;

edit_savenew=function(idhelp){
	var nara=['Date','Header','Body','Access','autoformat','autokaw','template','Comment_view','Comment_write','Comment_screen'];
	var ara={a:'savenew',idhelp:idhelp};
	for(var l in nara) { l=nara[l]; var ll=idhelp+'_'+l; if(idd(ll)) ara[l]=idd(ll).value; }
	majax('editor.php',ara);
};

helps('".$idhelp."',\"<fieldset id='commentform'><legend>Новая статья ".h($p['Date'])."</legend>".njsn($s)."</fieldset>\");
posdiv('".$idhelp."',-1,-1);
idd('".$idhelp."_textarea').focus();
";

if(isset($_REQUEST["clo"])) $s="clean('".h($_REQUEST["clo"])."');".$s;

otprav($s);

}

//=================================== запросили форму ===================================================================
if($a=='editform') {

$p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `num`='$num'","_1",0); if($p===false) idie("Отсутствует заметка #$num");

$s="
<img class=l onclick=\"majax('editor.php',{a:'newform',hid:hid,clo:'".$idhelp."'})\" src='".$www_design."e3/filenew.png' alt='new'>
<img class=l onclick=\"majax('editor.php',{a:'move',Date:'".h($p['Date'])."'})\" src='".$www_design."e3/redo.png' alt='move'>
<img class=l onclick=\"if(confirm('Вы точно хотите удалить эту заметку?')) majax('editor.php',{a:'delete',num:$num});\" src='".$www_design."e3/remove.png' alt='delete'>
<div id='".$idhelp."p' style='display:inline'>
<img class=l onclick=\"majax('editor.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'>
".($admin?'':"<font color=red>Вы не админ: в демо-режиме изменения не запишутся.</font>")."
</div>

<br><input onchange='ch_edit_pole(this,$num)' class=t type='text' name='Header' value='".h($p["Header"])."' maxlength='255' size=".$GLOBALS['editor_cols']."> <span class=br>".strlen($p['Body'])." букв</span>
<br><textarea onkeydown=\"keydownc('Body',this.value,$num)\" class=t id='".$idhelp."_textarea' cols=".$GLOBALS['editor_cols']." rows=".$GLOBALS['editor_rows'].">".h($p["Body"])."</textarea>

<div class=r>доступ: ".selecto('Access',$p['Access'],array('admin'=>"никому",'podzamok'=>"подзамок",'all'=>"всем"),"class=r onchange='ch_edit_pole(this,$num)' name")."
автоформат: ".selecto('autoformat',$p['autoformat'],array('p'=>"p/br",'no'=>"нет",'pd'=>"class=pd"),"class=r onchange='ch_edit_pole(this,$num)' name")."
не менять кавычки: <input onchange='edit_polesend(this.name,this.checked,".$num.")' type=checkbox name=autokaw".($p["autokaw"]=='no'?" checked":"").">";

// выяснить о модулях
$inc=glob($filehost."template/*.html"); $ainc=array(''=>'- нет -'); foreach($inc as $l) { $l=preg_replace("/^.*?\/([^\/]+)\.html$/si","$1",$l); $ainc[$l]=$l; }
$s.="template: ".selecto('template',$p['template'],$ainc,"class=r onchange='ch_edit_pole(this,$num)' name");

// нужны ли опции комментов
if($_REQUEST["comments"]==1 or strstr(file_get_contents($filehost."template/".$p['template'].".html"),'{_COMENTS:')) $s.="<br>
Комментарии показывать: ".selecto('Comment_view',$p['Comment_view'],array('timeload'=>"поначалу",'load'=>"кнопку",'off'=>"нет",'rul'=>"важные",'on'=>"все"),
"class=r onchange='ch_edit_pole(this,$num)' name")."
принимать: ".selecto('Comment_write',$p['Comment_write'],array('timeoff'=>"поначалу",'on'=>"вечно от всех",'off'=>"нет",'friends-only'=>"вечно от друзей",'login-only'=>"вечно от логинов",'login-only-timeoff'=>"поначалу от логинов"),
"class=r onchange='ch_edit_pole(this,$num)' name")."
открывать: ".selecto('Comment_screen',$p['Comment_screen'],array('open'=>"всех",'friens-open'=>"друзей",'screen'=>"скрывать"),
"class=r onchange='ch_edit_pole(this,$num)' name")."
тип: ".selecto('Comment_tree',$p['Comment_tree'],array('1'=>"форум",'0'=>"гостевая"),
"class=r onchange='ch_edit_pole(this,$num)' name");

// -- тэги --------------
$tt=ms("SELECT `tag` FROM `dnevnik_tags` WHERE `num`='$num' ORDER BY `tag`","_a",0);
$t=''; foreach($tt as $l) $t.=$l['tag'].', '; $t=trim($t,', ');
$s.="<br><span class=l onclick=\"majax('editor.php',{a:'tags',num:$num,mytags:idd('tags_".$idhelp."').value})\">тэги</span> через запятую: <input onchange='ch_edit_pole(this,$num)' class=t type='text' name='tags' id='tags_".$idhelp."' value='".h($t)."' size=".$GLOBALS['editor_cols'].">";
//-----------------------

$s.="<br><input type=submit value='Save' onclick=\"if(idd('alltags_".$idhelp."')) clean('alltags_".$idhelp."'); edit_polesend('Body',idd('".$idhelp."_textarea').value,".$num.")\">";

// сортировка: ".selecto('comments_order',$p['comments_order'],array('normal'=>"нет",'allrating'=>"сборная",'rating'=>"тупая") )."

$s="
var keydowncount=0;
ch_edit_pole=function(e,num){ edit_polesend(e.name,e.value,num,0) };
edit_polesend=function(n,v,num,clo){ majax('editor.php',{a:'polesend',name:n,val:v,num:num,clo:clo}); };
keydownc=function(n,v,num){ keydowncount++; if(keydowncount>".$autosave_count.") { keydowncount=0; edit_polesend(n,v,num,1); } };

helps('".$idhelp."',\"<fieldset id='commentform'><legend>Заметка ".h($p['Date'])."</legend>".njsn($s)."</fieldset>\");
posdiv('".$idhelp."',-1,-1);
idd('".$idhelp."_textarea').focus();
";

$a=array('Date','Header','Body','DateUpdate','view_counter','num','count_comments_open','DateDatetime','DateDate');
foreach($a as $l) unset($p[$l]);
file_put_contents($GLOBALS['hosttmp'].'zapisi.set',serialize($p));

otprav($s);

}


//=================================== запросили форму ===================================================================

if($a=='polesend') {

	$name=$_REQUEST["name"];
	$val=$_REQUEST["val"];


	if($name=='tags') {
		if($admin) msq("DELETE FROM `dnevnik_tags` WHERE `num`='$num'"); // удалить все тэги этой заметки
		$p=explode(',',$val); foreach($p as $l) { $l=c($l); if($l!='' && $admin) msq_add('dnevnik_tags',array('num'=>$num,'tag'=>e(h($l)))); }
		if(stristr($GLOBALS['msqe'],'Duplicate')) $GLOBALS['msqe']=''; // ошибка дублей - не ошибка
		otprav("");
	}

	if($name=='Body') {

		include_once $include_sys."_onetext.php";
		include_once $include_sys."_modules.php";
		$p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `num`='$num'","_1",0); if($p===false) idie("Ошибочная заметка #$num");

		if($p["autokaw"]!="no") $val=ispravkawa($val); // если разрешено обработать кавычки и тире

		$p['Body']=$val;

		if($admin) msq_update('dnevnik_zapisi',array('Body'=>e($val),'DateUpdate'=>time()),"WHERE `num`='$num'");

		$s=onetext($p);

		$s="idd('Body_$num').innerHTML=\"".njs($s)."\";";
		if($_REQUEST["clo"]==0) $s.="clean('".$idhelp."');";
		otprav($s);
	}

	if($name=='template' and $val=='') $val='blog'; // поле по умолчанию
	if($name=='' or $num==0) otprav(''); //idie('Неверные данные!');
	if($name=='autokaw') $val=($val=='true'?'no':'auto');

		if($admin) msq_update('dnevnik_zapisi',array(e($name)=>e($val),'DateUpdate'=>time()),"WHERE `num`='$num'");

	if($name=='Header') otprav("idd('Header_".$num."').innerHTML=\"".njs($val)."\"");
	otprav("");
}

//=================================== удаление заметки ===================================================================

if($a=='delete') {
	if($admin) {
		msq("DELETE FROM `dnevnik_zapisi` WHERE `num`='$num'"); // удалить запись
		msq("DELETE FROM `dnevnik_comm` WHERE `DateID`='$num'"); // удалить к ней все комментарии
		msq("DELETE FROM `dnevnik_posetil` WHERE `url`='$num'"); // удалить статистику ее посетителей
		msq("DELETE FROM `dnevnik_link` WHERE `DateID`='$num'"); // удалить статистику заходов по ссылкам
		msq("DELETE FROM `dnevnik_search` WHERE `DateID`='$num'"); // удалить статистику заходов с поисковиков
	}
	redirect($httphost);
}

?>