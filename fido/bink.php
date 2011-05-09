<?php $help="
lbink.php 1.1 ����� LLeo Kaganov, lleo@aha.ru, +7-916-6801685

bink.php ����� �������� � ���������� �������� ������ �� http (POST-������, ��� �������� ������
���� �� ������� ��������� ��������� ����������, ��� ������ �� �����������) ��� ��� email (����� � POP3
����, �������, �� �������� � Google, ������ ��� ��� �� ������������ POP3, � �����-�� ttps-�����).

���� ����������� �� ���� � �������:
	bink.php - 	������� ���� ���� ���
			������� ���� �� http, ���� ���� ���������� POST-������

	bink.php?mail - ������ ��������� 1 ������ ����� �� POP3 (������� ��� ���� �� �� �����),
			���� ��� ���������� ����� �������, ��������� �� � IN-�����

	bink.php?send - ��������� �� OUT-����� �� ������, ��� ��� �������, ����������� � nodelist.php
			�� ��������� ��������� �� http (url � password � nodelist.php),
			���� url � nodelist.php �� ������, � ������ mail - ��������� ������� �� mail

	bink.php?list -	������� �������� ����� bink.php

";

header('Content-type: text/plain');

// print_r(iconv_get_encoding()); exit;


// ����� �������� ���-����:
$lbink_logfile="lbink_log.txt";


// ����� - ����� ������:
include("bink.conf.php"); // ��� ������ ��� ������:
/*
$fidodir_in="in/";
$fidodir_out="out/";
$fido_mynode="5020/313";
$fidomail_from="fido@lleo.aha.ru";
$fidomail_smtp="smtp.lleo.aha.ru";
$fidomail_subj="FIDO boundles - ".date("Y-m-d H:i:s");
$fidomail_pop="pop.lleo.aha.ru";
$fidomail_user="fido";
$fidomail_pass="cHeGevaRa";
*/

// � ����� ����������� �������:
$fidonodelist="nodelist.php"; // ��� ����� ����������:
/*
<?php ���� �������� �� ������������ ��� ���������, ������ �� php, �� �� ����������
#  node (��� 2:), bink-url (���������� ���), bink-mail (����� ��� �������, ���� ��� url), password (������)
	5020/1519, http://lushnikov.net/fido/bink.php ,, kolbasakolbasa
	5020/313, http://lleo.aha.ru/fido/bink.php ,fido@lleo.aha.ru, kolbasakolbasa
?>
*/

// ���. ��������� ���������. ����� ���� ��� ���:
// ===============================================================================
// ===============================================================================
// ===============================================================================
// ===============================================================================

$a=$_SERVER['QUERY_STRING']; if($a=="list") die(file_get_contents(__FILE__));
function h($s) { return htmlspecialchars($s); }
function dier($s) { die(print_r($s,1)); }
// ============== ����� ������� ================
function get_nodelist($s) { $s=file($s); $r=array();
	foreach($s as $l) { $l=trim($l); if(strstr($l,'<?')||strstr($l,'?>')||$l==''||substr($l,0,1)=='#') continue;
		list($node,$addr,$mail,$pass)=explode(',',$l,4); $r[trim($node)]=array('addr'=>trim($addr),'mail'=>trim($mail),'pass'=>trim($pass));
	} return $r;
}


$res='OK'; $log='';
function oklog($s) { global $log; $log.=$s."\n"; }
function erlog($s) { global $log,$res; $res='ERROR'; $log.=$s."\n"; }
function erdie($s) { global $res; $res='ERROR'; ldie("Error: ".$s); }
function okdie($s) { ldie("--- ".$s); }
function ldie($s) { global $log,$res,$lbink_logfile; $s=$res."
*** ".date("Y-m-d H:i:s")." start lbink.php: ".$log.$s."
*** ".date("Y-m-d H:i:s")." end lbink.php\n";
	$l=fopen($lbink_logfile,"a+"); fputs($l,"\n".$s); fclose($l);
	die($s); }

