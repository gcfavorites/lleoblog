<?php // ������

if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����
// if(!$admin) redirect($wwwhost."login/"); // ����������� - �����

// $pravki_npage$npage=50; // ������� ���������� ������ �� ������ �������� ��� �������� ������

$_PAGE["design"] = file_get_contents($GLOBALS['host_design']."pravka.html");
$_PAGE["title"] = $_PAGE["header"] = "����������������� ������";
$_PAGE['wwwcharset']=$wwwcharset;
$_PAGE['admin_name']=$admin_name;
$_PAGE['www_design']=$www_design;
$_PAGE['hashpage']=$hashpage;


STYLE_ADD($GLOBALS['httpsite'].$GLOBALS['www_design']."pravka.css");

include_once $include_sys."_one_pravka.php"; // ��������� ������ ������ � ����� �������
include_once $include_sys."text_scripts.php"; // �������� ����������

SCRIPTS("
function pp(id) { pravka(id,'podrobno'); }
function pdi(id) { var i=prompt('\\n��, �������. ������ ���:\\n','�������, '); if(i && i.length != 0 ) pravka(id,1,i); }
function pni(id) { var i=prompt('\\n���, ���������. ������ ���:\\n','���, '); if(i && i.length != 0 ) pravka(id,0,i); }
function pd(id) { pravka(id,1,'da'); }
function pd(id) { pravka(id,1,'da'); }
function pu(id) { pravka(id,1,'ugovorili'); }
function pz(id) { pravka(id,0,'zadumano'); }
function pg(id) { pravka(id,0,'gramotei'); }
function pl(id) { pravka(id,0,'len'); }
function ps(id) { pravka(id,0,'spam'); }
function pe(id) { pravka(id,'edit'); }
function pc(id) { pravka(id,'edit_c'); }
function px(id) { // if(confirm('����� �������?'))
	pravka(id,'del'); }

function pravka(id,volya,answer) { JsHttpRequest.query('".$wwwhost."ajax_pravka.php', { action: volya, id: id, answer: answer,
hash: '".$hashpage."'
},
        function(responseJS, responseText) { if(responseJS.status) { document.getElementById(id).innerHTML = responseJS.otvet; } },true);
}
");

$s="<center>"; $npage1=$pravki_npage+1;
$act=htmlspecialchars($_GET['a']);
$nam=htmlspecialchars($_GET['nam']);
$skip=intval($_GET['skip']);
if(!$admin) $s .= "
<p>����� ������������ ����������, �� ��� �� ������������� ������. ������, �� �� �����.
<br>������ ����������, ����� �� �����, �� <font color=red>��������� �� ���������</font>.";

if($act=='arh') $sql = ms("SELECT * FROM `".$db_pravka."` ORDER BY `DateTime` DESC LIMIT ".$skip.",".$npage1,'_a',0);
elseif($act=='ego') $sql = ms("SELECT * FROM `".$db_pravka."` WHERE `sc`='".e($_GET['sc'])."' ORDER BY `DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='my') $sql = ms("SELECT * FROM `".$db_pravka."` WHERE `sc`='".e($_COOKIE['sc'])."' ORDER BY `DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='mynew') $sql = ms("SELECT * FROM `".$db_pravka."` WHERE `metka`='new' AND `sc`='".e($_COOKIE['sc'])."' ORDER BY `DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='ras') $sql = ms("SELECT * FROM `".$db_pravka."` WHERE `Date` LIKE '%".e($_GET['nam'])."%' ORDER BY `DateTime` LIMIT $skip,$npage1",'_a',0);
else $sql = ms("SELECT * FROM `".$db_pravka."` WHERE `metka`='new' ORDER BY `DateTime` DESC",'_a',0);

$colnewcom=sizeof($sql);

if($act=='arh' || $act=='my' || $act=='ras' ) {
	$prev=$next='';

	if($colnewcom==$npage1) { $colnewcom--; unset($sql[$colnewcom]);
		$prev="<a href='".$mypage."?a=".$act.($act=='ras'?"&nam=".$nam:'')."&skip=".($skip+$pravki_npage)."'>&lt;&lt; ������ ".$pravki_npage."</a>"; }

	if($skip>=$pravki_npage)
		$next.="<a href='".$mypage."?a=".$act.($act=='ras'?"&nam=".$nam:'')."&skip=".($skip-$pravki_npage)."'>������� ".$pravki_npage." &gt;&gt;</a>";

	$prevnext="<center><table width=96%><tr><td align=left width=32%>$prev</td><td align=center width=32%>
<a href='".$mypage."'>��� �����</a>
<a href='".$mypage."'?a=mynew'>��� �����</a>

<br><form action='".$mypage."'>".($skip?"<input type=hidden name=skip value=".$skip.">":"")."
<input type=hidden name=a value='ras'>
<input type=text size=10 name=nam value='".$nam."'>
<input type=submit value='������ �� �����'>
</form>

</td><td align=right width=32%>$next</td></tr></table></center>";

} else {
	$prevnext="<center><p class=br><a href='".$mypage."?a=arh'>��� ������������</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href='$mypage?a=my'>������ ���</a></p></center>";
}

$s .= $prevnext;

$s .= "<p>������: ".$colnewcom."</center>";

if($colnewcom) {
	for($i=0;$i<$colnewcom;$i++) if(isset($sql[$i])) {
		$p=$sql[$i]; $data=$p['Date'];
		$s .= print_header($data);
		foreach($sql as $i2=>$p2) if($p2['Date']==$data) { $s.=print1($p2); unset($sql[$i2]); }
	}
	$s .= "<p>".$prevnext;
} else { $s .= "<center>������ ������ ���</center>"; }

die($s);

#################################################################################################################################

function discard_pravka($id) { msq_update($GLOBALS['db'],array('metka'=>'discard'),"WHERE `id`='$id'"); } // �������� � ����

function print1($p) {
	$id=$p['id'];
	$textnew=htmlspecialchars($p['textnew']);
	$text=htmlspecialchars($p['text']);
	$stdpravka=($GLOBALS['pravshort']?$p['text']:$p['stdprav']);
//	$answer .= obrabotka_admina($p,$id,$text,$textnew,$stdpravka); // ������ ��� ������ ��������� - ���� ������ ���� �� ���������
	return one_pravka($p,$answer);
}

function print_header($data) {
	list($base,$table,$bodyname,$wherename,$whereid)=explode('@',$data);
	if($base=='file#') {
		$data='/'.htmlspecialchars($table); $link='/'.htmlspecialchars($table); // $GLOBALS['wwwhost'].$data;

	} elseif($base.$table=="lleo"."dnevnik_zapisi") { // � ������ ������� �����
		$p=ms("SELECT `Date`,`Header` FROM `lleo`.`dnevnik_zapisi` ".WHERE("`".e($wherename)."`='".e($whereid)."'")." LIMIT 1","_1",$GLOBALS['ttl']);
		$data=htmlspecialchars($p['Date'].($p['Header']!=''?" ".$p['Header']:''));
		$link="/dnevnik/".htmlspecialchars(strtr($p['Date'],'-','/')).".html";

	} elseif($base.$table=="lleo"."dnevnik_comments") { // � ������ ������� �����
		$data="����������� #".htmlspecialchars($whereid);
		$link="";

	} elseif($base.$table=="dnevnik_comments") { // � ������ ������ �����
		$data="����������� � ����� #".htmlspecialchars($whereid);
		$link="";


	} elseif($table=='dnevnik_zapisi') { // � ������ �����
		$p=ms("SELECT `Date`,`Header` FROM `dnevnik_zapisi` ".WHERE("`".e($wherename)."`='".e($whereid)."'")." LIMIT 1","_1",$GLOBALS['ttl']);
		$data=htmlspecialchars($p['Date'].($p['Header']!=''?" ".$p['Header']:''));
		$link=$GLOBALS['wwwhost'].htmlspecialchars($p['Date']).".html";

	} elseif($table.$bodyname=="site"."text") { // � ������ �����
		$data="���� ����� #".htmlspecialchars($whereid);
		$link=$GLOBALS['wwwhost']."adminsite?edit=".htmlspecialchars($whereid);

	} else {
		$data=htmlspecialchars("SELECT `".$bodyname."` FROM ".($base==''?'':"`".$base."`.")."`".$table."` WHERE ".$wherename."='".$whereid."'");
		$link='';
	}

	if($link!='') $data="<a href='".$link."'>".$data."</a>";
	return "<p><center><b>".$data."</b></center>";
}

?>
