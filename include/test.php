<?php // ПРОВЕРОЧНЫЙ СКРИПТ

// полезные переменные:
// $article['Header'] // заголовок
// $article["Date"] // дата (название) этой заметки
// $article["DateTime"] // дата заметки в формате time
// $article["view_counter"] // счетчик посещений
// $article["Prev"] // дата следующей заметки
// $article["Next"] // дата предыдущей заметки
// $article["DateUpdate"] // время последнего обновления заметки
// $_PAGE["coments"] // простыня комментариев (будьте внимательны: $_PAGE, а не $article)

// 1. Простой пример: заменим везде фразу {DateUpdate} на реальную дату обновления
$d=date("Y-m-d H:i:s",$article["DateUpdate"]);
$article['Body'] = str_ireplace("{DateUpdate}",$d,$article['Body']); // в заметке
$_PAGE["coments"] = str_ireplace("{DateUpdate}",$d,$_PAGE["coments"]); // и в комментариях

// 2. Вычленим в тексте заметки команду "{замени ... на ...}" и выполним ее
preg_match("/(\{замени ([^\}\s]+) на ([^\}\s]+)\})/si",$article['Body'],$m);
// $article['Body'] = str_replace($m[1],'',$article['Body']); // уберем из текста саму строчку "{zameni ...}"
$article['Body'] = str_replace($m[2],$m[3],$article['Body']); // сделаем замену в заметке
$_PAGE["coments"] = str_replace($m[2],$m[3],$_PAGE["coments"]); // и в комментариях

// 3. пример работы с датой
// если время больше 20:50, но меньше 21:00, то это время смотреть передачу "Спокойной ночи малыши", вставить в конец текста ролик:

if( // $admin ||
date("Hi")>"2050" && date("Hi")<"2100" ) $article['Body'] .= '<p><center>
<object type="application/x-shockwave-flash" data="http://deti.kiho.in.ua/data/flvplayer.swf" height="596" width="704"><param name="bgcolor" value="#FFFFFF" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="movie" value="http://deti.kiho.in.ua/data/flvplayer.swf" /><param name="FlashVars" value="way=http://teramult.org.ua/f/flv8/1988_su_spokoinoi.nochi.malyshi/spokoinoi.nochi.malyshi.flv&amp;swf=http://deti.kiho.in.ua/data/flvplayer.swf&amp;w=704&amp;h=528&amp;pic=http://deti.kiho.in.ua/data/zaglushka.gif&amp;autoplay=0&amp;tools=2&amp;skin=grey&amp;volume=70&amp;q=&amp;comment=" /></object>
</center>';




/*
 PS: Кстати, если вам надо добавить что-то в поле скриптов или стилей, просто используйте команду, указав ей текст в аргументе:

SCRIPTS("
var x=0;

function xss {
	alert(0);
}
");


STYLES("p {border: 1ps solid red;}");
*/

?>