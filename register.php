<?php
require_once("includes/header.php");

$the_message = "";

if (isset($_SESSION['the_message'])) {
    $the_message = $_SESSION['the_message'];
    unset($_SESSION['the_message']);
}

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmpassword = trim($_POST['confirmpassword']);

    // Controleer of gebruiker bestaat op basis van e-mail
    $user_found = User::find_by_email($email);

    if ($user_found) {
        $the_message = "This user exists, please login!";
    } elseif ($password === $confirmpassword) {
        // Hash het wachtwoord voordat het wordt opgeslagen
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $user = new User();
        $user->email = $email;
        $user->first_name = trim($_POST['first_name']);
        $user->last_name = trim($_POST['last_name']);
        $user->password = $hashed_password;  // Gebruik gehasht wachtwoord
        $user->create();

        $the_message = "New user: " . $user->email . " was added, click below to login!";
        $_SESSION['the_message'] = $the_message;
        header("Location: login.php"); // Redirect naar login
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
				<h1 class="auth-title">Sign Up</h1>
				<p class="auth-subtitle mb-5">Input your data to register to our website.</p>
                <?php if(!empty($the_message)):?>
					<div class="alert alert-danger alert-dismissible show fade">
                        <?php echo $the_message; ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
                <?php endif; ?>
				<form action="" method="post">
					<div class="form-group position-relative has-icon-left mb-4">
						<input type="text" class="form-control form-control-xl" placeholder="First Name" name="first_name" required>
						<div class="form-control-icon">
							<i class="bi bi-person"></i>
						</div>
					</div>
					<div class="form-group position-relative has-icon-left mb-4">
						<input type="text" class="form-control form-control-xl" placeholder="Last Name" name="last_name" required>
						<div class="form-control-icon">
							<i class="bi bi-person"></i>
						</div>
					</div>
					<div class="form-group position-relative has-icon-left mb-4">
						<input type="text" class="form-control form-control-xl" placeholder="email" name="email" required>
						<div class="form-control-icon">
							<i class="bi bi-person"></i>
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
					<input type="submit" name="submit" value="Register" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
					<!--                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>-->
				</form>
				<div class="text-center mt-5 text-lg fs-4">
					<p class='text-gray-600'>Already have an account? <a href="login.php" class="font-bold">Log
							in</a>.</p>
				</div>
			</div>
		</div>
		<div class="col-lg-7 d-none d-lg-block">
			<div id="auth-right">

			</div>
		</div>
	</div>

</div>
<script src="./admin/assets/compiled/js/app.js"></script>

</body>

</html>
