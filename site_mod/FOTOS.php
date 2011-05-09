<?php /* Вывод группы фоток через превьюшки и с подписями

Указываем относительный адрес фотки на сайте (а можно и полный с http://). Предполагается, что она залита средствами движка, поэтому там же есть папка pre/, где лежит для этой фотки одноименная превьюшка.
Через пробел можно указать строку подписи любой длины (она выровняется по длине блока).
Все фотки выстраиваются блоками, ширину блока полезно задать вручную, потому что движок не телепат и не знает, какого размера вы храните на этот раз превьюшки. По умолчанию ширина 210 (т.е. для превьюшек шириной 200), но можно указать командой "WIDTH nnn".

{_FOTOS: WIDTH=150
http://lleo.aha.ru/blog/2010/05/26-2098.jpg Какие-то съемки
http://lleo.aha.ru/blog/2010/05/30-2114.jpg День рождения Grassy, шашлыки в парке около Борисовского пруда
http://lleo.aha.ru/blog/2010/05/30-2112.jpg День рождения Grassy, шашлыки в парке
http://lleo.aha.ru/blog/2010/05/LLeo_Vysotsky.jpg Алекс Тарнавский пошутил и прислал скриншот
http://lleo.aha.ru/blog/2010/05/Screenshot0022.jpg А это скриншот с моей мобилки
_}
*/

if(!isset($GLOBALS['bigfoto'])) $GLOBALS['bigfoto']=0;
if(!isset($GLOBALS['bigfotopart'])) $GLOBALS['bigfotopart']=0;

function FOTOS($e) {
	$e=str_replace('WIDTH ','WIDTH=',$e); // для совместимости со старым говном
	$conf=array_merge(array('WIDTH'=>210),parse_e_conf($e));

	$pp=explode("\n",$e);
	$s=''; foreach($pp as $p) { $p=c($p); if($p=='' or strstr($p,'=')) continue;
		list($img,$txt)=explode(" ",$p,2); $img=c($img); $txt=c($txt);

		if(!strstr($img,'/')) {
			list($y,$m,)=explode('/',$GLOBALS['article']['Date'],3); if($y*$m) $img=$GLOBALS['wwwhost'].$y.'/'.$m.'/'.$img;
		}

		if(!strstr($img,',')) $epre=preg_replace("/^(.*?)\/([^\/]+)$/si","$1/pre/$2",$img);
		else list($img,$epre)=explode(',',$img);

		$s.="\n\n<a id='bigfot".($GLOBALS['bigfotopart'])."_".($GLOBALS['bigfoto'])."' href='".h($img)."' onclick='return bigfoto(".$GLOBALS['bigfoto'].",".$GLOBALS['bigfotopart'].")'><img src='".h($epre)."' border=0></a>"
		."<div class=r id='bigfott".($GLOBALS['bigfotopart'])."_".($GLOBALS['bigfoto']++)."'>".($txt!=''?$txt:'')."</div>";

	}

	if($GLOBALS['admin']) $s.="<div style='display:none' id='bigfotnum".($GLOBALS['bigfotopart'])."'>".$GLOBALS['article']['num']."</div>";
	$GLOBALS['bigfotopart']++;
	return "{_BLOKI: WIDTH=".$conf['WIDTH']."\n\n".$s."_}";
}

?>
