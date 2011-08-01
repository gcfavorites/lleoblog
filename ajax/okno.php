<?php // Окно для листания всяческого добра

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

$a=$_REQUEST["a"];
$_GET['search']=c(ifu($_REQUEST["search"]));



// =========== шо я не видел ============================= majax('okno.php',{a:'notseen',[day:30]})
if($a=='notseen') {  $day=RE0('day'); if(!$day) $day=30; $o='';
$pp=ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` as d ".WHERE("`DateDate`>'".(time()-$day*86400)."' AND NOT EXISTS (SELECT `url` FROM `dnevnik_posetil` AS p WHERE `unic`='".$GLOBALS['unic']."' AND d.`num`=p.`url`)")." ORDER BY `Date` DESC");
if($pp) {
$m=array(); foreach($pp as $p) $m[]=$p['Date']." - <a href='".get_link_($p['Date'])."'>".($p['Header']!=''?$p['Header']:'(...)')."</a>";
$o=implode('<br>',$m);
otprav("helpc('notseen',\"<fieldset><legend>мои непрочитанные заметки</legend>".njs($o)."</fieldset>\");");
}
otprav("salert('все заметки прочитаны',1000);");
}
// ======== rekomenda - листать базу rekomenda ===========
if($a=='rekomenda') {
	$nskip=intval($_REQUEST["nskip"]);
	$nlim=20;

/*
$jscripts=($admin?"
chzamok=function(e,d){
	if(d=='user') var o='podzamok';
	else if(d=='podzamok') var o='user';
	else return;
	var unic=ecom(e).id.replace(/u+/,'');
	majax('okno.php',{a:'dostup',unic:unic,value:o})
};":'');
*/

if($_GET["search"]=='') $topo="";
else { $se="LIKE '%".e($_REQUEST["search"])."%'"; $topo="WHERE `link` $se OR `text` $se"; }

if(!intval($_REQUEST["n"])) $_REQUEST["n"]=ms("SELECT COUNT(*) FROM $db_rekomenda $topo","_l");
$pp=ms("SELECT `datetime`,`link`,`text` FROM $db_rekomenda $topo ORDER BY `datetime` DESC LIMIT $nskip,".($nlim));

/*
CREATE TABLE IF NOT EXISTS `rekomenda` (
  `n` bigint(20) NOT NULL auto_increment,
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `link` varchar(2048) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`n`),
  KEY `datetime` (`datetime`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 COMMENT='Горящие ссылки дня' AUTO_INCREMENT=41 ;
*/



mk_okno("<center><input id='search_rekomenda' type='text'
onchange=\"majax('okno.php',{a:'rekomenda',search:this.value})\"
size='40' value=\"".h($_GET['search'])."\">"
."<input type='submit' value='search' onclick=\"majax('okno.php',{a:'rekomenda',search:idd('search_rekomenda').value})\"></center><br>"
.pr_rekomenda($pp),"База rekomenda ($nlim с ".$nskip.", всего ".$_REQUEST["n"].")","a:'$a',id:'$id'"
);
}



// ======== fidosearch - поиск по базе FIDO ===========
if($a=='fidosearch') { $se=c($_GET['search']); 

	$GLOBALS['db_fid']="`fido`";
	$GLOBALS['db_fido']=$GLOBALS['db_fid'].".`fidoecho`";
	$GLOBALS['db_fido_num']=$GLOBALS['db_fid'].".`fidoecho_num`";

//	if($_REQUEST["type"]=='name') { $a='unics'; } else {

$s="<center>поиск: <INPUT style='font-size: 12px; border: 1px solid #ccc' TYPE='text' id='oknom_search' value=\"".h($se)."\"
 onchange=\"majax('okno.php',{a:'fidosearch',search:this.value,type:idd('oknom_search_type').value})\" SIZE='40' MAXLENGTH='160'> "
.selecto('oknom_search_type',$_REQUEST["type"],array(
'all'=>'all','body'=>'Message','subj'=>'Subj','from'=>'From','to'=>'To','fromaddr'=>'From addr','toaddr'=>'To addr'
),"class='r' id='oknom_search_type'")
." <INPUT style='font-size: 12px' TYPE=SUBMIT VALUE='go' onclick=\"majax('okno.php',
{a:'fidosearch',search:idd('oknom_search').value,type:idd('oknom_search_type').value})\">
</center><p>";

	if($se=='') { $jscripts="idd('oknom_search').focus();"; mk_okno($s,"ПОИСК"); }


	$nskip=intval($_REQUEST["nskip"]);

	$nlim=10; 

$ser="='".e($se)."'";
$sera="LIKE '%".e($se)."%'";
$seru="LIKE '%".e(wu($se))."%'";

// поиск в записях
$SR=array(); $q=preg_replace("/[^a-z]+/s","",$_REQUEST["type"]);
	if($q=='all' or $q=='body') $SR[]="`BODY` $seru";
	if($q=='all' or $q=='subj') $SR[]="`SUBJ` $seru";
	if($q=='all' or $q=='from') $SR[]="`FROMNAME` $seru";
	if($q=='all' or $q=='to'  ) $SR[]="`TONAME` $seru";
	if(             $q=='fromaddr') $SR[]="`FROMADDR` $ser";
	if(             $q=='toaddr') $SR[]="`TOADDR` $ser";
	if(!sizeof($SR)) idie("unknown type: ".h($_REQUEST["type"]));
	$SEAR="FROM ".$GLOBALS['db_fido']." WHERE ".implode(" OR ",$SR);
	//MSGID REPLYID `FROMNAME`,`TONAME`,`FROMADDR`,`TOADDR` RAZMER DATETIME RECIVDATE ATTRIB

   $GLOBALS['msq_charset']='utf8';
   mysql_query("SET NAMES ".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_client=".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_results=".$GLOBALS['msq_charset']);
   mysql_query("SET @@local.character_set_connection=".$GLOBALS['msq_charset']);

	$_REQUEST["n"]=intval(ms("SELECT COUNT(*) ".$SEAR,"_l"));

//idie("SELECT COUNT(*) ".$SEAR);

if($_REQUEST["n"]) {
	$pp=ms("SELECT `id`,`AREAN`,`SUBJ`,`BODY`,`FROMNAME`,`TONAME`,`FROMADDR`,`TOADDR`,`RECIVDATE` ".$SEAR." ORDER BY `RECIVDATE` DESC LIMIT $nskip,".($nlim),"_a");

	foreach($pp as $p){ $txt=h(uw($p['BODY'])); $z=strlen($txt); $o=str_isearch2($se,$txt); 
	$s.="<div style='background-color: #ccc'><b>".h(strtoupper(get_arenum0($p['AREAN'])))."</b> ".h(uw($p['FROMNAME']))." ".h($p['FROMADDR'])." (".date("Y/m/d H:i",strtotime($p['RECIVDATE']))."):
<a href='".$GLOBALS['httpsite']."/fido?search=".urlencode($se)."#area:".h(get_arenum0($p['AREAN']))."|id:".$p['id']."'>".(trim($p['SUBJ'])==''?'(---)':h(uw($p['SUBJ'])))."</a></div>";
//	$s.="<br><a href='.?search=".urlencode($se)."'>".h($p['SUBJ'])."</a>";
	$otstup=30;
	$t=array(); foreach($o as $in=>$i) { if($in>10) { $t[]="<i><b>...и ещё подобных совпадений ".(sizeof($o)-10)."</b></i>"; break; }

	$start=($i>$otstup?$i-$otstup:0); if(isset($o[$in-1])&&$start<($o[$in-1]+strlen($se))) $start=$o[$in-1]+strlen($se);
	else { $k=0; while($k++<10&&$start!=0&&$txt[$start]!=" "&&$txt[$start]!="\n") $start--; }

	$end=(($i+strlen($se)+$otstup)<$z?$i+strlen($se)+$otstup:$z);  if(isset($o[$in+1])&&$end>($o[$in+1])) $end=$o[$in+1];
	else { $k=0; while($k++<10&&$end<$z&&$txt[$end]!=" "&&$txt[$start]!="\n") $end++; }

	$t[]=substr($txt,$start,$end-$start);
	} if(sizeof($t)) foreach($t as $l) $s.="<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".strtr($l,"\n"," ");
	}

} else $s.="<p class=r><i>ничего не найдено</i>";


// }
//idie('1');
mk_okno($s,"FIDO search '<b>".h($se)."</b>' from ".$nskip.($_REQUEST["n"]?" (".$_REQUEST["n"].")":''),"a:'$a',type:'$q'");


/*
// поиск в именах
elseif($_REQUEST["type"]=='name') { // поиск по комментариям
	$nlim=10; // include_once $include_sys."_onecomm.php";
	$SEAR="FROM ".$GLOBALS['db_fid']." WHERE `FROMNAME` LIKE '%".e($se)."%' AND `FROMADDR` LIKE '%".e($se)."%'";
	$_REQUEST["n"]=intval(ms("SELECT COUNT(*) ".$SEAR,"_l"));
	$s.=($_REQUEST["n"]? pr_comments_("SELECT * ".$SEAR." ORDER BY `Time` DESC LIMIT $nskip,".($nlim)):"<center>ничего не найдено</center>");
	mk_okno($s,"comments search '<b>".h($se)."</b>' from ".$nskip.($_REQUEST["n"]?" (".$_REQUEST["n"].")":''),"a:'$a',type:'comm'");
}

}
*/
}

// ======== search - поиск по базе ===========
if($a=='search') { $se=c($_GET['search']); 

if($_REQUEST["type"]=='name') { $a='unics'; } else {

$s="<center>поиск: <INPUT style='font-size: 12px; border: 1px solid #ccc' TYPE='text' id='oknom_search' value=\"".h($se)."\"
 onchange=\"majax('okno.php',{a:'search',search:this.value,type:idd('oknom_search_type').value})\" SIZE='40' MAXLENGTH='160'> "
.selecto('oknom_search_type',$_REQUEST["type"],array('zapisi'=>'в заметках','comm'=>'в комментариях','name'=>'в именах'),"class='r' id='oknom_search_type'")
." <INPUT style='font-size: 12px' TYPE=SUBMIT VALUE='go' onclick=\"majax('okno.php',
{a:'search',search:idd('oknom_search').value,type:idd('oknom_search_type').value})\">
</center><p>";

if($se=='') { $jscripts="idd('oknom_search').focus();"; mk_okno($s,"ПОИСК"); }

$nskip=intval($_REQUEST["nskip"]);

// поиск в записях
if($_REQUEST["type"]=='zapisi') {
	$nlim=10; $SEAR="FROM `dnevnik_zapisi` ".WHERE("(`Body` LIKE '%".e($se)."%' OR `Header` LIKE '%".e($se)."%')");
	$_REQUEST["n"]=intval(ms("SELECT COUNT(*) ".$SEAR,"_l"));
if($_REQUEST["n"]) {
	$pp=ms("SELECT `Body`,`Header`,`Date`,`num` ".$SEAR." ORDER BY `Date` DESC LIMIT $nskip,".($nlim),"_a");

	foreach($pp as $p){ $txt=h($p['Body']);	$z=strlen($txt); $o=str_isearch2($se,$txt);
	$s.="<br><a href='".get_link($p['Date'])."?search=".urlencode($se)."'>".h($p['Date'].": ".$p['Header'])."</a>";
	$otstup=30;
	$t=array(); foreach($o as $in=>$i) { if($in>10) { $t[]="<i><b>...и ещё подобных совпадений ".(sizeof($o)-10)."</b></i>"; break; }

	$start=($i>$otstup?$i-$otstup:0); if(isset($o[$in-1])&&$start<($o[$in-1]+strlen($se))) $start=$o[$in-1]+strlen($se);
	else { $k=0; while($k++<10&&$start!=0&&$txt[$start]!=" "&&$txt[$start]!="\n") $start--; }

	$end=(($i+strlen($se)+$otstup)<$z?$i+strlen($se)+$otstup:$z);  if(isset($o[$in+1])&&$end>($o[$in+1])) $end=$o[$in+1];
	else { $k=0; while($k++<10&&$end<$z&&$txt[$end]!=" "&&$txt[$start]!="\n") $end++; }

	$t[]=substr($txt,$start,$end-$start);
	} if(sizeof($t)) foreach($t as $l) $s.="<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".strtr($l,"\n"," ");
	}
} else $s.="<p class=r><i>ничего не найдено</i>";
	mk_okno($s,"zapisi search '<b>".h($se)."</b>' from ".$nskip.($_REQUEST["n"]?" (".$_REQUEST["n"].")":''),"a:'$a',type:'zapisi'");
}

// поиск в комментариях
elseif($_REQUEST["type"]=='comm') { // поиск по комментариям
	$nlim=10; include_once $include_sys."_onecomm.php";
	$SEAR="FROM `dnevnik_comm` WHERE `Text` LIKE '%".e($se)."%'".($podzamok?'':" AND `scr`='0'");
	$_REQUEST["n"]=intval(ms("SELECT COUNT(*) ".$SEAR,"_l"));
	$s.=($_REQUEST["n"]? pr_comments_("SELECT * ".$SEAR." ORDER BY `Time` DESC LIMIT $nskip,".($nlim)):"<center>ничего не найдено</center>");
	mk_okno($s,"comments search '<b>".h($se)."</b>' from ".$nskip.($_REQUEST["n"]?" (".$_REQUEST["n"].")":''),"a:'$a',type:'comm'");
}


}}

// ======== hiscomment - все комментарии одного человека ===========
if($a=='hiscomment') {
	$id=intval($_REQUEST["id"]); if(!$id) idie("unic = null!");
	$nskip=intval($_REQUEST["nskip"]);
	$nlim=10;
	include_once $include_sys."_onecomm.php";
	$s=pr_comments_("SELECT * FROM `dnevnik_comm` WHERE `unic`='$id'".($podzamok?'':" AND `scr`='0'")." ORDER BY `Time` DESC LIMIT $nskip,".($nlim));
	mk_okno($s,"comments from user #".h($id)." from ".$nskip,"a:'$a',id:'$id'");
}

// ======== unics - листать базу посетителей ===========
if($a=='unics') {
	// $id=intval($_REQUEST["id"]); if(!$id) idie("unic = null!");
	$nskip=intval($_REQUEST["nskip"]);
	$nlim=20;
$jscripts=($admin?"
chzamok=function(e,d){
	if(d=='user') var o='podzamok';
	else if(d=='podzamok') var o='user';
	else return;
	var unic=ecom(e).id.replace(/u+/,'');
	majax('okno.php',{a:'dostup',unic:unic,value:o})
};":'');



if($_GET["search"]=='') $topo="WHERE (`login`!='' AND `password`!='') OR `openid`!=''";
else { $se="LIKE '%".e($_REQUEST["search"])."%'";
$topo="WHERE `login` $se OR `openid` $se OR `lju` $se OR `realname` $se OR `site` $se"
.($admin?" OR `mail` $se":"");
}

if(!intval($_REQUEST["n"])) $_REQUEST["n"]=ms("SELECT COUNT(*) FROM $db_unic $topo","_l");

$pp=ms("SELECT `id`,`login`,`openid`,`obr`,`lju`,`realname`,`site`,`birth`,`time_reg`,`timelast`"
.($podzamok?",`mail`,`admin`,`ipn`,`capchakarma`":'')
." FROM $db_unic $topo ORDER BY `time_reg` DESC LIMIT $nskip,".($nlim));

mk_okno("<center><input id='search_unic' type='text'
onchange=\"majax('okno.php',{a:'unics',search:this.value})\"
size='40' value=\"".h($_GET['search'])."\">"
."<input type='submit' value='search' onclick=\"majax('okno.php',{a:'unics',search:idd(search_unic).value})\"></center><br>"
.pr_unics($pp),"Зарегистрировавшиеся посетители ($nlim с ".$nskip.", всего ".$_REQUEST["n"].")","a:'$a',id:'$id'"
);
}




if(isset($_REQUEST['onload'])) otprav(''); // все дальнейшие опции будут запрещены для GET-запроса

// ========================== hiscomment ================================

if($a=='dostup') { // смена доступа
        if(!$admin) idie("Ты не админ.");
        $u=intval($_REQUEST['unic']);
        $v=$_REQUEST['value'];
 	ms("UPDATE ".$GLOBALS['db_unic']." SET `admin`='".e($v)."' WHERE `id`='$u'","_l",0);
	$p=ms("SELECT * FROM $db_unic WHERE id='$u'","_1",0); $s=pr_unics0($p);
	otprav("
idd('u".$u."').style.backgroundColor='".($p['admin']=='user'?'transparent':$podzamcolor)."';
zabil(\"u".$u."\",\"".njs($s)."\"); 
");
}



function pr_unics($pp){ global $admin,$podzamok; $s="<table style='border-bottom: 1px dotted #ccc;'>";

$s.="<tr style='background-color:#CED;text-align:center;font-size:10px;'>".($podzamok?"<td><img src='".$GLOBALS['www_design']."e/podzamok.gif'></td>":'')
.($admin?"<td>N</td>":'')."<td>unic</td><td>login</td><td>openid</td><td>lju</td><td>realname</td>"
.($podzamok?"<td>email</td><td>site</td>":'')."</tr>";

$k=0; foreach($pp as $p) { $k++; $s.="<tr bgcolor='"
.($p['admin']=='user'?(($k%2)?"#E0E0E0":"#D0D0D0"):$GLOBALS['podzamcolor'])
."'".($admin?" id='u".$p['id']."'":'').">".pr_unics0($p)."</tr>"; }

return $s."</table>";
}

function pr_unics0($p){ global $podzamok,$admin;

if($admin) {
$p['N']=ms("SELECT COUNT(*) FROM `dnevnik_posetil` WHERE `unic`='".$p['id']."'","_l"); if($p['N']==0) $p['N']=' ';
//ms("SELECT `date` FROM `dnevnik_posetil` WHERE `unic`='".$p['id']."' ORDER BY `date` LIMIT 1","_l");
//if($p['date']===false) $p['date']=' ';
}

return ($podzamok?"<td style='cursor:pointer;border:1ps dotted red;' onclick=\"chzamok(this,'".$p['admin']."')\">".zamok($p['admin'])."&nbsp;</td>":'')
.($admin?"<td class=br>".h($p['N'])."</td>":'')
."<td class=ll onclick=\"majax('login.php',{action:'getinfo',unic:".$p['id']."})\">".$p['id']."</td>"
."<td>".h($p['login'])."</td>"
."<td>".($p['openid']!=''?"<a href='http://".h(strtr($p['openid'],'@','.'))."'>".h($p['openid'])."</a>":'&nbsp;')."</td>"
."<td>".($p['lju']!=''?"<a class=br href=http://".h($p['lju']).".livejournal.com>".h($p['lju'])."</a>":"")."</td>"
."<td>".($p['realname']!=$p['login']?h($p['realname']):'&nbsp;')."</td>"

.($podzamok?""
."<td>".($p['mail']?"<a href=\"mailto:".h($p['mail'])."\">".h($p['mail'])."</a>":"&nbsp;")."</td>"
."<td>".($p['site']?"<a href=\"".h($p['site'])."\">".h($p['site'])."</a>":"&nbsp;")."</td>"
// ."<td>".($p['mail']?"<a href=\"mailto:".h($p['mail'])."\">".h($p['mail'])."</a>":"&nbsp;")."</td>"
//.($p['site']?"<td><a href=\"mailto:".h($p['mail'])."\"</td>":"")
:'');

}






function pr_rekomenda($pp){ global $admin,$podzamok; $s="<table style='border-bottom: 1px dotted #ccc;'>";

$s.="<tr style='background-color:#CED;text-align:center;font-size:10px;'>".($podzamok?"<td><img src='".$GLOBALS['www_design']."e/podzamok.gif'></td>":'')
."<td>text</td><td>link</td></tr>";

$k=0; foreach($pp as $p) { $k++; $s.="<tr bgcolor='"
.($p['admin']=='user'?(($k%2)?"#E0E0E0":"#D0D0D0"):$GLOBALS['podzamcolor'])
."'".($admin?" id='u".$p['id']."'":'').">".pr_rekomenda0($p)."</tr>"; }

return $s."</table>";
}

function pr_rekomenda0($p){ global $podzamok,$admin;

return ($podzamok?"<td style='cursor:pointer;border:1ps dotted red;' onclick=\"chzamok(this,'".$p['admin']."')\">".zamok($p['admin'])."&nbsp;</td>":'')
."<td>".h($p['text'])."</td>"
."<td><a href=\"".h($p['link'])."\">".h($p['link'])."</a></td>";
}





// ========================== okno ================================

function mk_okno($s,$legend,$ar='') { global $nlim,$nskip,$a,$jscripts;
	$s=$GLOBALS['msqe'].$s;
	
	if($_GET["search"]!='') {
		$ar.=",search:'".h($_GET['search'])."'"; // и поиск добавить, если был
		$s=search_podsveti_body($s); // подсветить найденное
	}

	$setkey='';
$n=intval($_REQUEST["n"]);

if($nskip!=0) {
	$m="majax('okno.php',{".$ar.",n:'$n',nskip:'".($nskip-$nlim)."'})";
	$prev="<span class=l onclick=\"$m\">&lt;&lt; предыдущие ".($nlim)."</span>";
	$setkey.='setkey("left","ctrl",function(e){'.$m.'},true);';
	$setkey.='setkey("4","",function(e){'.$m.'},true);';
} else $prev='';

if($nskip<($n-$nlim)) {
	$m="majax('okno.php',{".$ar.",n:'$n',nskip:'".($nskip+$nlim)."'})";
	$next="<span class=l onclick=\"$m\">следующие ".($nlim)." &gt;&gt;</span>";
	$setkey.='setkey("right","ctrl",function(e){'.$m.'},true);';
	$setkey.='setkey("7","",function(e){'.$m.'},true);';
} else $next='';

$prevnext="<p>".mk_prevnest($prev,$next);

otprav("helps('okno_$a',\"<fieldset><legend>$legend</legend>".njs($prevnext.$s.$prevnext)."</fieldset>\");
posdiv('okno_$a',-1,-1); $setkey
$jscripts");

}

// ========================== hiscomment ================================

// печать ленты комментариев
function pr_comments_($sql) { $s=''; $pp=ms($sql,"_a"); $colnewcom=sizeof($pp); if(!$colnewcom) return $s;
        $s.="<b>Комментариев: ".$colnewcom
.($_REQUEST['n']?" из ".$_REQUEST['n']:'')
."</b>";
        $tmpDate='';

        foreach($pp as $p) { $d=$p['DateID'];

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

function str_isearch2($search,$s){ $o=array();
	$SEARCH=strtolower2_body($search); $S=strtolower2_body($s);
	$i=-1; while(($i=strpos($S,$SEARCH,++$i))!==false) $o[]=$i;
	return $o;
}


function get_arenum0($n) { global $areans; if(!isset($areans)) $areans=array();
        $s=array_search($n,$areans); if($s!==false) return $s;
        $s=ms("SELECT `echo` FROM ".$GLOBALS['db_fido_num']." WHERE `echonum`='".e($n)."'","_l");
        if($s!==false) $areans[$s]=$n; return $s;
}


?>