<?php
require_once './includes/config_google.php';

if (!isset($_GET['code'])) die("Geen authorisatiecode ontvangen");

// Debug output
error_log("Ontvangen code: " . $_GET['code']);

$post_data = [
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code',
    'code' => $_GET['code']
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Uit voor localhost

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if (!empty($error)) die("cURL Fout: " . $error);

$token_data = json_decode($response, true);

if (isset($token_data['error'])) {
    die("Google API Fout: " . $token_data['error_description']);
}

// Verwerk de gebruiker hierna...
$_SESSION['google_token'] = $token_data['access_token'];
header("Location:admin/index.php");
exit();
?>