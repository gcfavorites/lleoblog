<?php // ����������� ����� ����������� ��� ���������


function NIKONOV($e) { global $admin,$podzamok,$article;
	list($url,$text)=explode("\n",$e,2); $url=c($url); $text=c($text);

	if(isset($GLOBALS['nikonov_no_epilog'])) return $text; // ���� ���������� ���������� - ������� �������.

	if( time() > (strtotime(substr($article['Date'],0,10)) + 86400*8) ) { // ���� ������ ������ - �� ��������
	return $text.NIKONOV_NEMALO(NIKONOVU($url));
}

// --- razgovor.org ---
if(strstr($url,'razgovor.org')) { $flag=$GLOBALS['hosttmp']."razgovor.org.flag"; if(file_exists($flag)) {
	return $text."<p><font color=red><i>���� �� ������ ���� ����� ����� �������, ������, ���������� ���������� ����� ���������, "
."��� ���� <a href='$url'>razgovor.org</a>, ��� �������� ������� ���� ��������, ����� ��������. ����������� ������� - "
.file_get_contents($flag).". ��� ���������� ��������� ������, ��� �� ����� ����� ����� �.����� ������� � ����� �������� �������� ��� "
."����� ������ ���� ���� ���������� �����������.</i></font>";
	} else return NIKONOV_PRODOL($url);
}

// --- solidarnost.org ---
if(strstr($url,'solidarnost.org')) return NIKONOV_PRODOL($url);

// --- solidarnost.org ---
if(strstr($url,'f5.ru')) { $flag=$GLOBALS['hosttmp']."f5.ru.flag"; if(file_exists($flag)) {
	return $text."<p><font color=red><i>�� ������ ���� ����� ��������� - ������, ���������� ���������� ����� ���������, ��� ���� "
."<b>f5.ru</b>, �� ������� ������������ ��� ������, ����� ��������. ����������� ������� - ".file_get_contents($flag).". ��� "
."���������� ��������� ������, ��� ��� �� ������� ��� ���� � ��� ������.</i></font>";

	} else return NIKONOV_PRODOL($url);
}

}

// ------------------------------------------------------------------

function NIKONOVU($url) {
	$u='razgovor.org'; if(strstr($url,$u)) return $u;
	$u='solidarnost.org'; if(strstr($url,$u)) return $u;
	$u='f5.ru'; if(strstr($url,$u)) return $u;
	return 'error';
}

function NIKONOV_NEMALO($u) { return
"<p><div style='border: 1px dotted black; margin-left:15%; margin-right:15%; padding:10pt; font-size: 12pt;'>"
."���� ����� ������� ��� ������� <a href='http://$u'>$u</a>, ��� � ���� ��������� �������. ������ ��� $u � ������� ������ �������� "
."����������, <span class=l onclick=\"majax('search.php',{a:'header',search:'".$u."'})\">��� �� ������ ������</span></div>";
}

function NIKONOV_PRODOL($url) {
	$u=NIKONOVU($url);
	return "<p><center><a href='$url'>...������ ����������� ����� ��������� �� ".NIKONOVU($url)."</a></center>".NIKONOV_NEMALO($u);
}

?>