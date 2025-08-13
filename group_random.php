<?php

function query_team_color_week_exist($team_id, $weekID)
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
	
	$sql = "SELECT id FROM team_color_week_tbl where team_id=$team_id and week_id=$weekID";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = true ;
		
	} else {
		$output = false;
	}
	$conn->close();

	return $output ;
}

function query_members_remain()
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
	
	$sql = "SELECT count(*) as cnt FROM member_tbl where team_id=0";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = "Registered Members\n" ;
		while($row = $result->fetch_assoc()) {
			$output = $row["cnt"] ;
		}
	} else {
		$output = 0;
	}
	$conn->close();

	return $output ;
}

function query_members_count($weekid)
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
	
	$sql = "SELECT count(*) as cnt FROM member_team_week_tbl where week_id=$weekid";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = "Registered Members\n" ;
		while($row = $result->fetch_assoc()) {
			$output = $row["cnt"] ;
		}
	} else {
		$output = 0;
	}
	$conn->close();

	return $output ;
}

function get_group_member_count($weekid)
{
		$member_count=query_members_count($weekid) ;

		//echo "member count = " . $member_count ;
		//$member_count = 15 ;
		if ($member_count < 14) {
			$max_team = 2 ;
		} else {
			$max_team = 3 ;
		}
		$group_number = floor($member_count / $max_team);
		$remain = ($member_count % $max_team) ;
		if ($remain > 0) {
				$group_number++ ;
		}
		
		$remain = ($group_number * $max_team) - $member_count ;
		//$group = array($group_number,$group_number,$group_number);
		for($i=0;$i<=($max_team-1);$i++) {
			$group[] = $group_number;
		}
		
		for ($i=1;$i<=$remain;$i++) {
			$group[$max_team -$i] = $group[$max_team -$i] - 1 ;
		}
		
		return $group ;
}

function add_random_color_team_week($color, $team_num, $weeknum = 0)
{
	
	$result = query_week_id($weeknum) ;
	
	if($result[0]) {
			$weekid = $result[1] ;
			
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
			if (query_team_color_week_exist($team_num,$weekid)) {
				$sql = "update team_color_week_tbl set color='$color' where team_id=$team_num and week_id=$weekid" ;
			} else {
				$sql = "insert into team_color_week_tbl values(default,$team_num,$weekid,'$color')";
			}
			echo $sql ;
			if ($conn->query($sql) === TRUE) {
				$output[0] = true ;
				$output[1] = "Success Add Color to team $team_num" ;
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

function query_team_color_count_week($team_id, $weekid)
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
	
	//$sql = "SELECT count(*) as cnt, avg FROM member_team_week_tbl where week_id=$weekid and team_id=$team_id";
	$sql = <<< SQL
	SELECT COUNT(*) As cnt, Coalesce(AVG(member_tbl.power),0) AS power FROM member_team_week_tbl, member_tbl 
where member_team_week_tbl.week_id = $weekid 
AND member_tbl.id = member_team_week_tbl.member_id
and member_team_week_tbl.team_id = $team_id 
SQL;
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		
		while($row = $result->fetch_assoc()) {
			$output = $row ;
		}
	} else {
		$output = 0 ;
	}
	$conn->close();
	
	return $output;

}


function query_all_team_color_week($weekid, $maxgroup = 2)
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
	
	$sql = "SELECT id FROM team_color_week_tbl where week_id=$weekid limit $maxgroup";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		
		while($row = $result->fetch_assoc()) {
			$output[] = $row["id"] ;
	
		}
	} else {
		$output = "none";
	}
	$conn->close();
	
	return $output;

}

function query_team_guest_exist($weekid)
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
	
	$sql = "SELECT member_tbl.name, member_tbl.id FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid AND member_tbl.id =121 AND member_tbl.id = member_team_week_tbl.member_id";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = true ;
		
	} else {
		$output = false;
	}
	$conn->close();

	return $output ;
}

