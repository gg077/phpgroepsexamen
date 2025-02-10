<?php
require_once("includes/header.php");
ob_start();
require_once("includes/sidebar.php");
require_once("includes/content-top.php");

//// Controleer of er een melding in de sessie staat
$the_message = "";
if (isset($_SESSION['the_message'])) {
    $the_message = $_SESSION['the_message'];
    unset($_SESSION['the_message']); // Verwijder de melding na ophalen
}

if (isset($_POST['submit'])) {
    $user = new User();
    $user->email = trim($_POST['email']);
    $user->first_name = trim($_POST['first_name']);
    $user->last_name = trim($_POST['last_name']);
    $user->password = trim($_POST['password']);
    $user->save();

    if (!empty($user)) {
        $the_message = "New user: " . $user->email . " was added to the Database!";
    } else {
        $the_message = "Adding the new user FAILED!";
    }

    // Zet de boodschap in de sessie voor gebruik na redirect
    $_SESSION['the_message'] = $the_message;

    // Voer een redirect uit naar dezelfde pagina (zonder POST-data)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); // Stop verdere uitvoering van het script
}
?>

<?php if (!empty($the_message)): ?>
	<div class="alert alert-success alert-dismissible show fade">
        <?php echo $the_message; ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>
<div class="card">
	<div class="card-header">
		<h4 class="card-title">Add User</h4>
	</div>
	<div class="card-content">
		<div class="card-body">
			<form class="form form-vertical" method="post">
				<div class="form-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group has-icon-left">
								<label for="first-name-icon">email</label>
								<div class="position-relative">
									<input type="text" class="form-control"
									       placeholder="email input" id="first-name-icon" name="email">
									<div class="form-control-icon">
										<i class="bi bi-person"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group has-icon-left">
								<label for="email-id-icon">First Name</label>
								<div class="position-relative">
									<input type="text" class="form-control" placeholder="First Name Input"
									       id="email-id-icon" name="first_name">
									<div class="form-control-icon">
										<i class="bi bi-people"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group has-icon-left">
								<label for="email-id-icon">Last Name</label>
								<div class="position-relative">
									<input type="text" class="form-control" placeholder="Last Name Input"
									       id="email-id-icon" name="last_name">
									<div class="form-control-icon">
										<i class="bi bi-people"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group has-icon-left">
								<label for="password-id-icon">Password</label>
								<div class="position-relative">
									<input name="password" type="password" class="form-control" placeholder="Password"
									       id="password-id-icon">
									<div class="form-control-icon">
										<i class="bi bi-lock"></i>
									</div>
								</div>
							</div>
						</div>

						<div class="col-12 d-flex justify-content-end">
							<input type="submit" name="submit" class="btn btn-primary me-1 mb-1" value="Submit">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
require_once("includes/widget.php");
require_once("includes/footer.php");
ob_end_flush();
?>
