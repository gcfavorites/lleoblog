<?php

/* Вывод группы фоток через превьюшки и с подписями

Указываем относительный адрес фотки на сайте (а можно и полный с http://). Предполагается, что она залита средс
Через пробел можно указать строку подписи любой длины (она выровняется по длине блока).
Все фотки выстраиваются блоками, ширину блока полезно задать вручную, потому что движок не телепат и не знает,

{_FOTOS: WIDTH=150
http://lleo.aha.ru/blog/2010/05/26-2098.jpg Какие-то съемки
http://lleo.aha.ru/blog/2010/05/30-2114.jpg День рождения Grassy, шашлыки в парке около Борисовского пруда
http://lleo.aha.ru/blog/2010/05/30-2112.jpg День рождения Grassy, шашлыки в парке
http://lleo.aha.ru/blog/2010/05/LLeo_Vysotsky.jpg Алекс Тарнавский пошутил и прислал скриншот
http://lleo.aha.ru/blog/2010/05/Screenshot0022.jpg А это скриншот с моей мобилки
_}
*/

include_once $GLOBALS['include_sys']."_onetext.php";

function ANONS($e) { $oldarticle=$GLOBALS['article'];

$conf=array_merge(array(
'mode'=>'all', // тип выборки: 'all' - все, 'blog' - только блог, 'page' - статические страницы
'unread'=>0, // не читанные посетителем
'podzamok'=>0, // только подзамки
'tags'=>'', // выбрать по тэгу (если пусто - то все записи), тэги можно перечислить через запятую
'tags_and'=>'OR', // объединение тэгов OR или AND
'limit'=>20, // максимальное число записей
'days'=>0, // ограничить последними N днями (0 - без ограничений)
'sort'=>'date', // сортировка: 'date' - по дате заметки, 'update' - по последнему обновлению
'sortx'=>'DESC', // сортировка: 'DESC' - самый новый сверху; если '' - то наоборот
'length'=>200, // число букв в отрывке текста, если 0 - то тест целиком
'media'=>0, // 0 - голый текст (без верстки, картинок и т.п.)
'template'=>"<div style='text-align:left; padding: 10px 0 10px 0; font-size:12px;'>"
// ."{edit}"
."<b>{Y}-{M}-{D}: {Header}</b>"
."<br>{Body}&nbsp;<a href='{link}'>(...)</a>"
."</div>\n\n"
),parse_e_conf($e));

$bodyneed=strstr($conf['template'],'{Body}');

$wher=array();
$on=array();
$as=array("`dnevnik_zapisi` as z");

if($conf['mode']=='blog') $wher[]="z.`DateDatetime`!='0'";
elseif($conf['mode']=='page') { $wher[]="z.`DateDatetime`='0'"; $conf['days']=0; }

if($conf['days']!=0) $wher[]="z.`DateDatetime`>='".(time()-$conf['days']*86400)."'";

if($conf['podzamok']) {
	if($GLOBALS['podzamok']) $wher[]="z.`Access`='podzamok'";
	else return '';
}

if($conf['unread']) {
	$wher[]="(z.`num` NOT IN (SELECT `url` FROM `dnevnik_posetil` WHERE `unic`='".$GLOBALS['unic']."'))";
//	$wher[]="p.`url` IS NULL";
//	$as[]="LEFT JOIN `dnevnik_posetil` as p";
//	$on[]="z.`num`=p.`url`";
}

if($conf['tags']=='') $mstag_sel=$mstag_gr='';
else { $a=explode(',',$conf['tags']);
	$t=array(); foreach($a as $l) $t[]="t.`tag`='".e(trim($l))."'";
		$wher[]="(".implode(' '.e($conf['tags_and']).' ',$t).")";
		$mstag_sel=", GROUP_CONCAT(t.`tag` SEPARATOR ';') as `t`";
		$as[]="INNER JOIN `dnevnik_tags` as t";
		$on[]="t.`num`=z.`num`";
		$mstag_gr="GROUP BY z.`num`";
}

$sq="SELECT z.`opt`,z.`Date`,".($bodyneed?"z.`Body`,":'')."z.`Header`,z.`Access`,z.`num` $mstag_sel FROM ".implode(" ",$as)." "
.(sizeof($on)?" ON (".implode(' AND ',$on).") ":'')
.WHERE(implode(' AND ',$wher),'z.')
." $mstag_gr"
." ORDER BY z.`".($conf['sort']=='date'?'DateDatetime':'DateUpdate')."` ".($conf['sortx']=='DESC'?'DESC':'')
.($conf['limit']==0?'':" LIMIT ".e($conf['limit']));

$pp=ms($sq,"_a");
// dier($pp,$sq);

$s=''; if(sizeof($pp)) foreach($pp as $p) { if($p['num']==$oldarticle['num']) continue;
	$p=mkzopt($p); $GLOBALS['article']=$p;
	list($Y,$M,$D) = explode('/', $p['Date'], 3); $article["Day"]=substr($article["Day"],0,2);

	if($bodyneed) {
		$body=onetext($p,0);

	if($conf['media']) { // текст полный
		$body=preg_replace("/(<img[^>]+src\=[\'\"]*)([^\/\:]{4,})/si","$1".$GLOBALS['wwwhost'].$Y."/".$M."/$2",$body);
	} else { // текст урезанный
		$body=str_replace('<',' <',$body); // добавить пробелы перед вычисткой тэгов 
		$body=strip_tags($body); // вычистить все тэги
		$body=str_ireplace('&nbsp;',' ',$body);
		$body=preg_replace("/\s+/s",' ',$body); // убрать двойные пробелы и переносы
		$body=trim($body);
		if($conf['length']!=0) $body=substr($body,0,$conf['length']+strcspn($body,' ,:;.',$conf['length'])); // обрезать
	}

	} else $Body='';

$s.=mper(str_replace("\\n","\n",$conf['template']),array(
'Body'=>$body,
'Header'=>$p["Header"],
'link'=>get_link_($p["Date"]), // неполная ссылка на статью
'num'=>$p["num"],
'Y'=>$Y,'M'=>$M,'D'=>$D
// ,'edit'=>($GLOBALS['admin']?"<img style='margin: 0 10px 0 10px;' class=knop onClick=\"majax('editor.php',{a:'editform',num:'".$p['num']."',comments:(idd('commpresent')?1:0)})\" src='".$GLOBALS['www_design']."e3/color_line.png' alt='editor'>":'')
));

}

$GLOBALS['article'] = $oldarticle;

return $s;
}
?>