<?php

function get_colors_code($color)
{
	$color = strtolower($color) ;
	switch ($color) { 
		case "red": 
			return "#ff0000" ;
			
		break;
		case "green":
			return "#11872e" ;
		break;
		case "white":
			return "#D3D3D3" ;
		break;
		case "black";
			return "#000000";
		break;
		default:
		break ;
		
	}
	
	return 0 ;
}

function query_colors_image_url($color)
{
	$color = strtolower($color) ;
	switch ($color) { 
		case "red": 
			return "https://api.revemu.org/red2.jpg" ;
			//return "https://cdn.planetradio.co.uk/one/media/5ef4/f21f/3fbf/bd77/2d76/afc0/Champ19nsv2.jpg?quality=80&format=jpg&crop=48,0,723,1200&resize=crop";
			
		break;
		case "white":
			return "https://api.revemu.org/white2.jpg" ;
		break;
		case "green":
			return "https://api.revemu.org/green.jpg" ;
		break;
		case "black";
			return "https://api.revemu.org/black2.jpg";
		break;
		default:
		break ;
		
	}
	
	return 0 ;
}


function query_member_week_goal($member_id, $weekid)
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
	
	$sql = <<<EOT
	
	SELECT COUNT(*) AS goal
FROM match_goal_tbl, match_stat_tbl
where match_stat_tbl.week_id = $weekid
AND match_goal_tbl.match_id = match_stat_tbl.id
AND match_goal_tbl.member_id =$member_id
AND match_goal_tbl.status <> 3
	
EOT;
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output["code"] = 1 ;
		while($row = $result->fetch_assoc()) {
			$output["data"] = $row["goal"] ;
		}
	} else {
		$output["code"] = 0 ;
	}
	$conn->close();
	
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
	
	return  $output;
}

function query_team_colors_members2($weeknum = 0)
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

/*	
	$sql = <<< EOT
	
	SELECT member_team_week_tbl.name, member_team_week_tbl.team_id, 
member_team_week_tbl.member_id , match_goal_tbl.id, COUNT(*) AS goal
FROM member_team_week_tbl, match_goal_tbl, match_stat_tbl
where member_team_week_tbl.week_id=$weekid
AND member_team_week_tbl.member_id = match_goal_tbl.member_id
AND match_goal_tbl.match_id = match_stat_tbl.id
and match_stat_tbl.week_id = member_team_week_tbl.week_id
group BY member_id
order by goal DESC
	
EOT;
	*/
	echo "$sql" ;
	
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
		$team_info = array() ;
		while($row = $result->fetch_assoc()) {
			//$output .= "\n** $last_id <> " . $row["team_id"] . "**\n" ;
			$j++ ;
			if ($last_id < $row["team_id"]) {
				$last_id = $row["team_id"] ;
				
				if ($last_id > 0) {
					$i++ ;
					$team_info[$i]["color"] = query_team_colors($last_id,$weekid) ;
					$team_info[$i]["txt"] = "" ;
					//$output .= "\nTeam [" . query_team_colors($last_id,$weekid) . "]\n" ;
				} else {
					$team_info[$i]["color"] = "none" ;
					//$output .= "\nNone Random Team Members\n" ;
				}
				$team_count = 0 ;
			} else {
				$team_info[$i]["txt"] .= ", " ;	
			}
			$team_count++ ;
			$name = query_member_name($row["member_id"]);
			$rawname = $name ;
			$member_id = $row["member_id"] ;
			$goal = 0 ;
			
			//$team_info[$i]["member"][$]["name"] = $name ;
			//$team_info[$i]["member"][$member_id]["goal"] = 0 ;
			$response = query_member_week_goal($row["member_id"],$weekid) ;
			if ($response["code"] == 1) {
				if ($response["data"] > 0) { 
					$name .= "(+" . $response["data"] . ")" ;
					$goal = $response["data"] ;
				}
			}
			$team_info[$i]["member"][] = array( "id" => $row["member_id"], "name" => $rawname, "goal" => $goal ) ;
			$team_info[$i]["txt"] .= getFlexText($name,"md","center") ;
			$output .= $team_count . ". " .  $name ;
			
		}
	} else {
		$output = "no registered member\n\n";
	}
	$conn->close();
	
	
	return $team_info  ;

}


