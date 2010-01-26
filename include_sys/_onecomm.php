<?php

// if(!$GLOBALS['admin']) idie('переделываю, зайдите чуть позже');

$GLOBALS['comment_tmpl']=file_get_contents($GLOBALS['file_template']."comment_tmpl.htm");

$GLOBALS['browsers']=array('Linux'=>'Linux','Windows'=>'Windows','NokiaE90'=>'Nokia-E90','Mac OS X'=>'Mac','FreeBSD'=>'FreeBSD','Ubuntu'=>'Ubuntu','Debian'=>'Debian','Firefox'=>'Firefox','Opera'=>'Opera','Safari'=>'Safari','MSIE'=>'IE','Konqueror'=>'Konqueror','Chrome'=>'Chrome');

if(function_exists('SCRIPTS')) SCRIPTS("knopki","

var comnum=0;

function kus(unic) { if(unic) majax('login.php',{action:'getinfo',unic:unic}); }// лична€ карточка автора
function kd(e) { if(confirm('“очно удалить?')) majax('comment.php',{a:'del',id:ecom(e).id}); } // удалить комментарий
function ked(e) { majax('comment.php',{a:'edit',id:ecom(e).id}); } // редактировать комментарий
function ksc(e) { majax('comment.php',{a:'scr',id:ecom(e).id}); } // скрыть-раскрыть
function rul(e) { majax('comment.php',{a:'rul',id:ecom(e).id}); } // rul-не rul
function ka(e) { e=ecom(e); majax('comment.php',{a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ответить

function kpl(e) { majax('comment.php',{a:'plus',id:ecom(e).id}); } // плюсик
function kmi(e) { majax('comment.php',{a:'minus',id:ecom(e).id}); } // minus

function opc(e) { e=ecom(e); majax('comment.php',{a:'pokazat',oid:e.id,lev:e.style.marginLeft,comnu:comnum}); } // показать

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
.cplu {color:red} .cplu:before { content:'+'} .cplu:after { content:'††'}
.cmin {color:blue} .cmin:before { content:'-'} .cmin:after { content:'††'}
.kmail:after { content: '†'url('{www_design}e2/kmail.png')'†' }

.rul1:before {content:url('{www_design}e3/button_accept.png')}
.rul0:before {content:url('{www_design}e3/list-remove.png')}

.kd,.ked,.ks0,.ks1,.ka { cursor: pointer; float:left; font-size: 6pt; color: blue; padding: 0 2pt 0 7pt; }
.kd:before {content:'del'}
.ked:before {content:'edit'}
.ks0:before {content:'скрыть'}
.ks1:before {content:'раскрыть'}
.ka:before {content:'ответить'}


.cdel {font-size:8px; font-weight: bold; }
.cdel:before {content:'* комментарий удален'}

.opc { font-size: 11px; }
.opc:before {content:url('{www_design}e3/expand_plus.gif')'показать'}

");
*/

function comment_one($p,$nediv=0) {

	if($p['Time']==0 && $nediv) // удаленный комментарий
		return "<div id=".$p['id']." name=".$p['id']." class='cdel' style='margin-left:{comment_otstup}px'></div>";

	$c=comment_prep($p); // подготовить данные

	$tmpl=$GLOBALS['comment_tmpl'];

if(!$nediv) $tmpl="<div id={id} name={id} class={commclass} style='margin-left:{comment_otstup}px'>".$tmpl."</div>";

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
	$a=preg_replace("/^.*\ninetnum:\s+/si","\ninetnum: ",$a); // очистить до
	$a=preg_replace("/\nroute:\s.*$/si","",$a); // очистить после
//	$a=preg_replace("/\ninetnum:\s+([\d \-\.]+)/si","<font size=2 color=green><b>".whois_strana($a)."</b></font> : <a href=http://yandex.ru/yandsearch?text=%22".$ip."%22>".$ip.($p['IPx']?" /".$p['IPx']:'')."</a> ($1)",$a);
	$a=preg_replace("/\n%[^\n]+/si","",$a); // удал€ем камменты
	$a=preg_replace("\n/remarks\:\s+[\-\+\+\*\#\s]+(?:\n)/si","\n",$a);
	$a=preg_replace("\n/remarks\:\s+([^\n]+)(?:\n)/si","$1\n",$a);

	$e=explode(" ","country admin-c tech-c status mnt-by source role fax-no admin-c tech-c nic-hdl mnt-by source mnt-lower mnt-routes mnt-domains OrgID changed org mnt-lower organisation org-name org-type mnt-ref");
	foreach($e as $l) $a=preg_replace("/\n".$l.": [^\n]+/si","",$a);

	$e=explode("\n",$a);
	$a=$sn=$ss=''; foreach($e as $l) {
				if(preg_match("/^([^:]+):\s+(.*$)/",$l,$m)) {
					if($m[1]==$sn) { if($m[2]!='') $ss.=", ".$m[2]; }
					else { 	if($sn!='') $a.="\n".$sn.": ".$ss; // выдать что в буфере
						$sn=$m[1]; $ss=$m[2]; // и запомнить
						}
				} else { if($ll!='') $ss.=", ".$l; }
			}
		if($sn!='') $a.="\n".$sn.": ".$ss; // выдать что в буфере

	$a=preg_replace("/,+/",",",$a); // убрать двойные зап€тые

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

function commclass($p) { if($p['scr']==1) return 'c0'; else return 'c'.($p['group']+1); }

function comment_prep($p) { global $admin,$unic,$podzamok,$geoip_color; 
	$c=array();

	$c['id']=$p['id'];
	$c['commclass']=commclass($p);

// ---- особые отметки, голосовани€, unic ----
	
	$c['golos_plu']=$p['golos_plu']; // +
	$c['golos_min']=$p['golos_min']; // -
	$c['unic']=$p['unic'];

// ---- город и страна ----

	list($gorod,$strana)=explode("\001",$p['whois']);
	$c['whois'] = ($strana?search_podsveti(h($strana)):'').($gorod?($strana?", ":'').search_podsveti(h($gorod)):'');

// ---- врем€ ----

	$c['Time']=date('Y-m-d H:i', $p["Time"]);

// ---- Mail ----

	$c['Mail']=($p['Mail']==''?'':"<div class=kmail></div>");


// ---- кнопки ----

	$c['kn'] .= "<div class=ka onclick='ka(this)'></div>"; // ответить
//	$c['kn'] .= "<div class=kus onclick='kus(".$p['unic'].")'></div>"; // показать личную карточку автора

if($admin or ($unic==$p['unic'] and time()-$p['Time'] < 15*60)) $c['kn'] .= "<div class=ked onclick='ked(this)'></div>"; // редактировать комментарий

if($admin) {
	$c['kn'] .= "<div class=kd onclick='kd(this)'></div>"; // удалить комментарий
	$c['kn'] .= "<div class=ks".intval($p['scr'])." onclick='ksc(this)'></div>"; // скрыть/раскрыть
	$c['rul'] .= "<div class=rul".intval($p['rul'])." onclick='rul(this)'></div>"; // особа€ отметка
} else {
	$c['rul'] .= ''; // особа€ отметка
}


// ---- браузер ----

	$x=''; foreach($GLOBALS['browsers'] as $a=>$b) if(stristr($p['BRO'],$a)) $x.=($x?' ':'').$b;
	$c['BRO']=search_podsveti(h($x));

// ---- им€ автора ----

	$c['Name']=search_podsveti(h($p['Name']));
//	$is=();

/*
<noindex>
<img src='".$is['IMG']."' alt=' (".$is['ROOT'].") '><b><a href='http://".($is['DOMAIN']=='lleo.aha.ru'?'lleo.aha.ru/user/':'').$logn."' rel='nofollow'>".search_podsveti($is['USER0'])."</a></b>
</noindex>
*/

// ---- заголовок комментари€ ---- // нужен ли?

// ---- текст комментари€ ----
		$text=nl2br(h($p["Text"]));
		$text=AddBB($text);
		$text="\n$text\n";
		$text=hyperlink($text);
		$text=trim($text,"\n\t ");

		$text=preg_replace("/\{(\_.*?\_)\}/s","&#123;$1&#125;",$text); // удалить подстыковки нахуй из пользовательского текста!

	$c['Text']=search_podsveti($text);

return $c;
}


//==========================================================================

function load_comments($num,$comments_order='normal') { global $admin,$podzamok,$unic,$load_comments_MS,$comc,$comindex,$s,$kstop;

$plusp=$load_comments_MS;
// if(!$podzamok&&!$admin) $plusp .= " AND (`scr`=1 OR `unic`='$unic')"; // только открытые или свои
if($_GET['mastercom']=='yes') $plusp .= " AND `rul`='1'"; // только с особой отметкой

$sql=ms("SELECT `id`,`unic`,`group`,`Name`,`Text`,`Parent`,`Time`,`whois`,`rul`,`ans`,`golos_plu`,`golos_min`,`scr`,`Mail`,`DateID`
FROM `dnevnik_comm` WHERE `DateID`='".e($num)."'".$plusp." ORDER BY `Time`","_a",0);

if(!sizeof($sql)) return ppp_nocomment();

return print_prostynka($sql,0);

}


//====================================================================


function print_prostynka($sql,$id,$l=0) { global $comc,$comindex,$kstop; $kstop=10000;
	$comc=array(); $comindex=array();
	foreach($sql as $p) {
		$comc[$p['id']]=comment_one($p);
		if($p['rul']==1) $comindex[$p['Parent']][$p['id']]='rul';
		elseif($p['scr']==0) $comindex[$p['Parent']][$p['id']]='open';
		else $comindex[$p['Parent']][$p['id']]=$p['unic'];
	}

	$s=vseprint_comm($id,0,$l);

	// а теперь потер€нные комменты
	if(sizeof($comc)) { $s.='<hr>'; foreach($comc as $c) $s.=str_replace("{comment_otstup}",'0',$c); }
return $s;
}

//====================================================================


function vseprint_comm($id,$level,$l) { global $maxcommlevel,$podzamok,$comc,$comindex,$kstop; if(!isset($comindex[$id])) return;
	$s='';
	$level++; if(! $kstop--) idie('err kstop'.$id.$s);

	foreach($comindex[$id] as $id2=>$value) { if(!$value) continue;

		if($podzamok or $value=='open' or $value=='rul' or $value==$GLOBALS['unic']) {

			if($level < $maxcommlevel or $value=='rul') { // выдать коммент
				$s.=str_replace("{comment_otstup}",($level*25),$comc[$id2]);
			} elseif($level == $maxcommlevel) { // выдать раскладушку
				$s.="<div id='o$id' class='opc' onclick='opc(this)' style='margin-left:".($level*25)."px'></div>";
			}
			$s.=vseprint_comm($id2,$level,$l);
		}
		unset($comc[$id2]); // в любом случае удалить коммент из массива
	}
	return $s;
}



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
} function strtolower2($s){ return strtr(strtolower($s),'јЅ¬√ƒ≈®∆«»… ЋћЌќѕ–—“”‘’÷„Ўўџ№ЏЁёя','абвгдеЄжзийклмнопрстуфхцчшщыьъэю€'); }



function ppp_nocomment() { return "<p class=z>комментариев нет или они все скрыты"; }

?>