<?php

function reset_deck($weeknum = 0)
{
	
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			//$weekdate = $output[2];
	} else {
			return $output ;
	}

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	

	$sql = "update decks set owned=0 where owned=1 ;";
	$result = $conn->query($sql);

	$conn->close();
	
	return "ล้างไพ่แล้ว!";

}

function get_decks($weeknum = 0)
{
	
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			//$weekdate = $output[2];
	} else {
			return $output ;
	}

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT card_name FROM decks WHERE owned=0 ORDER BY RAND() LIMIT 1;";
	$result = $conn->query($sql);

	//$ret = "" ;

	if ($result->num_rows > 0) {
    // output data of each row
		//$ret = "ผลสุ่มสมาชิก\n\n" ;
		while($row = $result->fetch_assoc()) {
			$ret = $row["card_name"] ;
		}
	} else {
		$ret = "none";
	}

	$sql = "update decks set owned=1 where card_name='" . $ret . "';";
	$result = $conn->query($sql);

	$conn->close();
	
	return $ret;

}


function query_week_id($weeknum = 0)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	if($weeknum == 0) {
		$sql = "SELECT id, number, DATE_FORMAT(date, '%e %b %Y') as date FROM week_tbl ORDER BY NUMBER DESC LIMIT 1" ;
	} else {
		$sql = "SELECT id, number, DATE_FORMAT(date, '%e %b %Y') as date FROM week_tbl where number=$weeknum" ;
	}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["id"] ;
			$output[2] = $row["date"] ;		
			$output[3] = $row["number"] ;
			$output[4] = $sql ;
		}
	} else {
		$output[0] = false ;
	}
	$conn->close();
	
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
	
	return  $output;
}

function query_week_exist($weekdate)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	//$sql = "SELECT WEEKOFYEAR('$weekdate') as weeknum" ;
	$sql = "SELECT * from week_tbl where date='$weekdate'" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = false ;
		/*while($row = $result->fetch_assoc()) {
			$output[1] = $row["weeknum"] ;		
		}*/
	} else {
		$output[0] = true ;
	}
	$conn->close();
	
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
	
	return  $output;
}

function query_week_number()
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	//$sql = "SELECT WEEKOFYEAR('$weekdate') as weeknum" ;
	$sql = "SELECT number+1 as weeknum from week_tbl order by number desc limit 1 " ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["weeknum"] ;		
		}
	} else {
		$output[0] = false ;
	}
	$conn->close();
	
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
	
	return  $output;
}

function query_team_id_from_color($color,$weekid)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select id from team_color_week_tbl where lower(color) = lower('$color') and week_id=$weekid" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["id"] ;		
		}
	} else {
		$output[0] = false ;
		$output[1] = "Error " . $sql ;	
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function check_match_week($matchNum,$weeknum = 0)
{
	
	$result = query_week_id($weeknum) ;
	
	if($result[0]) {
		$weekid = $result[1];
	} else {
		return array(true, "No Week ID") ;
	}
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select id from match_stat_tbl where match_num = $matchNum  and week_id=$weekid" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		$output[1] = "Match already Added" ;
		
	} else {
		$output[0] = false ;
		$output[1] = "Match [$matchNum ] week [$weeknum] is ready to added" ;
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function report_match_score($matchID)
{
	//$sql = "select team_a_id, team_b_id, team_a_goal, team_b_goal from match_stat_tbl where id=$matchID" ;
	$output = query_match_detail($matchID) ;
	//$output = "Report Match\n" ;
	//$ouput = $output["team_a_id"
	//return $output ;
	print_r($output) ;
	
}

function query_match_score($matchID)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT team_week_id, COUNT(*) AS goal FROM match_goal_tbl WHERE match_id=$matchID GROUP BY team_week_id ;" ;
	//echo $sql ;
	//exit ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[] = true ;
		while($row = $result->fetch_assoc()) {
			$output[] = $row ;		
		}
	} else {
		$output[0] = false ;
		$output[1] = "Error " . $sql ;	
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_teamweek_id($week_id, $team_num)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select * from team_color_week_tbl where week_id=" . $week_id  . " and team_id=" . $team_num  ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["id"] ;		
		}
	} else {
		$output[0] = false ;
		$output[1] = "Error " . $sql ;	
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_match_detail($matchID)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select team_a_id, team_b_id, team_a_goal, team_b_goal from match_stat_tbl where id=$matchID" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[] = true ;
		while($row = $result->fetch_assoc()) {
			$output[] = $row ;		
		}
	} else {
		$output[0] = false ;
		$output[1] = "Error " . $sql ;	
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_macth_id($matchnum,$weekid)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select id from match_stat_tbl where match_num=$matchnum and week_id=$weekid" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["id"] ;		
		}
	} else {
		$output[0] = false ;
		$output[1] = "Error " . $sql ;	
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_team_week_id($memberID,$weekID)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select team_id from member_team_week_tbl where member_id=$memberID and week_id=$weekID" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["team_id"] ;		
		}
	} else {
		$output[0] = false ;
		$output[1] = "Error " . $sql ;	
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}



