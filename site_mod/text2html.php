<?php // ����������� ����� ����������� ��� ���������

function text2html($e) { return str_replace("\n","<br>",htmlspecialchars($e)); }

?>