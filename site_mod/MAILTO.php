<?php // ������� ����� �������
if(!isset($GLOBALS['admin_name'])) die("Error 404"); // ����������� ����������� ������ - �����

function MAILTO($e) { $s=explode(':',$e);
	$from_name=c($s[1]);
	$s2=explode(':',$s[2]);
	$from_adr=c($s2[0]);
//	$tonmame=
//	$fo_name=


//?a=create&name=answer

//	include_once $include_sys."_sendmail.php";
//	sendmail($GLOBALS["admin_name"].": �������",$GLOBALS["admin_mail"],$p['Name'],$mailmail,$mailsubj,$mailtext);

////////sendmail($from_name, $from_adr, $to_name, $to_adr, $subj, $text);

//	sendmail($GLOBALS["admin_name"].": ����",$GLOBALS["admin_mail"],$p['Name'],$mailmail,"������ � �����","������ �����");

//die($from_name.$from_adr);
}


?>
