<?php
include "config.php";
include $include_sys."_autorize.php";
include $include_sys."_modules.php";

if($admin) { // �������� ��������� �� �������
	ini_set("display_errors","1");
	ini_set("display_startup_errors","1");
	ini_set('error_reporting', E_ALL);
	// error_reporting(E_ALL);
	// error_reporting = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR
	// error_reporting = E_ALL & ~E_USER_ERROR & ~E_USER_WARNING & ~E_USER_NOTICE
}

include $include_sys."_msq.php";
$_SCRIPT=array(); $_SCRIPT_ADD=array();
$_STYLE=array(); $_STYLE_ADD=array();
mystart();

// die( $filehost."<pre>".file_get_contents($filehost."config.php"));
// if(!$admin) die("admin error");
/*
if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����
if(!$admin) redirect($wwwhost."login/"); // ����������� - �����
blogpage();
// $_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."dnevnik.html"),
*/

$hashpage=rand(0,1000000); $hashpage=substr(broident($hashpage.$hashinput),0,6).'-'.$hashpage;

function zamok($d) {
	if($d=='all') return '';
	$z = "<img src=".$GLOBALS['www_design']."e/podzamok.gif>&nbsp;";
	if($d=='podzamok') return $z;
	return $z.$z;
}




function DESIGN($template,$title) {

if($template=='plain') $GLOBALS['_PAGE'] = array('design'=>file_get_contents($GLOBALS['host_design']."plain.html"),
'header'=>$title,
'title'=>$title,

'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'wwwcharset'=>$wwwcharset,
'signature'=>$signature
);

}


function blogpage() { global $_PAGE,$wwwhost,$login,$podzamok;

	STYLE_ADD($GLOBALS['httpsite'].$GLOBALS['www_design']."styles.css");

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."dnevnik.html"),
'prevnext'=>'',
'preword'=>'',
'preheader'=>'',
'calendar'=>'',
'counter'=>'',
'linkoff'=>"<a class=br href='".($_COOKIE['ctrloff']=='off'?$wwwhost."ctrl-on'>��������":$wwwhost."ctrl-off'>���������")."</a>",
'coments'=>'',
'javascript'=>'',
'ajaxscript'=>'',

'prevlink'=>$wwwhost,
'nextlink'=>$wwwhost,
'uplink'=>$wwwhost,
'downlink'=>$wwwhost."contents/",

'www_design'=>$GLOBALS['www_design'],
'admin_name'=>$GLOBALS['admin_name'],
'httphost'=>$GLOBALS['httphost'],
'wwwhost'=>$wwwhost,
'signature'=>$GLOBALS['signature'],
'wwwcharset'=>$GLOBALS['wwwcharset'],

'hashpage'=>$GLOBALS['hashpage'],
'foto_www_preview'=>$GLOBALS['foto_www_preview'],
'foto_res_small'=>$GLOBALS['foto_res_small']

);

$_PAGE['logino'] = ($login ? "<p class=br><a href='".$wwwhost."logon?userinfo=".$login."'><img
src='".$GLOBALS['IS']['IMG']."' border=0>".$GLOBALS['IS']['USER0']."</a>".($podzamok?"<br><font color=red>����������� ������</font><br><a
href='".$wwwhost."logon/?action=logoff'>�������������</a>":"") :
"<p class=r><a href='".$wwwhost."logon?retpage=".urlencode($GLOBALS['mypage'])."'>������������</a>");
}

list($path)=explode('?',$_SERVER["REQUEST_URI"]); $path=rtrim($path,'\/');
$pwwwhost=str_replace('/','\/',$wwwhost);

$months = explode(" ", " ������ ������� ���� ������ ��� ���� ���� ������ �������� ������� ������ �������");
$months_rod = explode(" ", " ������ ������� ����� ������ ��� ���� ���� ������� �������� ������� ������ �������");

// ����� ������� ��������?
if($admin) $access=""; elseif($podzamok) $access="`Access` IN ('all','podzamok')"; else $access="`Access`='all'";
function WHERE($s='') { global $access;	if($s.$access=='') return ''; if($s=='' || $access=='') return "WHERE ".$s.$access; return "WHERE ".$s." AND ".$access; }

// ============== ������ ��������, ����� ������ ��������� ==============

// ������� �������
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d\/\d\d.*)\.html/si", $path, $m)) { $Date = $m[1]; include("article.php"); exit; } // �������

// ������� ������
if(preg_match("/^".$pwwwhost."(\d\d\d\d\/\d\d)$/si", $path, $m)) { $Date = $m[1]; include("article.php"); exit; } // �������

