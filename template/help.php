<?php

function select_modul() {
	$inc=glob($GLOBALS['filehost']."site_mod/*.php"); $ainc=array(); $ainc['']='- модули -';
	foreach($inc as $l) {
		$s=file_get_contents($l);
		$l=preg_replace("/^.*?\/([^\/]+)\.php$/si","$1",$l);
		if(!preg_match("/\/\*(.*?)\*\//si",$s,$m)) $l="--".$l;
		$ainc[$l]=$l;
	}
	return selecto('editor_mod','',$ainc,"onchange=\"majax('editor.php',{a:'help',mod:this.value})\" id");
}

if($name=='') $s="{_B:{_CENTER:Команды, кнопки и модули редактирования_}_}

<p>1. <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'module'})\">Описание всех модулей</a>
<p>2. <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'knop'})\">Описание кнопок</a>
<p>3. <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'about'})\">Общая информация</a>
<p>4. Быстрая справка по модулям: ".select_modul()."
";

elseif($name=='module') {
	$s="{_CENTER:{_B:Модули_}_}";

	$inc=glob($GLOBALS['filehost']."site_mod/*.php"); $ainc=array(); foreach($inc as $l) {
		$t=file_get_contents($l);
		$l=preg_replace("/^.*?\/([^\/]+)\.php$/si","$1",$l);



		if(preg_match("/\/\*(.*?)\*\//si",$t,$m)) {
			$s.="<br><a href=\"javascript:majax('editor.php',{a:'help',mod:'$l'})\"\">$l</a> - ";
			$t=c($m[1]);
			if(preg_match("/^([^\n]+)\n(.*?)$/si",$t,$m)) { $head=$m[1]; $t=c($m[2]); }
			if(preg_match("/(.*?)\n([^\n]*\{\_.*?)$/si",$t,$m)) { $t=c($m[1]); $prim=c($m[2]); }
			$s.=$head;
		}
	}
}

elseif($name=='about') $s="{_CENTER:{_B:Общий принцип_}_}

Текст, набираемый в редакторе, автоматически обновляется на сайте по мере набора - помните об этом, когда работаете над страницей,
опция доступа которой позволяет кому-то ее увидеть в этот момент.

{_CENTER:{_B:Кнопки_}_}

Чтобы воспользоваться <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'knop'})\">горячими кнопками</a>, выделите мышкой
нужный кусок и нажмите кнопку - появятся необходимые вставки.

{_CENTER:{_B:Модули_}_}

Здесь используется система <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'module'})\">модулей</a>. Модуль - короткая программка,
которая вызывается из текста комбинацией символов {<b></b>_ИМЯ:[параметры]_<b></b>}.
Почему выбран такой странный формат тэга? Потому что комбинация {<b></b>_ и _<b></b>} не встречается нигде, даже в коде Javascript.
Который тоже хотелось бы использовать. Технически модуль может выполнить абсолютно любую монотонную работу. Поэтому если мне
нужно сверстать какие-то сложные таблицы или сделать вывод чего-либо, лично я всегда пишу модуль, которому можно задать данные в простой
форме, и он их сверстает сам.

Пробелы и переводы строки между аргументами внутри тэга {<b></b>_ _<b></b>} значения не имеют - можно ставить пробелы, можно не ставить. Главное -
указать имя существующего модуля и поставить двоеточие, после чего можно указать аргументы (если они для этого модуля есть и требуются).

{_CENTER:{_B:Фотоальбом_}_}

Одна из повседневных задач - быстро и легко вставить в текст страницы фотки. Для этого я разрабатываю систему фотоальбома, который
позволял бы в момент выбирать и вставлять фотки кликом мышки. Но пока этот фотоальбюом существует только в моем воображении, когда я
его закончу, это будет круто. Но пока его нет и зря вы читали этот абзац.
";

elseif($name=='knop') $s="{_CENTER:{_B:Список горячих кнопок_}_}

<p><img border=1 src=".$www_design."e3/foto.png> - открыть систему фотоальбома (пока не работает)
<p><img border=1 src=".$www_design."e3/gnome-help.png> - вызвать справку (вы это сделали, да?)
<p><img border=1 src=".$www_design."e2/pd.gif> - вставить тэг нового абзаца с отступом (устаревшее)
<p><img border=1 src=".$www_design."e2/d.gif> - вставить тэг новой строки с отступом (устаревшее)
<p><img border=1 src=".$www_design."e2/nbsp.gif> - вставить неразрывный пробел
<p><img border=1 src=".$www_design."e2/copy.gif> - вставить значок копирайта &copy;
<p><img border=1 src=".$www_design."e2/mdash.gif> - вставить длинное тире &mdash; (теперь расставляется само, если не отключена опция \"не менять кавычки\")
<p><img border=1 src=".$www_design."e2/ltgt.gif> - взять выделенный мышкой текст в фигурные кавычки &laquo; и $raquo; (можно ставить обычные кавычки - при записи заметки они, где надо, автоматически превратятся в красивые фигурные, если не отключена опция \"не менять кавычки\").
<p><img border=1 src=".$www_design."e2/bold.gif> - выделите мышкой текст и нажмите: он станет <b>жирным</b>
<p><img border=1 src=".$www_design."e2/italic.gif> - выделите мышкой текст и нажмите: он станет <i>курсивом</i>
<p><img border=1 src=".$www_design."e2/strikethrough.gif> - выделите мышкой текст и нажмите: он станет <s>зачеркнутым</s>
<p><img border=1 src=".$www_design."e2/underline.gif> - выделите мышкой текст и нажмите: он станет <u>подчеркнутым</u>
<p><img border=1 src=".$www_design."e2/justifycenter.gif> - выделенный мышкой кусок будет по центру
<p><img border=1 src=".$www_design."e2/image.gif> - помощь при вставке картинки
<p><img border=1 src=".$www_design."e2/link.gif> - помощь при оформлении линка: выделите мышкой линк, и он обработается
<p><img border=1 src=".$www_design."e2/ljvideo.gif> - помощь при вставке видео
<p><img border=1 src=".$www_design."e2/tableb_1.gif>
<img border=1 src=".$www_design."e2/tableb_r.gif>
<img border=1 src=".$www_design."e2/tableb1.gif>
<img border=1 src=".$www_design."e2/tableb2.gif>
<img border=1 src=".$www_design."e2/tableb3.gif>
<img border=1 src=".$www_design."e2/tableb_pre.gif> - разные виды таблиц
";


$s="<div align=justify>".str_replace(array("\n\n","\n"),array("<p>"," "),$s)."</div>";

?>
