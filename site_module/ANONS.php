<?php

/* ����� ������ ����� ����� ��������� � � ���������

��������� ������������� ����� ����� �� ����� (� ����� � ������ � http://). ��������������, ��� ��� ������ �����
����� ������ ����� ������� ������ ������� ����� ����� (��� ����������� �� ����� �����).
��� ����� ������������� �������, ������ ����� ������� ������ �������, ������ ��� ������ �� ������� � �� �����,

{_FOTOS: WIDTH=150
http://lleo.aha.ru/blog/2010/05/26-2098.jpg �����-�� ������
http://lleo.aha.ru/blog/2010/05/30-2114.jpg ���� �������� Grassy, ������� � ����� ����� ������������ �����
http://lleo.aha.ru/blog/2010/05/30-2112.jpg ���� �������� Grassy, ������� � �����
http://lleo.aha.ru/blog/2010/05/LLeo_Vysotsky.jpg ����� ���������� ������� � ������� ��������
http://lleo.aha.ru/blog/2010/05/Screenshot0022.jpg � ��� �������� � ���� �������
_}
*/

include_once $GLOBALS['include_sys']."_onetext.php";

function ANONS($e) { $oldarticle=$GLOBALS['article'];

$conf=array_merge(array(
'mode'=>'all', // ��� �������: 'all' - ���, 'blog' - ������ ����, 'page' - ����������� ��������
'unread'=>0, // �� �������� �����������
'podzamok'=>0, // ������ ��������
'tags'=>'', // ������� �� ���� (���� ����� - �� ��� ������), ���� ����� ����������� ����� �������
'tags_and'=>'OR', // ����������� ����� OR ��� AND
'limit'=>20, // ������������ ����� �������
'days'=>0, // ���������� ���������� N ����� (0 - ��� �����������)
'sort'=>'date', // ����������: 'date' - �� ���� �������, 'update' - �� ���������� ����������
'sortx'=>'DESC', // ����������: 'DESC' - ����� ����� ������; ���� '' - �� ��������
'length'=>200, // ����� ���� � ������� ������, ���� 0 - �� ���� �������
'media'=>0, // 0 - ����� ����� (��� �������, �������� � �.�.)
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

	if($conf['media']) { // ����� ������
		$body=preg_replace("/(<img[^>]+src\=[\'\"]*)([^\/\:]{4,})/si","$1".$GLOBALS['wwwhost'].$Y."/".$M."/$2",$body);
	} else { // ����� ���������
		$body=str_replace('<',' <',$body); // �������� ������� ����� ��������� ����� 
		$body=strip_tags($body); // ��������� ��� ����
		$body=str_ireplace('&nbsp;',' ',$body);
		$body=preg_replace("/\s+/s",' ',$body); // ������ ������� ������� � ��������
		$body=trim($body);
		if($conf['length']!=0) $body=substr($body,0,$conf['length']+strcspn($body,' ,:;.',$conf['length'])); // ��������
	}

	} else $Body='';

$s.=mper(str_replace("\\n","\n",$conf['template']),array(
'Body'=>$body,
'Header'=>$p["Header"],
'link'=>get_link_($p["Date"]), // �������� ������ �� ������
'num'=>$p["num"],
'Y'=>$Y,'M'=>$M,'D'=>$D
// ,'edit'=>($GLOBALS['admin']?"<img style='margin: 0 10px 0 10px;' class=knop onClick=\"majax('editor.php',{a:'editform',num:'".$p['num']."',comments:(idd('commpresent')?1:0)})\" src='".$GLOBALS['www_design']."e3/color_line.png' alt='editor'>":'')
));

}

$GLOBALS['article'] = $oldarticle;

return $s;
}
?>