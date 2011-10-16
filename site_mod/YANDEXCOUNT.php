<?php /* счетчик ссылок с Яндекса

Модуль YANDEXCOUNT бегает на Яндекс и считает число ссылок на эту страницу.

Можно использовать без параметров, а по параметры умолчанию выглядят так:

{_YANDEXCOUNT:

text=<span style='color: red; font-size:8pt; text-decorate: bold' title='Ссылки на эту заметку по Яндексу<br>последнее обновление {time}'>Yandex: <a href='http://blogs.yandex.ru/search.xml?ft=all&holdres=mark&link={urlencode}' id='yandexcount'>{count}</a></span>

update=<input id='yandexcount_update' style='font-size:8px;' type=button value='update' onclick="majax('module.php',{mod:'YANDEXCOUNT',a:'update',num:'{num}'})">

_}
*/

$YANDEXCOUNT_admintime=60;
$YANDEXCOUNT_time=86400;

function YANDEXCOUNT_ajax($R) { // if(!$GLOBALS['admin']) idie('admin only');
	 $time=time(); $num=intval($_REQUEST['num']); if(!$num) idie("Неверный запрос num='$num'");

	$t=ms("SELECT `time` FROM `yablogs_count` WHERE `num`='$num'","_l");
	if($t!==false and $GLOBALS['YANDEXCOUNT_admintime']>($time-$t)) idie("Так часто проверять не надо");

	$Date=ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `num`='$num'","_l");
//	$f="http://blogs.yandex.ru/search.rss?ft=all&holdres=mark&link=".urlencode(h(get_link($Date)));
	$f="http://blogs.yandex.ru/search.xml?ft=all&holdres=mark&numdoc=1&link=".urlencode(h(get_link($Date)));
	$s=file_get_contents($f);

// "/<yablogs\:count>(\d+)<\/yablogs:count>/si"
	if(!preg_match(
wu("/<div class=\"b-search-found\">Показан.+сообщени.+?из <b>(\d+)<\/b> найденных/s")
,$s,$m)) {

	if(!stristr($s,'<div class="b-error">')) idie("page error:<br><a href='$f'>$f</a>".h(uw($s)));
	$count=0;
	
	} else $count=intval($m[1]);

	msq_add_update('yablogs_count',array('count'=>$count,'time'=>$time,'num'=>$num),'num');
	return "zabil('yandexcount','$count');zakryl('yandexcount_update');";
}

function YANDEXCOUNT($e) { 

$conf=array_merge(array(
'text'=>"<span style='color: red; font-size:8pt; text-decorate: bold' title='Ссылки на эту заметку по Яндексу<br>последнее обновление {time}'>Yandex: <a href='http://blogs.yandex.ru/search.xml?ft=all&holdres=mark&link={urlencode}' id='yandexcount'>{count}</a></span>",
'update'=>"<input id='yandexcount_update' style='font-size:8px;' type=button value='update' onclick=\"majax('module.php',{mod:'YANDEXCOUNT',a:'update',num:'{num}'})\">"
),parse_e_conf($e));

	$p=ms("SELECT `count`,`time` FROM `yablogs_count` WHERE `num`='".$GLOBALS['article']['num']."'","_1");
	if($p==false) $p=array('count'=>0,'time'=>0);
$t=time()-$p['time'];
return mper($conf['text'].(( ($GLOBALS['admin'] and $GLOBALS['YANDEXCOUNT_admintime']<$t) or ($GLOBALS['YANDEXCOUNT_time'] < $t) )?$conf['update']:''),array(
	'text'=>$conf['text'],
	'urlencode'=>urlencode(get_link($GLOBALS['article']['Date'])),
	'num'=>$GLOBALS['article']['num'],
	'count'=>$p['count'],
	'time'=>date("Y-m-d H:i:s",$p['time'])
));

}

?>