<?php //рекоменда

function REKOMENDA($e) { $s=''; return $s;
	$pp=ms("SELECT `link`,`text`,`datetime` FROM `rekomenda` WHERE `datetime`>(NOW()- INTERVAL 2 DAY) ORDER BY `datetime` DESC","_a");
	if(sizeof($pp)) {
		foreach($pp as $p) $s.="<div>".$p['datetime'].": <a href=".$p['link'].">".$p['text']."</a></div>";
		return "<div class='rekomenda'>".$s."</div>";
	}
	return $s;
}

?>