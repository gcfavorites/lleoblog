<?php // ����������� �������������
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

if(!$admin) idie("���� ������������ �������!");


$autosave_count = 200; // 128; // ����� ������� ������� ������ ����������

$num=intval($_REQUEST["num"]); $idhelp='editor'.$num;
$a=$_REQUEST["a"];



// === test ===
if($a=='test') {

/*
$s='';
	if(count($_FILES)>0) foreach($_FILES as $FILE) if(is_uploaded_file($FILE["tmp_name"])) {

        	$fname=h($FILE["name"]);
		$s.="<p> LOADED: $fname";

	} else { $s.=print_r($_FILES,1); }
*/

	idie(nl2br(h(print_r($_FILES,1))));

}



//=================================== editpanel ===================================================================
if($a=='foto') {

// <script>onload = function() { tree("root") }</script>
// <p>My photo <span onclick='tree(\"root\")'>albums</span>:

$s="<div id='ooo'></div>
<ul class='Container' id='root'>
  <li class='Node IsRoot IsLast ExpandClosed'>
    <div class='Expand'></div>
    <div class='Content'>photo</div>
    <ul class='Container'>
    </ul>
  </li>
</ul>
";

otprav(	"
	loadScript('tree.js');
	loadCSS('tree.css');
	helps('foto',\"<fieldset id='commentform'><legend>����������</legend><div  style='width: 750px'>".njs($s)."</div></fieldset>\");
	tree('root');
");

}

//=================================== editpanel ===================================================================
if($a=='loadpanel') { $idhelp=$_REQUEST['idhelp'];
	$id=$idhelp."_textarea"; include($file_template."panel_editor.php");
	otprav("idd('".h($idhelp."p")."').innerHTML='".njs($panel)."'; loadScript('pins.js'); idd('".$id."').focus();");
}
//=================================== move ===================================================================
if($a=='savemove') { // $Date=$_REQUEST['DateOld']; $idhelp='move';

	$New=$_REQUEST['DateNew'];
	$Old=$_REQUEST['DateOld'];
	if($New=='' or $Old=='') idie("�������� ����!");
	if($New==$Old) idie("����������?");

	if(intval("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($_REQUEST['DateNew'])."'","_l",0))
		idie("������� � ����� ".h($New)." ��� ����������!");

	$t=getmaketime($New);
	if($admin) msq_update('dnevnik_zapisi',array('Date'=>e($New),'DateUpdate'=>time(),'DateDate'=>$t[0],'DateDatetime'=>$t[1] ),"WHERE `Date`='".e($Old)."'");

	redirect(get_link($Date)); // �� ��� � �������
}

// ===================

if($a=='move') { $Date=$_REQUEST['Date'];

$s="<input type='hidden' id='move_DateOld' name='DateOld' value='".h($Date)."'><span style='border: 1px dotted #ccc'>".h($Date)."</span>
&mdash; <input class=t type='text' id='move_DateNew' name='DateNew' value='".h($Date)."' maxlength='128' size='20'>
<input type=submit value='Move' onclick=\"majax('editor.php',{a:'savemove',DateOld:idd('move_DateOld').value,DateNew:idd('move_DateNew').value})\">";

$s="
	helps('move',\"<fieldset id='commentform'><legend>������� ������� ".h($p['Date'])."</legend>".njsn($s)."</fieldset>\");
	idd('move_DateNew').focus();
";

otprav($s);
}

//=================================== ����� ������� ===================================================================
if($a=='savenew') {

$Date=$_REQUEST['Date']; if($Date=='') idie("������: �������� ����.");

$t=getmaketime($Date);

$ara=array(
	'Date'		=> e($Date),
'DateDate'=>$t[0],
'DateDatetime'=>$t[1],
	'Header'	=> e($_REQUEST['Header']),       	
	'Body'		=> e($_REQUEST['Body']),
	'Access'	=> e($_REQUEST['Access']),
	'autoformat'	=> e($_REQUEST['autoformat']),
	'autokaw'	=> e($_REQUEST['autokaw']),
	'template'	=> e(($_REQUEST['template']!=''?$_REQUEST['template']:'blog')),
	'Comment_view'	=> e($_REQUEST['Comment_view']),
	'Comment_write'	=> e($_REQUEST['Comment_write']),
	'Comment_screen'=> e($_REQUEST['Comment_screen']),
	'DateUpdate'=>time()
);

if(intval(ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".$ara['Date']."'","_l",0))) idie("������ � ���� ��������� ��� ����!");

if($admin) msq_add('dnevnik_zapisi',$ara);

redirect(get_link($Date)); // �� ��� � �������
}
//=================================== ����� ������� ===================================================================
if($a=='newform') {

$Date=date("Y/m/d"); $i=0;
while(ms("SELECT COUNT(*) FROM `dnevnik_zapisi` WHERE `Date`='".e($Date)."'","_l",0)!=0) { $Date=date("Y/m/d").'_'.sprintf("%02d", ++$i); }
$idhelp=$Date;

$p=unserialize(file_get_contents($GLOBALS['hosttmp'].'zapisi.set')); // ����� ��������� �� ���������

// idie('---'.nl2br(h(print_r($p,1))));

$s="
<input class=t type='text' id='".$idhelp."_Date' name='Date' value='".h($Date)."' maxlength='128' size='20'>

<div id='".$idhelp."p' style='display:inline'><img class=l onclick=\"majax('editor.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'></div>

<br><input class=t type='text' id='".$idhelp."_Header' name='Header' value='".h($p["Header"])."' maxlength='255' size=".$GLOBALS['editor_cols']."> <span class=br>".strlen($p['Body'])." ����</span>
<br><textarea onkeydown=\"keydownc('Body',this.value,$num)\" class=t id='".$idhelp."_textarea' cols=".$GLOBALS['editor_cols']." rows=".$GLOBALS['editor_rows'].">".h($p["Body"])."</textarea>

<div class=r>������: ".selecto('Access',$p['Access'],array('admin'=>"������",'podzamok'=>"��������",'all'=>"����"),"class=r id='".$idhelp."_Access' name")."
����������: ".selecto('autoformat',$p['autoformat'],array('p'=>"p/br",'no'=>"���",'pd'=>"class=pd"),"class=r id='".$idhelp."_autoformat' name")."
�� ������ �������: <input type=checkbox id='".$idhelp."_autokaw' name=autokaw".($p["autokaw"]=='no'?" checked":"").">";

// �������� � �������
$inc=glob($filehost."template/*.html"); $ainc=array(''=>'- ��� -'); foreach($inc as $l) { $l=preg_replace("/^.*?\/([^\/]+)\.html$/si","$1",$l); $ainc[$l]=$l; }
$s.="template: ".selecto('template',$p['template'],$ainc,"class=r id='".$idhelp."_template' name");

// ����� ����� ���������
if(strstr(file_get_contents($filehost."template/".$p['template'].".html"),'{_COMENTS:')) 
$s.="<br>
����������� ����������: ".selecto('Comment_view',$p['Comment_view'],array('timeload'=>"��������",'load'=>"������",'off'=>"���",'rul'=>"������",'on'=>"���"),
"class=r id='".$idhelp."_Comment_view' name")."
���������: ".selecto('Comment_write',$p['Comment_write'],array('timeoff'=>"��������",'on'=>"����� �� ����",'off'=>"���",'friends-only'=>"����� �� ������",'login-only'=>"����� �� �������",'login-only-timeoff'=>"�������� �� �������"),
"class=r id='".$idhelp."_Comment_write' name")."
���������: ".selecto('Comment_screen',$p['Comment_screen'],array('open'=>"����",'friens-open'=>"������",'screen'=>"��������"),
"class=r id='".$idhelp."_Comment_screen' name")."
���: ".selecto('Comment_tree',$p['Comment_tree'],array('1'=>"�����",'0'=>"��������"),
"class=r onchange='ch_edit_pole(this,$num)' name")."

<br><input type=submit value='Save' onclick=\"edit_savenew()\">";

// ����������: ".selecto('comments_order',$p['comments_order'],array('normal'=>"���",'allrating'=>"�������",'rating'=>"�����") )."

$s="edit_savenew=function(){ majax('editor.php',{a:'savenew',idhelp:'".$idhelp."',"
."Date:idd('".$idhelp."_Date').value,"
."Header:idd('".$idhelp."_Header').value,"
."Body:idd('".$idhelp."_textarea').value,"
."Access:idd('".$idhelp."_Access').value,"
."autoformat:idd('".$idhelp."_autoformat').value,"
."autokaw:idd('".$idhelp."_autokaw').value,"
."template:idd('".$idhelp."_template').value,"
."Comment_view:idd('".$idhelp."_Comment_view').value,"
."Comment_write:idd('".$idhelp."_Comment_write').value,"
."Comment_screen:idd('".$idhelp."_Comment_screen').value}); };
helps('".$idhelp."',\"<fieldset id='commentform'><legend>����� ������� ".h($p['Date'])."</legend>".njsn($s)."</fieldset>\");
idd('".$idhelp."_textarea').focus();
";

if(isset($_REQUEST["clo"])) $s="clean('".h($_REQUEST["clo"])."');".$s;

otprav($s);

}

//=================================== ��������� ����� ===================================================================
if($a=='editform') {

$p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `num`='$num'","_1",0); if($p===false) idie("����������� ������� #$num");

$s="
<img class=l onclick=\"majax('editor.php',{a:'newform',clo:'".$idhelp."'})\" src='".$www_design."e3/filenew.png' alt='new'>
<img class=l onclick=\"majax('editor.php',{a:'move',Date:'".h($p['Date'])."'})\" src='".$www_design."e3/redo.png' alt='move'>
<img class=l onclick=\"if(confirm('�� ����� ������ ������� ��� �������?')) majax('editor.php',{a:'delete',num:$num});\" src='".$www_design."e3/remove.png' alt='delete'>
<div id='".$idhelp."p' style='display:inline'>
<img class=l onclick=\"majax('editor.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'>
".($admin?'':"<font color=red>�� �� �����: � ����-������ ��������� �� ���������.</font>")."
</div>

<br><input onchange='ch_edit_pole(this,$num)' class=t type='text' name='Header' value='".h($p["Header"])."' maxlength='255' size=".$GLOBALS['editor_cols']."> <span class=br>".strlen($p['Body'])." ����</span>
<br><textarea onkeydown=\"keydownc('Body',this.value,$num)\" class=t id='".$idhelp."_textarea' cols=".$GLOBALS['editor_cols']." rows=".$GLOBALS['editor_rows'].">".h($p["Body"])."</textarea>

<div class=r>������: ".selecto('Access',$p['Access'],array('admin'=>"������",'podzamok'=>"��������",'all'=>"����"),"class=r onchange='ch_edit_pole(this,$num)' name")."
����������: ".selecto('autoformat',$p['autoformat'],array('p'=>"p/br",'no'=>"���",'pd'=>"class=pd"),"class=r onchange='ch_edit_pole(this,$num)' name")."
�� ������ �������: <input onchange='edit_polesend(this.name,this.checked,".$num.")' type=checkbox name=autokaw".($p["autokaw"]=='no'?" checked":"").">";

// �������� � �������
$inc=glob($filehost."template/*.html"); $ainc=array(''=>'- ��� -'); foreach($inc as $l) { $l=preg_replace("/^.*?\/([^\/]+)\.html$/si","$1",$l); $ainc[$l]=$l; }
$s.="template: ".selecto('template',$p['template'],$ainc,"class=r onchange='ch_edit_pole(this,$num)' name");

// ����� �� ����� ���������
if(strstr(file_get_contents($filehost."template/".$p['template'].".html"),'{_COMENTS:')) $s.="<br>
����������� ����������: ".selecto('Comment_view',$p['Comment_view'],array('timeload'=>"��������",'load'=>"������",'off'=>"���",'rul'=>"������",'on'=>"���"),
"class=r onchange='ch_edit_pole(this,$num)' name")."
���������: ".selecto('Comment_write',$p['Comment_write'],array('timeoff'=>"��������",'on'=>"����� �� ����",'off'=>"���",'friends-only'=>"����� �� ������",'login-only'=>"����� �� �������",'login-only-timeoff'=>"�������� �� �������"),
"class=r onchange='ch_edit_pole(this,$num)' name")."
���������: ".selecto('Comment_screen',$p['Comment_screen'],array('open'=>"����",'friens-open'=>"������",'screen'=>"��������"),
"class=r onchange='ch_edit_pole(this,$num)' name")."
���: ".selecto('Comment_tree',$p['Comment_tree'],array('1'=>"�����",'0'=>"��������"),
"class=r onchange='ch_edit_pole(this,$num)' name")."

<br><input type=submit value='Save' onclick=\"edit_polesend('Body',idd('".$idhelp."_textarea').value,".$num.")\">";

// ����������: ".selecto('comments_order',$p['comments_order'],array('normal'=>"���",'allrating'=>"�������",'rating'=>"�����") )."

$s="
keydowncount=0;
ch_edit_pole=function(e,num){edit_polesend(e.name,e.value,num,0)};
edit_polesend=function(n,v,num,clo){ majax('editor.php',{a:'polesend',name:n,val:v,num:num,clo:clo}); };
keydownc=function(n,v,num){ keydowncount++; if(keydowncount>".$autosave_count.") { keydowncount=0; edit_polesend(n,v,num,1); } };

helps('".$idhelp."',\"<fieldset id='commentform'><legend>������� ".h($p['Date'])."</legend>".njsn($s)."</fieldset>\");
idd('".$idhelp."_textarea').focus();

//setOpacity(document.body, 0.5);
//setOpacity(idd('".$idhelp."'), 3);
";

$a=array('Date','Header','Body','DateUpdate','view_counter','num','count_comments_open','DateDatetime','DateDate');
foreach($a as $l) unset($p[$l]);
file_put_contents($GLOBALS['hosttmp'].'zapisi.set',serialize($p));

otprav($s);

}


//=================================== ��������� ����� ===================================================================

if($a=='polesend') {

	$name=$_REQUEST["name"];
	$val=$_REQUEST["val"];
	if($name=='template' and $val=='') $val='blog'; // ���� �� ���������

	if($name=='' or $num==0) otprav(''); //idie('�������� ������!');
	if($name=='autokaw') $val=($val=='true'?'auto':'no');

	if($admin) msq_update('dnevnik_zapisi',array(e($name)=>e($val),'DateUpdate'=>time()),"WHERE `num`='$num'");

	if($name=='Header') otprav("idd('$name').innerHTML=\"".njs($val)."\"");
	if($name=='Body') {

		include_once $include_sys."_onetext.php";
		include_once $include_sys."_modules.php";
		$p=ms("SELECT * FROM `dnevnik_zapisi` WHERE `num`='$num'","_1",0); if($p===false) idie("��������� ������� #$num");

		$s=onetext($p);
		$s="idd('$name').innerHTML=\"".njs($s)."\";";
		if($_REQUEST["clo"]==0) $s.="clean('".$idhelp."');";
		otprav($s);
		}
	otprav("");
}

//=================================== �������� ������� ===================================================================

if($a=='delete') {
	if($admin) {
		msq("DELETE FROM `dnevnik_zapisi` WHERE `num`='$num'"); // ������� ������
		msq("DELETE FROM `dnevnik_comm` WHERE `DateID`='$num'"); // ������� � ��� ��� �����������
		msq("DELETE FROM `dnevnik_posetil` WHERE `url`='$num'"); // ������� ���������� �� �����������
		msq("DELETE FROM `dnevnik_link` WHERE `DateID`='$num'"); // ������� ���������� ������� �� �������
		msq("DELETE FROM `dnevnik_search` WHERE `DateID`='$num'"); // ������� ���������� ������� � �����������
	}
	redirect($httphost);
}

?>