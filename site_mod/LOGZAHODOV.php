<?php /* ������ �����

��������� ����� ���������� ������ (����������� � ��� &lt;b&gt;).
�������� ����������� ������ � ���� ������ ���, ������ �� ��� ������ ������ ���� �����.

{_B:������_} �����
*/

function LOGZAHODOV($e) {

	$is=$GLOBALS['IS']; unset($is['password']);

	$s="\n\n".date("Y-m-d H:i:s")." ".$GLOBALS['mypage']."
".$GLOBALS['IP']." ".$GLOBALS['BRO']."
".print_r($is,1);

	logi("LOGZAHODOV_".preg_replace("/[^a-z0-9_\.]+/si","_",$GLOBALS['article']['Date']).".txt",$s);

	return "";

}
?>