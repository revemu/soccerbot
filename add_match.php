<?php

header('Access-Control-Allow-Origin: *');

require_once('./logger.php');

$log = new Logger("log.txt");
$log->setTimestamp("D M d 'y h.i A");
/*
			[weekid]
            [matchnum]
            [teamaid]
            [teambid]

add_match_week($matchNum,$teamAid, $teamBid,$weekid)
*/

if (	isset($_POST["weekid"]) && isset($_POST["matchnum"]) && isset($_POST["teamaid"]) && isset($_POST["teambid"]) )
{
		$weekid = $_POST["weekid"];
		$matchnum = $_POST["matchnum"];
		$team_a_id = $_POST["teamaid"];
		$team_b_id = $_POST["teambid"];
		
		$log->putLog("\nget_info.php add week $weekid match $matchnum team $team_a_id vs team $team_b_id") ;
		add_match_week($matchnum,$team_a_id, $team_b_id, $weekid) ;
		
} elseif (	isset($_POST["memberid"]) && isset($_POST["matchid"]) && isset($_POST["teamid"]) ) {

//function add_match_goal($match_id, $team_id, $member_id)
		$memberid = $_POST["memberid"] ;
		$matchid = $_POST["matchid"] ;
		$teamid = $_POST["teamid"] ;
		$statusid = $_POST["statusid"] ;
		add_match_goal($matchid, $teamid, $memberid, $statusid);
		update_match_score($matchid) ;
		$log->putLog("\nget_info.php add goal match_id $matchid from member id $memberid team id $teamid") ;
} elseif (	isset($_POST["goalid"])  ) {

//function add_match_goal($match_id, $team_id, $member_id)
		$goal_id = $_POST["goalid"] ;
		$result = do_query("select * from match_goal_tbl where id = $goal_id") ;
		if (!empty($result)) {
				$matchid = $result[0]["match_id"] ;
		}
		remove_match_goal($goal_id);
		update_match_score($matchid) ;
		$log->putLog("\nget_info.php remove goal_id $goal_id in match_id $matchid\n") ; 
} elseif (	isset($_POST["matchid"]) &&  isset($_POST["cmd"])) {

		$match_id = $_POST["matchid"] ;
		
		$sql = "delete from match_goal_tbl where match_id=$match_id" ;
		do_update($sql) ;
		
		$sql = "delete from match_stat_tbl where id=$match_id" ;
		do_update($sql) ;
		//remove_match_goal($goal_id);
		//update_match_score($matchid) ;
		$log->putLog("\nget_info.php remove match_id $match_id\n") ; 
} else {
		update_table_week(11) ;
		//$log->putLog("get_info.php Request " . print_r($_REQUEST, true), true) ;
		//update_match_score(132) ;
		//$sql = "update match_stat_tbl set team_a_goal=0, team_b_goal=0 where id=132" ;
		//do_update($sql) ;
}

if (isset($_POST["weekid"])) {
		$log->putLog("add_match.php Request " . print_r($_REQUEST, true), true) ;
		$weekid = $_POST["weekid"] ;
		update_table_week($weekid) ;
}



function do_update($sql)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->exec("set names utf8mb4");
		//echo "Connected successfully";
		try {
				//$sql = "SELECT id,color FROM team_color_week_tbl where week_id = $weekid";
				
				$array = $conn->query($sql) ;
				return($array);
				
		} catch(PDOException $e)
		{
			//echo "\n$sql\n" ;
			echo "Query failed: " . $e->getMessage();
		}
		
		
    } catch(PDOException $e)
    {
		echo "Connection failed: " . $e->getMessage();
    }
}

function do_query($sql)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->exec("set names utf8mb4");
		//echo "Connected successfully";
		try {
				//$sql = "SELECT id,color FROM team_color_week_tbl where week_id = $weekid";
				
				$array = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
				return($array);
				
		} catch(PDOException $e)
		{
			//echo "\n$sql\n" ;
			echo "Query failed: " . $e->getMessage();
		}
		
		
    } catch(PDOException $e)
    {
		echo "Connection failed: " . $e->getMessage();
    }
}

function update_match_score($matchid)
{			
	$sql = <<<EOT
	SELECT match_goal_tbl.team_week_id , COUNT(*) as goal
from match_goal_tbl, match_stat_tbl 
WHERE match_stat_tbl.id = match_goal_tbl.match_id
AND match_goal_tbl.match_id = $matchid AND match_goal_tbl.team_week_id = match_stat_tbl.team_a_id AND match_goal_tbl.status <> 3
GROUP BY team_week_id
	
EOT;
	echo $sql . "\n" ;
	$team_aGoal = 0 ;
	$result = do_query($sql) ;
	print_r($result) ;
	if (!empty($result)) {	$team_aGoal = $result[0]["goal"] ; }
	
	$sql = <<<EOT
	SELECT match_goal_tbl.team_week_id , COUNT(*) as goal
from match_goal_tbl, match_stat_tbl 
WHERE match_stat_tbl.id = match_goal_tbl.match_id
AND match_goal_tbl.match_id = $matchid AND match_goal_tbl.team_week_id = match_stat_tbl.team_b_id AND match_goal_tbl.status <> 3
GROUP BY team_week_id
	
EOT;
	echo $sql . "\n" ;	
	$result = do_query($sql) ;
	$team_bGoal = 0 ;
	print_r($result) ;
	if (!empty($result)) {	$team_bGoal = $result[0]["goal"] ; } 
	
	
	$sql = "update match_stat_tbl set team_a_goal=$team_aGoal, team_b_goal=$team_bGoal where id=$matchid";
	
	echo $sql . "\n" ;
	do_update($sql) ;
			

}

