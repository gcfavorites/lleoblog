<?php

/*
$commentary=nl2br(htmlspecialchars($commentary));
$commentary=AddBB($commentary);
$commentary="\n$commentary\n";
$commentary=hyperlink($commentary);
$commentary=trim($commentary,"\n");
*/

// die("1");


function link_lj_var($t) {
	$t1=str_ireplace('&quot;','"',$t[1]);
	$t2=str_ireplace('&quot;','"',$t[2]);
	$t1=trim($t1,"'\"\n ");
	$t2=str_ireplace('&lt;wbr&gt;&lt;/wbr&gt;','',trim($t2,"'\"\n "));
	if($t2==$t1) return $t1;
//	idie($t2." ".$t1);
	return $t2." (".$t1.")";
}

function AddBB($var) {


	$var=preg_replace_callback("/&lt;a href=(.*?)&gt;(.*?)&lt;\/a&gt;/si","link_lj_var",$var);

//              $text=
//<a href="http://www.handhelds.org/moin/moin.cgi/GeneratingSyntheticX11Events">
//http://www.handhelds.org/moin/moin.c<wbr></wbr>gi/GeneratingSyntheticX11Events</a>























	$var = str_replace('&quot;','"', $var);

        $search = array(
                '/\[b\](.*?)\[\/b\]/is',
                '/&lt;b&gt;(.*?)&lt;\/b&gt;/is',
                '/&lt;strong&gt;(.*?)&lt;\/strong&gt;/is',

                '/\[i\](.*?)\[\/i\]/is',
                '/&lt;i&gt;(.*?)&lt;\/i&gt;/is',
                '/&lt;em&gt;(.*?)&lt;\/em&gt;/is',

                '/\[u\](.*?)\[\/u\]/is',
                '/&lt;u&gt;(.*?)&lt;\/u&gt;/is',

                '/\[s\](.*?)\[\/s\]/is',
                '/&lt;s&gt;(.*?)&lt;\/s&gt;/is',

                '/&lt;quote&gt;(.*?)&lt;\/quote&gt;/is',
                '/&lt;cite&gt;(.*?)&lt;\/cite&gt;/is',

                '/&gt;([^\&\n<]+)/is',

                '/\[img\](.*?)\[\/img\]/is',
                '/\[url\](.*?)\[\/url\]/is',
                '/\[url\=(.*?)\](.*?)\[\/url\]/is'
                );

        $replace = array(
		'<b>$1</b>',
                '<b>$1</b>',
                '<b>$1</b>',

                '<i>$1</i>',
                '<i>$1</i>',
                '<i>$1</i>',

                '<u>$1</u>',
                '<u>$1</u>',

                '<s>$1</s>',
                '<s>$1</s>',

                '<i><font color=gray>$1</font></i>',
                '<i><font color=gray>$1</font></i>',

                '<font color=gray>&gt;$1</font>',

                ' $1 ',		// '<img src="$1" />',
                ' $1 ',		// '<a href="$1">$1</a>',
		'<a href=\'$1\'>$2</a>'
                );

        $var = preg_replace ($search, $replace, $var);
	$var = str_replace('"','&quot;',$var);
        return $var;
}


function hyperlink($s) { 
//return preg_replace_callback("/([\s>\(\:])(([a-zA-Z]+:\/\/|(www\.))([a-z][a-z0-9_\.\-]*[a-z]{2,6})([a-zA-Z0-9!#\$%&\(\)\*\+,\-\.\/:;=\?\@\[\]\\^_`\{\}\|~]*[a-zA-Z0-9\/]))([\s<,:\.\)\!\?])/i","url_present", $s); }
return preg_replace_callback("/([\s>\(\:])(([a-zA-Z]+:\/\/|(www\.))([a-z][a-z0-9_\.\-]*[a-z]{2,6})([a-zA-Z0-9\!\#\$\%\&\(\)\*\+\,\-\.\/\:\;\=\?\@\[\]\\\^\_\`\{\}\|\~]*[a-zA-Z0-9\/]))([\s<,:\.\;\)\!\?\=])/s","url_present", $s); }


function url_present($p) {
	if($p[3]=='www.') $p[2]='http://'.$p[2];
	$l=$p[6];
	if(stristr($l,'.jpg') or stristr($l,'.gif') or stristr($l,'.jpeg') or stristr($l,'.png') 
or stristr($p[0],'http://pix2.blogs.yandex.net/getavatar')
)
		$s='<img src="'.$p[2].'"'.(strstr($l,'&amp;prefix=normal')?' align=left hspace=10':'').'>';
	else $s='<noindex><a href="'.$p[2].'" rel="nofollow">'.reduceurl($p[3].$p[5].$l,60).'</a></noindex>';
	return $p[1].$s.$p[7];
}

function reduceurl($s,$l) { if(strlen($s) > $l) $s=substr($s,0,$l)."[...]"; return $s; }

?>