// ������ => ��������� ������� ???
if($path."/" == $wwwhost) {
 	// Yandex ������ ������������� ����� �����! �� �� �������� ��� �����! �����, ��� ���� robots.txt ��� �������?!
 	if( strstr($BRO,'Yandex') || $IP=='78.110.50.100') {
 	logi("yandex_nah.log","\n".date("Y/m/d H:i:s")." Yandex ����� �����");
 	redirect('http://lleo.aha.ru/na/?WWFuZGV4JSDy+yDt6PXz-yDt5SD36PLg5fj8IHJvYm90cy50eHQg6CDr5efl+Pwg6vPk4CDt5SDt4OTuLiDfIOTr-yDq7uPuIHJvYm90cy50eHQg7+jx4Os-JSDv8OXq8OD54Okg6O3k5erx6PDu4uDy-CDy6PLz6yDv5fDl4OTw5fHg9ujoIPLl7CDq7u3y5e3y7uwsIOru8u7w++kg7+4g7OXx8vMg7+Xw5eDk8OXx4Pbo6C4gx+Dl4eDrLCBZYW5kZXgsIPfl8fLt7uUg8evu4u4h');
 	}

	$last=ms("SELECT `Date` FROM `dnevnik_zapisi` ".WHERE("`DateDatetime`!=0")." ORDER BY `Date` DESC LIMIT 1","_l",$ttl);

	if($last=='') {
		if(!msq_table('site') and !msq_table('dnevnik_zapisi')) redirect($wwwhost."admin"); // � �������, ���� �� ������� ����
		redirect($wwwhost."editor"); // � ��������, ���� ������� ���
		// idie("<p>��� ��������� ������! ".$o);
		}
	redirect($wwwhost.$last.".html"); // �� ���������
	}

// ������ ����� ����������
if(preg_match("/^".$pwwwhost."(\d\d\d\d)\-(\d\d)\-(\d\d)\.shtml/", $path, $m)) redirect($httphost.$m[1]."/".$m[2]."/".$m[3].".html");

// ���������� �� �����
if (preg_match("/^".$pwwwhost."(\d\d\d\d)\/(\d\d)\/?$/", $path, $m)) {
	$_PAGE["calendar"] = getCalendar($m[1], $m[2]);
	$_PAGE["title"] = $_PAGE["header"] = "���������� �������� �� ".$months[intval($m[2])]." ".$m[1]."-��";
	include($host_module."contents.php"); exit;
	}

// ===== ����������� ������� ������� �� ���������� /module/* ====
$mod_name=substr($path,strlen($wwwhost)); $mod_name=str_replace('..','.',$mod_name);
if(preg_match("/[^0-9a-z_\-\.\/]+/si",$mod_name)) idie("Error 404: wrong name \"<b>".htmlspecialchars($mod_name)."</b>\"");

// ������ ���� � �������
$mod=$host_module.$mod_name.".php"; if(file_exists($mod)) { include($mod); exit; }

// ����� � ���� site
$text=ms("SELECT `text` FROM `site` ".WHERE("`name`='".e($mod_name)."' AND `type`='page'"),"_l",$ttl);
if($text!='') { $name=$mod_name; include("site.php"); exit; }

// ����� � ���� ��������
$article=ms("SELECT * FROM `dnevnik_zapisi` WHERE `Date`='".e($mod_name)."'","_1",$ttl);
if($article!==false) { $Date=$mod_name; include("article.php"); exit; }

// ���� ������ ������ �� �������
idie("Error 404: Module not found \"<b>".htmlspecialchars($mod_name)."</b>\""
.($admin?"<p><a href='".$wwwhost."adminsite/?a=create&name=".urlencode($mod_name)."'>������� ��� �������� � ���� `".$db_site."`?</a>":"")
);

/*
if($path == $wwwhost."rss.xml") { $_PAGE["template"]=false; include($host_module."rss.php"); exit; } // RSS-�����
if($path == $wwwhost."whatis") { makehead("������� ����� ��������"); include("mod_whatis.php"); exit; }
if($path == $wwwhost."whatis_pravka") { makehead("��� ����� ������� �������������� ������?"); include("mod_whatis_pravka.php"); exit; }
if($path == $wwwhost."contents") { makehead("���������� ��������"); include("mod_contents.php"); exit; }
if($path == $wwwhost."rating") { makehead("������� ��������");	include("mod_rating.php"); exit; }
if($path == $wwwhost."comments") { makehead("�������� ������������"); include("mod_comments.php"); exit; }
if($path == $wwwhost."pravki") { makehead("�������� ������"); include("mod_pravka.php"); exit;	}
if($path == $wwwhost."ctrl-off") { set_cookie('ctrloff','off',time()+86400*365,"/",$cookie_site,0); redirect($wwwhost); } // ���� �������
if($path == $wwwhost."ctrl-on") { set_cookie('ctrloff','no',time()+86400*365,"/",$cookie_site, 0); redirect($wwwhost); } // ��� �������
if($path == $wwwhost."admin") { makehead("�������"); include("admin.php"); exit; }
if($path == $wwwhost."logon") { makehead("������ ����������� � �������"); include("mod_logon.php"); exit; }
if($path == $wwwhost."rssc.xml") { $template=false; include("mod_rssc.php"); exit; } // RSS-����� ������������
*/
/* del */ // if($path == $wwwhost."dsbw.xml") { $template = false; include("mod_dsbw.php"); exit; } // RSS-�����

