<?php // update

// exit;

idebug("PHP working, congratulations :) - ok");

// �������� ��������� �� �������
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
set_time_limit(0);

if(!function_exists('file_put_contents')) { function file_put_contents($url,$s) { $f=fopen($url,"w"); fputs($f,$s); fclose($f); } }

//	die("<p>Fatal error: '$s' not found! Install in Apache/PHP!!!");

// ��������, ���� �� ���������� curl (��������, � ���� �������� ����� ���, �������� � ����� fsockopen) � ������
// UPD: ��, �� ��������, ����������. ����� CURL!!!!!!!!!!!
// $s='curl_init'; if(function_exists($s)) idebug("CURL module - ok"); else die("<p>Fatal error: CURL not found! Install CURL module in Apache/PHP.");



$s='iconv'; if(function_exists($s)) idebug("ICONV module - ok"); else die("<p>Fatal error: ICONV not found! Install ICONV-module in Apache/PHP.");
$s='ImageCreateFromJpeg'; if(function_exists($s)) idebug("GB module - ok"); else idebug("Warning: GD not found, install GD-module in Apache/PHP (for working with photoalbum).");

$MYPAGE=str_replace(array('<','>',"'",'"'),array('%3C','%3E','%27','%22'),$_SERVER["REQUEST_URI"]);
list($mypage) = explode('?',$MYPAGE.'?',2);

$filehost_s=rtrim($_SERVER["DOCUMENT_ROOT"],'/').'/blog/';

if($filehost_s=='/www/htdocs/w00e4af2/blog/') {
	idebug("filehost: $filehost_s");
	chmod($filehost_s.'.htaccess',0666);
	chmod($filehost_s.'install.php',0666);
	chmod($filehost_s.'index.php',0666);
	chmod($filehost_s.'config.php',0666);
}

$vetoserver=$filehost_s."update_veto_files.txt";
$nadoserver=$filehost_s."update_nado_files.txt";



// =========== ��������� ����� (������ ��� ���� http://lleo.me/blog/) =============
if(strstr($_SERVER["HTTP_HOST"],'lleo.me')) { // ������ �� ������� lleo.me - ����� ������ ��� �������������� ���� � ������������?

if(isset($_GET['config'])) { //die('1');

$s='###<pre>'; $r=get_config_perem(); die($s.print_r($r,1));
foreach($r as $l) $s.="\$".$l."\n"; die($s); 

}

if(isset($_GET['probe'])) { die("".($_GET['probe']+12345)); } // ��������, ��� ������ ��������

if(isset($_GET['load'])) { // ������ ��������� ����
	$filehost=rtrim($_SERVER["DOCUMENT_ROOT"],'/').'/blog/';
	$f=$_GET['load'];
	$f=str_replace('..','#',$f);
	$f=str_replace('./','#',$f);
	$f=str_replace(chr(0),'#',$f);
	$f=preg_replace("/(\.{2,}|[^0-9a-z\-\_\.\!\/]+)/si",'#',$f);
	if(strstr($f,'#')) die('��� � ���');

	$veto=get_veto_files($vetoserver,0);
	$a=explode('/',$f);

	foreach($a as $l) if(in_array($l,$veto)) die('error veto');

	if(!is_file($filehost.$f)) die('error not_found: '.$filehost_s.$f);
	if(($txt=file_get_contents($filehost.$f))==false) die('error not_found: '.$filehost_s.$f);
	if(preg_match("/\.php$/si",$f)) $txt=preg_replace("/[\n\r]+\/\*\s*lleo\s*\*\/[^\n\r]+/si","\n",$txt);

	if(!empty($_GET['mode'])&&$_GET['mode']=='view') die(highlight_string($txt,1));

	if(!empty($_GET['mode'])&&$_GET['mode']=='install') {
		header("Content-Type: application/octet-stream; charset=Windows-1251");
		die($txt);
	}


	header("Content-Type: text/plain; charset=Windows-1251");
	die($txt);
}

if((sizeof($_POST) and !isset($_POST['action'])) or (isset($_GET['action']) and $_GET['action']=='test')) { // �������� ������ � ������ ��������

	header("Content-Type: text/plain; charset=Windows-1251");
	$filehost=$filehost_s;
	$veto_dir=get_veto_files($vetoserver);
	$my_files=get_dvijok_files($filehost."*",strlen($filehost));
	foreach($my_files as $l=>$n) { $ll=strtr($l,'#','.');
		if(isset($_POST[$l])) { if($_POST[$l]!=$n) { print "upd: $ll\n"; } unset($_POST[$l]); }
		else { print "add: $ll\n"; }
	}

	$my_config=get_config_data();
	foreach($my_config as $l=>$n) { $c='config:'.$l; if(isset($_POST[$c])) unset($_POST[$c]); else print "add: config:$l = $n\n"; }

	foreach($_POST as $l=>$n) { $e=strstr($l,'config:'); if(!$e) print "del: ".strtr($l,'#','.')."\n"; else print "del: $e\n"; }

	print file_get_contents($nadoserver);

	exit;
}

}
// =========== / ��������� ����� =============

