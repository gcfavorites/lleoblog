<?php

// include $include_sys."_onecomment.php";

include_once $include_sys."_onecomm.php";



if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй
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
	'st_nikonov'=>"статьи razgovor.org",
	'st_solidarnost'=>"статьи solidarnost.org",
	'mudoslov'=>"нецензурные",
	'nemudoslov'=>"цензурные",
	'mudoslov_rating'=>"рейтинг нецензурных",
	'nemudoslov_rating'=>"рейтинг цензурных");

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
if($se!='') {
	$se=str_replace('_','\_',$se);
	$se="LIKE '%".e($se)."%'";

$a=$_GET['smode'];

if($a=='hed') pr_zapisi("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE("`Header` $se")." ORDER BY `Date` DESC");
if($a=='zam') pr_zapisi("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE("`Body` $se OR `Header` $se OR `Comment` $se".($admin?" OR `include` $se":''))." ORDER BY `Date` DESC");

$WHERECOM1="SELECT * FROM `dnevnik_comm` WHERE (";
$WHERECOM2=")".($podzamok?'':" AND `scr`='0'")." ORDER BY `Time` DESC";

if($a=='com') pr_comments($WHERECOM1."`Text` $se OR `Name` $se".$WHERECOM2);
// if($a=='ans') pr_comments($WHERECOM1."`Answer` $se".$WHERECOM2);
if($a=='name') pr_comments($WHERECOM1."`Name` $se".$WHERECOM2);
if($a=='comans') pr_comments($WHERECOM1."`Text` $se `Name` $se".$WHERECOM2);
if($podzamok && $a=='namescr') { $onecomment_info=true; pr_comments($WHERECOM1."`Name` $se OR `lju` $se OR `mail` $se OR `IP` $se".$WHERECOM2); }

if($a=='') pr_zapisi_comments("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE("`Body` $se OR `Header` $se OR `Comment` $se")." ORDER BY `Date` DESC",$WHERECOM1."`Text` $se OR `Name` $se".$WHERECOM2);

}

$g=$_GET['mode'];

  if($g=='') pr_zapisi("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE()." ORDER BY `Date` DESC LIMIT ".$SIZEDEFAULT,true);
  if($g=='more') pr_zapisi("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE()." ORDER BY `Date` DESC");
  if($g=='rating') pr_zapisi_rating("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE()." ORDER BY `view_counter` DESC");

if($podzamok) {
  if($admin) if($g=='invis_adm') pr_zapisi("SELECT `Date`,`view_counter`,`Header`,`Access` FROM `dnevnik_zapisi` WHERE `Access`='admin' ORDER BY `Date` DESC");
  if($g=='invis') pr_zapisi("SELECT `Date`,`view_counter`,`Header`,`Access` FROM `dnevnik_zapisi` WHERE `Access`='podzamok' ORDER BY `Date` DESC");
  if($g=='st_solidarnost') pr_zapisi("SELECT `Date`,`view_counter`,`Header`,`Access` FROM `dnevnik_zapisi` WHERE `include`='nikonov.php' AND `Body` LIKE '%{nikonov%' AND `Body` LIKE '%solidarnost.org%' ORDER BY `Date` DESC");
  if($g=='st_nikonov') pr_zapisi("SELECT `Date`,`view_counter`,`Header`,`Access` FROM `dnevnik_zapisi` WHERE `include`='nikonov.php' AND `Body` LIKE '%{nikonov%' AND `Body` LIKE '%razgovor.org%' ORDER BY `Date` DESC");
  if($g=='nikonoverr') pr_zapisi("SELECT `Date`,`view_counter`,`Header`,`Access` FROM `dnevnik_zapisi` WHERE `include`='nikonov.php' AND `Body` NOT LIKE '%{nikonov%' ORDER BY `Date` DESC");
}

if($g=='mudoslov') pr_zapisi("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE(mudos('LIKE','OR'))." ORDER BY `DATE` DESC");
if($g=='mudoslov_rating') pr_zapisi_rating("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE(mudos('LIKE','OR'))." ORDER BY `view_counter` DESC");
if($g=='nemudoslov') pr_zapisi("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE(mudos('NOT LIKE','AND'))." ORDER BY `DATE` DESC");
if($g=='nemudoslov_rating') pr_zapisi_rating("SELECT `Date`,`Header`,`view_counter`,`Access` FROM `dnevnik_zapisi` ".WHERE(mudos('NOT LIKE','AND'))." ORDER BY `view_counter` DESC");

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
	$ara=explode("\n",file_get_contents('mudoslov.txt'));
	$a=''; foreach($ara as $m) if($m!='') $a.="`Body` $like '%$m%',";
	$a=trim($a,','); $a=str_replace(',',"\n$or ",$a);
	return $a;
}

//=========================================================================================================
function pr_zapisi_($sq) { global $ttl,$wwwhost,$www_design,$numos,$podz_img,$colnewcom;
	$s=''; $year=0;
	$sql=ms($sq,"_a",$ttl); $s.=$msqe;
	$colnewcom=sizeof($sql); if(!$colnewcom) return $s;
	$s.="<h2>Заметок найдено: ".$colnewcom."</h2>";
	$s.="<ul>\n";
// dier($sql);
	foreach($sql as $p) {
			$Date=$p["Date"];
			$head=($p["Header"]?" - ".htmlspecialchars($p["Header"]):"");

		if(preg_match("/^(\d\d\d\d)\/(\d\d)\/(\d\d.*)$/si",$Date,$m)) { $Y=$m[1]; $M=$m[2]; $D=$m[3];
			if($Y!=$year) { if($year) $s.="</ul>\n"; $s .= "<h2>".$Y." год</h2>\n<ul>\n"; $year = $Y; }
			if($numos) $detail=" ".$numos++.". "; else $detail='';
			$z=$detail."<a href='".$wwwhost."$Y/$M/$D.html".($_GET['search']?"?search=".$_GET['search']:'')."'>$M-$D (".$p["view_counter"].") ".$head."</a>";
			$z = zamok($p['Access']).$z;
			$s.="\t<li>".$z."</li>\n";

		} else {
			$z=$detail."<a href='".$wwwhost.$Date.($_GET['search']?"?search=".$_GET['search']:'')."'>".$Date." (".$p["view_counter"].") ".$head."</a>";
			$z = zamok($p['Access']).$z;
			$s.="<br>".$z;
		}
	}

	$s .= "</ul>\n";

	return $s;
}


//=========================================================================================================
function pr_zapisi_rating($sq) { global $ttl,$web_path,$www_design,$podz_img;
	$s='';
	$sql=ms($sq,"_a",$ttl); $s.=$msqe;
	$s.="<h2>Записей ".sizeof($sql)." (сортировка по счетчику посещений)</h2>";
	$s.="<ol>\n";

	foreach($sql as $p) {
        	list($Y,$M,$D)=explode("/", $p["Date"]);
		$head=($p["Header"]?" - ".htmlspecialchars($p["Header"]):"");
	        $z = "<b>".$p['view_counter']."</b> - <a href='/".$web_path."$Y/$M/$D.html".($_GET['search']?"?search=".$_GET['search']:'')."'>".$p['Date'].$head."</a>";
		$z = zamok($p['Access']).$z;
		$s.="\t<li>".$z."</li>\n";
		}
	$s.="</ol>\n";
	print $s; exit;
}


//=========================================================================================================
function pr_comments_($sq) { global $wwwhost;
	$s='';
	$sql=ms($sq,"_a",$ttl); $s.=$msqe;
	$colnewcom=sizeof($sql); if(!$colnewcom) return $s;
	$s.='<h2>Комментариев найдено: '.$colnewcom."</h2>";
//?????????        	include_once("text_scripts.php"); $s.=text_scripts();
        $tmpDate='';

//	dier($sql);

	foreach($sql as $p) { $d=$p['DateID'];

	if($tmpDate!=$d) {
                $s .= "<p><b><a href='".$wwwhost.$d.".html".($_GET['search']?"?search=".$_GET['search']:'')."'>".$d." - ";
                $x=ms("SELECT `Header` FROM `dnevnik_zapisi` WHERE `num`='".$d."' LIMIT 1","_l"); $s.=($x!=''?$x:"(&nbsp;)");
                $s .= "</b></a>";
                $tmpDate=$d;
        	}

	$level=($p['Parent']!=0?'100':'0');
	$s.= comment_one($p,0,$level);
// str_replace('{comment_otstup}',$level,comment_one($p));
	}
	return $s;
}

?>
