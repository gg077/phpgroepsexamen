<?php
session_start();
require_once 'includes/config.php'; // Database-verbinding
require_once 'admin/includes/User.php'; // User-model

// 🔹 Google API instellingen
define('GOOGLE_CLIENT_ID', '778909448480-0uo8b8deag2te15nq7di0cao7blc73dm.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://127.0.0.1/blogoop2025klas/google_login.php');

define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// 🔹 Stap 1: Google Login-knop
if (!isset($_SESSION['user']) && !isset($_GET['code'])) {
    $params = [
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope'         => 'openid email profile',
        'access_type'   => 'offline',
        'prompt'        => 'consent',
    ];
    $auth_url = GOOGLE_AUTH_URL . '?' . http_build_query($params);
    echo "<a href='" . htmlspecialchars($auth_url) . "' class='btn btn-danger'>Log in met Google</a>";
    exit();
}

// 🔹 Stap 2: Haal de access token op
if (isset($_GET['code'])) {
    $post_data = [
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code',
        'code'          => $_GET['code'],
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (!isset($token_data['access_token'])) {
        die("Fout bij ophalen access token: " . json_encode($token_data));
    }

    $_SESSION['access_token'] = $token_data['access_token'];

    // 🔹 Stap 3: Haal gebruikersgegevens op
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GOOGLE_USERINFO_URL . '?access_token=' . $_SESSION['access_token']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $user_info = json_decode($response, true);

    if (!isset($user_info['email'])) {
        die("Fout bij ophalen gebruikersgegevens.");
    }

    // 🔹 Stap 4: Sla gebruiker op in database
    $user = User::find_by_email($user_info['email']);

    if (!$user) {
        // 🔹 Gebruiker bestaat niet, maak een nieuwe aan
        $new_user = new User();
        $new_user->email = $user_info['email'];
        $new_user->google_id = $user_info['id'];
        $new_user->first_name = $user_info['given_name'] ?? '';
        $new_user->last_name = $user_info['family_name'] ?? '';
        $new_user->password = NULL; // Geen wachtwoord nodig voor Google-login
        $new_user->create();
        $user = $new_user;
    } else {
        // 🔹 Gebruiker bestaat al, update Google ID indien nodig
        if (!$user->google_id) {
            $user->google_id = $user_info['id'];
            $user->update();
        }
    }

    // 🔹 Sla de gebruiker op in de sessie
    $_SESSION['user'] = [
        'id'       => $user->id,
        'name'     => $user_info['name'],
        'email'    => $user_info['email'],
        'picture'  => $user_info['picture'],
    ];

    // Refresh de pagina zonder de code parameter in de URL
    header("Location: " . GOOGLE_REDIRECT_URI);
    exit();
}

// 🔹 Stap 5: Dashboard weergeven als de gebruiker is ingelogd
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    echo "<h1>Welkom, " . htmlspecialchars($user['name']) . "!</h1>";
    echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
    echo "<img src='" . htmlspecialchars($user['picture']) . "' alt='Profielfoto'>";
    echo "<br><br><a href='?logout=true'>Uitloggen</a>";
}

// 🔹 Stap 6: Uitloggen
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: google_login.php");
    exit();
}
?>
