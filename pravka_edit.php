<?php // ������

//$pravshort=true;
//$arhdir=$_SERVER['DOCUMENT_ROOT'].'/'; // � ����� �� ����� �����
//$db='archive_pravki'; // ��� ����, ��� ������ ������������
$db='site_pravki'; // ��� ����, ��� ������ ������������
//$db='dnevnik_pravki'; // ��� ����, ��� ������ ������������
$npage=50; // ������� ���������� ������ �� ������ �������� ��� �������� ������

$namescr=$_SERVER['PHP_SELF']; // '/arhive/edit.php';

include "config.php";
include $include_sys."_autorize.php";
//include $include_sys."_msq.php";
// mystart();

include $include_sys."_one_pravka.php");
if($admin) include_once $include_sys."_podsveti.php"; // ��������� Diff - ������ ����, ��������, �� ��������

function print_header($data) { return "<p><center><b><a href='/".$data."'>".$data."</a></b></center>"; }

/*
function obrabotka_admina($p,$id,$text,$textnew,$stdprav) { global $admin;

if($admin && $p['metka']!='submit') {

        $oldtxt = file_get_contents($GLOBALS['arhdir'].$p['Date']);
        $text=pravka_bylo($stdprav);
//      return $text;
        if(substr_count($oldtxt,$text) != 1) return '�� ������� ����� ��� ����� ��������������.';
        return ''; }

return ''; // ������� ��� ���������
//if($GLOBALS['admin']) { if($textnew=='und�fin�d' || $textnew=='null') { $s .= "<br>������: und�fin�d/null"; discard_pravka($id); }
//$pp=ms("SELECT `Body` FROM `dnevnik_zapisi` WHERE `Date`='$data'",'_1',0); $oldtxt=$pp['Body']; $n=substr_count($oldtxt,$text);if($n==0)
}
*/

print "<html><head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\" />
<title>�������: ������</title>
<link href=\"/sys/pravka/pravka.css\" rel=\"stylesheet\" type=\"text/css\" />

<!-- script type=\"text/javascript\" language=\"JavaScript\" src=\"/sys/Js	HttpRequest.js\"></script -->
<script>
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
function px(id) { /* if(confirm('����� �������?')) */ pravka(id,'del'); }
function pravka(id,volya,answer) { JsHttpRequest.query('/sys/pravka/ajax_pravka.php', { action: volya, id: id, answer: answer },
        function(responseJS, responseText) { if(responseJS.status) { document.getElementById(id).innerHTML = responseJS.otvet; } },true);
}
</script>


".prostynka_pravok()."

</body></html>";

exit;









// code 

function prostynka_pravok() { global $admin,$npage,$db,$namescr;
	$prostynka=''; $npage1=$npage+1;
	$act=$_GET['a'];
	$skip=intval($_GET['skip']);
if(!$admin) $prostynka .= "<p>����� ������������ ����������, �� ��� �� ������������� ������.<br>������, �� �� �����. ������ ����������, ����� �� �����, �� ��������� �� ���������.";

if($act=='arh') $sql = ms("SELECT * FROM `$db` ORDER BY `DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='ego') $sql = ms("SELECT * FROM `$db` WHERE `sc`='".e($_GET['sc'])."' ORDER BY `DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='my') $sql = ms("SELECT * FROM `$db` WHERE `sc`='".e($_COOKIE['sc'])."' ORDER BY `DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='mynew') $sql = ms("SELECT * FROM `$db` WHERE `metka`='new' AND `sc`='".e($_COOKIE['sc'])."' ORDER BY `DateTime` DESC LIMIT $skip,$npage1",'_a',0);
elseif($act=='ras') $sql = ms("SELECT * FROM `$db` WHERE `Date` LIKE '%".e($_GET['nam'])."%' ORDER BY `DateTime` LIMIT $skip,$npage1",'_a',0);
else $sql = ms("SELECT * FROM `$db` WHERE `metka`='new' ORDER BY `DateTime` DESC",'_a',0);
	$colnewcom=sizeof($sql);

if($act=='arh' || $act=='my' || $act=='ras' ) {	$prev=$next='';

if($colnewcom==$npage1) { $colnewcom--; unset($sql[$colnewcom]);
	$prev="<a href='$namescr?a=$act".($act=='ras'?"&nam=".$_GET['nam']:"")."&skip=".($skip+$npage)."'>&lt;&lt; ������ $npage</a>"; }

if($skip>=$npage) $next.="<a href='$namescr?a=$act".($act=='ras'?"&nam=".$_GET['nam']:"")."&skip=".($skip-$npage)."'>������� $npage &gt;&gt;</a>";

$prevnext="<center><table width=96%><tr><td align=left width=32%>$prev</td><td align=center width=32%>
<a href='$namescr'>��� �����</a>
<a href='$namescr?a=mynew'>��� �����</a>

<br><form action='$namescr'>".($skip?"<input type=hidden name=skip value=".$skip.">":"")."
<input type=hidden name=a value='ras'>
<input type=text size=10 name=nam value='".htmlspecialchars($_GET['nam'])."'>
<input type=submit value='�������'>
</form>

</td><td align=right width=32%>$next</td></tr></table></center>";

} else {
	$prevnext="<center><p class=br><a href='$namescr?a=arh'>��� ������������</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href='$namescr?a=my'>������ ���</a></p></center>";
}

$prostynka .= $prevnext;

$prostynka .= "<p>������: ".$colnewcom;

if($colnewcom) {
	for($i=0;$i<$colnewcom;$i++) if(isset($sql[$i])) {
		$p=$sql[$i]; $data=$p['Date'];
		$prostynka .= print_header($data);
		foreach($sql as $i2=>$p2) if($p2['Date']==$data) { $prostynka.=print1($p2); unset($sql[$i2]); }
	}
	$prostynka .= "<p>".$prevnext;
} else { $prostynka .= "<center>������ ������ ���</center>"; }

return $prostynka;
}

function discard_pravka($id) { msq_update($GLOBALS['db'],array('metka'=>'discard'),"WHERE `id`='$id'"); } // �������� � ����

function print1($p) {
	$id=$p['id'];
	$textnew=htmlspecialchars($p['textnew']);
	$text=htmlspecialchars($p['text']);
	$stdpravka=($GLOBALS['pravshort']?$p['text']:$p['stdprav']);
//	$answer .= obrabotka_admina($p,$id,$text,$textnew,$stdpravka); // ������ ��� ������ ��������� - ���� ������ ���� �� ���������
	return one_pravka($p,$answer);
}

?>
