<?php /* ������ ������ ������-����� �� ��������� ����������

����� - ���� ������ http://temapavloff.ru/

��� ����������� �� ���� ���������� �������� ����: http://api.yandex.ru/maps/form.xml/
� ��������� ��� � config.php, ������:

$api_key='AJyqd00BAAAAfIxMQQMA1iQr2bwfHslJWjOqsh7QPtdO66wAAAAAAAAAAAAw2gLfwW9dGcYQHcB74cwRm-BdWg==';

�����:

lt = ������
lg = �������
zoom = �������
name = ��� �����
descr = �������� �����
width = ������ ����
height = ������ ����

{_YAMAP:
lt=73.380411
lg=54.981549
zoom=14
name=����, ��������� ������� ������
descr=������� � �������� � ������� ������ ���-�� ��������������<br />������� �����������. ���������, ������, � ������� � ����,<br />��� ��� �� ������� ����� ����, ������� ��� ���� ����.<br />����� �� ����� � ��� ����, �� ����� ���� �������� �����,<br />���������� ����� ����.
_}
*/

// APk4KksBAAAAs4gYHAIASD1kwFy_udVGmZYJyzBDUOSXh68AAAAAAAAAAADKbQVBR-EoF0QrCIQbjVSd4Gn0fg==

$GLOBAL['yamap_count'] = 0;

SCRIPT_ADD('http://api-maps.yandex.ru/1.1/index.xml?key='.$GLOBALS['api_key']);

SCRIPTS('yandex map','
function create_yamap(id, lt, lg, zoom, name, descr) {
	var map = new YMaps.Map(idd(id));
	var point = new YMaps.GeoPoint(lt, lg)
	map.setCenter(point, zoom);
	var placemark = new YMaps.Placemark(point);
	if(!name && !descr) placemark.openBalloon = function() { return false; };
	else { placemark.name = name; placemark.description = descr; }
	map.addOverlay(placemark);
}
');

// page_onstart.push(\"loadScript('http://api-maps.yandex.ru/1.1/index.xml?key='+api_key);\");

function YAMAP($e) { global $yamap_count;

$conf = array_merge(array(
// 'api_key' => $GLOBALS['api_key'],
'lt' => 0,
'lg' => 0,
'zoom' => 10,
'name' => '',
'descr' => '',
'width' => 400,
'height' => 200
),parse_e_conf($e));

// SCRIPTS('yandex map key',"var api_key='".$conf['api_key']."';");

$yamap_count++;

// $map_container = 
// $map_script = '<script type="text/javascript">create_yamap(\'yamap_'.$yamap_count.'\', '.$conf['lt'].','.$conf['lg'].','.$conf['zoom'].',\''.$conf['name'].'\',\''.$conf['descr'].'\')</script>';
SCRIPTS("page_onstart.push(\"create_yamap('yamap_".$yamap_count."',".$conf['lt'].",".$conf['lg'].",".$conf['zoom'].",'".$conf['name']."','".$conf['descr']."')\")");
return '<div style="width:'.$conf['width'].'px;height:'.$conf['height'].'px;" id="yamap_'.$yamap_count.'"></div>';

 // $map_container.$map_script;
}

?>