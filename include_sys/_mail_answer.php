<?php if(!function_exists('h')) die("Error 404"); // ����������� ����������� ������ - �����

include_once $include_sys."_sendmail.php";

function mail_answer($id,$ara) { //------------------- ������� �� �����

// if(!mail_validate($ara["Mail"])) return false; // ���� mail ������������ ��������

$p = ms("SELECT zapisi.`Header`, zapisi.`Date`, parent.* FROM `dnevnik_zapisi` AS zapisi, `dnevnik_comm` AS parent 
WHERE zapisi.`num`='".$ara['DateID']."' AND parent.`id`='".intval($id)."'","_1",0);

if($ara['unic']==$p['unic'] // ���� ��� ��� ����
or !mail_validate($p['Mail']) // ��� � ���� �������� �����
or $p['mail_checked']!=1 // ��� � ���� �������� �� ����������� ���� email
or $p['mail_comment']!=1 // ��� ��� ������� �������� ���� ������� �������
) return false;

$head=strtr($p['Date'],'/','-')." ".$p['Header'];

$c="<p>������ ����� �� ��� ����������� � �����, ������� ����� ".$GLOBALS["admin_name"].":
<br><a href='".h(get_link($p['Date'])."#".$id)."'>".h($head)."</a>

<p><div style='border: 1px dotted #ccc; background-color: #fff0ff'>"
.date('Y-m-d H:i:s',$p['Time'])." ".h($p['Name']).":<p>".h($p['Text'])."</div>

<p><i>�����:</i>
<div style='border: 1px dotted #ccc; background-color: #FFFBDF; '>"
.date('Y-m-d H:i:s',$ara['Time'])." ".h($ara['Name']).":<p>".h($ara['Text'])."</div>";

$subj = h($ara['Name']." �������� �� �������� ".$head);

sendmail(h($ara["Name"]),h($ara["Mail"]),h($p['Name']),h($p['Mail']),$subj,$c);

//$c=njs($c);
//$c="sendmail(".h($p["Name"]).",".h($p["Mail"]).",".h($ara['Name']).",".h($ara['Mail']).",".$subj.",".$c.");";
//$c=njs(nl2br(print_r($p,1)));
//otprav("helps('mailsend',\"<fieldset id='commentform'><legend>mailsend-test</legend><div class=br>".$c."</div></fieldset>\");");

/*
logi('mail_answer.txt',"
".$GLOBALS["admin_name"]." ".$GLOBALS["admin_mail"]." - ".$p['Name']." ".$mailmail."
".$mailsubj."
".$mailtext);
*/

}

?>