<?
include "config.php";
include $include_sys."_autorize.php";
require_once $include_sys."/JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest('UTF-8'); // ���������� AJAX
// JsHttpRequest($wwwcharset); // ���������� AJAX

$_REQUEST['answer']=uw($_REQUEST['answer']); // ����� ��������� � 1251 �� UTF-8






// logi('ajax',"\n\n\n\n".$_REQUEST['text']."\n".$_REQUEST['textnew']);

list($a,$b)=explode('-',$_REQUEST['hash']); if(substr(broident($b.$hashinput),0,6)!=$a)
pravka_otvet("������ �����������. �� ���-�� �� ��� ������, ����� ��������.");

include_once $include_sys."_msq.php";
include_once $include_sys."/_podsveti.php"; // ��������� Diff - ������ ����, ��������, �� ��������
include_once $include_sys."/_one_pravka.php"; // ��������� ������ ������ � ����� �������

$id=intval($_REQUEST['id']); $a=$_REQUEST['action'];

	if($a=='1') pravka_submit($id,pravka_answer($_REQUEST['answer'])); // ������ �������
	if($a=='0') pravka_discard($id,pravka_answer($_REQUEST['answer'])); // ������ ���������

	if($a=='podrobno') pravka_showmore($id); // �������� ������
	if($a=='edit') pravka_edit($id); // edit ������ ����� ��������������
	if($a=='edit_txt') pravka_edit_txt($id); // edit-send - ������ ����������������� �����

	if($a=='edit_c') pravka_edit_c($id); // edit ������ ����� �������������� ��������
	if($a=='edit_c_txt') pravka_edit_c_txt($id); // ������� ����������������� answer

	if($a=='del') { if($GLOBALS['admin']) msq_del($GLOBALS['db_pravka'],array('id'=>$id)); pravka_otvet(' '); } // ������� ����� �� ����

	if($a == 'opechatka') // ����� �������� �� ���������!
	{
		$text=pravka_valitext($_REQUEST['text']); // ���� ���� � �������!
		$textnew=pravka_valitext($_REQUEST['textnew']); // ���� ���� � �������!
		pravka_priem($_REQUEST['data'],$text,$textnew);
	}

	if($a == 'create') pravka_basa_create(); // �������: ������� ����� ����!
exit;

//===================== ������ ��������� ������ � ������� ===============
/*
function pravka_validata($data) {
		$d=preg_replace("/[^0-9a-z\._\-\#\/\:]/si","",$data); if($d!=$data) pravka_otvet('fuck you');
		return $data;
}
*/

function filename_valid($f) { return str_replace('..','',$f); } // �� ������ ������ ��� ���� ������ �� ����������� �� �����

function pravka_valitext($txt) {
	$txt=uw(htmlspecialchars(urldecode($txt)));
//	if( !$GLOBALS['admin'] && ( $GLOBALS['arhbasa']=='dnevnik_zapisi' || isset($GLOBALS['hashpresent']) ) ) $txt=strtr($txt,'ABCEHKMOPTXaceopxy','������������������'); // ��� ��� ������ ����
	return $txt;
}

