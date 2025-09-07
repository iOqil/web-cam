<?php
// Kamera sozlamalari
$cameraIP   = "192.168.1.4";
$cameraPort = "8899";
$username   = "admin";
$password   = "your_password";
$profile    = "PROFILE_000"; // Avval GetProfiles bilan tekshirib to‘g‘ri tokenni qo‘ying

// Frontenddan kelgan cmd
$cmd = $_POST['cmd'] ?? 'stop';

// Yo‘nalish sozlamalari
$commands = [
    "left"     => ['x' => -1.0, 'y' => 0,    'z' => 0],
    "right"    => ['x' => 1.0,  'y' => 0,    'z' => 0],
    "up"       => ['x' => 0,    'y' => 1.0,  'z' => 0],
    "down"     => ['x' => 0,    'y' => -1.0, 'z' => 0],
    "zoom_in"  => ['x' => 0,    'y' => 0,    'z' => 1.0],
    "zoom_out" => ['x' => 0,    'y' => 0,    'z' => -1.0],
    "stop"     => ['x' => 0,    'y' => 0,    'z' => 0],
];

$velocity = $commands[$cmd] ?? $commands['stop'];

// Agar stop bo‘lsa boshqa XML
if ($cmd === "stop") {
    $xml = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
            xmlns:tptz="http://www.onvif.org/ver20/ptz/wsdl">
  <s:Body>
    <tptz:Stop>
      <tptz:ProfileToken>$profile</tptz:ProfileToken>
      <tptz:PanTilt>true</tptz:PanTilt>
      <tptz:Zoom>true</tptz:Zoom>
    </tptz:Stop>
  </s:Body>
</s:Envelope>
XML;
} else {
    $x = $velocity['x'];
    $y = $velocity['y'];
    $z = $velocity['z'];
    $xml = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
            xmlns:tptz="http://www.onvif.org/ver20/ptz/wsdl"
            xmlns:tt="http://www.onvif.org/ver10/schema">
  <s:Body>
    <tptz:ContinuousMove>
      <tptz:ProfileToken>$profile</tptz:ProfileToken>
      <tptz:Velocity>
        <tt:PanTilt x="$x" y="$y"/>
        <tt:Zoom x="$z"/>
      </tptz:Velocity>
    </tptz:ContinuousMove>
  </s:Body>
</s:Envelope>
XML;
}

$url = "http://$cameraIP:$cameraPort/onvif/ptz_service";

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
$err = curl_error($ch);
curl_close($ch);

header("Content-Type: text/plain");
echo $err ? "Curl error: $err" : $response;
