<?php //���������� (c) ���� ������ http://temapavloff.ru/blog/
include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // ������ JsHttpRequest, ����� autorize

$a=RE('mode');

//����������� ����������� ������ �� ����� ���������
if($a=='get_cats') { 
	$pp = ms("SELECT `name` FROM `dnevnik_cats` ORDER BY `name` ASC","_a");
	$s=''; foreach($pp as $p) { $s.="<a style=\"margin: 4px;\" href=".$httphost."blog?cat=".urlencode($p["name"]).">".$p["name"]."</a> "; }
	$man=($admin?"<p style=\'margin-top: 10px;\' align=\'right\'><span style=\'font-size: 8pt;\'  class=scr onclick=\"majax(\'cats.php\',{mode:\'manage\'})\">����������</span></p>":'');
	$s="<fieldset><legend>����������</legend>".$s.$man."</fieldset>";
	otprav("helps('cats', '".$s."');");
}

if(isset($_REQUEST['onload'])) otprav(''); // ��� ���������� ����� ����� ��������� ��� GET-�������

//��������� ����� �� �������� (��� ��������� �������)
if($a=='search_cats') {
	if($_REQUEST["text"]=='') otprav("zabil('c_popup',''); zakryl('c_popup');");
	$pp = ms("SELECT `name` FROM `dnevnik_cats` ".WHERE("`name` LIKE '".e(RE("text"))."%'")." ORDER BY `name` DESC LIMIT 0,20","_a");
	$s=''; foreach($pp as $p) { $s.="<li onclick=\"idd('".$_REQUEST["idhelp"]."_cat').value=this.innerHTML;\">".$p["name"]."</li>"; }
	$s="<ul class=cat_its>$s</ul>";
	otprav("zabil('c_popup','".e($s)."'); otkryl('c_popup');");
}

//���������� ���������
if($a=='manage') { AD(); otprav("helps('cats', '".manage()."');"); }

if($a=='del') { AD();
	msq_update('dnevnik_zapisi',array('cat'=>''),"WHERE `cat`='".e(RE("name"))."'");
	msq("DELETE FROM `dnevnik_cats` WHERE `name`='".e(RE("name"))."'");
	otprav("helps('cats', '".manage()."');");
}

function manage() { global $www_design;
	$pp = ms("SELECT `name` FROM `dnevnik_cats` ORDER BY `name` ASC","_a");
	$s=''; foreach ($pp as $p) { $s.="<span style=\"margin: 4px;\">".$p["name"]."<img src=\"".$www_design."e3/remove.png\" style=\"cursor:pointer;\" onclick=\"if(confirm(\'����� �������?\')) {majax(\'cats.php\',{mode:\'del\',name:\'".$p["name"]."\'})}\"></span>"; }
	return "<fieldset><legend>����������</legend>$s</fieldset>";
}
?> 