function update_match_score($matchID)
{			
			$output = query_match_detail($matchID);
			if($output[0]) {
				$team_aID = $output[1]["team_a_id"] ;
				$team_bID = $output[1]["team_b_id"] ;
			} else {
					return array(false, "Error Query Match Detail");
			}
			print_r($output) ;
			$output = query_match_score($matchID);
			print_r($output) ;
			if($output[0]) {
					$team_aGoal = 0 ;
					$team_bGoal = 0 ;
					if ($team_aID == $output[1]["team_week_id"]) {
							$team_aGoal = $output[1]["goal"] ;
							if(isset($output[2]["goal"])) {
								$team_bGoal = $output[2]["goal"] ;
							}
					} elseif ($team_bID == $output[1]["team_week_id"]){
							$team_bGoal = $output[1]["goal"] ;
							if(isset($output[2]["goal"])) {
								$team_aGoal = $output[2]["goal"] ;
							}
					}
			} else {
					return array(false, "Error Query Match Score");
			}
			
			//return array(false, "Debug: team_a_goal $team_aGoal - team_b_goal $team_bGoal " . print_r($output,true)) ;
			
			$servername = "localhost";
			$username = "root";
			$password = "mysqladminroot";
			$dbname = "soccerbot";
			// Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$conn -> set_charset("utf8mb4");
	
			$sql = "update match_stat_tbl set team_a_goal=$team_aGoal, team_b_goal=$team_bGoal where id=$matchID";
			//echo $sql ;
			if ($conn->query($sql) === TRUE) {
				$output[0] = true ;
				$output[1] = "Success update Match[" .  $matchID . "] $team_aGoal - $team_bGoal" ;
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
				$output[1] = "Error: " . $sql . "\n" . $conn->error ."\n";
				$output[0] = false ;
				//$output = false ;
			}
		$conn->close();


	return $output ;

}