function new_query_team_colors_members($weeknum = 0)
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
	
	$sql = "SELECT name, team_id, member_id FROM member_team_week_tbl where week_id=$weekid and team_id > 0 order by team_id";

/*	
	$sql = <<< EOT
	
	SELECT member_team_week_tbl.name, member_team_week_tbl.team_id, 
member_team_week_tbl.member_id , match_goal_tbl.id, COUNT(*) AS goal
FROM member_team_week_tbl, match_goal_tbl, match_stat_tbl
where member_team_week_tbl.week_id=$weekid
AND member_team_week_tbl.member_id = match_goal_tbl.member_id
AND match_goal_tbl.match_id = match_stat_tbl.id
and match_stat_tbl.week_id = member_team_week_tbl.week_id
and member_team_week_tbl.team_id > 0
group BY member_id
order by goal DESC
	
EOT;
	*/
	echo "$sql" ;
	
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
		$k = 0 ;
		$team_count = 0 ;
		$team_info = array() ;
		while($row = $result->fetch_assoc()) {
			//$output .= "\n** $last_id <> " . $row["team_id"] . "**\n" ;
			$j++ ;
			if ($last_id < $row["team_id"]) {
				$last_id = $row["team_id"] ;
				
				if ($last_id > 0) {
					$i++ ;
					$team_info[$i]["color"] = query_team_colors($last_id,$weekid) ;
					$team_info[$i]["txt"] = "" ;
					//$output .= "\nTeam [" . query_team_colors($last_id,$weekid) . "]\n" ;
				} else {
					$team_info[$i]["color"] = "none" ;
					//$output .= "\nNone Random Team Members\n" ;
				}
				$team_count = 0 ;
			} else {
				$team_info[$i]["txt"] .= ", " ;	
			}
			$team_count++ ;
			$name = query_member_name($row["member_id"]);
			$response = query_member_week_goal($row["member_id"],$weekid) ;
			if ($response["code"] == 1) {
				if ($response["data"] > 0) { $name .= " (⚽ " . $response["data"] . ")" ;}
			}
			$team_info[$i]["txt"] .= getFlexText($name,"md", "start",1) ;
			$output .= $team_count . ". " .  $name ;
			
		}
	} else {
		$output = "no registered member\n\n";
	}
	$conn->close();
	
	
	return $team_info  ;

}


function query_team_colors_members($weeknum = 0)
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
	
	#$sql = "SELECT name, team_id, member_id FROM member_team_week_tbl where week_id=$weekid and team_id > 0 order by team_id";
	$sql = "SELECT name, team_id, member_id FROM member_team_week_tbl where week_id=$weekid and team_id > 0 order by team_id, RAND()";

/*	
	$sql = <<< EOT
	
	SELECT member_team_week_tbl.name, member_team_week_tbl.team_id, 
member_team_week_tbl.member_id , match_goal_tbl.id, COUNT(*) AS goal
FROM member_team_week_tbl, match_goal_tbl, match_stat_tbl
where member_team_week_tbl.week_id=$weekid
AND member_team_week_tbl.member_id = match_goal_tbl.member_id
AND match_goal_tbl.match_id = match_stat_tbl.id
and match_stat_tbl.week_id = member_team_week_tbl.week_id
and member_team_week_tbl.team_id > 0
group BY member_id
order by goal DESC
	
EOT;
	*/
	echo "$sql" ;
	
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
		$team_info = array() ;
		while($row = $result->fetch_assoc()) {
			//$output .= "\n** $last_id <> " . $row["team_id"] . "**\n" ;
			$j++ ;
			if ($last_id < $row["team_id"]) {
				$last_id = $row["team_id"] ;
				
				if ($last_id > 0) {
					$i++ ;
					$team_info[$i]["color"] = query_team_colors($last_id,$weekid) ;
					$team_info[$i]["txt"] = "" ;
					//$output .= "\nTeam [" . query_team_colors($last_id,$weekid) . "]\n" ;
				} else {
					$team_info[$i]["color"] = "none" ;
					//$output .= "\nNone Random Team Members\n" ;
				}
				$team_count = 0 ;
			} else {
				$team_info[$i]["txt"] .= ", " ;	
			}
			$team_count++ ;
			$name = query_member_name($row["member_id"]);
			$response = query_member_week_goal($row["member_id"],$weekid) ;
			if ($response["code"] == 1) {
				if ($response["data"] > 0) { $name .= " (⚽ " . $response["data"] . ")" ;}
			}
			$team_info[$i]["txt"] .= getFlexText($name,"md","center") ;
			$output .= $team_count . ". " .  $name ;
			
		}
	} else {
		$output = "no registered member\n\n";
	}
	$conn->close();
	
	
	return $team_info  ;

}

