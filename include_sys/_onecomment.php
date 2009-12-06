<?
include_once $include_sys."_obracom.php";
//include_once $include_sys."_geoip_all.php";

//$lcol=array('rus'=>'green','world'=>'brown','whois'=>'red');
//        $a="<font color=".$l[$a['basa']].">".$a['country'].", ".$a['city']."</font>";


//function onecomment($p,$i) { return "<div id=".$p['id']." class=po>"._onecomment($p,$i)."</div>"; }
//function _one_pravka($p,$answer='') { global $admin;

function onecomment_ans($p,$i) { global $sc,$admin,$IPsc,$IP;
	$s=onecomment($p,$i); if($p['Answer']) {

	$ans=$p["Answer"];
	$ans=nl2br(htmlspecialchars($ans));
	$ans=AddBB($ans);
	$ans="\n$ans\n";
	$ans=hyperlink($ans);
	$ans=trim($ans,"\n");
	$ans=search_podsveti($ans);
		$s .= "<div class=ct>";
		$a = intval($p["Answer_time"]);	if($a!=0) $s .= "<div class=time>".date('Y.m.d - H:i:s',$a)."</div>";
		if($p['ans_rulit']) $s .= getrulits($p['ans_rulit'],'red','+');
		if($p['ans_spamit']) $s .= getrulits($p['ans_spamit'],'blue','-');

	if( $p['ans_lastIPsc']!=$IPsc && $p['IP']!=$IP && $p['speckod']!=$sc ) { $s.= "<div class=kne onclick=\"ljjote('".$p['id']."','".$i."');\">жжошь!</div><div class=kne onclick=\"lsspam('".$p['id']."','".$i."');\">гонишь!</div>"; }

	$s .= "<div class=clh>".$GLOBALS['admin_name'].($p['Answer_user']!=''?" (".htmlspecialchars($p['Answer_user']).")":'').":</div>";

	$s .= "<div id='a".$p['id']."' class=cl>".$ans."</div></div>";
	} return $s;
}



