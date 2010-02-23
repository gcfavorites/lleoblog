<?php // тестовое решение
include "../config.php";
include $include_sys."_autorize.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

$a=$_REQUEST['a'];

if($a=='chi') {

$N=$_REQUEST['N'];
if(!intval($N) and $N!='0') idie("Я же просил число!");
if( $N%2 ) otprav("zabil('chi_otvet','нечетное'); helps('chi_okno','нечетное!');");
otprav("zabil('chi_otvet','четное'); helps('chi_okno','четное!');");
}

?>
