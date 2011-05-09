<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

// include $include_sys."_onecomment.php";

include_once $include_sys."_onecomm.php";

blogpage();

$_PAGE["title"] = $_PAGE["header"] = "Содержание дневника";
$_PAGE["calendar"] = "<a href=".$GLOBALS["wwwhost"].">в блог</a>";
if($admin) $_PAGE["calendar"] .= "<p><a href=".$GLOBALS["wwwhost"]."install.php>install</a>
<p><a href=".$GLOBALS["wwwhost"]."admin>admin</a>";
$_PAGE["counter"] = "";

$SIZEDEFAULT=60;

$opt=array(
	''=>"по дате",
	'rating'=>"по рейтингу",
	'mudoslov'=>"нецензурные",
	'nemudoslov'=>"цензурные",
	'mudoslov_rating'=>"рейтинг нецензурных",
	'nemudoslov_rating'=>"рейтинг цензурных");

if(strstr($httphost,'lleo.aha.ru')) $opt=array_merge(
array('st:f5.ru'=>"стихи f5.ru",'st:razgovor.org'=>"статьи razgovor.org",'st:solidarnost.org'=>"статьи solidarnost.org")
,$opt);

$opt2=array(
	''=>"везде",
	'zam'=>"только в заметках",
	'hed'=>"в заголовках заметок",
	'com'=>"только в комментариях",
	'ans'=>"только в ответах",
	'comans'=>"в комментариях и ответах",
	'name'=>"имена комментаторов");


if($podzamok) {
	$opt=array_merge($opt,array('invis'=>"подзамочные заметки",'nikonoverr'=>"ошибки верстки"));
	$opt2=array_merge($opt2,array('namescr'=>"имена - скрытое"));
	if($admin) $opt=array_merge($opt,array('invis_adm'=>"совсем скрытые заметки"));
}


$s="<FORM METHOD=get ACTION='".$wwwhost."contents/'>

<p><center>

<i>Показать:</i> "
.selecto('mode',$_GET['mode'],$opt,
" onchange='for(var i=0;i<this.length;i++)if(this.options[i].selected){top.window.location=\"?mode=\"+this.options[i].value;break;}'")
."

<p><i>Искать:</i> <INPUT class='t' TYPE='text' NAME='search' SIZE=30 VALUE='".h($_GET['search'])."' MAXLENGTH=160> "
.selecto('smode',$_GET['smode'],$opt2)."<INPUT TYPE=SUBMIT VALUE='ИСКАТЬ'>

</form>
</center>";



//===========================================================
$se=$_GET['search'];

$sss="SELECT `num`,`Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ";

if($se!='') {
	$se=str_replace('_','\_',$se); $se="LIKE '%".e($se)."%'";

$a=$_GET['smode'];

if($a=='hed') pr_zapisi($sss.WHERE("`Header` $se")." ORDER BY `Date` DESC");
if($a=='zam') pr_zapisi($sss.WHERE("(`Body` $se OR `Header` $se OR `Comment` $se".($admin?" OR `include` $se":'').")")." ORDER BY `Date` DESC");

$WHERECOM1="SELECT * FROM `dnevnik_comm` WHERE (";
$WHERECOM2=")".($podzamok?'':" AND `scr`='0'")." ORDER BY `Time` DESC";

if($a=='com') pr_comments($WHERECOM1."`Text` $se OR `Name` $se".$WHERECOM2);
// if($a=='ans') pr_comments($WHERECOM1."`Answer` $se".$WHERECOM2);
if($a=='name') pr_comments($WHERECOM1."`Name` $se".$WHERECOM2);
if($a=='comans') pr_comments($WHERECOM1."`Text` $se `Name` $se".$WHERECOM2);
if($podzamok && $a=='namescr') { $onecomment_info=true; pr_comments($WHERECOM1."`Name` $se OR `lju` $se OR `mail` $se OR `IP` $se".$WHERECOM2); }

if($a=='') pr_zapisi_comments($sss.WHERE("(`Body` $se OR `Header` $se OR `Comment` $se)")." ORDER BY `Date` DESC",$WHERECOM1."`Text` $se OR `Name` $se".$WHERECOM2);

}

$g=$_GET['mode'];

  if($g=='') pr_zapisi($sss.WHERE()." ORDER BY `Date` DESC LIMIT ".$SIZEDEFAULT,true);
  if($g=='more') pr_zapisi($sss.WHERE()." ORDER BY `Date` DESC");
  if($g=='rating') pr_zapisi_rating($sss." ".WHERE()." ORDER BY `view_counter` DESC");

if($podzamok) {
  if($admin) if($g=='invis_adm') pr_zapisi($sss."WHERE `Access`='admin' ORDER BY `Date` DESC");
  if($g=='invis') pr_zapisi($sss."WHERE `Access`='podzamok' ORDER BY `Date` DESC");
}

if($g=='mudoslov') pr_zapisi($sss.WHERE(mudos('LIKE','OR'))." ORDER BY `DATE` DESC");
if($g=='mudoslov_rating') pr_zapisi_rating($sss.WHERE(mudos('LIKE','OR')) );
if($g=='nemudoslov') pr_zapisi($sss.WHERE(mudos('NOT LIKE','AND'))." ORDER BY `DATE` DESC");
if($g=='nemudoslov_rating') pr_zapisi_rating($sss.WHERE(mudos('NOT LIKE','AND')));

