<?php

function PREWORD($e) { global $IS,$REF,$article; $s='';

	$name=$IS['imgicourl']; if(substr($name,0,1)=='#') $name=false;
	$time_expirie=($article["DateTime"]<time()-86400*30);

if(!empty($GLOBALS['linksearch'])) {

	$u0=$GLOBALS['linksearch'][0];
	$u1=$GLOBALS['linksearch'][1];

if($u0!='') { // ���� ������ �� ����������
	$s.=LL('preword:poisk',array('name'=>$name,'site'=>h($u1),'string'=>h($u0),'time_expirie'=>$time_expirie));
	// $s.=($name?"����� �������, ".$name."!<p>":"")."����� ����� ".h($u1)." ������ ���� \"<b><u>".h($u0)."</u></b>\"?
	if(stristr($u0,"������")) $s.=LL('preword:download'); // ������ ������ \"������\". ����� - �������� ��������, � �� ������������.

} elseif( !strstr($REF,$GLOBALS["httpsite"]) && !strstr($REF,"livejournal.com") ) { // ��� ���� ������ �� ������
	$s.=LL('preword:poisk',array('name'=>$name,'site'=>h(urldecode($REF)),'time_expirie'=>$time_expirie));
	// �� ������ c <font color=green>".h($fromlink)."</font> �� ������� ������ �������� �� ����� �����.";
	}
} elseif($name!='') $s.=LL('preword:opoznan',$name); // ����������, ������� ".$name."!

if($_GET['search']!='') $s.=LL('preword:search',array('search'=>h($_GET['search']),'normal'=>$GLOBALS['mypage']));
// �������� ���������� � ���������� ���� \"<span class=search>".h($_GET['search'])."</span>\", <a href='".$GLOBALS['mypage']."'>������������� � ���������� �����</a>";

return LL('preword',$s); // '"<div class='preword'>$s</div>";
}

?>