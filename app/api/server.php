<?php

header('Content-Type: application/json');
function generateAESKey() {
    return openssl_random_pseudo_bytes(32);
}

// Lấy Public Key từ client
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['publicKey'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Public key is required']);
    exit;
}

$publicKey = $data['publicKey'];

$aesKey = generateAESKey();

// Mã hóa khóa AES bằng Public Key
openssl_public_encrypt($aesKey, $encryptedAESKey, $publicKey);

if (!$encryptedAESKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to encrypt AES key']);
    exit;
}

echo json_encode([
    'encryptedKey' => base64_encode($encryptedAESKey),
]);
?>
