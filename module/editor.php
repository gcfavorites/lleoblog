<?php // �������� �������

if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����
if(!$admin) redirect($wwwhost."login/"); // ����������� - �����
blogpage();

$_PAGE["header"]=$_PAGE["title"]="�������� �������";

$Date = ( isset($_GET["Date"]) ? htmlspecialchars($_GET["Date"]) : (
isset($_POST["Date"]) ? htmlspecialchars($_POST["Date"]) : (
ms("SELECT `Date` FROM `dnevnik_zapisi` ORDER BY `Date` DESC LIMIT 1","_l",0)
)));


if($article["Prev"].$article["Next"]!='') { list($y,$m)=explode("-",$Date,2); $_PAGE["calendar"] = getCalendar($y,$m); }

if($_POST["action"] == "Move") {
	$num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."' LIMIT 1","_l",0);
   if($num!==false) {
	print "<p><font color=red><b>���������� ���������!<br>������� � ����� <a href=".$wwwhost.htmlspecialchars($Date).".html>".htmlspecialchars($Date)."</a> ��� ����������!</b></font>";
   } else {
	msq_update('dnevnik_zapisi',array( 'Date'=>e($Date),'DateUpdate'=>time() ),"WHERE `num`='".e($num)."'");
	reset_prevnext_del($_POST["oldDate"]);
	reset_prevnext_ins($Date);
	print "<p><font color=green><b>��������� �������</b></font>";
   }
   $_POST["action"] = "Save";
}

// ����� ������ �������
if($_POST["action"] == "Save") {
			// ��������������� �������� ���������

			$s=$_POST["Body"];
			$s=str_replace("\r",'',$s); // ��� ��� �����, ������ ��� ��������

		if($_POST["autokaw"]!="no") { // ���� ��������� ���������� ������� � ����
			$s=preg_replace_callback("/(>[^<]+<)/si","kawa",$s);
			$s=preg_replace("/([\s>]+)\-([\s<]+)/si","$1".chr(151)."$2",$s); // ������� ����
		}

//	getCalendar_clear($_POST["Date"]); // �������� ��� ���������
	msq_add_update('dnevnik_zapisi',array(
			'Date'=>e($_POST["Date"]),
			'Header'=>e($_POST["Header"]),
			'Body'=>e($s),
			'Access'=>e($_POST["Access"]),
//			'Comment'=>e($_POST["Comment"]),
			'Comment_view'=>e($_POST["Comment_view"]),
			'Comment_write'=>e($_POST["Comment_write"]),
			'Comment_screen'=>e($_POST["Comment_screen"]),
			'comments_order'=>e($_POST["comments_order"]),
			'autoformat'=>e($_POST["autoformat"]),
			'autokaw'=>($_POST["autokaw"]=='no'?'no':'auto'),
			//count_comments_open
			'DateUpdate'=>time()
		),'Date');
//	prosris(); // ���������� `dnevnik_zapisi`
	reset_prevnext_ins($_POST["Date"]); // ������������ ���������� ��� ���� �������, ��� ���������� � ���������
	$Date=$_POST["Date"];
}

