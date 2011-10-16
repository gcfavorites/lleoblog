<?php // hashdata v2

// ���������� ������� ������
$GLOBALS['hash_ru']='������������������'; $GLOBALS['hash_en']='ABCEHKMOPTXaceopxy'; //y
// ��������� ����� � ����-������������������:
$GLOBALS['hash_s1']='&#'; $GLOBALS['hash_e1']='#&'; // ������ �����
$GLOBALS['hash_s']='&#2'; $GLOBALS['hash_e']='2#&'; // ������� ����� (� ��� ��� �� ���������, ������� ��� �������������)


/* =======================================================================================================

$metka="Vot takoi tekstik ja zakodiroval. IP=10.8.0.100"; // ������ ������� �����
$text=file_get_contents("santa.html"); // ����� �����

print hashflash($text); // �������� ��� ������� ��� ������ � ���� ������

$text2 = hashdata($text,$metka); // ������������ ����� � �����

print datahash($text2); // ������ ����� �� ������

exit;

=========================================================================================================== */


// === hashdata ===

// �������������
function hashinit() { global $lju,$login;
$CommentaryName=tolat($_COOKIE['CommentaryName']);
$CommentaryAddress=$_COOKIE['CommentaryAddress'];
$sc=$_COOKIE['sc'];
return $login."
".$lju."
".$CommentaryName."
".$CommentaryAddress."
".$sc."
".$_SERVER['REMOTE_ADDR']."
".$_SERVER['HTTP_X_FORWARDED_FOR']."
".date('Y-m-d H:i:s')."
".$_SERVER['HTTP_USER_AGENT'];
}

// ������� � �������� (� ��������� ������� � 7 ���)
function tolat($s) {
	$r='�������������������������������������Ũ��������������������������';
	$l='abvgdeejzijklmnoprstufhccssiqqeuqABVGDEEJZIJKLMNOPRSTUFXCCSSIQQEUQ';
	return strtr($s,$r,$l);
}

// ��������������� ������� �������� �������� ����� � ������ � �������
function str2binm($str,$r=8) { for($s='',$end=strlen($str),$i=0; $i<$end; $i++) $s .= int2bin(ord($str[$i]),$r); return $s; }
function binm2str($str,$r=8) { for($s='',$end=strlen($str),$i=0; $i<$end; $i+=$r) $s.=chr(bin2int(substr($str,$i,$r),7)); return $s; }
function bin2int($c,$r=8) { return base_convert($c,2,10); }
function int2bin($c,$r=8) { return vsenuli(base_convert($c,10,2),$r); }
function vsenuli($s,$r=8) { $s='0000000'.$s; return substr($s,strlen($s)-$r,$r); }


// ��������� ������� �����, �������������� � ������ $text
function datahash($text) { global $hashdata_hash,$hash_s,$hash_e,$hash_s1,$hash_e1,$hash_en;
	$hashdata_hash='';
	preg_replace_callback("/(>[^<]+<)/si","data2hash",'<>'.$text.'<>'); // ������ ������� ������������������ �� ������

	$a=array();
	$a['v1']=hashdata_read($hashdata_hash,$hash_s1,$hash_e1,'ver 1.0'); // ���������� �������� ����� ������ ������

	$hashdata_hash=str_replace('111111','',$hashdata_hash); // ������ ���, ��� ������� ��������� ��������
	$hashdata_hash=str_replace('111110','11111',$hashdata_hash); //������� ������� 5 ������ ������

	$a['v2']=hashdata_read($hashdata_hash,$hash_s,$hash_e,'ver 2.0 (new)');

	if($a['v2']['try']!=0) return $a['v2']['data'];
	if($a['v1']['try']!=0) return $a['v1']['data'];
	return $a['v1']['data'];
}


