<?php 

header('Access-Control-Allow-Origin: *');

//$log->putLog("get_info.php Request " . print_r($_REQUEST, true), true) ;

$cmd = $_GET["cmd"] ;

switch ($cmd) {
	
		case "query_log":

			$start_date = $_GET["start_date"] ;
			$end_date = $_GET["end_date"] ;
			$log_table = $_GET["log_table"] ;
			$flowrate = $_GET["flowrate"] ;
			$waterlevel = $_GET["water_level_map"] ;

			$sql = "select datetime, " . $flowrate . " as flowrate, " . $waterlevel . " as waterlevel from " . $log_table . " where DateTime >='" . $start_date .  "' and DateTime <='" . $end_date . "'" ;


			$result = do_query($sql) ;
			foreach ($result as $logs) {
				
				$all_log[] = $logs ;
			}

			echo json_encode($all_log, JSON_PRETTY_PRINT) ;
			//echo "$sql" ;
			
			//$result = get_all_current_rate() ;
			//$log->putLog("get_info.php Request " . print_r($result, true), true) ;
			break ;
		default:
			get_last_match_week(5) ;
			break ;
			
}


function query_all_devices()
{
	
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "flowrate";
	// Create connection
	
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo "Connected successfully";
		try {
				$sql = "SELECT * FROM wsv_devices, cs_device_info WHERE wsv_devices.dvi_id = cs_device_info.dvi_id";
				
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

function get_last_match_week($weeknum = 0) {
	
	$result = query_all_devices() ;
	$i = 0 ;
	foreach ($result as $devices) {
		//$all_devices['dvi_id']['id'] = $devices['dvi_id'] ;
		//$all_devices[$devices['dvi_id']] = $devices ;
		$all_devices[$i] = $devices ;
		//$all_devices[$i]['data']= $devices ;
		$sql = "select tag_name,tag_value, ext_tag_column, tag_last_update from wsv_tags WHERE (tag_name like 'MB_TYPE8DEVICE1Data%' or tag_name like'MB_TYPE8DEVICE2Data%' or tag_name like 'FlowRate%') and dvi_id = " .  $devices['dvi_id'] ;
		$result1 = do_query($sql) ;
		foreach ($result1 as $tags) {
			$tag_name = preg_replace('/(\v|\s)+/', '', $tags['tag_name']);
			$all_devices[$i][$tag_name] = $tags ;
		}
		$i++ ;
	}

	//$response         = [];
    //$response['data'] =  $all_devices ;

	echo json_encode($all_devices, JSON_PRETTY_PRINT) ;

	return 0 ;

}

function do_query($sql)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "flowrate";
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


?>