<?php // редактор заметок
if(!isset($admin_name)) idie("Error 404"); // неправильно запрошенный скрипт - нахуй
if(!$admin) redirect($wwwhost."login/"); // посторонних - нахуй
blogpage("Редактор заметок");

$Date = ( isset($_GET["Date"]) ? h($_GET["Date"]) : (
isset($_POST["Date"]) ? h($_POST["Date"]) : (
ms("SELECT `Date` FROM `dnevnik_zapisi` ORDER BY `Date` DESC LIMIT 1","_l",0)
)));


if($article["Prev"].$article["Next"]!='') { list($y,$m)=explode("-",$Date,2); $_PAGE["calendar"] = getCalendar($y,$m); }

if($_POST["action"] == "Move") {
	$num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."' LIMIT 1","_l",0);
   if($num!==false) {
	print "<p><font color=red><b>Невозможно перенести!<br>Заметка с датой <a href=".$wwwhost.h($Date).".html>".h($Date)."</a> уже существует!</b></font>";
   } else {
	$t=getmaketime($Date);
	msq_update('dnevnik_zapisi',array( 'Date'=>e($Date),'DateUpdate'=>time(), 'DateDate'=>$t[0],'DateDatetime'=>$t[1] ),"WHERE `num`='".e($num)."'");
	print "<p><font color=green><b>Пренесено успешно</b></font>";
   }
   $_POST["action"] = "Save";
}

// Форма правки заметок
if($_POST["action"] == "Save") {
			// предварительная полезная обработка

			$s=$_POST["Body"];
			$s=str_replace("\r",'',$s); // вот это сразу, потому что ненавижу

		if($_POST["autokaw"]!="no") { // если разрешено обработать кавычки и тире
			$s=preg_replace_callback("/(>[^<]+<)/si","kawa",$s);
			$s=preg_replace("/([\s>]+)\-([\s<]+)/si","$1".chr(151)."$2",$s); // длинное тире
		}


	$t=getmaketime($_POST["Date"]);

//	getCalendar_clear($_POST["Date"]); // сбросить кэш календаря
	msq_add_update('dnevnik_zapisi',array(
			'Date'=>e($_POST["Date"]),
			'Header'=>e($_POST["Header"]),
			'template'=>e($_POST["template"]),
			'Body'=>e($s),
			'Access'=>e($_POST["Access"]),
//			'Comment'=>e($_POST["Comment"]),
			'Comment_view'=>e($_POST["Comment_view"]),
			'Comment_write'=>e($_POST["Comment_write"]),
			'Comment_screen'=>e($_POST["Comment_screen"]),
			'comments_order'=>e($_POST["comments_order"]),
			'autoformat'=>e($_POST["autoformat"]),
			'autokaw'=>($_POST["autokaw"]=='no'?'no':'auto'),
			'DateDate'=>$t[0],
			'DateDatetime'=>$t[1],
			//count_comments_open
			'DateUpdate'=>time()
		),'Date');
//	prosris(); // устаканить `dnevnik_zapisi`
	$Date=$_POST["Date"];
}

