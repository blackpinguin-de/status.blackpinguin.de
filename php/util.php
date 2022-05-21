<?php

function toDuration($seconds, $min = "d") {
        $dur = "";
        $tm = round(abs($seconds) / 60);
        $th = floor($tm / 60);
        $td = floor($th / 24);
        $ta = floor($td / 365);

        $m = floor($tm % 60);
        $h = floor($th % 24);
        $d = floor($td % 365);
        $a = floor($ta);

        $dur = ( $m < 10 ? "0" : "" ) . "${m}m";
        if($th >= 1 || in_array($min, ["h","d","a"])){$dur = ($h<10?"0":"")."${h}h " . $dur;}
        if($td >= 1 || in_array($min, ["d","a"])){$dur = "${d}d " . $dur;}
        if($ta >= 1 || in_array($min, ["a"])){$dur = "${a}y " . $dur;}
        if($seconds < 0){$dur = "- " . $dur;}
        return $dur;
}

function toDate($date) {
  return preg_replace("@([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}):[0-9]{2}@", "\$1", $date);
}

function breakDate($str){
	$str = toDate($str);
        return str_replace("-", "&#8209;", str_replace(" ", "&nbsp;", trim($str)));
}

function breakTime($str){
	$str = toDate($str);
        return str_replace("-", "&#8209;", str_replace(" CE", "&nbsp;CE", trim($str)));
}

function breakDuration($str){
        return str_replace("d&nbsp;", "d ", str_replace(" ", "&nbsp;", $str));
}
