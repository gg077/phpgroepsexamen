<?php
require_once("includes/header.php");

?>
<?php
	$the_message = "";

    if($session->is_signed_in()){
        header("location:index.php");
    }
    if(isset($_POST['submit'])){
        $email=trim($_POST['email']);
        $password=trim($_POST['password']);
        //check als de user bestaat in onze database
        $user_found = User::verify_user($email, $password);

		if($user_found){
			$session->login($user_found);
			header("location:admin/index.php");
            exit();
		}else{
			$the_message = "Your password and email FAILED!";
		}
    }else{
		$email = "";
		$password = "";
    }

$params = [
    'client_id'     => '778909448480-0uo8b8deag2te15nq7di0cao7blc73dm.apps.googleusercontent.com',
    'redirect_uri'  => 'http://127.0.0.1/blogoop2025klas/google_callback.php',
    'response_type' => 'code',
    'scope'         => 'openid email profile',
];

$google_auth_url = "https://accounts.google.com/o/oauth2/auth?" . http_build_query($params);


?>
<div id="auth">
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="index.php"><img src="./admin/assets/compiled/svg/logo.svg" alt="Logo"></a>
                </div>
                <h1 class="auth-title">Log in.</h1>
                <p class="auth-subtitle mb-5">Log in with your data that you entered during registration.</p>
	            <?php if(!empty($the_message)):?>
		            <div class="alert alert-success alert-dismissible show fade">
			            <?php echo $the_message; ?>
			            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		            </div>
				<?php endif; ?>
                <form action="" method="post">
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" class="form-control form-control-xl" placeholder="email" name="email" value="<?php echo htmlentities($email); ?>">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-xl" placeholder="Password" name="password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <div class="form-check form-check-lg d-flex align-items-end">
                        <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label text-gray-600" for="flexCheckDefault">
                            Keep me logged in
                        </label>
                    </div>
                    <div class="d-flex justify-content-center gap-3 mt-5">
                        <input type="submit" name="submit" value="Log in" class="btn btn-primary" style="width: 200px">

                        <a href="<?php echo htmlspecialchars($google_auth_url); ?>" class="btn btn-danger">
                            Log in met Google
                        </a>

                    </div>


                    <!--                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>-->
                </form>

                <div class="text-center mt-3 text-lg fs-4">
                    <p class="text-gray-600">Don't have an account? <a href="register.php" class="font-bold">Sign
                            up</a>.</p>
                    <p><a class="font-bold" href="auth-forgot-password.html">Forgot password?</a>.</p>
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
<?php ob_end_flush(); ?>
</html>
