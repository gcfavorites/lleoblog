<?php // случайное число

include "../config.php"; // конфиг движка
include $include_sys."_autorize.php"; // авторизация посетителя, ну и библиотека аякс:
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

otprav("helps('random','Случайное число <b>".rand($_REQUEST["min"],$_REQUEST["max"])."</b>, вот!');");

?>