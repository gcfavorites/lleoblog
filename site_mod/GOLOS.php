<?php // ������ ����������� v.2
/*

���� ������ ���� �� ��������!!!
� ������������ � ���� ���� ���!!!!


��� ����� ��� ����������� � �������:

{_ GOLOS: ���_������_�����������:

1. ��� �� �������, ��� ���?
-- ��� �����������
-- ��� ��������, ��� �������� ������ �����������
-- ��� ��������, ��� �������� ������, � ����� ����� ����������

2. ��� �� ������ ���� ������?
-- ����� ��������
-- �������� �� ������� RSS
-- �� ���, �����, ��������...

3. �������� �� ��� ������ ��������?
-- ��-����� ���� ������������: ��� ����� ���������� � ���� �������
-- � ������ � ���� ������ "���������� ������"

_}
*/

// ���������� � ����� ������� �������� ��� �����?


$GLOBALS['GOLOS_db_golosa']='golosovanie2_golosa';
$GLOBALS['GOLOS_db_result']='golosovanie2_result';

if(isset($_POST['golos_return'])) {
		include "../config.php";
		include $include_sys."_autorize.php";
//		include $include_sys."_msq.php";
		include $include_sys."_antibot.php";
		die(post_code());
	}



function GOLOS($e) { global $GOLOS_db_golosa,$GOLOS_db_result, $sc,$IP,$admin,$www_design,$wwwhost,$mypage,$antibot_C;

	list($gol_name,$vopr)=explode(':',$e,2); $gol_name=c($gol_name); $vopr=golos_chit($vopr);

	$golosoval=msq_exist($GOLOS_db_golosa,"WHERE name='".e($gol_name)."' AND (sc='".e($sc)."' OR ipipx='".e($IP.' '.$_SERVER['HTTP_X_FORWARDED_FOR'])."')");

//	if($admin) $golosoval=false;

	// ����� ����������
	if($golosoval) {
		$s=ms("SELECT `text`,`n` FROM ".$GOLOS_db_result." WHERE name='".e($gol_name)."'",'_1');
		$go=unserialize($s['text']); $nn=$s['n'];
	}

	$s=''; if($admin) $s.=nl2br(golos_recalculate($gol_name)).'<p>';

	$k=($nn?(640/$nn):0); // ����������� ����������� array_sum($go[$n])
	$kp=($nn?(100/$nn):0);

	$n=0; foreach($vopr as $vop=>$var) { $n++;

	$s.="\n<p><b>$vop</b><br><ul>";

		foreach($var as $i=>$va) {

			if($golosoval) { // ���� ���������
				$x=$go[$n][$i+1];
				$s .= "$va<br><img src=".$www_design."e/gol.gif width=".floor($k*$x)." height=14>
<span class=br><b>".floor($kp*$x)."%</b> (".intval($x).")</span><p>";
			} else { // ���� �� ���������
				$gr = "grad_".$n."-".($i+1);
				$s .= "\n<input id='".$gr."' name='".$gol_name."_".$n."' type='radio' value='".($i+1)."'><label for='".$gr."'> $va</label><br>";
			}
		}

	$s.="</ul>\n";

	}


	$s="<center><table width=90% cellspacing=20><td align=left>".$s."</td></table></center>";


	if($golosoval) { // ���� ���������

		$s = "<p>������������� <b>$nn</b> �������:".$s."<p><center><b>�������, ��� �������������!</b></center>";
	} else { // ���� �� ���������

include_once $GLOBALS['include_sys']."_antibot.php";

$s = "
<form name='golos_".$gol_name."' method=post action='".$wwwhost."site_mod/GOLOS.php'>
<input type=hidden name=hash value='".antibot_make()."'>
<input type=hidden name=golos_name value='".$gol_name."'>
<input type=hidden name=golos_return value='".$mypage."'>
<input type=hidden name=vopr value='".sizeof($vopr)."'>
".$s."
<table><tr valign=center>
	<td>��������:</td>
	<td>".antibot_img()."</td>
	<td><input class=t size=".$antibot_C." type=text name=code></td>
	<td><input type='submit' value='�������������'></td>
</tr></table>
</form>
";

}


//$article['Body'] = preg_replace("/\{golosovalka[^\}]*\}/si",$s,$article['Body']);
return $s;
}


//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================

