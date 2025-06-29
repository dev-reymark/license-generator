<?php

function normalize($str)
{
    return strtolower(trim(preg_replace('/[[:^print:]]/', '', $str)));
}

function extractWmicValue($command)
{
    $output = shell_exec($command . ' 2>nul');
    $lines = array_filter(explode("\n", trim($output)));
    return isset($lines[1]) ? normalize($lines[1]) : '';
}

function extractVolumeSerial()
{
    $output = shell_exec('vol C: 2>nul');
    if (preg_match('/Serial Number is ([\w\-]+)/i', $output, $matches)) {
        return normalize($matches[1]);
    }
    return '';
}

function extractMacAddress()
{
    $output = shell_exec('getmac | findstr /V "Media"');
    if (preg_match('/([0-9A-Fa-f]{2}[-:]){5}[0-9A-Fa-f]{2}/', $output, $matches)) {
        return normalize($matches[0]);
    }
    return '';
}

$fingerprint = [
    'bios_serial' => extractWmicValue('wmic bios get serialnumber'),
    'volume_serial' => extractVolumeSerial(),
    'os_serial' => extractWmicValue('wmic os get SerialNumber'),
    'machine_uuid' => extractWmicValue('wmic csproduct get uuid'),
    'cpu_id' => extractWmicValue('wmic cpu get ProcessorId'),
    'mac' => extractMacAddress(),
];

header('Content-Type: application/json');
echo json_encode($fingerprint, JSON_PRETTY_PRINT);
