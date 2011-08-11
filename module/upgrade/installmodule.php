<?php if(!function_exists('h')) die("Error 404"); // неправильно запрошенный скрипт - нахуй
// Проинсталлировать модули

foreach(array(

	'install'=>'INSTALL',
	'pravki'=>'PRAVKI',
	'comm'=>'PAGE_COMM',
	'imbload'=>'IMBLOAD',
	'contents'=>'CONTENTS',
	'haship'=>'HASHIP',
	'fido'=>'FIDO'

) as $n=>$l) { $action='createmodule_'.$n;
	if($PEST['action']==$action) {



		msq_add('dnevnik_zapisi',array(
'Date'=>e($n),
'Header'=>'',
'Body'=>e("{_".$l.":_}"),
'Access'=>'all',
'DateUpdate'=>time(),
'DateDatetime'=>0,
'DateDate'=>0
// ,'opt'=>e(ser(array('Comment'=>'disabled','autoformat'=>'no','autokaw'=>'no','template'=>'blank')))
));

$s.="module <a href='".$GLOBALS['www_host'].$n."'>".$GLOBALS['www_host'].$n."</a> created".$GLOBALS['msqe'];

return;
	}

if(false===ms("SELECT `Date` FROM `dnevnik_zapisi` WHERE `DateDate`='0' AND `Body` LIKE '%".e("{_".$l).":%'","_l")
) $s .= admin_kletka('action',"создать страницу <b>/$n</b> с модулем {_$l:_}",$action);
}

?>