<?php // Отображение статьи с каментами - дата передана в $Date

function CONTENTS($e) { global $admin,$podzamok;

$SIZEDEFAULT=60;

$opt=array(
	''=>"по дате",
	'rating'=>"по посещениям",
	'mudoslov'=>"все нецензурные",
	'nemudoslov'=>"все цензурные",
	'mudoslov_rating'=>"рейтинг нецензурных",
	'nemudoslov_rating'=>"рейтинг цензурных"
);
if($podzamok) $opt=array_merge($opt,array('invis'=>"подзамочные заметки"));
if($admin) $opt=array_merge($opt,array('invis_adm'=>"совсем скрытые заметки"));

$o="<p><center><p>Найти: <INPUT type='text' style='color: #777777' size='7' value='поиск' onclick=\"majax('okno.php',{a:'search'})\">
<p><FORM METHOD=get ACTION='".$GLOBALS['mypage']."'>"
."<i>Показать:</i> ".selecto('mode',$_GET['mode'],$opt,
" onchange='for(var i=0;i<this.length;i++)if(this.options[i].selected){top.window.location=\"?mode=\"+this.options[i].value;break;}'")
."</form></center>";

//===========================================================
$g=$_GET['mode'];

// $sss="SELECT z.`num`,z.`Date`,z.`Header`,z.`view_counter` as `count`,z.`Access` FROM `dnevnik_zapisi` as z ";

function swhe($s) { return str_replace("{whe}",$s,
"SELECT z.`num`,z.`Date`,z.`Header`,z.`view_counter` as `count`,z.`Access`,count(*) as `count2`
FROM `dnevnik_zapisi` as z
left join `dnevnik_posetil` as r on z.`num`=r.`url`
{whe}
group by z.`num` "); }

if($g=='') $o.=pr_zapisi(swhe(WHERE())."ORDER BY z.`Date` DESC LIMIT ".$SIZEDEFAULT,true);
if($g=='more') $o.=pr_zapisi(swhe(WHERE())."ORDER BY z.`Date` DESC");
if($g=='rating') $o.=pr_zapisi_rating(swhe(WHERE())."ORDER BY z.`count` DESC");
if($admin && $g=='invis_adm') $o.=pr_zapisi(swhe("WHERE `Access`='admin'")."ORDER BY z.`Date` DESC");
if($podzamok && $g=='invis') $o.=pr_zapisi(swhe("WHERE `Access`='podzamok'")."ORDER BY z.`Date` DESC");
if($g=='mudoslov') $o.=pr_zapisi(swhe(WHERE(mudos('LIKE','OR')))." ORDER BY z.`DATE` DESC");
if($g=='mudoslov_rating') $o.=pr_zapisi_rating(swhe(WHERE(mudos('LIKE','OR'))) );
if($g=='nemudoslov') $o.=pr_zapisi(swhe(WHERE(mudos('NOT LIKE','AND')))." ORDER BY z.`DATE` DESC");
if($g=='nemudoslov_rating') $o.=pr_zapisi_rating(swhe(WHERE(mudos('NOT LIKE','AND'))));
// if(substr($g,0,3)=='st:') { $o.=pr_zapisi($sss."WHERE `Header` LIKE '".e(substr($g,3))."%' ORDER BY `Date` DESC"); }
return $o;
}

function pr_zapisi($sq,$more=false) { global $colnewcom,$SIZEDEFAULT;
	return pr_zapisi_($sq)
	.($more && ($colnewcom >= $SIZEDEFAULT)?"<p><a href='?mode=more'>показать больше &gt;&gt;</a>":'');
}

function mudos($like,$or) {
	$ara=file($GLOBALS['host_design'].'mudoslov.txt');
	$a=''; foreach($ara as $m) { $m=trim($m); if($m!='') $a.="z.`Body` $like '%".$m."%',"; }
	return str_replace(',',"\n".$or." ",trim($a,','));
}

//=========================================================================================================
function pr_zapisi_($sq) { global $numos,$colnewcom;
	$s=''; $year=0;
	$sql=ms($sq,"_a"); $s.=$GLOBALS['msqe'];
	$colnewcom=sizeof($sql); if(!$colnewcom) return $s;
	$s.="<h2>Заметок найдено: ".$colnewcom."</h2>";
	$s.="<ul>";

	$get=getget();

	foreach($sql as $p) {
			$p["count"]+=$p['count2'];
			// $p["counter"]=get_counter($p);
			$Date=$p["Date"];
			$head=($p["Header"]?" - ".h($p["Header"]):"");

		if(preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d.*)$/si",$Date,$m)) { $Y=$m[1]; $M=$m[2]; $D=$m[3];
			if($Y!=$year) { if($year) $s.="</ul>"; $s .= "<h2>".$Y." год</h2><ul>"; $year = $Y; }
			$detail=($numos?" ".$numos++.". ":'');
			$z=$detail."<a href='".get_link($Date).$get."'>$M-$D (".$p["count"].") ".$head."</a>";
			$z = zamok($p['Access']).$z;
			$s.="\t<li>".$z."</li>";

		} else {
			$z=$detail."<a href='".get_link($Date).$get."'>".$Date." (".$p["count"].") ".$head."</a>";
			$z = zamok($p['Access']).$z;
			$s.="<br>".$z;
		}
	}

	$s .= "</ul>";
	return $s;
}

//=========================================================================================================
function pr_zapisi_rating($l) {	$pp=ms($l,"_a");
	$ray=array(); foreach($pp as $n=>$p) { $c=$p['count']+$p['count2']; $pp[$n]["counter"]=$c; $ray[$n]=$c; } arsort($ray);
	
	$s = $GLOBALS['msqe'];
	$s .= "<h2>Записей ".sizeof($pp)." (сортировка по числу посетителей)</h2>";
	if($GLOBALS['old_counter']) $s.="<font color=red size=1>Учтите, что с февраля 2010 в движке подсчитываются не показы заметки, а реальные посетители, это число в несколько раз меньше.</font>";
	$s .= "<p><table>";

	$get=getget(); $i=0; foreach($ray as $n=>$l) { $p=$pp[$n];
		$s.= "<tr><td align=right>".(++$i).".</td><td><b>".$l."</b></td>"
		."<td><small>".zamok($p['Access'])."<a href='".get_link($p['Date']).$get."'>"
		.$p['Date'].($p["Header"]?" - ".h($p["Header"]):"")
		."</a></small></td></tr>";
	}

	$s.="</table>";
	return $s;
}

//=========================================================================================================

function getget() { $ge=$_GET;
	if(isset($ge['mode']) && $ge['mode']=='more') unset($ge['mode']);
	if(!sizeof($ge)) return '';
	$s="?"; foreach($ge as $a=>$b) $s.=urlencode($a)."=".urlencode($b)."&";
	return trim($s,"&");
}

?>