if(substr($g,0,3)=='st:') { pr_zapisi($sss."WHERE `Header` LIKE '".e(substr($g,3))."%' ORDER BY `Date` DESC"); }

exit;



function pr_comments($sq) { global $s; die($s.pr_comments_($sq)); }

function pr_zapisi($sq,$more=false) { global $s; die($s.pr_zapisi_($sq).($more && $GLOBALS["colnewcom"]>=$GLOBALS["SIZEDEFAULT"]
?"<p><a href=?mode=more>показать больше &gt;&gt;</a>":'')); }

function pr_zapisi_comments($sqz,$sqc) {
	$s=pr_zapisi_($sqz).pr_comments_($sqc);
	if($s=='') {
	$search=htmlspecialchars($_GET['search']);

$s="

Внутренний поиск сервера ничего не нашел.
Повторите другой запрос или поищем через Яндекс, который иногда работает умнее со сложными запросами".(strstr($search,' ')?" (а этот достаточно сложный)":'')."?

<p><center>
<FORM METHOD=get NAME=web ACTION='http://www.yandex.ru/yandsearch'>
<INPUT TYPE=hidden NAME=serverurl VALUE='".$admin_site."'>
<INPUT TYPE=hidden NAME=server_name VALUE='".$admin_name.": дневник'>
<INPUT TYPE=hidden NAME=referrer1 VALUE='".$httphost."'>
<INPUT TYPE=hidden NAME=referrer2 VALUE='".$admin_name.": дневник'>
<INPUT class=t TYPE=text NAME=text SIZE=".max(10,min(strlen($search),80))." VALUE='".htmlspecialchars($_GET['search'])."' MAXLENGTH=160>
<INPUT TYPE=SUBMIT VALUE='Яндекс'>
</FORM></center>

";
}

	die($s); }





function mudos($like,$or) {
	$ara=explode("\n",file_get_contents($GLOBALS['host_design'].'mudoslov.txt'));
	$a=''; foreach($ara as $m) if($m!='') $a.="`Body` $like '%$m%',";
	$a=trim($a,','); $a=str_replace(',',"\n$or ",$a);
	return $a;
}

//=========================================================================================================
function pr_zapisi_($sq) { global $numos,$colnewcom;
	$s=''; $year=0;
	$sql=ms($sq,"_a"); $s.=$GLOBALS['msqe'];
	$colnewcom=sizeof($sql); if(!$colnewcom) return $s;
	$s.="<h2>Заметок найдено: ".$colnewcom."</h2>";
	$s.="<ul>\n";
// dier($sql);

	$get=getget();

	foreach($sql as $p) {
			$p["counter"]=get_counter($p);
			$Date=$p["Date"];
			$head=($p["Header"]?" - ".htmlspecialchars($p["Header"]):"");

		if(preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d.*)$/si",$Date,$m)) { $Y=$m[1]; $M=$m[2]; $D=$m[3];
			if($Y!=$year) { if($year) $s.="</ul>\n"; $s .= "<h2>".$Y." год</h2>\n<ul>\n"; $year = $Y; }
			if($numos) $detail=" ".$numos++.". "; else $detail='';
			$z=$detail."<a href='".get_link($Date).$get."'>$M-$D (".$p["counter"].") ".$head."</a>";
			$z = zamok($p['Access']).$z;
			$s.="\t<li>".$z."</li>\n";

		} else {
			$z=$detail."<a href='".get_link($Date).$get."'>".$Date." (".$p["counter"].") ".$head."</a>";
			$z = zamok($p['Access']).$z;
			$s.="<br>".$z;
		}
	}

	$s .= "</ul>\n";

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
function pr_comments_($sq) {
	$s='';
	$sql=ms($sq,"_a"); $s.=$GLOBALS['msqe'];
	$colnewcom=sizeof($sql); if(!$colnewcom) return $s;
	$s.='<h2>Комментариев найдено: '.$colnewcom."</h2>";
        $tmpDate='';

	foreach($sql as $p) { $d=$p['DateID'];

	if($tmpDate!=$d) {
		$x=ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` WHERE `num`='".$d."'","_1");
                $s .= "<p><b><a href='".get_link($x['Date']).($_GET['search']?"?search=".$_GET['search']:'')."'>".$x['Date']." - "
		.($x['Header']!=''?$x['Header']:"(&nbsp;)")."</b></a>";
                $tmpDate=$d;
        	}

	$level=($p['Parent']!=0?'1':'0');
	$s.= comment_one($p,0,$level);
	}
	return $s;
}

function getget() { $ge=$_GET;
	if(isset($ge['mode']) && $ge['mode']=='more') unset($ge['mode']);
		if(!sizeof($ge)) return '';
		$s="?"; foreach($ge as $a=>$b) $s.=urlencode($a)."=".urlencode($b)."&";
	return trim($s,"&");
}

?>
