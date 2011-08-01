<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй

if(!isset($GLOBALS['comments_on_page'])) $GLOBALS['comments_on_page']=0;

// if(!$GLOBALS['admin']) idie('переделываю, зайдите чуть позже');

$GLOBALS['comment_tmpl']=file_get_contents($GLOBALS['file_template'].(empty($template)?"comment_tmpl.htm":$template));
$GLOBALS['browsers']=array('Linux'=>'Linux','Windows'=>'Windows','NokiaE90'=>'Nokia-E90','Mac OS X'=>'Mac','FreeBSD'=>'FreeBSD','Ubuntu'=>'Ubuntu','Debian'=>'Debian','Firefox'=>'Firefox','Opera'=>'Opera','Safari'=>'Safari','MSIE'=>'IE','Konqueror'=>'Konqueror','Chrome'=>'Chrome');

function comment_one($p,$mojno_comm,$level=false) { $lev=$level*$GLOBALS['comment_otstup'];

	if($p['Time']==0 && $level!==false) // удаленный комментарий
		return "<div id=".$p['id']." name=".$p['id']." class='cdel' style='margin-left:".$lev."px'></div>";

	if(($c=comment_prep($p,$mojno_comm,$level))===false) return ''; // подготовить данные
	
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



// 3 значени€: false - нельз€ вообще 'root' - можно в корне 'tree' - можно везде
function mojno_comment($p) { global $IS,$podzamok,$N_maxkomm,$enter_comentary_days;
	if($p['Comment']=='disabled') return false; // если запрещены вообще
	if($p['Comment_tree']=='0') return 0; // если запрещено отвечать на комменты

	// ѕревышение количества посещений или слишком стара€ заметка
	if(!isset($p["counter"])) $t=0;
	else $t=($p["counter"] < $N_maxkomm and $p["DateDatetime"] > time()-86400*$enter_comentary_days ?1:0);

//if($GLOBALS['IP']=='178.140.69.89') {}

$comm_zapret = ($p['capchakarma']<2 && (($IS['login']!='' and $IS['password']!='') or $IS['openid']!=''));

	switch($p["Comment_write"]) {
		case 'off': return false;
		case 'on': return 1;
		case 'friends-only': return ($podzamok?1:false);
		case 'login-only': return ($comm_zapret?1:false);
		case 'timeoff': return ($t?1:false);
		return (($t and $comm_zapret)?1:false);
	}
}

function commclass($p) { if($p['scr']==1) return 'c0'; else return 'c'.($p['group']+1); }

function comment_prep($p,$mojno_comm,$level) { global $admin,$unic,$podzamok,$geoip_color; 
	$c=array();

	$c['id']=$p['id'];
	$c['commclass']=commclass($p);

// ---- особые отметки, голосовани€, unic ----
	
	$c['golos_plu']=$p['golos_plu']; // +
	$c['golos_min']=$p['golos_min']; // -
	$c['unic']=$p['unic'];

// ---- город и страна ----
	list($gorod,$strana)=explode("\001",$p['whois'],2);
	$c['whois'] = ($strana?search_podsveti(hh($strana)):'').($gorod?($strana?", ":'').search_podsveti(hh($gorod)):'');

// ---- врем€ ----

	$c['Time']=date('Y-m-d H:i', $p["Time"]);

// ---- Mail ----

	$c['Mail']=($p['mail']==''?'':"<div class=kmail></div>");


// ---- кнопки ----
	$c['kn']=''; // "'$mojno_comm'";

	if($p['ans']!='0' and ($admin or $mojno_comm=="1" or $p['ans']=='1') )
		$c['kn'] .= "<div class=ka onclick='ka(this)'></div>"; // ответить
	if($admin) $c['kn'] .= "<div class=ko".$p['ans']." onclick='ko(this)'></div>"; // ответить

//	$c['kn'] .= "<div class=kus onclick='kus(".$p['unic'].")'></div>"; // показать личную карточку автора

if($admin or ($unic==$p['unic'] and time()-$p['Time'] < 15*60)) $c['kn'] .= "<div class=ked onclick='ked(this)'></div>"; // редактировать комментарий

if( $GLOBALS['comment_friend_scr'] && $podzamok || $admin ) {
	$c['kn'] .= "<div class=ks".intval($p['scr'])." onclick='ksc(this)'></div>"; // скрыть/раскрыть
}


$c['karma'] = ($podzamok ? "<div class=kr>".zamok($p['admin']).($p['capchakarma']>1?$p['capchakarma']:'')."</div>" : '');


if($admin || ($GLOBALS['del_user_comments'] && $unic==$p['unic'])) $c['kn'] .= "<div class=kd onclick='kd(this)'></div>"; // удалить комментарий

if($admin) {
	$c['rul'] .= "<div class=rul".intval($p['rul'])." onclick='rul(this)'></div>"; // особа€ отметка
} else {
	$c['rul'] .= ''; // особа€ отметка
}


// ---- браузер ----

	$x=''; foreach($GLOBALS['browsers'] as $a=>$b) if(stristr($p['BRO'],$a)) $x.=($x?' ':'').$b;
	$c['BRO']=search_podsveti(hh($x));

// ---- им€ автора ----

	$c['Name']=search_podsveti(hh($p['Name']));
	if($c['unic']==0) $c['Name']="<i>".$c['Name']."</i>";
//	$is=();

/*
<noindex>
<img src='".$is['IMG']."' alt=' (".$is['ROOT'].") '><b><a href='http://".($is['DOMAIN']=='lleo.aha.ru'?'lleo.aha.ru/user/':'').$logn."' rel='nofollow'>".search_podsveti($is['USER0'])."</a></b>
</noindex>
*/

// ---- заголовок комментари€ ---- // нужен ли?

// ---- текст комментари€ ----
	$text=h($p["Text"]);

if(stristr($text,'{screen:') or stristr($text,'{scr:')) {
	$text=(($admin||$unic==$p['unic'])?
			preg_replace("/\{screen:\s*(.+?)\s*\}/si","<div style='border: 1px dotted red; background: #eeeeee'>$1</div>",$text)
			: preg_replace("/\{screen:.*?\}/si",'',$text)
	);

	$text=(($podzamok||$unic==$p['unic'])?
			preg_replace("/\{scr:\s*(.+?)\s*\}/si","<div style='border: 1px dotted blue; background: #eeeeee'>$1</div>",$text)
			: preg_replace("/\{scr:.*?\}/si",'',$text)
	);
}

	if(!$admin && c($text)=='') return false;

		$text=nl2br($text);
		$text=AddBB($text);
		$text="\n$text\n";
		$text=hyperlink($text);
		$text=c($text);

		$text=preg_replace("/\{(\_.*?\_)\}/s","&#123;$1&#125;",$text); // удалить подстыковки нахуй из пользовательского текста!

		$text=preg_replace("/&amp;(#[\d]+;)/si","&$1",$text); // отображать спецсимволы и национальыне кодировки

	$c['Text']=search_podsveti($text);

return $c;
}

//====================================================================
function comment_cachename($num) { return $GLOBALS['blogdir'].'-comment-'.$num; }
//function clean_commentcache($num) { cache_rm(comment_cachename($num)); }


//==========================================================================
function load_mas($num) { global $kstop,$comc,$comindex,$db_unic,$admin,$load_mas_cached,$ttl_longsite;
	$load_mas_cached==1;
	$cachename=comment_cachename($num); $mas=cache_get($cachename); // есть ли в кэше?

	if($mas===false or ($admin and !empty($_GET['nocache'])) ) {
	// ------------ если нет в кэше, то прочесть ------------

$sql=ms("SELECT c.`id`,c.`unic`,c.`group`,c.`Name`,c.`Text`,c.`Parent`,c.`Time`,c.`whois`,c.`rul`,c.`ans`,
c.`golos_plu`,c.`golos_min`,c.`scr`,c.`DateID`,c.`BRO`,
u.`capchakarma`,u.`mail`,u.`admin`
FROM `dnevnik_comm` AS c LEFT JOIN $db_unic AS u ON c.`unic`=u.`id` WHERE `DateID`='".e($num)."'
ORDER BY `Time`","_a",0);

	if(!sizeof($sql)) return false;

	$kstop=10000; $comc=array(); $comindex=array();
	foreach($sql as $p) {
		$comc[$p['id']]=$p;
		if($p['rul']==1) $comindex[$p['Parent']][$p['id']]='rul';
		elseif($p['scr']==0) $comindex[$p['Parent']][$p['id']]='open';
		else $comindex[$p['Parent']][$p['id']]=intval($p['unic']);
	}
	$mas=vseprint_comm(0,0,0); // запихнуть в массив всю простыню комментов комменты
	// добавить потер€нные комменты
	if(sizeof($comc)) { foreach($comc as $id2=>$p) $mas[]=array('p'=>$p,'value'=>1,'id'=>$id2,'level'=>0); }
	cache_set($cachename,$mas,$ttl_longsite);
	$GLOBALS['load_mas_cached']==0;
	}

return $mas;
}

// $GLOBALS['Comment_media']=$article['Comment_media'];

function load_comments($art) { global $opt,$IP,$BRO,$MYPAGE,$www_design,$admin,$podzamok,$load_mas_cached,
$unic,$comment_otstup,$comment_pokazscr,$maxcommlevel,
$comments_pagenum,$comments_on_page;

	$num=$art['num'];
	if(($mas=load_mas($num))===false) return ppp_nocomment();

	$GLOBALS['opt']=mkzopt(array('opt'=>$art['opt'])); // дл€ обработки каментов могут оказатьс€ полезные опции

	$s=($load_mas_cached && $admin?"<a href='".$MYPAGE."?nocache=1'><img src='".$www_design."e3/ledgreen.png'>$s</a>":'');

	$mojno_comm=mojno_comment($art); // установить, на какие комменты можно отвечать

// а вот теперь, откуда бы ни был массив $mas, из кэша или собранный вживую, выдать комменты

	$podz = $podzamok && (sizeof($mas)<100 || isset($_GET['screen']));
	$yandex = (strstr($BRO,'Yandex') || $IP=='78.110.50.100'?1:0);

//$comments_on_page=6;

$i=0; $m=$comments_pagenum*$comments_on_page; while($m && isset($mas[$i]) && ($mas[$i]['level']!=1 || $m-- )) // пероуровневые
	while(isset($mas[++$i]) && $mas[$i]['level']!=1) { } // 

$k=0; while(isset($mas[$i]) && ( $mas[$i]['level']!=1 || (++$k <= $comments_on_page) ) ) { $m=$mas[$i++];
		if($podz or !$m['value'] or ($m['value']==$unic and $unic) or $yandex) { // открыт ли?

		if($m['level'] == $maxcommlevel) { // подготовить заплатку
			$zaglush="<div id='o".$m['p']['id']."' class='opc' onclick='opc(this,$num)' style='margin-left:".($m['level']*$GLOBALS['comment_otstup'])."px'></div>";
		}

		if($m['level'] <= $maxcommlevel) $s.=comment_one($m['p'],$mojno_comm,$m['level']); // выдать раскладушку
		else if($zaglush) { $s.=$zaglush; $zaglush=false; } // выдать заплатку, но только одну

		} elseif($comment_pokazscr) $s.="<div class=cscr style='margin-left:".($m['level']*$comment_otstup)."px'></div>";
	}

if(sizeof($mas)>10) {
	$s.=LL('comm:itogo',array('nmas'=>sizeof($mas),'u'=>(!$podz && $podzamok && sizeof($mas)>=100),
	'majax'=>"onclick=\"majax('comment.php',{a:'loadcomments',dat:".intval($art['num']).",mode:'all'})\""
	));



//	$s.="<center><p class=br>всего комментариев: ".sizeof($mas)."</p>";
//	if(!$podz && $podzamok && sizeof($mas)>=100) $s.="<p>показаны только открытые комментарии - 
// <a href=\"javascript:majax('comment.php',{a:'loadcomments',dat:".intval($art['num']).",mode:'all'})\">показать все</a>";
//	$s.="</center>";

}
if(sizeof($mas) && function_exists('PREVNEXT')) $s.=PREVNEXT();

return get_comm_button($num).$s.get_comm_button($num);
}

//====================================================================
function vseprint_comm($id,$level,$l) { global $comc,$comindex,$kstop; if(!isset($comindex[$id])) return array();
	$mas=array(); $level++; if(!$kstop--) idie('err kstop'.$id.h(print_r($mas,1)));
	foreach($comindex[$id] as $id2=>$value) { //if(!$value) continue;
		$mas[]=array('p'=>$comc[$id2],'value'=>( ($value=='open' or $value=='rul')?0:$value ),'id'=>$id2,'level'=>$level);
		$mas=array_merge($mas,vseprint_comm($id2,$level,$l));
		unset($comc[$id2]); // в любом случае удалить коммент из массива
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
} function strtolower2($s){ return strtr(strtolower($s),'јЅ¬√ƒ≈®∆«»… ЋћЌќѕ–—“”‘’÷„Ўўџ№ЏЁёя','абвгдеЄжзийклмнопрстуфхцчшщыьъэю€'); }

function ppp_nocomment() { return LL('comm:nocomments'); } // "<p class=z>комментариев нет или они все скрыты";

?>