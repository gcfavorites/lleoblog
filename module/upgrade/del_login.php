<? // ������� ������� login, ��� ��� �� ����� �����

$s.=msq_del_table('login',"��� ����� �� ����� ����.");

//$s .= msq_del_pole("dnevnik_zapisi","Prev","��� ����� �� �����, ��� ���� ��� ������!");
//$s .= msq_add_index("dnevnik_zapisi","DateDatetime","(`DateDatetime`)","������ �����");

$s .= msq_add_pole("unic","timelast","timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP","����� ���������� ������");
$s .= msq_del_pole("unic","sc","��� ����� �� �����");
$s .= msq_del_pole("unic","bro","��� ����� �� �����");

$s .= msq_add_pole("unic","time_reg","int(11) NOT NULL default '0'","����� �����������");
$s .= msq_del_pole("unic","timereg","����� ������");

//  `timereg` timestamp NOT NULL default CURRENT_TIMESTAMP,
//  `DateTime` int(11) NOT NULL default '0',

// $s .= msq_del_pole("unic","bro","��� ����� �� �����");
//`timelast` ,
//`sc` varchar(32) NOT NULL,
//`bro` varchar(1024) NOT NULL,


?>