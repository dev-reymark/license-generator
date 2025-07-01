<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (file_exists(__DIR__ . '/.env')) {
    foreach (file(__DIR__ . '/.env') as $line) {
        if (preg_match('/^\s*([A-Z0-9_]+)\s*=\s*(.*)\s*$/', trim($line), $matches)) {
            putenv("{$matches[1]}={$matches[2]}");
        }
    }
}

$keyHex = getenv('SOURCE_ENCRYPTION_KEY');

if (!$keyHex || strlen($keyHex) !== 64) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid encryption key. Must be 64-character hex string.']);
    exit;
}

$key = hex2bin($keyHex);

if (!$key || strlen($key) !== 32) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid encryption key. Must be 32-byte hex.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['encFile'])) {
    $fileContent = file_get_contents($_FILES['encFile']['tmp_name']);

    $decoded = base64_decode($fileContent);

    if (!$decoded || strlen($decoded) <= 16) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or corrupted encrypted file.']);
        exit;
    }

    // Extract IV (first 16 bytes) and ciphertext (rest)
    $iv = substr($decoded, 0, 16);
    $cipherText = substr($decoded, 16);

    $decrypted = openssl_decrypt($cipherText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

    if (!$decrypted) {
        file_put_contents('debug.log', "Decryption failed.\n", FILE_APPEND);
        file_put_contents('debug.log', "Key: " . bin2hex($key) . "\n", FILE_APPEND);
        file_put_contents('debug.log', "IV: " . bin2hex($iv) . "\n", FILE_APPEND);
        file_put_contents('debug.log', "CipherText Length: " . strlen($cipherText) . "\n", FILE_APPEND);
        file_put_contents('debug.log', "OpenSSL Error: " . openssl_error_string() . "\n", FILE_APPEND);

        http_response_code(400);
        echo json_encode(['error' => 'Decryption failed. Check debug.log for details.']);
        exit;
    }

    // Output the decrypted JSON
    header('Content-Type: application/json');
    echo $decrypted;
}
