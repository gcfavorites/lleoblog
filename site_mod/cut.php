<?php // ����������� ����� ����������� ��� ���������

function cut($e) {

	$e=str_replace(array("\\","'","\n","\r"),array("\\\\","\\'","\\n",""),$e);

	SCRIPTS("cut","function cut(e,s) {
	if(e.className=='') { e.className='cut'; e.innerHTML='[��������&nbsp;����������]'; } else { e.className=''; e.innerHTML=s;}
	return false; }");

	STYLES(".cut { text-align: center; cursor: pointer; color: blue; text_decoration: underline; }");

	return '<div class=cut onclick="cut(this,\''.$e.'\');">[��������&nbsp;����������]</div>';

}

?>
