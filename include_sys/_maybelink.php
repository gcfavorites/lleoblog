<?

function maybelink($e) { // ����������� ���������
	$s=urldecode($e); if($s!=$e) $s=htmlspecialchars($s);
	if( ( strlen($s)/((int)substr_count($s,'�')+0.1) ) < 11 ) return(iconv("utf-8",$GLOBALS["wwwcharset"]."//IGNORE",$s));
	else return(trim($s));
}

function striplink($l) { return ( 
	( strstr($l,'.livejournal.com') && strstr($l,'/friends') ) // �� ����������
	|| strstr($l,'yandex.ru/top/') // ����
	|| strstr($l,'blog.yandex.ru') // ����
	|| strstr($l,'blogs.yandex.ru') // ����
	|| strstr($l,'yandex.ru/read.xml') // ���������� �������
	|| strstr($l,'yandex.ru/unread.xml') // ���������� �������
	|| strstr($l,'bloglines.com') // ��������� �����-��
	|| strstr($l,'graveron.fatal.ru') // ������� ��� ��?
	|| ( strstr($l,'www.google.') && strstr($l,'/reader/') )  // google reader
	?true:false);
}
?>
