<?php

function ACC_ajax(){ if_iphash(RE('iphash'));
	$acc=RE('acc');
  if(($acn=ms("SELECT `acn` FROM `jur` WHERE `acc`='".e($acc)."' LIMIT 1","_l",0))!==false)
    idie("������� ��� ����������."
.($GLOBALS['IS']['login']==$acc?"<p>�������� <a href=\"javascript:majax('editor.php',{acn:".$acn.",a:'newform',hid:hid})\">����� �������</a>":'')
    );
  if(($u=ms("SELECT `id` FROM ".$GLOBALS['db_unic']." WHERE `login`='".e($acc)."'","_l",0))===false) idie("User `".h($acc)."` not found!");


  msq_add('jur',array('acc'=>e($acc),'unic'=>$u));
  $acn=ms("SELECT `acn` FROM `jur` WHERE `acc`='".e($acc)."'","_l",0);
  return "
idie('User: `".h($acc)."` unic=$u <font color=green>CREATED</font> with id=$acn"
."<p>�������� <a href=\"javascript:majax('editor.php',{acn:".$acn.",a:'newform',hid:hid})\">����� �������</a>"
."');";
}

function ACC($e) { global $admin,$acc,$acn,$ADM,$IS,$httphost;
$conf=array_merge(array(
'mode'=>"admin",
'sort'=>'',
'template'=>"<br><a href='{acc_link}'>{acc}</a> (<a href='{acc_link}contents'>{count}</a>)"
),parse_e_conf($e));

if($conf['mode']=='list') {
	$pp=ms("SELECT `acc`,`acn` FROM `jur`".($conf['sort']!=''?" ORDER BY `".e($conf['sort'])."`":''),"_a");
	$x=strstr($conf['template'],'{count}');
	$o=''; foreach($pp as $p) {
// die(WHERE()."   ### $acn");
		$count=($x?ms("SELECT COUNT(*) FROM `dnevnik_zapisi`".WHERE("`acn`='".$p['acn']."'"),"_l"):0);
		$o.=mper($conf['template'],array('acc'=>h($p['acc']),'acc_link'=>acc_link($p['acc']),'count'=>$count));
	}
	return $o;
}

if($conf['mode']=='count') { return ms("SELECT COUNT(*) FROM `jur`","_l"); }

if($conf['mode']=='admin' && !$admin) {	if(empty($acc)) return "Admin only!"; redirect($httphost.'acc'); }

// return "admin: ".intval($admin);

	// ����� ����� ������� �������:
	if($admin&&!empty($acc)) return "<span class='ll' onclick=\"if(confirm('create?'))majax('module.php',{mod:'ACC',acc:'$acc',iphash:'".iphash()."'});\">Create '".h($acc)."'?</span>";

	// ����� ����� ������� �������:
	if(empty($IS['login'])) return "� ��� �� ��������� ���� `login` � <span class='ll' onclick=\"majax('login.php',{action:'openid_form'})\">��������</span>";
	if(empty($IS['password'])&&empty($IS['openid'])) return "� ��� �� ��������� ���� `password` � <a href=\"javascript:majax('login.php',{action:'openid_form'})\">��������</a>. ��� �� ���������� ������� ���� �������, ����� ����������� �������� ������?";
	$l=h($IS['login']);
	if(preg_match("/[^a-z0-9\_\-]+/s",$l)) return "� ����� ������ `$l` ���������� ������� (��������� ��������: a-z0-9_-). ��� �� �� ����, ��� ������ ������, �� ��������, �� ����� ������ ���� �� �������������. ���������� ������� ���� � LSO ��� ������� ����� � ������-�� ������� ��������.";
	if(0!=ms("SELECT COUNT(*) FROM `jur` WHERE `acc`='".e($l)."'","_l",0)) return "������� `$l` ��� ��� ������: <a href='".h(acc_link($l))."contents'>".h(acc_link($l))."contents</a>";

	return "������� `".h($IS['login'])."`: <span class='ll' onclick=\"if(confirm('create?'))majax('module.php',{mod:'ACC',acc:'$l',iphash:'".iphash()."'});\">�������?</span>";
}

?>