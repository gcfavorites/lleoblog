<?php // ќтображение статьи с каментами - дата передана в $Date

function onetext($p) { global $wwwhost,$IP,$unic;

	$s=$p["Body"];

	// ѕосчитать юзера
	mysql_query("UPDATE `dnevnik_zapisi` SET view_counter=view_counter+1, last_view_ip='".e($IP)."' WHERE `num`='".e($p['num'])."' AND last_view_ip!='".e($IP)."'");
	if($unic) {
		$msqe_old=$GLOBALS['msqe']; // запомним накопленные ошибки
		msq_add("dnevnik_posetil",array('unic'=>$unic,'url'=>e($p['num']))); // если есть - не внесет, а даст ошибку, нам не важно
		$GLOBALS['msqe']=$msqe_old; // восстановим ошибки (без учета последней)
	}

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

} elseif( $login!='corwin' && !$podzamok && !$admin && $_GET['mode']!='h' ) { // hashdata дл€ чужих
	$article['Body'] = str_replace(array('&nbsp;','&copy;','$mdash;','&laquo','&raquo;'),array(chr(160),chr(169),chr(151),chr(171),chr(187)),$article['Body']);
	//include_once $include_sys."_hashdata2.php"; $pa=hashinit();
	// $article['Body'] = hashdata($article['Body'],$pa);
}
*/

// произвести автоформатирование
if($p['autoformat']=='no') return $s;
return str_replace(array("\n\n","\n"),($p['autoformat']=='p'?array("<p>","<br>"):array("<p class=pd>","<p class=d>")),"\n\n"

.str_replace("\n ","\n<p class=z>",$s)

);

}



function search_podsveti_body($a) {
        $a=preg_replace_callback("/>([^<]+)</si","search_p_body",'>'.$a.'<');
        $a=ltrim($a,'>'); $a=rtrim($a,'<');
        return $a;
} function search_p_body($r) { return '>'.str_ireplace2_body($_GET['search'],"<span class=search>","</span>",$r[1]).'<'; }


function str_ireplace2_body($search,$rep1,$rep2,$s){ $c=chr(1); $nashlo=array(); $x=strlen($search);
        $SEARCH=strtolower2_body($search);
        $S=strtolower2_body($s);
        while (($i=strpos($S,$SEARCH))!==false){
                $nashlo[]=substr($s,$i,$x);
                $s=substr_replace($s,$c,$i,$x);
                $S=substr_replace($S,$c,$i,$x);
        } foreach($nashlo as $l) $s=substr_replace($s,$rep1.$l.$rep2,strpos($s,$c),1);
        return $s;
}

function strtolower2_body($s){
        $s=strtr($s,'јЅ¬√ƒ≈®∆«»… ЋћЌќѕ–—“”‘’÷„Ўўџ№ЏЁёя','абвгдеЄжзийклмнопрстуфхцчшщыьъэю€'); // русские в строчные
        $s=strtr($s,'авсенкмортху','abcehkmoptxy'); // русские какие похожи - в латинские
        $s=strtolower($s); // латинские в строчные
        return $s;
}

?>
