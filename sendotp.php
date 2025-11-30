<?php
// sendotp.php - EmailJS OTP Sender

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Preflight check
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$SERVICE_ID  = "service_wdvsyrl";
$TEMPLATE_ID = "template_kjvl0fz";
$PUBLIC_KEY  = "0psZdLhjDM-06doVy";

// Read input
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["status" => "error", "msg" => "Invalid JSON"]);
    exit;
}

$email = isset($data['email']) ? trim($data['email']) : "";
$otp   = isset($data['otp']) ? trim($data['otp']) : "";

// Validation
if (empty($email) || empty($otp)) {
    echo json_encode(["status" => "error", "msg" => "Missing email or otp"]);
    exit;
}

// Build EmailJS payload
$payload = [
    "service_id" => $SERVICE_ID,
    "template_id" => $TEMPLATE_ID,
    "user_id" => $PUBLIC_KEY,
    "template_params" => [
        "to_email" => $email,
        "otp" => $otp
    ]
];

$ch = curl_init("https://api.emailjs.com/api/v1.0/email/send");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curl_error) {
    echo json_encode(["status" => "error", "msg" => $curl_error]);
} elseif ($http_code >= 200 && $http_code < 300) {
    echo json_encode(["status" => "success", "msg" => "OTP sent successfully"]);
} else {
    echo json_encode([
        "status" => "error",
        "msg" => "EmailJS response: ".$response,
        "http_code" => $http_code
    ]);
}
?>
