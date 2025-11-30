<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$SERVICE_ID  = "service_wdvsyrl";
$TEMPLATE_ID = "template_kjvl0fz";
$PUBLIC_KEY  = "0psZdLhjDM-06doVy";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data["email"];
$otp   = $data["otp"];

$payload = [
    "service_id" => $SERVICE_ID,
    "template_id" => $TEMPLATE_ID,
    "user_id" => $PUBLIC_KEY,
    "template_params" => [
        "email" => $email,   // ← FIXED
        "otp" => $otp        // ← FIXED
    ]
];

$ch = curl_init("https://api.emailjs.com/api/v1.0/email/send");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(["status" => "error", "msg" => $error]);
} else {
    echo json_encode(["status" => "success", "emailjs" => $response]);
}
?>
