<?php // ��������� �����

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // ������ JsHttpRequest, ����� autorize

otprav("helps('random','��������� ����� <b>".rand($_REQUEST["min"],$_REQUEST["max"])."</b>, ���!');");

?>