function add_match_goal_week($matchNum,$name,$weeknum = 0, $teamColor = "")
{
			$output = query_week_id($weeknum) ;
	
			if($output[0]) {
				$weekID = $output[1];
			} else {
				return $output ;
			}
	
			$output = query_macth_id($matchNum,$weekID);
			//echo "TEST\n" ;
			//print_r($output) ;
			if ($output[0]) {
					$matchID = $output[1];
			} else {
					return $output ;
			}
			$output = query_member_id($name);
			
			//print_r($output) ;
			
			if ($output[0]) {
					$memberID = $output[1];
			} else {
					return $output ;
			}
			
			$output = query_team_week_id($memberID,$weekID);
			
			if ($output[0]) {
				$team_member_weekID = $output[1];
			} else {
				return $output ;
			}
			
			if ($teamColor == "" ) {
				$team_weekID = $team_member_weekID ;
				$goal_status = 0 ;
			} else {
					$result = query_team_id_from_color($teamColor,$weekID) ;
					$team_weekID = $result[1] ;
					$output = query_match_detail($matchID) ;
					$team_aID = $output[1]["team_a_id"] ;
					$team_bID = $output[1]["team_b_id"] ;
					if ( ($team_member_weekID == $team_aID) || ($team_member_weekID == $team_bID) ) {
						$goal_status = 1 ;
					} else {
						$goal_status = 2 ;
					}
			}
			$servername = "localhost";
			$username = "root";
			$password = "mysqladminroot";
			$dbname = "soccerbot";
			// Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$conn -> set_charset("utf8mb4");
	
			$sql = "insert into match_goal_tbl values(default,$matchID,$memberID,$team_weekID,$goal_status)";
			//echo $sql ;
			if ($conn->query($sql) === TRUE) {
				$output[0] = true ;
				$output[1] = "Success Add Goal Match[" .  $matchID . "] $name" ;
				$result = update_match_score($matchID) ;
				$output[1] .= "\n" . $result[1] ;
				
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
				$output[1] = "Error: " . $sql . "\n" . $conn->error ."\n";
				$output[0] = false ;
				//$output = false ;
			}
		$conn->close();


	return $output ;

}

function add_match_week($matchNum,$teamA, $teamB,$weeknum = 0)
{
	
	$result = query_week_id($weeknum) ;
	
	if($result[0]) {
			$weekid = $result[1] ;
			$output = query_team_id_from_color($teamA,$weekid);
			//echo "TEST\n" ;
			print_r($output) ;
			if ($output[0]) {
					$teamAID = $output[1];
			} else {
					return $output ;
			}
			$output = query_team_id_from_color($teamB,$weekid);
			print_r($output) ;
			if ($output[0]) {
					$teamBID = $output[1];
			} else {
					return $output ;
			}
			$servername = "localhost";
			$username = "root";
			$password = "mysqladminroot";
			$dbname = "soccerbot";
			// Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$conn -> set_charset("utf8mb4");
	
			$sql = "insert into match_stat_tbl values(default,$matchNum,$teamAID,0,$teamBID,0,$weekid)";
			echo $sql ;
			if ($conn->query($sql) === TRUE) {
				$output[0] = true ;
				$output[1] = "Success Add Match[" .  $matchNum . "] $teamA vs $teamB" ;
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
				$output[1] = "Error: " . $sql . "\n" . $conn->error ."\n";
				$output[0] = false ;
				//$output = false ;
			}
		$conn->close();
	} else {
		$output[0] = false ;
		$output[1] = " Error ";
	}

	return $output ;

}

function add_week($weekdate, $year)
{
	
	$result = query_week_number() ;
	if($result[0]) {
			$weeknum = $result[1] ;
	

			$servername = "localhost";
			$username = "root";
			$password = "mysqladminroot";
			$dbname = "soccerbot";
			// Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$conn -> set_charset("utf8mb4");
	
			$sql = "insert into week_tbl values(default,$weeknum,'$weekdate',2,'$year')";
			if ($conn->query($sql) === TRUE) {
				$output[0] = true ;
				$output[1] = $weeknum ;
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
				$output[1] = "Error: " . $sql . "\n" . $conn->error ."\n";
				$output[0] = false ;
				//$output = false ;
			}
		$conn->close();
	} else {
		$output[0] = false ;
		$output[1] = " Error ";
	}

	return $output ;

}

function update_member_misc($name, $val, $weekid, $type, $id)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	if ($type == 'atk') {
		//$sql = "update member_team_week_tbl set atk=" . $val . " where name='" . $name . "' and week_id = $weekid";
		$sql = "update member_team_week_tbl set atk=" . $val . " where member_id='" . $id . "' and week_id = $weekid";
	} else {
		//$sql = "update member_team_week_tbl set pay=" . $val . " where name='" . $name . "' and week_id = $weekid";
		$sql = "update member_team_week_tbl set pay=" . $val . " where member_id='" . $id . "' and week_id = $weekid";
	}
	if ($conn->query($sql) === TRUE) {
		$output[0] = true ;
		$output[1] = "Notice: " . $sql  ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output[0] = false ;
		$output[1] = "Error: " . $sql . "<br>" . $conn->error ;
	}
	$conn->close();

	return $output ;

}

