<?php // �������� �������
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$a=$_REQUEST['a'];

if($a=='chi') {

$N=$_REQUEST['N'];
if(!intval($N) and $N!='0') idie("� �� ������ �����!");
if( $N%2 ) otprav("zabil('chi_otvet','��������'); helps('chi_okno','��������!');");
otprav("zabil('chi_otvet','������'); helps('chi_okno','������!');");
}

?>
