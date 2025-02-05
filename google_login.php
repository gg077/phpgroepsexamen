<?php
session_start();
require_once './includes/config_google.php';
require_once './admin/includes/Db_object.php';
require_once './admin/includes/User.php';

define('GOOGLE_CLIENT_ID', '778909448480-0uo8b8deag2te15nq7di0cao7blc73dm.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://127.0.0.1/blogoop2025klas/google_callback.php');


// Stap 1: Redirect naar Google login
if (!isset($_GET['code'])) {
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'openid email profile',
    ];
    header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    exit();
}

// Stap 2: Code verwerken
$token_url = 'https://oauth2.googleapis.com/token';
$post_data = [
    'code' => $_GET['code'],
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$token_data = json_decode($response, true);

// Stap 3: Gebruikersgegevens ophalen
$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token_data['access_token'];
$user_info = json_decode(file_get_contents($user_info_url), true);

// Stap 4: Gebruiker registreren of inloggen
$user = User::find_by_email($user_info['email']);

if (!$user) {
    // Nieuwe gebruiker aanmaken
    $new_user = new User();
    $new_user->email = $user_info['email'];
    $new_user->google_id = $user_info['id'];
    $new_user->first_name = $user_info['given_name'];
    $new_user->last_name = $user_info['family_name'];
    $new_user->create();
    $user = $new_user;
}

// Inloggen
$_SESSION['user_id'] = $user->id;
header('Location: admin/index.php');
exit();
?>