<?php

function getFlexHeader($text, $header = "@soccerbot message",$quotetoken = '')
{

if ($quotetoken == '') {
	$message = <<<EOT
	{  
		"type": "flex",
		"altText": "$header",
		"contents": 
		
			$text		
		
	}

EOT;
} else {
	$message = <<<EOT
	{  
		"type": "flex",
		"altText": "$header",
		"quoteToken": "$quotetoken" ,
		"contents": 
		
			$text		
		
	}

EOT;
}

	return $message ;
}

function getFlexSperator($color = "#000000", $margin = "xs")
{
	$message = <<<EOT
		{
        "type": "separator",
        "color": "$color",
		"margin": "$margin"
      }
EOT;

	return $message ;
}

function getFlexcarousel($text) 
{
	
$message = <<<EOT

{
  "type": "carousel",
  "contents": [
	$text
  ]

}

EOT;
	return $message ;
}

function getRawFlexBubble($txt, $size = "nano", $imgUrl = "https://ewscripps.brightspotcdn.com/dims4/default/3f2a8fa/2147483647/strip/true/crop/640x360+0+25/resize/1280x720!/quality/90/?url=https%3A%2F%2Fmediaassets.abc15.com%2Fphoto%2F2017%2F03%2F25%2FIMG_1345_1490511415361_57374237_ver1.0_640_480.JPG")
{
	$message = <<<EOT
	{
			"type": "bubble",
			"size": "$size",
			"hero": {
						"type": "image",
						"url": "$imgUrl",
						"size": "full",
						"aspectRatio": "12:6",
						"aspectMode": "cover"
					},
			"body": {
						"type": "box",
						"layout": "vertical",
						"contents": [
									$txt
						]
					}
	}	
EOT;
	return $message ;
}

function getReplyMSG($event, $text)
{
	$replytoken = $event['replyToken'] ;
	$quotetoken = $event['message']['quoteToken'] ;
	$message = <<<EOT
{
	"replyToken":"$replytoken",
	
	"messages":[
		
		$text
		

	]
}

EOT;
	return $message ;
}

function getFlexBubble($text, $size = "micro", $header = "@soccerbot message")
{

$message = <<<EOT
	{  
		"type": "flex",
		"altText": "$header",
		"contents": 
		{
			"type": "bubble",
			"size": "$size",
			"body": {
				"type": "box",
				"layout": "vertical",
				"contents": [
					$text
				]
			}				
		}
	}

EOT;
	//$message = str_replace("\n","",$message);
	//$message = json_decode($message,true) ;
	//$message = json_encode($message) ;
	return $message ;
}

function getFlexMSG($text, $size = "micro", $header = "@soccerbot message", $url = "https://ewscripps.brightspotcdn.com/dims4/default/3f2a8fa/2147483647/strip/true/crop/640x360+0+25/resize/1280x720!/quality/90/?url=https%3A%2F%2Fmediaassets.abc15.com%2Fphoto%2F2017%2F03%2F25%2FIMG_1345_1490511415361_57374237_ver1.0_640_480.JPG")
{

$message = <<<EOT
	{  
		"type": "flex",
		"altText": "$header",
		"contents": 
		{
			"type": "bubble",
			"size": "$size",
			"hero": 
			{
				"type": "image",
				"url": "$url",
				"size": "full",
				"aspectRatio": "12:6",
				"aspectMode": "cover",
				"action": {
					"type": "uri",
					"uri": "http://linecorp.com/"
				}
			},
			"body": {
				"type": "box",
				"layout": "vertical",
				"contents": [
				{
					"type": "box",
					"layout": "vertical",
					"margin": "md",
					"contents": [
			
					$text 
					
					]
				}
				]
			}				
		}
	}

EOT;
	//$message = str_replace("\n","",$message);
	//$message = json_decode($message,true) ;
	//$message = json_encode($message) ;
	return $message ;
}



function getFlexMatch($event, $text, $header = "No Match Week Found", $location = "None")
{
	$replytoken = $event['replyToken'] ;
	$quotetoken = $event['message']['quoteToken'] ;
$message = <<<EOT
{
	"replyToken":"$replytoken",
	"messages":[{  
					"type": "flex",
					"altText": "Match Table Week",
					"contents": {
						

  "type": "bubble",
  "size": "mega",
  "hero": {
    "type": "image",
    "url": "https://ewscripps.brightspotcdn.com/dims4/default/3f2a8fa/2147483647/strip/true/crop/640x360+0+25/resize/1280x720!/quality/90/?url=https%3A%2F%2Fmediaassets.abc15.com%2Fphoto%2F2017%2F03%2F25%2FIMG_1345_1490511415361_57374237_ver1.0_640_480.JPG",
    "size": "full",
    "aspectRatio": "12:6",
    "aspectMode": "cover",
    "action": {
      "type": "uri",
      "uri": "http://linecorp.com/"
    }
  },
  "body": {
    "type": "box",
    "layout": "vertical",
    "contents": [
            
      {
        "type": "box",
        "layout": "vertical",
        "margin": "md",
        "contents": [
			
			$text 		
		    {
            "type": "box",
            "layout": "baseline",
            "contents": [
              {
                "type": "text",
                "text": "Location: $location",
                "color": "#aaaaaa",
                "size": "sm",
                "flex": 1
              }
            ],
            "margin": "md"
          }
		  
        ]
      }
    ]
  }

						
						
					}
	}]
}

EOT;
	//$message = str_replace("\n","",$message);
	//$message = json_decode($message,true) ;
	//$message = json_encode($message) ;
	return $message ;
}


