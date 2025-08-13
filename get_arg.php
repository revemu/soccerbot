<?php

	function parseArg($querystring) {
	
		//$querystring = "@SoccerBot register name 2" ;
		try {
			$parts  = explode(" ", $querystring);
			$result             = array();
			$i = 0 ;
			foreach ($parts as $part) {
				if ($i <= 2) {
					$result[] = trim($part) ;
					$i++ ;
				} else {
					$result[$i-1] .= " " . $part ;
				}
			}
			if ($i>1) {
				$result[2] = trim($result[2]) ;
			}
			return $result ;
		} catch (Exception $e) {
			$result = 'Caught exception: ' .  $e->getMessage() . "\n";
		}
	}

	function parseArgArray($arg) {
	
		//$querystring = "@SoccerBot register name 2" ;
		try {
				//print_r($arg) ;
				$querystr = array_pop($arg) ;
				$posQuote = strpos($querystr,"'") ;
			if (($posQuote === false) || ($posQuote > 0)) {
				//echo $querystr . "\n" ;
				$pos = strpos($querystr," ") ;
				if($pos === false) {
					$arg[] = $querystr ;
					//print_r($arg) ;
					return $arg ;
				} else {
					$arg[] =  trim(substr($querystr,0,$pos)) ;
					$arg[] =  trim(substr($querystr,$pos,strlen($querystr))) ;
					return parseArgArray($arg) ;
				}
			} else {
					$querystr = substr($querystr,1,strlen($querystr)-1);
					$posQuote = strpos($querystr,"'");
					$arg[] = trim(substr($querystr,0,$posQuote));
					if ($posQuote < (strlen($querystr)-1)) {
						$arg[] = trim(substr($querystr,$posQuote+1,strlen($querystr)-$posQuote));
						print_r($arg) ;
						return parseArgArray($arg) ;
					} else {
						return $arg ;
					}
			}
		} catch (Exception $e) {
			$result = 'Caught exception: ' .  $e->getMessage() . "\n";
		}
		
		//return $arg ;
	}

?>