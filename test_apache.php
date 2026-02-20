<?php
sleep(2);
$ch = curl_init('http://localhost/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_HEADER => 1
]);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo 'HTTP Status: ' . $info['http_code'] . "\n";
echo 'Content Length: ' . strlen($response) . " bytes\n";

if ($info['http_code'] == 307) {
    if (preg_match('/Location:\s*(.*?)\n/', $response, $m)) {
        echo 'Redirect to: ' . trim($m[1]) . "\n";
    }
}

if (preg_match('/<title>(.*?)<\/title>/i', $response, $m)) {
    echo 'Page Title: ' . htmlspecialchars($m[1]) . "\n";
}
?>
