<?php
$ch = curl_init('http://localhost:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "== LOGIN PAGE TEST ==\n";
echo "HTTP Code: $code\n";
if ($code == 200) {
    echo "✓ Login page loaded successfully!\n";
    echo "Response length: " . strlen($response) . " bytes\n";
    // Check if login form exists
    if (stripos($response, 'login') !== false || stripos($response, 'password') !== false) {
        echo "✓ Login form found in response\n";
    } else {
        echo "✗ Login form not found\n";
    }
} else {
    echo "✗ Unexpected HTTP code\n";
}
?>
