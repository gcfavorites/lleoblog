<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����

if(!$admin) redirect($wwwhost."login/"); // ����������� - �����

if(!isset($admin_ljuser)) idie("������: �� ������� ��� ������������ livejournal!
<br>�������� � ���� config.sys ��� �������, ������: <b>\$admin_ljuser=\"lleo\";</b>");

DESIGN('plain',"������ ������ $admin_ljuser �� ���� �������");

if(!sizeof($_GET)) {

if(!sizeof($_POST)) {
	$r=rand(0,999); die("<form method=post action='$mypage'><input type=hidden name=chislo1 value='$r'>
��� ���? ��� ������ �� ���������� ������������. ������� �� - ������ � ������� �������, ������� ����������� ���� �������.
���-�� ����� ���� �� �������� ���� ������ ��������, ����� ��������� ������ ������ ����������� ������� �������
��� ��������. ���� �� �������, ��� ���������� ������ �����������, ���� ����������� ���� ������� �������.
������� ��� ��� ��������� �����: <b>$r</b> <input type=text size=4 name=chislo2 value=''>
<input type=submit value='������'></form>
"); } else { if($_POST['chislo1'].$_POST['chislo2']=='' or $_POST['chislo1']!=$_POST['chislo2']) die('������, �����������.'); }

}

$numdoc=100;
$all=array();
$into=(isset($_GET['into'])?intval($_GET['into']):0);

if($_GET['mode']=='comments') get_ya_comments();

$prostynka='';

$url="http://blogs.yandex.ru/search.xml?rd=0&spcctx=doc&ft=blog&server=livejournal.com&author=".$admin_ljuser."&numdoc=".$numdoc."&full=1&p=".($into++);

$GLOBALS['prostynka'].="<p>������ <a href='$url'>������ ������� �� �������</a>";

$syandex=uw(ufile_get($url));


$syandex=preg_replace("/<div class=\"b-item Ppb-c-ItemMore SearchStatistics-item\"[^>]+>.*?/si","-\001-",$syandex);
$arsyandex=explode("-\001-",$syandex); unset($arsyandex[0]);

foreach($arsyandex as $lyandex) { $ara=get_one_ya($lyandex);
	$Date=$ara['Date'];

	if(isset($all[$Date]) and $ara['Header']==$all[$Date]['Header'] and $ara['Body']==$all[$Date]['Body'] ) {
		$GLOBALS['prostynka'].="<br><font color=red>����� $Date - �� ������ � ����!</font>";
	} else {
		if(isset($all[$Date])) {
			$GLOBALS['prostynka'].="<br>����� $Date:<p><pre>".print_r($ara,1)."</pre><hr>����:<p><pre>".print_r($all[$Date],1)."</pre>";
			$i=0; while(isset($all[ $Date."_".(++$i) ])){} $Date=$Date."_".$i;
			$ara['Date']=$Date;
		}
	$all[$Date]=$ara;
	msq_add_update('dnevnik_zapisi',$ara,"Date");

	$GLOBALS['prostynka'].="<br>".$GLOBALS['cached'].(sizeof($all)).". <font color=green>".$ara['Date']."</font>".($ara['Header']!=''?" - ".$ara['Header']:"");

	}
}

$n=''; foreach($all as $a=>$b) $n.=" ".$a; $n=md5($n);

// ������������ ����
if( sizeof($arsyandex)==0 // ������ � ���� ��� �� ��������
or $n==$_GET['lastmd5'] // ������ ������ �� ����� � ���� �� ��������� � �������� �����
or !strstr($syandex,'<a id="next_page" href="') // ������ �� ������� ���� "��������� ��������"
) {

// .admin_redirect("$mypage?mode=comments",10).

die("<p>� ��������� ���������? ������ ����� � ����: $n<br><a href='$url'>$url</a>

<p>������ ������ �����������: <a href='$mypage?mode=comments'>$mypage?mode=comments</a>

<p>��� ����������� ����������� ��� �� ���������: <a href='$mypage?into=$into&lastmd5=".$n."'>$mypage?into=$into&lastmd5=".$n."</a>
<br><a href='$path'>$into</a></b> (������� ".sizeof($all).")

");

} else {

die("<p><b>������ ������ �������, ���� <a href='$path'>$into</a></b> (������� ".sizeof($all).")".admin_redirect("$mypage?into=$into&lastmd5=".$n,3) );

}


function ufile_get($url) { global $mypage;
	$s=file_get($url); if(strstr($s,'</body></html>')) return $s;

	file_get($url,0);
	$e=intval($_GET['etage']++);
	die("<p><font color=red>����!"
.($e?" � ��� � $e ���!<br>��������� ����� ".strlen($s)." ������. ����������� �����: ".md5($s):'')
."<br>��������� ���������!</font>".admin_redirect($mypage.add_get(),5) );
}


function admin_redirect($path,$timesec) {

SCRIPTS("var tiktimen=".$timesec.";
function tiktime(id) { document.getElementById(id).innerHTML = tiktimen--; setTimeout(\"tiktime('\" + id + \"')\", 1000); }");

return "<p><font color=orange><b>������ ������ �� �������! ���� ������! �� ������������ �������� <span id='tiktime'><script>tiktime('tiktime')</script></span>!</b></font>
<noscript><meta http-equiv=refresh content=\"".$timesec.";url=\"".$path."\"></noscript>
<script> setTimeout(\"location.replace('".$path."')\", ".($timesec*1000)."); </script></p>".$GLOBALS['prostynka']; }


function get_one_ya($lyandex) { $r=array(); // ������ ������
	if(!preg_match("/<ul class=\"info b-hlist b-hlist-middot\"><li>([^<>]+)<\/li>/si",$lyandex,$m)) { die("�� ������� ����!"); }
		preg_match("/(\d+) ([^ ]+) (\d+), (\d+):(\d+)/si",$m[1],$d); $da=array_keys($GLOBALS['months_rod'],$d[2]);
		$datet=sprintf("%04d-%02d-%02d %02d:%02d", $d[3],$da[0],$d[1],$d[4],$d[5]);
	$Dname=e(str_replace(array('-',':',' '),array('/','-','_'),$datet));


	if(!preg_match("/<div class=\"long\s+ItemMore-Description\"><div class=\"b-text\"><div>(.*?)<\/div><\/div><div class=\"links\">/si",$lyandex,$m)) { idie("�� ������ text!"); }
	$text=govnolink($m[1]);

	if(!preg_match("/<h3 class=\"title\s*\"><a href=\"([^>\"\'\s]+)\"[^>]+target=\"_blank\">(.*?)<\/a><\/h3>/si",$lyandex,$m)) { idie("�� ������ head!"); }
	$link=govnolink($m[1]);
	$head=govnolink($m[2]);str_ireplace(array('<wbr />','<wbr/>','<wbr>','</wbr>'),'',$link);

        $t=getmaketime($Dname);

	return array(
'Date'=>e($Dname),
'Header'=>e($head),
'Body'=>e($text."<hr><a href='$link'>$link</a>"),
'Access'=>'all',
'DateUpdate'=>time(),
'DateDate'=>$t[0],
'DateDatetime'=>$t[1],
'opt'=>e(serialize(array('Comment'=>'allways_on','Comment_view'=>'on','Comment_write'=>'friends-only',
'Comment_screen'=>'open','comments_order'=>'normal','autoformat'=>'no','autokaw'=>'no','template'=>'blog')))
);
}

//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================
//================================================================================================================

function get_ya_comments() { global $into,$numdoc,$mypage;

	$ppp=(isset($_GET['ppp'])?intval($_GET['ppp']):0); // into - ����� ������� (�� �����), ppp - ����� �������� � �������������

	$p=ms("SELECT `Header`,`Date`,`Body`,`num` FROM `dnevnik_zapisi` LIMIT ".$into.",1","_1"); if($p===false) die("<p><br>������ ���������!");

	// $GLOBALS['prostynka'].="<p>������ ������ �� ������! ������ ������������ ������ 2 ������� - �������� ���� ���������������!";

	$Body=$p['Body']; $num=$p['num']; $Date=$p['Date']; $Header=$p['Header'];

	$nnn=ms("SELECT COUNT(*) FROM `dnevnik_zapisi`", '_l',2);


	if(!preg_match("/<hr><a href=[^>]+>(.*?)<\/a>$/si",$Body,$m)) die("������������ ������ ������! <p>".$Body);
	$link=govnolink($m[1]);

	$url="http://blogs.yandex.ru/search.xml?post=".urlencode($link)."&ft=comments&rd=0&spcctx=doc&full=1&numdoc=".$numdoc."&p=".$ppp;

	$GLOBALS['prostynka'].="<p>������ <a href='$url'>����������� � ���� ������� �� �������</a>";

	$syandex=uw(ufile_get($url));

	$syandex=preg_replace("/<div class=\"b-item Ppb-c-ItemMore SearchStatistics-item\"[^>]+>.*?/si","-\001-",$syandex);
	$arsyandex=explode("-\001-",$syandex); unset($arsyandex[0]);

if(sizeof($arsyandex)) foreach($arsyandex as $lyandex) {
	$ara=get_one_ya_c($lyandex,$Date,$num,$url);
	$unic=$ara['Name']."#".$ara['Time'];
	if(isset($all[$unic]) and $ara['Name']==$all[$unic]['Name'] and $ara['Text']==$all[$unic]['Text'] ) {
		$GLOBALS['prostynka'].= "<hr><font color=red>������� ������ ����� ���������� $unic, �� ������ � ����!</font>"; // <p><pre>".print_r($ara,1)."</pre><hr>����:<p><pre>".print_r($all[$unic],1)."</pre>"; exit;
		} else {
			if(isset($all[$unic])) {
				// $GLOBALS['prostynka'].= "<hr>������� ����� $unic:<p><pre>".print_r($ara,1)."</pre><hr>����:<p><pre>".print_r($all[$unic],1)."</pre>";
				$i=0; while(isset($all[ $unic."_".(++$i) ])){} $unic=$unic."_".$i;
			}
		$all[$unic]=$ara;
		msq_add_update('dnevnik_comm',$ara,"Name Time"); // ������ � ����
		}
}

$n=''; if(sizeof($all)) { foreach($all as $a=>$b) $n.=" ".$a; $n=md5($n); }

print "<p>������� <b>$into �� $nnn</b>: <a href='".$GLOBALS['wwwhost']."$Date'>$Date".($Header!=''?' - '.$Header:'')."</a>
<br>".($ppp?"������������ �������� ������������ $ppp<br>":'');

// ������������ ����
if( sizeof($arsyandex)==0 // ������ � ���� ��� �� ��������
or $n==$_GET['lastmd5'] // ������ ������ �� ����� � ���� �� ��������� � �������� �����
or !strstr($syandex,'<a id="next_page" href="') // ������ �� ������� ���� "��������� ��������"
) {
die("������������: ".sizeof($all).", ��������� � ��������� �������.".admin_redirect("$mypage?mode=comments&into=".(++$into),2));
} else {
die("������������: ".sizeof($all).", �� ��� �� ���, ���� ��� �������� ������������ ����� ".($ppp+1).", ����������."
.admin_redirect("$mypage?mode=comments&ppp=".(++$ppp)."&into=$into&lastmd5=".$n,2));
}


}

function bredie($s,$lyandex,$url) { global $mypage;
	file_get($url,0);

	$path="$mypage?mode=comments&ppp=".$_GET['ppp']."&into=".$_GET['into']."&repeat=".(++$_GET['repeat']);

	die($s." ".$lyandex."

<p>������, �� �� ��������� ��������������� ����� 15 ������!
<noscript><meta http-equiv=refresh content=\"15;url=\"".$path."\"></noscript>
<script> setTimeout(\"location.replace('".$path."')\", 15000);  </script>");

}

function govnolink($s) { return str_ireplace(array('<wbr />','<wbr/>','<wbr>','</wbr>'),'',$s); }

function get_one_ya_c($lyandex,$Date,$num,$url) {

	if(!preg_match("/<span class=\"icon\"><img src=\"([^\">]+)\"/si",$lyandex,$m)) { /* idie("�� ������� ������"); */ }
	else $img=str_replace("&amp;prefix=small","&prefix=normal",govnolink($m[1]));

	if(!preg_match("/<li>����������� <a href=\"([^\">]+)\">([^<>\s]+)<\/a> � �������/si",$lyandex,$m)) { /* idie("�� ������ �����!");*/ }
	$autor=($m[2]!=''?govnolink($m[2]):'anonymouse');
	$autor_link=govnolink($m[1]);

	if(!preg_match("/<ul class=\"info b-hlist b-hlist-middot\"><li>([^<>]+)<\/li><li>�����������/si",$lyandex,$m)) { bredie("�� ������� �����!",$lyandex,$url); }

	preg_match("/(\d+) ([^ ]+) (\d+), (\d+):(\d+)/si",$m[1],$d); $da=array_keys($GLOBALS['months_rod'],$d[2]);
	$datet=sprintf("%04d-%02d-%02d %02d:%02d", $d[3],$da[0],$d[1],$d[4],$d[5]);


	if(!preg_match("/<div class=\"long\s+ItemMore-Description\"><div class=\"b-text\"><div>(.*?)<\/div><\/div><div class=\"links\">/si",$lyandex,$m)) { bredie("�� ������ �����!",$lyandex,$url); }
	$text=govnolink($m[1]);

	if(!preg_match("/<h3 class=\"title *\"><a href=\"([^>\"]+)\"[^>]+>(.*?)<\/a><\/h3>/si",$lyandex,$m)) { bredie("�� ������ ���������!",$lyandex,$url); }
	$headlink=govnolink($m[1]);
	$head=govnolink($m[2]);
	if($headlink==$head) $head='';

	$text=str_ireplace(array('<p>','<br>','<br />','<br/>',"\r"),array("\n\n","\n","\n","\n",''),$text);

	$text=preg_replace("/<img\s+src=[\"\']*([^\'\"\s>]+)[\'\"]*\s*\/*>/si","\n$1\n",$text);
	$text=str_ireplace('<span class="ljuser" style="white-space: nowrap;"><img alt="[info]" height="17" src="http://stat.livejournal.com/img/userinfo.gif" style="vertical-align: bottom; border: 0; padding-right: 1px;" width="17" />','',$text);
	$text=str_ireplace('&gt;','>',$text);


	$head=str_ireplace(array('<p>','<br>','<br />','<br/>',"\r"),array("\n\n","\n","\n","\n",''),$head);

	$text=(isset($img)?$img." ":'').($head!=''?"<b>$head</b>\n\n":'').$text; // ."\n\n".$autor_link;

$GLOBALS['prostynka'].= "<p><table style='border: 1px dashed #ccc;'
width=100% border=0><tr valign=top>
<td align=center width=100><img src='$img'><p class=br><a href='$autor_link'>$autor</a></td>
<td><b>$head</b> $datet
<p>".h($text)."
<p class=br><a href='$comlink'>$comlink</a>
</td></tr></table>";

	return array(
        'unic'=>0,
        'DateID'=>intval($num),
        'Time'=>strtotime($datet),
	'whois'=>e($autor_link."\001".$headlink),
        'Name'=>e($autor),
//        'Mail'=>e($autor_link),
        'Text'=>e($text),
	'Parent'=>0,
	'scr'=>0,
	'rul'=>0,
	'ans'=>1,
	'group'=>0
);

}

?>
