<?php

// if(!$GLOBALS['admin']) idie('�����������, ������� ���� �����');

$GLOBALS['comment_tmpl']=file_get_contents($GLOBALS['file_template'].(empty($template)?"comment_tmpl.htm":$template));

$GLOBALS['browsers']=array('Linux'=>'Linux','Windows'=>'Windows','NokiaE90'=>'Nokia-E90','Mac OS X'=>'Mac','FreeBSD'=>'FreeBSD','Ubuntu'=>'Ubuntu','Debian'=>'Debian','Firefox'=>'Firefox','Opera'=>'Opera','Safari'=>'Safari','MSIE'=>'IE','Konqueror'=>'Konqueror','Chrome'=>'Chrome');

if(function_exists('SCRIPTS')) SCRIPTS("knopki","

var comnum=0;

function kus(unic) { if(unic) majax('login.php',{action:'getinfo',unic:unic}); }// ������ �������� ������
function kd(e) { if(confirm('����� �������?')) majax('comment.php',{a:'del',id:ecom(e).id}); } // ������� �����������
function ked(e) { majax('comment.php',{a:'edit',id:ecom(e).id}); } // ������������� �����������
function ksc(e) { majax('comment.php',{a:'scr',id:ecom(e).id}); } // ������-��������
function ko(e) { majax('comment.php',{a:'ans',id:ecom(e).id}); } // ans-0-1-undef
function rul(e) { majax('comment.php',{a:'rul',id:ecom(e).id}); } // rul-�� rul
function ka(e) { e=ecom(e); majax('comment.php',{a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������

function kpl(e) { majax('comment.php',{a:'plus',id:ecom(e).id}); } // ������
function kmi(e) { majax('comment.php',{a:'minus',id:ecom(e).id}); } // minus

function opc(e) { e=ecom(e); majax('comment.php',{a:'pokazat',oid:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������

function ecom(e) {
	while( ( e.id == '' || e.id == undefined ) && e.parentNode != undefined) e=e.parentNode;
	if(e.id == undefined) return 0; return e;
}
");

/*
if(0 && function_exists('STYLES')) STYLES("commentstyle","

.opc, .cnam, .rul1,.rul0,.cplu,.cmin {cursor: pointer}
.rul1,.rul0,.cplu,.cmin,.kmail {float:left}

.c0, .c1, .c2, .c3
{ font: 80% sans-serif, Helvetica, Arial, Verdana; max-width: 80%;
border: 1px dotted #bbb; overflow: auto; padding: 0 0 0 5px; margin-top: 5px; }

.c0, { background-color: #AAFAFA; }
.c1, .opc { background-color: red; }
.c2 { background-color: red; }
.c3 { background-color: green; }

.ctxt { padding: 5pt; overflow: auto; text-align: justify; }


.ctim, .cbro, .cwho { font-size: 8pt; position: relative; margin-left: 10pt; top: 0px; float: right; }
.kn { font-size: 8pt; position: relative; float: left; }

.cnam { font-size: 12pt; font-weight: bold; }

.cplu,.cmin { font-size:6pt }
.cplu {color:red} .cplu:before { content:'+'} .cplu:after { content:'��'}
.cmin {color:blue} .cmin:before { content:'-'} .cmin:after { content:'��'}
.kmail:after { content: '�'url('{www_design}e2/kmail.png')'�' }

.rul1:before {content:url('{www_design}e3/button_accept.png')}
.rul0:before {content:url('{www_design}e3/list-remove.png')}

.kd,.ked,.ks0,.ks1,.ka { cursor: pointer; float:left; font-size: 6pt; color: blue; padding: 0 2pt 0 7pt; }
.kd:before {content:'del'}
.ked:before {content:'edit'}
.ks0:before {content:'������'}
.ks1:before {content:'��������'}
.ka:before {content:'��������'}

.cdel {font-size:8px; font-weight: bold; }
.cdel:before {content:'* ����������� ������'}

.opc { font-size: 11px; }
.opc:before {content:url('{www_design}e3/expand_plus.gif')'��������'}

");
*/

function comment_one($p,$mojno_comm,$level=false) { $lev=$level*$GLOBALS['comment_otstup'];

	if($p['Time']==0 && $level!==false) // ��������� �����������
		return "<div id=".$p['id']." name=".$p['id']." class='cdel' style='margin-left:".$lev."px'></div>";

	$c=comment_prep($p,$mojno_comm,$level); // ����������� ������

	$tmpl=$GLOBALS['comment_tmpl'];

if($level!==false) $tmpl="<div id={id} name={id} class={commclass} style='margin-left:".$lev."px'>".$tmpl."</div>";

	foreach($c as $n=>$l) $tmpl=str_replace('{'.$n.'}',$l,$tmpl);
	return str_replace("\n",'',$tmpl);
}

// =========================================================================================================

include_once $include_sys."_obracom.php";
//include_once $include_sys."_geoip_all.php";
//$lcol=array('rus'=>'green','world'=>'brown','whois'=>'red');
//        $a="<font color=".$l[$a['basa']].">".$a['country'].", ".$a['city']."</font>";
/*
if($GLOBALS['onecomment_info']) {
	$ip=ipn2ip($p['IPN']);
	$x="<span class=br><font color=gray>";

if($podzamok) {
	$a=strtr(exec('whois '.$ip.' | tr "\n" "\001"'),"\001","\n");
	$a=preg_replace("/^.*\ninetnum:\s+/si","\ninetnum: ",$a); // �������� ��
	$a=preg_replace("/\nroute:\s.*$/si","",$a); // �������� �����
//	$a=preg_replace("/\ninetnum:\s+([\d \-\.]+)/si","<font size=2 color=green><b>".whois_strana($a)."</b></font> : <a href=http://yandex.ru/yandsearch?text=%22".$ip."%22>".$ip.($p['IPx']?" /".$p['IPx']:'')."</a> ($1)",$a);
	$a=preg_replace("/\n%[^\n]+/si","",$a); // ������� ��������
	$a=preg_replace("\n/remarks\:\s+[\-\+\+\*\#\s]+(?:\n)/si","\n",$a);
	$a=preg_replace("\n/remarks\:\s+([^\n]+)(?:\n)/si","$1\n",$a);

	$e=explode(" ","country admin-c tech-c status mnt-by source role fax-no admin-c tech-c nic-hdl mnt-by source mnt-lower mnt-routes mnt-domains OrgID changed org mnt-lower organisation org-name org-type mnt-ref");
	foreach($e as $l) $a=preg_replace("/\n".$l.": [^\n]+/si","",$a);

	$e=explode("\n",$a);
	$a=$sn=$ss=''; foreach($e as $l) {
				if(preg_match("/^([^:]+):\s+(.*$)/",$l,$m)) {
					if($m[1]==$sn) { if($m[2]!='') $ss.=", ".$m[2]; }
					else { 	if($sn!='') $a.="\n".$sn.": ".$ss; // ������ ��� � ������
						$sn=$m[1]; $ss=$m[2]; // � ���������
						}
				} else { if($ll!='') $ss.=", ".$l; }
			}
		if($sn!='') $a.="\n".$sn.": ".$ss; // ������ ��� � ������

	$a=preg_replace("/,+/",",",$a); // ������ ������� �������

	$e=explode("\n",$a);
	foreach($e as $l) if($l!='') { $l="\n".$l; while(substr_count($a,$l)>1) $a=substr_replace($a,"",strrpos($a,$l),strlen($l)); }

	$a=preg_replace("/netname\:\s+([^\n]+)\ndescr\:\s+([^\n]+)/si","<b>$1 ($2)</b>",$a);
//	$gorod=whois_gorod($a);
	$a.="\n\n<b>".$gorod."</b>";
	$a=str_replace("\n","<br>",$a);
//	$A=geoip($ip);
	$x.="<font color=".$geoip_color[$A['basa']].">".$A['country'].", ".$A['city']."</font><br><font color=red>".$A['country'].", ".$gorod."</font><br>";
	$x.=$a."<br>--------------------------------<p>"
	.($p["Guest_LJ"]!=''?"lj: ".search_podsveti(htmlspecialchars($p["Guest_LJ"]))."<br>":"")
	.($p["Guest_Name"]!=''?"name: ".search_podsveti(htmlspecialchars($p["Guest_Name"]))."<br>":"");
}

$x.=search_podsveti(htmlspecialchars($p['UserAgent']))."<br>";
if($podzamok) $x .= search_podsveti(htmlspecialchars($p['speckod']))."<br>".($p['mudak']=='yes'?"mudak=YES":'');
$x.="</font></span><p>";

$commentary=$x.$commentary;
}
*/



// 3 ��������: false - ������ ������ 'root' - ����� � ����� 'tree' - ����� �����
function mojno_comment($p) { global $IS,$podzamok,$N_maxkomm,$enter_comentary_days;
	if($p['Comment']=='disabled') return false; // ���� ��������� ������
	if($p['Comment_tree']=='0') return 0; // ���� ��������� �������� �� ��������

	// ���������� ���������� ��������� ��� ������� ������ �������
	$t=($p["counter"] < $N_maxkomm and $p["DateDatetime"] > time()-86400*$enter_comentary_days ?1:0);

	switch($p["Comment_write"]) {
		case 'off': return false;
		case 'on': return 1;
		case 'friends-only': return ($podzamok?1:false);
		case 'login-only': return (($IS['login']!='' or $IS['password']!='')?1:false);
		case 'timeoff': return ($t?1:false);
		return (($t and ($IS['login']!='' or $IS['password']!=''))?1:false);
	}
}

function commclass($p) { if($p['scr']==1) return 'c0'; else return 'c'.($p['group']+1); }

function comment_prep($p,$mojno_comm,$level) { global $admin,$unic,$podzamok,$geoip_color; 
	$c=array();

	$c['id']=$p['id'];
	$c['commclass']=commclass($p);

// ---- ������ �������, �����������, unic ----
	
	$c['golos_plu']=$p['golos_plu']; // +
	$c['golos_min']=$p['golos_min']; // -
	$c['unic']=$p['unic'];

// ---- ����� � ������ ----

	list($gorod,$strana)=explode("\001",$p['whois']);
	$c['whois'] = ($strana?search_podsveti(h($strana)):'').($gorod?($strana?", ":'').search_podsveti(h($gorod)):'');

// ---- ����� ----

	$c['Time']=date('Y-m-d H:i', $p["Time"]);

// ---- Mail ----

	$c['Mail']=($p['Mail']==''?'':"<div class=kmail></div>");


// ---- ������ ----
	$c['kn']=''; // "'$mojno_comm'";

	if($p['ans']!='0' and ($admin or $mojno_comm=="1" or $p['ans']=='1') )
		$c['kn'] .= "<div class=ka onclick='ka(this)'></div>"; // ��������
	if($admin) $c['kn'] .= "<div class=ko".$p['ans']." onclick='ko(this)'></div>"; // ��������

//	$c['kn'] .= "<div class=kus onclick='kus(".$p['unic'].")'></div>"; // �������� ������ �������� ������

if($admin or ($unic==$p['unic'] and time()-$p['Time'] < 15*60)) $c['kn'] .= "<div class=ked onclick='ked(this)'></div>"; // ������������� �����������

if($podzamok) {
	$c['kn'] .= "<div class=ks".intval($p['scr'])." onclick='ksc(this)'></div>"; // ������/��������
}

if($admin) {
	$c['kn'] .= "<div class=kd onclick='kd(this)'></div>"; // ������� �����������
	$c['rul'] .= "<div class=rul".intval($p['rul'])." onclick='rul(this)'></div>"; // ������ �������
} else {
	$c['rul'] .= ''; // ������ �������
}


// ---- ������� ----

	$x=''; foreach($GLOBALS['browsers'] as $a=>$b) if(stristr($p['BRO'],$a)) $x.=($x?' ':'').$b;
	$c['BRO']=search_podsveti(h($x));

// ---- ��� ������ ----

	$c['Name']=search_podsveti(h($p['Name']));
//	$is=();

/*
<noindex>
<img src='".$is['IMG']."' alt=' (".$is['ROOT'].") '><b><a href='http://".($is['DOMAIN']=='lleo.aha.ru'?'lleo.aha.ru/user/':'').$logn."' rel='nofollow'>".search_podsveti($is['USER0'])."</a></b>
</noindex>
*/

// ---- ��������� ����������� ---- // ����� ��?

// ---- ����� ����������� ----
		$text=nl2br(h($p["Text"]));
		$text=AddBB($text);
		$text="\n$text\n";
		$text=hyperlink($text);
		$text=trim($text,"\n\t ");

		$text=preg_replace("/\{(\_.*?\_)\}/s","&#123;$1&#125;",$text); // ������� ����������� ����� �� ����������������� ������!

	$c['Text']=search_podsveti($text);

return $c;
}

//====================================================================
function comment_cachename($num) { return $GLOBALS['blogdir'].'-comment-'.$num; }
//function clean_commentcache($num) { cache_rm(comment_cachename($num)); }

//==========================================================================
function load_comments($art) { global $comc,$comindex,$kstop,$podzamok,$comment_otstup,$comment_pokazscr;

	$num=$art['num'];

$cachename=comment_cachename($num); $mas=cache_get($cachename); // ���� �� � ����?
if(($GLOBALS['admin'] and !empty($_GET['nocache'])) or $mas===false) { // ------------ ���� ��� � ����, �� �������� ------------
	$sql=ms("SELECT `id`,`unic`,`group`,`Name`,`Text`,`Parent`,`Time`,`whois`,`rul`,`ans`,`golos_plu`,`golos_min`,`scr`,`Mail`,`DateID`
	FROM `dnevnik_comm` WHERE `DateID`='".e($num)."' ORDER BY `Time`","_a",0);
	if(!sizeof($sql)) return ppp_nocomment();

	$kstop=10000; $comc=array(); $comindex=array();
	foreach($sql as $p) {
		$comc[$p['id']]=$p;
		if($p['rul']==1) $comindex[$p['Parent']][$p['id']]='rul';
		elseif($p['scr']==0) $comindex[$p['Parent']][$p['id']]='open';
		else $comindex[$p['Parent']][$p['id']]=$p['unic'];
	}
	$mas=vseprint_comm(0,0,0); // ��������� � ������ ��� �������� ��������� ��������
	// �������� ���������� ��������
	if(sizeof($comc)) { $s.='<hr>'; foreach($comc as $id2=>$p) $mas[]=array('p'=>$p,'value'=>1,'id'=>$id2,'level'=>0); }
	cache_set($cachename,$mas,$GLOBALS['ttl_longsite']); $s=''; // ��������� � ���
} else { $s="<img src=".$GLOBALS['www_design']."e3/ledgreen.png>"; }


$mojno_comm=mojno_comment($art); // ����������, �� ����� �������� ����� ��������

// � ��� ������, ������ �� �� ��� ������ $mas, �� ���� ��� ��������� ������, ������ ��������
foreach($mas as $m) { if($podzamok or !$m['value'] or $m['value']==$GLOBALS['unic']) // ������ ��?
	$s.=comment_one($m['p'],$mojno_comm,$m['level']);
	elseif($comment_pokazscr) $s.="<div class=cscr style='margin-left:".($m['level']*$comment_otstup)."px'></div>";
}

return $s;
}

//====================================================================
function vseprint_comm($id,$level,$l) { global $maxcommlevel,$comc,$comindex,$kstop; if(!isset($comindex[$id])) return array();
	$mas=array(); $level++; if(! $kstop--) idie('err kstop'.$id.h(print_r($mas,1)));
	foreach($comindex[$id] as $id2=>$value) { if(!$value) continue;
		$mas[]=array('p'=>$comc[$id2],'value'=>( ($value=='open' or $value=='rul')?0:$value ),'id'=>$id2,'level'=>$level);
		$mas=array_merge($mas,vseprint_comm($id2,$level,$l));
		unset($comc[$id2]); // � ����� ������ ������� ������� �� �������
	}
	return $mas;
}
//====================================================================


function search_podsveti($a) { if($_GET['search']=='') return $a;
	$a=preg_replace_callback("/>([^<]+)</si","search_p",'>'.$a.'<');
	$a=ltrim($a,'>'); $a=rtrim($a,'<');
	return $a;
} function search_p($r) { return '>'.str_ireplace2($_GET['search'],"<span class=search>","</span>",$r[1]).'<'; }

function str_ireplace2($search,$rep1,$rep2,$s){	$c=chr(1); $nashlo=array(); $x=strlen($search);
	$S=strtolower2($s);
	$SEARCH=strtolower2($search);
	while (($i=strpos($S,$SEARCH))!==false){
		$nashlo[]=substr($s,$i,$x);
		$s=substr_replace($s,$c,$i,$x);
		$S=substr_replace($S,$c,$i,$x);
	} foreach($nashlo as $l) $s=substr_replace($s,$rep1.$l.$rep2,strpos($s,$c),1);
	return $s;
} function strtolower2($s){ return strtr(strtolower($s),'�����Ũ��������������������������','��������������������������������'); }


function ppp_nocomment() { return "<p class=z>������������ ��� ��� ��� ��� ������"; }

?>