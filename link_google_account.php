<?php
// ✅ Start de sessie als die nog niet gestart is (voorkomt foutmelding)
if (!session_id()) {
    session_start();
}

// ✅ Config en database laden
require_once './includes/config_google.php';
require_once './admin/includes/database.php';
require_once './admin/includes/Db_object.php';
require_once './admin/includes/User.php';

// ✅ Controleer of de databaseverbinding bestaat
global $database;
if (!isset($database) || !$database->connection) {
    die("❌ Databaseverbinding mislukt! Controleer je `config.php` en `database.php`.");
}

// ✅ Controleer of Google ID en e-mail aanwezig zijn in de sessie
if (!isset($_SESSION['link_google_id']) || !isset($_SESSION['link_email'])) {
    header("Location: login.php");
    exit();
}

$google_id = $_SESSION['link_google_id'];
$email = $_SESSION['link_email'];

// ✅ Koppel Google-account aan bestaand account
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = User::find_by_email($email);

    if ($user) {
        $user->google_id = $google_id;
        $user->update();

        // ✅ Log gebruiker in en verwijder tijdelijke sessiegegevens
        $_SESSION['user_id'] = $user->id;
        unset($_SESSION['link_google_id'], $_SESSION['link_email']);

        header("Location: admin/index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google-account koppelen</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .google-icon {
            font-size: 50px;
            color: #4285F4;
        }
        .btn-google {
            background-color: #4285F4;
            color: white;
            font-weight: 500;
        }
        .btn-google:hover {
            background-color: #357ae8;
        }
        .btn-secondary {
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Centered container -->
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 text-center">
        <div class="mb-3">
            <span class="material-icons google-icon">link</span>
        </div>
        <h4>Google-account koppelen</h4>
        <p class="text-muted">Je hebt al een account met dit e-mailadres.<br>Wil je je Google-account hieraan koppelen?</p>

        <form method="POST">
            <button type="submit" class="btn btn-google w-100 mb-2">✅ Ja, koppelen</button>
            <a href="login.php" class="btn btn-secondary w-100">❌ Nee, terug</a>
        </form>
    </div>
</div>

<!-- Bootstrap JS (voor betere styling) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>