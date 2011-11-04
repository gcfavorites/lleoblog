<?php /* ������ ������ � ��������� read.ru

� CONFIG.PHP ���������� ���������:
	$readru_partner = '601'; // ����� ����� ����������� ���������
	$readru_api = 'api2468713479'; // ����� api, �������� ������ �����
��������� ��� API, ���� ���������: http://read.ru/partner/api/

������ ������ ���������� � ������ � �������� �������, ����� �� ������� (��� ������ ������ ������ 3 ���)
�������� ������ ����� API.

����� ����� �������� ���������� �������������, ��� GET-������� ��� ������� �������� ?readru=1


���������:
id - ����� ������ �� read.ru
template - ������ HTML-����, � ������� ����������� �����������, ���������� �� ������:
{small} - url ��������� ��������
{big} - url ������� ��������
{link} - ������ �� ����� � ������ ������ ����������� ���������
��������� ������ �� ������� id=521730:
{id} = 521730
{name} = ���� �������� � ������� ���������
{price} = 166
{img} = 1
{type_id} = 1
{author} = ������� ������
{author.id} = 5493
{pubhouse} = ���
{pubhouse.id} = 996
{series} = �������� �������� (���)
{series.id} = 6430
{genre} = ������� ����������
{genre.id} = 96
{isbn} = "978-5-17-062202-3"
{ean} = "9785170622023"
{supply_date} = 1296421200
{supply_date_str} = �������
{tags} =
{is_new} =
{rate} = 0.00
{rate_count} = 0
{weight} = 340

�� ��������� template �����:
<p><div style="width:100px;text-align:center;" class=br><a href="{link}"><div><img src="{small}" title="{name}" border=0></div><div><b>{name}</b></a><br>������: {price}�</div></div>

������ ������ ������:
{_readru:id=521730_}

*/

function readru_ajax() { $url=RE('url'); //$url='http://lleo.me/0.php';
	if(!strstr($url,':')) return "idie('readru error url: `".h($url)."`')";
	$timeout=(RE('upd')?5:10*60*60);

	$s=urlget($url,$timeout); // ��� � 10 �����
	if($s===false) { if(!$GLOBALS['admin']) return '';
		$fu=urlget_name($url);
		if(!is_file($fu)) $s='file not';
		else $s="timeout: ".(time()-filemtime($fu))." / ".$timeout;
		return "salert('readru: $s',3000)";
	}

	$R=array();
	if($GLOBALS['wwwcharset']!='UTF-8') $s=iconv("UTF-8",$GLOBALS['wwwcharset']."//IGNORE",$s); // �� UTF8
	preg_match_all("/\"([a-z0-9\_]+)\":\s*[\"\[\{](.*?)[\"\}\]],/si",$s,$m);
	foreach($m[1] as $n=>$l) {
		if(!preg_match("/^\s*\"([a-z0-9\_]+)\"\s*:\s*\"\s*(.*?)\s*\"\s*$/si",$m[2][$n],$m1)) $R[$l]=$m[2][$n];
		else { $R[$l]=$m1[2]; $R[$l.'.id']=$m1[1]; }
	}
	if(!empty($R['supply_date'])) $R['supply_date']=date("Y-m-d",$R['supply_date']);
	$R['read_time']=time();

//	if($GLOBALS['admin'] && isset($R['errors'])) dier($R,'readru: ERROR');

	msq_add_update('site',array('name'=>e(RE('name')),'text'=>e(serialize($R))),'name');

//	dier($R,RE('name').' #'.$GLOBALS['msqe']); //salert('readru get true',2000)";
}




function readru($e) {
$conf=array_merge(array(
'id'=>'521730',
'rub'=>'�',
'not_found'=>"<span style='font-size: 6px; color:#909090;'>���� ���</span>",
'url'=>"http://api.read.ru/?key=".$GLOBALS['readru_api']."&action=get_book&not_available=1&full_info=1&book_id={id}",
'template'=>'<p><div style="width:100px;text-align:center;" class=br><a href="{link}"><div><img src="{small}" title="&laquo;{name}&raquo;<br>�������� �� read.ru" border=0></div><div><b>{name}</b></a><br>������: {price}</div></div>'
),parse_e_conf($e));

$name=e('read.ru:'.$conf['id']);

$p=ms("SELECT `text` FROM `site` WHERE `name`='".$name."'","_l");
$R=($p!==false?unserialize($p):array());

if($p===false && isset($GLOBALS['readru_api']) // ���� ������ ���
|| (time()-$R['read_time']>3*86400) // ��� �� ����������� 3 ���
|| $GLOBALS['admin'] && isset($_GET['readru']) // ��� ����� ��� ������� ����������
) {
	$url=mpr('url',$conf);
	$fu=urlget_name($url); if($GLOBALS['admin']||!is_file($fu) or time()-filemtime($fu)>10*60*60)
	SCRIPTS("readru_timeout","page_onstart.push(\"majax('module.php',{mod:'readru',url:'$url',name:'$name'"
.($GLOBALS['admin'] && isset($_GET['readru'])?",upd:1":'')
."});"
	.($GLOBALS['admin']?"salert('loading read.ru: "
.((time()-$R['read_time']>3*86400)?'NO DATA':
($p===false && isset($GLOBALS['readru_api'])?'3 DAY':'GET'
))
."',500);":'')
	."\")");
}

if($d===false || !sizeof($R) || isset($R['errors'])) 
return "<div class=br><s><a href='".mpr('url',$conf)."'>".$conf['id']."</a></s></div>";

//not_available=1&
//not_available=1&
// $conf['template']='<hr><p><br>EE: {?}';
// "supply_date": "",

return mper($conf['template'],array_merge($conf,$R,array(
	'?'=>"R='#".intval($R)."#'", // <pre>".(print_r($R,1))."</pre>",
	'price'=>(empty($R["supply_date"])?$conf['not_found']:$R['price'].$conf['rub']),
	'small'=>"http://read.ru/covers_rr/small/".$conf['id'].".jpg",
	'big'=>"http://read.ru/covers_rr/big/".$conf['id'].".jpg",
	'link'=>"http://read.ru/id/".$conf['id']."/?pp=".$GLOBALS['readru_partner'])
));
}

/*

{ "id": "435706",
 "name": "���� ���� � ��������� ����",
 "price": "155",
 "img": "1",
 "type_id": "1",
 "author": { "5493": "������� ������",
 "7287": "�������� �����",
 "18981": "������ ���������" },
 "pubhouse": { "989": "������" },
 "series": { "16468": "���� ����" },
 "genre": { "151": "������������ ������������� ���������" },
 "isbn": [ "978-5-353-04466-6" ],
 "ean": [ "9785353044666" ],
 "supply_date": "",
 "supply_date_str": "01.01.1970",
 "tags": [  ],
 "is_new": "",
 "rate": "0.00",
 "rate_count": "0",
 "weight": "226",
 "time": "0.009" }

{ "id": "995080",
 "name": "����� � ������",
 "price": "227",
 "img": "1",
 "type_id": "1",
 "author": { "5493": "������� ������" },
 "pubhouse": { "996": "���" },
 "series": [  ],
 "genre": { "96": "������� ����������" },
 "isbn": [ "978-5-17-065481-9" ],
 "ean": [ "9785170654819" ],
 "supply_date": "1297803600",
 "supply_date_str": "�������",
 "tags": [  ],
 "is_new": "",
 "rate": "4.86",
 "rate_count": "14",
 "weight": "375",
 "time": "0.008" }
*/

?>