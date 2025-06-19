<?php
// pix.php - Backend to create Pix charge via BlackCat Pagamentos API

// Your BlackCat API keys
$publicKey = 'pk_pXb05DCxytcnz8SViYmOjSo2BlHKf0vUlpegTgmgkfwdNF-7';
$secretKey = 'sk_Br-pkbauum5bAzSRqqHa1kfcirDqVLrVMRu5Dr-gZdn2B4WP';

// Set content type to JSON
header('Content-Type: application/json');

// Read POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos.']);
    exit;
}

// Validate required fields
$requiredFields = ['fullName', 'cpf', 'email', 'phone', 'quantity', 'unitPrice'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Campo obrigatório faltando: $field"]);
        exit;
    }
}

// Sanitize and assign variables
$fullName = filter_var($data['fullName'], FILTER_SANITIZE_STRING);
$cpf = preg_replace('/\D/', '', $data['cpf']);
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$phone = preg_replace('/\D/', '', $data['phone']);
$quantity = (int)$data['quantity'];
$unitPrice = (float)$data['unitPrice'];

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'E-mail inválido.']);
    exit;
}

if ($quantity < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Quantidade inválida.']);
    exit;
}

// Calculate total amount in cents
$amount = intval(round($unitPrice * $quantity * 100));

// Prepare payload for BlackCat API
$payload = [
    'amount' => $amount,
    'paymentMethod' => 'pix',
    'customer' => [
        'name' => $fullName,
        'cpf' => $cpf,
        'email' => $email,
        'phone' => $phone,
    ],
    'items' => [
        [
            'name' => 'Cotas',
            'quantity' => $quantity,
            'unitPrice' => intval(round($unitPrice * 100)),
        ]
    ],
];

// Prepare Basic Auth header
$auth = base64_encode($publicKey . ':' . $secretKey);
$headers = [
    "Authorization: Basic $auth",
    "Content-Type: application/json",
];

// Initialize cURL
$ch = curl_init('https://api.blackcatpagamentos.com/v1/transactions');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['error' => "Erro na requisição: $error_msg"]);
    exit;
}

curl_close($ch);

if ($httpCode !== 201 && $httpCode !== 200) {
    http_response_code($httpCode);
    echo $response;
    exit;
}

$result = json_decode($response, true);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Resposta inválida da API.']);
    exit;
}

// Extract Pix payment info
$qrCodeUrl = $result['pix']['qrCode'] ?? null;
$copyPasteCode = $result['pix']['copyPasteCode'] ?? null;
$status = $result['status'] ?? null;
$paymentId = $result['id'] ?? null;

// Store payment info locally (append to a JSON file)
$storageFile = 'payments.json';
$paymentRecord = [
    'id' => $paymentId,
    'fullName' => $fullName,
    'cpf' => $cpf,
    'email' => $email,
    'phone' => $phone,
    'quantity' => $quantity,
    'unitPrice' => $unitPrice,
    'amount' => $amount,
    'status' => $status,
    'createdAt' => date('c'),
];

if (file_exists($storageFile)) {
    $existingData = json_decode(file_get_contents($storageFile), true);
    if (!is_array($existingData)) {
        $existingData = [];
    }
} else {
    $existingData = [];
}

$existingData[] = $paymentRecord;
file_put_contents($storageFile, json_encode($existingData, JSON_PRETTY_PRINT));

// Return response to frontend
echo json_encode([
    'qrCodeUrl' => $qrCodeUrl,
    'copyPasteCode' => $copyPasteCode,
    'status' => $status,
    'amount' => $amount,
    'paymentId' => $paymentId,
]);
?>
