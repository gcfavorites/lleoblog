<?php // работа с файлами

// function testdir($s) { $a=explode('/',rtrim($s,'/')); $s=''; for($i=0;$i<sizeof($a);$i++) { $s.='/'.$a[$i]; if(!is_dir($s)) dirput($s); } }
// function getras($s){ $r=explode('.',$s); if(sizeof($r)==1) return ''; return strtolower(array_pop($r)); }

//==================================================================================================
// процедура передачи данных и файлов через POST-запрос по старинке без всяких там уебищных CURL-библиотек
// $filePath - полное имя (с путем) файла для передачи или массив имен файлов для передачи (если файлов нет - '')
// $urla - адрес запроса, напр. http://lleo.aha.ru/blog/install
// $ara - массив переменных POST, напр: array('action'=>'do','key'=>'1','user'=>123)
// возвращает ответ сервера или, если ошибка, строку, начинающуюся с 'ERROR:'
function POST_file($filePath,$urla,$ara,$port=80,$scheme='http',$charset='Windows-1251') {
        if(gettype($filePath)!='array') $filePath=array($filePath);
        $url=array_merge(array('scheme'=>$scheme,'port'=>$port),parse_url($urla));
        $bu="---------------------".substr(md5($filePath.rand(0,32000)),0,10); $r="\r\n"; $ft=$r.'--'.$bu.'--'.$r;

        // данные
        $dat=''; if(count($ara)) foreach($ara as $n=>$v) $dat.='--'.$bu.$r.'Content-Disposition: form-data; name="'.$n
.'"'.$r.$r.urlencode($v).$r;

        $len=strlen($dat); // общая длина

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

        // открыть хост
        if(!$fp=fsockopen($url['host'],$url['port'])) return "ERROR: can't open url ".$url['host'].":".$url['port'];
        // запихнуть заголовок и POST-массив
        if(fputs($fp,$headers)===false) return "ERROR: can't send #1";

        if(count($files)) foreach($files as $l=>$fh) { // позапихивать файлы
                if(fputs($fp,$fh)===false) return "ERROR: can't send #2";
                // открыть файл и запихнуть его
                if(($fp2=fopen($l,"rb"))===false) return "ERROR: can't open file '".$l."'";
                while(!feof($fp2)) if(fputs($fp,fgets($fp2,1024*100))===false) return "ERROR: can't send #4";
                fclose($fp2);
                // запихнуть заключительный хедер
                if(fputs($fp,$ft)===false) return "ERROR: can't send #5";
        }

        // и получить ответ
        $s=''; while(!feof($fp)) $s.=fgets($fp,4096); fclose($fp);
        if($s=='') return "ERROR: NO RESPONSE";
        list($h,$t)=explode($r.$r,$s,2);

        // обработка переноса
        if(stristr($h,'301 Moved Permanently')) {
                return POST_file($filePath,preg_replace("/^.+Location: ([^\s]+).*$/si","$1",$h),$ara);
        }

return $t;
}


// отправить в ответ просто файл

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