function check_member_team($member_id)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	$sql = "select * from  member_team_week_tbl where member_id='" . $member_id . "' and team_id = 0";
	$result = $conn->query($sql);
	
	//$top_assist_id["id"] = 0 ;

	if ($result->num_rows > 0) {
		$output = true ;
	} else {
		$output = false ;
	}

	$conn->close();

	return $output ;
}
//echo query_all_registered_members();

function update_member_team($name, $team_id, $weekid, $member_id)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	$sql = "select * from  member_team_week_tbl where member_id='" . $member_id . "' and team_id = 0";
	$result = $conn->query($sql);
	
	//$top_assist_id["id"] = 0 ;

	if ($result->num_rows == 0) {

	}

	//$sql = "update member_team_week_tbl set team_id=" . $team_id . " where name='" . $name . "' and week_id = $weekid";
	$sql = "update member_team_week_tbl set team_id=" . $team_id . " where member_id='" . $member_id . "' and week_id = $weekid";
	if ($conn->query($sql) === TRUE) {
		$output[0] = true ;
		$output[1] = "Notice: " . $sql  ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output[0] = false ;
		$output[1] = "Error: " . $sql . "<br>" . $conn->error ;
	}
	$conn->close();

	return $output ;

}

function remove_members($name)
{

	$result = query_week_id(0) ;
	
	if($result[0]) {
		$weekid = $result[1];
	} else {
		return array(true, "No Week ID") ;
	}

	$output = query_member_id($name) ;
	if($output[0]) {
			$member_id = $output[1] ;
	} else {
			return array(true, "User not Found") ;
	}

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	$sql = "delete from member_team_week_tbl where member_id=$member_id and week_id=$weekid";
	if ($conn->query($sql) === TRUE) {
		$output = "success" ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = "Error: " . $sql . "\n" . $conn->error ."\n";
		//$output = false ;
	}
	$conn->close();

	return $output ;

}

function clear_member_stat()
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	$sql = "delete from member_stat_tbl";
	if ($conn->query($sql) === TRUE) {
		$output = "success" ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = "Error: " . $sql . "\n" . $conn->error ."\n";
		//$output = false ;
	}
	$conn->close();

	return $output ;

}

function import_member_stat($id,$name,$goal,$mvp)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	$sql = "insert into member_stat_tbl values('$id','$name',$goal,$mvp)";
	if ($conn->query($sql) === TRUE) {
		$output = "success" ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = "Error: " . $sql . "\n" . $conn->error ."\n";
		//$output = false ;
	}
	$conn->close();

	return $output ;

}

function register_members($name, $weeknum =0)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	$result = query_week_id($weeknum) ;
	if($result[0]) {
			$weekid = $result[1] ;
	} else {
		$output = "Error: can query week id\n" . $conn->error ."\n";
		return $output ;
	}
	$output = query_member_id($name) ;
	if($output[0]) {
			$member_id = $output[1] ;
	} else {
			$output[1] = "User $name not found" ;
			return $output ;
	}
	$sql = "insert into member_team_week_tbl values(default,$member_id,'$name',0,$weekid,0,0)";
	if ($conn->query($sql) === TRUE) {
		$output = "success" ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = "Error: " . $sql . "\n" . $conn->error ."\n";
		//$output = false ;
	}
	$conn->close();

	return $output ;

}