elseif($_POST["action"] == "Delete") {
	// getCalendar_clear($_POST["Date"]); // сбросить кэш календаря
	$num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($_POST["Date"])."' LIMIT 1","_l",0);
	if($num!==false) {
		msq_del('dnevnik_zapisi', array('num'=>e($num)) ); // удалить заметку
		msq_del('dnevnik_comm', array('DateID'=>e($num)) ); // удалить комментарии
	}
	//	prosris(); // устаканить `dnevnik_zapisi`
	// а теперь найти последнюю из предыдущих оставшихся
	$Date=ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`<'".e($Date)."' ORDER BY `Date` DESC LIMIT 1","_l",1);
}


if($Date) $_POST=ms("SELECT * FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'","_1",0);
else $_POST=ms("SELECT * FROM `dnevnik_zapisi` ORDER BY `Date` DESC LIMIT 1","_1",0);


$urldata=urldata(h($_POST["Date"]));

SCRIPTS("
var valiDatemessage = 2; function valiDate(id) {	var s=document.getElementById(id).value;
if( s.replace(/[0-9a-z_\-\.\/\~]/gi,'').length !=0 ) {
if( --valiDatemessage > 0 ) alert('Этот адрес посетители будут открывать браузером,\\nпоэтому здесь допустимы только символы:\\n\\n0-9 a-z _ - . / ~');
document.getElementById(id).value=s.replace(/[^0-9a-z_\-\.\/\~]/gi,'');	}
}");

print "<form action='".$wwwhost."editor/' name='formedit' method='POST'>
Data: <input type='text' id='Date' name='Date' class='t' onkeyup=\"valiDate('Date')\" value='".($Date!=''?htmlspecialchars($Date):date("Y/m/d"))."' maxlength=128 size=12>
<input type='hidden' name='oldDate' value='".htmlspecialchars($Date)."'>
<input type='submit' name='action' value='Move'>
&nbsp;&nbsp;&nbsp;&nbsp;[<a href='".$urldata."'>открыть эту заметку $urldata</a>]

<br>Header:
<br><input type='text' name='Header' class='t' value='".$_POST["Header"]."' maxlength='255' size='60' style='width: 100%;'>
<br><img class=knop onClick=\"pns('BodyTextarea','<p class=pd>','')\" src=".$www_design."e2/pd.gif><img
class=knop onClick=\"pns('BodyTextarea','<p class=d>','')\" src=".$www_design."e2/d.gif><img
class=knop onClick=\"pns('BodyTextarea','".chr(160)."','')\" src=".$www_design."e2/nbsp.gif><img
class=knop onClick=\"pns('BodyTextarea','".chr(169)."','')\" src=".$www_design."e2/copy.gif><img
class=knop onClick=\"pns('BodyTextarea','".chr(151)."','')\" src=".$www_design."e2/mdash.gif><img
class=knop onClick=\"pns('BodyTextarea','".chr(171)."','".chr(187)."')\" src=".$www_design."e2/ltgt.gif><img
class=knop onClick=\"pns('BodyTextarea','<b>','</b>')\" src=".$www_design."e2/bold.gif><img
class=knop onClick=\"pns('BodyTextarea','<i>','</i>')\" src=".$www_design."e2/italic.gif><img
class=knop onClick=\"pns('BodyTextarea','<s>','</s>')\" src=".$www_design."e2/strikethrough.gif><img
class=knop onClick=\"pns('BodyTextarea','<u>','</u>')\" src=".$www_design."e2/underline.gif><img
class=knop onClick=\"pns('BodyTextarea','\\n<center>','</center>')\" src=".$www_design."e2/justifycenter.gif><img
class=knop onClick=\"pns2('BodyTextarea','\\n<img src=".$www_design."e2/lj.gif style=\'vertical-align: middle;\'><a href=http://','.livejournal.com>','</a>')\" src=".$www_design."e2/ljuser.gif><img
class=knop onClick=\"pns('BodyTextarea','<p><center><img src=',' border=1></center>')\" src=".$www_design."e2/image.gif><img
class=knop onClick=\"pns2('BodyTextarea','<a href=','>','</a>')\" src=".$www_design."e2/link.gif><img
class=knop onClick=\"pns2('BodyTextarea','\\n\\n<p><center><object width=320 height=240><param name=movie value=\'','.swf\'></param><param name=wmode value=transparent></param><embed src=\'','.swf\' type=\'application/x-shockwave-flash\' wmode=transparent width=320 height=240></embed></object></center>')\" src=".$www_design."e2/ljvideo.gif><img
class=knop onClick=\"pns('BodyTextarea','\\n<blockquote style=\'border: 1px dashed rgb(255,0,0); padding: 20px; margin-left: 50px; margin-right: 50px; background-color: rgb(255,252,223);\'>','</blockquote>')\" src=".$www_design."e2/tableb_1.gif><img
class=knop onClick=\"pns('BodyTextarea','\\n<table style=\'border-collapse: collapse; border: 1px solid red; margin: 20pt;\'\\nbgcolor=#fffff0 border=1 cellpadding=20><td><div align=justify>','</td></table>')\" src=".$www_design."e2/tableb_r.gif><img
class=knop onClick=\"pns('BodyTextarea','\\n<table bgcolor=#fff0ff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>')\" src=".$www_design."e2/tableb1.gif><img
class=knop onClick=\"pns('BodyTextarea','\\n<table bgcolor=#f0ffff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>')\" src=".$www_design."e2/tableb2.gif><img
class=knop onClick=\"pns('BodyTextarea','\\n<table bgcolor=#fffff0 border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>')\" src=".$www_design."e2/tableb3.gif><img
class=knop onClick=\"pns('BodyTextarea','\\n<pre style=\'border: 0.01mm solid rgb(0,0,0); padding: 4px; line-height: 100%; font-family: monospace; background-color: rgb(255,255,255);\'>','</pre>')\" src=".$www_design."e2/tableb_pre.gif>

Text ".strlen($_POST['Body'])." букв:
<br><textarea class='t' style='width: 100%;' id='BodyTextarea' name='Body' cols='60' rows='20'>".htmlspecialchars($_POST["Body"])."</textarea>

";




// выяснить о модулях
$inc=glob($filehost."template/*.html");
$ainc=array(); $ainc['']='- нет -'; foreach($inc as $l) { $l=preg_replace("/^.*?\/([^\/]+)\.html$/si","$1",$l); $ainc[$l]=$l; }

STYLES("
.knop { border: 1px solid white; }
.knop:hover { border: 1px solid black; }
");

SCRIPT_ADD($www_design."pins.js"); // подгрузить pins

print "<div class=r>Заметка: ".selecto('Access',$_POST['Access'],array(
					'admin'=>"никому",
					'podzamok'=>"подзамок",
					'all'=>"всем") )."


автоформат строк: ".selecto('autoformat',$_POST['autoformat'],array(
					'p'=>"p/br",
					'no'=>"нет",
					'pd'=>"class=pd") )."

не менять кавычки: <input type=checkbox name=autokaw value='no'".($_POST["autokaw"]=='no'?"checked":"").">


<br>Комментарии показывать: ".selecto('Comment_view',$_POST['Comment_view'],array(
                                        'timeload'=>"поначалу",
                                        'load'=>"кнопку",
                                        'off'=>"нет",
                                        'rul'=>"важные",
                                        'on'=>"все") )."

принимать: ".selecto('Comment_write',$_POST['Comment_write'],array(
					'timeoff'=>"поначалу",
					'on'=>"вечно от всех",
					'off'=>"нет",
					'friends-only'=>"вечно от друзей",
					'login-only'=>"вечно от логинов",
					'login-only-timeoff'=>"поначалу от логинов") )."

открывать: ".selecto('Comment_screen',$_POST['Comment_screen'],array(
					'open'=>"всех",
					'friens-open'=>"друзей",
					'screen'=>"скрывать") )."

сортировка: ".selecto('comments_order',$_POST['comments_order'],array(
					'normal'=>"нет",
					'allrating'=>"сборная",
					'rating'=>"тупая") )."


<br>шаблон дизайна: ".selecto('template',$_POST['template'],$ainc)."

<br>
<input type=submit name='action' value='Save'> &nbsp;  &nbsp;  &nbsp;
<input type=submit name='action' value='Delete' onclick=\"return(confirm('Вы точно хотите удалить эту заметку?'))\">
</form>

<p>[<a href='".$wwwhost."logout/'>logout</a>]
&nbsp;&nbsp;&nbsp;&nbsp;[<a href='".$urldata."'>открыть эту заметку $urldata</a>]
</div>";

/*
function prosris() {
//	ms("ALTER TABLE `dnevnik_zapisi` ORDER BY `Date`","_l",0);
	ms("ALTER TABLE `dnevnik_zapisi` DROP num","_l",0);
	ms("ALTER TABLE `dnevnik_zapisi` ADD num INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, AUTO_INCREMENT = 1","_l",0);
}
*/

?>