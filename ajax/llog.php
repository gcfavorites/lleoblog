<?php // �������
include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
if(isset($_REQUEST['onload'])) otprav(''); // ��� ���������� ����� ����� ��������� ��� GET-�������
include $include_sys."_autorize.php"; // ������ JsHttpRequest, ����� autorize

function llog2($s) { global $aharu,$IP,$BRO,$MYPAGE;
        if(!$aharu) return;
        logi('autorizaAJAX.txt',"\n".h($s." ".$IP." | ".$BRO." | ".$MYPAGE));
}

llog2("Ajax: ".$_REQUEST["s"]." ");

otprav("");

?>