function onecomment($p,$i) { global $admin,$podzamok,$sc,$IPsc,$IP,$geoip_color; 
	$s=$kn='';

	$commentary=$p["Commentary"];
	$commentary=nl2br(htmlspecialchars($commentary));
	$commentary=AddBB($commentary);
	$commentary="\n$commentary\n";
	$commentary=hyperlink($commentary);
	$commentary=trim($commentary,"\n");
	$commentary=search_podsveti($commentary);
	$id=$p['id'];

	if($p['rulit_master']) $kn .= "<img onclick=\"comment('','$id','$i','rulm_off')\" src=".$GLOBALS["www_design"]."e/true.png>";
	if($p['rulit']) $kn .= getrulits($p['rulit'],'red','+');
	if($p['spamit']) $kn .= getrulits($p['spamit'],'blue','-');

	$logn=$p['login']; if($logn) { $is=get_IS($logn); }

if(strstr($_SERVER['REQUEST_URI'],'.html') || strstr($_SERVER['REQUEST_URI'],'ajax_comments.php')) $i="<a href=#c".$id.">".$i."</a>";

	$s .= "<div class=time>";
//================================================ Linux/Windows
	$x=$p['UserAgent']; $y='';
	$a=array('Linux'=>'Linux','Windows'=>'Windows','NokiaE90'=>'Nokia-E90','Mac OS X'=>'Mac','FreeBSD'=>'FreeBSD','Ubuntu'=>'Ubuntu','Debian'=>'Debian','Firefox'=>'Firefox','Opera'=>'Opera','Safari'=>'Safari','MSIE'=>'IE','Konqueror'=>'Konqueror','Chrome'=>'Chrome');
	foreach($a as $a=>$b) if(strstr($x,$a)) $y.=($y?' ':'').$b;

	if($GLOBALS['onecomment_info']) $s .= "<span class=kn0 onclick=\"comment('','$id','<a href=#c$id>$i</a>','nochange')\">".($y?$y:'###')."</span> &nbsp; ";
	else $s .= "<span class=kn0 onclick=\"info_comment('$id','$i')\">".($y?$y:'###')."</span> &nbsp; ";

	if($p['whois_strana']!='') $s .= "<font color=".$geoip_color[$p['whois_basa']].">"
	.search_podsveti(htmlspecialchars($p['whois_strana'])).", "
	.search_podsveti(htmlspecialchars($p['whois_gorod']))
	."</font> ";
//================================================

$s .= date('Y.m.d - H:i:s', $p["DateTime"])."</div>
<b>".$i.".</b><div class=ch>".($logn?"
<noindex>
<img src='".$is['IMG']."' alt=' (".$is['ROOT'].") '><b><a href='http://".($is['DOMAIN']=='lleo.aha.ru'?'lleo.aha.ru/user/':'').$logn."' rel='nofollow'>".search_podsveti($is['USER0'])."</a></b>
</noindex>
":search_podsveti(htmlspecialchars($p["Name"])) ).
"</div>";


if($admin) {

	if($p['Address']!='') {	$mail=str_replace('mailto:','',$p['Address']);
		$s .= ' <span class=br><a href=mailto:'.$mail.'>'.search_podsveti(htmlspecialchars($mail)).'</a></span>';
		}
	if($logn) $s .= " <span class=kne><a href='".$GLOBALS["wwwhost"]."logon/?userinfo=".$logn."'>info</a></span>";
        $kn .= "<div class=kne onclick=\"del_comment('$id')\">del</div>";
        $kn .= "<div class=kne onclick=\"edit_comment('$id','$i')\">EDIT</div>";
        $kn .= "<div class=kne onclick=\"ans_comment('$id','$i')\">ќтвет</div>";
//      $kn .= "<div class=kne onclick=\"comment('','$id','$i','ed_rul')\">~+</div>";
        $kn .= "<div class=kne onclick=\"comment('','$id','$i','".($p['rulit_master']?"rulm_off')\">-":"rulm_on')\">V")."</div>";
        $kn .= "<div class=kne onclick=\"sec_comment('$id','$i','".($p['metka']=='open'?"screen')\">скрыть":"open')\">раскрыть")."</div>";
}

$s .= "<span class=kn><a href=".$GLOBALS["wwwhost"]."comments/?mode=onesc&sc=".htmlspecialchars($p['speckod']).">".htmlspecialchars(substr($p['speckod'],0,3))."</a></span>";

if($p['rulit_lastIPsc']!=$IPsc && $p['IP']!=$IP && $p['speckod']!=$sc) {
$kn .= "<div class=kne onclick=\"jjote('$id','$i');\">жжошь!</div><div class=kne onclick=\"sspam('$id','$i');\">гонишь!</a></div>";
}


if($GLOBALS['onecomment_info']) {
	$ip=$p['IP'];
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

$s .= "\n\n<div class=cc>\n".$commentary."\n</div>\n";

$s="<div>".$kn."</div><p>".$s;

if($p['metka']!='screen') $s = "<div class=c>".$s."</div>";
else $s = "<div class=c_c>".$s."</div>";

return($s);
}


function getrulits($n,$color,$znak) { return "\n<span class=br-".$color.">$znak"."$n</span>\n"; }




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



//==========================================================================

function load_comments_one($p) {
        $s = "\n\n<a name='c".$p["id"]."'></a><div class='item_com' name='".$p["id"]."' id='".$p["id"]."'>\n";
        $in=intval($p['idza']); if(!$in) $in=$p['idza0'].")";
        $s .= onecomment_ans($p,$in);
        $s .= "\n</div>\n";
        return $s;
}

function load_comments($num,$comments_order='normal') { global $podzamok,$sc,$lju,$load_comments_MS,$ttl;

$plusp=$load_comments_MS;

if(!$podzamok&&!$IS_EDITOR) $plusp .= " AND (`metka`='open' OR `speckod`='".e($sc)."')"; // только открытые комментарии или комментарии sc
if($_GET['mastercom']=='yes') $plusp .= " AND `rulit_master`='1'"; // только помеченные хоз€ином

$sql=ms("SELECT * FROM `dnevnik_comments` WHERE `DateID`='".e($num)."'".$plusp." ORDER BY `id`","_a",$ttl);

if(!sizeof($sql)) return "<p class=z>комментариев нет или они все скрыты";

$s='';

if($comments_order == 'rating') {
	$rati=array(); foreach($sql as $l=>$p) $rati[$l]=($p['rulit']-$p['spamit']); arsort($rati);
	foreach($rati as $l=>$n) $s.=load_comments_one($sql[$l]);
} elseif($comments_order == 'allrating') {
	$rati=array(); foreach($sql as $l=>$p) {
		$rei=$p['rulit']-$p['spamit']; // рейтинг комментари€
		$ans=$p['ans_rulit']-$p['ans_spamit']; // рейтинг моего ответа
		if(!$ans) $rati[$l]=$rei; // если нет рейтинга моего ответа - то обычна€ сумма
		else $rati[$l]=($p['rulit']+$ans); // если есть - то не учитывать минусы комментари€
	} arsort($rati);
	foreach($rati as $l=>$n) $s.=load_comments_one($sql[$l]);
} else {
	foreach($sql as $p) $s.=load_comments_one($p);
}

return $s;
}

?>