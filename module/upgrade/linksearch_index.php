<?

$s .= msq_add_index("dnevnik_link","DateID","(`DateID`)","������ �����");
$s .= msq_add_index("dnevnik_link","last_ipn","(`last_ipn`)","������ �����");

$s .= msq_add_index("dnevnik_search","DateID","(`DateID`)","������ �����");
$s .= msq_add_index("dnevnik_search","last_ipn","(`last_ipn`)","������ �����");

?>