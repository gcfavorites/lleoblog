<?php // Отображение статьи с каментами - дата передана в $Date

function onetext($p,$q=1) { global $wwwhost,$unic;

	$GLOBALS['article']=$p;

	$s=$p["Body"];

//	// Посчитать юзера

	if($q&&$unic) {
		$msqe_old=$GLOBALS['msqe']; // запомним накопленные ошибки
		msq_add("dnevnik_posetil",array('unic'=>$unic,'url'=>$p['num'],'date'=>time())); // если есть - не внесет, а даст ошибку, нам не важно
		$GLOBALS['msqe']=$msqe_old; // восстановим ошибки (без учета последней)
	}

	$s=modules($s); // процедуры site

	if(isset($_GET['search'])) $s=search_podsveti_body($s); // подсветка выделенных слов


if(isset($_GET['mode']) and ($_GET['mode']=='mudoslov' or $_GET['mode']=='mudoslov_rating')) {
        $ara=explode("\n",file_get_contents($GLOBALS['host_design'].'mudoslov.txt'));
        foreach($ara as $m) { $m=trim($m); if($m!='') { $_GET['search']=$m; $s=search_podsveti_body($s); }}
}
/*
//} elseif($_GET['mode']=='hash') { include_once $include_sys."_hashdata2.php"; $article['Body'] = hashflash($article['Body']);

} elseif( $login!='corwin' && !$podzamok && !$admin && $_GET['mode']!='h' ) { // hashdata для чужих
	$article['Body'] = str_replace(array('&nbsp;','&copy;','$mdash;','&laquo','&raquo;'),array(chr(160),chr(169),chr(151),chr(171),chr(187)),$article['Body']);
	//include_once $include_sys."_hashdata2.php"; $pa=hashinit();
	// $article['Body'] = hashdata($article['Body'],$pa);
}
*/

// произвести автоформатирование
if($p['autoformat']!='no') $s=str_replace(
	array("\n\n","\n"),($p['autoformat']=='p'?array("<p>","<br>"):array("<p class=pd>","<p class=d>")),
str_replace("\n ","\n<p class=z>","\n\n".$s));

// return "<div id='Body_".$article['num']."'".

if($p['Access']=='all') return $s;
return "<div style=\"background-color:".$GLOBALS['podzamcolor'].";\">".zamok($p['Access']).$s."</div>";

}

?>
