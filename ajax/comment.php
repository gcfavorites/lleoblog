<?php // ����������� �������������
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

// if($admin) idie('���������� ������ - ������ ��� ���������� �����!');

$erorrs=array();

$a=$_REQUEST["a"];

$comnu=intval($_REQUEST["comnu"]); $idhelp='cm'.$comnu;
$id=intval($_REQUEST["id"]); // if(!$id and $_REQUEST["id"]!='000') $erorrs[]="��������� ����.";
$lev=intval($_REQUEST["lev"]);
$dat=intval($_REQUEST["dat"]); // if(!$dat and $id) $erorrs[]="��������� ����.";

include $include_sys."_onecomm.php";

//========================================================================================================================
if($a=='loadpanel') { $idhelp=$_REQUEST['idhelp'];
        $id=$idhelp."_textarea"; include($file_template."panel_comment.php");
        otprav("idd('".h($idhelp."p")."').innerHTML='".njs($panel)."'; loadScript('pins.js'); idd('".$id."').focus();");
}
//========================================================================================================================
if($a=='loadcomments') {
	$art=ms("SELECT `Comment`,`Comment_write`,`Comment_tree`,`num` FROM `dnevnik_zapisi` WHERE `num`='".e($dat)."'","_1");

$s="
	// loadCSS('commentstyle.css');

	kus=function(unic) { if(unic) majax('login.php',{action:'getinfo',unic:unic}); };
	kd=function(e) { if(confirm('����� �������?')) majax('comment.php',{a:'del',id:ecom(e).id}); };
	ked=function(e) { majax('comment.php',{a:'edit',id:ecom(e).id}); };
	ksc=function(e) { majax('comment.php',{a:'scr',id:ecom(e).id}); };
	rul=function(e) { majax('comment.php',{a:'rul',id:ecom(e).id}); };
	ka=function(e) { e=ecom(e); majax('comment.php',{a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); };
	kpl=function(e) { majax('comment.php',{a:'plus',id:ecom(e).id}); };
	kmi=function(e) { majax('comment.php',{a:'minus',id:ecom(e).id}); };
	opc=function(e) { e=ecom(e); majax('comment.php',{a:'pokazat',oid:e.id,lev:e.style.marginLeft,comnu:comnum}); };
	ecom=function(e) { while( ( e.id == '' || e.id == undefined ) && e.parentNode != undefined) e=e.parentNode; if(e.id == undefined) return 0; return e; };

	idd(0).innerHTML=\"".njs(load_comments($art))."\";";

//cache_rm(comment_cachename($i));

	otprav($s);
}
//========================================================================================================================
if($a=='pokazat') { // ��������
	$oid=$_REQUEST["oid"]; $id=intval(substr($oid,1));
	if(substr($oid,0,1)!='o' or !$id) oalert("WTF?!");
	$pp=ms("SELECT * FROM `dnevnik_comm` WHERE `Parent`='$id'","_a");
	$r="clean('$oid');";
	foreach($pp as $p) { $pid=$p['id'];
	$r.="
	mkdiv2($pid,\"".njs(comment_one($p,getmojno_comm($p['DateID'])))."\", '".commclass($p)."', idd(0), idd($id));
	idd($pid).style.marginLeft='".$lev."px';
	otkryl($pid);";

	if(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`='$pid'","_l")) $r.="
		mkdiv2('o$pid','', 'opc', idd(0), idd($pid));
		idd('o$pid').style.marginLeft='".($lev+25)."px';
		addEvent(idd('o$pid'),'click', function(){ opc(this) } );
		otkryl('o$pid');";
        }
otprav($r);
}

//========================================================================================================================
if($a=='plus') { // ������
	if(!$unic) otprav("� � ��� ������ ���� ���������?");
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($p['unic']==$unic) idie("�� ������� �� ������������?");
	$e=mysql_query("INSERT INTO `dnevnik_plusiki` (`commentID`,`unic`,`var`) VALUES ($id,$unic,'plus')"); if(!$e) otprav("");
	mysql_query("UPDATE `dnevnik_comm` SET `golos_plu`=`golos_plu`+1 WHERE `id`='$id'");
	$p['golos_plu']++;
	otprav_comment($p);
}
//========================================================================================================================
if($a=='minus') { // �������
	if(!$unic) otprav("� � ��� ������ ���� ���������?");
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($p['unic']==$unic) idie("�� ������� �� ������������?");
	$e=mysql_query("INSERT INTO `dnevnik_plusiki` (`commentID`,`unic`,`var`) VALUES ($id,$unic,'minus')"); if(!$e) otprav("");
	mysql_query("UPDATE `dnevnik_comm` SET `golos_min`=`golos_min`+1 WHERE `id`='$id'");
	$p['golos_min']++;
	otprav_comment($p);
}
//========================================================================================================================
if($a=='editsend') { // ������� ����������������� �����������

	$p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($dat===false) oalert("� ������ ����������� ���.");
	if(!$admin and $p['unic']!=$unic) oalert("����� ����������� �������������?");
	
	$text=$_REQUEST["text"]; $text=trim($text,"\n\r\t "); $text=str_replace("\r","",$text);
	if($text==$p['Text']) otprav("clean('$idhelp');");

	msq_update('dnevnik_comm',array('Text'=>e($text)),"WHERE `id`='$id'");
	$p['Text']=$text;
	otprav_comment($p,"clean('$idhelp');");
}
//========================================================================================================================
if($a=='del') { // id ������� �����������
	if(!$admin) otprav("clean($id);");
	cache_rm(comment_cachename(ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`='$id'","_l")));
	otprav( del_comm($id) );
}
//========================================================================================================================
if($a=='edit') { // id ������������� �����������

	$p=ms("SELECT `unic`,`Text`,`Time`,`Name` FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($dat===false) oalert("��������� ����.");

	if(!$admin) { // ��������� �� �������������?
		if($unic != $p['unic']) idie("������������� ����� �����������? �����������!");
		if(time()-$p['Time'] > 15*60) idie("������������� ����� ������ � ������� 15 �����.");
		if(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`=$id","_l",0)) idie("������������� ������ - ��� ���� ������.");
	}

//if(!$admin) oalert("������������� ���� ������.");

// onsubmit='cmsend_edit(this,".$comnu.",".$id.");
// majax(\'comment.php\',{a:\'editsend\',this.txt.value,".$comnu.",".$id."});
// cmsend_edit(this,".$comnu.",".$id.");
$s="<form name='sendcomment_".$comnu."' onsubmit='cmsend_edit(this,".$comnu.",".$id."); return false;'><div id='co_$comnu'></div>
<textarea id='textarea_".$comnu."' style='border: 1px dotted #ccc; margin: 0; padding: 0;' name='txt' cols=50 rows=".page(h($p['Text']),50).">".h(str_replace("\n",'\\n',$p['Text']))."</textarea>
<div><input type=submit value='send'></div>
</form>";

$s="comnum++; helps('".$idhelp."',\"<fieldset id='commentform_".$comnu."'><legend>"
.($admin?h($p['Name']):"��������������, �������� <span id='tiktik_".$comnu."'>".(time()-$p['Time'])."</span> ������")
."</legend>".$s."</fieldset>\"); idd('textarea_".$comnu."').focus();";

otprav_sb('commentform.js',$s);
}

//========================================================================================================================
if($a=='ans') { // id ������-��������
	if(!$admin) idie("�� �����");
	$p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($dat===false) oalert("� ������ ����������� ���.");
	$p['ans']=($p['ans']=='u'?'0':($p['ans']=='0'?'1':'u'));
	msq_update('dnevnik_comm',array('ans'=>$p['ans']),"WHERE `id`='$id'");
	otprav_comment($p,"idd($id).className='".commclass($p)."';");
}

//========================================================================================================================
if($a=='scr') { // id ������-��������
//	if(!$admin) oalert("�� �����");
	if(!$podzamok) idie("�� ��������");
	$p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($dat===false) oalert("� ������ ����������� ���.");
	$p['scr']=($p['scr']==1?0:1);
	msq_update('dnevnik_comm',array('scr'=>$p['scr']),"WHERE `id`='$id'");
	otprav_comment($p,"idd($id).className='".commclass($p)."';");
}

//========================================================================================================================
if($a=='rul') { // id ������-��������
	if(!$admin) idie("�� �����");
	$p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($dat===false) oalert("� ������ ����������� ���.");
	$p['rul']=($p['rul']==1?0:1);
	msq_update('dnevnik_comm',array('rul'=>$p['rul']),"WHERE `id`='$id'");
	otprav_comment($p,"idd($id).className='".commclass($p)."';");
}

//========================================================================================================================
// if($a=='editpanel') { 	otprav("alert('$a')");	// otprav_sb('commentform.js',$s); }
//========================================================================================================================

if($a=='comsend') {


$text=$_REQUEST["text"]; $text=trim($text,"\n\r\t "); $text=str_replace("\r","",$text); if($text=='')  $erorrs[]="� ��� �� �����������?";
$name=($IS['user']!=''&&$IS['user_noname']!='noname'?$IS['user']:$_REQUEST["name"]); if($name=='') $erorrs[]="�� ������ �����������.";
$mail=mail_validate($_REQUEST["mail"]);

if(!sizeof($erorrs)) {

	$ara_kartochka=array(); // ���� ����� ��������� ������ � ��������

// ============ ���� ����� �������� ����� ==============
if($IS['capcha']!='yes') {
	if($_REQUEST['capcha']=='') otprav_error("������� ����� � �������� � ��������.");

	include_once $GLOBALS['include_sys']."_antibot.php";
        if(!antibot_check($_REQUEST['capcha'],$_REQUEST['capcha_hash'])) otprav_error("�������� ����� � ��������, ���������!",
"zabil('ozcapcha_".$comnu."',\"".njs("<table><tr valign=center><td>
<input maxlength=".$GLOBALS['antibot_C']." class='capcha' type=text name='capcha'>
<input type=hidden name='capcha_hash' value='".antibot_make()."'></td><td>".antibot_img()."</td></tr></table>")."\");");
	else $ara_kartochka['capcha']='yes'; // �������� � ����, ��� ����� ����
}
// ============ / ���� ����� �������� ����� ==============

	$scr=0;
	include_once $GLOBALS['include_sys']."spamoborona.php";

	$c=ms("SELECT `Comment_screen` FROM `dnevnik_zapisi` WHERE `num`='$dat'","_l");
	if($c=='screen' or (!$podzamok && $c=='friends-open')) $scr=1;

	$ara=array(
		'Text'=>e($text),
		'Mail'=>e(($mail!=''?$mail:$IS['mail'])),
		'Name'=>e($name),
		'group'=>($admin?1:0),
		'IPN'=>e($GLOBALS['IPN']),
		'BRO'=>e($GLOBALS['BRO']),
// 'whois'
		'DateID'=>$dat,
		'unic'=>e($GLOBALS['unic']),
		'Time'=>time(),
		'scr'=>e($scr),
		'Parent'=>$id );

	// ================ ��������� ������ =============================
	if($GLOBALS['admin'] and $id!=0 ) { include_once $GLOBALS['include_sys']."_mail_answer.php"; mail_answer($id,$ara); }
	// ===============================================================

// � ����� �� �� ����� ���������� ���� ����?
	$ans=($id==0?'u':ms("SELECT `ans` FROM `dnevnik_comm` WHERE `id`='$id'","_l"));
	if(!$admin and $ans=='0') idie('����� �������� �������� �� ���� �����������.');
	if($ans=='u') {	$e=getmojno_comm($dat);
		if(!$admin and $e===false) idie('� ���� ������� �������� ������.');
		if(!$admin and $e=='root' and $id!=0) idie('� ���� ������� ��������� �����������, �� �� ������ �� ���.');
	}
// ------------------------------------------

	msq_add('dnevnik_comm',$ara); $newid=mysql_insert_id();

	$p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='".intval($newid)."'","_1",0);
	$c=njs(comment_one($p,getmojno_comm($p['DateID'])));

// ================= ��������� ������ � �������� =================
	if($IS['realname']=='') $ara_kartochka['realname']=e($name);
	if($mail!='' && $IS['mail']=='') $ara_kartochka['mail']=e($mail);
	if(sizeof($ara_kartochka)) msq_update($GLOBALS['db_unic'],$ara_kartochka,"WHERE `id`='$unic'");
// ================= ��������� ������ � �������� =================

	cache_rm(comment_cachename($dat));

otprav("clean('$idhelp');
".($id?"mkdiv2($newid,\"$c\",'".commclass($p)."',idd(0),idd($id));":"mkdiv($newid,\"$c\",'".commclass($p)."',idd(0));")."
idd($newid).style.marginLeft='".($lev+25)."px';
idd($newid).name='$newid';
otkryl($newid);
".(!$id?"window.location=mypage.replace(/#[^#]+$/g,'')+'#$newid';":"")."
"

// .($GLOBALS['admin']?"alert('�������� ��� ������� #".$dat." = ".comment_cachename($dat)."'); ":'')
);


} else { otprav_error(implode('<br>',$erorrs)); }

}

//=================================== ��������� ����� ===================================================================

if($a=='comform') { // {a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������

if($dat==0) $dat=ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`='$id'","_l",0); if($dat===false) oalert("��������� ����.");

$s="<form name='sendcomment' onsubmit='cmsend(this,".$comnu.",".$id.",".$dat.",".$lev."); return false;'><div id='co_$comnu'></div>";

$s.= "<div><div class=l1>"
.($IS['user']!=''&&$IS['user_noname']!='noname'?$imgicourl:"���: <input name='nam' class='in' type='text'>")."

<div id='".$idhelp."p' style='display:inline; margin-left: 3px;'><img class=l onclick='majax(\\\"comment.php\\\",{a:\\\"loadpanel\\\",idhelp:\\\"".$idhelp."\\\"})' src='".$www_design."e3/finish.png' alt='panel'></div>

</div><div class=l2>"
.($IS['mail']!=''?"<acronym title='������ ������ �� ".h($IS['mail'])."'><img src='".$www_design."e2/mail.png' align=right></acronym>"
:"mail: <input name='mail' class=in type=text onkeyup='this.value=cm_mail_validate(this)'>"
)."</div>
<br class=q /></div>";

if($IS['capcha']!='yes') { include_once $include_sys."_antibot.php";
$s.="<div><div class=l1>�� ������� �� �����<br>�����������, ��� �� �� �����:</div>
<div class=l2 id='ozcapcha_$comnu'><table><tr valign=center><td><input maxlength=".$GLOBALS['antibot_C']." class='capcha'
type=text name='capcha'><input type=hidden name='capcha_hash' value='".antibot_make()."'></td>
<td>".antibot_img()."</td></tr></table></div><br class=q /></div>";
}



//$s.="<div><textarea name='txt' id='".$idhelp."_textarea' class='textar'></textarea></div>"
$s.="<div><textarea name='txt' id='".$idhelp."_textarea' style='border: 1px dotted #ccc; margin: 0; padding: 0;' cols=70 rows=15></textarea></div>"
."<div class=l0><input type=submit value='send'></div></form>";

$s="comnum++; helps('".$idhelp."',\"<fieldset id='commentform'><legend>�����������</legend>".$s."</fieldset>\"); idd('".$idhelp."_textarea').focus();";

otprav_sb('commentform.js',$s);

}

//=================================== ������� ����������� ===================================================================

function del_comm($id,$l=1) { if(!$id && !$GLOBALS['admin']) return " alert('id=0?!');";

	if($l and ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`='$id'","_l",0) ) { // ���� � ���� ���� ������� - ������ ��������

		msq_update('dnevnik_comm',array(
			'Time'=>0,
			'unic'=>0,
			'Name'=>'',
			'Mail'=>'',
			'Text'=>'',
			'IPN'=>0,
			'BRO'=>'',
			'whois'=>'',
			'rul'=>'no',
			'ans'=>'disable',
			'golos_plu'=>0,
			'golos_min'=>0 ),"WHERE `id`='$id'");

		return " idd($id).innerHTML=''; idd($id).className='cdel';";
	}

	// ����� ������� ������
	
	$Parent=ms("SELECT `Parent` FROM `dnevnik_comm` WHERE `id`='$id'","_l",0); // ������ ��������� �������
	// ����� �������
	ms("DELETE FROM `dnevnik_comm` WHERE `id`='$id'","_l",0);

	$r=" clean($id);";

	if( ! $Parent // ���� �� ��� � �����
		or ms("SELECT `Time` FROM `dnevnik_comm` WHERE `id`='$Parent'","_l",0) // ��� ��� ������� �� ������
		or ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`='$Parent'","_l",0) // ���� � �������� ���� ������ �������
	) return $r; // �� ������ ��������� � ������� ����

	return $r.del_comm($Parent,0); // ����� ��������� �������� � ���
}


function otprav_comment($p,$r='') {
	cache_rm(comment_cachename($p['DateID'])); // �������� ��� �������� ���� ������
//	if($GLOBALS['admin']) $r.=" alert('�������� ��� ������� #".$p['DateID']." = ".comment_cachename($p['DateID'])."');";
	otprav("idd(".$p['id'].").innerHTML=\"".njs(comment_one($p, getmojno_comm($p['DateID']) ))."\"; ".$r);
}

function getmojno_comm($num) {
	$p=ms("SELECT `Comment`,`Comment_write`,`Comment_tree`,`DateDatetime`,`num` FROM `dnevnik_zapisi` WHERE `num`='".e($num)."'","_1");
	$p['counter']=get_counter($p);
	return mojno_comment($p);
}

function otprav_error($s,$p='') { global $comnu; otprav("zabil('co_".$comnu."',\"<div class=e>".njs($s)."</div>\");".$p); }
function otprav_sb($scr,$s) { global $_RESULT,$msqe; $_RESULT["modo"] = ScriptBefore($scr,$msqe.$s); $_RESULT["status"] = true; exit; }
function ScriptBefore($script,$run) { return "loadScriptBefore('$script',\"".njs($run)."\");"; }
function prejs($s) { return str_replace(array("&","\\","'",'"',"\n","\r"),array("&amp;","\\\\","\\'",'\\"',"\\n",""),$s); }

?>