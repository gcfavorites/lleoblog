<?php

/* Вот такой массив передается:
    [db] => fido
    [table] => fidoecho
    [echo] => cooking
    [db_fido] => fidoecho
    [db_fido_num] => fidoecho_num
    [db_fidopoints] => fidopoints
    [db_fidopodpiska] => fidopodpiska
    [db_fidomy] => fidomy
    [msqid] => Resource id #125
    [id] => 8629
    [area] => cooking
    [arean] => 11
    [msgid] => 1:261/1381.0 4d34b1ac
    [reply] => 1:18/200.0 4d32cb6a
    [subj] => packratting  was: Bargains that aren't
    [from] => Nancy Backus
    [to] => Dave Drum
    [froma] => 1:261/1381
    [toa] => 0
    [number] => 324
    [body] => ^AMSGID: 1:261/1381.0 4d34b1ac^M^AREPLY: 1:18/200.0 4d32cb6a^M^APID: BWTG 3.11 [Eval]^M^ATID: GE/32 1.2^M-=>
    [datetime] => 1295277651
    [date] => 1295321126
    [attrib] => 0

//	$g=fopen("___startlog2.txt","a+"); fputs($g,"\n\n\n RESULT: $pp \n\n".print_r($pp,1)); fclose($g);
//        if ($mode == '_a') { $s = array(); while ($p = mysql_fetch_assoc($sql)) $s[]=$p; }
//        elseif ($mode == '_1') { if(mysql_num_rows($sql)>=1) $s = mysql_fetch_assoc($sql); else $s=false; }
//        elseif ($mode == '_l') { if(mysql_num_rows($sql)>=1) $s = mysql_result($sql,0,0); else $s=false; }
//        else { $s=array(); while($p=mysql_fetch_assoc($sql)) $s[$p[$mode]]=$p; }
//$g=fopen("___startlog2.txt","a+"); fputs($g,"\n\n\nEEP: $msqid,$point,$arean,$id ".$GLOBALS['db_fidomy'].""); fclose($g);

*/

function robot_copypoint($ara) {
	if($ara['area']!='netmail') {
		$q=mysql_query("SELECT `point` FROM ".$GLOBALS['db_fidopodpiska']." WHERE `echonum`='".$ara['arean']."'",$ara['msqid']);
		while($l=mysql_fetch_assoc($q)) add_table_fidomy($ara['msqid'],$l['point'],$ara['arean'],$ara['id']);
		mysql_free_result($q);
	} else {
		$point=substr(strstr($ara['toa'],'.'),1);
		if($point!='') add_table_fidomy($ara['msqid'],$point,$ara['arean'],$ara['id']);
	}
	return true;
}

function add_table_fidomy($msqid,$point,$arean,$id) {
mysql_query("INSERT INTO ".$GLOBALS['db_fidomy']." (point,arean,id) VALUES ('".intval($point)."','$arean','$id')",$msqid);
}

?>