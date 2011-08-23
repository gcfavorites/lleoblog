<?php // ������ � �������

// function testdir($s) { $a=explode('/',rtrim($s,'/')); $s=''; for($i=0;$i<sizeof($a);$i++) { $s.='/'.$a[$i]; if(!is_dir($s)) dirput($s); } }
// function getras($s){ $r=explode('.',$s); if(sizeof($r)==1) return ''; return strtolower(array_pop($r)); }

//==================================================================================================
// ��������� �������� ������ � ������ ����� POST-������ �� �������� ��� ������ ��� �������� CURL-���������
// $filePath - ������ ��� (� �����) ����� ��� �������� ��� ������ ���� ������ ��� �������� (���� ������ ��� - '')
// $urla - ����� �������, ����. http://lleo.aha.ru/blog/install
// $ara - ������ ���������� POST, ����: array('action'=>'do','key'=>'1','user'=>123)
// ���������� ����� ������� ���, ���� ������, ������, ������������ � 'ERROR:'
function POST_file($filePath,$urla,$ara,$port=80,$scheme='http',$charset='Windows-1251') {
        if(gettype($filePath)!='array') $filePath=array($filePath);
        $url=array_merge(array('scheme'=>$scheme,'port'=>$port),parse_url($urla));
        $bu="---------------------".substr(md5($filePath.rand(0,32000)),0,10); $r="\r\n"; $ft=$r.'--'.$bu.'--'.$r;

        // ������
        $dat=''; if(count($ara)) foreach($ara as $n=>$v) $dat.='--'.$bu.$r.'Content-Disposition: form-data; name="'.$n
.'"'.$r.$r.urlencode($v).$r;

        $len=strlen($dat); // ����� �����

        $files=array(); $k=0; foreach($filePath as $l) { if(empty($l)) continue;
                if(!is_file($l)) return "ERROR: file not found '$l'";
                $fh='--'.$bu.$r
                .'Content-Disposition: form-data; name="file'.(++$k).'"; filename="'.urlencode(basename($l)).'"'.$r
                .'Content-Type: '.$charset.$r
                .$r;

                $len+=strlen($fh.$ft)+filesize($l);
                $files[$l]=$fh;
        }

        $headers="POST ".$url['path']." HTTP/1.0".$r
        ."Host: ".$url['host'].$r
        ."Referer: ".$url['host'].$r
        ."Content-type: multipart/form-data, boundary=".$bu.$r
        ."Content-length: ".$len.$r
        .$r
        .$dat;

        // ������� ����
        if(!$fp=fsockopen($url['host'],$url['port'])) return "ERROR: can't open url ".$url['host'].":".$url['port'];
        // ��������� ��������� � POST-������
        if(fputs($fp,$headers)===false) return "ERROR: can't send #1";

        if(count($files)) foreach($files as $l=>$fh) { // ������������ �����
                if(fputs($fp,$fh)===false) return "ERROR: can't send #2";
                // ������� ���� � ��������� ���
                if(($fp2=fopen($l,"rb"))===false) return "ERROR: can't open file '".$l."'";
                while(!feof($fp2)) if(fputs($fp,fgets($fp2,1024*100))===false) return "ERROR: can't send #4";
                fclose($fp2);
                // ��������� �������������� �����
                if(fputs($fp,$ft)===false) return "ERROR: can't send #5";
        }

        // � �������� �����
        $s=''; while(!feof($fp)) $s.=fgets($fp,4096); fclose($fp);
        if($s=='') return "ERROR: NO RESPONSE";
        list($h,$t)=explode($r.$r,$s,2);

        // ��������� ��������
        if(stristr($h,'301 Moved Permanently')) {
                return POST_file($filePath,preg_replace("/^.+Location: ([^\s]+).*$/si","$1",$h),$ara);
        }

return $t;
}


// ��������� � ����� ������ ����

function Exit_SendFILE($file) {
$mimetypes=array(
	'jpg'=>'image/jpg',
	'jpeg'=>'image/jpg',
	'gif'=>'image/gif',
	'png'=>'image/png',
	'bmp'=>'image/bmp',

	'mp3'=>'audio/mp3',
	'wav'=>'audio/wav',

	'mid'=>'audio/midi',
	'txt'=>'text/plain'
); $mime=$mimetypes[getras(basename($file))]; if(empty($mime)) $mime='application/octet-stream';

header('Content-Description: File Transfer');
header('Content-Type: '.$mime);
//header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '.filesize($file));
ob_clean(); flush();
readfile($file);
exit;
}

?>