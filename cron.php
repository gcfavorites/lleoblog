<?php // ������ ��������� �� �����

if(!isset($admin_name)) {
	$cronprint=true;
	require("config.php");

$cronrez0='<p>�������� ����� ��������� �������� ������ ����� ������� �� ����� (crontab -e) � ������, ��� ���������� php-cli
������ �� ������ �������� ��� php web, ������� �, �� ��������� ������, ������������ cron.php ����� web:
<br><font color=green>*/5 * * * * /usr/bin/fetch -o /dev/null '.$httphost.'cron.php >/dev/null 2>&1</font>
<br>��� ���:
<br><font color=green>*/5 * * * * wget -O /dev/null '.$httphost.'cron.php >/dev/null 2>&1</font>
<hr><i>��������� ���������� cron.php:</i><p>';
}

// ====== ��������� ����������� ������ ========
//$cronrez=dostupen_li("f5.ru","http://kaganov.f5.ru/");
//$cronrez=dostupen_li("razgovor.org","http://www.razgovor.org/special/");

// ====== ��������� ������ ����������� �������� ========

include_once $include_sys."_antibot.php";
$cronrez .= "<br>".antibot_del();

// ====== ��������� ���� ����� ========
file_put_contents($cronfile,$cronrez);


if($cronprint and !$admin_upgrade) die("<html><body>".$cronrez0.$cronrez."</body></html>");

//===========================================================================================
//===========================================================================================
//===========================================================================================
//===========================================================================================

function dostupen_li($name,$url) { $flag=$GLOBALS['host_log'].$name.".flag"; $s='';
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_COOKIE,'');
curl_setopt($ch,CURLOPT_USERAGENT,'ROBOT from http://lleo.aha.ru/dnevnik/ - test if '.$name.' exist every 10 min');
	curl_setopt($ch,CURLINFO_HEADER_OUT,true);
	curl_setopt($ch,CURLOPT_HEADER,1); // get the header
//	curl_setopt($ch,CURLOPT_NOBODY,1); // and *only* get the header
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // get the response as a string from curl_exec(), rather than echoing it
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,1); // don't use a cached version of the url
curl_setopt($ch,CURLOPT_TIMEOUT,5); //������ ����������� ����� ���������� �������� � ��������.

if(!($ans=curl_exec($ch))) { $s.="<h1>error - not found!</h1>"; file_put_contents($flag,prichinar(1,$name)); }
elseif( strstr($ans,'.php</b> on line') ) { $s.="<h1>error - PHP error!</h1>"; file_put_contents($flag,prichinar(2,$name)); }
elseif( strstr($ans,'mysql_query(): ') ) { $s.="<h1>error - MySQL error!</h1>"; file_put_contents($flag,prichinar(3,$name)); }
elseif( strstr($ans,'gninx') ) { $s.="<h1>error - gnix error!</h1>"; file_put_contents($flag,prichinar(4,$name)); }
else unlink($flag);

$s.="<p>
<br>������� �������� �������: ".curl_getinfo($ch,CURLINFO_SPEED_DOWNLOAD)."
<br>������ ����� ".curl_getinfo($ch,CURLINFO_TOTAL_TIME)." ���
<br><pre>$ans</pre>
";

curl_close($ch);
}

function prichinar($l,$name) { $i=1; return "<select>
<option value=".$i.($i++ == $l ?' selected':'').">���� ����� �� ��������
<option value=".$i.($i++ == $l ?' selected':'').">������� ������ PHP-����
<option value=".$i.($i++ == $l ?' selected':'').">����� MySQL-����
<option value=".$i.($i++ == $l ?' selected':'').">��������� ������ gninx
<option value=".$i.($i++ == $l ?' selected':'').">���� ���� ��������
<option value=".$i.($i++ == $l ?' selected':'').">�� ����� �����
<option value=".$i.($i++ == $l ?' selected':'').">������ ������ ��������
<option value=".$i.($i++ == $l ?' selected':'').">����� �������� �������������
<option value=".$i.($i++ == $l ?' selected':'').">������ �����, ����������
<option value=".$i.($i++ == $l ?' selected':'').">������� CSS, ��������� �����
<option value=".$i.($i++ == $l ?' selected':'').">������ ���� �� ".$name."
</select>";
}

?>