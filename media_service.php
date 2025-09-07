<?php
$cameraIP   = "192.168.1.4";
$cameraPort = "8899";
$username   = "admin";
$password   = "your_password";

$xml = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
            xmlns:trt="http://www.onvif.org/ver10/media/wsdl">
  <s:Body>
    <trt:GetProfiles/>
  </s:Body>
</s:Envelope>
XML;

$url = "http://$cameraIP:$cameraPort/onvif/media_service";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/soap+xml; charset=utf-8",
    "Content-Length: " . strlen($xml)
]);

$response = curl_exec($ch);
curl_close($ch);

header("Content-Type: text/xml");
echo $response;