function random_full_team($limit = 3)
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
	
	$conn -> set_charset("utf8mb4");
	
	//$sql = "SELECT member_tbl.name, member_tbl.id FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid AND member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.team_id=0 and member_team_week_tbl.team_id=0 and member_tbl.id <> 121 ORDER BY member_tbl.power DESC;";
	$sql = "SELECT member_tbl.name, member_tbl.id FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid  AND member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.team_id=0 and member_team_week_tbl.atk>0 and member_tbl.id <> 121 ORDER BY member_team_week_tbl.atk ASC;";
	$result = $conn->query($sql);

	//$ret = "" ;

	if ($result->num_rows > 0) {
    // output data of each row
		random_team_color() ;
		$ret = "ผลสุ่มสมาชิก\n" ;
		$i=0 ;
		$team2_tpl  = ["Black","Red"] ;
		$team3_tpl  = ["Black","Red","White"];
		$ret .= "จัดทีมแล้ว " ;
		if($limit == 3) {
			$team_arrays = $team3_tpl ;
			$ret .= "จำนวน " . $limit . " ทีม\n\n" ;
			$n_team = 3;
		} elseif($limit == 2) {
			$ret .= "จำนวน " . $limit . " ทีม\n\n" ;
			$team_arrays = $team2_tpl;
			$n_team = 2;
		}
		
		shuffle($team_arrays);
		$team1 = $team_arrays[0];
		$team2 = $team_arrays[1];
		if( $limit == 3) { 
			$team3 = $team_arrays[2];
			$team3_tpl = $team_arrays;
		} else {
			$team2_tpl = $team_arrays;
		}
		$ress = query_team_id_from_color($team1,$weekid);
		
		
		if($ress[0]) {
			//$ret .= "t1 id =" . $ress[1] . " ";
			$t1id = $ress[1];
		} else {
			//$ret .= $ress[1] ;
		}
		$ress = query_team_id_from_color($team2,$weekid);
		if($ress[0]) {
			//$ret .= "t2 id =" . $ress[1] . " ";
			$t2id = $ress[1];
		} else {
			//$ret .= $ress[1] ;
		}
		$ress = query_team_id_from_color($team3,$weekid);
		if($ress[0]) {
			//$ret .= "t3 id =" . $ress[1] . " ";
			$t3id = $ress[1];
		} else {
			//$ret .= $ress[1] ;
		}
		$t1 = "" ;
		$t2 = "" ;
		$t3 = "" ;
		$ta1 = [];
		$ta2 = [];
		$ta3 =[];
		while($row = $result->fetch_assoc()) {

			
			if(($i) % $n_team == 0){
				if($limit ==3) {
					//$team_arrays = ["Black","Red","White"];
					$team_arrays = $team3_tpl ;
				} elseif($limit == 2) {
					//$team_arrays = ["White","Red"];
					$team_arrays = $team2_tpl ;
				}
			}	

			$team_n = sizeof($team_arrays) ;

			//if ($i <= -1 ) {
			$bound = 12 ;
			$fixi = 0 ;
			//if ( ($i % $n_team == $fixi) && ($i < $bound) ) {
			//*limit 4
			//if  ($i < $bound)  {
			//full team
			if ($i > -1) {
			//if ($i <= -1 ) {
			//if ($i < 6) {
				$index = $i % $n_team ;
				//$index = $row["atk"]  % 3;
				//$index = $fixi ;
			} else {
				shuffle($team_arrays);
				$index = rand(0,100) % $team_n ;			
			}
			$rnd_team = $team_arrays[$index] ;
			unset($team_arrays[$index]) ;
			$team_id = 0 ;
			$member_id = $row["id"] ;
			if ($team1 == $rnd_team) {
				$team_id = $t1id ;
				$ta1[] = $row["name"] ;
				//$t1 .= $row["name"] . " ";
			} elseif($team2 == $rnd_team) {
				$team_id = $t2id ;
				$ta2[] = $row["name"];
				//$t2 .= $row["name"] . " ";
			} else {
				$team_id = $t3id ;
				$ta3[] = $row["name"];
				//$t3 .= $row["name"] . " ";
			}
			update_member_team($row["name"], $team_id,$weekid, $member_id) ;
			
			$i+=1;
		}
		shuffle($ta1);
		foreach ($ta1 as $name) {
			$t1 .= $name . " ";
		}
		shuffle($ta2);
		foreach ($ta2 as $name) {
			$t2 .= $name . " ";
		}
		shuffle($ta3);
		foreach ($ta3 as $name) {
			$t3 .= $name . " ";
		}

		if($limit == 3) {
			$team_week = [1,2,3] ;
		} else {
			$team_week = [1,2] ;
		}

		shuffle($team_week);
		$x = 0 ;
		foreach ($team_week as $rnd_team) {
			if ($rnd_team == 1) {
				$ret .= $team1 . " รวม " . sizeof($ta1) . " คน\n"  . $t1 ."\n" ;
			} else if ($rnd_team == 2) {
				$ret .= $team2 . " รวม "  . sizeof($ta2) . " คน\n"  . $t2 ."\n" ;
			}  else if($rnd_team == 3) {
				$ret .= $team3 . " รวม " . sizeof($ta3) . " คน\n"  . $t3 ."\n" ;
			}
			if ($x < 2) {
				$ret .= "\n" ;
			}
			$x += 1 ;
		}

		if (query_team_guest_exist($weekid)) {
			$ress = query_team_id_from_color("White",$weekid);
			if($ress[0]) {
				//$ret .= "t1 id =" . $ress[1] . " ";
				$tid = $ress[1];
				update_member_team("", $tid, $weekid, 121) ;
				$ret .= "ทีมรับเชิญ White\n@Team1+7 " ;
			}
			
		}
		/*
		for ($x = 0; $x < $limit; $x++) {
			shuffle($team_week);
			$team_n = sizeof($team_week) ;
			$index = rand(0,100) % $team_n ;
			$rnd_team = $team_week[$index] ;
			//$ret .=
			if ($rnd_team == 1) {
				$ret .= $team1 . "\n"  . $t1 ."\n" ;
			} else if ($rnd_team == 2) {
				$ret .= $team2 . "\n"  . $t2 ."\n" ;
			}  else if($rnd_team == 3) {
				$ret .= $team3 . "\n"  . $t3 ."\n" ;
			}
			if ($x < 2) {
				$ret .= "\n" ;
			}
			unset($team_week[$index]) ;
		}*/
		/*
		$ret .= $team1 . "\n"  . $t1 ."\n\n" ;
		$ret .= $team2 . "\n"  . $t2 ."\n\n" ;
		if($limit == 3) { $ret .= $team3 . "\n"  . $t3 ."\n" ;}
		*/
	} else {
		$ret = "none";
	}
	$conn->close();
	
	return $ret;

}

