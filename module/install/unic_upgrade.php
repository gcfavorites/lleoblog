<?php

// ��� ������� ���������� 0, ���� ��������� ���� ������ �� ��������� (����. ������ ��� �������)
// ���� - ������ ��� ����������� ������ ������� ������.

$GLOBALS['DBSTRING']="FROM ".$GLOBALS['db_unic']
." WHERE `openid`!='' AND `openid` NOT LIKE '%".e('://')."%'";

function installmod_init() {
		$a=ms("SELECT COUNT(*) ".$GLOBALS['DBSTRING'],"_l",0);
		return ($a?"DB `unic` UPGRADE ($a)":false);
}

// ��� ������� - ���� ������ ������. ���� ������ �� ������� ������ - ������� 0,
// ����� ������� ����� �������, � ������� ���������� ������, ����� �� ������ ������� ����������.
// skip - � ���� ��������, allwork - ����� ���������� (�������� �����), $o - ��, ��� ������ �� �����.
function installmod_do() { global $o,$skip,$allwork,$delknopka; $starttime=time();

	$nLim=10;
	while((time()-$starttime)<5 && $skip<$allwork) {
		$pp=ms("SELECT * ".$GLOBALS['DBSTRING']." LIMIT $skip,$nLim","_a",0);

	foreach($pp as $p) { $i=$p['openid'];
		$i=strtolower($i);
		$i=trim($i,"/");
		if(!strstr($i,':')) $i='http://'.$i;
		//$o.="#".nl2br(h(print_r($pp,1)));
		$o.="<br><a href='".h($i)."'>".h($i)."</a>";
		//	usleep(1000);

		if($i!=$p['openid']) {
			msq_update($GLOBALS['db_unic'],array('openid'=>e($i)),"WHERE `id`=".$p['id'],"_l",0);
			$o.='<font color=green> upgrade</font> '.h($p['openid']);
		}
	}

		$skip+=$nLim;
	}
	$o.=" ".$skip;
	if($skip<$allwork) return $skip;
	$delknopka=0;
	return 0;
}

// ���������� ����� ����� ����������� ������ (����. ����� ������� � ���� ��� ���������).
// ���� ������ ������������ ������� - ������� 0.
function installmod_allwork() { return ms("SELECT COUNT(*) ".$GLOBALS['DBSTRING'],"_l",0); }

?>