// ============== �������� ����� � ���� ================
if($a=='mail') { if(!isset($fidomail_from)) okdie("EMAIL not settings (lbink.php?mail)");
	oklog("EMAIL CHECK (lbink.php?mail)");
	while(1) {
		$r=readmail($fidomail_pop,$fidomail_user,$fidomail_pass,1); if($r===false) okdie("mailbox empty");
		if(isset($r['files'])) foreach($r['files'] as $fil=>$bin) {
			if(fido_validname($fil)) {
				oklog("OK: file `$fil` saved"); file_put_contents($fidodir_in.$fil,$bin);
				if(isset($fidodir_dup) && is_dir($fidodir_dup)) file_put_contents($fidodir_dup.date("Y-m-d_h-i-s")."_".$fil,$bin);
				} else erlog("mail: attach name error `$fil`:\n-------------------\n".h(print_r($r,1))."\n-------------------");
		} else { erlog("mail: no attach:\n-------------------\n".h(print_r($r,1))."\n-------------------"); }
	}
}

ini_set("display_errors","1"); ini_set("display_startup_errors","1"); ini_set('error_reporting', E_ALL); set_time_limit(0);

$nodelist=get_nodelist($fidonodelist);

// ============== ������� ����� ================
if(isset($_POST['login'])) { oklog("HTTP-load (from POST[])");
	$node=urldecode($_POST['login']);
	if(!isset($nodelist[$node])) erdie("unknown node ".h($node)); //." ".dier($nodelist));
	if($_POST['pass']!=md5($nodelist[$node]['pass'].$node)) erdie("wrong password");

	if(count($_FILES)>0) foreach($_FILES as $FILE) if(is_uploaded_file($FILE["tmp_name"])) { $f=$FILE["name"];
		if(!preg_match("/^[0-9a-z\.]+$/si",$f) || strstr($f,'..') || substr($f,0,1)=='.') erdie("�����, �����?");
		copy($FILE["tmp_name"],$fidodir_in.$f);
		if(isset($fidodir_dup) && is_dir($fidodir_dup)) copy($FILE["tmp_name"],$fidodir_dup.date("Y-m-d_h-i-s")."_".$f);
		oklog("OK: `".h($FILE["tmp_name"])."` saved: `".h($fidodir_in.$f)."`");
	}
	okdie("files saved: ".count($_FILES));
}
// ============== ��������� ����� 1 ���� ================
if($a=='send') { oklog("HTTP-send ***");
	$f=glob($fidodir_out."*.?lo"); if(!sizeof($f)) okdie("No new files");
	foreach($f as $le) {
		$lo=basename($le); $node=base_convert(substr($lo,0,4),16,10)."/".base_convert(substr($lo,4,4),16,10);
		if(!isset($nodelist[$node])) continue; // ��� ������ � ����� �������� - ������, �� ��� ��� ��� ���� �����
		$n=$nodelist[$node];

	//die("SEND TO NODE: $node ".$n['addr']);

		$bsy=substr($le,0,-4).'.bsy'; if(is_file($bsy)) { erlog("file busy `$bsy`"); continue; }
file_put_contents($bsy,"");
		$s=file($le); $ara=array('login'=>$fido_mynode,'ver'=>'lbink 1.0'); $ara['pass']=md5($n['pass'].$ara['login']);

		foreach($s as $l) { $l=trim($l); if($l=='') continue; $c=substr($l,0,1); $b=substr($l,1);
			if($c=='~') continue; // ~ ����p�p����� ����� �� ���� ��������.
			if($c=='#') file_put_contents($b,''); // # ��p����� �� �y����� �����;
			if($c=='^') {  // ^ y������ ������ ���� ����� y������� �������:
				$to=$n['addr']; if($to=='') $to=$n['mail']; if($to=='') erlog("wrong destination in Nodelist");
					$o=fidosend($b,$to,$ara);
if(substr($o,0,2)=='OK') unlink($b); else erlog("answer: '".h($o)."'");
			}
		} unlink($le); if(is_file($bsy)) unlink($bsy);
	oklog("OK: success connect with node $node, answer: '".$o."'");
	}
okdie("$fidodir_out is done");
}
// ============== ���� ================
die($help); 

//==================================================================================================
function fidosend($b,$to,$ara='') {
	if(strstr($to,'@')) { // ��������� �� email
		global $fidomail_from,$fidomail_smtp,$fidomail_subj;
		$mail=new html_mime_mail();
		$bn=basename($b);
		$mail->add_html("<html><body><h2>����� �������� �����: ".h($bn)."</h2></body></html>");
		$mail->add_attachment(substr($b,0,-strlen($bn)),$bn);
		$mail->build_message('koi8'); // ���� �� "win", �� ����p���� koi8
		$mail->send($fidomail_smtp,$to,$fidomail_from,$fidomail_subj);
		return "OK\n<br>file `".h($b)."` send to `".h($to)."`";
	}
	$url=array_merge(array('scheme'=>'http','port'=>'80'),parse_url($to));
	return sendFile($url['host'],$url['port'],$to,$b,basename($b), "oo", $ara);
}