function random_full_team2($limit = 3)
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
	
	$conn -> set_charset("utf8mb4");
	
	//$sql = "SELECT member_tbl.name, member_tbl.id FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid AND member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.team_id=0 and member_team_week_tbl.team_id=0 and member_tbl.id <> 121 ORDER BY member_tbl.power DESC;";
	//$sql = "SELECT member_tbl.name, member_tbl.id FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid  AND member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.team_id=0 and member_team_week_tbl.atk>0 and member_tbl.id <> 121 ORDER BY member_team_week_tbl.atk ASC;";
	//$result = $conn->query($sql);

	$sql = "SELECT member_tbl.name, member_tbl.id, member_team_week_tbl.atk FROM member_team_week_tbl, member_tbl WHERE member_team_week_tbl.week_id=$weekid  AND member_tbl.id = member_team_week_tbl.member_id and member_team_week_tbl.team_id=0  and member_team_week_tbl.atk>0 and member_tbl.id <> 121 ORDER BY member_team_week_tbl.atk ASC;";
	$result = $conn->query($sql);

	//$ret = "" ;

	if ($result->num_rows > 0) {
    // output data of each row
		random_team_color() ;
		$ret = "ผลสุ่มสมาชิก\n" ;
		$i=0 ;
		$team2_tpl  = ["Black","Red"] ;
		$team3_tpl  = ["Black","Red","White"];
		$ret .= "จัดทีมแล้ว " ;
		if($limit == 3) {
			$team_arrays = $team3_tpl ;
			$ret .= "จำนวน " . $limit . " ทีม\n\n" ;
			$n_team = 3;
		} elseif($limit == 2) {
			$ret .= "จำนวน " . $limit . " ทีม\n\n" ;
			$team_arrays = $team2_tpl;
			$n_team = 2;
		}
		
		shuffle($team_arrays);
		$team1 = $team_arrays[0];
		$team2 = $team_arrays[1];
		if( $limit == 3) { 
			$team3 = $team_arrays[2];
			$team3_tpl = $team_arrays;
		} else {
			$team2_tpl = $team_arrays;
		}
		$ress = query_team_id_from_color($team1,$weekid);
		
		$team_id = [] ;
		if($ress[0]) {
			//$ret .= "t1 id =" . $ress[1] . " ";
			$team_id[0] = $ress[1];
		} 
		$ress = query_team_id_from_color($team2,$weekid);
		if($ress[0]) {
			//$ret .= "t2 id =" . $ress[1] . " ";
			$team_id[1] = $ress[1];
		} 
		$ress = query_team_id_from_color($team3,$weekid);
		if($ress[0]) {
			//$ret .= "t3 id =" . $ress[1] . " ";
			$team_id[2] = $ress[1];
		} 
		$t1 = "" ;
		$t2 = "" ;
		$t3 = "" ;
		$ta[0] = [] ;
		$ta[1] = [] ;
		$ta[2] = [] ;
		while($row = $result->fetch_assoc()) {

			$id = $row["atk"] % $limit;
			$member_id = $row["id"] ;
			$ta[$id][] = $row["name"] ;
		
			update_member_team($row["name"], $team_id[$id],$weekid, $member_id) ;
			
		}
		shuffle($ta[0]);
		foreach ($ta[0] as $name) {
			$t1 .= $name . " ";
		}
		shuffle($ta[1]);
		foreach ($ta[1] as $name) {
			$t2 .= $name . " ";
		}
		shuffle($ta[2]);
		foreach ($ta[2] as $name) {
			$t3 .= $name . " ";
		}

		if($limit == 3) {
			$team_week = [1,2,3] ;
		} else {
			$team_week = [1,2] ;
		}

		shuffle($team_week);
		$x = 0 ;
		foreach ($team_week as $rnd_team) {
			if ($rnd_team == 1) {
				$ret .= $team1 . " รวม " . sizeof($ta[0]) . " คน\n"  . $t1 ."\n" ;
			} else if ($rnd_team == 2) {
				$ret .= $team2 . " รวม "  . sizeof($ta[1]) . " คน\n"  . $t2 ."\n" ;
			}  else if($rnd_team == 3) {
				$ret .= $team3 . " รวม " . sizeof($ta[2]) . " คน\n"  . $t3 ."\n" ;
			}
			if ($x < 2) {
				$ret .= "\n" ;
			}
			$x += 1 ;
		}

		if (query_team_guest_exist($weekid)) {
			$ress = query_team_id_from_color("White",$weekid);
			if($ress[0]) {
				//$ret .= "t1 id =" . $ress[1] . " ";
				$tid = $ress[1];
				update_member_team("", $tid, $weekid, 121) ;
				$ret .= "ทีมรับเชิญ White\n@Team1+7 " ;
			}
			
		}
		/*
		for ($x = 0; $x < $limit; $x++) {
			shuffle($team_week);
			$team_n = sizeof($team_week) ;
			$index = rand(0,100) % $team_n ;
			$rnd_team = $team_week[$index] ;
			//$ret .=
			if ($rnd_team == 1) {
				$ret .= $team1 . "\n"  . $t1 ."\n" ;
			} else if ($rnd_team == 2) {
				$ret .= $team2 . "\n"  . $t2 ."\n" ;
			}  else if($rnd_team == 3) {
				$ret .= $team3 . "\n"  . $t3 ."\n" ;
			}
			if ($x < 2) {
				$ret .= "\n" ;
			}
			unset($team_week[$index]) ;
		}*/
		/*
		$ret .= $team1 . "\n"  . $t1 ."\n\n" ;
		$ret .= $team2 . "\n"  . $t2 ."\n\n" ;
		if($limit == 3) { $ret .= $team3 . "\n"  . $t3 ."\n" ;}
		*/
	} else {
		$ret = "none";
	}
	$conn->close();
	
	return $ret;

}