function query_week_location($weekid)
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
	
	$sql = "SELECT location_tbl.name, location_tbl.kickoff  FROM week_tbl, location_tbl "
		. "where week_tbl.id=$weekid and week_tbl.location_id=location_tbl.id" ;
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = true ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["name"] ;
			$output[2] = $row["kickoff"] ;
		}
	} else {
		$output[0] = false ;
	}
	$conn->close();
	
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
	
	return  $output;
}


function report_table_week($weeknum = 0) {

	$result = query_week_id($weeknum) ;
	if($result[0]) {
			$weekid = $result[1];
			$weekdate = $result[2];
			$weeknum = $result[3];
			$sql_week = $result[4];
	} else {
			return $result ;
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
	
	$sql = "SELECT team_color_week_tbl.color, table_week_tbl.*" ; 
	$sql .= " FROM table_week_tbl , team_color_week_tbl" ;
	$sql .= " where table_week_tbl.week_id = $weekid" ;
	$sql .= " AND table_week_tbl.team_week_id = team_color_week_tbl.id order by table_week_tbl.pts DESC, (table_week_tbl.g - table_week_tbl.a) DESC" ;

	$result = $conn->query($sql);
	$numrow = $result->num_rows ;

	if ($result->num_rows > 0) {
    // output data of each row
		
		$teamcount = 0;
		
		$team_header = <<<JSON
				          {
            "type": "box",
            "layout": "baseline",
			"margin": "xs",
            "contents": [
              {
                "type": "text",
                "text": "Team",
                "weight": "bold",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
              {
                "type": "text",
                "text": "W",
                "wrap": true,
				"weight": "bold",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
              {
                "type": "text",
                "text": "D",
				"weight": "bold",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
              {
                "type": "text",
                "text": "L",
				"weight": "bold",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
			  {
                "type": "text",
                "text": "G",
				"weight": "bold",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
			  {
                "type": "text",
                "text": "A",
				"weight": "bold",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
              {
                "type": "text",
                "text": "PTS",
				"weight": "bold",
				"size": "sm",
				"align": "center",
                "flex": 1
              }
            ]
          },
JSON;
		
		
		$output[2] = "Table Week $weeknum @ $weekdate" ;
		$output[1] = "" ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			if($i == 0) {
				$top = "★" ;
			} else {
				$top = "" ;
			}
			$i++ ;
			$team_name = $row["color"];
			$w = $row["w"];
			//if ($w < 10) { $w = " $w" ;}
			$d = $row["d"];
			//if ($d < 10) { $d = " $d" ;}
			$l = $row["l"];
			//if ($l < 10) { $l = " $l" ;}
			$g = $row["G"];
			//if ($G < 10) { $G = " $G" ;}
			$a = $row["A"];
			//if ($A < 10) { $A = " $A" ;}
			$pts = $row["pts"];
			//if ($pts < 10) { $pts = " $pts" ;}
			//if ( strtolower($team_name) == "red") {
			//		$team_name .= "  " ; 
			//}
			if (strtolower($team_name) == "red" ) {
					$color = "#FF0000" ;
			} elseif (strtolower($team_name) == "green" ) {
				$color = "#11872e" ;
			} elseif (strtolower($team_name) == "black" ) {
				$color = "#000000" ;
			} elseif (strtolower($team_name) == "white" ) {
				$color = "#D3D3D3" ;
			}
			$team_str = <<<JSON
				          {
            "type": "box",
            "layout": "baseline",
			"margin": "xs",
            "contents": [
              {
                "type": "text",
                "text": "$top$team_name",
                "color": "$color",
                "size": "xs",
				"weight": "bold",
				"align": "center",
                "flex": 1
              },
              {
                "type": "text",
                "text": "$w",
                "wrap": true,
                "align": "center",
                "size": "sm",
                "flex": 1
              },
              {
                "type": "text",
                "text": "$d",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
              {
                "type": "text",
                "text": "$l",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
			  {
                "type": "text",
                "text": "$g",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
			  {
                "type": "text",
                "text": "$a",
				"size": "sm",
				"align": "center",
                "flex": 1
              },
              {
                "type": "text",
                "text": "$pts",
				"size": "sm",
				"align": "center",
                "flex": 1
              }
            ]
          },
JSON;
			
			//$output[1] .= $team_str . getFlexSperator("#aaaaaa", "xs") . ", " ;
			$output[1] .= $team_str . getFlexSperator("#aaaaaa", "xs")  ;
			if ($i < $numrow) { $output[1] .= ", " ;}
			//$output .= "|   $team_name  | $w  | $d  | $l  | $G  | $A  |  $pts  |\n" ;
		}
		$scorer = query_match_scorer_week($weekid, 6) ;
		if ($scorer <> "") {
			
			$scorer_str = <<<EOT
			{
				"type": "box",
				"layout": "baseline",
				"contents": [
				{
					"type": "text",
					"text": "Top 6 Scorer",
					"weight": "bold",
					"size": "xs",
					"flex": 0
				}
				],
				"margin": "md"
			},
			
			{
				"type": "box",
				"layout": "baseline",
				"contents": [
				{
					"type": "text",
					"text": "$scorer",
					"color": "#aaaaaa",
					"size": "xs",
					"flex": 0
				}
				],
				"margin": "md"
			}
EOT;
			$output[1] .= ", " . $scorer_str . ", " . getFlexSperator("#FFFFFF", "xs");
		}
		
		$output[1] = $team_header . getFlexSperator("#aaaaaa", "xs") . ", " . $output[1] ;
		$output["result"] = 1 ;
		
	} else {
		$output["result"] = 0 ;
		//$output = "Error $sql\n";
	}
	$conn->close();
	
	return $output ;
	
}

function query_top_mvp($limit = 5)
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
	
	$sql = <<<EOT
	SELECT member_tbl.name, member_tbl.alias, 
sum(table_week_tbl.pts + (table_week_tbl.G / (table_week_tbl.A + 10))) AS pts 
from member_team_week_tbl, table_week_tbl, member_tbl
WHERE member_team_week_tbl.team_id = table_week_tbl.team_week_id 
AND member_team_week_tbl.member_id = member_tbl.id
group BY member_id  ORDER BY pts desc limit $limit
EOT;
	
	
	echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = round($row["pts"],2) ;
			
			//$output .= $name ;
			
			$scorer_str = <<<EOT
			
				 {
					"type": "box",
					"layout": "baseline",
					"margin": "xs",
					"contents": [
EOT;
			$scorer_str .= getFlexText("$i. ", "md", "start", 0) . ", " ;
			
			$scorer_str .= getFlexText("$name", "md", "start", 0) . ", " ;
			
			if ($i == 1) {
				$scorer_str .= getFlexIcon() . ", " ; 
			}
			
			$scorer_str .= getFlexText("$goal", "md", "end", 1)  ;
			
			$scorer_str .= <<<EOT
					
					]
				}
EOT;
			$output .= 	$scorer_str ;
			if ($i < $numrow) { $output .= 	"," ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}

	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_top_scorer($limit = 5)
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
	
	$sql = <<<EOT
	SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, 
match_goal_tbl.status as statusid, count(*) as goal 
FROM match_goal_tbl, member_tbl, goal_status_tbl , match_stat_tbl 
WHERE match_goal_tbl.member_id = member_tbl.id 
and match_goal_tbl.status=goal_status_tbl.id AND match_goal_tbl.status < 2 
AND match_goal_tbl.match_id = match_stat_tbl.id AND match_stat_tbl.week_id > 186
group by member_tbl.id order by goal DESC limit $limit
EOT;
	
	
	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			//$output .= $name ;
			
			$scorer_str = <<<EOT
			
				 {
					"type": "box",
					"layout": "baseline",
					"margin": "xs",
					"contents": [
EOT;
			$scorer_str .= getFlexText("$i. ", "md", "start", 0) . ", " ;
			
			$scorer_str .= getFlexText("$name", "md", "start", 0) . ", " ;
			
			if ($i == 1) {
				$scorer_str .= getFlexIcon() . ", " ; 
			}
			
			$scorer_str .= getFlexText("$goal", "md", "end", 1)  ;
			
			$scorer_str .= <<<EOT
					
					]
				}
EOT;
			$output .= 	$scorer_str ;
			if ($i < $numrow) { $output .= 	"," ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}

	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_top_owngoal($limit = 5)
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
	
	$sql = <<<EOT
	SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, 
match_goal_tbl.status as statusid, count(*) as goal 
FROM match_goal_tbl, member_tbl, goal_status_tbl , match_stat_tbl 
WHERE match_goal_tbl.member_id = member_tbl.id 
and match_goal_tbl.status=goal_status_tbl.id AND match_goal_tbl.status = 2 
AND match_goal_tbl.match_id = match_stat_tbl.id AND match_stat_tbl.week_id > 186
group by member_tbl.id order by goal DESC limit $limit
EOT;
	
	
	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			//$output .= $name ;
			
			$scorer_str = <<<EOT
			
				 {
					"type": "box",
					"layout": "baseline",
					"margin": "xs",
					"contents": [
EOT;
			$scorer_str .= getFlexText("$i. ", "md", "start", 0) . ", " ;
			
			$scorer_str .= getFlexText("$name", "md", "start", 0) . ", " ;
			
			if ($i == 1) {
				$scorer_str .= getFlexIcon() . ", " ; 
			}
			
			$scorer_str .= getFlexText("$goal", "md", "end", 1)  ;
			
			$scorer_str .= <<<EOT
					
					]
				}
EOT;
			$output .= 	$scorer_str ;
			if ($i < $numrow) { $output .= 	"," ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}

	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_top($limit = 5, $goal_status)
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
	if ($goal_status < 2) {
		$txt_status = "match_goal_tbl.status < 2" ;
	} else {
		$txt_status = "match_goal_tbl.status = $goal_status";
	}
	$conn -> set_charset("utf8mb4");
	
	$sql = <<<EOT
	SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, 
match_goal_tbl.status as statusid, count(*) as goal 
FROM match_goal_tbl, member_tbl, goal_status_tbl , match_stat_tbl , week_tbl
WHERE match_goal_tbl.member_id = member_tbl.id 
and match_goal_tbl.status=goal_status_tbl.id AND $txt_status 
AND match_goal_tbl.match_id = match_stat_tbl.id AND match_stat_tbl.week_id = week_tbl.id 
And YEAR(week_tbl.date) = YEAR(CURRENT_DATE()) and member_tbl.id <> 121 and member_tbl.id <> 169 and member_tbl.id < 9000
group by member_tbl.id order by goal DESC limit $limit
EOT;
	
	
	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			//$output .= $name ;
			
			$scorer_str = <<<EOT
			
				 {
					"type": "box",
					"layout": "baseline",
					"margin": "xs",
					"contents": [
EOT;
			$scorer_str .= getFlexText("$i. ", "md", "start", 0) . ", " ;
			
			$scorer_str .= getFlexText("$name", "md", "start", 0) . ", " ;
			
			if ($i == 1) {
				$scorer_str .= getFlexIcon() . ", " ; 
			}
			
			$scorer_str .= getFlexText("$goal", "md", "end", 1)  ;
			
			$scorer_str .= <<<EOT
					
					]
				}
EOT;
			$output .= 	$scorer_str ;
			if ($i < $numrow) { $output .= 	"," ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}

	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}


function query_top_assist($limit = 5)
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
	
	$sql = <<<EOT
	SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, 
match_goal_tbl.status as statusid, count(*) as goal 
FROM match_goal_tbl, member_tbl, goal_status_tbl , match_stat_tbl 
WHERE match_goal_tbl.member_id = member_tbl.id 
and match_goal_tbl.status=goal_status_tbl.id AND match_goal_tbl.status = 3 
AND match_goal_tbl.match_id = match_stat_tbl.id AND match_stat_tbl.week_id > 186
group by member_tbl.id order by goal DESC limit $limit
EOT;
	
	
	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			//$output .= $name ;
			
			$scorer_str = <<<EOT
			
				 {
					"type": "box",
					"layout": "baseline",
					"margin": "xs",
					"contents": [
EOT;
			$scorer_str .= getFlexText("$i. ", "md", "start", 0) . ", " ;
			
			$scorer_str .= getFlexText("$name", "md", "start", 0) . ", " ;
			
			if ($i == 1) {
				$scorer_str .= getFlexIcon() . ", " ; 
			}
			
			$scorer_str .= getFlexText("$goal", "md", "end", 1)  ;
			
			$scorer_str .= <<<EOT
					
					]
				}
EOT;
			$output .= 	$scorer_str ;
			if ($i < $numrow) { $output .= 	"," ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}

	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_match_scorer_week($weekid, $limit = 3)
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
	
	$sql = "SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, "
			. "match_goal_tbl.status as statusid, count(*) as goal "
			. "FROM match_goal_tbl, member_tbl, goal_status_tbl , match_stat_tbl "
			. "WHERE match_stat_tbl.week_id=$weekid and match_goal_tbl.member_id = member_tbl.id "
			. "and match_goal_tbl.status=goal_status_tbl.id AND match_goal_tbl.status < 2 "
			. "AND match_goal_tbl.match_id = match_stat_tbl.id "
			. "group by member_tbl.id order by goal DESC limit $limit" ;
	

	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			$output .= $name ;
			if($goal > 1) {
				$output .= "(+$goal)";
			}
			if ($statusID >0) {
				$output .= "[" . $status . "]" ;
			}
			if ($i < $numrow) { $output .= ", " ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}

	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_match_assist($matchID)
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
	
	$sql = "SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, match_goal_tbl.status as statusid, count(*) as goal FROM match_goal_tbl, member_tbl, goal_status_tbl" ;
	$sql .= " WHERE match_goal_tbl.match_id=$matchID and match_goal_tbl.member_id = member_tbl.id and match_goal_tbl.status = 3";
	$sql .= " and match_goal_tbl.status=goal_status_tbl.id group by member_tbl.id" ;
	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			$output .= $name ;
			if($goal > 1) {
				$output .= "(+$goal)";
			}
			//if ($statusID >0) {
			//	$output .= "[" . $status . "]" ;
			//}
			if ($i < $numrow) { $output .= ", " ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}
	//$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function query_match_scorer($matchID)
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
	
	$sql = "SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, match_goal_tbl.status as statusid, count(*) as goal FROM match_goal_tbl, member_tbl, goal_status_tbl" ;
	$sql .= " WHERE match_goal_tbl.match_id=$matchID and match_goal_tbl.member_id = member_tbl.id and match_goal_tbl.status < 2";
	$sql .= " and match_goal_tbl.status=goal_status_tbl.id group by member_tbl.id" ;
	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	$output = "" ;
	$numrow = $result->num_rows ;
	if ($numrow > 0) {
    // output data of each row
		//$output[] = true ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			$output .= $name ;
			if($goal > 1) {
				$output .= "(+$goal)";
			}
			if ($statusID >0) {
				$output .= "[" . $status . "]" ;
			}
			if ($i < $numrow) { $output .= ", " ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}
	//$conn->close();
	
	$sql = "SELECT member_tbl.name, member_tbl.alias, goal_status_tbl.status, match_goal_tbl.status as statusid, count(*) as goal FROM match_goal_tbl, member_tbl, goal_status_tbl" ;
	$sql .= " WHERE match_goal_tbl.match_id=$matchID and match_goal_tbl.member_id = member_tbl.id and match_goal_tbl.status = 2";
	$sql .= " and match_goal_tbl.status=goal_status_tbl.id group by member_tbl.id" ;
	//echo $sql . "\n";
	//exit ;
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
    // output data of each row
		//$output[] = true ;
		if($numrow > 0) { $output .= ", " ;}
		$numrow = $result->num_rows ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			if($row["alias"] == "") {
				$name = $row["name"] ; 
			} else {
				$name = $row["alias"] ; 
			}
			$goal = $row["goal"] ;
			$status = $row["status"];
			$statusID = $row["statusid"];
			$output .= $name ;
			if($goal > 1) {
				$output .= "(+$goal)";
			}
			if ($statusID >0) {
				$output .= "[" . $status . "]" ;
			}
			if ($i < $numrow) { $output .= ", " ; }
		}
		//$output .= "\n\n" ;
	} else {
		//$output .= "\n" ;
	}
	$conn->close();
	
	return $output ;
	//$output = $team0 . " " . $team1 . " " . $team2 . " " . $team3 . "\n" ;
}

function report_match_week($weeknum = 0, $match_num = 0) {

	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
			$weeknum = $output[3];
			$sql_week = $output[4];
	} else {
			return array(0,"1","No Match for Week $weeknum","2") ;
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
	
	$sql = "SELECT * FROM match_stat_tbl where week_id = $weekid order by match_num";
	$result = $conn->query($sql) ;
	$numrow = $result->num_rows ;
	if ($result->num_rows > 0) {
    // output data of each row
		
		$teamcount = 0;
		$output[1] = "" ;
		$output[3] = "" ;
		$i = 0 ;
		while($row = $result->fetch_assoc()) {
			$i++ ;
			$team_aColor = query_team_colors_byID($row["team_a_id"]) ;
			$team_bColor = query_team_colors_byID($row["team_b_id"]) ;
			$team_aGoal = $row["team_a_goal"] ;
			$team_bGoal = $row["team_b_goal"] ;
			$match_num = $row["match_num"] ;
			//$output .= "Match [" . $row["match_num"] . "] $team_aColor $team_aGoal - $team_bGoal" ;	
			//$output .= " $team_bColor\n" ;
			if (strtolower($team_aColor) == "red" ) {
					$colorA = "#FF0000" ;
			} elseif (strtolower($team_aColor) == "green" ) {
				$colorA = "#11872e" ;
			} elseif (strtolower($team_aColor) == "black" ) {
				$colorA = "#000000" ;
			} elseif (strtolower($team_aColor) == "white" ) {
				$colorA = "#D3D3D3" ;
			}
			
			if (strtolower($team_bColor) == "red" ) {
					$colorB = "#FF0000" ;
			} elseif (strtolower($team_bColor) == "green" ) {
				$colorB = "#11872e" ;
			} elseif (strtolower($team_bColor) == "black" ) {
				$colorB = "#000000" ;
			} elseif (strtolower($team_bColor) == "white" ) {
				$colorB = "#D3D3D3" ;
			}
			
			$team_str = <<<EOT
				{
				"type": "box",
				"layout": "baseline",
				"margin": "md",
				"contents": [
				{
					"type": "text",
					"text": "Match [$match_num]",
					"flex": 0,
					"weight": "bold",
					"align": "center",
					"size": "sm"
				},
								{
					"type": "text",
					"text": "$team_aColor",
					"color": "$colorA",
					"weight": "bold",
					"align": "center",
					"flex": 1,
					"size": "sm"
				},
				{
					"type": "text",
					"text": "$team_aGoal - $team_bGoal",
					"flex": 1,
					"align": "center",
					"size": "sm"
					
				},
				{
					"type": "text",
					"text": "$team_bColor",
					"color": "$colorB",
					"weight": "bold",
					"flex": 1,
					"align": "center",
					"size": "sm"
				}
				],
				"spacing": "xl"
			}
EOT;
			$output[1] .= $team_str ;
			$scorer = query_match_scorer($row["id"]) ;
			$assist = query_match_assist($row["id"]) ;
			if ($scorer <> "") {
				$team_score = <<<EOT
			 ,{
				"type": "box",
				"layout": "baseline",
				"contents": [
				{
					"type": "text",
					"text": "⚽ $scorer ",
					"size": "xs"
				}
				]
			}
			,{
				"type": "box",
				"layout": "baseline",
				"contents": [
				{
					"type": "text",
					"text": "👟 $assist",
					"size": "xs"
				}
				]
			}
EOT;
			$output[1] .= $team_score ;
			
			
			}
			if($i < $numrow) { 

				$output[1] .= ", " . getFlexSperator("#CCCCCC", "xs") . ", " ;
			}
			
		}
		$output["result"] = 1 ;
		$output[2] = "Match Week @ $weekdate" ;
		//$output = "+$teamcount " . $output ;
	} else {
		$output["result"] = 0 ;
		$output[2] = "No Match for $weekdate" ;
	}
	$conn->close();
	
	return $output ;
	
}

function update_table_week($weeknum = 0) {

	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
			$weekdate = $output[2];
			$weeknum = $output[3];
			$sql_week = $output[4];
	} else {
			return $output ;
	}
	
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
		$output[1] = "All Match Week $weeknum - $weekdate\n\n" ;
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
		$output[1] = "Error $sql_week\n$sql\n";
	}
	$conn->close();
	
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
	//print_r($team_week) ;
	return $output ;
	
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
