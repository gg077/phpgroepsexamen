<?php
if (!session_id()) {
    session_start();
}

// Google credentials
define('GOOGLE_CLIENT_ID', '778909448480-0uo8b8deag2te15nq7di0cao7blc73dm.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://127.0.0.1/blogoop2025klas/google_callback.php'); // Pas dit aan aan jouw domein

// Controleer of alless correct gelade is
if (!defined('GOOGLE_CLIENT_ID')) {
    die("GOOGLE_CLIENT_ID is niet correct gedefinieerd in `config_google.php`!");
}

?>
