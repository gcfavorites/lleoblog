<?

msq_add_pole("dnevnik_zapisi","autoformat","enum('no','p','pd') default 'no'","����������� ��������� ������������������� �������");
msq_add_pole("dnevnik_zapisi","autokaw","enum('auto','no')","����������� ��������� ������������� �������� ����");

msq_add_pole("dnevnik_zapisi","count_comments_open","int(10) unsigned default '0'","����� �������� ������������ � ������� - ���� �� ������ �� ���� �� ���� ������ ���");

msq_del_pole("dnevnik_zapisi","include","���� include ������ �� �����, �� ������� �� ������� ������� II ���������");

#msq_del_pole("dnevnik_zapisi","autoformat","����������� ��������� ������������������� �������");
#msq_del_pole("dnevnik_zapisi","autokaw","����������� ��������� ������������� ������� � ����");

?>