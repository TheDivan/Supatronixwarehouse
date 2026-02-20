<?php
// Get response body even on 500 errors
$ch = curl_init('http://localhost:8000/index.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);  // Don't follow to capture actual response
curl_setopt($ch, CURLOPT_FAILONERROR, false); // Don't fail on HTTP errors
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "HTTP Code: $code\n";
echo "Content-Length: " . strlen($response) . "\n";
echo "==== RESPONSE BODY ====\n";
echo $response;
echo "\n==== END ====\n";
?>
