<?php // ��������� �����

include "../config.php"; // ������ ������
include $include_sys."_autorize.php"; // ����������� ����������, �� � ���������� ����:
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest("windows-1251");

otprav("helps('random','��������� ����� <b>".rand($_REQUEST["min"],$_REQUEST["max"])."</b>, ���!');");

?>