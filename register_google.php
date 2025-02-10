<?php
require_once("includes/header.php");
if (!session_id()) {
    session_start();
}
require_once './includes/config_google.php';
require_once './admin/includes/Db_object.php';
require_once './admin/includes/User.php';

// Controleer of de gebruiker via Google is geregistreerd
if (!isset($_SESSION['register_google_id']) || !isset($_SESSION['register_email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['register_email'];
$google_id = $_SESSION['register_google_id'];
$first_name = $_SESSION['register_first_name'];
$last_name = $_SESSION['register_last_name'];

$the_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = trim($_POST['password']);
    $confirmpassword = trim($_POST['confirmpassword']);

    if ($password === $confirmpassword) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Maak de gebruiker aan in de database
        $new_user = new User();
        $new_user->email = $email;
        $new_user->google_id = $google_id;
        $new_user->password = $hashed_password;
        $new_user->first_name = $first_name;
        $new_user->last_name = $last_name;
        $new_user->create();

        // Inloggen en doorsturen naar koppeling
        $_SESSION['link_google_id'] = $google_id;
        $_SESSION['link_email'] = $email;

        unset($_SESSION['register_google_id'], $_SESSION['register_email']);
        header("Location: link_google_account.php");
        exit();
    } else {
        $the_message = "Passwords do not match!";
    }
}
?>

<div id="auth">
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="index.php"><img src="./admin/assets/compiled/svg/logo.svg" alt="Logo"></a>
                </div>
                <h1 class="auth-title">Create Password</h1>
                <p class="auth-subtitle mb-5">Set a password for your account to enable password login.</p>
                <?php if(!empty($the_message)):?>
                    <div class="alert alert-danger alert-dismissible show fade">
                        <?php echo $the_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form action="" method="post">
                    <!-- Automatisch ingevulde e-mail -->
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" class="form-control form-control-xl" name="email" value="<?php echo htmlentities($email); ?>" disabled>
                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-xl" placeholder="Password" name="password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-xl" placeholder="Confirm Password" name="confirmpassword" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <input type="submit" name="submit" value="Save Password" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
                </form>
                <div class="text-center mt-5 text-lg fs-4">
                    <p class='text-gray-600'>Already have an account? <a href="login.php" class="font-bold">Log in</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
    </div>
</div>
<script src="./admin/assets/compiled/js/app.js"></script>

</body>
</html>