function query_random_team($weeknum = 0, $member_name)
{
	$log = new Logger("debug.txt");
	$log->setTimestamp("D M d 'y h.i A");
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
	} else {
			return $output ;
	}
	
	$max_group = get_group_member_count($weekid);
	shuffle($max_group) ;
	
	$max_group = array(8,8,8) ;
	
	$maxgroup = count($max_group) ;
	
	print_r($max_group) ;
	
	$sql = "select power from member_tbl where name = '$member_name'" ;
	
	$result = do_query($sql) ;
	
	print_r($result) ;
	
	$member_power = $result[0]["power"] ;
	
	//$result = query_all_team_color_week($weekid) ;

	$result = query_all_team_color_week($weekid, $maxgroup) ;
	$i = 0 ;
	$memcount = 0 ;
	$memcountmax =0 ;
	foreach ($result as $team)
	{
		$team_id = $team ;
		$response = query_team_color_count_week($team_id, $weekid) ;
		$cnt = $response["cnt"] ;
		$memcountmax += $max_group[$i] ;
		$memcount += $cnt ;
		$avgpwr = $response["power"] ;
	
		$team_cnt[] = array($team_id, $cnt, $avgpwr, $max_group[$i]) ; 
		$i++ ;
	}
	$log->putLog("$member_name power = $member_power\n") ;
	//$log->putLog(print_r($maxpow,true));
	//$log->putLog(print_r($lowpow,true));
	//print_r($result) ;
	$memslot = $memcountmax - $memcount ;
	print_r($team_cnt) ;

	

	//print_r($max_group) ;
	$i=0 ;
	$team_id = array() ;
	$lower = 0 ;
	$upper = 0 ;
	$number = mt_rand(1,100);
	$random_id = 0 ;
	foreach ($team_cnt as $team_info) {
		echo "Team [" . $team_info[0] . "] => " . $team_info[1] . "\n" ;
		if ($team_info[1] < $max_group[$i]) {
				$remains = $team_info[3] - $team_info[1] ;
				$prob = floor(($remains/$memslot) * 100) ;
				$lower = $upper + 1;
				$upper = $upper + $prob;
				if ($number >= $lower and $number <= $upper) {
					$random_id = $team_info[0] ;
				}
				$team_id[]=array($team_info[0], $prob, $lower, $upper) ;
		}
		$i++ ;
	}
	echo "Available Group\n" ;
	print_r($team_id) ;
	echo "random number = $number\n" ;
	$random_id = $team_id[array_rand($team_id,1)][0];

