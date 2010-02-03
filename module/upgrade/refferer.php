<? // привести в порядок таблицы статистики link и search

$s .= msq_del_index("dnevnik_link","last_ip","мусор");
//$s .= msq_del_pole("dnevnik_link","Date","мусор");

$s .= msq_del_index("dnevnik_link","Date","мусор");
//$s .= msq_del_pole("dnevnik_link","last_ip","мусор");

$s .= msq_del_index("dnevnik_search","last_ip","мусор");
//$s .= msq_del_pole("dnevnik_search","last_ip","мусор");

$s .= msq_del_index("dnevnik_search","Date","мусор");
//$s .= msq_del_pole("dnevnik_search","Date","мусор");

//$s .= msq_add_pole("dnevnik_link","last_ipn","int(10) unsigned NOT NULL","статистика по линкам");
//$s .= msq_add_pole("dnevnik_search","last_ipn","int(10) unsigned NOT NULL","статистика по поисковикам");

?>