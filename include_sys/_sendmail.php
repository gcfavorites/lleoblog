<?php
function sendmail ($from_name, $from_adr, $to_name, $to_adr, $subj, $text) {

$to = "=?windows-1251?B?".base64_encode($to_name)."?= <".$to_adr.">";
$subj = "=?windows-1251?B?".base64_encode($subj)."?=";
$headers = "MIME-Version: 1.0
From: =?windows-1251?B?".base64_encode($from_name)."?= <".$from_adr.">
Date: ".date("r")."
Content-type: text/html; charset=windows-1251";

$s="from_name='".$from_name."'
from_adr='".$from_adr."'
to_name='".$to_name."'
to_adr='".$to_adr."'
subj='".$subj."'
text='".$headers."'

<hr>

".$headers."

".$to."

".$subj."

".$text;

// logi("mail-comment","\n\n\n\n".$s);

mail($to, $subj, $text, $headers);

}

function send_mail_confirm($mail,$realname) {
        global $include_sys,$httphost,$unic,$hashlogin,$newhash_user,$admin_name,$admin_mail;
        if(!mail_validate($mail)) idie("Неверный формат ".h($mail).".
<p>По крайней мере, роботу так кажется.
<br>Если вы считаете, что это глюк сайта,
<br>сообщите мне на lleo@aha.ru Спасибо.");

$link=$httphost."ajax/login.php?action=mailconfirm&mail=".urlencode($mail)."&pass=".md5($mail.$unic.$hashlogin.$newhash_user);
//$link=$httphost."ajax/login.php?action=mailconfirm&mail=".urlencode($mail)."&pass=".md5($mail.$hashlogin.$newhash_user);
$c="<p>Этот адрес был указан при регистрации на сайте <b>$httphost</b>, который ведет ".$admin_name
.". Чтобы подтвердить, пройдите по ссылке:
<p><a href='$link'>$link</a>
<p>В дальнейшем вы сможете получать ответы, присланные на ваши комментарии.
<p>Если это ошибка, просто игнорируйте письмо.";
   sendmail(h($admin_name),h($admin_mail),h($realname!=''?$realname:$mail),h($mail),$admin_name.": email confirm",$c);
   return;
}

?>