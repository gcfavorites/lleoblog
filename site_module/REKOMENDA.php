<?php //рекоменда

function REKOMENDA($e) { $s=''; // return $s;
	$pp=ms("SELECT `link`,`text`,`datetime` FROM ".$GLOBALS['db_rekomenda']." WHERE `datetime`>(NOW()- INTERVAL 2 DAY) ORDER BY `datetime` DESC","_a");
	if(sizeof($pp)) {
		foreach($pp as $p) {
			list($date,)=explode(' ',$p['datetime']);
			$s.="<div>".$date." <a href=".$p['link'].">".$p['text']."</a></div>";
		} return "<div class='rekomenda'>".$s."</div>";
	}
	return $s;
}

?>