<?php
/*
�����: ����� ������, http://temapavloff.ru

������ ������� ����� ������ http://addthis.com

���������:
link - ������ �� ������ (�� ��������� - ������ �� ������� �������)
title - ��������� ������ (�� ��������� - ��������� ������� ������)
template - ������ (�� ��������� ���������� ���� ������ ��� ������������� ���� �� ������� ���� ��������. ��� ���������� ������ ���������� � ���� div � �������� addthis_toolbox � addthis_pill_combo_style �������� ��� <a></a> � ��������������� ���������� �� ������. �������� ����������� <a class="addtis_button_livejournal"></a> ��������� ������ ��� ��������� � ��

���� ������ ����� �������� � �������� template ������� LAST � CONTENTER, � ���� ������ ��������� link � title ������ ���� ����� {link} � {Header} ��������������.

P.S. ���������� � ��������� ������, �� ������ ������������ ���������� :-)

{_ADDTHIS:_}

*/

SCRIPT_ADD("http://s7.addthis.com/js/250/addthis_widget.js");

function ADDTHIS($e) { global $httphost, $article, $admin_name; //link, title, description
$conf=array_merge(array(
'link'=>get_link($article['Date']),
'title'=>h($article['Header']),
'text'=>'�������&nbsp;�&nbsp;����',
'template'=>'<div style="float:right" class="addthis_toolbox addthis_pill_combo_style" addthis:url="{link}" addthis:title="{title}"><a class="addthis_button_compact">{text}</a></div>'
),parse_e_conf($e));

return mper($conf['template'],$conf);
}

?>