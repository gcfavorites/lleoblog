<?php /* ���� '� �� mary kay'

���� ������ ��� ������ �� ������� ������ �����. ��� ��������� ������ �� ������ �������� �������� � ��������.

������ ����� �����: ����� ����������� | � ������ ������ ���������:

������ ��� ��������, ������� � �����������, ���� ������ ��� (��������� ����) | ��������� | ���� | ��������� ��������

<script>function menukay(i,img,text) { idd('img'+i).src=img; zabil('text'+i,text); }</script>

{_MENUKAY:

http://lleo.aha.ru/blog/photo/1.jpg | ���� ������ | /popa/gnezdo/index.html | ���� ������ - ���������� ���������, ����������� ��� �� �������� �������. ������ �������� �� ������ � ������ � ����������� ��������������. ���� ���������� �������� �������� ������ ������� ����� ����� ������ �� ����� ��� ��������� ������.

http://lleo.aha.ru/blog/photo/2.jpg | ����� �� ������� ����� | /postel/podushka | ��������� ��������� ��������� ���������� ���. ��� ��� ����� �������, �� ��� �� ����� � �����? � ���, ��� ������� �� ������� �������, ��������� ��� ������ '���������� ����� ������ ������'.

http://lleo.aha.ru/blog/photo/3.jpg | ����� � ������������ | /medical/super/spina | ����� �����? ������� ����� ���������? �� ������������� ���������� ������, ����� 50% ����������� ������ �������� �� ������ � ������������. �������-������� �������� ���������� �� ������ ��� �������� �� ���� �������!

_}

*/

SCRIPTS("SilkTest procedure","
function silktest(i){
//	var e=window.document.silktest_'+i].1.value;
	var e=window.document.silktest_1.silktest_1_2.value;
	alert(e);
	// window.document.f_name.i_name.value='��������� ����';
	// if(img.indexOf('/')) idd('img'+i).src=img; else idd('img'+i).src='".$GLOBALS['foto_www_small']."'+img;
	// zabil('text'+i,text);
}");

function SILK_TEST($e) { global $silktest_n; $silktest_n++;

	list($vopros,$otvet)=explode("\n---",$e,2);
	$vopr=get_vopross($vopros);
	$otv=get_vopross_simple($otvet);




// 	dier($otv);

	$g=0; $s="<form name='silktest_".$silktest_n."'>"; foreach($vopr as $v=>$p) {
		$s.="<p><b>".c($v)."</b><ul>";
		$gr="silktest_".$silktest_n."_".++$g;

		foreach($p as $x=>$l) {
			list($n,$t)=explode(' ',$l,2);
			$s.="<label><input name='$gr' type='radio' value='".intval(c($n))."'> ".c($t)."</label><br>";
		}
		$s.="</ul>";
	}
	$s.="<p><input type=button value='�������� ���������' onclick=\"silktest('".$silktest_n."')\"></form><div id='silktest_".$silktest_n."_rez'></div>";

	return $s;


// window.document.f_name.i_name.value="��������� ����";

SCRIPTS("MenyKay procedure"," function menukay(i,img,text) { 

if(img.indexOf('/')) idd('img'+i).src=img; else idd('img'+i).src='".$GLOBALS['foto_www_small']."'+img;

zabil('text'+i,text); } ");

$m=explode("\n",$e); foreach($m as $n=>$l) { if(c($l)=='') { unset($m[$n]); continue; }
	list($img,$txt,$link,$text)=explode('|',$l,4);
	$m[$n]=array('img'=>c($img),'txt'=>c($txt),'link'=>c($link),'text'=>c($text));
}


$s=''; foreach($m as $p) {
  $s.="<p><a onmouseover=\"menukay('".$menykay_n."','".$p['img']."','".njs($p['text'])."')\" href='".$p['link']."'>".$p['txt']."</a>";
}

return "<center><table><tr valign=center>"
."<td><img id='img".$menykay_n."' src='".(strstr($m[0]['img'],'/')?$m[0]['img']:$GLOBALS['foto_www_small'].$m[0]['img'])."' hspace=5 vspace=5 border=0></td>"
."<td>{_CENTER:".$s."_}</td></tr></table>"
."<div align=justify id='text".$menykay_n."'>".$m[0]['text']."</div></center>";

}


function get_vopross($s) { // ���������� �����������
        preg_match_all("/#+\n*([^#]+)/si","#".str_replace("\n\n","#",$s),$km);
        $vopr=array(); foreach($km[1] as $m) {
		$z=trim( preg_replace("/^([^\n]+)\n.*$/si","$1",$m) );
                preg_match_all("/\n+[\s\-".chr(151)."]+([^\n]+)/si",trim($m),$v);
                if($z && sizeof($v[1])) $vopr[$z]=$v[1];
        }
        return $vopr;
}

function get_vopross_simple($s) { preg_match_all("/#+\n*([^#]+)/si","#".str_replace("\n\n","#",$s),$km); return $km; } // ���������� ������

?>