if(is_file("config.php")) {
	$configphp=true;
	idebug("config.php found - ok");
	include "config.php";
	if(setconf()==1) require "config.php";
} else if(is_file("config.php.tmpl")) {
	idebug("Restore config.php from config.php.tmpl?");
		$s=file_get_contents("config.php.tmpl");
		$s=preg_replace_callback("/_md5_/","md5rand",$s);

/*
function next_year($matches)
{
  // as usual: $matches[0] is the complete match
  // $matches[1] the match for the first subpattern
  // enclosed in '(...)' and so on
  return $matches[1].($matches[2]+1);
}
echo preg_replace_callback(
            "|(\d{2}/\d{2}/)(\d{4})|",
            "next_year",
            $text);

*/

		fileput("config.php",$s);
	setconf();
	exit;
} else {

	// ��������, ����� �� ������ ��������� ����� �����
	$f='_temp_test.0';
	$e=0; $i=rand(0,100);	
	if(is_file($f)) { if(unlink($f)===false) $e=1; } // ���� ���� (�������, ������ ��?), �� �� ��������� - ������
	if(fileput($f,$i)===false) $e=2; // ���� �� ������� ������� - ������
	if(($s=file_get_contents($f))===false or $s!=$i) $e=3; // ���� �� ������� �������� - ������
	if(unlink($f)===false) $e=4; // ���� �� ������� �� ����� ������� - ������
	if($e) die("<p>Fatal error #$e: wrong permissions. You can allow to write in all this folder!");
	idebug("Permissions - ok");

	// ��������, ��������� �� ������ ������������ ������ � �������� �������
	if(($s=file_get_contents('http://lleo.me/blog/install.php?probe='.$i))===false or ($i+12345)!=intval($s))
	die("<p>Fatal error: unable to connect http://lleo.me/blog/<p><b>".h($s)."</b>");
	else idebug("Connect to http://lleo.me/blog/ - ok");
}


if(!is_file('.htaccess') and is_file('htaccess') and $msq_host!='') {
	$s=file_get_contents("htaccess");
	$s=str_replace('RewriteBase /blog/','RewriteBase /'.$blogdir,$s);
	if(fileput(".htaccess",$s)===false) die("<p>Fatal error: can't write '.htaccess'");
	idebug("Create .htaccess - ok");
}

