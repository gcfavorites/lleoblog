<?php // ����������� ������ � ��������� - ���� �������� � $Date

function onetext($p,$q=1) { global $wwwhost,$unic;

	$GLOBALS['article']=$p;

	$s=$p["Body"];

//	// ��������� �����

	if($q&&$unic) {
		$msqe_old=$GLOBALS['msqe']; // �������� ����������� ������
		msq_add("dnevnik_posetil",array('unic'=>$unic,'url'=>$p['num'],'date'=>time())); // ���� ���� - �� ������, � ���� ������, ��� �� �����
		$GLOBALS['msqe']=$msqe_old; // ����������� ������ (��� ����� ���������)
	}

	$s=modules($s); // ��������� site

	if(isset($_GET['search'])) $s=search_podsveti_body($s); // ��������� ���������� ����


if(isset($_GET['mode']) and ($_GET['mode']=='mudoslov' or $_GET['mode']=='mudoslov_rating')) {
        $ara=explode("\n",file_get_contents($GLOBALS['host_design'].'mudoslov.txt'));
        foreach($ara as $m) { $m=trim($m); if($m!='') { $_GET['search']=$m; $s=search_podsveti_body($s); }}
}
/*
//} elseif($_GET['mode']=='hash') { include_once $include_sys."_hashdata2.php"; $article['Body'] = hashflash($article['Body']);

} elseif( $login!='corwin' && !$podzamok && !$admin && $_GET['mode']!='h' ) { // hashdata ��� �����
	$article['Body'] = str_replace(array('&nbsp;','&copy;','$mdash;','&laquo','&raquo;'),array(chr(160),chr(169),chr(151),chr(171),chr(187)),$article['Body']);
	//include_once $include_sys."_hashdata2.php"; $pa=hashinit();
	// $article['Body'] = hashdata($article['Body'],$pa);
}
*/

// ���������� ������������������
if($p['autoformat']!='no') $s=str_replace(
	array("\n\n","\n"),($p['autoformat']=='p'?array("<p>","<br>"):array("<p class=pd>","<p class=d>")),
str_replace("\n ","\n<p class=z>","\n\n".$s));

// return "<div id='Body_".$article['num']."'".

if($p['Access']=='all') return $s;
return "<div style=\"background-color:".$GLOBALS['podzamcolor'].";\">".zamok($p['Access']).$s."</div>";

}

?>
