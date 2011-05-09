<?php // случайное число

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

otprav("helps('random','—лучайное число <b>".rand($_REQUEST["min"],$_REQUEST["max"])."</b>, вот!');");

?>