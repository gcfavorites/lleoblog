<?php // СКРИПТ ГОЛОСОВАНИЯ v.2

/*
Вот такой код вставляется в дневник:

{golosovalka ИМЯ_ВАШЕГО_ГОЛОСОВАНИЯ

1. Как вы думаете, что это?
-- Это голосование
-- Это проверка, как работает модуль голосования
-- Это проверка, как работает модуль, а потом будут объяснения

2. Как вы вообще сюда попали?
-- Зашел случайно
-- Подписан на здешний RSS
-- Да так, брожу, наблюдаю...

3. Нравится ли вам движок дневника?
-- По-моему пора закругляться: для теста достаточно и двух пунктов
-- Я шутник и член партии "Шаловливая Россия"
}

*/

$db_golosa='golosovanie2_golosa';
$db_result='golosovanie2_result';

// вот такая проверка на Пост
if(isset($_POST['golos_return'])) {
		include_once "../config.php";
		include_once $include_sys."_autorize.php";
		include $include_sys."_msq.php";
		include_once $include_sys."_antibot.php";
		die(post_code());
	}

$vopr=golos_chit($article["Body"]); // $gol_name - автоматически;

$golosoval=msq_exist($db_golosa,"WHERE name='".e($gol_name)."' AND (sc='".e($sc)."' OR ipipx='".e($IP.' '.$_SERVER['HTTP_X_FORWARDED_FOR'])."')");

//if($admin) $golosoval=true;

// взять результаты
if($golosoval) {
	$s=ms("SELECT text,n FROM $db_result WHERE name='".e($gol_name)."'",'_1',0);
	$go=unserialize($s['text']); $nn=$s['n'];
	}


$s=''; if($admin) $s.=nl2br(golos_recalculate($gol_name)).'<p>';

	$k=($nn?(640/$nn):0); // вычислилить коэффициент array_sum($go[$n])
	$kp=($nn?(100/$nn):0);

$n=0; foreach($vopr as $vop=>$var) { $n++;

	$s.="\n<p><b>$vop</b><br><ul>";

		foreach($var as $i=>$va) {

			if($golosoval) { // если голосовал
				$x=$go[$n][$i+1];
				$s .= "$va<br><img src=".$www_design."/e/gol.gif width=".floor($k*$x)." height=14>
<span class=br><b>".floor($kp*$x)."%</b> (".intval($x).")</span><p>";
			} else { // если не голосовал
				$gr = "grad_".$n."-".($i+1);
				$s .= "\n<input id='".$gr."' name='".$gol_name."_".$n."' type='radio' value='".($i+1)."'><label for='".$gr."'> $va</label><br>";
			}
		}

	$s.="</ul>\n";

	}


	$s="<center><table width=90% cellspacing=20><td align=left>".$s."</td></table></center>";




	if($golosoval) { // если голосовал

		$s = "<p>Проголосовали <b>$nn</b> человек:".$s."<p><center><b>спасибо, что проголосовали!</b></center>";
	} else { // если НЕ голосовал

include_once $include_sys."_antibot.php";
$s = "
<form name='golos_".$gol_name."' method=post action='".$wwwhost."include/golosovalka2.php'>
<input type=hidden name=hash value='".antibot_make()."'>
<input type=hidden name=golos_name value='".$gol_name."'>
<input type=hidden name=golos_return value='".$mypage."'>
<input type=hidden name=vopr value='".sizeof($vopr)."'>
".$s."
<table><tr valign=center>
	<td>антиспам:</td>
	<td>".antibot_img()."</td>
	<td><input class=t size=".$antibot_C." type=text name=code></td>
	<td><input type='submit' value='ПРОГОЛОСОВАТЬ'></td>
</tr></table>
</form>
";

}

$article['Body'] = preg_replace("/\{golosovalka[^\}]*\}/si",$s,$article['Body']);

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

	if(!antibot_check($_POST["code"],$_POST["hash"])) idie("Цифры с картинки указаны неверно, вернитесь.");

	$gol=array(); for($i=1;$i<=$vopr;$i++) { $g=intval($_POST[$gol_name.'_'.$i]);
		if(!$g) idie("Нельзя оставлять пункты невыбранными.<br>Пожалуйста вернитесь и сделайте выбор.");
		else $gol[$i]=$g;
	}

	if(!golos_update($gol_name,$gol)) idie("Ошибка: вы уже голосовали!");
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