// �������� ��������� �� ��� �����
function fido_validname($s) { list($n,$r)=explode('.',$s);

	if(strlen($n)!=8 or strlen($r)!=3 or !preg_match("/^[0-9a-z]{8}$/si",$n)) return 0;

	if(	stristr($r,'pkt') or
		preg_match("/^(mo|tu|we|th|fr|sa|su)[0-9]$/si",$r) or
		preg_match("/^(m|t|w|t|f|s|s)[0-9]{2}$/si",$r)
	) return 1;

	return 0;
}

//==================================================================================================
function sendFile($host, $port="80", $path, $filePath, $fileName, $fileField, $fields = array()) {

if(!is_file($filePath)) { $s="<br>file not found: $filePath<br>"; return $s; }

$boundary="---------------------".substr(md5(rand(0,32000)),0,10);
$fieldsData=''; if(count($fields)) foreach($fields as $field=>$value) {
    $fieldsData.="--$boundary\r\n"."Content-Disposition: form-data; name=\"$field\"\r\n\r\n".urlencode($value)."\r\n";
}

$fileHeaders = "--$boundary\r\n"
."Content-Disposition: form-data; name=\"$fileField\"; filename=\"$fileName\"\r\n"."Content-Type: Windows-1251\r\n\r\n";

$fileHeadersTail = "\r\n--$boundary--\r\n";

$filesize = filesize($filePath);

$contentLength = strlen($fieldsData) + strlen($fileHeaders) + $filesize + strlen($fileHeadersTail);

$headers= "POST $path HTTP/1.0\r\n"."Host: $host\r\n"."Referer: $host\r\n"."Content-type: multipart/form-data, boundary=$boundary\r\n"
	."Content-length: $contentLength\r\n\r\n".$fieldsData.$fileHeaders;

  if(!$fp=fsockopen($host,$port)) return "ERROR_01";
  if(fputs($fp,$headers)===false) return "ERROR_1";
	  $fp2=fopen($filePath,"rb"); if($fp2===false) return "ERROR_02";
	  while(!feof($fp2)) if(fputs($fp, fgets($fp2, 1024*100))===false) return "ERROR_2";
	  fclose($fp2);
  fputs($fp, $fileHeadersTail);
  $s=''; while(!feof($fp)) $s.=fgets($fp,4096); fclose($fp);

  if($s=='') return "_NO RESPONSE_";
  list($h,$t)=explode("\r\n\r\n",$s,2); return nl2br($t);
}
//==================================================================================================
class html_mime_mail {
 var $headers; 
 var $multipart; 
 var $mime; 
 var $html; 
 var $parts = array(); 

function html_mime_mail($headers="") { $this->headers=$headers; } 
function add_html($html="") { $this->html.=$html; } 

function build_html($orig_boundary,$kod) { 
	$this->multipart.="--$orig_boundary\n"; 
	if ($kod=='w' || $kod=='win' || $kod=='windows-1251') $kod='windows-1251';
	else $kod='koi8-r';
	$this->multipart.="Content-Type: text/html; charset=$kod\n"; 
	// $this->multipart.="BCC: del@ipo.spb.ru\n";
	$this->multipart.="Content-Transfer-Encoding: Quot-Printed\n\n"; 
	$this->multipart.="$this->html\n\n"; 
}

function add_attachment($path="", $name = "", 
$c_type="application/octet-stream") { 
if(!file_exists($path.$name)) { erlog("(add_attachment): file `$path.$name` dosn't exist"); return; }
	$fp=fopen($path.$name,"r");
	if(!$fp) { erlog("(add_attachment): file `$path.$name` coudn't be read"); return; } 
	$file=fread($fp, filesize($path.$name));
	fclose($fp);
	$this->parts[]=array("body"=>$file, "name"=>$name,"c_type"=>$c_type); 
}

function build_part($i) { 
	$message_part=""; 
	$message_part.="Content-Type: ".$this->parts[$i]["c_type"]; 
	if($this->parts[$i]["name"]!="") $message_part.="; name = \"".$this->parts[$i]["name"]."\"\n"; 
	else $message_part.="\n"; 
	$message_part.="Content-Transfer-Encoding: base64\n"; 
	$message_part.="Content-Disposition: attachment; filename = \"".$this->parts[$i]["name"]."\"\n\n"; 
	$message_part.=chunk_split(base64_encode($this->parts[$i]["body"]))."\n";
	return $message_part;
}

function build_message($kod) { 
	$boundary="=_".md5(uniqid(time())); 
	$this->headers.="MIME-Version: 1.0\n"; 
	$this->headers.="Content-Type: multipart/mixed; boundary=\"$boundary\"\n"; 
	$this->multipart=""; 
	$this->multipart.="This is a MIME encoded message.\n\n"; 
	$this->build_html($boundary,$kod); 
	for($i=(count($this->parts)-1); $i>=0; $i--) $this->multipart.="--$boundary\n".$this->build_part($i); 
	$this->mime = "$this->multipart--$boundary--\n"; 
}

function send($server, $to, $from, $subject="", $headers="") { $resp='';
	$headers="To: $to\nFrom: $from\nSubject: $subject\nX-Mailer: The Mouse!\n$headers";
	$fp = fsockopen($server, 25, &$errno, &$errstr, 30);
	if(!$fp) erdie("(send): Server $server. Connection failed: $errno, $errstr");
	fputs($fp,"HELO $server\n");
	fputs($fp,"MAIL FROM: $from\n");
	fputs($fp,"RCPT TO: $to\n");
	fputs($fp,"DATA\n");
	fputs($fp,$this->headers);
	if(strlen($headers)) fputs($fp,"$headers\n");
	fputs($fp,$this->mime);
	fputs($fp,"\n.\nQUIT\n");
	while(!feof($fp)) $resp.=fgets($fp,1024);
	fclose($fp);
}
}