function getFlexJSON($event, $text, $header="No Table Week Found", $location="None")
{
	
	$replytoken = $event['replyToken'] ;
	$quotetoken = $event['message']['quoteToken'] ;
	//$message = "{\"replyToken\":\"$replytoken\",\"messages\":[{\"type\":\"text\",\"text\":\"TEST JSON\\n\"}]}" ;
/*
$message = <<<EOT
{
	"replyToken":"$replytoken",
	"messages":[{"type":"text","text":"$text"}]
EOT;
}*/
$message = <<<EOT
{
	"replyToken":"$replytoken",
	"messages":[{  
					"type": "flex",
					"altText": "Match Table Week",
					"contents": {
						

  "type": "bubble",
  "hero": {
    "type": "image",
    "url": "https://www.newsradioklbj.com/sites/g/files/exi641/f/styles/large_730/public/article-images-featured/soccer_by_sergey_nivens_3.jpg?itok=QxSm2vs4",
    "size": "full",
    "aspectRatio": "12:6",
    "aspectMode": "cover",
    "action": {
      "type": "uri",
      "uri": "http://linecorp.com/"
    }
  },
  "body": {
		"type": "box",
		"layout": "vertical",
		"contents": [
		{
			"type": "text",
			"text": "$header",
			"weight": "bold",
			"align": "center",			
			"size": "md"
		},
      
		$text
		
				
		{
            "type": "box",
            "layout": "baseline",
            "contents": [
              {
                "type": "text",
                "text": "Location: $location",
                "color": "#aaaaaa",
                "size": "sm",
                "flex": 1
              }
            ],
            "margin": "md"
         }
      
    ]
  }

						
						
					}
	}]
}

EOT;
	//$message = str_replace("\n","",$message);
	//$message = json_decode($message,true) ;
	//$message = json_encode($message) ;
	return $message ;
}


function getFlex($event, $text, $header="None", $imgurl = "https://www.234.in.th/images/2020/02/06/113834.jpg")
{
	//$message = "{\"replyToken\":\"$replytoken\",\"messages\":[{\"type\":\"text\",\"text\":\"TEST JSON\\n\"}]}" ;
/*
$message = <<<EOT
{
	"replyToken":"$replytoken",
	"messages":[{"type":"text","text":"$text"}]
EOT;
}*/
	$replytoken = $event['replyToken'] ;
	$quotetoken = $event['message']['quoteToken'] ;
$message = <<<EOT
{
	"replyToken":"$replytoken",
	"messages":[{  
					"type": "flex",
					"altText": "$header",
					"contents": {
						

  "type": "bubble",
  "hero": {
    "type": "image",
    "url": "$imgurl",
    "size": "full",
    "aspectRatio": "21:18",
    "aspectMode": "cover",
    "action": {
      "type": "uri",
      "uri": "http://linecorp.com/"
    }
  },
  "body": {
		"type": "box",
		"layout": "vertical",
		"spacing": "xs",
		"contents": [

		 {
			"type": "text",
			"text": "$header",
			"weight": "bold",
			"size": "md",
			"align": "start"
		},
		
		$text
		
				
		     
		]
  }

						
						
					}
	}]
}

EOT;
	//$message = str_replace("\n","",$message);
	//$message = json_decode($message,true) ;
	//$message = json_encode($message) ;
	return $message ;
}


function getNewFlexText($text, $size = "sm", $align = "start", $flex = 1, $weight = "regular", $color="#666666")
{

$message = <<<EOT

	{
		"type": "text",
		"text": "$text",
		"weight": "$weight",
		"size": "$size",
		"flex": $flex,
		"color": "$color",
		"align": "$align"
	}    
	

EOT;

	return $message ;
}

function getFlexText($text, $size = "sm", $align = "start", $flex = 1, $options = "")
{

$message = <<<EOT

	{
		"type": "text",
		"text": "$text",
		"weight": "regular",
		"size": "$size",
		"flex": $flex,
		$options
		"align": "$align"
	}    
	

EOT;

	return $message ;
}

function getFlexIcon($url = "https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gold_star_28.png")
{

$message = <<<EOT

	{
        "type": "icon",
		"size": "xs",
        "url": "$url"
    }
	

EOT;

	return $message ;
}

function getRawFlexBox($text)
{

$message = <<<EOT

	{
		"type": "box",
		"layout": "vertical",
		"spacing": "xs",
		"contents": [
			$text 
		]
	}

EOT;

	return $message ;
}

function getFlexBox($text, $size = "xl", $align = "start")
{

$message = <<<EOT

	{
		"type": "box",
		"layout": "vertical",
		"spacing": "xs",
		"contents": [

		 {
			"type": "text",
			"text": "$text",
			"weight": "bold",
			"size": "$size",
			"align": "$align"
		 }    
		]
	}

EOT;

	return $message ;
}

?>