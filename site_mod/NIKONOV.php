<?php // ќтображение всего фотоальбома или избранных


function NIKONOV($e) { global $admin,$podzamok,$article;
	list($url,$text)=explode("\n",$e,2); $url=c($url); $text=c($text);

	if(isset($GLOBALS['nikonov_no_epilog'])) return $text; // ≈сли установлен спецфлажок - вернуть целиком.

	if( time() > (strtotime(substr($article['Date'],0,10)) + 86400*8) ) { // если больше недели - не замен€ть
	return $text.NIKONOV_NEMALO(NIKONOVU($url));
}

// --- razgovor.org ---
if(strstr($url,'razgovor.org')) { $flag=$GLOBALS['hosttmp']."razgovor.org.flag"; if(file_exists($flag)) {
	return $text."<p><font color=red><i>≈сли вы видите этот текст здесь целиком, значит, специально настронный робот определил, "
."что сайт <a href='$url'>razgovor.org</a>, дл€ которого написан этот материал, оп€ть сломалс€. —егодн€шн€€ причина - "
.file_get_contents($flag).". Ёто происходит регул€рно потому, что их админ –оман Ўкаев г.“омск занесен в книгу рекордов √иннесса как "
."самые кривые руки мира последнего дес€тилети€.</i></font>";
	} else return NIKONOV_PRODOL($url);
}

// --- solidarnost.org ---
if(strstr($url,'solidarnost.org')) return NIKONOV_PRODOL($url);

// --- solidarnost.org ---
if(strstr($url,'f5.ru')) { $flag=$GLOBALS['hosttmp']."f5.ru.flag"; if(file_exists($flag)) {
	return $text."<p><font color=red><i>¬ы видите этот текст полностью - значит, специально настронный робот определил, что сайт "
."<b>f5.ru</b>, на котором опубликована мо€ стать€, оп€ть сломалс€. —егодн€шн€€ причина - ".file_get_contents($flag).". Ёто "
."происходит регул€рно потому, что так уж устроен тот сайт и его админы.</i></font>";

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
."Ётот текст написан дл€ проекта <a href='http://$u'>$u</a>, где € веду авторскую колонку. ¬ообще дл€ $u € написал немало подобных "
."материалов, <span class=l onclick=\"majax('search.php',{a:'header',search:'".$u."'})\">вот их полный список</span></div>";
}

function NIKONOV_PRODOL($url) {
	$u=NIKONOVU($url);
	return "<p><center><a href='$url'>...читать продолжение этого материала на ".NIKONOVU($url)."</a></center>".NIKONOV_NEMALO($u);
}

?>