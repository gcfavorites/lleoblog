<?php // Отображение всего фотоальбома или избранных


function NIKONOV($e) { global $admin,$podzamok,$article;
	list($url,$text)=explode("\n",$e,2); $url=c($url); $text=c($text);
	if( time() > (strtotime($article['Date']) + 86400*8) ) { // если больше недели - не заменять
	return $text.NIKONOV_NEMALO(NIKONOVU($url));
}

// --- razgovor.org ---
if(strstr($url,'razgovor.org')) { $flag=$GLOBALS['hosttmp']."razgovor.org.flag"; if(file_exists($flag)) {
	return $text."<p><font color=red><i>Если вы видите этот текст здесь ценликом, значит, специально настронный робот определил, "
."что сайт <a href='$url'>razgovor.org</a>, для которого написан этот материал, опять сломался. Сегодняшняя причина - "
.file_get_contents($flag).". Это происходит регулярно потому, что их админ Роман Шкаев г.Томск занесен в книгу рекордов Гиннесса как "
."самые кривые руки мира последнего десятилетия.</i></font>";
	} else return NIKONOV_PRODOL($url);
}

// --- solidarnost.org ---
if(strstr($url,'solidarnost.org')) return NIKONOV_PRODOL($url);

// --- solidarnost.org ---
if(strstr($url,'f5.ru')) { $flag=$GLOBALS['hosttmp']."f5.ru.flag"; if(file_exists($flag)) {
	return $text."<p><font color=red><i>Вы видите этот текст полностью - значит, специально настронный робот определил, что сайт "
."<b>f5.ru</b>, на котором опубликована моя статья, опять сломался. Сегодняшняя причина - ".file_get_contents($flag).". Это "
."происходит регулярно потому, что так уж устроен тот сайт и его админы.</i></font>";

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
."Этот текст написан для проекта <a href='$url'>$u</a>, где я веду авторскую колонку. Вообще для $u я написал немало подобных "
."материалов, <span class=l onclick=\"majax('search.php',{a:'header',search:'".$u."'})\">вот их полный список</span></div>";
}

function NIKONOV_PRODOL($url) {
	$u=NIKONOVU($url);
	return "<p><center><a href='$url'>...читать продолжение этого материала на ".NIKONOVU($url)."</a></center>".NIKONOV_NEMALO($u);
}

?>