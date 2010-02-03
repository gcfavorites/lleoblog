<?php // Комментарии
if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй
blogpage();
$_PAGE["title"] = $_PAGE["header"] = "Комментарии дневника";
$_PAGE["calendar"] = "<a href=".$GLOBALS["wwwhost"].">в блог</a>";
if($admin) $_PAGE["calendar"] .= "<p><a href=".$GLOBALS["wwwhost"]."install.php>install</a>
<p><a href=".$GLOBALS["wwwhost"]."admin>admin</a>";


SCRIPTS("answercom","
var comnum=0;
function sendcm(nam,txt,comnu,id,dat,lev) { majax('comment.php',{action:'comsend',text:txt,name:nam,comnu:comnu,dat:dat,id:id,lev:lev}); }
function ansct(id,dat,lev) { majax('comment.php',{action:'comform', id:id, dat:dat, comnu: comnum, lev:lev}); }
");

$namecomlast="comlast";
$admin_comment_last=$hosttmp.$namecomlast.".txt"; // сюда будет записываться, до какого комментария админ уже просмотрел

$lim=50;
$mytime=time();
$er=$ok=array(); // сюда пишутся ошибки и системные сообщения

//############################################################################################3
// взять откуда-нибудь данные
$mode=$_GET['mode'];
$l=strtotime(preg_replace("/(\d\d\d\d-\d\d-\d\d)_(\d\d)-(\d\d)-(\d\d)/si","$1 $2:$3:$4",$_GET['lastcom']));
if($_GET['ncom']=='prev') $l=-$l;

if(!$l && !$mode) { if($admin) $l=intval(file_get_contents($admin_comment_last)); else $l=intval($_COOKIE[$namecomlast]); }

// нормализовать, если дата неверно задана, взять текущую дату и читать назад
if(!$l) { $lastcom=$mytime; $ncom='-'; if(!$mode) $prostynka .= 'Вы тут впервые? Тогда вот последние '.$lim.' комментариев.'; }
elseif($l<0) { $lastcom=-$l; $ncom='-'; } else { $lastcom=$l; $ncom=''; }

if(!$mode) { // запомнить последние параметры
	$l=$ncom.$lastcom;
	set_cookie($namecomlast, $l, time()+86400*365, "/", "", 0, true);
	if($admin) if(!file_put_contents($admin_comment_last,$l)) $er[]="не удается записать файл <b>$admin_comment_last</b>, проверьте права папки на запись"; else chmod($admin_comment_last,0666);
}

//print "<p>\$lastcom='$lastcom' \$ncom='$ncom'<p>";
//############################################################################################3

if($admin) {
	$a=glob($antibot_file."*.jpg");	$abot=sizeof($a); unset($a); // сколько антиботовых картинок?
	if($abot>5000) $er[]="антиботовых картинок накопилось $abot";
	else $ok[]="антиботовых картинок $abot";

	if(!$memcache) $er[]='memcache сдох...'; else $ok[]='memcache работает.';


	$mt=(is_file($cronfile)?(time()-filemtime($cronfile)):9999999);
		if($mt > 60*60) $er[]="cron последний раз запускался ".floor($mt/60)." минут назад!
настрой crontab или <a href='".$httphost."cron.php'>запусти вручную</a>";
		else $ok[]="cron последний раз запускался ".floor($mt/60)." минут назад";


	$ok[]="Список <a href=".$wwwhost."logon/?list>друзей</a>";
	$ok[]="Читать <a href=".$wwwhost."pravki/>правки</a>";

	if($er) { $prostynka.="<font color=red><i><ul>"; foreach($er as $e) $prostynka.="<li>$e</li>"; $prostynka.="</ul></i></font>"; }
	if($ok) { $prostynka.="<font color=green><i><ul>"; foreach($ok as $e) $prostynka.="<li>$e</li>"; $prostynka.="</ul></i></font>"; }
}
//####

/*

$p=ms("SELECT `login`,`realname`,`birth`,`mail`,`site`,`podpiska`,`count`,`admin`,`timereg`,`timelast`
FROM `$db_login` WHERE `timereg`>'".$lastcom."'
AND `timelast`>( NOW() - 24*60*60*60 )
ORDER BY `timereg` DESC
","_a",0);

// die("#".sizeof($p).' '.);

if(sizeof($p)) {

$prostynka.="<p><b>Зарегистрировано: ".sizeof($p)."</b><p>";

$admin_color=array('admin'=>'#FF9090','user'=>false,'podzamok'=>'#9090FF','comblock'=>'#90FF90');

$s=''; foreach($p as $l) {

	list($timelast)=explode(' ',$l['timelast']);
	switch($timelast) {
		case date('Y-m-d'): { $timelast='<font color=red>сегодня</font>'; break; }
		case date('Y-m-d',time()-60*60*24): { $timelast='<font color=green>вчера</font>'; break; }
		case date('Y-m-d',time()-2*60*60*24): { $timelast='<font color=green>позавчера</font>'; break; }
		case date('Y-m-d',time()-3*60*60*24): { $timelast='<font color=green>3 дня</font>'; break; }
		case date('Y-m-d',time()-4*60*60*24): { $timelast='<font color=green>4 дня</font>'; break; }
		case date('Y-m-d',time()-5*60*60*24): { $timelast='<font color=green>5 дней</font>'; break; }
		case date('Y-m-d',time()-6*60*60*24): { $timelast='<font color=green>6 дней</font>'; break; }
		case date('Y-m-d',time()-7*60*60*24): { $timelast='<font color=green>7 дней</font>'; break; }
		case date('Y-m-d',time()-8*60*60*24): { $timelast='<font color=green>8 дней</font>'; break; }
		case date('Y-m-d',time()-9*60*60*24): { $timelast='<font color=green>9 дней</font>'; break; }
		case date('Y-m-d',time()-10*60*60*24): { $timelast='<font color=green>10 дней</font>'; break; }
		case date('Y-m-d',time()-11*60*60*24): { $timelast='<font color=green>11 дней</font>'; break; }
		case date('Y-m-d',time()-12*60*60*24): { $timelast='<font color=green>12 дней</font>'; break; }
		case date('Y-m-d',time()-13*60*60*24): { $timelast='<font color=green>13 дней</font>'; break; }
		case date('Y-m-d',time()-14*60*60*24): { $timelast='<font color=green>14 дней</font>'; break; }
	}

	$is=get_IS($l['login']);
	$bg=($bg=='#FFFFE0'?'#FFE0FF':'#FFFFE0');
	$bg2=($admin_color[$l['admin']]?$admin_color[$l['admin']]:$bg);
	$s.="<tr bgcolor='".$bg2."'>

<td align=center>".$timelast."</td>

<td><font size=2><img src='".$is['IMG']."' alt=' (".$is['ROOT'].") '><a href='http://"
.($is['DOMAIN']=='lleo.aha.ru'?'lleo.aha.ru/user/':'').$login."'>"
."<a href=http://".htmlspecialchars($l['login']).">".htmlspecialchars($l['login'])."</a></font></td>

<td><a href='/dnevnik/logon/?userinfo=".$l['login']."'>info</a></td>

<td align=center>".($l['realname']!=''?htmlspecialchars($l['realname']):"&nbsp;")."</td>
<td align=center>".($l['birth']!='0000-00-00'?htmlspecialchars($l['birth']):"&nbsp;")."</td>

<td>".($l['mail']!=''?"<a href='mailto:".htmlspecialchars($l['mail'])."'>".htmlspecialchars($l['mail'])."</a>":"&nbsp;")."</td>
<td>".($l['site']!=''?"<a href='".htmlspecialchars($l['site'])."'>".htmlspecialchars($l['site'])."</a>":"&nbsp;")."</td>
<td>".strtoupper(substr($l['podpiska'],0,1))."</td>
<td align=right>".$l['count']."</td>
</tr>";

	} if($s!='') $prostynka.="<p><center><table class=br cellspacing=0 cellpadding=2>".$s."</table></center>";
}
*/

//--------------------------------------------------------------------------------------------------------------------------------

$sqlref="SELECT * FROM `dnevnik_comm` WHERE ";
	if($mode=='one') $sqlref .= "(`unic`='".e($_GET['unic'])."') AND ";
//	if($mode=='onelogin') $sqlref .= "`login`='".e($_GET['user'])."' AND ";
	if($ncom!='-') $sqlref.="Time>".$lastcom." ORDER BY Time";
	else $sqlref.="Time<".$lastcom." ORDER BY Time DESC";
$sqlref.=" LIMIT ".($lim+1);
$sql=ms($sqlref,'_a',0); $colnewcom=$colcom=sizeof($sql);



if($colnewcom) {
	include_once $include_sys."_onecomm.php";
	include_once $include_sys."text_scripts.php"; // включить аякс

if($colnewcom>$lim) { $colnewcom--; unset($sql[$colnewcom]); }

	$a=date('Y-m-d_H-i-s',$sql[0]['Time']);
	$b=date('Y-m-d_H-i-s',$sql[$colnewcom-1]['Time']);
	if($ncom!='-') { $l=$a; $a=$b; $b=$l; }
	$linknext_x=$a; $linkprev_x=$b;

if($colcom<=$lim) if($ncom=='-') $linkprev_x=''; else { $link_x='<br>на этом всё пока'; }

$prostynka .= "<div id=0>";

	for($i=0;$i<$colnewcom;$i++) if(isset($sql[$i])) {
		$p=$sql[$i];
		$num=$p['DateID']; $prostynka .= print_header($num);
		foreach($sql as $i2=>$p2) if($p2['DateID']==$num) { $prostynka.=print1($p2); unset($sql[$i2]); }
	}

$prostynka .= "<div></div></div>";


} else {
	$link_x1=true;
	if($ncom!='-') $linkprev_x=date('Y-m-d_H-i-s',$lastcom+1);
	else $linknext_x=date('Y-m-d_H-i-s',$lastcom-1);
}


$linkprev_link=$wwwhost."comm?".makeGET(array('ncom'=>'prev','lastcom'=>urlencode($linkprev_x)));
$linknext_link=$wwwhost."comm?".makeGET(array('ncom'=>'next','lastcom'=>urlencode($linknext_x)));

//$prevnext="<div class=navig>"
//.($linkprev_x!=''?'<div class=pre_post><a href="'.$linkprev_link.'">&lt;&lt; предыдущие (до '.$linkprev_x.')</a></div>':'')
//.($linknext_x!=''?'<div class=next_post><a href="'.$linknext_link.'">следующие (c '.$linknext_x.') &gt;&gt'.(isset($link_x)?$link_x:'').'</a></div>':'')
//."</div>";


$prevnext="<p>".mk_prevnest(
($linkprev_x!=''?'<a href="'.$linkprev_link.'">&lt;&lt; предыдущие (до '.$linkprev_x.')</a>':''),
($linknext_x!=''?'<a href="'.$linknext_link.'">следующие (c '.$linknext_x.') &gt;&gt'.(isset($link_x)?$link_x:'').'</a>':'')
);

$prostynka = ($link_x1!==true?$prevnext:'').$prostynka.$prevnext;

$_PAGE['prevlink'] = $linkprev_link;
$_PAGE['nextlink'] = $linknext_link;
$_PAGE['uplink'] = $httphost."contents";
$_PAGE['downlink'] = $httphost;

print $prostynka.'<p>';


// function screen_comments($id) { return msq_update('dnevnik_comm',array('metka'=>'screen'),"WHERE `ID`='$id'"); } // пометить в базу
// function del_comments($id) { return msq_del('dnevnik_comm',array('ID'=>"$id")); } // удалить из базы

function makeGET($ar) { $r=''; $m=$_GET;
	foreach($ar as $a=>$b) $m[$a]=$b;
	foreach($m as $a=>$b) if($b!='') $r.="$a=$b&";
	return trim($r,'&');
}

function print_header($num) {
	$p=ms("SELECT `Date`,`DateDate`,`Header`".($GLOBALS['old_counter']==1?",`view_counter`":'')." FROM `dnevnik_zapisi` WHERE `num`='".e($num)."' LIMIT 1",'_1');
	$p['counter']=get_counter($p);
	$x=$p['Header']; if($x=='') $x='(&nbsp;)';
	return "<p><b><a href=".h($GLOBALS['wwwhost'].$p['Date'].($p['DateDate']?".html":'')).">".h($p['Date']." - ".$x)."</a></b> (счетчик: ".$p['counter'].")";
}

function print1($p) { global $admin;
	$id=$p['id'];
	$level=($p['Parent']!=0?'4':'0');
	return comment_one($p,0,$level);
}

?>