//=======================================================================================
// _mailpop.php
// �����, ������ ���-�� � ��������� ��������� � ���������� ������������... ����, ��� ��� ����� ��������!!!
// � � ������ ��������! � �������� �� �������, � ��������� ����������� ������ � ������� ��� �� ��������... ����!!!
//=======================================================================================

function readmail($mail_pop,$mail_user,$mail_pass,$delmail=0) {

	$pop_conn = fsockopen($mail_pop,110,$errno, $errstr, 10); $code=fgets($pop_conn,1024);
	fputs($pop_conn,"USER $mail_user\r\n");	$code= fgets($pop_conn,1024);
	fputs($pop_conn,"PASS $mail_pass\r\n");	$code= fgets($pop_conn,1024);

	fputs($pop_conn, "STAT\r\n"); $s=fgets($pop_conn, 1024); $a=explode(' ',$s); if(!$a[1]) return false; // ��� �����

	fputs($pop_conn,"RETR 1\r\n"); $text=get_data($pop_conn); // $text ��� ������ � �����������
	$struct=fetch_structure($text); // ��������� ������ �� ��������� � ����
	$mass_header=decode_header($struct['header']); // ������������ ��������� �� ��������
	$mass_header["subject"] = decode_mime_string($mass_header["subject"]); //�������� ��������� ����� �������������

	// ������ �������� ��������� Content-Type, ��� ��� �����������. ���������, ��� � ������, ������ ����� ��� ��� � �����.
	// Content-Type: text/plain; charset=Windows-1251 ��� ������� ��������� ������
	// Content-Type: multipart/mixed; boundary="_----------=_118224799143839" ��� ��������� ������ �� ���������� ������, � ���������� �������.
	$type = $ctype = $mass_header['content-type'];
	$ctype = split(";",$ctype);
	$types = split("/",$ctype[0]);
	$maintype = trim(strtolower($types[0])); // text ��� multipart
	$subtype = trim(strtolower($types[1])); // � ��� ������(plain, html, mixed)

	if(!isset($mass_header['body'])) $mass_header['body']='';

	// ������ ��������� ��� ����������� ������
	// ���� ��� ������� ��������� ���������� (����� ��� html) ��� ��������
	if($maintype=="text") {
	    // $subtype ����� ������������ ��� ���������� ��� ����������� ��������� ������ ��� html
	    // ��� �������� ������ ��������� ����
	    // �������� ���� ������ � �������, �� ���������������. � ��� �� �������� ���������, ������������� � ���, ��� ���� ������������ ������.
	    $body = compile_body($struct['body'],$mass_header["content-transfer-encoding"],$mass_header["content-type"]);
	    $mass_header['body'].=$body; // print $body;
	}

	// ������ ���������� �������, ���� ������ ����� ��������� ������ ������.
	// ��� ������������ ������� signed,mixed,related, �� ���� ��� ������ alternative, ������� ������ ��� ��������������� ����������� ������.
	// ��������, ������ � html � � ���� �� ����� �������� �������������� ��������� ����������.
	// ��������� ������� ��� ���� ������ � �������� ��������� "MIME" (RFC1521) (http://webi.ru/webi_files/26_15_f.html)
	elseif($maintype=="multipart" and ereg($subtype,"signed,mixed,related")) {
	    // �������� �����-����������� ������ ������
	    $boundary=get_boundary($mass_header['content-type']);

	    // �� ������ ����� ����������� ��������� ������ �� �����
	    $part = split_parts($boundary,$struct['body']);

	// ������ ������������ ������ ����� ������
	for($i=0;$i<count($part);$i++) {

	        // ��������� ������� ����� �� ���� � ���������
	        $email=fetch_structure($part[$i]); $header=$email["header"]; $body=$email["body"];

	        // ��������� ��������� �� ������
	        $headers=decode_header($header); $ctype=$headers["content-type"]; $cid=$headers["content-id"];
	        $Actype=split(";",$headers["content-type"]); $types=split("/",$Actype[0]); $rctype=strtolower($Actype[0]);

	        // ������ ���������, �������� �� ��� ����� ������������� ������
	        $is_download = (
// ������ ����� ���������� �����!!!
//ereg("name=",$headers["content-disposition"].$headers["content-type"])
preg_match("/name\s*=\s*/si",$headers["content-disposition"].$headers["content-type"])
 || $headers["content-id"] != "" || $rctype == "message/rfc822");
	        // ������ ������ � ������� ���� ���� �����, ���� ��� ������� �����
	        if($rctype == "text/plain" && !$is_download) {
	            $body = compile_body($body,$headers["content-transfer-encoding"],$headers["content-type"]);
		    $mass_header['body'].=$body; // print $body;
	        }

	        // ���� ��� html
	        elseif($rctype == "text/html" && !$is_download) {
	            $body = compile_body($body,$headers["content-transfer-encoding"],$headers["content-type"]);
		    $mass_header['body'].=$body; // print $body;
	        }

	        // � �������, ���� ��� ����
	        elseif($is_download) {
	            // ��� ����� ����� ��������� �� ���������� Content-Type ��� Content-Disposition
	            $cdisp=$headers["content-disposition"]; $ctype=$headers["content-type"];
	            $ctype2=explode(";",$ctype); $ctype2=$ctype2[0];
	            $Atype=split("/",$ctype); $Acdisp=split(";",$cdisp);
			// ������ ����� ���������� �����!!! �������, ��� ����:
			if(!preg_match("/filename\s*=\s*(.*$)/si",$Acdisp[1],$regs) and
			!preg_match("/name\s*=\s*(.*?)$/si",$ctype,$regs)) $filename=rand(0,32000).".bin";
			else $filename=trim($regs[1],"\r\n\t\'\" ");
		            // ��� �������� ��� �����, ������ ��� ����� ������������
		            $filename = trim(decode_mime_string($filename));

	            // ������ ������ ���� � ����������.
	            $body = compile_body($body,$headers["content-transfer-encoding"],$ctype);
			if(!isset($mass_header['files'])) $mass_header['files']=array();
			$mass_header['files'][$filename]=$body;
	            // $ft=fopen($filename,"wb"); fwrite($ft,$body); fclose($ft);
	     	  }
	    }
	}
	$mass_header['header']=$header;

	if($delmail) { fputs($pop_conn, "DELE 1\r\n"); $mass_header['del']=fgets($pop_conn,1024); }
	fputs($pop_conn, "QUIT\r\n"); $mass_header['quitl']=fgets($pop_conn,1024);
	fclose($pop_conn);
	return $mass_header;
}

