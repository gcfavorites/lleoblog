<?php

include_once $include_sys."_sendmail.php";

function mail_answer($id,$newans) { //------------------- коммент по емайл

$p = mysql_fetch_assoc(mysql_query("SELECT r.Date,a.Header,r.Name,r.Address,r.IP,r.IPx,r.UserAgent,r.Guest_LJ,r.Guest_Name,
r.Commentary,r.Answer FROM `dnevnik_zapisi` AS a, `dnevnik_comments` AS r WHERE r.Date=a.Date AND r.id='".intval($id)."'"));

if(substr($p['Address'],0,7) == "mailto:"
&& $newans !='' // новый ответ что-то содержит
&& $p['Answer']=='' // а старого ответа (базу еще не обновл€ть!) не было
) {

$maillink=$GLOBALS["httphost"].str_replace('-','/',$p["Date"]).".html";
$mailmail=str_replace("mailto:","",$p["Address"]);
$mailsubj = "ответ на комментарий за ".$p["Date"].($p["Header"]?" (".$p["Header"].")":"");

$mailtext="<p>Ёто автоматическое письмо-ответ. ¬ы оставили комментарий в блоге, который ведет ".$GLOBALS["admin_name"].",
указав дл€ обратного ответа email: ".$mailmail.". ќткрыть заметку можно по ссылке: <a href=".$maillink.">".$maillink."</a>

<p><i>¬ы писали:</i>
<table width=100% style='border-collapse: collapse; border: 1px solid red;' bgcolor=#fff0ff>
<tr><td>јвтор:</td><td>".$p['Name']."</td></tr>
".($p['Guest_LJ']?"<tr><td><img src=http://stat.livejournal.com/img/userinfo.gif style=\"vertical-align: center;\"><a href=http://".$p['Guest_LJ'].".livejournal.com>".$p['Guest_LJ']."</a></td><td>".($p['Guest_Name']?" (".$p['Guest_Name'].")":"")."&nbsp;</td></tr>":"")."
<tr><td>".$p['IP'].($p['IPx']?", ".$p['IPx']:"")."</td><td>".$p['UserAgent']."</td></tr>
<tr bgcolor=#fffff0><td colspan=2>".$p['Commentary']."</td></tr></table>

<p><i>".$GLOBALS["admin_name"]." отвечает:</i>
<p><table width=100% style='border-collapse: collapse; border: 1px solid red;' bgcolor=#fffff0>
<tr><td>".$newans."</td></tr></table>
";

sendmail($GLOBALS["admin_name"].": дневник",$GLOBALS["admin_mail"],$p['Name'],$mailmail,$mailsubj,$mailtext);

}
}

?>