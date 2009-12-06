<?

function maybelink($e) { // определ€лка кодировки
	$s=urldecode($e); if($s!=$e) $s=htmlspecialchars($s);
	if( ( strlen($s)/((int)substr_count($s,'–')+0.1) ) < 11 ) return(iconv("utf-8",$GLOBALS["wwwcharset"]."//IGNORE",$s));
	else return(trim($s));
}

function striplink($l) { return ( 
	( strstr($l,'.livejournal.com') && strstr($l,'/friends') ) // из френдленты
	|| strstr($l,'yandex.ru/top/') // топы
	|| strstr($l,'blog.yandex.ru') // топы
	|| strstr($l,'blogs.yandex.ru') // топы
	|| strstr($l,'yandex.ru/read.xml') // €ндексовые читалки
	|| strstr($l,'yandex.ru/unread.xml') // €ндексовые читалки
	|| strstr($l,'bloglines.com') // блоглайнс какой-то
	|| strstr($l,'graveron.fatal.ru') // спаммер что ли?
	|| ( strstr($l,'www.google.') && strstr($l,'/reader/') )  // google reader
	?true:false);
}
?>