// ��� �������� �����, ��� �� ��������� ������� � ���������� ����������,
// �������� ���� ������ ����� ��������� ��� =?windows-1251?B?7/Du4uXw6uA=?=
// ��� ����� ������ � ����� ��������������� ��� �������
function decode_mime_string($subject) {
    $string = $subject;
    if(($pos = strpos($string,"=?")) === false) return $string;
    while(!($pos === false)) {
        $newresult .= substr($string,0,$pos);
        $string = substr($string,$pos+2,strlen($string));
        $intpos = strpos($string,"?");
        $charset = substr($string,0,$intpos);
        $enctype = strtolower(substr($string,$intpos+1,1));
        $string = substr($string,$intpos+3,strlen($string));
        $endpos = strpos($string,"?=");
        $mystring = substr($string,0,$endpos);
        $string = substr($string,$endpos+2,strlen($string));
        if($enctype == "q") $mystring = quoted_printable_decode(ereg_replace("_"," ",$mystring));
        else if ($enctype == "b") $mystring = base64_decode($mystring);
        $newresult .= $mystring;
        $pos = strpos($string,"=?");
    }

    $result = $newresult.$string;
    if(ereg("koi8", $subject)) $result = convert_cyr_string($result, "k", "w");
    if(ereg("KOI8", $subject)) $result = convert_cyr_string($result, "k", "w");
    return $result;
}

