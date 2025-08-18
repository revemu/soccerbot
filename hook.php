<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');
require_once('./logger.php');
require_once('./query.php');
require_once('./get_arg.php');
require_once('./group_random.php');
require_once('./cmd.php');
require_once("./match_query.php");
require_once("./flex.php");
require_once("./member.php");
include("./config.inc") ;
require __DIR__ . '/vendor/autoload.php';


function hasLetterAndNumber($str) {
    // Check if the string contains at least one letter (uppercase or lowercase)
    $hasLetter = preg_match('/[a-zA-Z]/', $str);

    // Check if the string contains at least one number (0-9)
    $hasNumber = preg_match('/[0-9]/', $str);

    // Return true if both conditions are met
    return $hasLetter && $hasNumber;
}

$log = new Logger("log.txt");
$log->setTimestamp("Y-m-d H:i:s");

//$channelAccessToken = getAccessToken() ;
//$channelSecret = 
//global $channelSecret;
//$log->putLog("token: " . $channelAccessToken . ", secret: " . $channelSecret,true);
$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
	
    switch ($event['type']) {
        case 'message':
			
            $message = $event['message'] ;
			//$log->putLog(print_r($event,true));
            switch ($message['type']) {
				case 'image':
					
					
					

					//return ;
					

					$chat_type = "[private_chat] - " ;
					$LineUserId = $event['source']['userId'] ;
					$response = get_name_by_lineId($LineUserId) ;
					$db_displayname = $response[0]["name"] ;
					$member_id = $response[0]["id"] ;
					$DisplayName = $db_displayname ;
					//$log->putLog($DisplayName . "[" . $LineUserId . "]: " . $str,true);
					
					//$log->putLog(print_r($event,true));	
					if (!empty($event['source']['groupId'])) {
						$LineGroupId = $event['source']['groupId'] ;
						$chat_type = "[group_chat] - " ;
					} else {
						if ($member_id != 14) {
							$log->putLog($chat_type . "$DisplayName sent image, not authorized!");
							break ;
						}
						
					}

					$log->putLog($chat_type . "$DisplayName sent image");

					$start_time = microtime(TRUE);
					$curl = curl_init();
					curl_setopt_array($curl, array(
					//CURLOPT_URL => "https://api.revemu.org/webhook?msgid=" . $message['id'],
					CURLOPT_URL => "http://localhost:3000/hook?msgid=" . $message['id'],
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					));
					$apijson1 = curl_exec($curl);
					curl_close($curl);
					$end_time = microtime(TRUE);
					$elapsed = $end_time - $start_time ;
					$log->putLog("read qr api time:" . $elapsed);
					//$res = json_decode($apijson) ;
					$apijson = '{"status":200,"data":{"payload":"004600060000010103002022520250816210339240054672085102TH9104D5EB","transRef":"2025081621033924005467208","date":"2025-08-16T21:03:39+07:00","countryCode":"","amount":{"amount":249,"local":{"amount":0,"currency":""}},"fee":0,"ref1":"","ref2":"","ref3":"","sender":{"bank":{"id":"002","name":"ธนาคารกรุงเทพ","short":"BBL"},"account":{"name":{"en":"PYSIT P"},"bank":{"type":"BANKAC","account":"086-0-xxx588"}}},"receiver":{"bank":{},"account":{"name":{"th":"นาย เศรษฐ ว","en":"SAGE"},"proxy":{"type":"MSISDN","account":"085-xxx-5894"}}}}}' ;
					$res = json_decode($apijson) ;
					$log->putLog(print_r($apijson1,true));
					$decodedData = "" ;
					if ($res->status == 200) {
						//$decodedData = $res->data->payload ;
						//$log->putLog($decodedData);
						//$log->putLog($apijson);
						//$res = json_decode($apijson) ;
						
						$tdate = strtotime($res->data->date);
						$tdate = date("Y-m-d H:i:s", $tdate);
						$amount = $res->data->amount->amount ;
						$bank = $res->data->sender->bank->short ;

						if (!isset($res->data->sender->account->name->en)) {
							$sender = $res->data->sender->account->name->th ;
						} else {
							$sender = $res->data->sender->account->name->en ;
						}
						if (!isset($res->data->receiver->account->name->en)) {
							$receiver = $res->data->receiver->account->name->th ;
						} else {
							$receiver = $res->data->receiver->account->name->en ;
						}

						$searchCmd = ["เศรษฐ", "SAGE"];
						//print($receiver . "\n") ;
						$pattern = '/(' . implode('|', array_map('preg_quote', $searchCmd)) . ')/i';
						if (preg_match($pattern, $receiver, $matches)) {
							$msg = "⌚ - $tdate\n💸 - $bank - $sender \n💵 - Kyne \n💰 จำนวนเงิน $amount บาท\n\n🙏 $DisplayName ได้รับ เงินโอนแล้ว\n\n" ;
						} else {
							$msg = "⌚ - $tdate\n💸 - $bank - $sender \n💵 - $receiver \n💰 - จำนวนเงิน $amount บาท\n\n⚠️ $DisplayName ชื่อผู้รับไม่ตรง\n\n" ;
							$member_id = -1 ;
						}
						//$log->putLog("qr_read: $decodedData (" . strlen($decodedData) . "), payload: $payload (" . strlen($payload) .")");
						//$log->putLog(var_dump($res));
						//$apijson = shell_exec($command);
						//$log->putLog($apijson);
						$log->putLog("\n" . $msg) ;
						$str = "@soccerbot +slip " . $DisplayName ;
						//$log->putLog($chat_type . "$DisplayName sent slip: $tdate - $bank - $sender to $receiver 💰 จำนวนเงิน $amount บาท");

						$argc[] = $str;
						$argc = parseArgArray($argc) ;
						$result = cmd_process($argc, $str, $member_id, $msg, $event) ;
						$txtlog = $result[1] ;
						
						//$log->putLog("\n" . $msg) ;
						

						if ($txtlog == "") {							

						} else {
							
							//$log->putLog(print_r($argc , true) . ' ' . $txtlog . ' ' . print_r($result, true) );
							
							//if ($result[0] == 0) {
							if ($result["type"] == 0) {	
							$message = [
								'replyToken' => $event['replyToken'],
								'messages' => [
									[
											'type' => 'text',
											'quoteToken' => $message['quoteToken'] ,
											'text' => $txtlog
											
										]
									]
								];
								
								$client->replyMessage($message);
								$test = json_encode($message);
								//error_log($test) ;
							}
						}
					}
					
					/*if (is_file($filePath)) {
						$command = "rm -f " . $filePath;
						//unlink($filePath) ;
						$remove = shell_exec($command);
					}*/

					break ;
                case 'text':
					$str = trim($message['text']) ;
					$LineUserId = $event['source']['userId'] ;
					$response = get_name_by_lineId($LineUserId) ;
					$db_displayname = $response[0]["name"] ;
					$member_id = $response[0]["id"] ;
					$DisplayName = $db_displayname ;
					//$log->putLog("[" . $LineUserId . "]: db_name:" . $db_displayname);
					/*if (check_exist_lineId($LineUserId) == 1) {
						//$response = $client->getLineDisplayNameByGroupId($LineUserId,$LineGroupId);
						$response = $client->getLineDisplayName($LineUserId) ;
						//$log->putLog(print_r($response,true));	
						$response = json_decode($response) ;
						
						$DisplayName = '@' . $response->displayName ;
						$DisplayName = str_replace("'", "\'",$DisplayName);
						//$log->putLog($DisplayName . "[" . $LineUserId . "]: db_name: " . $db_displayname);
						if ($db_displayname != $DisplayName) {
								//$putLog .= "Name Change Update Name $DisplayName\n" ;
								$log->putLog("Name change detect update from " . $db_displayname . " to " . $DisplayName);
									//$sql = "update member_tbl set  name='$DisplayName' where line_user_id = '$LineUserId'" ;
									//$putLog .= $sql . "\n" ;
								$ret = update_member_name($DisplayName,$LineUserId) ;
						}
							
					}*/
					
					
					$Talker = $DisplayName ;
					
					$mylog = $DisplayName . ": " . $str ;
					$chat_type = "[private_chat] - " ;
					$tchat = 0 ;
					//$log->putLog($DisplayName . "[" . $LineUserId . "]: " . $str,true);
					
					//$log->putLog(print_r($event,true));	
					if (!empty($event['source']['groupId'])) {
						$LineGroupId = $event['source']['groupId'] ;
						$chat_type = "[group_chat] - " ;
						$tchat = 1 ;
						
						
						$putLog = "\nLine User ID = $LineUserId\n" ;
						$putLog .= "Line Group ID = $LineGroupId\n" ;
						$putLog .= "Line Display Name = $DisplayName\n" ;
						$putLog .= "Message = " . $message['text'] ;
						
						if (check_exist_lineId($LineUserId) == 0) {
							$putLog .= "No LineID Found \n" ;
							add_new_member($DisplayName,$LineUserId) ;
						} else {
							$response = $client->getLineDisplayNameByGroupId($LineUserId,$LineGroupId);
							//$response = $client->getLineDisplayName($LineUserId) ;
							//$log->putLog(print_r($response,true));	
							$response = json_decode($response) ;
							
							$DisplayName = '@' . $response->displayName ;
							$DisplayName = str_replace("'", "\'",$DisplayName);
							//$log->putLog("[LOG] - [" . $LineUserId . "] => line_name: " .$DisplayName . " <=> db_name: " . $db_displayname);
							if ($db_displayname != $DisplayName) {
									//$putLog .= "Name Change Update Name $DisplayName\n" ;
									$log->putLog("[LOG] - [update] name " . $db_displayname . " => " . $DisplayName);
										//$sql = "update member_tbl set  name='$DisplayName' where line_user_id = '$LineUserId'" ;
										//$putLog .= $sql . "\n" ;
									$ret = update_member_name($DisplayName,$LineUserId) ;
							}
						}								
						
						//$log->putLog($putLog, true) ;
					}
					
					
					/*if (!empty($response)) {
							$DisplayName = $response[0]["name"] ;
							$Talker = $DisplayName ;
							$member_id = $response[0]["id"] ;
							//$log->putLog(print_r($response,true));
					}*/

					$mystr = $str ;
					
					$op = substr($str,0,1) ;
					//$log->putLog($chat_type . "op:" . $op ,true) ;
					switch ($op)  {
						case '+':
						case '-':
						case '/':
							$chat_type .= "[cmd] - " ;
							$end = substr(trim($str),1) ;
							$searchCmd = ["pay", "team", "1", "topscorer", "topassist", "topog", "register", "noteam", "squadweek", "matchweek", "tableweek", "randomfullteam"];

							// Construct a regex pattern for multiple alternatives
							$pattern = '/(' . implode('|', array_map('preg_quote', $searchCmd)) . ')/i';
							if (preg_match($pattern, $str, $cmd_matches)) {
								$is_mention = false ; 
								$pos = strpos($str, "@", 1) ;
								if ($pos !== false) {
									$member_name = substr($str,$pos) ;
									$str = str_replace("'", "\'",$str);
									$is_mention = true ; 
									$response = query_member_id($member_name) ;
									if ($response[0]) {
										$member_id = $response[1] ;
										$log->putLog($chat_type . "name: $member_name id: $member_id" ,true) ;
									} else {
										return ;
									}
									
									
								}
								switch ($cmd_matches[0]) {
									case 'pay':
									case '1':
										if (!$is_mention) {
											$mystr = "$str $DisplayName" ;
										} else {
											$mystr = $str ;
										}
										break ;
									case 'team':
										if ($is_mention) {
											$mystr = $str ;
										} else {
											break ;
										}
										break ;
									case 'randomfullteam':
										if ($tchat > 0 || $member_id == 14) {
											$mystr = $end ;
										} else {
											$log->putLog($chat_type . $str . ", not allow",true) ;
											return ;
										}
										
										break ;
									default :
										$mystr = "report $end" ;
										break ;
								}

								//$str = "@soccerbot " . $mystr ;
								//$log->putLog($chat_type . $mystr ,true) ;
							} else {
								$mystr = $end ;
							}
							$str = "@soccerbot " . $mystr ;
							$mylog .= " => " . $str;
							break ;
						default:
							break ;
					}
					$log->putLog($chat_type . $mylog ,true) ;
					//return ;

					//$log->putLog("cmd: " . $str, true);
					//$log->putLog("new str = " . $str .  " member_id = " . $member_id . "\n" ,true);

					

					$pos = strpos($str, "@soccerbot");

					if ($pos === false) {
						//$log->putLog(print_r($event,true));

						break ;
					} else {
						//$argc = parseArg($message['text']);
						$argc[] = $str;
						$argc = parseArgArray($argc);
						$result = cmd_process($argc, $str, $member_id, $Talker,$event) ;
						$txtlog = $result[1] ;

						if ($txtlog == "") {							

						} else {
							
							//$log->putLog(print_r($argc , true) . ' ' . $txtlog . ' ' . print_r($result, true) );
							
							//if ($result[0] == 0) {
							if ($result["type"] == 0) {	
								$message = [
									'replyToken' => $event['replyToken'],
									'messages' => [
										[
											'type' => 'text',
											'quoteToken' => $message['quoteToken'] ,
											'text' => $txtlog
											
										]
									]
								];
								
								$client->replyMessage($message);
								$test = json_encode($message);
								//error_log($test) ;
							} elseif ($result["type"] == 1) {
								$header = $result[2]  ;
								$message = getFlexJson($event,$txtlog, $header, $result["location_name"]);
								$client->replyFlexMessage($message);
								//error_log($message);
							} elseif ($result["type"] == 2) {
								
								//$header = "";
								if($result[0]==1) {
									$header = $result[2]  ;
									$message = getFlexMatch($event, $txtlog, $header, $result["location_name"]);
								} else {
									$message = getFlexMatch($event,"");
								}
								//$log->putLog($txtlog);
								$client->replyFlexMessage($message);
								//error_log(print_r($result,true));
								//error_log(print_r($message,true));
							} elseif ($result["type"] == 3) {
								
								$header = "Test";
								if(isset($result["header"])) {
									$header = $result["header"] ;
								}
								$message = getFlex($event, $txtlog , $header);
								//$log->putLog($message);
								$client->replyFlexMessage($message);
								//error_log(print_r($result,true));
								//error_log(print_r($message,true));
							}  elseif ($result["type"] == 4) {
														
								//$msg = getFlexMSG($txtlog) ;
								
								$msg = $result["msg"] ;
								$message = getReplyMSG($event, $msg) ;
								//$log->putLog($msg);
								$client->replyFlexMessage($message);
								//error_log(print_r($result,true));
								//error_log(print_r($message,true));
							}
						}
					}
					
					//error_log('Message Text: ' . $message['text']);
                    break;
                default:
                    error_log('Unsupported message type: ' . $message['type']);
                    break;
            }
            break;
        default:
            error_log('Unsupported event type: ' . $event['type']);
            break;
    }
};
