<?php // �����������

function PAGE_COMM() { global $admin;

$GLOBALS['opt']=mkzopt(array('opt'=>$GLOBALS['article']['opt']));

$namecomlast="comlast";
$admin_comment_last=$GLOBALS['hosttmp'].$namecomlast.".txt"; // ���� ����� ������������, �� ������ ����������� ����� ��� ����������
$lim=50;
$mytime=time();
$er=$ok=array(); // ���� ������� ������ � ��������� ���������

//############################################################################################3
// ����� ������-������ ������
$mode=$_GET['mode'];
$l=strtotime(preg_replace("/(\d\d\d\d-\d\d-\d\d)_(\d\d)-(\d\d)-(\d\d)/si","$1 $2:$3:$4",$_GET['lastcom']));
if($_GET['ncom']=='prev') $l=-$l;

if(!$l && !$mode) { if($admin) $l=intval(file_get_contents($admin_comment_last)); else $l=intval($_COOKIE[$namecomlast]); }

// �������������, ���� ���� ������� ������, ����� ������� ���� � ������ �����
if(!$l) { $lastcom=$mytime; $ncom='-'; if(!$mode) $s .= '�� ��� �������? ����� ��� ��������� '.$lim.' ������������.'; }
elseif($l<0) { $lastcom=-$l; $ncom='-'; } else { $lastcom=$l; $ncom=''; }

if(!$mode) { // ��������� ��������� ���������
	$l=$ncom.$lastcom;
	setcoo($namecomlast,$l);
	if($admin) if(!file_put_contents($admin_comment_last,$l)) $er[]="�� ������� �������� ���� <b>$admin_comment_last</b>, ��������� ����� ����� �� ������"; else chmod($admin_comment_last,0666);
}
//############################################################################################3

if($GLOBALS['admin']) {
	$a=glob($GLOBALS['antibot_file']."*.jpg"); $abot=sizeof($a); unset($a); // ������� ����������� ��������?
	if($abot>5000) $er[]="����������� �������� ���������� $abot";
	else $ok[]="����������� �������� $abot";

	if(!$GLOBALS['memcache']) $er[]='memcache ��������...'; else $ok[]='memcache ��������.';

	$mt=(is_file($GLOBALS['cronfile'])?(time()-filemtime($GLOBALS['cronfile'])):9999999);
		if($mt > 60*60) $er[]="cron ��������� ��� ���������� ".floor($mt/60)." ����� �����!
������� crontab ��� <a href='".$GLOBALS['httphost']."cron.php'>������� �������</a>";
		else $ok[]="cron ��������� ��� ���������� ".floor($mt/60)." ����� �����";

//	$ok[]="������ <a href=".$GLOBALS['wwwhost']."logon/?list>������</a>";
	$ok[]="������ <a href=".$GLOBALS['wwwhost']."pravki/>������</a>";

	if($er) { $s.="<font color=red><i><ul>"; foreach($er as $e) $s.="<li>$e</li>"; $s.="</ul></i></font>"; }
	if($ok) { $s.="<font color=green><i><ul>"; foreach($ok as $e) $s.="<li>$e</li>"; $s.="</ul></i></font>"; }
}

//--------------------------------------------------------------------------------------------------------------------------------

$sqlref="SELECT c.`id`,c.`unic`,c.`group`,c.`Name`,c.`Text`,c.`Parent`,c.`Time`,c.`whois`,c.`rul`,c.`ans`,
c.`golos_plu`,c.`golos_min`,c.`scr`,c.`DateID`,c.`BRO`,
u.`capchakarma`,u.`mail`,u.`admin`,
".($GLOBALS['admin']?"z.Access,z.num,":'')."
z.`opt`,z.Access,z.`Date`,z.`DateDate`,z.`Header`,z.`view_counter`
FROM
`dnevnik_comm` AS c
JOIN `dnevnik_zapisi` AS z ON c.`DateID`=z.`num`
LEFT JOIN ".$GLOBALS['db_unic']." AS u ON c.`unic`=u.`id`

WHERE "
// ������ �������� ���
// ���� �������� - ������� ������� ������� ��� � ������������ ����������
.($GLOBALS['admin']?"1":"z.`Access`='all' AND (c.`scr`='0' OR c.`unic`='".$GLOBALS['unic']."')")


//($GLOBALS['podzamok']?" AND z.`Access`!='admin' AND z.`Comment_view`!='off'":
// ��� ���� ������ ��������� ����������� � ��������
// " AND z.`Access`!='all' AND z.`Comment_view`!='off'"))

.($mode=='one'?" AND c.`unic`='".e($_GET['unic'])."'":"") // ���� ��������� �������� ������ �� ������
." AND ".($ncom!='-'?"c.`Time`>'".$lastcom."' ORDER BY c.`Time`":"c.`Time`<'".$lastcom."' ORDER BY c.`Time` DESC")." LIMIT ".($lim+1);

$sql=ms($sqlref,"_a",0); $colnewcom=$colcom=sizeof($sql);

if($GLOBALS['msqe']!='') idie($GLOBALS['msqe']);
// dier($sql);

if($colnewcom) {
	include_once $GLOBALS['include_sys']."_onecomm.php";

if($colnewcom>$lim) { $colnewcom--; unset($sql[$colnewcom]); }

	$a=date('Y-m-d_H-i-s',$sql[0]['Time']);
	$b=date('Y-m-d_H-i-s',$sql[$colnewcom-1]['Time']);
	if($ncom!='-') { /*list($a,$b)=array($b,$a);*/ $l=$a; $a=$b; $b=$l; }
	$linknext_x=$a; $linkprev_x=$b;

if($colcom<=$lim) if($ncom=='-') $linkprev_x=''; else { $link_x='<br>�� ���� �� ����'; }

$s .= "<div id=0>";

	for($i=0;$i<$colnewcom;$i++) if(isset($sql[$i])) {
		$p=mkzopt($sql[$i]); 
		$num=$p['DateID']; //$s .= print_header($num);
$s .= print_headerp($p);
		foreach($sql as $i2=>$p2) if($p2['DateID']==$num) { $s.=print1($p2); unset($sql[$i2]); }
	}

$s .= "<div></div></div>";


} else {
	$link_x1=true;
	if($ncom!='-') $linkprev_x=date('Y-m-d_H-i-s',$lastcom+1); else $linknext_x=date('Y-m-d_H-i-s',$lastcom-1);
}


$linkprev_link=$GLOBALS['mypage']."?".makeGET(array('ncom'=>'prev','lastcom'=>urlencode($linkprev_x)));
$linknext_link=$GLOBALS['mypage']."?".makeGET(array('ncom'=>'next','lastcom'=>urlencode($linknext_x)));

$prevnext="<p>".mk_prevnest(
($linkprev_x!=''?'<a href="'.$linkprev_link.'">&lt;&lt; ���������� (�� '.$linkprev_x.')</a>':''),
($linknext_x!=''?'<a href="'.$linknext_link.'">��������� (c '.$linknext_x.') &gt;&gt'.(isset($link_x)?$link_x:'').'</a>':'')
);

$s = ($link_x1!==true?$prevnext:'').$s.$prevnext;

$_PAGE['prevlink'] = $linkprev_link;
$_PAGE['nextlink'] = $linknext_link;
#$_PAGE['uplink'] = $GLOBALS['httphost']."contents";
#$_PAGE['downlink'] = $GLOBALS['httphost'];

return $s.$GLOBALS['msqe'];
}

function makeGET($ar) { $r=''; $m=$_GET;
	foreach($ar as $a=>$b) $m[$a]=$b;
	foreach($m as $a=>$b) if($b!='') $r.="$a=$b&";
	return trim($r,'&');
}

function print_headerp($p) {
	$p['counter']=get_counter($p);
	$x=$p['Header']; if($x=='') $x='(&nbsp;)';
	return "<p>"
."<img class='knop' id='knopnocomment_".$p['DateID']."' onclick=\"majax('editor.php',{a:'nocomment',num:'".$p['DateID']."'})\" src='".$GLOBALS['www_design']."e3/"
.($p['Comment_write']=='off'?'ledred.png':'ledgreen.png')."' alt='���������/��������� �����������'>&nbsp;"
."<b><a href=".h($GLOBALS['wwwhost'].$p['Date'].($p['DateDate']?".html":'')).">".h($p['Date']." - ".$x)."</a></b> (�������: ".$p['counter'].")"
.ADMINSET($p);
}


function print1($p) { $level=($p['Parent']!=0?'4':'0');	return comment_one($p,0,$level); }

?>