function golos_update($name,$gol) { global $db_golosa,$sc,$IP;
  $ara=array(
	'name'=>e($name), 'sc'=>e($sc),
	'ipipx'=>e($IP.' '.$_SERVER['HTTP_X_FORWARDED_FOR']),
	'value'=>e(serialize($gol))
	);
  if(!msq_exist($db_golosa,"WHERE `name`='".$ara['name']."' AND (`sc`='".$ara['sc']."' OR `ipipx`='".$ara['ipipx']."')")) {
	msq_add($db_golosa,$ara); return true; } else return false;
}

function golos_calculate($name,$gol) { global $db_result;
	$g=ms("SELECT `n`,`text` FROM `$db_result` WHERE `name`='".e($name)."'","_1",0); if($g===false) $g=array();
	$n=$g['n'];
	$go=unserialize($g['text']); if($go===false) $go=array();
	foreach($gol as $i=>$j) $go[$i][$j]++; // добавить голос нынешний
	msq_add_update($db_result,array( 'name'=>e($name),'n'=>e(++$n),'text'=>e(serialize($go)) ),'name');
}


function golos_recalculate($name) { global $db_golosa,$db_result;

	$limit=1000;
	$start=0;

	$summ=0;
	$go=array();

	$mes='';

	$ct=0; while($ct++<100) {
		$pp=ms("SELECT `value` FROM `$db_golosa` WHERE `name`='".e($name)."' LIMIT $start,$limit","_a",0);
		if(!sizeof($pp)) { break; }
		$start+=$limit;
		foreach($pp as $p) {
			$g=unserialize($p['value']); if($g===false) { $mes.=' error 1'; break; }
			$summ++;
			foreach($g as $i=>$v) $go[$i][$v]++;
		}
	}



	$go0=unserialize(ms("SELECT `text` FROM `$db_result` WHERE `name`='".e($name)."'",'_l',0));
	$summ0=ms("SELECT `n` FROM `$db_result` WHERE `name`='".e($name)."'",'_l',0);

	$mmes='';

	if($summ!=$summ0) $mmes.="\nОПС! не сошлось число голосовавших: ".$summ0.", а правильно: ".$summ."\n";

	if(sizeof($go0)!=sizeof($go)) $mmes.="\nОПС! не равны суммы: в базе: ".sizeof($go0).", а правильно: ".sizeof($go)."\n";

	foreach($go as $i=>$g) {
	   if(sizeof($go0[$i])!=sizeof($g)) $mmes.="\n $i) не равны суммы: в базе: ".sizeof($go0[$i]).", а правильно: ".sizeof($g)."\n";
	   foreach($g as $k=>$l) if($go0[$i][$k]!=$l) $mmes.="\n $i($k): ".$go0[$i][$k]." != $l";
	}

	if($mmes=='') $mes .= '<font color=green>Пересчет: ВСЕ СОШЛОСЬ</font>'; else {
	$mes.=$mmes;
	if($GLOBALS['IS_EDITOR']) {
	$mes .= '<p><font color=red>UPDATE! '.
	msq_add_update($db_result,array( 'name'=>e($name),'n'=>e($summ),'text'=>e(serialize($go)) ),'name')
	.'</font>';
	}

	}

	return $mes;

}


function golos_chit($text) { // распознать голосовалку
	preg_match("/\{golosovalka\s+([^\s]+)([^\}]+)\}/si",$text,$m);
	$GLOBALS['gol_name']=trim($m[1]);
	preg_match_all("/#+\n*([^#]+)/si","#".str_replace("\n\n","#",$m[2]),$km);
	$vopr=array(); foreach($km[1] as $k=>$mm) {
		$z=trim( preg_replace("/^([^\n]+)\n.*$/si","$1",$mm) );
		preg_match_all("/\n+[\s\-]+([^\n]+)/si",trim($mm),$vv);
		if($z && sizeof($vv[1])) $vopr[$z]=$vv[1];
	}
	return $vopr;
}

?>