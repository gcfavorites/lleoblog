<?php

include_once $include_sys."_sendmail.php";

function mail_answer($id,$newans) { //------------------- ������� �� �����

$p = mysql_fetch_assoc(mysql_query("SELECT r.Date,a.Header,r.Name,r.Address,r.IP,r.IPx,r.UserAgent,r.Guest_LJ,r.Guest_Name,
r.Commentary,r.Answer FROM `dnevnik_zapisi` AS a, `dnevnik_comments` AS r WHERE r.Date=a.Date AND r.id='".intval($id)."'"));

if(substr($p['Address'],0,7) == "mailto:"
&& $newans !='' // ����� ����� ���-�� ��������
&& $p['Answer']=='' // � ������� ������ (���� ��� �� ���������!) �� ����
) {

$maillink=$GLOBALS["httphost"].str_replace('-','/',$p["Date"]).".html";
$mailmail=str_replace("mailto:","",$p["Address"]);
$mailsubj = "����� �� ����������� �� ".$p["Date"].($p["Header"]?" (".$p["Header"].")":"");

$mailtext="<p>��� �������������� ������-�����. �� �������� ����������� � �����, ������� ����� ".$GLOBALS["admin_name"].",
������ ��� ��������� ������ email: ".$mailmail.". ������� ������� ����� �� ������: <a href=".$maillink.">".$maillink."</a>

<p><i>�� ������:</i>
<table width=100% style='border-collapse: collapse; border: 1px solid red;' bgcolor=#fff0ff>
<tr><td>�����:</td><td>".$p['Name']."</td></tr>
".($p['Guest_LJ']?"<tr><td><img src=http://stat.livejournal.com/img/userinfo.gif style=\"vertical-align: center;\"><a href=http://".$p['Guest_LJ'].".livejournal.com>".$p['Guest_LJ']."</a></td><td>".($p['Guest_Name']?" (".$p['Guest_Name'].")":"")."&nbsp;</td></tr>":"")."
<tr><td>".$p['IP'].($p['IPx']?", ".$p['IPx']:"")."</td><td>".$p['UserAgent']."</td></tr>
<tr bgcolor=#fffff0><td colspan=2>".$p['Commentary']."</td></tr></table>

<p><i>".$GLOBALS["admin_name"]." ��������:</i>
<p><table width=100% style='border-collapse: collapse; border: 1px solid red;' bgcolor=#fffff0>
<tr><td>".$newans."</td></tr></table>
";

sendmail($GLOBALS["admin_name"].": �������",$GLOBALS["admin_mail"],$p['Name'],$mailmail,$mailsubj,$mailtext);

}
}

?>