elseif($_POST["action"] == "Delete") {
	// getCalendar_clear($_POST["Date"]); // �������� ��� ���������
	$num=ms("SELECT `num` FROM `dnevnik_zapisi` WHERE `Date`=`".e($_POST["Date"])."' LIMIT 1","_l",0);
	if($num!==false) {
		msq_del('dnevnik_zapisi', array('num'=>e($num)) ); // ������� �������
		reset_prevnext_del($_POST["Date"]); // ��������� ������ PrevNext �� ��������
		msq_del('dnevnik_comments', array('DateID'=>e($num)) ); // ������� �����������
	}
	//	prosris(); // ���������� `dnevnik_zapisi`
	// � ������ ����� ��������� �� ���������� ����������
	$Date=ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`<'".e($Date)."' ORDER BY `Date` DESC LIMIT 1","_l",1);
}



if($Date) $_POST=ms("SELECT * FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'","_1",0);
else $_POST=ms("SELECT * FROM `dnevnik_zapisi` ORDER BY `Date` DESC LIMIT 1","_1",0);

$urldata=urldata(htmlspecialchars($_POST["Date"]));

print "<form action='".$wwwhost."editor/' name='formedit' method='POST'>
Data: <input type='text' name='Date' class='t' value='".($Date!=''?htmlspecialchars($Date):date("Y/m/d"))."' maxlength=128 size=12>
<input type='hidden' name='oldDate' value='".htmlspecialchars($Date)."'>
<input type='submit' name='action' value='Move'>
&nbsp;&nbsp;&nbsp;&nbsp;[<a href='".$urldata."'>������� ��� ������� $urldata</a>]

<br>Header:
<br><input type='text' name='Header' class='t' value='".$_POST["Header"]."' maxlength='255' size='60' style='width: 100%;'>
<br>Text ".strlen($_POST['Body'])." ����:
<br><textarea class='t' style='width: 100%;' id='BodyTextarea' name='Body' cols='60' rows='20'>".htmlspecialchars($_POST["Body"])."</textarea>

<p>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'<p class=pd>','');\"><img border=1 src=".$www_design."e2/pd.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'<p class=d>','');\"><img border=1 src=".$www_design."e2/d.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'".chr(160)."','');\"><img border=1 src=".$www_design."e2/nbsp.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'".chr(169)."','');\"><img border=1 src=".$www_design."e2/copy.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'".chr(151)."','');\"><img border=1 src=".$www_design."e2/mdash.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'".chr(171)."','".chr(187)."');\"><img border=1 src=".$www_design."e2/ltgt.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'<b>','</b>');\"><img border=1 src=".$www_design."e2/bold.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'<i>','</i>');\"><img border=1 src=".$www_design."e2/italic.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'<s>','</s>');\"><img border=1 src=".$www_design."e2/strikethrough.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'<u>','</u>');\"><img border=1 src=".$www_design."e2/underline.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'\\n<center>','</center>');\"><img border=1 src=".$www_design."e2/justifycenter.gif></a>
<a onClick=\"pins2(document.getElementById('BodyTextarea'),'\\n<img src=".$www_design."e2/lj.gif style=\'vertical-align: middle;\'><a href=http://','.livejournal.com>','</a>');\"><img border=1 src=".$www_design."e2/ljuser.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'<p><center><img src=',' border=1></center>');\"><img border=1 src=".$www_design."e2/image.gif></a>
<a onClick=\"pins2(document.getElementById('BodyTextarea'),'<a href=','>','</a>');\"><img border=1 src=".$www_design."e2/link.gif></a>
<a onClick=\"pins2(document.getElementById('BodyTextarea'),'\\n\\n<p><center><object width=320 height=240><param name=movie value=\'','.swf\'></param><param name=wmode value=transparent></param><embed src=\'','.swf\' type=\'application/x-shockwave-flash\' wmode=transparent width=320 height=240></embed></object></center>');\"><img border=1 src=".$www_design."e2/ljvideo.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'\\n<blockquote style=\'border: 1px dashed rgb(255,0,0); padding: 20px; margin-left: 50px; margin-right: 50px; background-color: rgb(255,252,223);\'>','</blockquote>');\"><img border=1 src=".$www_design."e2/tableb_1.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'\\n<table style=\'border-collapse: collapse; border: 1px solid red; margin: 20pt;\'\\nbgcolor=#fffff0 border=1 cellpadding=20><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e2/tableb_r.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'\\n<table bgcolor=#fff0ff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e2/tableb1.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'\\n<table bgcolor=#f0ffff border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e2/tableb2.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'\\n<table bgcolor=#fffff0 border=1 cellpadding=50 cellspacing=0 width=80%><td><div align=justify>','</td></table>');\"><img border=1 src=".$www_design."e2/tableb3.gif></a>
<a onClick=\"pins(document.getElementById('BodyTextarea'),'\\n<pre style=\'border: 0.01mm solid rgb(0,0,0); padding: 4px; line-height: 100%; font-family: monospace; background-color: rgb(255,255,255);\'>','</pre>');\"><img border=1 src=".$www_design."e2/tableb_pre.gif></a>
";

SCRIPT_ADD($www_design."pins.js"); // ���������� pins

print "<div class=r>�������: ".selecto('Access',$_POST['Access'],array(
					'admin'=>"������",
					'podzamok'=>"��������",
					'all'=>"����") )."


���������� �����: ".selecto('autoformat',$_POST['autoformat'],array(
					'p'=>"p/br",
					'no'=>"���",
					'pd'=>"class=pd") )."

�� ������ �������: <input type=checkbox name=autokaw value='no'".($_POST["autokaw"]=='no'?"checked":"").">


<br>����������� ����������: ".selecto('Comment_view',$_POST['Comment_view'],array(
                                        'timeload'=>"��������",
                                        'load'=>"������",
                                        'off'=>"���",
                                        'rul'=>"������",
                                        'on'=>"���") )."

���������: ".selecto('Comment_write',$_POST['Comment_write'],array(
					'timeoff'=>"��������",
					'on'=>"����� �� ����",
					'off'=>"���",
					'friends-only'=>"����� �� ������",
					'login-only'=>"����� �� �������",
					'login-only-timeoff'=>"�������� �� �������") )."

���������: ".selecto('Comment_screen',$_POST['Comment_screen'],array(
					'open'=>"����",
					'friens-open'=>"������",
					'screen'=>"��������") )."

����������: ".selecto('comments_order',$_POST['comments_order'],array(
					'normal'=>"���",
					'allrating'=>"�������",
					'rating'=>"�����") )."

<br>
<input type=submit name='action' value='Save'> &nbsp;  &nbsp;  &nbsp;
<input type=submit name='action' value='Delete' onclick=\"return(confirm('�� ����� ������ ������� ��� �������?'))\">
</form>

<p>[<a href='".$wwwhost."logout/'>logout</a>]
&nbsp;&nbsp;&nbsp;&nbsp;[<a href='".$urldata."'>������� ��� ������� $urldata</a>]
</div>";

/*
function prosris() {
//	ms("ALTER TABLE `dnevnik_zapisi` ORDER BY `Date`","_l",0);
	ms("ALTER TABLE `dnevnik_zapisi` DROP num","_l",0);
	ms("ALTER TABLE `dnevnik_zapisi` ADD num INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, AUTO_INCREMENT = 1","_l",0);
}
*/

/*
function reset_prevnext($Date) {
	$a=reset_prevnext1($Date);
	reset_prevnext1($a['Prev']['Date']);
	reset_prevnext1($a['Next']['Date']);
}

function reset_prevnext1($Date) { $a=array();

$a['Prev']=ms("SELECT `Date`,`num` FROM `dnevnik_zapisi` WHERE `Date`<'".e($Date)."' ORDER BY `Date` DESC LIMIT 1","_1",0);
$a['Next']=ms("SELECT `Date`,`num` FROM `dnevnik_zapisi` WHERE `Date`>'".e($Date)."' ORDER BY `Date` LIMIT 1","_1",0);
msq_update('dnevnik_zapisi',array( 'Prev'=>intval($a['Prev']['num']),'Next'=>intval($a['Next']['num']) ),"WHERE `Date`='".e($Date)."'");
return $a;
}
*/


function reset_prevnext_ins($Date) { $Date=e($Date);
// ��������� �������
$Prev=e(ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`<'".$Date."' AND `Date` LIKE '____/__/%' ORDER BY `Date` DESC LIMIT 1","_l",0));
$Next=e(ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`>'".$Date."' AND `Date` LIKE '____/__/%' ORDER BY `Date` LIMIT 1","_l",0));
msq_update('dnevnik_zapisi',array( 'Prev'=>$Prev,'Next'=>$Next ),"WHERE `Date`='".$Date."'");
// ��������� Next � ����������
$l=e(ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`>'".$Prev."' AND `Date` LIKE '____/__/%' ORDER BY `Date` LIMIT 1","_l",0));
if($l!='') msq_update('dnevnik_zapisi',array( 'Next'=>$l ),"WHERE `Date`='".$Prev."'");
// ��������� Prev � ���������
$l=e(ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`<'".$Next."'  AND `Date` LIKE '____/__/%'ORDER BY `Date` DESC LIMIT 1","_l",0));
if($l!='') msq_update('dnevnik_zapisi',array( 'Prev'=>$l ),"WHERE `Date`='".$Next."'");
}


function reset_prevnext_del($Date) { $Date=e($Date);
// ����� ��������
$Prev=e(ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`<'".$Date."' AND `Date` LIKE '____/__/%' ORDER BY `Date` DESC LIMIT 1","_l",0));
$Next=e(ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `Date`>'".$Date."' AND `Date` LIKE '____/__/%' ORDER BY `Date` LIMIT 1","_l",0));
// ��������� Next � ���������� � Prev � ���������
if($Prev!='') msq_update('dnevnik_zapisi',array( 'Next'=>$Next ),"WHERE `Date`='".$Prev."'");
if($Next!='') msq_update('dnevnik_zapisi',array( 'Prev'=>$Prev ),"WHERE `Date`='".$Next."'");
}

?>