/*
	if ( ($member_power < 60) and ($maxpow["pwr"] > 0) and ($maxpow["cnt"] < $maxpow["max"]) ) {
		$random_id = $maxpow["id"] ;
	} elseif ( ($member_power > 80) and ($lowpow["pwr"] >= 0) and ($lowpow["cnt"] < $lowpow["max"])) {
		$random_id = $lowpow["id"];
	} else {
		
		
		//$random_id = $team_id[array_rand($team_id,1)][0];
	}
*/
	//print_r($random_id) ;
	
	

	return $random_id ;
	//return 398 ;

}

/*
function query_random_team1($weeknum = 0, $member_name)
{
	$log = new Logger("debug.txt");
	$log->setTimestamp("D M d 'y h.i A");
	$output = query_week_id($weeknum) ;
	if($output[0]) {
			$weekid = $output[1];
	} else {
			return $output ;
	}
	
	$max_group = get_group_member_count($weekid);
	shuffle($max_group) ;
	
	$max_group = array(8,8,8) ;
	
	$maxgroup = count($max_group) ;
	
	print_r($max_group) ;
	
	$sql = "select power from member_tbl where name = '$member_name'" ;
	
	$result = do_query($sql) ;
	
	print_r($result) ;
	
	$member_power = $result[0]["power"] ;
	
	//$result = query_all_team_color_week($weekid) ;

	$result = query_all_team_color_week($weekid, $maxgroup) ;
	$i = 0 ;
	$memcount = 0 ;
	$memcountmax =0 ;
	foreach ($result as $team)
	{
		$team_id = $team ;
		$response = query_team_color_count_week($team_id, $weekid) ;
		$cnt = $response["cnt"] ;
		$memcountmax += $max_group[$i] ;
		$memcount += $cnt ;
		$avgpwr = $response["power"] ;
		if ($i == 0) {
			$maxpow = array("id" => $team_id, "pwr" => $avgpwr, "cnt" => $cnt, "max" => $max_group[$i])  ;
			$lowpow = array("id" => $team_id, "pwr" => $avgpwr, "cnt" => $cnt, "max" => $max_group[$i]) ;
		} else {
			if ($avgpwr > $maxpow["pwr"]) {
					$maxpow["id"] = $team_id ;
					$maxpow["pwr"] = $avgpwr ;
					$maxpow["cnt"] = $cnt ;
					$maxpow["max"] = $max_group[$i] ;
			} else {
					$lowpow["id"] = $team_id ;
					$lowpow["pwr"] = $avgpwr ;
					$lowpow["cnt"] = $cnt ;
					$lowpow["max"] = $max_group[$i] ;
			}
		}
		$team_cnt[] = array($team_id, $cnt, $avgpwr, $max_group[$i]) ; 
		$i++ ;
	}
	$log->putLog("$member_name power = $member_power\n") ;
	$log->putLog(print_r($maxpow,true));
	$log->putLog(print_r($lowpow,true));
	//print_r($result) ;
	$memslot = $memcountmax - $memcount ;
	print_r($team_cnt) ;

	

	//print_r($max_group) ;
	$i=0 ;
	$team_id = array() ;
	$lower = 0 ;
	$upper = 0 ;
	$number = mt_rand(1,100);
	$random_id = 0 ;
	foreach ($team_cnt as $team_info) {
		echo "Team [" . $team_info[0] . "] => " . $team_info[1] . "\n" ;
		if ($team_info[1] < $max_group[$i]) {
				$remains = $team_info[3] - $team_info[1] ;
				$prob = floor(($remains/$memslot) * 100) ;
				$lower = $upper + 1;
				$upper = $upper + $prob;
				if ($number >= $lower and $number <= $upper) {
					$random_id = $team_info[0] ;
				}
				$team_id[]=array($team_info[0], $prob, $lower, $upper) ;
		}
		$i++ ;
	}
	echo "Available Group\n" ;
	print_r($team_id) ;
	echo "random number = $number\n" ;
	$random_id = $team_id[array_rand($team_id,1)][0];

	//print_r($random_id) ;
	
	

	return $random_id ;
	//return 398 ;

}
*/

function random_team_color($weeknum = 0)
{
	
	$random_color = array("Red","White","Black") ;
    shuffle($random_color) ;
	$i = 1 ;
	$output = "" ;
	foreach ($random_color as $color) {
	     add_random_color_team_week($color,$i,$weeknum) ;
		 $i++ ;
	}
	return $output ;
}

?>
