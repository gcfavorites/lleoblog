<?php // rss заметок дневника

if(!isset($admin_name)) die("Error 404"); // неправильно запрошенный скрипт - нахуй

//$RSSZ_skip = 10;
//$RSSZ_mode = 1;

$zamok="<img src=".$www_design."e/podzamok.gif>&nbsp;";

header("Content-Type: text/xml; charset=".$wwwcharset);

$skip=intval($_GET['skip']);

$pp = ms("SELECT `Date`,`Body`,`Header`,`DateUpdate`,`Access` FROM `dnevnik_zapisi` ".WHERE(
"`Date` LIKE '____/__/%'"
)." ORDER BY `Date` DESC LIMIT ".$skip.",".$RSSZ_skip,"_a",$ttl);

$s.="<?xml version='1.0' encoding='".$wwwcharset."'?>
<rss version='2.0' xmlns:ya='http://blogs.yandex.ru/yarss/' xmlns:wfw='http://wellformedweb.org/CommentAPI/'>

<channel>
  <title>Леонид Каганов: второй блог - о веб-разработке</title>
  <link>".$httphost."</link>
  <description>Блог lleo о разработке блогодвижка</description>
  <generator>LLeoBlog 1.0</generator>
  <wfw:commentRss>".$httphost."rssc"."</wfw:commentRss>
  <ya:more>".$MYPAGE."?skip=".($skip+$RSSZ_skip)."</ya:more>
  <image>
    <url>".$www_design."userpick.jpg"."</url>
    <width>120</width>
    <height>155</height>
  </image>
";

$lastupdate=0; foreach($pp as $p) {
	$lastupdate = max($lastupdate,$p["DateUpdate"]);
	$link=$httphost.$p["Date"].".html"; // полная ссылка на статью

	$p['Body']=RSS_zaban($p['Body']); // обработать забаненных

	if($RSSZ_mode==1) $p['Body']=RSSZ_mode1($p['Body']); // если в настройках указано не давать полный RSS

	$Header=$p["Date"].($p["Header"]?" - ".$p["Header"]:"");

	$Body=($p['Access']=='podzamok'?$zamok:'').($p['Access']=='admin'?$zamok.$zamok.$zamok.$zamok.$zamok:'').$p['Body'];

$s .= "\n<item>
	<guid isPermaLink='true'>".$link."</guid>
	<author>http://lleo.aha.ru</author>
	<pubDate>".date("r", $p["DateUpdate"])."</pubDate>
	<link>".$link."</link>
	<description>".htmlspecialchars($Body)."</description>
	<title>".htmlspecialchars($Header)."</title>
	<comments>".$link."</comments>
</item>\n";
}

$s .= "\n</channel>\n\n</rss>\n";

check_if_modified($lastupdate,"$lastupdate"); // время последней модификации (оно же как ETag)

die($s);

//=========================================================================================================

// процедура времени последней модификации
function check_if_modified($date, $etag = NULL) { $cache = NULL;
        if( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) $cache = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $date;
        if( $cache !== false && isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {
                $cache = $_SERVER['HTTP_IF_NONE_MATCH'] == '*' || isset( $etag )
                        && in_array( '"'.$etag.'"', explode( ', ',$_SERVER['HTTP_IF_NONE_MATCH'] ) );
                }
        if($cache) { header('HTTP/1.1 304 Not Modified'); ob_clean(); exit(); }
        else {
                header( 'Last-Modified: '.date( DATE_RFC822, $date ) );
                if( isset( $etag ) ) header( 'ETag: "'.$etag.'"' );
        }
}


// функция подготовки RSS, если он неполный

function RSS_zaban($s) { global $admin; 	// если это забаненные мудаки, воры и роботы
	if(	$_SERVER["REMOTE_ADDR"]=='78.46.74.53' // http://feedex.net/view/lleo.aha.ru/dnevnik/rss.xml
		|| strstr($_SERVER["HTTP_USER_AGENT"],'eedjack') // BRO='Feedjack 0.9.10 - http://www.feedjack.org/'
		// || $_SERVER["REMOTE_ADDR"]=='79.165.191.215' // Feed43.com? http://feeds.feedburner.com/lleo ?
		|| strstr($_SERVER["HTTP_USER_AGENT"],'Wget/') // а нехуй вгетом качать!
		|| $_SERVER["REMOTE_ADDR"]=='140.113.88.218' || strstr($_SERVER["HTTP_USER_AGENT"],'Yahoo Pipes')
	) return "Для вас полный текст заметки <a href=".mkna("читатель RSS!",
"вы настолько обленились, что вам лень ткнуть в ссылку и вы пытаетесь читать RSS пиратскими способами.",
"ходите и читайте дневник по-человечески.").">находится здесь</a>";
	return $s;
}


function RSSZ_mode1($s) { global $admin;
	$sim=ereg_replace("<[^>]*>",'',html_entity_decode($s)); // удалить все теги
	$sim=ereg_replace("{[^}]*}",'',$sim); // удалить все фичи в фигурных скобках
	$bukv=round(((strlen($sim))+99 )/100)*100;
	$sim=trim(preg_replace("/^(.{260}[^\.\?\!]*[\.\!\?]).*$/si","$1",$sim))
	."... [<a href='$zalink'>читать полностью: примерно $bukv символов</a>]\n\n";
	// if(strstr($s,'<img')) $sim .= " + картинки или фотки";
	// if(strstr($s,'<script')) $sim .= " + скрипты какие-то";
	// if(strstr($s,'<object') || strstr($s,'<OBJECT')) $sim .= " + флэш вставлен (может, ролик или музыка?)";
	// $sim .= ".";

	// если это Яндекс

	if( strstr($_SERVER["HTTP_USER_AGENT"],'Yandex') || $_SERVER["REMOTE_ADDR"]=='78.110.50.100' ) return $sim."
\n<p><b>Пытаетесь читать мой дневник через RSS-ленту Яндекса? Здесь лишь грубая текстовая выжимка
для индексации в поиске - с битыми абзацами, без фоток, картинок, верстки, роликов, скриптов, голосований и прочего.
Настоящую версию моего дневника вы можете прочесть только на моем сайте (причины описаны <a href=".$httphost."/about_rss>здесь</a>).</b>";

	// если это робот трансляции ЖЖ

	// 204.9.177.18 (): , 'LiveJournal.com (webmaster@livejournal.com; for http://www.livejournal.com/users/lleo_run/; 488 readers)'
	if(strstr($_SERVER["HTTP_USER_AGENT"],'LiveJournal.com') )
	return $sim."\nЧитатели ЖЖ-трансляции! Оставляйте комментарии только на моем сайте, иначе я их не увижу.";

	return $sim;
}

function mkna($name,$prichina,$delat) { // создать ссылку посыла нахуй
	$stroka=$name."%".$prichina."%".$delat;	$stroka=base64_encode($stroka);
	$stroka=str_replace("=","",$stroka); $stroka=str_replace("/","-",$stroka);
	return "http://lleo.aha.ru/na/?".$stroka;
}

?>
