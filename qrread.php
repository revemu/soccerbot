<?php
    require __DIR__ . '/vendor/autoload.php';
    //use Zxing\QrReader;
    $channelAccessToken = 'RLy8qoVKhlAlmNhHEvZ/D8OLdz44FyukW4Uo9FQwbUh5oYNsghDXda4WxCS7MKW9BawEgRcyDAWGNGFPN8sR81q197MwlLOfcKFotdZjZ3k+Unf05Pxvtuyw1g/tQNKDXtMDyn06klKSeJL0EvQsuAdB04t89/1O/w1cDnyilFU=';
    $uploadDirectory = '/var/www/html/img_line/'; // e.g., 'images/'
    $fileName = uniqid() . '.jpg'; // Generate a unique filename, or use a more descriptive name
    $filePath = $uploadDirectory . $fileName;
    $url_content = "https://api-data.line.me/v2/bot/message/573764910014791791/content" ;
    $ch = curl_init($url_content);
    $headers = array('Authorization: Bearer ' . $channelAccessToken);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $imageData = curl_exec($ch);
    $data = base64_encode($imageData);
    curl_close($ch);
    //file_put_contents($filePath, $imageData);
    //echo "$filePath\n" ;
    $QRCodeReader = new Libern\QRCodeReader\QRCodeReader();
    $qrcode_text = $QRCodeReader->decode('data:image/jpg;base64,'.$data);
    //$qrcode = new QrReader('/var/www/html/slip.jpg');
    //$decodedText = $qrcode->text();
//$qrcode_text = $QRCodeReader->decode("/var/www/html/slip1.jpg");
    print("qr:" . $qrcode_text . "\n") ;
?>