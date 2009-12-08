<?php // Отображение статьи с каментами - дата передана в $Date

function onetext($s) { global $wwwhost,$IP;

	// Посчитать юзера
	mysql_query("UPDATE `dnevnik_zapisi` SET view_counter=view_counter+1, last_view_ip='".e($IP)."' WHERE `num`='".$article["num"]."' AND last_view_ip!='".e($IP)."'");

	$s=modules($s); // процедуры site

	if($_GET['search']) $s=search_podsveti_body($s); // подсветка выделенных слов

/*
if($_GET['mode']=='mudoslov') {
        $ara=explode("\n",file_get_contents('mudoslov.txt'));
        foreach($ara as $m) { $m=trim($m); if($m!='') {
			$_GET['search']=$m;
			$article['Body']=search_podsveti_body($article['Body']);
			}}

//} elseif($_GET['mode']=='hash') { include_once $include_sys."_hashdata2.php"; $article['Body'] = hashflash($article['Body']);

} elseif( $login!='corwin' && !$podzamok && !$admin && $_GET['mode']!='h' ) { // hashdata для чужих
	$article['Body'] = str_replace(array('&nbsp;','&copy;','$mdash;','&laquo','&raquo;'),array(chr(160),chr(169),chr(151),chr(171),chr(187)),$article['Body']);
	//include_once $include_sys."_hashdata2.php"; $pa=hashinit();
	// $article['Body'] = hashdata($article['Body'],$pa);
}
*/

	return $s;
}

?>
