<?php

    $DisplayName = "Kyne" ;
$apijson = '{"status":200,"data":{"payload":"0041000600000101030040220015215092758BPP147425102TH91048060","transRef":"015215092758BPP14742","date":"2025-08-03T09:27:58+07:00","countryCode":"TH","amount":{"amount":213,"local":{"amount":213,"currency":"764"}},"fee":0,"ref1":"","ref2":"","ref3":"","sender":{"bank":{"id":"004","name":"ธนาคารกสิกรไทย","short":"KBANK"},"account":{"name":{"th":"นาย อนุพงศ์ บ","en":"MR. ANUPONG B"},"bank":{"type":"BANKAC","account":"xxx-x-x9800-x"}}},"receiver":{"bank":{},"account":{"name":{"th":"นาย เศรษฐ ว","en":"SAGE"},"proxy":{"type":"MSISDN","account":"xxx-xxx-5894"}}}}}' ;

$apijson = '{"status":200,"data":{"payload":"0046000600000101030140225202508037HrwTcbtkWrmJwnIl5102TH9104F0E7","transRef":"202508037HrwTcbtkWrmJwnIl","date":"2025-08-03T09:01:22+07:00","countryCode":"TH","amount":{"amount":219,"local":{"amount":0,"currency":""}},"fee":0,"ref1":"","ref2":"","ref3":"","sender":{"bank":{"id":"014","name":"ธนาคารไทยพาณิชย์","short":"SCB"},"account":{"name":{"th":"นาย พิพัฒน์ บ"},"bank":{"type":"BANKAC","account":"xxxx-xx348-4"}}},"receiver":{"bank":{"id":"025","name":"ธนาคารกรุงศรีอยุธยา","short":"BAY"},"account":{"name":{"th":"นาย  ว"},"bank":{"type":"BANKAC","account":"XXXXX2389X"}}}}}' ;

$res = json_decode($apijson) ;
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
    $receiver = "Kyne" ;
    $msg = "⌚ - $tdate\n💸 - $bank - $sender \n💵 - $receiver \n💰 จำนวนเงิน $amount บาท\n\n🙏 $DisplayName ได้รับ เงินโอนแล้ว\n\n" ;
} else {
    $msg = "⌚ - $tdate\n💸 - $bank - $sender \n💵 - $receiver \n💰 จำนวนเงิน $amount บาท\n\n⚠️  $DisplayName ชื่อผู้รับไม่ใช่ Kyne\n\n" ;
}
//$rbank = $res->{"data"}->{"receiver"}->{"bank"}->{"short"} ;
print($msg) ;

?>