function makehead($s) { global $_PAGE;
	$_PAGE["calendar"] = "<a href=".$GLOBALS["wwwhost"].">� �������</a>";
	$_PAGE["title"] = $_PAGE["header"] = $s;
	$_PAGE["counter"] = "";
	return;
}

function getCalendar($year, $mon, $day = false) { global $admin, $wwwhost, $months, $podzamok; $s = "";

	if(intval($year)==0) return '';

	$ttl=($admin?0:$GLOBALS["ttl"]*10); // ��� ��������� - ������������� ����� ���������� � ����

	$m = mktime(1, 1, 1, $mon, 1, $year); // ����� ������
	$k = date("w",$m)-1; if($k<0) $k=6; // ���� ������ ������� ����� ������
	$end = date("t",$m); // ���� � ���� ������
	$now = date("Y/m/d"); // ����������� ����

	// ������� ������������ ������� ������
$sql = ms("SELECT `DateDate`,`Date`,`Access` FROM `dnevnik_zapisi` ".WHERE("`DateDate`>='".$m."' AND `DateDate`<'".($m+($end-1)*86400)."'")." ORDER BY `DateDate`","_a",$ttl);

	$a=array(); foreach($sql as $p) { $i=intval(substr($p['Date'],8,2)); $a[$i]=array($p['Access'],$p['Date'],++$a[$i][2]); }

//dier($a);

	$Prev=$sql[0]['Prev']; if($Prev!='') $Prev="<a href='".$wwwhost.$Prev.".html'>&lt;&lt;</a>";
	elseif($admin) $Prev="<a href='".$wwwhost.date("Y/m",$m-60*60*24)."'>&lt;&lt;</a>";
	$Next=$sql[sizeof($sql)-1]['Next']; if($Next!='') $Next="<a href='".$wwwhost.$Next.".html'>&gt;&gt;</a>";
	elseif($admin) $Next="<a href='".$wwwhost.date("Y/m",$m+$end*60*60*24)."'>&gt;&gt;</a>";
	
$s .= "<table border=0 cellspacing=0 cellpadding=1>
<tr><td class=cld_top>".$Prev."</td><td colspan=5 align=center class=cld_top>".$months[intval($mon)]." ".intval($year)."</td><td align=right class=cld_top>".$Next."</td></tr>
<tr><td class=cld_days>��</td><td class=cld_days>��</td><td class=cld_days>��</td><td class=cld_days>��</td><td class=cld_days>��</td><td class=cld_red><b>��</b></td><td class=cld_red><b>��</b></td></tr>";


//$s=''; for($i=0;$i<10;$i++) $s.="<br>".(date("w",$i*60*60*24)); die($s);
// $a=0; if(--$a) die("1"); else die("0");
#1 2 3 4 5 6 0
//$a=array(); $a[0]='1'; if(($x=$a[0])) die('true'); else die('false');
//die("#######<pre>".print_r($a,1));


	if($k) { $s.="<tr>"; for($i=0;$i<$k;$i++) $s.="<td class=".($i>4?"cld_red":"cld").">&nbsp;</td>"; } // ���������� ������ ������

	for($i=1; $i<=$end; $i++) {
		if(!$k) $s .= "<tr>";
		$d=sprintf("%04d/%02d/%02d",$year,$mon,$i);
		$style=($d==$now?" style='background-color: #FFFFa0; border: red solid 1px;'":'');
		$di=$i;
		if(!($x=$a[$i][0])) { if($admin) $di="<a class=cld_ed href='".$wwwhost."editor/?Date=".urlencode($d)."'>".$i."</a>";
		} else {
			if($x=='podzamok') $di="<s>".$di."</s>";
			elseif($x=='admin') $di="<s><i>".$di."</i></s>";
			if($a[$i][2]>1) $di="<b>$di</b>";
			$di="<a href='".$wwwhost.$a[$i][1].".html'>".$di."</a>";
		}
		$s .= "<td class=".($k>4?"cld_red":"cld").$style.">".$di."</td>";
		if($k==6) $s .= "</tr>"; if(++$k>6) $k=0;
	}

return $s."</table>";
}

function ljaddr($lju) { if(!$lju) return '';
return "http://".($lju==trim($lju,"_-")?$lju.".livejournal.com":"users.livejournal.com/".$lju)."/";
}

function ljaddru($lju) { if(!$lju) return '';
return "<img src=http://stat.livejournal.com/img/userinfo.gif style=\"vertical-align: center;\"><a href=".ljaddr($lju).">".$lju."</a>";
}

function urldata($d) { return $GLOBALS['wwwhost'].htmlspecialchars($d).(strstr($d,'/')?".html":''); }

function mk_prevnest($prev,$next) { // ����� ����� ��� �����!!! �� ���������� � ���� � ������ ��������� CSS!!! ������ �� � ���!!!
$prev=($prev==''?'&nbsp;':"<font size=1>".$prev."</font>");
$next=($next==''?'&nbsp;':"<font size=1>".$next."</font>");
return "<center><table width=98% cellspacing=0 cellpadding=0><tr valign=top><td width=50%>$prev</td><td width=50% align=right>$next</td></tr></table></center>";
}

?>
