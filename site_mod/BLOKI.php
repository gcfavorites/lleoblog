<?php /* ������������ �� ������ �������� ������� �� ���������� � design.ru

������ ����� � �������� �������� ����� WIDTH
���������� ���� ���� - ������������ ����������� ������, ����������� ����� ������� ��������

{_BLOKI: WIDTH=150

{_readru:id=995080_}

{_readru:id=521730_}

{_readru:id=416788_}

{_readru:id=120831_}

{_readru:id=304944_}

{_readru:id=408986_}

{_readru:id=435706_}

_}
*/

// .thmbns {margin: -3em 0 0 -2em; text-align:center;}

STYLES("����� design.ru","
.thmbns {margin: margin: -3em 0 0 -2em; text-align:center;}
.thmbn {text-decoration:none; display: -moz-inline-box; display:inline-block; vertical-align:top; text-align:left; margin:3em 0 0 2em;}
.thmbn .rth {float:left;}
"); // width:210px; 

function BLOKI($e) { // list($e,$s)=explode(':',$e,2); $e=c($e);
	$conf=array_merge(array('WIDTH'=>210),parse_e_conf($e));
	$e=explode("\n\n",($conf['body']));
	if(sizeof($e)<2) $e=explode("\n",($conf['body']));

	$s=''; foreach($e as $l) { $l=c($l); if($l=='') continue;
		$s.="<ins class='thmbn' style='width: ".$conf['WIDTH']."px'><div class='rth' style='width: ".$conf['WIDTH']."px'>"
		.$l
		."</div></ins>";
	}
	return "<div class=thmbns>$s</div>";
}

?>