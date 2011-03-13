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
		/**
		 *	Converts a timestamp to pretty human-readable format.
		 * 
		 *	Original JavaScript Created By John Resig (jquery.com)  Copyright (c) 2008
		 *	Copyright (c) 2008 John Resig (jquery.com)
		 *	Licensed under the MIT license.
		 *	Ported to PHP >= 5.1 by Zach Leatherman (zachleat.com)
		 *
		 */	
		public static function differenceInWords($date, $compareTo = NULL) {
			$date = new DateTime($date);
			if(!is_null($compareTo)) $compareTo = new DateTime($compareTo); 
			else $compareTo = new DateTime('now'); 
			$diff = $compareTo->format('U') - $date->format('U'); 
			$dayDiff = floor($diff / 86400); 
	
			if(is_nan($dayDiff) || $dayDiff < 0) return ''; 
					 
			if($dayDiff == 0) { 
				if($diff < 60) return $diff.' seconds'; 
				elseif($diff < 120)	return '1 minute'; 
				elseif($diff < 3600) return ucwords(TextHelper::numberToWords(floor($diff/60))) . ' minutes'; 
				elseif($diff < 7200) return '1 hour'; 
				elseif($diff < 86400) return ucwords(TextHelper::numberToWords(floor($diff/3600))) . ' hours'; 
			} elseif($dayDiff == 1) return 'yesterday';
			elseif($dayDiff < 7) return ucwords(TextHelper::numberToWords($dayDiff)) . ' days'; 
			elseif($dayDiff == 7) return '1 week'; 
			elseif($dayDiff < (7*6)) return "About " . TextHelper::numberToWords(ceil($dayDiff/7)) . ' weeks'; 
			elseif($dayDiff < 365) return "About " . TextHelper::numberToWords(ceil($dayDiff/(365/12))) . ' months'; 
			else { 
				$years =  TextHelper::numberToWords(round($dayDiff/365)); 
				return $years . ' year' . ($years != 1 ? 's' : ''); 
			} 
		} 
	}
?>
