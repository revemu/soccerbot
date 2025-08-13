<?php

$name = $_POST["wallet_name"] ;
#var_dump($_POST);
#$name = "revemu.tg1" ;

$sql = "select * from wallet where name='$name'" ;
$authen = 0 ;

if (do_query($sql)) {
    $authen = 1 ;
}

$data = [ 'wallet_name' => $name , 'authen' => $authen, 'query' => $sql ];

echo json_encode( $data );

function do_query($sql)
{
	$servername = "localhost";
	$username = "root";
	$password = "mysqladminroot";
	$dbname = "hotbot";
    $find = false ;
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn -> set_charset("utf8mb4");
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
        $find = true ;
	} 

	$conn->close();
	

	
	return  $find ;
}



?>