function hashdata_read($hashdata_hash,$hash_s,$hash_e,$ver) {
	$hashdata_hash=strstr($hashdata_hash,str2binm($hash_s,7)); // ����������������� �� ������ �������
	$data=binm2str($hashdata_hash,7);
	preg_match_all('/'.$hash_s.'(.*?)'.$hash_e.'/si', $data.$hash_e , $m, PREG_SET_ORDER);
	$data=''; $try=0;

	foreach($m as $p) {
		$s=$p[1]; $s=str_replace($hash_e,'',$s);
		$code=substr($s,strlen($s)-1,1);
		$s=substr($s,0,strlen($s)-1);
		$hctrl=substr(md5($s),7,2); $hctrl=base_convert($hctrl,16,2); $hctrl=vsenuli($hctrl,7); $hctrl=binm2str($hctrl,7);

	$s=preg_replace("/([0-9a-f]{32})/si","<a href=/dnevnik/comments?mode=onesc&sc=$1>$1</a>",$s);

		if($code==$hctrl) { $try++; $s="<p><font color=green>$s</font>"; } else $s="<p><font color=red>$s</font>"; 
		$data .= "<p><blockquote style='border: 1px dashed rgb(255,0,0); padding: 20px; margin-left: 50px; margin-right: 50px; background-color: rgb(255,252,223);'><font size=1><b>$ver</b></font><p>$s</blockquote>";
	}
	if(sizeof($m)==0) return array('try'=>0,'data'=>$ver.' - �� �������');
	return array('try'=>$try,'data'=>$data);
}


function data2hash($r) { global $hash_ru,$hash_en,$hashdata_hash; $text=$r[1];
	if(str_replace("\n",'',$text)=='><') return $text;
	$end=strlen($text); for($i=0; $i<$end; $i++) { $c=$text[$i];
		if(strstr($hash_ru,$c)) $hashdata_hash .= '0';
		elseif(strstr($hash_en,$c)) $hashdata_hash .= '1';
	}
	return $text;
}

// �������� �� $text ������� ����� ������������������ ����� $data
function hashdata($text,$data) { global $hashdata_count,$sixed,$hashdata_end,$hashdata_hash,$hash_s,$hash_e;

	if(strstr($text,'<script')) return $text; // �� ���������� �� ��������� ������!

	$hctrl=substr(md5($data),7,2); // ��������� ����������� ����� ������� ������������������ $data
	$hctrl=base_convert($hctrl,16,2); $hctrl=vsenuli($hctrl,7);
	$hs=str2binm($data,7); // ��������� $data � ��������� 7-������ �������������

	$hashdata_hash=str2binm($hash_s,7).$hs.$hctrl.str2binm($hash_e,7); // start+.....data......+checksum+end

	$hashdata_hash=str_replace('11111','111110',$hashdata_hash); //�� ��������� ��������� 6 ������ ������

	$hashdata_count=0; // �������� ��������� $data
	$sixed=0; // �������� ��������� ���.������
	$hashdata_end=strlen($hashdata_hash); // ���������� ����� $data
	$text=preg_replace_callback("/(>[^<]+<)/si","hash2data",'<>'.$text.'<>'); $text=str_replace('<>','',$text);
	return $text;
}

function hash2data($r) { global $hash_ru,$hash_en,$hashdata_count,$sixed,$hashdata_hash,$hashdata_end; $s=''; $text=$r[1];
	if(str_replace("\n",'',$text)=='><') return $text;
	 $end=strlen($text); for($i=0; $i<$end; $i++) { $c=$text[$i];

		if(strstr($hash_en,$c)) { // $c='&#'.ord($c).';';
			$c=strtr($c,$hash_en,$hash_ru); // ������� �������, � ����� �������
			if(!$sixed) $sixed=6;
		}

		if(strstr($hash_ru,$c)) { // ����� �������
			if($sixed) { $sixed--; $c=strtr($c,$hash_ru,$hash_en); // �������� 1
			} else {
				if($hashdata_hash[$hashdata_count]=='1') $c=strtr($c,$hash_ru,$hash_en);
				if(++$hashdata_count == $hashdata_end) $hashdata_count=0;
			}
		}
		$s .= $c;
	}
return $s;
}


// ��������� ����������� ������ ��� �����
function hashflash($s) {return str_replace('<>','',preg_replace_callback("/(>[^<]+<)/si","hash2fl","<>$s<>")); }
function hash2fl($r) { return preg_replace("/([".$GLOBALS['hash_en'].$GLOBALS['hash_ru']."]+)/si","<font color=green>$1</font>",$r[1]); }

// function p($s,$color) { print "<br><font color=$color>".htmlspecialchars($s)."</font>"; }
// function pp($s,$color) { print "<font color=$color>".htmlspecialchars($s)."</font>"; }

?>