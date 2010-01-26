<? // удаляем таблицу login, она нам не нужна более

$s.=msq_del_table('login',"Оно нахуй не нужно было.");

//$s .= msq_del_pole("dnevnik_zapisi","Prev","Оно нахуй не нужно, это была моя ошибка!");
//$s .= msq_add_index("dnevnik_zapisi","DateDatetime","(`DateDatetime`)","индекс нужен");

$s .= msq_add_pole("unic","timelast","timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP","время последнего захода");
$s .= msq_del_pole("unic","sc","Оно нахуй не нужно");
$s .= msq_del_pole("unic","bro","Оно нахуй не нужно");

$s .= msq_add_pole("unic","time_reg","int(11) NOT NULL default '0'","время регистрации");
$s .= msq_del_pole("unic","timereg","нужно другое");

//  `timereg` timestamp NOT NULL default CURRENT_TIMESTAMP,
//  `DateTime` int(11) NOT NULL default '0',

// $s .= msq_del_pole("unic","bro","Оно нахуй не нужно");
//`timelast` ,
//`sc` varchar(32) NOT NULL,
//`bro` varchar(1024) NOT NULL,


?>