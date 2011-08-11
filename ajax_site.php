<?php
include "config.php";
include $include_sys."_autorize.php";
if(!$admin) die('Error autorize');
// include $include_sys."_msq.php";
// include $include_sys."_onecomment.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

function otprav2($s) { global $_RESULT,$msqe; $_RESULT["otvet"] = $msqe.$s; $_RESULT["status"] = true; exit(); }

$a=$_REQUEST["action"];
$id=intval($_REQUEST["id"]);


function saveit() { global $db_site,$id; // $_REQUEST; // ?
	$ara=array(
		'name'=>e($_REQUEST["name"]),
		'text'=>e($_REQUEST["text"]),
		'type'=>e($_REQUEST["type"]),
		'Access'=>e($_REQUEST["Access"])
	);
	if($id)	msq_update($db_site,$ara,"WHERE `id`=".$id); else msq_add($db_site,$ara);
}



if($a == "new") { edit_form(0,$_REQUEST['name'],$_REQUEST['text'],$_REQUEST['type'],$_REQUEST['Access']); } // нова€

if($a == "send_edit") { // сохранить изменени€ и закрыть
	$_RESULT["reload"]=true;
	saveit();
	$a="close";
}

if($a == "save_edit") { // сохранить изменени€, но не закрывать
	saveit();
	$a="view";
}

if($a == "view") {
	$p=ms("SELECT * FROM `".$db_site."` WHERE `id`='".$id."'","_1",0);
	edit_form($id,$p['name'],$p['text'],$p['type'],$p['Access']);
}

if($a == "close") {
	if($id)	{ $p=ms("SELECT * FROM `".$db_site."` WHERE `id`='".$id."'","_1",0); $name=$p['name']; }
	else { $name="создать новую"; }

	if($p['type']!='photo') {
otprav2("<li><a href=\"javascript:l('".$id."')\">".h($name)."</a>"
// ."&nbsp;(<a href=".$GLOBALS['wwwhost'].h($name)." target=_blank>open</a>)</li>"
);
	} else {

otprav2("<li><table><tr valign=center>
<td><img src=".$GLOBALS['foto_www_preview'].$p['text']."></td>
<td><a href=\"javascript:l('".$id."')\">".(strlen($name)?$name:"&lt;...&gt;")."</a></td>
</tr></table></li>");
$_RESULT["reload"]=false;

	}

}

if($a == "delete") { msq_del($db_site,array('id'=>$id)); otprav2(""); }

exit;



function edit_form($id,$name,$text,$type,$Access) {

// enum('page', 'design', 'news', 'pageplain', 'photo')

	$id_text=$id."t";
	$id_name=$id."n";
	$id_type=$id."y";
	$id_access=$id."a";

	$text=htmlspecialchars($text);

$jsvalue=",document.getElementById('".$id_name."').value,"
."document.getElementById('".$id_text."').value,"
."document.getElementById('".$id_type."').value,"
."document.getElementById('".$id_access."').value)";

otprav2("<li><a href=\"javascript:ajax_site('close',".$id.")\">закрыть</a>
<p><center>
<table><tr>
".($type!='photo'?'':"<td><img src=".$GLOBALS['foto_www_preview'].$text."></td>")."
<td><input id='".$id_name."' type=text class='t' size=32 value='".htmlspecialchars($name)."'>
".selecto($id_type,htmlspecialchars($type),array(
	'page'=>"page: загружаема€ страница",
	'design'=>"design: элемент дизайна",
	'news'=>"news: новость",
	'photo'=>"photo: фотографи€",
),'id')."
доступ: ".selecto($id_access,htmlspecialchars($Access),array(
	'all'=>"всем",
	'podzamok'=>"избранным",
	'admin'=>"никому"
),'id')."


<br><TEXTAREA id='".$id_text."' class='t' cols='80' rows='".max(page($text,80),3)."'>".$text."</TEXTAREA></td>
<td valign=top><a title='”далить запись' href=\"javascript:if(confirm('“очно удалить?')) ajax_site('delete',".$id.")\">del</a>
<p><input title='—охранить изменени€, но не закрывать' value='SEND >>' class='t' onclick=\"javascript:ajax_site('send_edit',".$id.$jsvalue."\" type='button'>
".($id?"<p><input title='—охранить и выйти' value='SAVE >>' class='t' onclick=\"javascript:ajax_site('save_edit',".$id.$jsvalue."\" type='button'>":'')."
</td></tr></table>
</li>");
}

?>