//  ���������� � �������
if(!empty($admin_hash1) and !empty($koldunstvo1)
or !empty($admin_hash) and !empty($koldunstvo)) {

//	if(md5($_SERVER["HTTP_USER_AGENT"].$_SERVER["HTTP_ACCEPT_LANGUAGE"]
//	.$_SERVER["HTTP_ACCEPT_ENCODING"].$_SERVER["HTTP_ACCEPT_CHARSET"].$admin_hash.$koldunstvo)==$_COOKIE["adm "])

if(isset($_COOKIE["adm2"]) && $_COOKIE["adm2"]==$admin_hash1
|| isset($_COOKIE["adm"]) && $_COOKIE["adm"]==md5($_SERVER["HTTP_USER_AGENT"].$_SERVER["HTTP_ACCEPT_LANGUAGE"].$_SERVER["HTTP_ACCEPT_ENCODING"].$_SERVER["HTTP_ACCEPT_CHARSET"].$admin_hash.$koldunstvo
) ) { $admin=1; idebug("Admin login - succsess"); }
	else { $admin=0; idebug("You are not admin! What are you doing here?"); }

} else { $admin=1; idebug("Admin passworis d not define: admin is anybody, who open this page - ok"); }

if(empty($filehost)) {
	$blogdir=substr(str_replace(strstr_true($mypage,'/'),'',$mypage),1);
	$filehost=rtrim($_SERVER["DOCUMENT_ROOT"],'/').'/'.$blogdir;
	idebug("\$filehost = $filehost - ok");
}

$vetomyfiles=$filehost."update_veto_my_files.txt";

// =============== �������� ����� ================
if(!$admin) die("Admin only"); // ����������� ����������� ������ - �����

function mkdir_fileblog($l) { global $filehost;
	$a=explode('/',$l); unset($a[sizeof($a)-1]);
	$dir=$filehost;
	foreach($a as $d) { if($d!='') {
		$dir.=$d.'/';
		if(!is_dir($dir)) {
			if(dirput($dir)===false) die("<font color=red>ERROR mkdir '".h($dir)."'</font>");
		}
	}}
}

function load_fileblog($l) { global $filehost;
	$s=file_get_contents('http://lleo.me/blog/install.php?load='.urlencode($l));
	if(substr($s,0,5)=='error') {
		print "<font color=red>ERROR LOAD FILE '".h($l)."' - ".h($s)."</font>";
		return false; }

	mkdir_fileblog($l);
//---------------------------- ���� �� ���� �������� -------------
if(!empty($GLOBALS['www_design']) && preg_match("/\.css$/si",$l)) {
        $s=preg_replace("/url\([\'\"]*\/blog\/design\/(.*?)[\'\"]*\)/si",'url('.$GLOBALS['www_design']."$1)",$s);
        $s=preg_replace("/\@charset\s[\'\"]*windows-1251[\'\"]*/si",'@charset "'.$GLOBALS['wwwcharset'].'"',$s);
        $s=str_replace('{www_design}',$GLOBALS['www_design'],$s);
}
//----------------------------------------------------------------
	if(fileput($filehost.$l,$s)===false) print "<font color=red>ERROR WRITE FILE '".h($l)."'</font>";
	return true;
}


// ============== ���������� �������� ��������� =========================
if( isset($_POST['action']) ) { unset($_POST['action']);

	$veto='';
	$conf='';

foreach($_POST as $n=>$l) { list($n,)=explode('_',$n,2); $l=urldecode($l);
	
//	print "<br>".h($l)." = ".h($n);

	if(substr($l,0,3)=='no:') { list($l,)=explode(' ',substr($l,3),2); $veto.="$l\n"; continue; }

	if(substr($l,0,7)=='config:') {	$l=substr($l,7); // � ��������
		if($conf=='') $conf=file_get_contents('config.php');
		if($n=='add') {
			$l=str_replace('_md5_',md5rand(),$l); // ���� ��������� �������
			$conf=preg_replace("/\n\s*\?>\s*$/s","\n\n\$".$l."\n?>\n",$conf);
		}
		if($n=='del') $conf=preg_replace("/\n(\s*[\$]".$l."\s*=[^\n]+)/s","// delete this: $1",$conf);
	continue; }
	
	// � �������
	$file=$filehost.$l;

	if($n=='del') {
		if(!is_file($file)) print "<br><font color=red>DELETE: file not found '".h($file)."'</font>";
		else { copyf($file,$file.'.old'); unlink($file); }
	}

	if($n=='add') {	load_fileblog($l); }
	if($n=='upd') {	copyf($file,$file.'.old'); load_fileblog($l); }
	if($n=='mkdir') { mkdir_fileblog($l); }
}
	if($conf!='' && fileput('config.php',$conf)===false) die('Write error: config.php');
	if(fileput($vetomyfiles,$veto)===false) die('Write error: '.$vetomyfiles);
}

