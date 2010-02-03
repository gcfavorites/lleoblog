<?php
/*
 * AntiBot - ����� ��� �������� ����������� ����������� ��������
 * ���������� � ������� ���������� ������� �����, ��� ����, ��� ��
 * ��������, ��� �� �������� ����, � �� ������� ���.
 * 
 * ������� GetPic �������� ���� ��������, ���������� � SID -
 * ���, �� �������� ����� ����� ��������� ������������
 * � ����������� �������������.
 *
 * ������� CheckCode($code) �������� ��������� ������������ �����
 * � ��������� - ���� ����� �������� �� ����� �����������
 * � � ���������� ����������� �������� �����, �� �� -
 * ����������� TRUE � aqk �������� ���������.
 * ���� ������������ FALSE.
 *
 * ������� ����� ������������ �������� aka ��zi������
 * 26-�� ������� 2004 ���� ��� ������� FlirtCenter.com
 *
 * � ����� ���������� LLeo
 * ��������� � ����� � ������� ������� �� ���� ��� ������� ��������� ����������������
 * � ��� ������� ��������� ������, ��� ��� ��� ����!
*/

function antibot_make() { global $antibot_pic, $antibot_H, $antibot_W, $antibot_C, $antibot_add2hash, $antibot_file, $antibot_hash;
	$bgs=glob($antibot_pic."bg*"); $im=imagecreatefromjpeg($bgs[rand(0,count($bgs)-1)]); // ����� ��������� ��������
	$h = round((ImageSY($im)-$antibot_H)/2); // ������� ������
	$w = round(ImageSX($im)/$antibot_C); // ������� ������

	// ��������� ������ �������� � ���������� �� �� �������
	$path=$antibot_pic."sum_"; $lpath=strlen($path); $files=glob($path."*.png"); $n=count($files)-1; // ������, ����� ������� ����
	$imS=array(); $sums=''; for($i=0; $i<$GLOBALS['antibot_C']; $i++) {
		$f=$files[rand(0,$n)]; // �������� ��������� ���������� ����
		$l=substr($f,$lpath,1); // ��������, ��� ��� �� ������
		if(!isset($imS[$l])) $imS[$l]=imagecreatefrompng($f); // ���� �� ���� - ���������� ��� ��������
		imagecopymerge($im,$imS[$l],($w*$i)+rand(0,$w-$antibot_W),rand(2,$h*2-2),0,0,18,20,40); // ����������� �� �������
		$sums.=$l; // �������� ������ � ������
	}

// �������������� ���������� - ������ ���������
//	$color = ImageColorAllocate($im, 200, 200, 200);
//	for($i=0; $i<=round(ImageSX($im)/7); $i++) { $x = $i*7; ImageLine($im, $x, 0, $x-ImageSY($im), ImageSY($im), $color); }

//	// �������������� ���������� - �����������
	$imT=ImageCreate(1,1); ImageFill($imT,1,1,ImageColorAllocate($imT,0,0,0));
	// ��������� �� ����������� � ���������
	$t=rand(4,15); for($i=round(ImageSY($im)/$t);$i>=0;$i--) ImageCopyMerge($im,$imT,0,$i*$t,0,0,ImageSX($im),1,20);
	$t=rand(4,15); for($i=round(ImageSX($im)/$t);$i>=0;$i--) ImageCopyMerge($im,$imT,$i*$t,0,0,0,1,ImageSY($im),20);

	$antibot_hash = md5($sums.$antibot_add2hash);


	if(!imagejpeg($im,$antibot_file.$antibot_hash.".jpg")) // ��������� ��������
	{
	if(!is_dir($GLOBALS['hosttmp'])) { mkdir($GLOBALS['hosttmp']); chmod($GLOBALS['hosttmp'],0777); }
	if(!is_dir($GLOBALS['antibot_file'])); { mkdir($GLOBALS['antibot_file']); chmod($GLOBALS['antibot_file'],0777); }
	if(!imagejpeg($im,$antibot_file.$antibot_hash.".jpg")) // ��������� ��������
	idie("������! �� ���� ��������� �������� � ���������� \"".$antibot_file."\", ���������, ������� �� ���, � ����������� �� ����� ������?");
	}

	$GLOBALS['antibot_imW'] = ImageSX($im);
	$GLOBALS['antibot_imH'] = ImageSY($im);
	ImageDestroy($im);
	return $antibot_hash;
}

/* �������� �������� - ��������� �� ��� � ����� � ���� �� ����� �������� */
function antibot_check($code, $hash) {
	$code = preg_replace("/[^0-9a-z]/si","",$code); // ������ ����������� �������, ����� ���� (� ����)
	$hash2=md5($code.$GLOBALS['antibot_add2hash']);
	$f = $GLOBALS['antibot_file'].$hash2.".jpg";
	if($hash==$hash2 and is_file($f)) { unlink($f); return true; }
	if(is_file($f)) unlink($f); return false;
}

/* �������� ������� ���������� HTML ��� ������ ��� �������� ��������. <img src="URL" width=WIDTH height=HEIGHT border=0> */
function antibot_img() {
return "<img src='".$GLOBALS['antibot_www'].$GLOBALS['antibot_hash'].".jpg' width=".$GLOBALS['antibot_imW']." height=".$GLOBALS['antibot_imH']." alt='captcha' border=0>";
}

/* ������� ������ ��������, ������� ���� ������� ����� ���� �����. */
function antibot_del() { $old = time()-$GLOBALS['antibot_deltime']; $deleted = 0;
	$p=glob($GLOBALS['antibot_file']."*.jpg"); if($p===false or !sizeof($p)) return "����������� �������� ���";
	foreach($p as $f) if(filemtime($f)<$old) { unlink($f); $deleted++; }
	return "����������� ��������, �������: ".$deleted;
}

?>