// �������������� ���� ������.
// ���� ������ ����� ���� ������������ � ������ ������� �������� ���� ������ � ���������� ���.
// ��� �� � ��������� ����� ����� ���������������� ���� ��������.
function compile_body($body,$enctype,$ctype) {
    $enctype = explode(" ",$enctype); $enctype = $enctype[0];
    if(strtolower($enctype) == "base64")
    $body = base64_decode($body);
    elseif(strtolower($enctype) == "quoted-printable")
    $body = quoted_printable_decode($body);
    if(ereg("koi8", $ctype)) $body = convert_cyr_string($body, "k", "w");
    return $body;
}

// ������� ��� ������������ ����� boundary �� ��������� Content-Type
// boundary ��� ����������� ����� ������ ���������� � ������,
// ��������, ����� �������� ���� �� ������ ������
function get_boundary($s){

	if(preg_match("/boundary *= *[\"\'](.*?)[\"\']/si",$s,$r)) return "--".trim($r[1]); return false;

// �� ��� ����� ����� ����� ���������� � ��������������� ����� �� ������, ����:
//    if(preg_match('/boundary[ ]?=[ ]?(["]?.*)/i',$ctype,$regs)) {
//        $boundary = preg_replace('/^\"(.*)\"$/', "\1", $regs[1]);
//        return trim("--$boundary");
//    }
}

// ���� ������ ����� �������� �� ���������� ������ (�����, ����� � �.�.)
// �� ��� ������� �������� ����� ������ �� ����� (� ������), �������� ����������� boundary
function split_parts($boundary,$body) {
    $startpos = strpos($body,$boundary)+strlen($boundary)+2;
    $lenbody = strpos($body,"\r\n$boundary--") - $startpos;
    $body = substr($body,$startpos,$lenbody);
    return explode($boundary."\r\n",$body);
}

// ��� ������� �������� ��������� �� ����.
// � ���������� ������ � ����������� � �����
function fetch_structure($email) {
    $ARemail = Array();
    $separador = "\r\n\r\n";
    $header = trim(substr($email,0,strpos($email,$separador)));
    $bodypos = strlen($header)+strlen($separador);
    $body = substr($email,$bodypos,strlen($email)-$bodypos);
    $ARemail["header"] = $header;
    $ARemail["body"] = $body;
    return $ARemail;
}

// ��������� ��� ��������� � ������� ������, � ������� ������ ������� �������� �������������� ����������
function decode_header($header) { $lasthead='HEAD0';
    $headers = explode("\r\n",$header);
    $decodedheaders = array();
    for($i=0;$i<count($headers);$i++) {
        $thisheader = trim($headers[$i]);
        if(!empty($thisheader))
        if(!ereg("^[A-Z0-9a-z_-]+:",$thisheader)) $decodedheaders[$lasthead] .= " $thisheader";
        else {
            $dbpoint = strpos($thisheader,":");
            $headname = strtolower(substr($thisheader,0,$dbpoint));
            $headvalue = trim(substr($thisheader,$dbpoint+1));
            if($decodedheaders[$headname] != "") $decodedheaders[$headname] .= "; $headvalue";
            else $decodedheaders[$headname] = $headvalue;
            $lasthead = $headname;
        }
    }
    return $decodedheaders;
}

// ��� ������� ��� ��� �������. ��� �������� ������ � ��������� �� �����, ������� �������� �������� � ����� ������.
function get_data($pop_conn)
{
    $data="";
    while (!feof($pop_conn)) {
        $buffer = chop(fgets($pop_conn,1024));
        $data .= "$buffer\r\n";
        if(trim($buffer) == ".") break;
    }
    return $data;
}

?>