<?php
include "config.php";
include $include_sys."_autorize.php";
if(!$admin) idie('Error autorize');
// include $include_sys."_msq.php";
// include $include_sys."_onecomment.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

function otprav2($s) { global $_RESULT,$msqe; $_RESULT["otvet"] = $msqe.$s; $_RESULT["status"] = true; exit(); }

$a=RE("action");
$id=RE0("id");

function saveit() { global $db_site,$id; // $_REQUEST; // ?
	$ara=array(
		'name'=>e(RE("name")),
		'text'=>e(RE("text")),
		'type'=>e(RE("type")),
		'Access'=>e(RE("Access"))
	);
	if($id)	msq_update($db_site,$ara,"WHERE `id`=".$id); else msq_add($db_site,$ara);
}

if($a == "new") { edit_form(0,RE('name'),RE('text'),RE('type'),RE('Access')); } // �����

if($a == "send_edit") { // ��������� ��������� � �������
	$_RESULT["reload"]=true;
	saveit();
	$a="close";
}

if($a == "save_edit") { // ��������� ���������, �� �� ���������
	saveit();
	$a="view";
}

if($a == "view") {
	$p=ms("SELECT * FROM `".$db_site."` WHERE `id`='".$id."'","_1",0);
	edit_form($id,$p['name'],$p['text'],$p['type'],$p['Access']);
}

if($a == "close") {
	if($id)	{ $p=ms("SELECT * FROM `".$db_site."` WHERE `id`='".$id."'","_1",0); $name=$p['name']; }
	else { $name="������� �����"; }
	otprav2("<li><a href=\"javascript:l('".$id."')\">".h($name)."</a>"
	.($p['type']=='page'?"&nbsp;(<a href=".$GLOBALS['wwwhost'].h($name)." target=_blank>open</a>)</li>":''));
}

if($a == "delete") { msq_del($db_site,array('id'=>$id)); otprav2(""); }

exit;


function edit_form($id,$name,$text,$type,$Access) {

// enum('page', 'design', 'news', 'pageplain', 'photo')

	$id_text=$id."t";
	$id_name=$id."n";
	$id_type=$id."y";
	$id_access=$id."a";

	$text=h($text);

$jsvalue=",idd('$id_name').value,idd('$id_text').value,idd('$id_type').value,idd('$id_access').value)";

otprav2("<li><a href=\"javascript:ajax_site('close',".$id.")\">�������</a>
<p><center>
<table><tr>
".($type!='photo'?'':"<td><img src=".$GLOBALS['foto_www_preview'].$text."></td>")."
<td><input id='".$id_name."' type=text class='t' size=32 value='".h($name)."'>
".selecto($id_type,h($type),array(
	'page'=>"page: ����������� ��������",
	'design'=>"design: ������� �������",
	'news'=>"news: �������",
	'photo'=>"photo: ����������",
),'id')."
������: ".selecto($id_access,h($Access),array(
	'all'=>"����",
	'podzamok'=>"���������",
	'admin'=>"������"
),'id')."


<br><TEXTAREA id='".$id_text."' class='t' cols='80' rows='".max(page($text,80),3)."'>".$text."</TEXTAREA></td>
<td valign=top><a title='������� ������' href=\"javascript:if(confirm('����� �������?')) ajax_site('delete',".$id.")\">del</a>
<p><input title='��������� ���������, �� �� ���������' value='SEND >>' class='t' onclick=\"javascript:ajax_site('send_edit',".$id.$jsvalue."\" type='button'>
".($id?"<p><input title='��������� � �����' value='SAVE >>' class='t' onclick=\"javascript:ajax_site('save_edit',".$id.$jsvalue."\" type='button'>":'')."
</td></tr></table>
</li>");
}

?>