// ==================== back ===================================
if(isset($_GET['action']) and $_GET['action']=='back') {

        $p=get_dvijok_files_old($filehost."*");
	foreach($p as $l) { $lnew=substr($l,0,strlen($l)-4);
		if(rename($l,$lnew)===false) die("Fatal error: can\'t rename file: ".$l." to ".$lnew);
		print "<br>restore: ".$lnew;
	}
	print "<p><hr>"; unset($_GET['action']);
}
// ==================== clean ===================================
if(isset($_GET['action']) and $_GET['action']=='clean') {
        $p=get_dvijok_files_old($filehost."*");
	foreach($p as $l) { 
		if(unlink($l)===false) die("Fatal error: can\'t delete file: ".$l);
		print "<br>delete: ".$l;
	}
	print "<p><hr>"; unset($_GET['action']);
}

// ==================== check ===================================
if(isset($_GET['action']) and $_GET['action']=='check') {

$veto_dir=get_veto_files($filehost."update_veto_files.txt");
$my_files=array_merge( get_dvijok_files($filehost."*",strlen($filehost)) , get_config_perem() );

$s=curl_post( "http://lleo.me/blog/install.php", $my_files );

if($s=='1' or $s=='') die('no new updates');

$nado=explode("\n",$s);
$all=array();	// $del=array(); $upd=array(); $add=array(); $mkdir=array(); $addconfig=array();
if(sizeof($nado)) foreach($nado as $l) if($l!='') {
	if(!strstr($l,' ')) die('Error: '.$l);
	list($o,$file)=explode(' ',$l,2); $file=trim($file);

	if($o=='add:') $all[$file]=array('act'=>'add','color'=>'green'); // $add[]=$file;
	elseif($o=='upd:') $all[$file]=array('act'=>'upd','color'=>'blue'); //$upd[]=$file;
	elseif($o=='del:') $all[$file]=array('act'=>'del','color'=>'red'); //$del[]=$file;
	elseif($o=='addconfig:') $all[$file]=array('act'=>'add_config','color'=>'green'); //$addconfig[]=$file;
	elseif($o=='delconfig:' and is_file('config.php')) $all[$file]=array('act'=>'del_config','color'=>'red'); //$addconfig[]=$file;
	elseif($o=='mkdir:' and !is_dir($filehost.$file)) $all[$file]=array('act'=>'mkdir','color'=>'green'); //$mkdir[]=$file;
} unset($nado);

$veto_my=get_veto_files($vetomyfiles,0);

$s='';

$s.=print_oo($all);

// die("<pre>".print_r($all,1));

//$c=print_o($add,'add','green'); if($c!='') $s.="<h1>Add new files:</h1>$c";
//$c=print_o($del,'del','red'); if($c!='') $s.="<h1>Delete files:</h1>$c";
//$c=print_o($upd,'upd','blue'); if($c!='') $s.="<h1>Update files:</h1>$c";
//$c=print_o($mkdir,'mkdir','magenta'); if($c!='') $s.="<h1>Make folder:</h1>$c";
//$c=print_o($addconfig,'add_config','pink'); if($c!='') $s.="<h1>Add to config.php:</h1>$c";


if($s!='') {
print "<form method=post action='$mypage'>".$s."<p>
<input type=hidden name='action' value='Update'>
<input type=submit value='Update'></form>";
exit;
} else 	print "<p>no new update<p><hr>"; unset($_GET['action']);

}


