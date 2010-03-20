<?php
// это процедура не аякса, я просто не нашел для него лучшего места
//
// эта штука выдает секретные файлы (работает с модулем {_SECRET_FILE: link _})
//
// файлы секретны тем, что:
// а) по прямой ссылке их открыть невозможно
// (для этого разместите их в папке, где создайте файл .htaccess, куда наколотите строчку ереси типа 'trololololo')
// б) ссылку на такой файл постороннему лицу переслать не получится - она зависит от IP и Браузера

include "../config.php";

if( $_GET['o'] != substr(md5($hashinput.$_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]),5,5) ) die('Error 404: ахуюшки');

$f=$filehost.str_replace('..','_',$_GET['file']);


// die(basename($file));

if(!file_exists($f)) die("File not found: ".$f);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($f).'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '.filesize($f));
ob_clean();
flush();
readfile($f);
exit;

?>
