<?php

/*
        $s="DELETE FROM $tb WHERE $a $u";
// if(msq_pole($table,$pole)===false) msq("ALTER TABLE `".$table."` ADD `".$pole."` ".$s
msq_pole($tb,$pole) // ���������, ���������� �� ����� ���� � ������� $tb
 // ���������, ���������� �� ����� �������
msq_index($tb,$index) // ���������, ���������� �� ����� ������
// �������� ���� � �������	function msq_change_pole($table,$pole,$s)
// �������� ���� �������	function msq_add_pole($table,$pole,$s)
// ������� ���� �� �������	function msq_del_pole($table,$pole)
// �������� ������ � �������	function msq_add_index($table,$pole,$s)
// ������� ������ �� �������	function msq_del_index($table,$pole)
// ������� �������		function msq_add_table($table,$s)
// ������� �������		function msq_del_table($table,$text)
*/

// ��� ������� ���������� 0, ���� ��������� ���� ������ �� ��������� (����. ������ ��� �������)
// ���� - ������ ��� ����������� ������ ������� ������.
function installmod_init(){
	if(msq_pole('dnevnik_zapisi','template')===false) return false;
	return "������������� ������� dnevnik_zapisi � ����� ������!";
}

function installmod_get(){ global $skip,$lim,$o;
	$pp=ms("SELECT * FROM `dnevnik_zapisi` LIMIT $skip,$lim","_a",0);
	if($pp===false or !sizeof($pp)) { $o.="<p><hr>done"; return 0; }
	return $pp;
}

// ��� ������� - ���� ������ ������. ���� ������ �� ������� ������ - ������� 0,
// ����� ������� ����� �������, � ������� ���������� ������, ����� �� ������ ������� ����������.
// skip - � ���� �������� (���������� 0), allwork - ����� ���������� (�������� �����), $o - ��, ��� ������ �� �����.
function installmod_do() { global $zopt_a,$o,$skip,$allwork,$delknopka,$lim,$msqe; $starttime=time();	$lim=100;

$m=explode(' ',"include Comment_view Comment_write Comment_screen Comment_tree autokaw autoformat template");

	if(($pp=installmod_get())===0) return $pp;

	$i=0; while((time()-$starttime)<2 && $skip<$allwork) {
		if(!isset($pp[$i])) { if(($pp=installmod_get())===0) return $pp; else $i=0; }
		$p=$pp[$i++]; $skip++;
	//--------------------------------------
		$o.="<br>".$p['Date'];
		$opt=array();
		foreach($m as $l) {
			if($p[$l]!=$zopt_a[$l][0]) {
				$opt[$l]=$p[$l];
				$o.=sg("<br> &nbsp; &nbsp; $l: '".$p[$l]."' (default: '".$zopt_a[$l][0]."')");
			}
		}
//		if($p['default_comments_order']!=0) $o.=sr(" #".$p['default_comments_order']);
		// usleep(10000);
	//--------------------------------------
	if(sizeof($opt)) msq_update('dnevnik_zapisi',array('opt'=>ser($opt)),"WHERE `num`='".$p['num']."'");
	}

	$o.="<hr>".$skip;
	if($skip<$allwork) return $skip;

	foreach($m as $l) msq_del_pole('dnevnik_zapisi',e($l));
	msq_del_pole('dnevnik_zapisi','Comment');
	msq_del_pole('dnevnik_zapisi','comments_order');
	msq_del_pole('dnevnik_zapisi','count_comments_open');
	$delknopka=1;

	return 0;
}

// ���������� ����� ����� ����������� ������ (����. ����� ������� � ���� ��� ���������).
// ���� ������ ������������ ������� - ������� 0.
// ��������� �������, ��� ����� ���-�� ������� �������� - ��������, �������� ������� ��� �������� ����������
function installmod_allwork() {
	msq_del_pole('dnevnik_zapisi','opt');
	msq_add_pole('dnevnik_zapisi','opt','text NOT NULL');
	return ms("SELECT COUNT(*) FROM `dnevnik_zapisi`","_l",0);
}

/*
$zopt_a=array(
        // 'comments_order'=>array('','normal rating allrating'),
        'include'=>array('','s',40),
        'Comment'=>array('enabled','enabled disabled allways_on screen normalscreen'),
        'Comment_view'=>array('on','on off rul load timeload'),
        'Comment_write'=>array('on','on off friends-only login-only timeoff login-only-timeoff'),
        'Comment_screen'=>array('open','open screen friends-open'),
        'Comment_tree'=>array('1','1 0'),
        'autoformat'=>array('p','no p pd'),
        'template'=>array('blog','s',32),
        'autokaw'=>array('auto','auto no')
);
foreach($zopt_a as $n=>$l) if(isset(${'zopt_'.$n})) $zopt_a[$n][0]=${'zopt_'.$n};
*/

?>