// ==================== �� ����� ===================================
if(!isset($_GET['action'])) { die("<center>
<table><tr>
<td><form method=get action='$mypage'><input type=hidden name='action' value='check'><input type=submit value='Check'></form></td>
<td><form method=get action='$mypage'><input type=hidden name='action' value='back'><input type=submit value='Back'></form></td>
<td><form method=get action='$mypage'><input type=hidden name='action' value='clean'><input type=submit value='Clean *.old'></form></td>
</tr></table>
<p><a href='".'/'.$blogdir."admin'>admin</a>
</center>");
}
// ==================== �� ����� ===================================

function print_oo($a) { global $filehost,$veto_my; $rez=strlen($filehost);
	ksort($a);
	$k=0;
	$s='';
	$lastdir='';
	foreach($a as $ll=>$l) { $act=$l['act']; $color=$l['color'];
		if(strstr($ll,'config:')) { if(!is_file('config.php')) continue;
			$otstup=''; $dirname='config:'; $filename=' $'.substr($ll,strlen('config:'));
		} else {
			$filename=strstr_true($ll,'/');
			$dirname=substr($ll,0,strlen($ll)-strlen($filename));
			$otstup=str_repeat("&nbsp;",substr_count($ll, '/')*10);
		}
		if($dirname!=$lastdir) $s.="<p><b>".h($dirname)."</b>";
		$lastdir=$dirname;

	list($lvet,)=explode(' ',$ll,2);
	if(in_array($lvet,$veto_my)) { $s2=' selected'; $s1=''; $col='black'; } else { $s1=' selected'; $s2=''; $col=$color; }

		$s.="<br>$otstup"
."<select name='".$act."_".(++$k)."'><option value='".urlencode($ll)."'$s1>$act<option value='".urlencode("no:".$ll)."'$s2>no</select>"
."<font color='$col'>".h($filename)."</font>";
	}
	return $s;
}


function print_o($a,$act,$color) { global $filehost,$veto_my; $rez=strlen($filehost);
	$s='';
	$lastdir='';
	foreach($a as $ll) {
		$filename=strstr_true($ll,'/');
		$dirname=substr($ll,0,strlen($ll)-strlen($filename));
		$otstup=str_repeat("&nbsp;",substr_count($ll, '/')*10);
		if($dirname!=$lastdir) $s.="<p><b>".h($dirname)."</b>";
		$lastdir=$dirname;

	if(in_array($ll,$veto_my)) { $s2=' selected'; $s1=''; $col='black'; } else { $s1=' selected'; $s2=''; $col=$color; }

		$s.="<br>$otstup"
."<select name='".h(strtr($ll,'.','#'))."'><option value='$act'$s1>$act<option value='no'$s2>no</select>"
."<font color='$col'>".h($filename)."</font>";
	}
	return $s;
}

//====================================================================

function get_config_perem() { if(!is_file('config.php')) return array();
        $a=file('config.php'); $r=array();
	foreach($a as $l) if(preg_match("/^\s*[\$]([0-9a-z\_\-]+)\s*\=\s*/si",$l,$m)) $r['config:'.$m[1]]=$m[1];
        return $r;
}

function get_config_data() {
        $a=file('config.php.tmpl'); $r=array();
	foreach($a as $l) if(preg_match("/^\s*[\$]([0-9a-z\_\-]+)\s*\=\s*(.*?)$/si",$l,$m)) $r[$m[1]]=$m[2];
        return $r;
}


function get_dvijok_files($files,$filehostn) { global $stop,$veto_dir;
$stop=(intval($stop)?intval($stop):1000); if(!--$stop) die('stop error');
        $a=glob($files); $r=array();
        foreach($a as $n=>$l) if(!is_dir($l)){ if(!in_array($l,$veto_dir) and substr($l,strlen($l)-4,4)!='.old'
and substr($l,strlen($l)-4,4)!='.off'
and !preg_match("/\/pre\/[^\/]+$/si",$l)
) {

        $txt=file_get_contents($l);
        if(preg_match("/\.php$/si",$l)) $txt=preg_replace("/[\n\r]+\/\*\s*lleo\s*\*\/[^\n\r]+/si","\n",$txt);
        $r[strtr(trim(substr($l,$filehostn)),'.','#')]=md5($txt);
        }
        unset($a[$n]); }
        foreach($a as $l) if(!in_array($l,$veto_dir)) $r=array_merge($r,get_dvijok_files($l."/*",$filehostn));
        return $r;
}


function get_dvijok_files_old($files) { global $stop; $stop=(intval($stop)?intval($stop):1000); if(!--$stop) die('stop error 2');
        $a=glob($files); $r=array(); if($a===false or !sizeof($a)) return $r;
        foreach($a as $n=>$l) if(!is_dir($l)) { if( preg_match("/\.old$/si",$l) ) { $r[]=$l; } unset($a[$n]); }
        foreach($a as $l) $r=array_merge($r,get_dvijok_files_old($l."/*"));
        return $r;
}



function get_veto_files($f,$x=1) { global $filehost; $a=array(); if(is_file($f)) {
	foreach(explode("\n",file_get_contents($f)) as $l) { $l=trim($l," \n\t\r/"); if($l!='') $a[]=($x?$filehost:'').$l; }
}
	$a[]=($x?$filehost:'').'update_veto_my_files.txt';
//	$a[]=($x?$filehost:'').'update_veto_files.txt';
	$a[]=($x?$filehost:'').'update_nado_files.txt';
	$a[]=($x?$filehost:'').'config.php';
	return $a;
}

function strstr_true($s,$c) { $a=explode($c,$s); return ($a[sizeof($a)-1]); }

//---------------------------
function curl_post($url, $post) {

	// ������� �� ��������� ��� curl:

        //==================================================================================================
        // ��������� �������� ������ � ������ ����� POST-������ �� �������� ��� ������ ��� �������� CURL-���������
        // $filePath - ������ ��� (� �����) ����� ��� �������� ��� ������ ���� ������ ��� �������� (���� ������ ��� - '')
        // $urla - ����� �������, ����. http://lleo.aha.ru/blog/install
        // $ara - ������ ���������� POST, ����: array('action'=>'do','key'=>'1','user'=>123)
        // ���������� ����� ������� ���, ���� ������, ������, ������������ � 'ERROR:'
        $o=POST_file('',$url,$post);
        if(substr($o,0,6)=='ERROR:') { echo "<hr>$o "; return false; }
        return $o;

/*
	� ��� ��� curl-����� ���� ������:

        $p=array(); foreach($post as $n=>$v) $p[]=$n.'='.urlencode($v);
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
//      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $p));
        $c=curl_exec($ch);
        if(curl_errno($ch)!=0) $c=false;
        curl_close($ch);
        return $c;
*/
}

//==================================================================================================
// ��������� �������� ������ � ������ ����� POST-������ �� �������� ��� ������ ��� �������� CURL-���������
// $filePath - ������ ��� (� �����) ����� ��� �������� ��� ������ ���� ������ ��� �������� (���� ������ ��� - '')
// $urla - ����� �������, ����. http://lleo.aha.ru/blog/install
// $ara - ������ ���������� POST, ����: array('action'=>'do','key'=>'1','user'=>123)
// ���������� ����� ������� ���, ���� ������, ������, ������������ � 'ERROR:'
function POST_file($filePath,$urla,$ara,$port=80,$scheme='http',$charset='Windows-1251') {
        if(gettype($filePath)!='array') $filePath=array($filePath);
        $url=array_merge(array('scheme'=>$scheme,'port'=>$port),parse_url($urla));
        $bu="---------------------".substr(md5($filePath.rand(0,32000)),0,10); $r="\r\n"; $ft=$r.'--'.$bu.'--'.$r;

        // ������
        $dat=''; if(count($ara)) foreach($ara as $n=>$v) $dat.='--'.$bu.$r.'Content-Disposition: form-data; name="'.$n
.'"'.$r.$r.urlencode($v).$r;

        $len=strlen($dat); // ����� �����

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

        // ������� ����
        if(!$fp=fsockopen($url['host'],$url['port'])) return "ERROR: can't open url ".$url['host'].":".$url['port'];
        // ��������� ��������� � POST-������
        if(fputs($fp,$headers)===false) return "ERROR: can't send #1";

        if(count($files)) foreach($files as $l=>$fh) { // ������������ �����
                if(fputs($fp,$fh)===false) return "ERROR: can't send #2";
                // ������� ���� � ��������� ���
                if(($fp2=fopen($l,"rb"))===false) return "ERROR: can't open file '".$l."'";
                while(!feof($fp2)) if(fputs($fp,fgets($fp2,1024*100))===false) return "ERROR: can't send #4";
                fclose($fp2);
                // ��������� �������������� �����
                if(fputs($fp,$ft)===false) return "ERROR: can't send #5";
        }

        // � �������� �����
        $s=''; while(!feof($fp)) $s.=fgets($fp,4096); fclose($fp);
        if($s=='') return "ERROR: NO RESPONSE";
        list($h,$t)=explode($r.$r,$s,2);

        // ��������� ��������
        if(stristr($h,'301 Moved Permanently')) {
                return POST_file($filePath,preg_replace("/^.+Location: ([^\s]+).*$/si","$1",$h),$ara);
        }

return $t;
}
//==================================================================================================


















function idebug($s) { if(sizeof($_GET) or sizeof($_POST)) return; else print "<br><font color=gray size=2><i>$s</i></font>"; }


function setconf() { global $mypage;

if(isset($_POST['action']) and $_POST['action']=='Setconfig') {
// �������� �������� 

unset($_POST['action']);
	$f='config.php'; $s=file_get_contents($f);
	if(empty($_POST['blog_name']) and empty($blog_name)) $_POST['blog_name']=$_SERVER["SERVER_NAME"];
	if(empty($_POST['admin_site']) and empty($admin_site)) $_POST['admin_site']=$_SERVER["SERVER_NAME"];
	foreach($_POST as $n=>$v) { // if($v=='_md5_') $v=md5(rand(0,32565).time().rand(0,32565));
		unset($_POST[$n]);
		if(!preg_match("/[\n\r]+\s*[\$]".$n."\s*=\s*[\'\"][^\'\"]*[\'\"]/si",$s))
		$s=str_replace('?'.'>',"\n\$".$n."='".$v."';\n".'?'.'>',$s);
		else $s=preg_replace("/([\n\r]+\s*[\$]".$n."\s*=\s*)[\'\"][^\'\"]*[\'\"]/si",'$1"'.$v.'"$2',$s);
	}
//	die("<p><hr>".nl2br(h($s)));
	if(is_file('editconfig.flag')); { fileput($f,$s); unlink('editconfig.flag'); }
	return 1;
}

$e=0;
$s="<h1>config.php</h1>
<form action='".$GLOBALS['mypage']."' method='post'>";
$s.="<p><b>Hosting:</b>";
if(empty($GLOBALS['httpsite'])) { $e++; $s.="<br><input type=text size=30 name='httpsite' value='http://".$_SERVER["HTTP_HOST"]."'> server name (without folders, like 'http://lleo.me')"; }
if(empty($GLOBALS['blogdir']) and $GLOBALS['blogdir']!=='') { $s.="<br><input type=text size=30 name='blogdir' value='".substr(str_replace(strstr_true($mypage,'/'),'',$mypage),1)."'> folder ('blog/' or '' for root in site)"; }

$s.="<p><b>MySQL:</b>";
if(empty($GLOBALS['msq_host'])) { $e++; $s.="<br><input type=text size=30 name='msq_host' value='localhost'> MySQL-host (mysql.baze.lleo.me:64256)"; }
if(empty($GLOBALS['msq_login'])) { $e++; $s.="<br><input type=text size=30 name='msq_login' value='".$_SERVER["USER"]."'> MySQL login"; }
if(empty($GLOBALS['msq_pass'])) { $e++; $s.="<br><input type=text size=30 name='msq_pass' value=''> MySQL password"; }
if(empty($GLOBALS['msq_basa'])) { $e++; $s.="<br><input type=text size=30 name='msq_basa' value=''> MySQL basa"; }

$s.="<p><b>Admin:</b>";
if(empty($GLOBALS['admin_name'])) { $e++; $s.="<p><input type=text size=30 name='admin_name' value=''> admin name ('������ �������')"; }
if(empty($GLOBALS['admin_mail'])) { $e++; $s.="<p><input type=text size=30 name='admin_mail' value='".$_SERVER["SERVER_ADMIN"]."'> admin email ('lleo@aha.ru')"; }
if(empty($GLOBALS['admin_ljuser'])) { $s.="<p><input type=text size=30 name='admin_ljuser' value='lleo_run'> ljuser (���� ����������� ������ ����� ��)"; }

$s.="<p><b>������. ����� �������� ������ ��� ����� ������:</b>";
if(empty($GLOBALS['koldunstvo'])) { $e++; $s.="<p><input type=text size=30 name='koldunstvo' value='".md5rand()."'> ('� ������ ������� ���� ��������')"; }
if(empty($GLOBALS['hashinput'])) { $e++; $s.="<p><input type=text size=30 name='hashinput' value='".md5rand()."'> ('������ ���� ������ ����')"; }
if(empty($GLOBALS['hashlogin'])) { $e++; $s.="<p><input type=text size=30 name='hashlogin' value='".md5rand()."'> ('������������� �������� �������')"; }
if(empty($GLOBALS['newhash_user'])) { $e++; $s.="<p><input type=text size=30 name='newhash_user' value='".md5rand()."'> ('�������� ������ �������')"; }


$s.="
<input type=hidden name='action' value='Setconfig'>

<p><input type='submit' value='Create config.php'></form>";

if(!$e) return 0;

fileput('editconfig.flag','1');

die($s);
}


//----------------------------------------------------------------------
function filechmod($f,$p=0664){ chmod($f,$p); }
function dirchmod($d,$p=0775){ chmod($d,$p); }
function fileput($f,$s) { $o=file_put_contents($f,$s); filechmod($f); return $o; }
function dirput($d) { $o=mkdir($d); dirchmod($d); return $o; }
function copyf($a,$b) { $o=copy($a,$b); filechmod($b); return $o; }
function h($s) { return htmlspecialchars($s); }


function md5rand() { return hash_generate(); }
// md5(rand(0,32000).time().rand(0,32000));

// ������������� hash-������
function rando($x,$y){ $s='';
// die(exec("uname -a"));
// $k=10; while((--$k)&&!strlen($s)){ if(($g=fopen("/dev/random","rb"))===false) break; $s=fgets($g); fclose($g); }
        if(!strlen($s)) { // /dev/random �� ��������, ������� ������������ �������
                list($t,)=explode(" ",microtime()); mt_srand($t+mt_rand()); $a=mt_rand(0,$y)+$t;
        } else { for($f=1,$a=$j=0;$j<min(strlen($s),3);$j++,$f*=256) $a+=ord($s[$j])*$f; }
        return $x+$a%($y-$x);
}

function hash_generate(){
        $A='ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz01234567890';
        for($s='',$i=0,$n=strlen($A);$i<128;$i++) $s.=$A[rando(0,$n)]; return $s; //convert_uuencode($s);
}


?>