function remove_match($match_id)
{
	
	$sql = "delete from match_stat_tbl where id=$match_id";
	
	do_update($sql) ;

}

function remove_match_goal($goal_id)
{
	
	$sql = "delete from match_goal_tbl where id=$goal_id";
	
	do_update($sql) ;

}

function add_match_goal($match_id, $team_id, $member_id, $status_id = 0)
{
	
	$sql = "insert into match_goal_tbl values(default, $match_id, $member_id, $team_id, $status_id)";
	
	do_update($sql) ;

}

function add_match_week($matchNum,$teamAid, $teamBid,$weekid)
{
	
	$sql = "insert into match_stat_tbl values(default,$matchNum,$teamAid,0,$teamBid,0,$weekid)";
	
	do_update($sql) ;

}

function check_team_table_exist($team_ID, $weekID)
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
	
	$sql = "select id from table_week_tbl where team_week_id=$team_ID and week_id=$weekID" ;
	//echo $sql ."\n" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
	} else {
		$output[0] = false ;
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function update_table_week($weekid) {

	$team_week = query_all_team_week_id($weekid) ; 
	//print_r($team_week) ;
	//exit ;
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
	
	$sql = "SELECT * FROM match_stat_tbl where week_id = $weekid";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$teamcount = 0;
		
		while($row = $result->fetch_assoc()) {
			$team_aID = $row["team_a_id"] ;
			$team_bID= $row["team_b_id"] ;
			$team_aGoal = $row["team_a_goal"] ;
			$team_bGoal = $row["team_b_goal"] ;
			$team_week[$team_aID]["G"] += $team_aGoal ;
			$team_week[$team_aID]["A"] += $team_bGoal ;
			$team_week[$team_bID]["G"] += $team_bGoal ;
			$team_week[$team_bID]["A"] += $team_aGoal ;
			if ($team_aGoal == $team_bGoal) {
				$team_week[$team_aID]["d"] ++ ;
				$team_week[$team_bID]["d"] ++ ;
			} elseif ($team_aGoal > $team_bGoal) {
				$team_week[$team_aID]["w"] ++ ;
				$team_week[$team_bID]["l"] ++ ;
			} elseif ($team_aGoal < $team_bGoal) {
				$team_week[$team_aID]["l"] ++ ;
				$team_week[$team_bID]["w"] ++ ;
			}
			
		}
		//$output = "+$teamcount " . $output ;
	} else {
		$output[0] = false ;
		$output[1] = "Error \n$sql\n";
	}
	$conn->close();
	$output[1] = "" ;
	foreach ($team_week as $team) {
			if(isset($team["team_id"])) {
				$team["pts"] = ($team["w"] * 3) + $team["d"];
				$team_week[$team["team_id"]]["pts"] = $team["pts"] ;
				$result = add_table_week($team["team_id"],$weekid,$team) ;
				if ($result[0]) {
						$output[1] .= $result[1] ;
				}
				//print_r($result) ;
			}
			$output[0]=true ;
	}
	print_r($team_week) ;
	return $output ;
	
}


function add_table_week($teamID, $weekID, $record)
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
			$w = $record["w"] ;
			$d = $record["d"] ;
			$l = $record["l"] ;
			$G = $record["G"] ;
			$A = $record["A"] ;
			$pts = $record["pts"] ;
			$result = check_team_table_exist($teamID, $weekID) ;
			if ($result[0]) {
				$sql = "update table_week_tbl set w=$w, d=$d, l=$l, G=$G, A=$A, pts=$pts where team_week_id=$teamID and week_id=$weekID" ;
			} else {
				$sql = "insert into table_week_tbl values(default,$teamID,$weekID,$w,$d,$l,$G,$A,$pts)";
			}
			//echo $sql ;
			//return ;
			if ($conn->query($sql) === TRUE) {
				$output[0] = true ;
				$output[1] = "Success Add Team ID $teamID to Table Week $weekID\n" ;
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error;
				$output[1] = "Error: " . $sql . "\n" . $conn->error ."\n";
				$output[0] = false ;
				//$output = false ;
			}
		$conn->close();

	return $output ;

}

function query_all_team_week_id($weekID)
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
	
	$sql = "select id from team_color_week_tbl where week_id=$weekID" ;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[$row["id"]] = array(
				"team_id" => $row["id"],
				"w" => 0,
				"d" => 0,
				"l" => 0,
				"G" => 0,
				"A" => 0,
				"pts" => 0,
			) ;
		}
	} else {
		$output[0] = false ;
		$output[1] = "Error " . $sql ;	
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

?>