function post_code() {
	$gol_name=$_POST['golos_name'];
	$vopr=intval($_POST['vopr']);
	if(!$vopr) die('error n');

	if(!antibot_check($_POST["code"],$_POST["hash"])) idie("����� � �������� ������� �������, ���������.");

	$gol=array(); for($i=1;$i<=$vopr;$i++) { $g=intval($_POST[$gol_name.'_'.$i]);
		if(!$g) idie("������ ��������� ������ ������������.<br>���������� ��������� � �������� �����.");
		else $gol[$i]=$g;
	}

	if(!golos_update($gol_name,$gol)) idie("������: �� ��� ����������!");
	golos_calculate($gol_name,$gol);
	redirect($_POST['golos_return']);
}

//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================
//=======================================================================================


function golos_update($name,$gol) { global $GOLOS_db_golosa,$sc,$IP;
  $ara=array(
	'name'=>e($name), 'sc'=>e($sc),
	'ipipx'=>e($IP.' '.$_SERVER['HTTP_X_FORWARDED_FOR']),
	'value'=>e(serialize($gol))
	);
  if(!msq_exist($GOLOS_db_golosa,"WHERE `name`='".$ara['name']."' AND (`sc`='".$ara['sc']."' OR `ipipx`='".$ara['ipipx']."')")) {
	msq_add($GOLOS_db_golosa,$ara); return true; } else return false;
}

function golos_calculate($name,$gol) { global $GOLOS_db_result;
	$g=ms("SELECT `n`,`text` FROM `$GOLOS_db_result` WHERE `name`='".e($name)."'","_1",0); if($g===false) $g=array();
	$n=$g['n'];
	$go=unserialize($g['text']); if($go===false) $go=array();
	foreach($gol as $i=>$j) $go[$i][$j]++; // �������� ����� ��������
	msq_add_update($GOLOS_db_result,array( 'name'=>e($name),'n'=>e(++$n),'text'=>e(serialize($go)) ),'name');
}





function golos_recalculate($name) { global $GOLOS_db_golosa,$GOLOS_db_result;

	$limit=1000;
	$start=0;

	$summ=0;
	$go=array();

	$mes='';

	$ct=0; while($ct++<100) {
		$pp=ms("SELECT `value` FROM `".$GOLOS_db_golosa."` WHERE `name`='".e($name)."' LIMIT $start,$limit","_a",0);
		if(!sizeof($pp)) { break; }
		$start+=$limit;
		foreach($pp as $p) {
			$g=unserialize($p['value']); if($g===false) { $mes.=' error 1'; break; }
			$summ++;
			foreach($g as $i=>$v) $go[$i][$v]++;
		}
	}


	$p=ms("SELECT `n`,`text` FROM `$GOLOS_db_result` WHERE `name`='".e($name)."'",'_1',0);
	$go0=unserialize($p['text']); $summ0=$p['n'];

	$mmes='';

	if($summ!=$summ0) $mmes.="\n���! �� ������� ����� ������������: ".$summ0.", � ���������: ".$summ."\n";

	if(sizeof($go0)!=sizeof($go)) $mmes.="\n���! �� ����� �����: � ����: ".sizeof($go0).", � ���������: ".sizeof($go)."\n";

	foreach($go as $i=>$g) {
	   if(sizeof($go0[$i])!=sizeof($g)) $mmes.="\n $i) �� ����� �����: � ����: ".sizeof($go0[$i]).", � ���������: ".sizeof($g)."\n";
	   foreach($g as $k=>$l) if($go0[$i][$k]!=$l) $mmes.="\n $i($k): ".$go0[$i][$k]." != $l";
	}

	if($mmes=='') $mes .= '<font color=green>��������: ��� �������</font>'; else {
	$mes.=$mmes;
	if($GLOBALS['admin']) {
	$mes .= '<p><font color=red>UPDATE! '.
	msq_add_update($GOLOS_db_result,array( 'name'=>e($name),'n'=>e($summ),'text'=>e(serialize($go)) ),'name')
	.'</font>';
	}

	}

	return $mes;

}


function golos_chit($s) { // ���������� �����������
	preg_match_all("/#+\n*([^#]+)/si","#".str_replace("\n\n","#",$s),$km);
	$vopr=array(); foreach($km[1] as $k=>$mm) {
		$z=trim( preg_replace("/^([^\n]+)\n.*$/si","$1",$mm) );
		preg_match_all("/\n+[\s\-]+([^\n]+)/si",trim($mm),$vv);
		if($z && sizeof($vv[1])) $vopr[$z]=$vv[1];
	}
	return $vopr;
}

?>