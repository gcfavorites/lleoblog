<?php

function select_modul() {
	$inc=glob($GLOBALS['filehost']."site_mod/*.php"); $ainc=array(); $ainc['']='- ������ -';
	foreach($inc as $l) {
		$s=file_get_contents($l);
		$l=preg_replace("/^.*?\/([^\/]+)\.php$/si","$1",$l);
		if(!preg_match("/\/\*(.*?)\*\//si",$s,$m)) $l="--".$l;
		$ainc[$l]=$l;
	}
	return selecto('editor_mod','',$ainc,"onchange=\"majax('editor.php',{a:'help',mod:this.value})\" id");
}

if($name=='') $s="{_B:{_CENTER:�������, ������ � ������ ��������������_}_}

<p>1. <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'module'})\">�������� ���� �������</a>
<p>2. <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'knop'})\">�������� ������</a>
<p>3. <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'about'})\">����� ����������</a>
<p>4. ������� ������� �� �������: ".select_modul()."
";

elseif($name=='module') {
	$s="{_CENTER:{_B:������_}_}";

	$inc=glob($GLOBALS['filehost']."site_mod/*.php"); $ainc=array(); foreach($inc as $l) {
		$t=file_get_contents($l);
		$l=preg_replace("/^.*?\/([^\/]+)\.php$/si","$1",$l);



		if(preg_match("/\/\*(.*?)\*\//si",$t,$m)) {
			$s.="<br><a href=\"javascript:majax('editor.php',{a:'help',mod:'$l'})\"\">$l</a> - ";
			$t=c($m[1]);
			if(preg_match("/^([^\n]+)\n(.*?)$/si",$t,$m)) { $head=$m[1]; $t=c($m[2]); }
			if(preg_match("/(.*?)\n([^\n]*\{\_.*?)$/si",$t,$m)) { $t=c($m[1]); $prim=c($m[2]); }
			$s.=$head;
		}
	}
}

elseif($name=='about') $s="{_CENTER:{_B:����� �������_}_}

�����, ���������� � ���������, ������������� ����������� �� ����� �� ���� ������ - ������� �� ����, ����� ��������� ��� ���������,
����� ������� ������� ��������� ����-�� �� ������� � ���� ������.

{_CENTER:{_B:������_}_}

����� ��������������� <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'knop'})\">�������� ��������</a>, �������� ������
������ ����� � ������� ������ - �������� ����������� �������.

{_CENTER:{_B:������_}_}

����� ������������ ������� <a href=\"javascript:majax('editor.php',{a:'loadhelp',name:'module'})\">�������</a>. ������ - �������� ����������,
������� ���������� �� ������ ����������� �������� {<b></b>_���:[���������]_<b></b>}.
������ ������ ����� �������� ������ ����? ������ ��� ���������� {<b></b>_ � _<b></b>} �� ����������� �����, ���� � ���� Javascript.
������� ���� �������� �� ������������. ���������� ������ ����� ��������� ��������� ����� ���������� ������. ������� ���� ���
����� ��������� �����-�� ������� ������� ��� ������� ����� ����-����, ����� � ������ ���� ������, �������� ����� ������ ������ � �������
�����, � �� �� ��������� ���.

������� � �������� ������ ����� ����������� ������ ���� {<b></b>_ _<b></b>} �������� �� ����� - ����� ������� �������, ����� �� �������. ������� -
������� ��� ������������� ������ � ��������� ���������, ����� ���� ����� ������� ��������� (���� ��� ��� ����� ������ ���� � ���������).

{_CENTER:{_B:����������_}_}

���� �� ������������ ����� - ������ � ����� �������� � ����� �������� �����. ��� ����� � ������������ ������� �����������, �������
�������� �� � ������ �������� � ��������� ����� ������ �����. �� ���� ���� ����������� ���������� ������ � ���� �����������, ����� �
��� �������, ��� ����� �����. �� ���� ��� ��� � ��� �� ������ ���� �����.
";

elseif($name=='knop') $s="{_CENTER:{_B:������ ������� ������_}_}

<p><img border=1 src=".$www_design."e3/foto.png> - ������� ������� ����������� (���� �� ��������)
<p><img border=1 src=".$www_design."e3/gnome-help.png> - ������� ������� (�� ��� �������, ��?)
<p><img border=1 src=".$www_design."e2/pd.gif> - �������� ��� ������ ������ � �������� (����������)
<p><img border=1 src=".$www_design."e2/d.gif> - �������� ��� ����� ������ � �������� (����������)
<p><img border=1 src=".$www_design."e2/nbsp.gif> - �������� ����������� ������
<p><img border=1 src=".$www_design."e2/copy.gif> - �������� ������ ��������� &copy;
<p><img border=1 src=".$www_design."e2/mdash.gif> - �������� ������� ���� &mdash; (������ ������������� ����, ���� �� ��������� ����� \"�� ������ �������\")
<p><img border=1 src=".$www_design."e2/ltgt.gif> - ����� ���������� ������ ����� � �������� ������� &laquo; � $raquo; (����� ������� ������� ������� - ��� ������ ������� ���, ��� ����, ������������� ����������� � �������� ��������, ���� �� ��������� ����� \"�� ������ �������\").
<p><img border=1 src=".$www_design."e2/bold.gif> - �������� ������ ����� � �������: �� ������ <b>������</b>
<p><img border=1 src=".$www_design."e2/italic.gif> - �������� ������ ����� � �������: �� ������ <i>��������</i>
<p><img border=1 src=".$www_design."e2/strikethrough.gif> - �������� ������ ����� � �������: �� ������ <s>�����������</s>
<p><img border=1 src=".$www_design."e2/underline.gif> - �������� ������ ����� � �������: �� ������ <u>������������</u>
<p><img border=1 src=".$www_design."e2/justifycenter.gif> - ���������� ������ ����� ����� �� ������
<p><img border=1 src=".$www_design."e2/image.gif> - ������ ��� ������� ��������
<p><img border=1 src=".$www_design."e2/link.gif> - ������ ��� ���������� �����: �������� ������ ����, � �� ������������
<p><img border=1 src=".$www_design."e2/ljvideo.gif> - ������ ��� ������� �����
<p><img border=1 src=".$www_design."e2/tableb_1.gif>
<img border=1 src=".$www_design."e2/tableb_r.gif>
<img border=1 src=".$www_design."e2/tableb1.gif>
<img border=1 src=".$www_design."e2/tableb2.gif>
<img border=1 src=".$www_design."e2/tableb3.gif>
<img border=1 src=".$www_design."e2/tableb_pre.gif> - ������ ���� ������
";


$s="<div align=justify>".str_replace(array("\n\n","\n"),array("<p>"," "),$s)."</div>";

?>