function query_no_paid_members($weeknum = 0, $noteam = 0, $name = '')
{
	
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
	} else {
			return $output ;
	}
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select * from hof_tbl" ;

	$result = $conn->query($sql);
	
	//$top_assist_id["id"] = 0 ;

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if ($row["type"] == 0) {
				$top_scorer_id = $row ;
			} elseif ($row["type"] == 1) {
				$top_assist_id = $row ;
			}
		}
	}

	$sql = "SELECT member_tbl.name, member_tbl.alias, member_team_week_tbl.team_id, member_team_week_tbl.atk, member_team_week_tbl.pay, member_tbl.id, member_tbl.donate FROM member_team_week_tbl, member_tbl where member_team_week_tbl.week_id = $weekid and member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.pay=0 and member_tbl.id <> 14";

	if ($noteam == 1) { 
		$sql .= " and member_team_week_tbl.team_id = 0" ;  
		//$header = "สมาชิกที่ยังไม่มีทีม ($weekdate)```\n" ;
		$header = "สมาชิกที่ยังไม่มีทีม ($weekdate)\n\n" ;
	} else {
		//$header = " ลงชื่อเตะบอล ($weekdate)```\n" ;
		$header = "คนที่ยังไม่ได้จ่ายค่าสนาม ($weekdate)\n\n" ;
	}
	$top = "" ;
	if ($noteam == 2 ) {
		//$header = " ลงชื่อเตะบอล ($weekdate)```\n" ;
		$top = "$name" ;
	}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = $header ;
		$teamcount = 0;
		
		while($row = $result->fetch_assoc()) {
			$teamcount++ ;
			$name = "" ;
			if ($row["id"] == $top_scorer_id["member_id"]) {
				//$name = "`" . $top_scorer_id["badge"] . "- " . $row["name"] . "`" ;
				$name = $top_scorer_id["badge"] . "- " . $row["name"] ;
			} elseif ($row["id"] == $top_assist_id["member_id"]) {
				//$name = "`" . $top_assist_id["badge"] . " - " . $row["name"] . "`";
				$name = $top_assist_id["badge"] . " - " . $row["name"] ;
			} else {
				$name = $row["name"] ;
			}

			if ($row['donate'] == 1) {
				$name = "🎗️ " . $name ;
			}
			/*if ( ($row["atk"] == 1) ||  ($row["pay"] == 1) ) {
				$name .= " -" ;
				//if ($row["atk"] == 1) $name .= " 💚" ;
				if ($row["pay"] == 1) $name .= " 🤑" ;
			}*/
			
			$output .= $teamcount . ". " . $name ."\n";	
		}
		//$output = " ```+$teamcount " . $output ;
		$output = $top . "$teamcount " . $output ;
	} else {
		//$output = "no registered member $sql\n";
		$output = "จ่ายครบหมดแล้ว" ;
	}
	$conn->close();
	
	return "$output";

}

function query_all_registered_members2($weeknum = 0, $noteam = 0)
{
	
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
	} else {
			return $output ;
	}
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "select * from hof_tbl" ;

	$result = $conn->query($sql);
	
	
	
	//$top_assist_id["id"] = 0 ;

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if ($row["type"] == 0) {
				$top_scorer_id = $row ;
			} elseif ($row["type"] == 1) {
				$top_assist_id = $row ;
			}
		}
	}
/*
	$sql = "select * from team_fav" ;

	$result = $conn->query($sql);
	$team_fav = [] ;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$team_fav[] = $row["emoticon"] ;
		}
	}*/

	//$sql = "SELECT member_tbl.name, member_tbl.alias, member_team_week_tbl.team_id, member_team_week_tbl.atk, member_team_week_tbl.pay, member_tbl.id, member_tbl.donate, member_tbl.team_id FROM member_team_week_tbl, member_tbl where member_team_week_tbl.week_id = $weekid and member_tbl.id = member_team_week_tbl.member_id";

	$sql = <<<EOT
	SELECT member_tbl.name, member_tbl.alias, member_team_week_tbl.team_id, member_team_week_tbl.atk, member_team_week_tbl.pay, member_tbl.id, member_tbl.donate, member_tbl.team_id, team_fav.emoticon FROM 
