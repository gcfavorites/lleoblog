<?php /* ������������ html

������� ��� ��������� ����, ���� �� ���������� ������.

{_neh:� ��������� ���� <b></b> � <s></s> _}

*/

function neh($e) { return str_replace(array('{@','@}','{','}'),array('{_','_}','&#123;','&#125;'),h($e)); }

?>
