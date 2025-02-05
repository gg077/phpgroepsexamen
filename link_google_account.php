<?php
require_once './includes/config_google.php';
require_once './admin/includes/Db_object.php';
require_once './admin/includes/User.php';

session_start();

if (!isset($_SESSION['link_google_id']) || !isset($_SESSION['link_email'])) {
    header("Location: login.php");
    exit();
}

$google_id = $_SESSION['link_google_id'];
$email = $_SESSION['link_email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = User::find_by_email($email);

    if ($user) {
        $user->google_id = $google_id;
        $user->update();

        $_SESSION['user_id'] = $user->id;
        header("Location: dashboard.php");
        exit();
    }
}

?>
<form method="POST">
    <p>Wil je je Google-account koppelen aan je bestaande account?</p>
    <button type="submit">Ja, koppelen</button>
    <a href="login.php">Nee, terug</a>
</form>
