<?php /* DAT

Удобное создание сущностей по темплейту. В каждой строке параметры разделяются |, первый
параметр называется {0}, второй {1} и т.д. Каждая строка заменяется на темплейт.

{_DAT: template=<p><a href='/dnevnik/{1}.html'>{1} ? {2}</a><br>{@MP3: http://lleo.me/audio/f5/{0}@}
facebook.mp3	| 2011/10/17 | Ода социальным сетям
konoplya.mp3	| 2011/10/03 | Ода газетным новостям
china.mp3	| 2011/09/26 | Ода про рис и репу
shlagbaum.mp3	| 2011/09/19 | Ода шлагбауму
_}
*/

function DAT($e) {
	$conf=array_merge(array('template'=>'{0} {1} {2} {3} {4} {5}'),parse_e_conf($e));

	$s=''; foreach(explode("\n",$conf['body']) as $l) { if(empty($l))continue; $a=explode('|',$l);
		foreach($a as $n=>$l) $a[$n]=trim($l,"\t\r\n ");
		$s.=mper($conf['template'],$a);
	}
	return str_replace(array('{@','@}'),array('{_','_}'),$s);
}
?>