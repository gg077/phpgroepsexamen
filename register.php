<?php
require_once("includes/header.php");

$the_message = "";

if (isset($_SESSION['the_message'])) {
    $the_message = $_SESSION['the_message'];
    unset($_SESSION['the_message']); // Verwijder de melding na ophalen
}
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmpassword = trim($_POST['confirmpassword']);
    //check als de user bestaat in onze database
    $user_found = User::verify_user($username, $password);

    if ($user_found) {
        $the_message = "This user exists, please login!";

    } elseif ($password === $confirmpassword) {
        $user = new User();
        $user->username = trim($_POST['username']);
        $user->first_name = trim($_POST['first_name']);
        $user->last_name = trim($_POST['last_name']);
        $user->password = trim($_POST['password']);
        $user->create();

        $the_message = "New user: " . $user->username . " was added to the Database, click below to login!";

        // Zet de boodschap in de sessie voor gebruik na redirect
        $_SESSION['the_message'] = $the_message;

        // Voer een redirect uit naar dezelfde pagina (zonder POST-data)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Stop verdere uitvoering van het script
    }else{
        $the_message = "Paswords are not alike!";

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
						<input type="text" class="form-control form-control-xl" placeholder="Username" name="username" required>
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
