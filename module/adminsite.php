<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// ѕравки

if(!$admin) redirect($wwwhost."login/"); // посторонних - нахуй

// $pravki_npage$npage=50; // сколько показывать правок на каждой странице при листании архива

$_PAGE = array('design'=>file_get_contents($GLOBALS['host_design']."adminsite.html"),
'title' => " онтент-администрирование сайта",
'header' => " онтент-администрирование сайта",
'prevlink'=>$wwwhost,
'nextlink'=>$wwwhost,
'uplink'=>$wwwhost,
'downlink'=>$wwwhost."contents/",

'www_design'=>$www_design,
'admin_name'=>$admin_name,
'httphost'=>$httphost,
'wwwhost'=>$wwwhost,
'signature'=>$signature,
'wwwcharset'=>$wwwcharset,

'hashpage'=>$hashpage,
);




//$_PAGE["wwwhost"] = $wwwhost;
// include_once $include_sys."_one_pravka.php"; // процедура вывода окошка с одной правкой

SCRIPT_ADD($GLOBALS['www_design']."JsHttpRequest.js");

SCRIPTS("
function zabil(id,text) { document.getElementById(id).innerHTML = text; }
function vzyal(id) { return document.getElementById(id).innerHTML; }
function zakryl(id) { document.getElementById(id).style.display='none'; }
function otkryl(id) { document.getElementById(id).style.display='block'; }

function ajax_site(action,id,name,text,type,Access) { JsHttpRequest.query('".$wwwhost."ajax_site.php',
{ action: action, id: id, name: name, text: text, type: type, Access: Access, hash: '{hashpage}' },
function(responseJS, responseText) { if(responseJS.status) { 
	if(responseJS.reload) top.window.location='".$mypage."?'+Math.random()*100000;
	zabil(id,responseJS.otvet); 
} },true);

}

function l(id,name,type) { zabil(id,'...wait...'); ajax_site('view',id,name,0,type); }
");


$s="<center>"; $npage1=$pravki_npage+1;
//$action=htmlspecialchars($_GET['a']);
$skip=0;
$npage1=1000;



if($_GET['a']=='create') {
	$name=htmlspecialchars(urldecode($_GET['name']));
	$js="ajax_site('new',0,'".$name."','".str_replace(array("\n","'"),array("\\n","\\'"),ms("SELECT `text` FROM `".$db_site."` WHERE `name`='site_template'",'_l',0))."','page');";
	die("<center><div id=0><a href=\"javascript:".$js."\">создать: '".$name."'</a></div></center><script>".$js."</script>");
}












// $nam=htmlspecialchars($_GET['nam']);
// $skip=intval($_GET['skip']);
// $text=htmlspecialchars($_POST['text']);
$s .= "<center><div id=0><li><a href=\"javascript:l(0)\">создать новую</a></li></div></center>";

//$s .= ms("SELECT `text` FROM `".$db_site."` WHERE `name`='fotoload'",'_l',0); // подстыковать модуль fotoload


//die("<pre>".htmlspecialchars(print_r($_PAGE,1)));

if($_GET['mode']!='one') {
$sql = ms("SELECT `name`,`id`,`type`,`text` FROM `".$db_site."` ORDER BY `name` LIMIT ".$skip.",".$npage1,'_a',0);
} else {
$sql = ms("SELECT `name`,`id`,`type`,`text` FROM `".$db_site."` WHERE `id`='".intval($_GET['edit'])."'",'_a',0);
}

/*
$colnewcom=sizeof($sql);

if($act=='arh' || $act=='my' || $act=='ras' ) {
	$prev=$next='';

	if($colnewcom==$npage1) { $colnewcom--; unset($sql[$colnewcom]);
		$prev="<a href='".$mypage."?a=".$act.($act=='ras'?"&nam=".$nam:'')."&skip=".($skip+$pravki_npage)."'>&lt;&lt; ранние ".$pravki_npage."</a>"; }

	if($skip>=$pravki_npage)
		$next.="<a href='".$mypage."?a=".$act.($act=='ras'?"&nam=".$nam:'')."&skip=".($skip-$pravki_npage)."'>поздние ".$pravki_npage." &gt;&gt;</a>";

	$prevnext="<center><table width=96%><tr><td align=left width=32%>$prev</td><td align=center width=32%>
<a href='".$mypage."'>все новые</a>
<a href='".$mypage."'?a=mynew'>мои новые</a>

<br><form action='".$mypage."'>".($skip?"<input type=hidden name=skip value=".$skip.">":"")."
<input type=hidden name=a value='ras'>
<input type=text size=10 name=nam value='".$nam."'>
<input type=submit value='искать по имени'>
</form>

</td><td align=right width=32%>$next</td></tr></table></center>";

} else {
	$prevnext="<center><p class=br><a href='".$mypage."?a=arh'>все обработанные</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href='$mypage?a=my'>только мои</a></p></center>";
}

$s .= $prevnext;

$s .= "<p>правок: ".$colnewcom."</center>
</center><ul>";

if($colnewcom) {
*/

$s.="</center>";

$o=''; foreach($sql as $n=>$p) if($p['type']=='page') { $o.=print1($p); unset($sql[$n]); }
if($o!='') $s.="<h1>загружаемые страницы сайта</h1>".$o;

$o=''; foreach($sql as $n=>$p) if($p['type']=='design') { $o.=print1($p); unset($sql[$n]); }
if($o!='') $s.="<h1>модули дизайна</h1>".$o; 

$o=''; foreach($sql as $n=>$p) if($p['type']!='photo') $o.=print1($p); elseif($_GET['foto']=='on') $o.=print_foto($p);
if($o!='') $s.="<h1>прочее</h1>".$o; 

if(intval($_GET['edit'])) { $s.="<script>l('".intval($_GET['edit'])."')</script>"; }



// if($_GET['foto']!='on') $s.="<p><a href=$mypage?foto=on>показать фото</a>";

die($s);



function print1($p) {
	$name=htmlspecialchars($p['name']); $id=intval($p['id']);
	return "\n<div id=".$id."><li><a href=\"javascript:l('".$id."')\">".(strlen($name)?$name:"&lt;...&gt;")."</a>
&nbsp;(<a href=".$GLOBALS['wwwhost'].$p['name']." target=_blank>open</a>)</li></div>";
}

function print_foto($p) {
	$name=htmlspecialchars($p['name']); $id=intval($p['id']);
	return "\n<div id=".$id."><li><table><tr valign=center>
<td><img src=".$GLOBALS['foto_www_preview'].$p['text']."></td>
<td><a href=\"javascript:l('".$id."')\">".(strlen($name)?$name:"&lt;...&gt;")."</a></td>
</tr></table></li></div>";
}




#################################################################################################################################
/*

function discard_pravka($id) { msq_update($GLOBALS['db'],array('metka'=>'discard'),"WHERE `id`='$id'"); } // пометить в базу

function print1($p) {
	$id=$p['id'];
	$textnew=htmlspecialchars($p['textnew']);
	$text=htmlspecialchars($p['text']);
	$stdpravka=($GLOBALS['pravshort']?$p['text']:$p['stdprav']);
//	$answer .= obrabotka_admina($p,$id,$text,$textnew,$stdpravka); // модель дл€ вс€кой обработки - типа чистки базы от спаммеров
	return one_pravka($p,$answer);
}

function print_header($data,$i) { return "<div id=><li>".htmlspecialchars($data)."</li>";

	list($base,$table,$bodyname,$wherename,$whereid)=explode('@',$data);
	if($base=='_file_') {
		$data=htmlspecialchars($table); $link=$GLOBALS['wwwhost'].$data;
	} elseif($table=='dnevnik  _zapisi') { // в случае блога
		$p=ms("SELECT `Date`,`Header` FROM `dnevnik  _zapisi` ".WHERE("`".e($wherename)."`='".e($whereid)."'")." LIMIT 1","_1",$GLOBALS['ttl']);
		$data=htmlspecialchars($p['Date'].($p['Header']!=''?" ".$p['Header']:''));
		$link=$GLOBALS['wwwhost'].htmlspecialchars($p['Date']).".html";
	} else {
		$data=htmlspecialchars("SELECT `".$bodyname."` FROM ".($base==''?'':"`".$base."`.")."`".$table."` WHERE ".$wherename."='".$whereid."'");
		$link='';
	}
	if($link!='') $data="<a href='".$link."'>".$data."</a";
	return "<p><center><b>".$data."</b></center>";
}
*/
?>
