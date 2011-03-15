<?php
	class TextHelper {
		public static function numberToWords($number, $uppercase = false){
			$numbers = array(	"zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine",
				"ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "ninteen");
			if ($number > 0 && $number < 20)		$number = $numbers[$number];
			else if ($number > 20 && $number < 30)	$number = "twenty-" . $numbers[$number-20]; 
			else if ($number > 30 && $number < 40)$number = "thirty-" . $numbers[$number-30];  
			return ($uppercase ? ucwords($number) : $number);
		}
		public static function possessive($string){
			return $string . "'" . (substr($string, -1) == "s" ? "" : "s");
		}
	}
	class DateCompare {
		public static function daysApart($one, $two){
			if(!is_int($one))$one = strtotime($one . " GMT");
			if(!is_int($two))$two = strtotime($two . " GMT");
			if(date("Y", $one) != date("Y", $two))
				return abs((date("z", $two) + 365*(date("Y", $two) - date("Y", $one))) - date("z", $one));
			else return abs(date("z", $one) - date("z", $two));
		}
		public static function differenceInWords($date, $compareTo = NULL, $uppercase = true) {
			if(!is_null($compareTo)) $compareTo = strtotime($compareTo); 
			else $compareTo = date('U');

			if(!is_numeric($date)) $date = strtotime($date);							
			
			$diff = $compareTo - $date; 
			$dayDiff = floor($diff / 86400); 
			
			$r = "";

			if($dayDiff < 1) {
				if(!$diff)				$r = 'just now';
				else if($diff < 60) 	$r = $diff.' seconds ago'; 
				elseif($diff < 120)		$r = 'one minute ago'; 
				elseif($diff < 3600) 	$r = TextHelper::numberToWords(floor($diff/60)) . ' minutes ago'; 
				elseif($diff < 7200) 	$r = 'one hour ago'; 
				elseif($diff < 86400) 	$r = TextHelper::numberToWords(floor($diff/3600)) . ' hours ago'; 
			} elseif($dayDiff == 1) 	$r = 'yesterday';
			elseif($dayDiff < 7) 		$r = TextHelper::numberToWords($dayDiff) . ' days ago'; 
			elseif($dayDiff == 7) 		$r = 'one week ago'; 
			elseif($dayDiff < 30){
				$round = ceil($dayDiff/7);
				if(!($round - ($dayDiff/7))) $r = TextHelper::numberToWords(ceil($dayDiff/7));
				else if($round - ($dayDiff/7) < 0.5) $r = "more than ".TextHelper::numberToWords(--$round);
				else $r = "just over ".TextHelper::numberToWords(--$round);
				$r .= ' week'.($round != 1 ? 's' : '').' ago';
			}elseif($dayDiff < 355){
				$round = ceil($dayDiff*12/365);
				if(!($round - ($dayDiff*12/365))) $r = TextHelper::numberToWords($round);	
				else if($round - ($dayDiff*12/365) < 0.33) $r = 'nearly '.TextHelper::numberToWords($round);
				else if($round - ($dayDiff*12/365) < 0.66) $r = 'more than '.TextHelper::numberToWords(--$round);
				else $r = 'just over '.TextHelper::numberToWords(--$round);
				$r .= ' month' . ($round != 1 ? 's' : '') . ' ago';
			} else {
				$round = ceil($dayDiff/365);
				$plural = false;
				if($round - $dayDiff/365 < 0.33) $r = 'nearly '.TextHelper::numberToWords($round);
				else if($round - $dayDiff/365 < 0.5){ $r = 'about '.TextHelper::numberToWords(--$round).' and a half'; $plural = true;}
				else if($round - $dayDiff/365 < 0.75) $r = 'more than '.((--$round == 1) ? 'a' : TextHelper::numberToWords($round));
				else if($round - $dayDiff/365 < 1) $r = 'just over '.((--$round == 1) ? 'a' : TextHelper::numberToWords($round));
				else $r = 'almost '.TextHelper::numberToWords(--$round);
				$r .= ' year' . ($round != 1 || $plural ? 's' : '') . ' ago'; 
			}
			
			if($uppercase) return ucfirst($r);
			else return $r;
		} 
	}
?>
