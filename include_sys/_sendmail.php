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
?>
