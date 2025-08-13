<?php

include "./query.php" ;
include "./group_random.php" ;
include "./get_arg.php" ;
include "./match_query.php" ;
include "./flex.php" ;
require_once("./member.php");


echo random_full_team() ;

//$result = get_group_member_count(12) ;
//print_r($result) ;
/*
$response = query_team_colors_members2(5) ;

foreach($response as $member)
{
		$member = $member["member"] ;
		usort($member,'goalDescSort');
		print_r($member) ;
}
*/

//print_r($member) ;

//$response = query_team_colors_members(6) ;
//print_r($response) ;

//echo query_top_mvp(3) ;

//$result = report_match_week() ;
//print_r($result) ;

//$result = update_table_week(4) ;
//print_r($result) ;

//$result = query_week_location(6) ;
//print_r($result) ;


//$result = report_match_week(3) ;
//print_r($result) ;

//$result = update_table_week(2) ;
//print_r($result) ;

//$result = update_match_score(68) ;
//echo report_match_week(5) ;
//print_r($result) ;

/*
	$random_id = query_random_team() ;
	echo "random id = $random_id" ;
*/

/*
$weeknum = 0 ;
$result = query_week_id($weeknum) ;
$weekid = $result[1] ;

$result = query_all_team_color_week($weekid) ;

foreach ($result as $team)
{
		$team_id = $team ;
		$team_cnt[] = array($team_id,query_team_color_count_week($team_id, $weekid)) ; 
}
//print_r($result) ;
//print_r($team_cnt) ;

$max_group = get_group_member_count($weekid);
shuffle($max_group) ;

//print_r($max_group) ;
$i=0 ;
$team_id = array() ;
foreach ($team_cnt as $team_info) {
		echo "Team [" . $team_info[0] . "] => " . $team_info[1] . "\n" ;
		if ($team_info[1] < $max_group[$i]) {
				$team_id[]=$team_info[0] ;
		}
		$i++ ;
}
//echo "Available Group\n" ;
//print_r($team_id) ;

$output = $team_id[array_rand($team_id,1)];

echo "Random Team_ID = $output\n" ;
*/







//report_match_score(66) ;

/*
$result = add_match_week(1,"Green","Red",5) ;
print_r($result) ;
*/

/*
$arg = array() ;
$arg[] = "cmd test '123  lkl' cmd2" ;
$amd = array() ;
$cmd[] = parseArgArray($arg) ;
print_r($cmd) ;
*/

//echo query_top_scorer() ;


//$random_team_id = query_random_team() ;
//echo "random team id = $random_team_id\n" ; 

//echo query_all_team_members() ;
//random_team_color() ;
//echo query_all_team_colors() ;

//reset_team_colors() ;
//reset_team_members() ;

//echo query_all_team_colors() ;
//echo query_all_team_members() ;
?>
