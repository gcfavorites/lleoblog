<?php /* ������� ������

������ ������� �� ���� ������, ����������� ������� "---". ������ �� ����������� ������� � �������� ������� (��� ���������� � -). � �������� ������ ������ ��������� ����� - ��� ����� ������ (��� ����� �� ���������, � ����� ��� ��������). ����� ����� ������ ��� �����.

�� ������ ����� �� ����������� (� ���������� � ������ ������) ��������� ������� �� ����������� - �� ����� ��������� ������. ������ ����������� ����� - �� �������� ��������� ������ �������� ���� �����. � ������ �������� ������ ������ ������ ������ 0: �� ��������, ���� ������� ����� ������ �� ����, �� ������ �����, ���������� � ��������� �������� ������.

��������! ������ ����� ���� ����� ����� �������� �� �����, ������ ��� ��� ���� ��������� ���� ������� ������ ��� �������, ������� ��������� ���� �������������, ����� �� ������ ��� ������������.

{_SILK_TEST:
1. ������� ����� �����?
- 0 ����
- 2 ����
- 4 �����
- 5 ����

2. ������� ����� ����?
- 1 ����
- 5 �����
- 4 ����
- 3 �����

---

0 �� ���������, �� ������� ����� {sum} ������.

3 �� ������ � ����������� � ���� �������.

8 �� �������.

10 �� - �����! � ��� ������ - �� {sum}!
_}

*/

SCRIPTS("SilkTest procedure","

function check_radio(e) { for(var i=0;i<e.length;i++) if(e[i].checked) return e[i].value; return 'undefined'; }

var silktest_ara;

function silktest(n,x,ara){ 
	var sum=0; for(var i=1;i<=x;i++) {
		var l=check_radio(document.getElementsByName('silktest_'+n+'_'+i));
		if(l=='undefined') {
			helps('silktest_error','<fieldset><legend>������</legend>����� '+i+' �� ��������.<br>��������� ��� ������!</fieldset>');
			posdiv('silktest_error',-1,-1); return;
		} sum=(sum+1*l);
	}

	silktest_ara=ara; ajaxon(); setTimeout('silktest_print('+sum+')',1500);
}

function silktest_print(sum){ ajaxoff();
	for(var i in silktest_ara) { if(i>sum) break; var txt=silktest_ara[i].replace(/\{sum\}/gi,sum); }
	helps('silktest_otvet','<fieldset><legend>�����:</legend><div style=\"width:700px\" align=justify>'+txt+'</div></fieldset>');
	posdiv('silktest_otvet',-1,-1);
}");

function SILK_TEST($e) { global $silktest_n; $silktest_n++;
	list($vopros,$otvet)=explode("\n---",$e,2);
	$vopr=get_vopross($vopros);
	$otv=get_vopross_simple($otvet);

	$g=0;
	$s=""; foreach($vopr as $v=>$p) {
		$s.="<p><b>".c($v)."</b><ul>";
		$gr="silktest_".$silktest_n."_".++$g;

		foreach($p as $x=>$l) {
			list($n,$t)=explode(' ',$l,2);
			$s.="<label><input name='$gr' type='radio' value='".intval(c($n))."'> ".c($t)."</label><br>";
		}
		$s.="</ul>";
	}
	$s.="<p><input type=button value='�������� ���������' onclick=\"silktest('$silktest_n',".sizeof($vopr).",silktest_".$silktest_n."_ara)\">";

	$c="var silktest_".$silktest_n."_ara={"; foreach($otv as $l) {
		list($x,$txt)=explode(' ',$l,2); $x=intval($x); $txt=c($txt);
		$c.=intval($x).":'".str_replace(array("&","\\","'",'"',"\n","\r"),array("&amp;","\\\\","\\'",'&quot;',"\\n",""),c($txt))."', ";
	} $c=trim($c,' ,')."};";

	SCRIPTS("silktest_".$silktest_n."_DATA",$c);

	return $s;
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

function get_vopross_simple($s) { preg_match_all("/#+\n*([^#]+)/si","#".str_replace("\n\n","#",$s),$km); return $km[1]; } // ���������� ������

?>