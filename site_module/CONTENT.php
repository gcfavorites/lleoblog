<?php // Отображение статьи с каментами - дата передана в $Date

function CONTENT($e) { global $admin,$podzamok;

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

$o="<p><center><FORM METHOD=get ACTION='".$GLOBALS['mypage']."'>"
."<i>Показать:</i> ".selecto('mode',$_GET['mode'],$opt,
" onchange='for(var i=0;i<this.length;i++)if(this.options[i].selected){top.window.location=\"?mode=\"+this.options[i].value;break;}'")
."</form></center>";

//===========================================================
$g=$_GET['mode'];

// $sss="SELECT `num`,`Date`,`Header`,`view_counter` as `count`,`Access` FROM `dnevnik_zapisi` ";

$sss="SELECT z.`num`,z.`Date`,z.`Header`,z.`Access`,z.`view_counter` as `count`, r.`count_new`
FROM `dnevnik_zapisi` as z
LEFT JOIN ( SELECT COUNT(*) as `count_new` FROM `dnevnik_posetil` as r WHERE r.`url`=z.`num` )";

/*
$sss="SELECT z.`num`,z.`Date`,z.`Header`,z.`Access`,sum(t1.cnt) as `count` FROM `dnevnik_zapisi` as z
LEFT JOIN (
SELECT r.`num`,r.`view_counter` as `cnt` FROM `dnevnik_zapisi` as r
UNION ALL
SELECT p.`num`, COUNT(*) as `cnt` FROM `dnevnik_posetil` as p GROUP BY P.`num`
) as t1
WHERE z.`num`=t0.`num`";


$sss="SELECT z.`num`,z.`Date`,z.`Header`,z.`Access`,sum(t1.cnt) as `count` FROM `dnevnik_zapisi` as t0
LEFT JOIN (
SELECT z.`view_counter` as `cnt` FROM `dnevnik_posetil` as z WHERE z.`url`=t0.`num`
UNION ALL
SELECT COUNT(*) as `cnt` FROM `dnevnik_posetil` as p WHERE p.`url`=t0.`num`
) as t1";

$sss="SELECT z.`num`,z.`Date`,z.`Header`,z.`Access`,z.`view_counter` as `count`, count(p.*) as `count_new`
FROM `dnevnik_zapisi` as z
LEFT JOIN `dnevnik_posetil` as p on p.`url`=z.`num`";


--------------------------------------
SELECT Z.num, Z.text, sum(t1.cnt) FROM `ZAMETKI` as t0
LEFT JOIN (
SELECT Z.coun_old as cnt FROM `ZAMETKI` as Z WHERE Z.num = t0.num
UNION ALL
SELECT COUNT(*) as cnt FROM `POSETIL` as P WHERE P.num = t0.num
) as t1


---------------------------------------
SELECT t0.num, t0.text, sum(t1.cnt)
FROM `ZAMETKI` as t0
LEFT JOIN (
SELECT Z.num, Z.coun_old as cnt FROM `ZAMETKI` as Z
UNION ALL
SELECT P.num, COUNT(*) as cnt FROM `POSETIL` as P GROUP BY P.nom
) as t1
WHERE t1.num = t0.num

---------------------------------------
SELECT Z.num, Z.text, Z.coun_old, count(P.*) as count_new
FROM `ZAMETKI` as Z,
LEFT JOIN `POSETIL` as P ON P.num = Z.num

*/


//if($g=='') $o.=pr_zapisi($sss.WHERE()." ORDER BY z.`Date` DESC LIMIT ".$SIZEDEFAULT,true);
if($g=='') $o.=pr_zapisi($sss.WHERE(),true);

if($g=='more') $o.=pr_zapisi($sss.WHERE()." ORDER BY z.`Date` DESC");
if($g=='rating') $o.=pr_zapisi_rating($sss." ".WHERE()." ORDER BY z.`count` DESC");
if($admin && $g=='invis_adm') $o.=pr_zapisi($sss."WHERE `Access`='admin' ORDER BY z.`Date` DESC");
if($podzamok && $g=='invis') $o.=pr_zapisi($sss."WHERE `Access`='podzamok' ORDER BY z.`Date` DESC");
if($g=='mudoslov') $o.=pr_zapisi($sss.WHERE(mudos('LIKE','OR'))." ORDER BY z.`DATE` DESC");
if($g=='mudoslov_rating') $o.=pr_zapisi_rating($sss.WHERE(mudos('LIKE','OR')) );
if($g=='nemudoslov') $o.=pr_zapisi($sss.WHERE(mudos('NOT LIKE','AND'))." ORDER BY z.`DATE` DESC");
if($g=='nemudoslov_rating') $o.=pr_zapisi_rating($sss.WHERE(mudos('NOT LIKE','AND')));
// if(substr($g,0,3)=='st:') { $o.=pr_zapisi($sss."WHERE `Header` LIKE '".e(substr($g,3))."%' ORDER BY `Date` DESC"); }
return $o;
}

function pr_zapisi($sq,$more=false) {
	return pr_zapisi_($sq).($more && $GLOBALS["colnewcom"]>=$GLOBALS["SIZEDEFAULT"]
	?"<p><a href='?mode=more'>показать больше &gt;&gt;</a>":'');
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
	$ray=array(); foreach($pp as $n=>$p) { $c=get_counter($p); $pp[$n]["counter"]=$c; $ray[$n]=$c; } arsort($ray);
	
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
	die($s);
}

//=========================================================================================================

function getget() { $ge=$_GET;
	if(isset($ge['mode']) && $ge['mode']=='more') unset($ge['mode']);
	if(!sizeof($ge)) return '';
	$s="?"; foreach($ge as $a=>$b) $s.=urlencode($a)."=".urlencode($b)."&";
	return trim($s,"&");
}

?>
