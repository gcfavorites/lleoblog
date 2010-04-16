<?php // выводит список тэгов заметки

function TAGS($e) { global $article; $s='';

$conf=array_merge(array(
'template'=>"<div style='font-size: 10pt; margin: 10px 0 10px 0; text-align:left;'>Тэги записи: {tags}</div>"
),parse_e_conf($e));

foreach(ms("SELECT `tag` FROM `dnevnik_tags` WHERE `num`='".$article['num']."' ORDER BY `tag`","_a") as $l)
$s.="<div class=ll onclick=\"majax('search.php',{a:'tag',tag:'".$l['tag']."'})\">".$l['tag']."</div>, ";
$s=trim($s,', ');
if($s!='') return mper($conf['template'],array('tags'=>$s)); return $s;
}

?>