function pravka_basa_replace($data,$txt) { if(!$GLOBALS['admin']) return; // �� ����������� // global $_REQUEST;
        list($base,$table,$bodyname,$wherename,$whereid)=explode('@',$data);
	if($base=='file#') { // ���� ������ ��������������� ��� �����, �� $table - ��� ��� ���
		return file_put_contents($GLOBALS['host'].filename_valid($table),$txt); // �������� ����������� � ����
	} else { // ������ ��� ����
		return ms("UPDATE ".($base!=''?"`".e($base)."`.":'')."`".e($table)."`
			SET `".e($bodyname)."`='".e($txt)."'
			WHERE `".e($wherename)."`='".e($whereid)."'","_l",0);
	}
}

function pravka_oldtxt($data) {
        list($base,$table,$bodyname,$wherename,$whereid)=explode('@',$data);
	if($base=='file#') { // ���� ������ ��������������� ��� �����, �� $table - ��� ��� ���
		if($txt=file_get_contents($GLOBALS['host'].filename_valid($table) ) ) return $txt;
		pravka_otvet("��� ������ ����� '".htmlspecialchars($table)."'");
	} else { // ������ ��� ����
		 return ms("SELECT `".e($bodyname)."` FROM ".($base!=''?"`".e($base)."`.":'')."`".e($table)."`
				WHERE `".e($wherename)."`='".e($whereid)."'","_l",0);
	}
}

//##################################################################
//##################################################################
// ��� �������� � ������ ������� ��������

function pravka_basa_add($data,$stdprav,$metka='new') { // �������� ������ � ���� (����������)
	if(!$GLOBALS['admin']) return;
	pravka_basa_add1($data,$stdprav,$metka);
}

function pravka_basa_add1($data,$stdprav,$metka='new') { // �������� ������ � ���� (�������!)
global $text,$textnew,$login;
        $ara=array();
        $ara['stdprav']=e($stdprav);
        $ara['sc']=e($GLOBALS['sc']);
        $ara['Date']=e($data);
        $ara['lju']=e($GLOBALS['lju']);
        $ara['ipbro']=e($_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_X_FORWARDED_FOR']."\n".$_SERVER['HTTP_USER_AGENT']);
        $ara['Mail']=e(str_replace('mailto:','',$_COOKIE['CommentaryAddress']));
        $ara['Name']=e($_COOKIE['CommentaryName']);
        $ara['login']=e($login);
        $ara['text']=e($text);
        $ara['textnew']=e($textnew);
        $ara['metka']=e($metka);
	msq_add($GLOBALS['db_pravka'],$ara); // ������ � ����
}

function pravka_basa_p($id) { 
	$p=ms("SELECT * FROM `".$GLOBALS['db_pravka']."` WHERE `id`='$id'",'_1',0);
	if($p['id']!=$id) pravka_otvet("������ ������!!!");

	if(!$GLOBALS['pravshort']) {
		$p['text']=pravka_bylo($p['stdprav']); // �� ���� �� ���������� ��� ����?
		$p['textnew']=pravka_stalo($p['stdprav']); // �� ���� �� ���������� ��� ����?
	}
	return $p;
}

function pravka_basa_metka($id,$metka,$answer) { if(!$GLOBALS['admin']) return;
	msq_update($GLOBALS['db_pravka'],array('metka'=>$metka,'Answer'=>$answer),"WHERE `id`='$id'");
}

function pravka_basa_getmetka($data,$stdprav) { // �������� ����� ��� ������ ������
	$p=ms("SELECT `metka` FROM `".$GLOBALS['db_pravka']."` WHERE `Date`='$data' AND `stdprav`='$stdprav'",'_1',0);
	return $p['metka'];
}


//======================================================================================================
function pravka_edit_c($id) { // editor ������������
	$p=pravka_basa_p($id); // ����� ������ �� ���� ������
	if($p['Answer']=='') pravka_otvet(_one_pravka($p));
	pravka_otvet(_one_pravka($p,pravka_textarea($id,$p['Answer'],'edit_c_txt')));
}

function pravka_edit_c_txt($id) { // editor ������������
	$p=pravka_basa_p($id);
	$p['Answer']=$_REQUEST['answer'];
	pravka_basa_metka($id,$p['metka'],e($p['Answer']));
	pravka_otvet(_one_pravka($p));
}

function pravka_edit($id) { // editor
	$p=pravka_basa_p($id); // ����� ������ �� ���� ������
	$p['oldtxt'] = pravka_oldtxt($p['Date']); // ����� �������� ����

	$stdprav = pravka_stdprav($p,200); // ����� ������� �����
	if($p['metka']=='submit') $text=pravka_stalo($stdprav); else $text=pravka_bylo($stdprav);

	$n=substr_count($p['oldtxt'],$text); if($n != 1) {
unset($p['oldtxt']);
pravka_otvet(_one_pravka($p,"
�� ������� ����� �����: ��� ����������� ".intval($n)." ���.
<p>stdprav='".htmlspecialchars($stdprav)."'
<p>text='".htmlspecialchars($text)."'
<p>p='".nl2br(htmlspecialchars(print_r($p,1)))."'
")); }
	pravka_textarea($id,$text,'edit_txt');
	pravka_otvet(_one_pravka($p,pravka_textarea($id,$text,'edit_txt')));
}

function pravka_edit_txt($id) { // editor
	$textnew=str_replace("\r","",$_REQUEST['answer']);

	$p=pravka_basa_p($id); // ����� ������ �� ���� ������
	$p['oldtxt'] = pravka_oldtxt($p['Date']); // ����� �������� ����
	$stdprav = pravka_stdprav($p,200); // ����� ������� �����
	if($p['metka']=='submit') $text=pravka_stalo($stdprav); else $text=pravka_bylo($stdprav);
	if(substr_count($p['oldtxt'],$text) != 1) pravka_otvet(_one_pravka($p,'�� ������� ����� ��� ����� ��������������.'));

	if($text == $textnew) pravka_otvet(_one_pravka($p,"��, �� � �����? ���������� �����������."));
	$stdprav=std_pravka($textnew,$text,$p['oldtxt']); // ��������� ����������� ����� ����� ������
	if($GLOBALS['pravka_paranoid']) pravka_basa_add($p['Date'],$stdprav,'submit'); // ������������� ���������� ����
	pravka_basa_replace($p['Date'],str_replace($text,$textnew,$p['oldtxt'])); // ��������� ������ �� ������
	$p['Answer'] .= '<i>��� ����� � �������������� �����:</i><p>'.str_replace("\n","\n<br>",$stdprav); $p['metka'] = 'discard';
	pravka_basa_metka($id,$p['metka'],$p['Answer']); // �������� ��� discard
	pravka_otvet(_one_pravka($p)); // ������ �����
}


function pravka_showmore($id) { // �������� ������
	$p=pravka_basa_p($id); // ����� ������ �� ���� ������
	$p['oldtxt'] = pravka_oldtxt($p['Date']); // ����� �������� ����
	$p['stdprav'] = pravka_stdprav($p,500); // ����� ������� �����
	pravka_otvet(_one_pravka($p));
}

####################################################

function pravka_submit($id,$answer) { // ������� ������
	$p=pravka_basa_p($id); // ����� ������ �� ���� ������
	if($p['metka']=='discard') $answer='�����������, ����� �������: '.$answer; // ������������ �����
	$oldtxt=pravka_oldtxt($p['Date']); // ����� �������� ����
	if(substr_count($oldtxt,$p['text'])!=1) $answer .= " �� ��� ����� ��� ����������!";
	else pravka_basa_replace($p['Date'],str_replace($p['text'],$p['textnew'],$oldtxt)); // ������� ������
	$p['Answer'] .= pravka_answer_n($answer,1); $p['metka'] = 'submit';
	pravka_basa_metka($id,$p['metka'],$p['Answer']); // �������� ��� submit
	pravka_otvet(_one_pravka($p)); // ������ �����
}

function pravka_discard($id,$answer) { // ��������� ������
	$p=pravka_basa_p($id); // ����� ������ �� ���� ������
	$metkanew='discard';
	if($p['metka']=='submit') { // ���� ���� ������� - �������
		$answer='�����������, ����� ��������: '.$answer; // ������������ �����
		$text=pravka_bylo($p['stdprav']); // ��� ����
		$textnew=pravka_stalo($p['stdprav']); // ��� �����
		$oldtxt=pravka_oldtxt($p['Date']); // ����� �������� ����
		if(substr_count($oldtxt,$textnew)!=1) { $answer .= '�������� �� �������.'; $metkanew='submit'; }
		else pravka_basa_replace($p['Date'],str_replace($textnew,$text,$oldtxt)); // ������� ������
		}
	$p['Answer'] .= pravka_answer_n($answer,0);
	$p['metka'] = $metkanew;
	pravka_basa_metka($id,$p['metka'],$p['Answer']);
	pravka_otvet(_one_pravka($p)); // ������ �����
}

###################################################

function pravka_priem($data,$text,$textnew) { global $_RESULT; // ����� �������� �� ���������!



	$oldtxt=pravka_oldtxt($data); // ����� �������� ����


	$nzamen=substr_count($oldtxt,$text); // ������� ��� ����������� ���� �������� � ������ (����, ����� 1)
if($oldtxt == '') pravka_otvet("������ �����-��. ��� ����� ������ � ����.");
if($text == $textnew) pravka_otvet("��, �� � �����? ���������� �����������.");
if($text == '') pravka_otvet("�������� ���-������ � ���������.\n����� ����� �����?");
if($nzamen == 0) {
if($GLOBALS['admin']) {
pravka_otvet("������. old:<p>".htmlspecialchars($text)."<hr>new:<p>'".htmlspecialchars($oldtxt)."'");
	if(preg_match("/\&[a-z0-9\#]+\;/si",$oldtxt,$m))
	pravka_otvet("������, �������������� �� �������.\n\n�����! � ��������-�� ������ �� ��������� ������ ���� '".htmlspecialchars($m[0])."'
".(preg_match("/^[^@]*@site@[^@]+@[^@]+@(\d+)$/",$data,$m)?" � <a href='".$wwwhost."adminsite/?mode=one&edit=".$m[1]."'>������ ���� ����� #".$m[1]."</a>":'') );
} pravka_otvet("������, �������������� �� �������.\n\n�����, ��� ������� HTML ��������?");
}

if($nzamen > 1) pravka_otvet("��, ����� �������������� ����������� ��������� ���.\n���������� �������� ������� ��������.");

	$stdprav=std_pravka($textnew,$text,$oldtxt); // ��������� ����������� ����� ����� ������
#	$stdprav=std_pravka(pravka_stalo($stdprav),pravka_bylo($stdprav),$oldtxt); // � ��� ��� ���������, ������ � ��������� �� �����
# �� ���� ������ ��� ���������! ���� ������




	if(!$GLOBALS['admin']) {
	$metka=pravka_basa_getmetka($data,$stdprav); // �������� �����, ���� ��� ����� ������
if($metka=='new') pravka_otvet("����� ������ ��� ��������\n� ��� ������������.");
if($metka=='discard') pravka_otvet("����� ������ ��� ������������,\n�� ����� ����� �� ���������.\n\n��� �����, ������ �� ��� �����.\n����� �������. � ������.");
if($metka=='submit') pravka_otvet("��... ����� ������ ��� ������������,\n� ���� ���� ������������ �������.\n���������, ������ �� �� �� ������ �� ������.\n�����, ��� ��������� ������� �����?\n�����������-�� ��������...");
	pravka_basa_add1($data,$stdprav,'new');
	$_RESULT['newbody']=wu(podsvetih(podsveti($textnew,$text))); pravka_otvet();
	}
	if($GLOBALS['pravka_paranoid']) pravka_basa_add($data,$stdprav,'submit'); // ������������� ���������� ����
	pravka_basa_replace($data,str_replace($text,$textnew,$oldtxt)); // ��������� ������ �� ������
	$_RESULT['newbody']=wu($textnew); pravka_otvet();
}



//=============================== ������ ����������� ==========================

function pravka_otvet($otvet='') { global $_RESULT; if($otvet!='') $_RESULT['otvet']=wu($otvet); $_RESULT['status']=true; exit; }

##############################################################################################################

function pravka_answer($answer) { // ����������� ����������� �������
$a=array(
	'da'=>'�� �������! ������� �������!',
	'ugovorili'=>'��... �� ���������? ��... �������, ��. �������.',
	'zadumano'=>'��������, �� ����� ���� ���� ������ ���.',
	'gramotei'=>'�, ����...',
	'len'=>'��, ��� ���-�� � ���� ��������... �����, ����� ����� ���� "���������" ���������?',
	'inache'=>'������, � ������� �� ����.',
	'spam'=>'��������� - �����.'
);
foreach($a as $l=>$m) if($l==$answer) return $m;
return $answer;
}

function pravka_answer_n($answer,$n) { if($answer!='') return '<div class='.($n?'y':'n').'>'.e(htmlspecialchars($answer)).'</div>'; }

function pravka_textarea($id,$text,$modescript) { $texth=htmlspecialchars($text); $ide=$id."_e";
return "<table><tr>
<td><TEXTAREA id='$ide' class='t' cols='50' rows='".max(page($texth),3)."'>".$texth."</TEXTAREA></td>
<td valign=top>
<input value='SEND >>' class='t' onclick=\"javascript:pravka($id,'$modescript',document.getElementById('$ide').value)\" type='button'>
</td></tr></table>";
}
 
// ��� ������ � �������� - ���� �� ���� ���������� ���� ����������������!
//$arhdir=$_SERVER['DOCUMENT_ROOT'].'arhive/'; // ��� ����������, ��� ������, ���� ��� � ������ (��������� windows-1251)
//$arhbasa='dnevnik_zapisi'; // ��� �������, ��� ������, ���� ��� � MySQL (� ���� - � ���� 'Body' �� ����� 'Data' ���� 2004-01-14

//require_once $_SERVER['DOCUMENT_ROOT'].'/sys/JsHttpRequest.php'; $JsHttpRequest =& new JsHttpRequest('windows-1251'); // ���������� AJAX
//include_once($_SERVER['DOCUMENT_ROOT'].'/dnevnik/_msq.php'); msq_open('lleo','windows-1251'); // ���������� MySQL
//include_once($_SERVER['DOCUMENT_ROOT'].'/dnevnik/_autorize.php'); // ���������� ����������� ������ $IS_EDITOR=true ���� �����
//include_once($_SERVER['DOCUMENT_ROOT'].'/sys/pravka/_podsveti.php'); // ��������� Diff - ������ ����, ��������, �� ��������
//include_once($_SERVER['DOCUMENT_ROOT'].'/sys/pravka/_one_pravka.php'); // ��������� ������ ������ � ����� �������
//include($_SERVER['DOCUMENT_ROOT'].'/sys/pravka/ajax_pravka_code.php'); // ������� ��������� - ��� ��������������� ��������


?>
