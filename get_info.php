<?php 

header('Access-Control-Allow-Origin: *');

require_once('./logger.php');

$log = new Logger("log.txt");
$log->setTimestamp("D M d 'y h.i A");

//$log->putLog("get_info.php Request " . print_r($_REQUEST, true), true) ;

$cmd = $_GET["cmd"] ;

switch ($cmd) {
	
		case "get_last_matchweek":
			if (isset($_GET["weeknum"])) {
				$weeknum = $_GET["weeknum"] ;
			} else {
				$weeknum = 0 ;
			}
			$result = get_last_match_week($weeknum) ;
			//$log->putLog("get_info.php Request " . print_r($result, true), true) ;
			break ;
		case "get_team_members":
			if (isset($_GET["matchid"])) {
				$matchid = $_GET["matchid"] ;
			}
			$result = query_team_members($matchid) ;
			echo json_encode($result) ;
			$log->putLog("get_info.php Request " . print_r($result, true), true) ;
			break ;
		default:
			get_last_match_week(5) ;
			break ;
			
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

function query_team_members($matchid)
{
		$sql = <<<EOT
		SELECT member_tbl.id, member_tbl.name, member_tbl.alias, member_team_week_tbl.team_id, team_color_week_tbl.color
		FROM member_team_week_tbl, member_tbl , match_stat_tbl, team_color_week_tbl
		WHERE ( match_stat_tbl.team_a_id = member_team_week_tbl.team_id 
		OR match_stat_tbl.team_b_id = member_team_week_tbl.team_id)
		and member_tbl.id = member_team_week_tbl.member_id
		AND member_team_week_tbl.team_id = team_color_week_tbl.id
		and match_stat_tbl.id = $matchid ORDER BY team_id
EOT;
		
		$result = do_query($sql) ;
		return $result ;
}

function query_registered_members($weekid)
{
		//$sql = "SELECT member_tbl.id, member_tbl.name, member_tbl.alias, member_team_week_tbl.team_id FROM "
		//. "member_team_week_tbl, member_tbl where member_team_week_tbl.week_id = $weekid and member_tbl.id = member_team_week_tbl.member_id";
		
		$sql = <<<EOT
		SELECT member_tbl.id, member_tbl.name, member_tbl.alias, 
	member_team_week_tbl.team_id, team_color_week_tbl.color
	FROM member_team_week_tbl, member_tbl, team_color_week_tbl 
	where member_team_week_tbl.week_id = $weekid
	and member_tbl.id = member_team_week_tbl.member_id
	AND member_team_week_tbl.team_id = team_color_week_tbl.id
	order by team_id
EOT;
		
		$result = do_query($sql) ;
		//print_r($result) ;
		return $result ;
}

function query_match_scorer($matchID)
{
	
	$sql = <<<EOT
	SELECT member_tbl.alias as name, match_goal_tbl.id AS goal_id, match_goal_tbl.status as goal_status 
FROM match_goal_tbl, member_tbl
WHERE match_goal_tbl.match_id=$matchID and match_goal_tbl.member_id = member_tbl.id
EOT;
	$result = do_query($sql) ;
	
	return $result ;
}

function query_week_table($weekid)
{
	
	$sql = <<<EOT
	SELECT team_color_week_tbl.color, table_week_tbl.* 
FROM table_week_tbl , team_color_week_tbl
where table_week_tbl.week_id = $weekid
AND table_week_tbl.team_week_id = team_color_week_tbl.id order by table_week_tbl.pts DESC
EOT;
	
	$result = do_query($sql) ;
	
	//print_r($result) ;
	return $result ;
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

function query_all_team_colors($weekid)
{
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo "Connected successfully";
		try {
				$sql = "SELECT id,color FROM team_color_week_tbl where week_id = $weekid";
				
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

function query_all_match_week($weekid, $limit = 2)
{
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	// Create connection
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "soccerbot";
	
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo "Connected successfully";
		try {
				$sql = "SELECT * from match_stat_tbl where week_id=$weekid order by match_num DESC limit $limit";
				//echo "$sql\n" ;
				$array = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
				return($array);
		} catch(PDOException $e)
		{
			echo "\n$sql\n" ;
			echo "Query failed: " . $e->getMessage();
		}
		
		
    } catch(PDOException $e)
    {
		echo "Connection failed: " . $e->getMessage();
    }
}


function get_last_match_week($weeknum = 0) {

	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
			$weeknum = $output[3];
			$sql_week = $output[4];
	} else {
			return array(0,"1","No Match for Week $weeknum","2") ;
	}

	$result = query_registered_members($weekid) ;
	if (!empty($result)) {
		$data["allmembers"] = $result ;
	}

	
	
	$result = query_all_team_colors($weekid) ;
	$i = 0 ;
	foreach ($result as $team) {
		$team_stat[$team["id"]]["cnt"] = 0 ;
		$team_stat[$team["id"]]["id"] = $team["id"] ;
		
		//$team_json["team" . $team["id"]]["teamid"] = $team["id"] ;
		//$team_json["team" . $team["id"]]["name"] = $team["color"] ;
		
		$team_json[$team["id"]]["teamid"] = $team["id"] ;
		$team_json[$team["id"]]["name"] = $team["color"] ;
		
	}
		$next_match["week_id"] = $weekid ;
		$next_match["match_num"] = 1 ;
		$next_match["team_a_id"] = $result[0]["id"] ;
		$next_match["team_b_id"] = $result[1]["id"] ;
		
		$array = query_all_match_week($weekid) ;
		
		if (empty($array)) {
					
					
		} else {
					
			$last_match = $array[0] ;
			$next_match["match_num"] = $last_match["match_num"]+1 ;
			$txtlog = "" ;
			foreach ($array as $match) {
						
				$txtlog = $match["match_num"] . " team_a " . $match["team_a_id"] 
						. " vs team_b " . $match["team_b_id"] ."\n" . $txtlog ;
						
				$team_stat[$match["team_a_id"]]["cnt"] ++ ;
				$team_stat[$match["team_b_id"]]["cnt"] ++ ;
						
				if ($team_stat[$match["team_a_id"]]["cnt"] > 1) {
					$next_match["team_a_id"] = $team_stat[$match["team_b_id"]]["id"] ;
						$next_match["team_b_id"] = $last_match["team_b_id"] ;
				} elseif ($team_stat[$match["team_b_id"]]["cnt"] > 1) {
					$next_match["team_b_id"] = $team_stat[$match["team_a_id"]]["id"] ;
					$next_match["team_a_id"] = $last_match["team_a_id"] ;
				}
						
			}
					
			//echo $txtlog ;
					
		}
		
		$result= query_all_match_week($weekid, 30) ;
		
		if (!empty($array)) {
			foreach ($result as $team) {
				$all_match[$team["match_num"]] = $team ;
				$all_match[$team["match_num"]]["team_a_color"] = $team_json[$team["team_a_id"]]["name"] ;
				$all_match[$team["match_num"]]["team_b_color"] = $team_json[$team["team_b_id"]]["name"] ;
				$all_match[$team["match_num"]]["goal"] = query_match_scorer($team["id"]) ;
			}
			$data["all_match"] = $all_match ;
			$data["members"] = query_team_members($all_match[$team["match_num"]]["id"]) ;		
		} 
		
		$result= query_week_table($weekid) ;
		if (!empty($result)) {
			$data["tableweek"] = $result ;
		}
		
		$data["next_match"] = $next_match ;
		$data["team_week"] = $team_json ;
		
		

		echo json_encode($data) ;	
		
		return $data ;
		//echo $message ;
		
		//echo $message ;

}

?>