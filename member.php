<?php 

header('Access-Control-Allow-Origin: *');

require_once('./logger.php');

$log = new Logger("log.txt");
$log->setTimestamp("D M d 'y h.i A");

function LogToApache($Message) {
	$stderr = fopen('php://stderr', 'w');
	fwrite($stderr,$Message);
	fclose($stderr);
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
			LogToApache("Query failed: " . $e->getMessage()) ;
		}
		
		
    } catch(PDOException $e)
    {
		echo "Connection failed: " . $e->getMessage();
    }
}

function check_exist_lineId($lineId)
{
	
	$sql = "select * from member_tbl  where line_user_id = '$lineId'" ;
	
    $result = do_query($sql) ;
    
    if(!empty($result)) {
        return 1 ;
    } else {
        return 0 ;
    }

}

function get_name_by_lineId($lineId)
{
	
	$sql = "select * from member_tbl  where line_user_id = '$lineId'" ;
	
    $result = do_query($sql) ;
    
    if(!empty($result)) {
        return $result ;
    } else {
        return 0 ;
    }

}

function update_member_lineId($name, $lineId)
{
	
	$sql = "update member_tbl set line_user_id = '$lineId' where name='$name'" ;
	
	do_update($sql) ;

}

function add_new_member($name, $lineId)
{
	
	$sql = "insert into member_tbl values (default, '$name', 0, 0, 0, '', '$lineId',0)" ;
	
	do_update($sql) ;

}

function update_member_name($name, $lineId)
{
	$query = sprintf("update member_tbl set name='%s' where line_user_id='%s'",
	str_replace("'", " ",$name),$lineId);
	$sql = "update member_tbl set  name=\"" . $name . "\" where line_user_id = '$lineId'" ;
	
	//LogToApache($sql) ;

	do_update($sql) ;

	return $sql . "\n" . $query ;

}

function check_exist_member_id($member_name)
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
	
	$sql = "SELECT id FROM member_tbl where name=\"$member_name\"";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output = 1 ;
		//while($row = $result->fetch_assoc()) {
		//	$output[1] = $row["id"] ;
		//}
	} else {
		$output = 0;
		
	}
	$conn->close();
	
	return $output ;

}

function get_member_id_by_line_uid($line_uid)
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
	
	$sql = "SELECT id FROM member_tbl where line_user_id='$line_uid'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		$output[0] = 1 ;
		while($row = $result->fetch_assoc()) {
			$output[1] = $row["id"] ;
		}
	} else {
		$output[1] = 0;
		
	}
	$conn->close();
	
	return $output ;

}

?>