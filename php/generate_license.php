<?php
session_start();

function normalize($str)
{
    return strtolower(trim(preg_replace('/[[:^print:]]/', '', $str)));
}

function loadEnvVars($envPath = __DIR__ . '/.env')
{
    if (!file_exists($envPath)) return;
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($val));
        }
    }
}

loadEnvVars();

$keyHex = getenv('SOURCE_ENCRYPTION_KEY');
$key = hex2bin($keyHex);

if (!$keyHex || $key === false || strlen($key) !== 32) {
    header('Location: index.php?error=Invalid encryption key.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = $_POST['fingerprint'] ?? '';
    $fingerprint = json_decode($rawInput, true);

    if (!is_array($fingerprint) || !isset($fingerprint['machine_uuid'])) {
        header('Location: index.php?error=Invalid fingerprint JSON or missing machine_uuid.');
        exit;
    }

    $fingerprint['issued_at'] = date('Y-m-d H:i:s');

    $iv = substr(hash('sha256', $fingerprint['machine_uuid'], true), 0, 16);
    $json = json_encode($fingerprint, JSON_UNESCAPED_SLASHES);
    $encrypted = openssl_encrypt($json, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    $encoded = base64_encode($encrypted);

    // Save to file (optional)
    file_put_contents(__DIR__ . '/license.key', $encoded);

    // Pass license to frontend
    $_SESSION['license'] = $encoded;
    header('Location: index.php?success=1');
    exit;
}