member_team_week_tbl
INNER JOIN 
member_tbl ON member_tbl.id = member_team_week_tbl.member_id
LEFT JOIN
team_fav ON member_tbl.team_id=team_fav.id
where member_team_week_tbl.week_id = $weekid
EOT;

	if ($noteam == 1) { 
		$sql .= " and member_team_week_tbl.team_id = 0" ;  
		//$header = "สมาชิกที่ยังไม่มีทีม ($weekdate)```\n" ;
		$header = "สมาชิกที่ยังไม่มีทีม ($weekdate)\n\n" ;
	} else {
		//$header = " ลงชื่อเตะบอล ($weekdate)```\n" ;
		$header = " ลงชื่อเตะบอล ($weekdate)\n\n" ;
	}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = $header ;
		$teamcount = 0;
		$gk_count = 0;
		$gk_output = "" ;
		//$memcount = $result->num_rows ;
		
		while($row = $result->fetch_assoc()) {
			if ($row["team_id"] != 100) {
				$teamcount++ ;
			} else {
				$gk_count++ ;
			}
			$name = "" ;
			$alias = $row["alias"] . " " . $row["emoticon"];
			/*
			if ($row['team_id'] > 0 && $row['team_id'] < 10) {
				$tdex = $row['team_id'] - 1 ;
				$alias .= " " . $team_fav[$tdex] ;
			}*/
			$mbname = $row["name"] . " (" . $row["alias"] . ")" ;
			//$mbname = $alias ;
			if ($row["id"] == $top_scorer_id["member_id"]) {
				//$name = "`" . $top_scorer_id["badge"] . "- " . $row["name"] . "`" ;
				$name = $top_scorer_id["badge"] . "- " . $mbname ;
			} elseif ($row["id"] == $top_assist_id["member_id"]) {
				//$name = "`" . $top_assist_id["badge"] . " - " . $row["name"] . "`";
				$name = $top_assist_id["badge"] . " - " . $mbname ;
			} else {
				$name = $mbname ;
			}

			if ($row['donate'] == 1) {
				$name = "🎗️ " . $name ;
			} elseif ($row['donate'] == 2) {
				$name = "👑 " . $name ;
			}
			
			/*
			if ( ($row["atk"] == 1) ||  ($row["pay"] == 1) ) {
				$name .= " -" ;
				if ($row["atk"] == 1) $name .= " 💚" ;
				if ($row["pay"] == 1) $name .= " 🤑" ;
			}*/
			if ($row["team_id"] != 100) {	
				if ($teamcount % 24 == 1 && $teamcount > 1) {
					$output .= "--- รายชื่อสำรอง ---\n";	
				}
				if ($teamcount > 24) {
					$num_team = $teamcount % 24 ;
					
				} else {
					$num_team = $teamcount ;
					//$output .= $num_team . ". " . $name ."\n";
				}
				$output .= $num_team . ". " . $name ."\n";
			} else {
				$gk_output .= $gk_count . ". " . $name ."\n";
			}
			
		}
		if ($teamcount > 24) {
			$teamcount = "24(" . ($teamcount % 24) . ")" ;
		}

		
		//$output = " ```+$teamcount " . $output ;
		$output = "+$teamcount " . $output ;
		if ($gk_count > 0) {
			$output .= "--- รายชื่อโกล์ ---\n";
			$output .= $gk_output ;
		}
	} else {
		//$output = "no registered member $sql\n";
		$output = "no" ;
	}
	$conn->close();
	
	return "$output";

}

