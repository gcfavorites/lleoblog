<?php // ����������� ������ � ��������� - ���� �������� � $Date

/*
comm:disabled_login     <p>����������� � ���� ������� ���� �������� ��������� ������ ������������, �� ������������� ���������
comm:screen             <p>����������� � ���� ������� ���������� - ��� ����� ����� ������ ��� � ���.
comm:screen_nofriend    <p>����������� � ���� ������� ����������, �� � ������ (� ����) ��� ����� �������.
comm:comment_this       <div id='commpresent' class='l' style='font-weight: bold; margin: 20pt; font-size: 16px;' {majax}>���
comm:future             <blockquote style='border: 3px dotted rgb(255,0,0); padding: 2px;'><font size=2>������� ���������� ��

comm:page               <div style='margin: 50px;'>{0}</div>
comm:button             <input TYPE='BUTTON' VALUE=' ������ �����������{dopload} ({podzamok?�����|��������} {idzan} ��) ' {ma

comm:s                  <div class=r style='margin: 50px;'>{s}</div>
comm:pro                <div id=0>{pro}<div></div></div>

comm:nocomments         <p class=z>������������ ��� ��� ��� ��� ������
comm:itogo              <center><p class=br>����� ������������: {nmas}</p>{u?<p>�������� ������ �������� ����������� - <span


*/
function COMENTS($e) { global $article, $podzamok, $load_comments_MS, $enter_comentary_days, $N_maxkomm, $idzan;

//===================================
// ��� ���� � �������������?
$s='';

$dopload="";

$comments_form=true; // �������� ����� ������ ������������
$comments_knopka=false; // �������� ������ �������� ������������
$comments_list=false; // ������� �������� ������������
$comments_screen=true;

	get_counter($article); // ���������� �������� ��������, ���� �� ����

$comments_timed=(
		$article["counter"] > $N_maxkomm // ���������� ���������� ���������
		|| $article["DateTime"] < time()-86400*$enter_comentary_days // ������� ������ �������
		?true:false); 

switch($article["Comment_view"]) { // Comment_view enum('on', 'off', 'rul', 'load', 'timeload')
	case 'on': $comments_knopka=false; $comments_list=true; break;
	case 'off': $comments_knopka=false; $comments_form=false; $comments_list=false; break;
	case 'rul': $comments_knopka=true; $comments_list=true; $load_comments_MS=" AND `rul`='1'";
$dopload=LL('comm:ostalnye'); // " ���������";
break;
	case 'load': $comments_knopka=true; $comments_list=false; break;
	case 'timeload': $comments_knopka=$comments_timed; $comments_list=!$comments_timed; break;
	}

switch($article["Comment_write"]) { // Comment_write enum('on', 'off', 'friends-only', 'login-only', 'timeoff', 'login-only-timeoff')
	case 'on': $comments_form=true; break;
	case 'off': $comments_form=false;
$s.=LL('comm:off'); // "����������� ���������";
break;
	case 'friends-only': $comments_form=$podzamok; if($podzamok)
$s.=LL('comm:friends_only'); // "�������� ����������� ����� ������";
break;
	case 'login-only': $comments_form=((($GLOBALS['IS']['login']!=''&&$GLOBALS['IS']['password']!='')||$GLOBALS['IS']['openid']!='')?true:false);
$s.=LL('comm:login_only',array('majax'=>"onclick=\"ifhelpc('".$GLOBALS['httphost']."login','logz','Login')\""));
// "<p>� ���� ������� �������� ���������� ����� ������ ������������. ������������ ����� �����";
break;
	case 'timeoff': $comments_form=!$comments_timed; if(!$comments_form)
$s.=LL('comm:disabled',array('1'=>$enter_comentary_days,'2'=>$N_maxkomm,'mail'=>$GLOBALS['admin_mail']));
// "����������� �����������, ������ ��� ������ ".$enter_comentary_days." ���� ��� ��������� ".$N_maxkomm.". ������ �������� mailto";
break;
	case 'login-only-timeoff': $comments_form=($login?!$comments_timed:false); if(!$comments_form)
$s.=LL('comm:disabled_login',array('1'=>$enter_comentary_days,'2'=>$N_maxkomm,'mail'=>$GLOBALS['admin_mail']));
// "����������� ���� ��������� ������������, �� ����������� � ���
break;
	}

switch($article["Comment_screen"]) { // Comment_screen  enum('open', 'screen', 'friends-open')
	case 'open': $comments_screen=false; break;
	case 'screen': $comments_screen=true; if($comments_form)
$s.=LL('comm:screen'); // "����� ����� ������ ��� � ���";
break;
	case 'friends-open': $comments_screen=!$podzamok; if($comments_form && $podzamok)
$s.=LL('comm:screen_nofriend'); // "� ������ (� ����) ��� ����� �������.
break;
	}

if(strstr($_SERVER["HTTP_USER_AGENT"],'Yandex') || $GLOBALS['IP']=='78.110.50.100') { // ������ �������
	$comments_form=false; // ��������� ����������� - �� ���� (����� ������� ��������� �����������?)
	$comments_knopka=false; // �������� ������������ - �������� � �������� (������ �� ����� �������� ������, � ����� �� �������������)
	$comments_list=true;
	}

//===================================

if($comments_form) { // ��������� �������� �����������
	$s.= LL('comm:comment_this',array('majax'=>"onclick=\"majax('comment.php',{a:'comform',id:0,lev:0,comnu:comnum,dat:".$article['num']."});\""));
// �������� �����������
	if ( $article["DateTime"] > time() ) $s.=LL('comm:future'); // ������� ���������� ������� ������
}

	$idzan=get_idzan($article['num']);
//	$idzan1=get_idzan1($article['num']);

if($idzan) { // ���� ������ ���� �����������
	if($comments_list) { // ������� �������� ����������
		$template=$e; include_once $GLOBALS['include_sys']."_onecomm.php";
		$pro = load_comments($article);
		SCRIPTS("page_onstart.push(\"var c=gethash_c(); if(c){ if(idd(c)){kl(idd(c))} else majax('comment.php',{a:'loadpage_with_id',page:0,id:c,dat:".$article['num']."})}\")");
	} elseif($comments_knopka) { // ���������� �� ������
		$pro=LL('comm:page', get_comm_button($article['num'],$dopload,$comments_knopka) );
		SCRIPTS("page_onstart.push(\"var c=gethash_c(); if(c) majax('comment.php',{a:'loadpage_with_id',page:0,id:c,dat:".$article['num']."})\");");
	}
} // {a:'loadcomments',dat:'".$article['num']."'}

return ($s!=''?LL('comm:s',$s):'').LL('comm:pro',$pro);

}
?>