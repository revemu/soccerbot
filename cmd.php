<?php

require_once('./logger.php');


function cmd_process($argc, $cmdstr = '', $member_id = 0, $Talker = 'NoName', $event) {
	$txtlog = "" ;
	$output["type"] = 0 ;
	$log = new Logger("log.txt");
	$log->setTimestamp("Y-m-d H:i:s");
	//$log->putLog("cmd = " . $argc[1] . "\n" ,true);
	if ( strtolower($argc[0]) == "@soccerbot" and isset($argc[1]) )
	{
		$cmd = $argc[1] ;
		if (isset($argc[2])) { $data = $argc[2] ; }
		$txtlog = "" ;
		//$txtlog =  "cmd : " . $cmd . " " . $data . "\n" ;
		//if ($data != "") {
		//$log->putLog($cmdstr,true) ;
		$result["type"] = 0;
		switch($cmd) {
			case '+1':
			case 'register': 
				if ($data != "") {
					$output["type"] = 0 ;
					//$member_name = $data ;
					$weeknum = 0 ;
					//$log->putLog($cmdstr,true) ;
					$pos = strpos($cmdstr, "@", 1);
					if ($pos === false) { break ; } else {
						$member_name = substr($cmdstr,$pos) ;
						//$member_name = str_replace("'", "\'",substr($cmdstr,$pos));
					}
					//if(isset($argc[3])) {
					//		$weeknum = $argc[3] ;
					//}
					$res = check_registered_members($member_name, false, $weeknum) ;
					if ($res[0] > 0) {
						//$txtlog .= "Member : " . $member_name . " [" . $output[1] . "]" ;
						$txtlog .= query_all_registered_members2($weeknum);
					} else {
						$reg = register_members($member_name, $weeknum);
						if ($reg[0]) {
							//$txtlog .= "Member : " . $member_name . " successfully registered\n" ;
							//$txtlog .= print_r($reg,true); 
							$txtlog .= query_all_registered_members2($weeknum);
						} else {
							$txtlog .= $reg[1] ;
						}
						//$txtlog .= "Enoding is : " . mb_detect_encoding($member_name) . "\n" ;
					}

					
				}
				break ;
			case '-1':
			case 'remove': 
				if ($data != "") {
					$member_name = $data ;
					$weeknum = 0 ;
					$pos = strpos($cmdstr, "@", 1);
					if ($pos === false) { break ; } else {
						$member_name = substr($cmdstr,$pos) ;
						//$log->putLog("remove $member_name",true) ;
						//$member_name = str_replace("'", "\'",substr($cmdstr,$pos));
					}
					$res = check_registered_members($member_name) ;
					if ($res[0] == 2) {
						$reg_remove = remove_members($member_name);
						if ($reg_remove == "success") {
							//$txtlog .= "Member : " . $member_name . " successfully removed\n" ;
							$txtlog .= query_all_registered_members2($weeknum);
						} else {
							$txtlog .= $reg_remove ;
						}
						
					} else {
						$txtlog .= "Member : " . $member_name . " is not registered\n" ;
						//$txtlog .= "Enoding is : " . mb_detect_encoding($member_name) . "\n" ;
					}
				}
				break ;
			case 'removeall': 
				if (remove_all_members()) {
					$txtlog .= "Remove All Registered Member\n" ;
				} else {
					$txtlog .= "Error Remove All Registered Member\n" ;
				}
		
				break ;
			case 'resetteamcolor': 
				if (reset_team_colors()) {
					$txtlog .= "Reset All Team Colors\n" ;
				} else {
					$txtlog .= "Error Reset Team Color\n" ;
				}
				
				break ;
			case 'check': 
				$check_type = $data ;
				if(isset($argc[3])) {
						$weeknum = $argc[3] ;
				} else {
						$weeknum = 0 ;
				}
				if ($check_type == "members") {
					$txtlog .= query_all_registered_members($weeknum);
					
				} elseif ($check_type == "register") {
					$txtlog .= query_all_registered_members2($weeknum);
					
				} elseif ($check_type == "noteam") {
					$txtlog .= query_all_registered_members2($weeknum, 1);
					
				} elseif ($check_type == "team1") {
						$weeknum = 0 ;
						if (isset($argc[3])) {
							$weeknum = $argc[3] ;
						}
						
						$result = query_week_id($weeknum) ;
	
						if($result[0]) {
							$weekid = $result[1];
							$weekdate = $result[2];
							$weeknum = $result[3] ;
						} 
						
						
						
						$output["type"] = 4 ;
						
						$team_info = query_team_colors_members($weeknum) ;
						$txtlog = "" ;
						$i = 0 ;
						$mytext = array("Team", "Week $weeknum", "$weekdate") ;
						foreach ($team_info as $team) {
								$team_str = getFlexText($mytext[$i],"md", "center",1,"\"weight\": \"bold\",")  ;
								$team_str .= ", " . getFlexSperator() ;
								$team_str .= ", " . getFlexText("[" . $team["color"] . "]","sm", "center",1,"\"weight\": \"bold\",")  ;
								//$team_str .= ", " . getFlexSperator() ;
								$team_str .= ", " . $team["txt"] ;
								$imgurl = query_colors_image_url($team["color"] ) ;
								if ($i >0) { $txtlog .= ", "; }
								$txtlog .= getRawFlexBubble($team_str,"nano", $imgurl) ;
								
								$i++ ;
						}
						
						$txtlog = getFlexcarousel($txtlog) ;
						
						$output["msg"] = getFlexHeader($txtlog) ;
				} elseif ($check_type == "scorer") {
					$txtlog .= query_top_scorer();
				} elseif ($check_type == "mvp") {
					$txtlog .= query_top_mvp();
				} 
				break ;
			case 'add':
				$check_type = $data ;
				if ($check_type == "week") {
					//$dateweek = $argc[3] ;
					$ts = strtotime('next saturday');
					$dateweek = date('Y-m-d', $ts);
					$satdate =  date('d', $ts);
					$monthnum =  date('m', $ts);
					$year =  date('Y', $ts);
					$monthname = ["ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค."] ;
					$check = query_week_exist($dateweek) ;
					if ($check[0] === true) {
						$response = add_week($dateweek, $year) ;
						if ($response[0] === true) {
							$txtlog .= "ลงชื่อเตะบอล เสาร์ที่ " . $satdate . " " .  $monthname[$monthnum-1] . " ได้ครับ" ;
						} 
					} else {
						$txtlog .= query_all_registered_members2(0);
					}
				} elseif ($check_type == "match") {
					//add match #match_num #color #color #weeknum(0) 
					
					$matchNum = $argc[3] ;
					$teamA = $argc[4] ;
					$teamB = $argc[5] ;
					if(isset($argc[6])) {
						$weeknum = $argc[6] ;
					} else {
						$weeknum = 0 ;
					}
					$result = check_match_week($matchNum, $weeknum) ;
					if ($result[0]) {
						$txtlog .= $result[1] ;
					} else {
						$response = add_match_week($matchNum,$teamA,$teamB ,$weeknum) ;
						$txtlog .= $response[1] ;
					}
				} elseif ($check_type == "goal") {
					//add goal #match_num #weeknum(0) #name #color() 
					
					$matchNum = $argc[3] ;
					$name= $argc[5] ;
					
					if(isset($argc[4])) {
						$weeknum = $argc[4] ;
					} else {
						$weeknum = 0 ;
					}
					
					if (isset($argc[6])) {
						$teamColor = $argc[6] ;
					} else {
						$teamColor = "" ;
					}
					//function add_match_goal_week($matchNum,$name,$weeknum = 0)
					$result = add_match_goal_week($matchNum, $name, $weeknum, $teamColor) ;
					$txtlog .= $result[1] ;
					//$txtlog .= "add $matchNum, $name, $weeknum, $teamColor\n" ;
				}
				break ;
			case '+team1' :
			case '+team2' :
			case '+team3' :
			case '+team4' :
			case '-team' :
				$teamnum = substr($cmd, 5);
				
				if ($data != "") {
					$pos = strpos($cmdstr, "@", 1);
					if ($pos === false) { break ; } else {
						$member_name = substr($cmdstr,$pos) ;
					}
					//$member_name = $data ;
					$weeknum = 0 ;
					$output["type"] = 0 ;
					//if (isset($argc[3])) {
					//		$weeknum = $argc[3] ;
					//}
						
					$result = query_week_id($weeknum) ;
		
					if($result[0]) {
						$weekid = $result[1];
					}
					$teamid = -1 ;

					if ($cmd == '-team') {
						$teamid = 0 ;
					} else {
						
						$result = query_teamweek_id($weekid, $teamnum) ;


						if($result[0]) {
							$teamid = $result[1] ;
						} else {
							//$txtlog .= print_r($result[1] ,true) ;
						}
					}
					//if ($teamid >= 0) {	
						if (check_member_team($member_id) || $teamid == 0) {
							$response = update_member_team($member_name, $teamid , $weekid, $member_id ) ;
							$myres = query_all_registered_members2($weeknum, 1) ;
							if ($myres != "no") {
								$txtlog = $myres ;
							} else {
								$txtlog = "จัดทีมครบแล้ว" ;
							}
						} else {
							$txtlog = "$member_name มีทีมแล้ว" ;
						}
					//}

					
					//$txtlog = print_r($txtlog,true) ;
					//$test = query_all_registered_members2($weeknum, 1) ;
					//$txtlog .= "name = " . $member_name . " pos =" . $pos ;
					//$txtlog .= " teamid = " . $teamid . " teamnum = " . $teamnum ;		
					//$txtlog .= print_r($test,true) ;
				}
				break ;
			case '+slip':
				$mode = substr($cmd, 1);
				if ($cmd[0] == "+") { 
					$val = 1 ;
				} 
				else {
					$val = 0 ;
				};
				if ($data != "") {
					$pos = strpos($cmdstr, "@", 1);
					if ($pos === false) { break ; } else {
						$member_name = substr($cmdstr,$pos) ;
					}
					$weeknum = 0 ;
					$output["type"] = 0 ;
					//if (isset($argc[3])) {
					//		$weeknum = $argc[3] ;
					//}
						
					$result = query_week_id($weeknum) ;
		
					if($result[0]) {
						$weekid = $result[1];
					} 
					//$log->putLog("id: $member_id\n" . $Talker) ;
					if ($member_id > 0) {
						$response = update_member_misc($member_name, $val, $weekid, $mode, $member_id) ;
					}
					
					$txtlog .= query_no_paid_members($weeknum, 2, $Talker) ;
					//$txtlog .= "name = " . $member_name . " pos =" . $pos ;
					//$txtlog .= "mode = " . $mode . "type = " . $type ;		
					//$txtlog .= print_r($result,true) ;
				}
				break ;
			case '+atk':
			case '-atk':
			case '+pay':
			case '-pay':
				$mode = substr($cmd, 1);
				if ($cmd[0] == "+") { 
					$val = 1 ;
				} 
				else {
					$val = 0 ;
				};
				if ($data != "") {
					$pos = strpos($cmdstr, "@", 1);
					if ($pos === false) { break ; } else {
						$member_name = substr($cmdstr,$pos) ;
					}
					$log->putLog("in cmd: $cmdstr") ;
					$weeknum = 0 ;
					$output["type"] = 0 ;
					//if (isset($argc[3])) {
					//		$weeknum = $argc[3] ;
					//}
						
					$result = query_week_id($weeknum) ;
		
					if($result[0]) {
						$weekid = $result[1];
					} 
						
					$response = update_member_misc($member_name, $val, $weekid, $mode, $member_id) ;
					$txtlog .= query_no_paid_members($weeknum) ;
					//$txtlog .= "name = " . $member_name . " pos =" . $pos ;
					//$txtlog .= "mode = " . $mode . "type = " . $type ;		
					//$txtlog .= print_r($result,true) ;
				}
				break ;
		
			case 'randomteam':
				if ($data != "") {
					$member_name = $data ;
					$weeknum = 0 ;
					$output["type"] = 0 ;
					if (isset($argc[3])) {
							$weeknum = $argc[3] ;
					}
					
					$result = query_week_id($weeknum) ;
	
					if($result[0]) {
						$weekid = $result[1];
					} 
					$result = check_registered_members($member_name, true) ;
					if ($result[0]) {
						if($result[1] == 0) {
							$random_id = query_random_team(0, $member_name) ;
							//$response = update_member_team($member_name, $random_id, $weekid, $member_id) ;
							$color = query_team_colors($random_id, $weekid) ;
							$txtlog .= $member_name. " สุ่มไปอยู่ทีม [" . $color . "]\n" ;
							//$txtlog .= print_r($response, true) ;
						} else {
							$txtlog .= $member_name . " สุ่มไปแล้ว\n" ;
						}
					} else {
						$txtlog .= $member_name . " ไม่ได้เป็นสมาชิก\n" ;
					}
					//$txtlog .= print_r($result,true) ;
				}
				break ;
			case "randomfullteam":
					//$txtlog .= "สมาชิก " ;
					$limit = 3 ;
					if (isset($argc[2])) {
						$limit = $argc[2] ;
						}
					$rand_txt = random_full_team2($limit) ;
					if ($rand_txt == "none") {
						$txtlog .= "ทำการสุ่มไปแล้ว ใช้ /teamweek เพื่อแสดง" ;
					} else {
						$txtlog .= $rand_txt;
					}
					break ;
			case "randomfullteam1":
						//$txtlog .= "สมาชิก " ;
					$limit = 3 ;
					if (isset($argc[2])) {
						$limit = $argc[2] ;
						}
					$rand_txt = random_full_team($limit) ;
					if ($rand_txt == "none") {
						$txtlog .= "ทำการสุ่มไปแล้ว, กรุณารีเซ็ตทีม" ;
					} else {
						$weeknum = 0 ;
						if (isset($argc[3])) {
							$weeknum = $argc[3] ;
						}
							
							$result = query_week_id($weeknum) ;
		
						if($result[0]) {
							$weekid = $result[1];
							$weekdate = $result[2];
							$weeknum = $result[3] ;
						} 
							
							
							
						$output["type"] = 4 ;
							
						$team_info = query_team_colors_members($weeknum) ;
						$txtlog = "" ;
						$i = 0 ;
						$mytext = array("Team", "Week", "$weekdate"," ") ;
							
						$result = query_week_location($weekid) ;
						if($result[0]) {
							$location_name = $result[1];
							$kick_off = $result[2];
						}  

						$mylocation = array("Location", $location_name, $kick_off," ") ;
						foreach ($team_info as $team) {
							$team_str = getNewFlexText($mytext[$i],"md", "center",1,"bold")  ;
							$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							$color = get_colors_code($team["color"]) ;
									
			
									
							$team_str .= ", " . getNewFlexText("[" . $team["color"] . "]","sm", "center",1,"bold", $color)  ;
							$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							$team_str .= ", " . $team["txt"] ;
							$imgurl = query_colors_image_url($team["color"] ) ;
									

							$team_str .= ", " . getFlexSperator("#DDDDDD", "xs") ;
							$team_str .= ", " . getNewFlexText($mylocation[$i],"xxs", "center",1,"regular", "#BBBBBB")  ;
									
									//Insert Next Bubble Block
							if ($i >0) { $txtlog .= ", "; }
																
								$txtlog .= getRawFlexBubble($team_str,"nano", $imgurl) ;
									//End Insert Next Bubble Block
									
								$i++ ;
						}
							
						$txtlog = getFlexcarousel($txtlog) ;
							
						$output["msg"] = getFlexHeader($txtlog, "Team Week $weekdate") ;
					}

					break ;
			case "randomcaptain":
					//$txtlog .= "สมาชิก " ;
					$txtlog .= random_captain() ;
					break ;
			case "randommember":
				//$txtlog .= "สมาชิก " ;
				$limit = 3 ;
				if (isset($argc[2])) {
					$limit = $argc[2] ;
				}
				$txtlog .= random_member($limit) ;
				break ;
			case "resetdeck":
					//$txtlog .= "สมาชิก " ;
					$txtlog .= reset_deck() ;
					break ;
			case 'draw':
					$output["type"] = 4 ;
							
							$dice_face = array(1=> "dice1.jpg", 2=> "dice2.jpg",3=> "dice3.jpg",4=> "dice4.jpg",5=> "dice5.jpg",6=> "dice6.jpg") ;
							//$r1 = (mt_rand()%6) + 1 ;
							//$r2 = (mt_rand()%6) + 1 ;
							list($usec, $sec) = explode(' ', microtime());
							srand( (10000000000 * (float)$usec) ^ (float)$sec  );
							$r1 = rand( 1, 6 );

							$draw = get_decks() . ".png" ;
							
							/*
							if ($member_id <> 14) {
								$draw = get_decks() . ".png" ;
							} else {
								$draw = "K-0.png" ;
							}
							*/
							$header = "$Talker" ;
							$txtlog .= getFlexBox($header,"sm", "center") . ", " ;
	
	
							$header = "จั่วได้" ;
							$txtlog .= getFlexBox($header,"sm", "center") . ", " ;
							
							$scorer_str = <<<EOT
				
							{
								"type": "image",
								"url": "https://api.revemu.org/deck/$draw",
								"size": "5xl",
								"margin": "md"
							}
EOT;
							$txtlog .= $scorer_str ;
	
							//$header = "ทอยได้ $sum" ;
							//$txtlog .= getFlexBox($header,"md", "center")  ;
	
							
							$output["msg"] = getFlexBubble($txtlog, "kilo", "$Talker จั่วไพ่") ;
							break;
			case 'roll':
				$output["type"] = 4 ;
						
						$dice_face = array(1=> "dice1.jpg", 2=> "dice2.jpg",3=> "dice3.jpg",4=> "dice4.jpg",5=> "dice5.jpg",6=> "dice6.jpg") ;
						//$r1 = (mt_rand()%6) + 1 ;
						//$r2 = (mt_rand()%6) + 1 ;
						list($usec, $sec) = explode(' ', microtime());
						srand( (10000000000 * (float)$usec) ^ (float)$sec  );
						$r1 = rand( 1, 6 );
						//list($usec, $sec) = explode(' ', microtime());
						//srand( (pi() * (float)$usec) ^ (float)$sec  );
						$r2 = rand( 1, 6 );
						
						$dice1 = $dice_face[$r1] ;
						$dice2 = $dice_face[$r2] ;

						$sum = $r1 + $r2 ;

						$header = "$Talker" ;
						$txtlog .= getFlexBox($header,"sm", "center") . ", " ;


						//$header = "ทอยได้ $sum" ;
						//$txtlog .= getFlexBox($header,"md", "center") . ", " ;
						
						$scorer_str = <<<EOT
			
						{
						   "type": "box",
						   "layout": "baseline",
						   "margin": "xs",
						   "alignItems": "center",
						   "justifyContent": "center",
						   "contents": [
							{
								"type": "icon",
								"size": "3xl",
								"url": "https://api.revemu.org/img/$dice1" 
							  },
							{
								"type": "icon",
								"size": "3xl",
								"url": "https://api.revemu.org/img/$dice2"
							  }
							]
						},
EOT;
						$txtlog .= $scorer_str ;

						$header = "ทอยได้ $sum" ;
						$txtlog .= getFlexBox($header,"sm", "center")  ;

						
						$output["msg"] = getFlexBubble($txtlog, "nano", "$Talker ทอยได้ $sum") ;
						break;
			case 'roll1':
				$x=($r=rand(1,6))>3?"o o":($r<2?"     ":"o   ");
				$z=$r>5?"o o":($r%2==0?"     ":"  o ");
				$v="$x";
				$s=strrev($x) ;

				$x2=($r2=rand(1,6))>3?"o o":($r2<2?"     ":"o   ");
				$z2=$r2>5?"o o":($r2%2==0?"     ":"  o ");
				$v2="$x2";
				$s2=strrev($x2) ;
				//$txtlog .= "ลูกเต๋าที่ออก\n$v|$z|".strrev($v);
				//$txtlog .= "ลูกเต๋าที่ออก\n" ;
				//$txtlog .= "$v2|$z2|\n$s2";
				$sum = $r + $r2 ;
				$txtlog .= "-----   ------\n" ;
				$txtlog .= "| $v |   | $v2 |\n" ;
				$txtlog .= "| $z |   | $z2 |\n" ;
				$txtlog .= "| $s |   | $s2 |\n" ;
				$txtlog .= "-----   ------\n" ;
				$txtlog .= "ทอยได้ $sum"  ;
				break ;
			case 'roll2':
					//$my_array = array("a"=>"⚀","b"=>"⚁","c"=>"3") ;
					$dice0 = rand(1,6) ;
					$dice1 = rand(1,6) ;
					$sum = $dice0 + $dice1 ;
	
					
					//print_r($my_array);
					$txtlog .= "ลูกเต๋าที่ออก\n[ $dice0 ] [ $dice1 ] = $sum" ;
					//$txtlog .= "[ " . $dice0 . " ] [" . $dice1 . " ] = "   ;
					break ;
			case 'random':
				$my_array = array("a"=>"1","b"=>"2","c"=>"3");

				shuffle($my_array);
				//print_r($my_array);
				$txtlog .= "ลำดับเลือก " ;
				$txtlog .= $my_array[0] . " " . $my_array[1] . " " . $my_array[2] ;
				break ;
			case 'randomcolor':
				//$member_name = $data ;
				random_team_color() ;
				$txtlog .= query_all_team_colors() ;
				break ;
			case 'costshare':
				//$member_name = $data ;
				if ($data != "") {
					$cost = $data ;
					$mem_count = query_members_count2() ;
					$cost = round(round($data/10) / $mem_count) * 10 ;
					//$mod = ($cost % 10) ;
					//if ($mod <=5 )
					$txtlog .=  "ค่าสนาม (" . $data . "/" . $mem_count . ") = คนละ " . $cost . " บาท" ;
				}
				break ;
			case 'matchweek':
				$weeknum = 0 ;
									
				$result = query_week_id($weeknum) ;
	
				if($result[0]) {
					$weekid = $result[1];
					$weekdate = $result[2];
					$weeknum = $result[3] ;
				} 
						
				$result = query_week_location($weekid) ;
				if($result[0]) {
					$location_name = $result[1];
				}
						
				$output["type"] = 4 ;
						
				$result = report_match_week($weeknum) ;
						
				if($result["result"] == 1) {
							
					$header = "Match Week ⚽ $weekdate" ;
					$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") . "," . $result[1] ;
							
					$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							
					$txtlog .= ", " . getFlexSperator("#FFFFFF", "xs") ;
					$txtlog .= ", " . getFlexSperator("#FFFFFF", "xs") ;
							
					$txtTmp = getFlexText("Location: $location_name","xs", "start",1)  ;
					$txtlog .= ", " . getRawFlexBox($txtTmp)  ;
													
				} else {
					$header = "Match Week $weeknum not found!" ;
					//$txtlog = getFlexBox($header,"md", "center") ;
				} 
						
				$txtlog = getFlexBox($header,"md", "center"). $txtlog ;
						  
						
				$output["msg"] = getFlexMSG($txtlog, "mega", $header) ;
						
				break ;
			case 'report':
				$check_type = $data ;
				if ($check_type == "match") {
						$weeknum = 0 ;
						if (isset($argc[3])) {
							$weeknum = $argc[3] ;
						}
						
						$txtlog .= report_match_week($weeknum) ;
				} elseif ($check_type == "tableweek") {
						$weeknum = 0 ;
						if (isset($argc[3])) {
							$weeknum = $argc[3] ;
						}
						
						$result = query_week_id($weeknum) ;
	
						if($result[0]) {
							$weekid = $result[1];
							$weekdate = $result[2];
							$weeknum = $result[3] ;
						} 
						
						
						
						$output["type"] = 4 ;
						
						
						$result = report_table_week($weeknum) ;
						
						if($result["result"] == 1) {
							$header = "Table Week $weeknum ⚽ $weekdate" ;
							$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							$txtlog .= ", " . $result[1] . ", " ;
						
							$result = query_week_location($weekid) ;
							if($result[0]) {
								$location_name = $result[1];
							}
							
							$txtlog .= getFlexSperator("#FFFFFF", "xs") . ", " ;
							
							$txtTmp = getFlexText("Location: $location_name","xs", "start",1, "\"offsetTop\": \"2px\",")  ;
							$txtlog .= getRawFlexBox($txtTmp)  ;
						} else {
								$header = "Table Week $weeknum not found!" ;
						}
						
						$txtlog = getFlexBox($header,"md", "center") . $txtlog ;
						
						$output["msg"] = getFlexMSG($txtlog, "giga", $header) ;
						
						//$output["msg"] = getFlexMSG($txtlog, "mega") ;
				} elseif ($check_type == "matchweek") {
						$weeknum = 0 ;
						if (isset($argc[3])) {
							$weeknum = $argc[3] ;
						}
						
						$result = query_week_id($weeknum) ;
	
						if($result[0]) {
							$weekid = $result[1];
							$weekdate = $result[2];
							$weeknum = $result[3] ;
						} 
						
						$result = query_week_location($weekid) ;
						if($result[0]) {
							$location_name = $result[1];
						}
						
						$output["type"] = 4 ;
						
						$result = report_match_week($weeknum) ;
						
						if($result["result"] == 1) {
							
							$header = "Match Week ⚽ $weekdate" ;
							$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") . "," . $result[1] ;
							
							$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							
							$txtlog .= ", " . getFlexSperator("#FFFFFF", "xs") ;
							$txtlog .= ", " . getFlexSperator("#FFFFFF", "xs") ;
							
							$txtTmp = getFlexText("Location: $location_name","xs", "start",1)  ;
							$txtlog .= ", " . getRawFlexBox($txtTmp)  ;
													
						} else {
							$header = "Match Week $weeknum not found!" ;
							//$txtlog = getFlexBox($header,"md", "center") ;
						} 
						
						$txtlog = getFlexBox($header,"md", "center"). $txtlog ;
						  
						
						$output["msg"] = getFlexMSG($txtlog, "mega", $header) ;
						
				} elseif ($check_type == "weeksum") {
						$weeknum = 0 ;
						if (isset($argc[3])) {
							$weeknum = $argc[3] ;
						}
						
						$result = query_week_id($weeknum) ;
	
						if($result[0]) {
							$weekid = $result[1];
							$weekdate = $result[2];
							$weeknum = $result[3] ;
						} 
						
						$output["header"] = "Week $weeknum @ $weekdate Summary" ;
						
						$txtlog .= getFlexBox($output["header"],"sm") . ", " ;
						
						
						$result = query_week_location($weekid) ;
						if($result[0]) {
							$output["location_name"] = $result[1];
						}  
						$output["type"] = 2 ;
						
						update_table_week($weeknum) ;
						
						$result = report_table_week($weeknum) ;
						$txtlog .=  $result[1] ;
						$output[2] = $result[2] ;
						
						
						$txtlog .= getFlexBox("Match Detailed","sm") . ", " ;
						
						$result = report_match_week($weeknum) ;
						
						$output[0] = $result[0] ;
						if($output[0]==1) {
							$txtlog .=  $result[1] ;
							$output[2] = $result[2] ;
							$output[3] = $result[3] ;
						} else {
							$txtlog .= "0" ;
							if ( isset($result[2]) ) { 
								$output[2] = $result[2] ;
							} else {
								$output[2] = "No Match for Week $weeknum" ;
							}
							$output[3] = "" ; 
						}
				} elseif ($check_type == "teamweek1") {
						
					$weeknum = 0 ;
					if (isset($argc[3])) {
						$weeknum = $argc[3] ;
					}
					
					$result = query_week_id($weeknum) ;

					if($result[0]) {
						$weekid = $result[1];
						$weekdate = $result[2];
						$weeknum = $result[3] ;
					} 
					
					
					
					$output["type"] = 4 ;
					
					$result = report_match_week($weeknum) ;

					$team_info = new_query_team_colors_members($weeknum) ;
					$header = "Team Week $weekdate " ;
					$team_str = ", " . getFlexSperator("#BBBBBB", "xs") ;
					foreach ($team_info as $team) {
						$color = get_colors_code($team["color"]) ;
												
						
						$team_str .= ", " . getNewFlexText("[" . $team["color"] . "]","sm", "start",1, "bold", $color)  ;
						$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
						//$team_str .= ", " . getFlexText($team["txt"],"xs", "start",1) ;
						$team_str .= ", " . $team["txt"] ;
						$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
								
					}
						
						if($result["result"] == 1) {
							/*
							$header = "Match Week ⚽ $weekdate" ;
							$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") . "," . $result[1] ;
							
							$txtlog .= ", " . getFlexSperator("#BBBBBB", "xs") ;
							
							$txtlog .= ", " . getFlexSperator("#FFFFFF", "xs") ;
							$txtlog .= ", " . getFlexSperator("#FFFFFF", "xs") ;
							
							$txtTmp = getFlexText("Location: $location_name","xs", "start",1)  ;
							$txtlog .= ", " . getRawFlexBox($txtTmp)  ;
										*/			
						} else {
							//$header = "Match Week $weeknum not found!" ;
							//$txtlog = getFlexBox($header,"md", "center") ;
						} 
						
						$txtlog = getFlexBox($header,"md", "center") .  $team_str ;
						  
						
						$output["msg"] = getFlexMSG($txtlog, "mega", $header)  ;
					
					//$output["msg"] = getFlexHeader($txtlog) ;
					
			} elseif ($check_type == "teamweek" || $check_type == "squadweek") {
						
						$weeknum = 0 ;
						if (isset($argc[3])) {
							$weeknum = $argc[3] ;
						}
						
						$result = query_week_id($weeknum) ;
	
						if($result[0]) {
							$weekid = $result[1];
							$weekdate = $result[2];
							$weeknum = $result[3] ;
						} 
						
						
						
						$output["type"] = 4 ;
						
						$team_info = query_team_colors_members($weeknum) ;
						$txtlog = "" ;
						$i = 0 ;
						$mytext = array("Team", "Week", "$weekdate"," ") ;
						
						$result = query_week_location($weekid) ;
						if($result[0]) {
							$location_name = $result[1];
							$kick_off = $result[2];
						}  

						$mylocation = array("Location", $location_name, $kick_off," ") ;
						$max_count = 0 ;
						$count = 0 ;
						$min_count = 10 ;
						$min_index  = 0 ;
						$j = 0 ;
						foreach ($team_info as $team) {
							$count = substr_count($team["txt"], "flex");
							if ($count > $max_count) {
								$max_count = $count ;
							}/*
							if ($count < $min_count) {
								$min_count = $count ;
								$min_index  = $j ;
							}*/
							$j += 1 ;
						}

						foreach ($team_info as $team) {
								$team_str = getNewFlexText($mytext[$i],"md", "center",1,"bold")  ;
								$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
								$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
								$color = get_colors_code($team["color"]) ;
								
		
								
								$team_str .= ", " . getNewFlexText("[" . $team["color"] . "]","sm", "center",1,"bold", $color)  ;
								$team_str .= ", " . getFlexSperator("#BBBBBB", "xs") ;
								#$count = substr_count($team["txt"], "\n");
								$team_str .= ", " . $team["txt"] ;
								$count = substr_count($team["txt"], "flex");
								if ($count < $max_count) {
									$blank_flex = ", " . getFlexText(" ","md","center") ;
									$team_str .= str_repeat($blank_flex,$max_count - $count) ;
								}
								$imgurl = query_colors_image_url($team["color"] ) ;
								

								$team_str .= ", " . getFlexSperator("#DDDDDD", "xs") ;
								$team_str .= ", " . getNewFlexText($mylocation[$i],"xxs", "center",1,"regular", "#BBBBBB")  ;
								
								//Insert Next Bubble Block
								if ($i >0) { $txtlog .= ", "; }
															
								$txtlog .= getRawFlexBubble($team_str,"nano", $imgurl) ;
								//End Insert Next Bubble Block
								
								$i++ ;
						}
						
						$txtlog = getFlexcarousel($txtlog) ;
						
						$output["msg"] = getFlexHeader($txtlog, "Team Week $weekdate") ;
						
				} elseif ($check_type == "topowngoal" || $check_type == "topog") {
						
					$limit = 20 ;
					if (isset($argc[3])) {
						$limit = $argc[3] ;
					}
					$output["type"] = 4 ;
					$header = date("Y") ;
					$txtlog .= getFlexBox($header,"md", "center") . ", " ;
					
					$header = "Top $limit Own goal" ;
					$txtlog .= getFlexBox($header,"md", "center") . ", " ;
					
					$result = query_top($limit, 2) ;
					$txtlog .= $result ;
					$output["msg"] = getFlexMSG($txtlog, "micro",$header) ;
			}  
				elseif ($check_type == "topscorer") {
						
						$limit = 20 ;
						if (isset($argc[3])) {
							$limit = $argc[3] ;
						}
						$output["type"] = 4 ;
						$header = date("Y") ;
						$txtlog .= getFlexBox($header,"md", "center") . ", " ;
						
						$header = "Top $limit goalscorer" ;
						$txtlog .= getFlexBox($header,"md", "center") . ", " ;
						
						$result = query_top($limit, 0) ;
						$txtlog .= $result ;
						$output["msg"] = getFlexMSG($txtlog, "micro",$header) ;
				} elseif ($check_type == "topmvp") {
						
						$limit = 5 ;
						if (isset($argc[3])) {
							$limit = $argc[3] ;
						}
						$output["type"] = 4 ;
						$header = "Top $limit MVP" ;
						$txtlog .= getFlexBox($header,"md", "center") . ", " ;
						$result = query_top_mvp($limit) ;
						$txtlog .= $result ;
						$output["msg"] = getFlexMSG($txtlog, "micro",$header) ;
				}  elseif ($check_type == "topassist") {
						
						$limit = 20 ;
						if (isset($argc[3])) {
							$limit = $argc[3] ;
						}
						$output["type"] = 4 ;
						$header = date("Y") ;
						$txtlog .= getFlexBox($header,"md", "center") . ", " ;
						
						$header = "Top $limit Assist" ;
						$txtlog .= getFlexBox($header,"md", "center") . ", " ;
						$result = query_top($limit, 3) ;
						$txtlog .= $result ;
						$output["msg"] = getFlexMSG($txtlog, "micro",$header) ;
				} elseif ($check_type == "register") {
					$txtlog .= query_all_registered_members2(0);
					
				} elseif ($check_type == "noteam") {
					$txtlog .= query_all_registered_members2(0, 1);
					
				}
				
				break ;
			default:
				//$txtlog .= "No CMD : $cmd\n" ;
				break ;
		}	
	} 
	$output[1] = $txtlog ;
	return $output ;
}
?>