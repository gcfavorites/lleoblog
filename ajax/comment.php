<?php // ��������������� �������

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
if(isset($_REQUEST['onload'])) otprav(''); // ��� ���������� ����� ����� ��������� ��� GET-�������
include $include_sys."_autorize.php"; // ������ JsHttpRequest, ����� autorize

// if($admin) idie('���������� ������ - ������ ��� ���������� �����!');

$erorrs=array();

$a=RE('a');


// idie('!!!');


if(!$unic) {
//	logi('unic0.txt',"\n\n".date("Y-m-d H:i:s")."\n\n".print_r($_REQUEST,1));
idie("<b>������ �����������: unic=0</b>

<p>�� ������� �� ���� �����? ����������, ������ �������� ��������,
<br>� ����� ������� ��������� �����������.

<p>���� �� ������� - ������, ��� ������� ������ �� ��������� �����������,
<br>������� ��� �������� ������ ����. ��� ����� ����� ���������? ����������
<br>�������� �������� � ������� �������� ������� ���������: �������� �������
<br>����������� ���� � ������� - ��� ��� �� ��������� �������� �����������.
<br>���������, �� ������ �� �� ���� ���� � ������ �����-�� �������� � �����
<br>��������. ���� �� ���������, ��� ���� ��������� �������� ������ �������,
<br>� ��������, ��� ��� ������ ������, ���������� �������� ���:
<a href=mailto:lleo@aha.ru?subject=Kaganovu_UNIC0_error>lleo@aha.ru</a>
");
}

$id=RE0('id'); // if(!$id and $_REQUEST["id"]!='000') $erorrs[]="��������� ����.";
$comnu=RE0('comnu');
$idhelp='cm'.$comnu;
$lev=RE0('lev');
$dat=RE0('dat'); // if(!$dat and $id) $erorrs[]="��������� ����.";

include $include_sys."_onecomm.php";

//========================================================================================================================
// ������ ������������ ��������� ������������ ����������� � ���������� �� ������������ � ����
if($a=='autosave') { put_last_tmp($_REQUEST['text']); otprav(''); }

//========================================================================================================================
// ��������� ���������� �������������� ��������
if($a=='loadpanel') { $idhelp=h($_REQUEST['idhelp']); $idhelp0=substr($idhelp,0,strlen($idhelp)-1);
        $id=$idhelp0."_textarea"; include($file_template."panel_comment.php");

        otprav("
zabil('".$idhelp."','".njs($panel)."');
idd('".$idhelp."').onclick=function(){return true};
idd('".$id."').focus();");
}

// show_url
if($a=='show_url') { $t=RE('type'); $u=RE('url'); $s='Error media type';
	switch($t) {
	        case 'mp3': include_once $site_mod."MP3.php"; $s=MP3($u); break;
	        case 'youtub': include_once $site_mod."YOUTUB.php"; $s=YOUTUB($u.",480,385,autoplay"); break;
	        case 'img': $s='<img src="'.$u.'" hspace="10">'; break;
        }
	otprav("zabil('".RE('media_id')."',\"".njs($s)."\")");
}
//========================================================================================================================
// ��������� ���������� �������������� �������� ����
/*
if($a=='loadfoto') {
	$id=h($_REQUEST["id"]);
	$idh=str_replace("_textarea","",$id);
	$panel="<br><input name='foto' type='file' onchange=\"idd('$id').value=idd('$id').value.replace(/\[IMG\]/gi,'')+'[IMG]'\">";
        otprav("clean('".$id."loadfoto');zabil('".$idh."p',vzyal('".$idh."p')+\"".njsn($panel)."\");");
}
*/
//========================================================================================================================
// ��������� �������� ������������ � �������
if($a=='loadcomments') {
	$art=ms("SELECT `opt`,`num` FROM `dnevnik_zapisi` ".WHERE("`num`='$dat'"),"_1");
	$art=mkzopt($art);
	//if(isset($_REQUEST["mode"]))
	$_GET['screen']=RE("mode");
	$comments_pagenum=RE("page");
	otprav("
	zabil('0',\"".njs(load_comments($art))."\");
	var c=gethash_c(); if(c && idd(c)) { kl(idd(c)); c=document.location.href; document.location.href=c; }
");
}

// ��������� �� �������� ���������, ��� ������� id
if($a=='loadpage_with_id') { // $id=RE0('id'); $dat
	$do="kl(idd('$id')); var c=document.location.href; document.location.href=c;";
	$pages=($comments_on_page?ceil(get_idzan1($dat)/$comments_on_page)-1:0); // ����� ������� ���������
	if(!$pages) otprav($do);
	if(($mas=load_mas($dat))===false) idie("err num: $id in $dat");
	$i=0; $n=0; while(isset($mas[$i]) && ($mas[$i]['level']!=1 || ++$n) && $mas[$i++]['id']!=$id){}
	$n=ceil($n/$comments_on_page)-1;
	if($n==RE0('page')) otprav($do);
	otprav("majax('comment.php',{a:'loadcomments',dat:$dat,page:$n})");
}

//========================================================================================================================
if($a=='pokazat') { // ��������
	$oid=$_REQUEST["oid"]; $id=intval(substr($oid,1));
	$level=($lev/$comment_otstup)+1;

	if(!$id /*or !$dat*/ or substr($oid,0,1)!='o') oalert("WTF?! oid:'".h($oid)."' id:'$id' dat:'$dat'");

$maxcommlevel=$level+2;
        $mas=load_mas($dat); if($mas===false) otprav("clean('$oid')");

$mojnocom=getmojno_comm($dat);

$r=''; $rr="clean('$oid');";

function otdalcomm($p,$id,$mojnocom){ return "
mkdiv(".$p['id'].",\"".njs(comment_one($p['p'],$mojnocom))."\",'".commclass($p['p'])."',idd(0),idd($id));
idd(".$p['id'].").style.marginLeft='".($p['level']*$GLOBALS['comment_otstup'])."px';
otkryl(".$p['id'].");
";
}
	for($i=0,$max=sizeof($mas);$i<$max;$i++){if($mas[$i]['p']['Parent']==$id){
		$rr.=otdalcomm($mas[$i],$id,$mojnocom);
		$i++; for(;$i<$max;$i++) { if($mas[$i]['level']<$level) break; $r=otdalcomm($mas[$i],$id,$mojnocom).$r; }
	}}

otprav($r.$rr);
}
//========================================================================================================================
if($a=='paren') { // �������� �������
	if(!$id) otprav('');
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0);
        $opt=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='".$p['DateID']."'","_1"); $GLOBALS['opt']=mkzopt($opt);
otprav("
mkdiv('show_parent',\"".njs(comment_one($p,getmojno_comm($p['DateID']),0 ))."\",'popup');
posdiv('show_parent',mouse_x+10,mouse_y);
");
}
//========================================================================================================================
if($a=='plus') { // ��������� ������
	if(!$unic) otprav("� � ��� ������ ���� ���������?");
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($p['unic']==$unic) idie("�� ������� �� ������������?");
	$e=mysql_query("INSERT INTO `dnevnik_plusiki` (`commentID`,`unic`,`var`) VALUES ($id,$unic,'plus')"); if(!$e) otprav("");
	mysql_query("UPDATE `dnevnik_comm` SET `golos_plu`=`golos_plu`+1 WHERE `id`='$id'");
	$p['golos_plu']++;
	otprav_comment($p);

}
//========================================================================================================================
if($a=='minus') { // ��������� �������
	if(!$unic) otprav("� � ��� ������ ���� ���������?");
        $p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0); if($p['unic']==$unic) idie("�� ������� �� ������������?");
	$e=mysql_query("INSERT INTO `dnevnik_plusiki` (`commentID`,`unic`,`var`) VALUES ($id,$unic,'minus')"); if(!$e) otprav("");
	mysql_query("UPDATE `dnevnik_comm` SET `golos_min`=`golos_min`+1 WHERE `id`='$id'");
	$p['golos_min']++;
	otprav_comment($p);
}
//========================================================================================================================
if($a=='editsend') { // ������� ����������������� �����������
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0))===false) idie('Comments:not_found');
	if(!$admin and $p['unic']!=$unic) idie('Comments:not_own');
	
	$text=$_REQUEST["text"]; $text=trim($text,"\n\r\t "); $text=str_replace("\r","",$text);
	if($text==$p['Text']) otprav("clean('$idhelp');"); // ���� ����� �� ��������� - ������ �������

	$scr=$p['scr']; include_once $GLOBALS['include_sys']."spamoborona.php";

	msq_update('dnevnik_comm',array('Text'=>e($text),'scr'=>$scr),"WHERE `id`='$id'");
	$p['Text']=$text;
	otprav_comment($p,"clean('$idhelp');");
}
//========================================================================================================================
if($a=='del') { // id ������� �����������
if($admin||($del_user_comments && $unic==ms("SELECT `unic` FROM `dnevnik_comm` WHERE `id`='$id'","_l")) ) {
cache_rm(comment_cachename(ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`='$id'","_l"))); otprav( del_comm($id) );
} otprav("clean($id);"); // ������ ����� ����� ����� ��� ������?
}
//========================================================================================================================
if($a=='edit') { // id ������������� �����������
	if(($p=ms("SELECT `unic`,`Text`,`Time`,`Name` FROM `dnevnik_comm` WHERE `id`='$id'","_1",0))===false) idie('Comments:not_found');

	if(!$admin) { // ��������� �� �������������?
		if($unic != $p['unic']) idie('Comments:not_own');
		if($comment_time_edit_sec && (time()-$p['Time'] > $comment_time_edit_sec)) idie("������������� ����� ������ � ������� ".floor($comment_time_edit_sec/60)." �����.");
		if(ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`=$id","_l",0)) idie("������������� ������ - ��� ���� ������.");
	}

$s="<form name='sendcomment_".$comnu."' onsubmit='cmsend_edit(this,".$comnu.",".$id."); return false;'><div id='co_$comnu'></div>
<textarea onkeyup='while(this.scrollTop)this.rows++' id='textarea_".$comnu."' style='border: 1px dotted #ccc; margin: 0; padding: 0;' name='txt' cols=50 rows=".max(3,page(h($p['Text']),50)).">".h(str_replace("\n",'\\n',$p['Text']))."</textarea>
<div><input title='Ctrl+Enter' id='editcomsend_".$comnu."' type=submit value='send'></div>
</form>";

if($comment_time_edit_sec && !$admin){
	$delta=$comment_time_edit_sec-(time()-$p['Time']); $dmin=date("i",$delta); $dsec=date("s",$delta);
	$o.="
var comm_red_timeout=function(id,n){ if(!idd('editcomsend_'+id)) return;
        if(!n) { idd('textarea_'+id).style.color='#AAAAAA'; return zakryl('editcomsend_'+id); }
        var N=new Date(); N.setTime(n*1000);
	var sec=N.getSeconds(); if(sec<10) sec='0'+sec;
        idd('editcomsend_'+id).value='Send before: '+N.getMinutes()+':'+sec;
        setTimeout('comm_red_timeout('+id+','+(--n)+')',1000);
}; comm_red_timeout(".$comnu.",".($dmin*60+$dsec-5).");";
} else $o='';

$s="comnum++; helps('".$idhelp."',\"<fieldset id='commentform_".$comnu."'><legend>".($admin?h($p['Name']):"��������������")."</legend>"
.$s."</fieldset>\"); idd('textarea_".$comnu."').focus();
setkey('enter','ctrl',function(){idd('editcomsend_".$comnu."').click()},false);

".$o;

otprav_sb('commentform.js',$s);
}

//========================================================================================================================
if($a=='ans') { // ���������-��������� ������ �� ���� �����������
	AD();
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0))===false) idie('Comments:not_found');
	$p['ans']=($p['ans']=='u'?'0':($p['ans']=='0'?'1':'u'));
	msq_update('dnevnik_comm',array('ans'=>$p['ans']),"WHERE `id`='$id'");
	otprav_comment($p,"idd($id).className='".commclass($p)."';");
}

//========================================================================================================================
if($a=='scr') { // ������-�������� ���� �����������
	if( !( ($GLOBALS['comment_friend_scr'] && $podzamok || $admin) ) ) oalert("� ���� ��� ���� ������ ���.");
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0))===false) idie('Comments:not_found');
	$p['scr']=($p['scr']==1?0:1);
	msq_update('dnevnik_comm',array('scr'=>$p['scr']),"WHERE `id`='$id'");
	otprav_comment($p,"idd($id).className='".commclass($p)."';");
}
//========================================================================================================================
if($a=='rul') { // ����������/����� ������ ����� �� ���� �����������
	AD();
	if(($p=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$id'","_1",0))===false) idie('Comments:not_found');
	$p['rul']=($p['rul']==1?0:1);
	msq_update('dnevnik_comm',array('rul'=>$p['rul']),"WHERE `id`='$id'");
	otprav_comment($p,"idd($id).className='".commclass($p)."';");
}

//========================================================================================================================
// if($a=='editpanel') { 	otprav("alert('$a')");	// otprav_sb('commentform.js',$s); }
//========================================================================================================================

if($a=='comsend') { razreshi_comm();
$text=str_replace("\r",'',trim(RE('text'),"\r\n\t ")); if($text=='') $erorrs[]=LL('Comments:empty_comm');
$name=($IS['user']!=''&&$IS['user_noname']!='noname'?$IS['user']:$_REQUEST["name"]); if($name=='') $erorrs[]=LL('Comments:empty_name');
$mail=mail_validate(RE('mail'));

//=====

if(count($_FILES)>0) {

	$opt=mkzopt(ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='$dat'","_1")); unset($opt['opt']);
//	dier($opt);
//    [Comment_foto_sign] => ����� ����� ���������
//    [Comment_foto_x] => 40
// eeeeeeeeeeeeeeeee
//    [Comment_foto_q] => 75

	foreach($_FILES as $n=>$FILE) if(is_uploaded_file($FILE["tmp_name"])){
	$foto_replace_resize=1;	require_once $include_sys."_fotolib.php";
        list($W,$H,$itype)=getimagesize($FILE["tmp_name"]); $img=openimg($FILE["tmp_name"],$itype);
        if($img===false) idie(LL('Comments:foto:musor',implode(', ',$foto_rash))); // �� �� �����?
	$imgs=obrajpeg_sam($img,$opt['Comment_foto_x'],$W,$H,$itype,str_ireplace('{name}',$name,$opt['Comment_foto_logo']));
	imagedestroy($img);
	}
}

// $imgs=array();
// $fname=h($FILE["name"]);
//	$frash=end(explode(".",strtolower($FILE['name'])));
//        if(!preg_match("/\.(jpe*g|gif|png)$/si",$fname)) idie("��� ����� �����?");
//        if(preg_match("/^\./si",$fname)) idie("��� � �����, ��? ��������!");
//        if(strstr($fname,'..') or strstr($fname,'/') or strstr($fname,"\\") ) idie("�����������, �������?");
//	elseif(is_file($fotodir.$fname)){$fname.='_'; $k=0; while(is_file($fotodir.$fname.(++$k))){} $fname.=$k;}
//        closeimg($img2,$to,$itype); imagedestroy($img);
//	if(false===obrajpeg($FILE["tmp_name"],$fotodir.$fname,$fotouser_x,$fotouser_q,str_ireplace('{name}',$name,$fotouser_logo))) idie("��� � �� �� ����� ������ ��� �����?");
//	$text=str_ireplace('[IMG]',"\n".$httphost."user/".$unic."/{comment_id}.".(3)."\n",$text);
//$imgs[]=array(obrajpeg_sam($img,$fotouser_x,$W,$H,$itype,$fotouser_q,str_ireplace('{name}',$name,$fotouser_logo)),$itype);
//        closeimg($img2,$to,$itype); imagedestroy($img);
//	if(false===obrajpeg($FILE["tmp_name"],$fotodir.$fname,$fotouser_x,$fotouser_q,str_ireplace('{name}',$name,$fotouser_logo))) idie("��� � �� �� ����� ������ ��� �����?");
//	$text=str_ireplace('[IMG]',"\n".$httphost."user/".$unic."/".urlencode($fname)."\n",$text);



//===


if(!sizeof($erorrs)) {

	$ara_kartochka=array(); // ���� ����� ��������� ������ � ��������

// ============ ���� ����� �������� ����� ==============
// 0 - �� ��������� ������ ����������, ��� ���� ������ ����� ���� ���
// 1 - ����� ���� ������� ���� ���, ����� �� ��������� �� ����
// 2 ... 255 - ��������� ����� � ���� ����������� ����
if($IS['capchakarma']!=1) {
	$karma=($IS['capchakarma']==0?$GLOBALS['antibot_C']:$IS['capchakarma']);
	include_once $GLOBALS['include_sys']."_antibot.php";
	if($_REQUEST['capcha']=='') otprav_error("������� ����� � �������� � ��������.");
        if(!antibot_check($_REQUEST['capcha'],$_REQUEST['capcha_hash'])) otprav_error("�������� ����� � ��������, ���������!",
"zabil('ozcapcha_".$comnu."',\"".njs("<table><tr valign=center><td>
<input maxlength=".$karma." class='capcha' type=text name='capcha'>
<input type=hidden name='capcha_hash' value='".antibot_make($karma)."'></td><td>".antibot_img()."</td></tr></table>")."\");");
	if($IS['capchakarma']==0) $ara_kartochka['capchakarma']=1; // �������� � ����, ��� ����� ������� ����
}
// ============ / ���� ����� �������� ����� ==============
	$scr=0;	include_once $GLOBALS['include_sys']."spamoborona.php";

	// $c=ms("SELECT `Comment_screen` FROM `dnevnik_zapisi` WHERE `num`='$dat'","_l");
	$po=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='$dat'","_1");
	$po=mkzopt($po); $c=$po['Comment_screen'];

	if($c=='screen' or (!$podzamok && $c=='friends-open')) $scr=1;

	$ara=array(
		'Text'=>$text,
		'Mail'=>$mail!=''?$mail:$IS['mail'],
		'Name'=>$name,
		'group'=>$admin?1:0,
		'IPN'=>$IPN,
		'BRO'=>$BRO,
// 'whois'
		'DateID'=>$dat,
		'unic'=>$unic,
		'Time'=>time(),
		'scr'=>$scr,
		'Parent'=>$id );

// ================ ��������� ������ =============================
if($id) // ���� ��� ����� (�� � ����� �������), ��������� ��������� ���� ���������
{ include_once $GLOBALS['include_sys']."_mail_answer.php"; mail_answer($id,$ara); }
// ===============================================================

// � ����� �� �� ����� ���������� ���� ����?
	$ans=($id==0?'u':ms("SELECT `ans` FROM `dnevnik_comm` WHERE `id`='$id'","_l"));
	if(!$admin and $ans=='0') idie('����� �������� �������� �� ���� �����������.');
	if($ans=='u') {	$e=getmojno_comm($dat);
		if(!$admin and $e===false) idie('� ���� ������� �������� ������.');
		if(!$admin and $e=='root' and $id!=0) idie('� ���� ������� ��������� �����������, �� �� ������ �� ���.');
	}
// ------------------------------------------

	msq_add('dnevnik_comm',arae($ara)); $newid=mysql_insert_id(); $ara['id']=$newid;
	del_last_tmp(); // ������� ���

//===================

if(isset($imgs)) { // ���� ���� ��������� �����
	$fotodir=$filehost."user/".$unic."/";
	if(!is_dir($fotodir)){ if(mkdir($fotodir)===false) idie("mkdir `".h($fotodir)."`"); chmod($fotodir,0777); }
	$to="user/$unic/$newid".".".$foto_rash[$itype];
	closeimg($imgs,$filehost.$to,$itype,$opt['Comment_foto_q']);
	$ara['Text']=str_ireplace("[IMG]","\n".$httphost.$to."\n",$text);
	msq_update('dnevnik_comm',array('Text'=>e($ara['Text'])),"WHERE `id`='$newid'");
}

//===================
//	$ara=ms("SELECT * FROM `dnevnik_comm` WHERE `id`='$newid'","_1",0);
//	$c=njs(comment_one($ara,getmojno_comm($ara['DateID'])));
	$ara['whois']=''; $ara['rul']=$ara['golos_plu']=$ara['golos_min']=0; $ara['ans']='u';
	$c=njs(comment_one($ara,getmojno_comm($ara['DateID'])));

// ================= ��������� ������ � �������� =================
	if($IS['realname']=='') $ara_kartochka['realname']=e($name);
	if($mail!='' && $IS['mail']=='') { $ara_kartochka['mail']=e($mail);
		include_once $include_sys."_sendmail.php"; send_mail_confirm($mail,$name);
	}
	if(sizeof($ara_kartochka)) msq_update($GLOBALS['db_unic'],$ara_kartochka,"WHERE `id`='$unic'");
// ================= ��������� ������ � �������� =================

	cache_rm(comment_cachename($dat));

otprav("f_save('comment',''); clean('$idhelp');
".($id?"mkdiv($newid,\"$c\",'".commclass($ara)."',idd(0),idd($id));":"mkdiv($newid,\"$c\",'".commclass($ara)."',idd(0));")."
idd($newid).style.marginLeft='".($lev+25)."px';
idd($newid).name='$newid';
otkryl($newid);
".(!$id?"window.location=mypage.replace(/#[^#]+$/g,'')+'#$newid';":"")."

if(typeof(playswf)!='undefined')playswf('http://lleo.me/dnevnik/design/kladez/'+((Math.floor(Math.random()*100)+1)%27));
"
////if(typeof(playswf)!='undefined')playswf('http://lleo.me/dnevnik/design/kladez/'+((Math.floor(Math.random()*100)+1)%27));
// .($GLOBALS['admin']?"alert('�������� ��� ������� #".$dat." = ".comment_cachename($dat)."'); ":'')
);


} else { otprav_error(implode('<br>',$erorrs)); }

}

//=================================== ��������� ����� ===================================================================

if($a=='comform') { // {a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������
 razreshi_comm();

if($dat==0) $dat=ms("SELECT `DateID` FROM `dnevnik_comm` WHERE `id`='$id'","_l",0); if($dat===false) idie("��������� ����.");

$s="<form enctype='multipart/form-data' name='sendcomment' onsubmit='cmsend(this,".$comnu.",".$id.",".$dat.",".$lev."); return false;'><div id='co_$comnu'></div>";

$s.= "<div><div class=l1>"
.($IS['user']!=''&&$IS['user_noname']!='noname'?$imgicourl:"���: <input name='name' class='in' type='text'>")."
</div><div class=l2>"
.($IS['mail']!=''?"<img alt='���� ��� ���-�� �������,<br>������ ������ �� ".h($IS['mail'])."<br>�� ����� ��� ���������,<br>������� � ���� ������� ��������<br>� ��������� ��� ������� `��������� ������`<br>� `����������� email`' src='".$www_design."e2/mail.png' align=right>"
:"mail: <input name='mail' class=in type=text onkeyup='this.value=cm_mail_validate(this)'>"
)."</div>
<br class=q /></div>";


// <div id='".$idhelp."p' style='display:inline; margin-left: 3px;'><img class=l onclick=\"majax('comment.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'></div>

if($IS['capchakarma']!=1) { include_once $include_sys."_antibot.php";
	$karma=($IS['capchakarma']==0?$GLOBALS['antibot_C']:$IS['capchakarma']);
$s.="<div><div class=l1>".($IS['capchakarma']==0?"�� ������� �� �����<br>":'')."�����������, ��� �� �� �����:</div>
<div class=l2 id='ozcapcha_$comnu'><table><tr valign=center><td><input maxlength=$karma class='capcha'
type=text name='capcha'><input type=hidden name='capcha_hash' value='".antibot_make($karma)."'></td>
<td>".antibot_img()."</td></tr></table></div><br class=q /></div>";
}

//$tmp=h(get_last_tmp()); if($tmp!='' && !strstr($tmp,'}')) $s.="<div class=br>� �������� ���� ������� ������������� �����:</div>";
//$s.="<div><textarea name='txt' id='".$idhelp."_textarea' class='textar'></textarea></div>"

$s.="<div id='".$idhelp."p' style='display:inline; margin-left: 3px; vertical-align: middle;' onclick=\"majax('comment.php',{a:'loadpanel',idhelp:this.id})\">
".LL('Comment:optionspanel')."</div>"
."<div><textarea name='text' onkeydown=\"f_save('comment',this.value)\" id='".$idhelp."_textarea' style='border: 1px dotted #ccc; margin:0; padding:0;' cols=60 rows=10></textarea></div>"
."<div class=l0><input id='".$idhelp."_submit' title='Ctrl+Enter' type=submit value='send'></div></form>";

//var keydownccount=0;
//keydowncom=function(s){ fc_save('comment',s);
//if(++keydownccount > ".$comment_autosave_count."){ keydownccount=0; majax('comment.php',{a:'autosave',text:s}); }};

otprav("

loadCSS('commentform.css');

if(f5s||jog) {
interval_clipboard=function(e){ if(!idd(e+'_textarea')) { eval('clearInterval(intervalID_'+e+')'); return; }
var m=f_read('clipboard_mode'); if(m=='') return;
if(m=='Copy link') {
        var l=f_read('clipboard_link'),t=f_read('clipboard_text');
        ti(e+'_textarea',l+\"\\n\\n[i]\"+t+\"[/i]\\n{select}\");
        f_save('clipboard_mode','');
}
}; var intervalID_".$idhelp."=setInterval(\"interval_clipboard('".$idhelp."')\",1000);
}

cm_mail_validate=function(p){ return p.value; };
cmsend=function(t,comnu,id,dat,lev) {
	var ara={a:'comsend',comnu:comnu,id:id,dat:dat,lev:lev}; var nara=['mail','name','text','capcha','capcha_hash','foto','html'];
        for(var l in nara) { l=nara[l]; if(t[l]) ara[l]=(l=='foto'?t[l]:t[l].value); } 
majax('comment.php',ara); return false;
};
comnum++; helpc('".$idhelp."',\"<fieldset id='commentform'><legend>�����������</legend>".njsn($s)."</fieldset>\"); 
idd('".$idhelp."_textarea').focus();
setkey('enter','ctrl',function(){idd('".$idhelp."_submit').click()},false);
setkey('esc','',function(e){
if( (e.target && e.target.value.length<2)||confirm('������� ����������� ��� ����������?')) clean('".$idhelp."'); },false);
var v=f_read('comment'); if(v!='') idd('".$idhelp."_textarea').value=v;
");

// alert(e.target.nodeName); e.target && e.target e.match(/input|textarea/i)
}

//=================================== ������� ����������� ===================================================================

function del_comm($id,$l=1) { if(!$id && !$GLOBALS['admin']) return " alert('id=0?!');";

	if($l and ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`='$id'","_l",0) ) { // ���� � ���� ���� ������� - ������ ��������

		msq_update('dnevnik_comm',array(
			'Time'=>0,'unic'=>0,'Name'=>'','Mail'=>'','Text'=>'','IPN'=>0,
			'BRO'=>'','whois'=>'','rul'=>'no','ans'=>'disable','golos_plu'=>0,
			'golos_min'=>0 ),"WHERE `id`='$id'");

		return " idd($id).innerHTML=''; idd($id).className='cdel';";
	}

	// ����� ������� ������
	
	$Parent=ms("SELECT `Parent` FROM `dnevnik_comm` WHERE `id`='$id'","_l",0); // ������ ��������� �������
	// ����� �������
	ms("DELETE FROM `dnevnik_comm` WHERE `id`='$id'","_l",0);
	$fot=glob($GLOBALS['filehost']."user/".$GLOBALS['unic']."/".$id.".*"); foreach($fot as $f) unlink($f); // � ����� ���� ����

	$r=" clean($id);";

	if( ! $Parent // ���� �� ��� � �����
		or ms("SELECT `Time` FROM `dnevnik_comm` WHERE `id`='$Parent'","_l",0) // ��� ��� ������� �� ������
		or ms("SELECT COUNT(*) FROM `dnevnik_comm` WHERE `Parent`='$Parent'","_l",0) // ���� � �������� ���� ������ �������
	) return $r; // �� ������ ��������� � ������� ����

	return $r.del_comm($Parent,0); // ����� ��������� �������� � ���
}

function otprav_comment($p,$r='') {
	cache_rm(comment_cachename($p['DateID'])); // �������� ��� �������� ���� ������
	$opt=ms("SELECT `opt` FROM `dnevnik_zapisi` WHERE `num`='".$p['DateID']."'","_1");
	$GLOBALS['opt']=mkzopt($opt);
	otprav("idd(".$p['id'].").innerHTML=\"".njs(comment_one($p,getmojno_comm($p['DateID']) ))."\"; ".$r);
}

function getmojno_comm($num) {
	$p=ms("SELECT `opt`,`DateDatetime`,`num` FROM `dnevnik_zapisi` WHERE `num`='".e($num)."'","_1");
	$p=mkzopt($p);
	$p['counter']=get_counter($p);
	return mojno_comment($p);
}

function otprav_error($s,$p='') { global $comnu; otprav("zabil('co_".$comnu."',\"<div class=e>".njs($s)."</div>\");".$p); }

//=================================== ��������� ����� ===================================================================
function send_comment_form($text,$id,$lev,$comnu) { // {a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // ��������

razreshi_comm();

$s="<form name='sendcomment' onsubmit='cmsend(this,".$comnu.",".$id.",".$dat.",".$lev."); return false;'><div id='co_$comnu'></div>";

$s.= "<div><div class=l1>"
.($IS['user']!=''&&$IS['user_noname']!='noname'?$imgicourl:"���: <input name='name' class='in' type='text'>")."
<div id='".$idhelp."p' style='display:inline; margin-left: 3px;'><img class=l onclick=\"majax('comment.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'></div>
</div><div class=l2>"
.($IS['mail']!=''?"<acronym title='������ ������ �� ".h($IS['mail'])."'><img src='".$www_design."e2/mail.png' align=right></acronym>"
:"mail: <input name='mail' class=in type=text onkeyup='this.value=cm_mail_validate(this)'>"
)."</div>
<br class=q /></div>";

if($IS['capchakarma']!=1) { include_once $include_sys."_antibot.php";
	$karma=($IS['capchakarma']==0?$GLOBALS['antibot_C']:$IS['capchakarma']);
$s.="<div><div class=l1>".($IS['capchakarma']==0?"�� ������� �� �����<br>":'')."�����������, ��� �� �� �����:</div>
<div class=l2 id='ozcapcha_$comnu'><table><tr valign=center><td><input maxlength=$karma class='capcha'
type=text name='capcha'><input type=hidden name='capcha_hash' value='".antibot_make($karma)."'></td>
<td>".antibot_img()."</td></tr></table></div><br class=q /></div>";
}

//$tmp=h(get_last_tmp()); if($tmp!='' && !strstr($tmp,'}')) $s.="<div class=br>� �������� ���� ������� ������������� �����:</div>";

//$s.="<div><textarea name='txt' id='".$idhelp."_textarea' class='textar'></textarea></div>"


// alert(e.target.nodeName); e.target && e.target e.match(/input|textarea/i)
}





function comment_textarea($s,$idhelp,$Text) {

$s.="<div><textarea name='text' onkeydown=\"fc_save('comment',this.value)\" id='".$idhelp."_textarea' style='border: 1px dotted #ccc; margin:0; padding:0;' cols=60 rows=10></textarea></div>"
."<div class=l0><input type=submit value='send'></div>"
."<div id='".$idhelp."p' style='display:inline; margin-left: 3px;'><img class=l onclick=\"majax('comment.php',{a:'loadpanel',idhelp:'".$idhelp."'})\" src='".$www_design."e3/finish.png' alt='panel'></div>"
."</form>";

$s="<form name='sendcomment_".$comnu."' onsubmit='cmsend_edit(this,".$comnu.",".$id."); return false;'><div id='co_$comnu'></div>
<textarea id='textarea_".$comnu."' style='border: 1px dotted #ccc; margin: 0; padding: 0;' name='txt' cols=50 rows=".max(3,page(h($p['Text']),50)).">".h(str_replace("\n",'\\n',$p['Text']))."</textarea>
<div><input type=submit value='send'></div>
</form>";

//$s="comnum++; helps('".$idhelp."',\"<fieldset id='commentform_".$comnu."'><legend>"
//.($admin?h($p['Name']):"��������������, �������� <span id='tiktik_".$comnu."'>".(date("i:s",$comment_time_edit_sec-(time()-$p['Time'])))."</span> ������")
//."</legend>".$s."</fieldset>\"); idd('textarea_".$comnu."').focus();";


//var keydownccount=0;
//keydowncom=function(s){ fc_save('comment',s);
//if(++keydownccount > ".$comment_autosave_count."){ keydownccount=0; majax('comment.php',{a:'autosave',text:s}); }};

//otprav_sb('commentform.js',$s);

otprav("

loadCSS('commentform.css');

if(f5_sup()) {
interval_clipboard=function(e){ if(!idd(e+'_textarea')) { eval('clearInterval(intervalID_'+e+')'); return; }
var m=f5_read('clipboard_mode'); if(m=='') return;
if(m=='Copy link') {
        var l=f5_read('clipboard_link'),t=f5_read('clipboard_text');
        ti(e+'_textarea',l+\"\\n\\n[i]\"+t+\"[/i]\\n{select}\");
        f5_save('clipboard_mode','');
}
}; var intervalID_".$idhelp."=setInterval(\"interval_clipboard('".$idhelp."')\",1000);
}

cm_mail_validate=function(p){ return p.value; };
cmsend=function(t,comnu,id,dat,lev) {
	var ara={a:'comsend',comnu:comnu,id:id,dat:dat,lev:lev}; var nara=['mail','name','text','capcha','capcha_hash','html'];
        for(var l in nara) { l=nara[l]; if(t[l]) ara[l]=t[l].value; } majax('comment.php',ara); return false;
};
comnum++; help�('".$idhelp
."',\"<fieldset id='commentform'><legend>�����������</legend>".njsn($s)."</fieldset>\"); 
idd('".$idhelp."_textarea').focus();
setkey('esc','',function(e){
if( (e.target && e.target.value.length<2)||confirm('������� ����������� ��� ����������?')) clean('".$idhelp."'); },false);
var v=fc_read('comment'); if(v!='') idd('".$idhelp."_textarea').value=v;
");

}

function razreshi_comm() { global $max_comperday,$unic,$admin;
if($GLOBALS['podzamok']) return; // ������� ����� ������ ����������� ������������
// if($admin) return; // ������ ����� ������ ����������� ������������
	if(!$max_comperday) return;
	$time=time();
	$p=ms("SELECT `Time` FROM `dnevnik_comm` WHERE `unic`='$unic' AND `Time`>'".($time-86400)."' ORDER BY `Time` LIMIT ".$max_comperday."","_a",0);
	if(sizeof($p)<$max_comperday) return;

$to=$p[0]['Time']+86400;
idie("���������� ���������� ������������ �� �������� � ����� &mdash; $max_comperday
<br>������ ".date("H:i",$time).", ����� ����������� ����� �������� "
.(date("d",$time)!=date("d",$to)?"������":"�������")." ����� ".(date("H:i",$to)) );
}

?>