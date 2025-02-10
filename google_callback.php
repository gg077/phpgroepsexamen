<?php
session_start();

//  Controleer of `config_google.php` correct wordt geladen
if (!file_exists('./includes/config_google.php')) {
    die("Fout: `config_google.php` bestaat niet of kan niet worden gevonden!");
}
require_once './includes/config_google.php';

//  Controleer of GOOGLE_CLIENT_ID is geladen
if (!defined('GOOGLE_CLIENT_ID')) {
    die("Fout: GOOGLE_CLIENT_ID is niet gedefinieerd. Controleer `config_google.php`!");
}

require_once './admin/includes/database.php';
require_once './admin/includes/Db_object.php';
require_once './admin/includes/User.php';



//  Controleer of de Google OAuth-code aanwezig is
if (!isset($_GET['code'])) {
    die("Fout: Geen authorisatiecode ontvangen van Google.");
}

//  Stap 1: Token aanvragen bij Google
$token_url = 'https://oauth2.googleapis.com/token';
$post_data = [
    'code'          => $_GET['code'],
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
];

//  cURL Initialiseren
$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Alleen voor lokaal testen!
curl_setopt($ch, CURLOPT_HEADER, true); // Toon headers in de response voor debugging

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

//  Stap 2: Controleer of de token respons geldig is
if (!$response) {
    die("Fout: Lege respons ontvangen van Google. Controleer je internetverbinding of firewall.");
}

//  Debugging - Print de volledige Google-respons als er een fout is
$response_parts = explode("\r\n\r\n", $response, 2); // Headers en body splitsen
$response_body = isset($response_parts[1]) ? $response_parts[1] : '{}';
$token_data = json_decode($response_body, true);

if ($http_code !== 200) {
    die("Fout bij ophalen access token. HTTP Status: $http_code\n\nGoogle Response: " . json_encode($token_data, JSON_PRETTY_PRINT));
}

//  Stap 3: Controleer of de access token aanwezig is
if (!isset($token_data['access_token']) || empty($token_data['access_token'])) {
    die("Fout: Geen toegangstoken ontvangen van Google. Debug info: " . json_encode($token_data, JSON_PRETTY_PRINT));
}

$access_token = $token_data['access_token'];

//  Stap 4: Gebruikersgegevens ophalen bij Google
$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $access_token;
$user_info_json = file_get_contents($user_info_url);
$user_info = json_decode($user_info_json, true);

//  Stap 5: Controleer of gebruikersgegevens aanwezig zijn
if (!$user_info || !isset($user_info['email'])) {
    die("Fout: Geen gebruikersgegevens ontvangen van Google. Response: " . json_encode($user_info, JSON_PRETTY_PRINT));
}

//  Stap 6: Controleer databaseverbinding
global $database;
if (!$database) {
    die("Databaseverbinding mislukt! Controleer je `config.php`.");
}

//  Stap 7: Controleer of gebruiker al bestaat
$user = User::find_by_email($user_info['email']);

if ($user) {
    //  Gebruiker bestaat al, maar heeft nog geen Google-koppeling
    if (empty($user->google_id)) {
        $_SESSION['link_google_id'] = $user_info['id'];
        $_SESSION['link_email'] = $user_info['email'];
        header("Location: link_google_account.php");
        exit();
    }
} else {
    //  Nieuwe gebruiker aanmaken, maar moet nog een wachtwoord instellen
    $_SESSION['register_google_id'] = $user_info['id'];
    $_SESSION['register_email'] = $user_info['email'];
    $_SESSION['register_first_name'] = $user_info['given_name'] ?? '';
    $_SESSION['register_last_name'] = $user_info['family_name'] ?? '';

    header("Location: register_google.php"); // Stuur door naar wachtwoord instellen
    exit();
}

//  Stap 9: Inloggen en doorsturen
$_SESSION['user_id'] = $user->id;
header('Location: admin/index.php');
exit();
