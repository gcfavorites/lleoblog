<?php // rss ������� ��������

if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����

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
  <title>������ �������: ������ ���� - � ���-����������</title>
  <link>".$httphost."</link>
  <description>���� lleo � ���������� �����������</description>
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
	$link=$httphost.$p["Date"].".html"; // ������ ������ �� ������

	$p['Body']=RSS_zaban($p['Body']); // ���������� ����������

	if($RSSZ_mode==1) $p['Body']=RSSZ_mode1($p['Body']); // ���� � ���������� ������� �� ������ ������ RSS

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

check_if_modified($lastupdate,"$lastupdate"); // ����� ��������� ����������� (��� �� ��� ETag)

die($s);

//=========================================================================================================

// ��������� ������� ��������� �����������
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


// ������� ���������� RSS, ���� �� ��������

function RSS_zaban($s) { global $admin; 	// ���� ��� ���������� ������, ���� � ������
	if(	$_SERVER["REMOTE_ADDR"]=='78.46.74.53' // http://feedex.net/view/lleo.aha.ru/dnevnik/rss.xml
		|| strstr($_SERVER["HTTP_USER_AGENT"],'eedjack') // BRO='Feedjack 0.9.10 - http://www.feedjack.org/'
		// || $_SERVER["REMOTE_ADDR"]=='79.165.191.215' // Feed43.com? http://feeds.feedburner.com/lleo ?
		|| strstr($_SERVER["HTTP_USER_AGENT"],'Wget/') // � ����� ������ ������!
		|| $_SERVER["REMOTE_ADDR"]=='140.113.88.218' || strstr($_SERVER["HTTP_USER_AGENT"],'Yahoo Pipes')
	) return "��� ��� ������ ����� ������� <a href=".mkna("�������� RSS!",
"�� ��������� ����������, ��� ��� ���� ������ � ������ � �� ��������� ������ RSS ���������� ���������.",
"������ � ������� ������� ��-�����������.").">��������� �����</a>";
	return $s;
}


function RSSZ_mode1($s) { global $admin;
	$sim=ereg_replace("<[^>]*>",'',html_entity_decode($s)); // ������� ��� ����
	$sim=ereg_replace("{[^}]*}",'',$sim); // ������� ��� ���� � �������� �������
	$bukv=round(((strlen($sim))+99 )/100)*100;
	$sim=trim(preg_replace("/^(.{260}[^\.\?\!]*[\.\!\?]).*$/si","$1",$sim))
	."... [<a href='$zalink'>������ ���������: �������� $bukv ��������</a>]\n\n";
	// if(strstr($s,'<img')) $sim .= " + �������� ��� �����";
	// if(strstr($s,'<script')) $sim .= " + ������� �����-��";
	// if(strstr($s,'<object') || strstr($s,'<OBJECT')) $sim .= " + ���� �������� (�����, ����� ��� ������?)";
	// $sim .= ".";

	// ���� ��� ������

	if( strstr($_SERVER["HTTP_USER_AGENT"],'Yandex') || $_SERVER["REMOTE_ADDR"]=='78.110.50.100' ) return $sim."
\n<p><b>��������� ������ ��� ������� ����� RSS-����� �������? ����� ���� ������ ��������� �������
��� ���������� � ������ - � ������ ��������, ��� �����, ��������, �������, �������, ��������, ����������� � �������.
��������� ������ ����� �������� �� ������ �������� ������ �� ���� ����� (������� ������� <a href=".$httphost."/about_rss>�����</a>).</b>";

	// ���� ��� ����� ���������� ��

	// 204.9.177.18 (): , 'LiveJournal.com (webmaster@livejournal.com; for http://www.livejournal.com/users/lleo_run/; 488 readers)'
	if(strstr($_SERVER["HTTP_USER_AGENT"],'LiveJournal.com') )
	return $sim."\n�������� ��-����������! ���������� ����������� ������ �� ���� �����, ����� � �� �� �����.";

	return $sim;
}

function mkna($name,$prichina,$delat) { // ������� ������ ������ �����
	$stroka=$name."%".$prichina."%".$delat;	$stroka=base64_encode($stroka);
	$stroka=str_replace("=","",$stroka); $stroka=str_replace("/","-",$stroka);
	return "http://lleo.aha.ru/na/?".$stroka;
}

?>
