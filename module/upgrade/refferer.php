<? // �������� � ������� ������� ���������� link � search

$s .= msq_del_index("dnevnik_link","last_ip","�����");
//$s .= msq_del_pole("dnevnik_link","Date","�����");

$s .= msq_del_index("dnevnik_link","Date","�����");
//$s .= msq_del_pole("dnevnik_link","last_ip","�����");

$s .= msq_del_index("dnevnik_search","last_ip","�����");
//$s .= msq_del_pole("dnevnik_search","last_ip","�����");

$s .= msq_del_index("dnevnik_search","Date","�����");
//$s .= msq_del_pole("dnevnik_search","Date","�����");

//$s .= msq_add_pole("dnevnik_link","last_ipn","int(10) unsigned NOT NULL","���������� �� ������");
//$s .= msq_add_pole("dnevnik_search","last_ipn","int(10) unsigned NOT NULL","���������� �� �����������");

?>