function query_all_registered_members($weeknum = 0)
{
	
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
	} else {
			return $output ;
	}
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT member_tbl.name, member_tbl.alias, member_team_week_tbl.team_id FROM "
		. "member_team_week_tbl, member_tbl where member_team_week_tbl.week_id = $weekid and member_tbl.id = member_team_week_tbl.member_id";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = "ลงชื่อเตะบอล ($weekdate)\n\n" ;
		$teamcount = 0;
		$memcount = $result->num_rows ;
		$i=0 ;
		while($row = $result->fetch_assoc()) {
			$teamcount++ ;
			if ($row["alias"] <> "") {
				$name = $row["alias"];
			} else {
				$name = $row["name"];
			}
			$output .= $teamcount . ". " . $name ;	
			$i++ ;
			if($i < $memcount) {
				$output .= "\n" ;
			}
		}
		$output = "+$teamcount " . $output ;
	} else {
		$output = "no registered member $sql\n";
	}
	$conn->close();
	
	return "$output";

}

function query_member_name($member_id)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT name, alias FROM member_tbl where id=$member_id";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		
		while($row = $result->fetch_assoc()) {
			
			if ($row["alias"] == "") 
			{
				$output = $row["name"] ;
			} else {
				$output = $row["alias"] ;
			}
		}
	} else {
		$output = "none";
	}
	$conn->close();
	
	return $output;

}

function query_member_id($member_name)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT id FROM member_tbl where name='$member_name'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["id"] ;
		}
	} else {
		$output[0] = false ;
		$output[1] = "$sql" ;
	}
	$conn->close();
	
	return $output;

}

function query_team_colors_byID($team_id)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT color FROM team_color_week_tbl where id=$team_id";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		
		while($row = $result->fetch_assoc()) {
			$output = $row["color"] ;
	
		}
	} else {
		$output = "none";
	}
	$conn->close();
	
	return $output;

}




function random_member($limit = 3)
{
	$weeknum = 0 ;
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			//$weekdate = $output[2];
	} else {
			return $output ;
	}

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	if( $limit == 2) { 
		$team_tpl  = ["Black","Red"] ;
	} else {
		$team_tpl  = ["Black","Red","White"];
	}

	shuffle($team_tpl);
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT member_tbl.name FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid AND member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.team_id=0 and member_team_week_tbl.team_id=0 ORDER BY RAND() LIMIT $limit;";
	$result = $conn->query($sql);

	//$ret = "" ;

	if ($result->num_rows > 0) {
    // output data of each row
		$ret = "ผลสุ่มสมาชิก\n\n" ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$ret .=  $row["name"] . " ไปอยู่ทีม " . $team_tpl[$i] . "\n" ;
			$i++ ;
		}
	} else {
		$ret = "none";
	}
	$conn->close();
	
	return $ret;

}

function random_captain($weeknum = 0)
{
	
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			//$weekdate = $output[2];
	} else {
			return $output ;
	}

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT member_tbl.name FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid AND member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.team_id=0 and member_tbl.team_id > 0 ORDER BY RAND() LIMIT 3;";
	$result = $conn->query($sql);

	//$ret = "" ;

	if ($result->num_rows > 0) {
    // output data of each row
		$ret = "ผลสุ่มสมาชิก\n\n" ;
		while($row = $result->fetch_assoc()) {
			$ret .= $row["name"] . "\n" ;
		}
	} else {
		$ret = "none";
	}
	$conn->close();
	
	return $ret;

}


function query_team_colors($team_id, $weekid)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT color FROM team_color_week_tbl where id=$team_id and week_id=$weekid";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		
		while($row = $result->fetch_assoc()) {
			$output = $row["color"] ;
	
		}
	} else {
		$output = "none";
	}
	$conn->close();
	
	return $output;

}

function update_team_colors($color,$team_id)
{

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "update team_tbl set name = '$color' where id=$team_id";

	if ($conn->query($sql) === TRUE) {
		$output = true ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = false ;
	}
	$conn->close();
	
	return $output;

}

function remove_all_members()
{

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "delete from member_tbl";

	if ($conn->query($sql) === TRUE) {
		$output = true ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = false ;
	}
	$conn->close();
	
	return $output;

}

function reset_team_members()
{

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "update member_tbl set team_id = 0";

	if ($conn->query($sql) === TRUE) {
		$output = true ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = false ;
	}
	$conn->close();
	
	return $output;

}

function reset_team_colors()
{

	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "update team_tbl set name = 'None' where id > 0";

	if ($conn->query($sql) === TRUE) {
		$output = true ;
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
		$output = false ;
	}
	$conn->close();
	
	return $output;

}

function query_members_count2($weeknum = 0)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
			$weeknum = $output[3];
	} else {
			return $output ;
	}
	
	$output = query_week_location($weekid) ;
	if($output[0]) {
			$location = $output[1] ;
	}
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT name, team_id, member_id  FROM member_team_week_tbl where week_id=$weekid";
	$result = $conn->query($sql);
	$numrow = $result->num_rows ;
	
	$conn->close();
	
	
	return $numrow  ;

}

function query_all_team_members($weeknum = 0)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
			$weeknum = $output[3];
	} else {
			return $output ;
	}
	
	$output = query_week_location($weekid) ;
	if($output[0]) {
			$location = $output[1] ;
	}
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT name, team_id, member_id FROM member_team_week_tbl where week_id=$weekid order by team_id";
	$result = $conn->query($sql);
	$numrow = $result->num_rows ;
	if ($result->num_rows > 0) {
    // output data of each row
		//$output = "Registered Team Members\n\n" ;
		$output = "+$numrow Team Week $weeknum @ $weekdate\n" ;
		$output .= "สนาม: $location\n" ;
		$last_id = -1 ;
		$i = 0 ;
		$j = 0 ;
		$team_count = 0 ;
		while($row = $result->fetch_assoc()) {
			//$output .= "\n** $last_id <> " . $row["team_id"] . "**\n" ;
			$j++ ;
			if ($last_id < $row["team_id"]) {
				$last_id = $row["team_id"] ;
				
				if ($last_id > 0) {
					$i++ ;
					$output .= "\nTeam [" . query_team_colors($last_id,$weekid) . "]\n" ;
				} else {
					$output .= "\nNone Random Team Members\n" ;
				}
				$team_count = 0 ;
			}
			$team_count++ ;
			$name = query_member_name($row["member_id"]);
			$output .= $team_count . ". " .  $name ;
			if ($j < $numrow) { $output .= "\n" ;}
		}
	} else {
		$output = "no registered member\n\n";
	}
	$conn->close();
	
	
	return $output  ;

}

function check_registered_members($name, $check_team = false, $weeknum = 0)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$week_id = $output[1] ;
	} else {
			return array(1, "Week not Found") ;
	}
	
	$output = query_member_id($name) ;
	if($output[0]) {
			$member_id = $output[1] ;
	} else {
			return array(1, "User not Found") ;
	}
		
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT id, team_id, name FROM member_team_week_tbl where member_id = $member_id and week_id = $week_id" ;
	//if ($check_team) {
	//		$sql .= " and team_id = 0" ;
	//}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		while($row = $result->fetch_assoc()) {
		//	$output = "id: " . $row["id"]. " - Name: " . $row["name"]. " " . "<br>";
			$output[1] = $row["team_id"] ;
		}
		$output[0] = 2;
		$output[1] = "Already Registered" ;
		$output[2] = $sql ;
		//$output[1] = $row["team_id"] ;
	} else {
		$output[0] = 0;
		$output[1] = $sql ;
	}
	$conn->close();

	return $output ;
}

function query_all_team_colors($weeknum = 0)
{
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
			$weeknum = $output[3];
	} else {
			return $output ;
	}
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$sql = "SELECT team_id,color FROM team_color_week_tbl where week_id = $weekid";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
    // output data of each row
		//$output = "Registered Team Members\n\n" ;
		$output = "All Team Color\n\n" ;
		while($row = $result->fetch_assoc()) {
			$output .= "Team " . $row["team_id"] . " => " . $row["color"] . "\n" ;
		}
	} else {
		$output = "no result\n\n";
	}
	$conn->close();
	
	
	return $output ;

}
?>
