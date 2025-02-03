<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top.php");

//// Controleer of er een melding in de sessie staat
//$the_message = "";
//if (isset($_SESSION['the_message'])) {
//    $the_message = $_SESSION['the_message'];
//    unset($_SESSION['the_message']); // Verwijder de melding na ophalen
//}
//
//if (isset($_POST['submit'])) {
//    $user = new User();
//    $user->username = trim($_POST['username']);
//    $user->first_name = trim($_POST['first_name']);
//    $user->last_name = trim($_POST['last_name']);
//    $user->password = trim($_POST['password']);
//    $user->create();
//
//    if (!empty($user)) {
//        $the_message = "New user: " . $user->username . " was added to the Database!";
//    } else {
//        $the_message = "Adding the new user FAILED!";
//    }
//
//    // Zet de boodschap in de sessie voor gebruik na redirect
//    $_SESSION['the_message'] = $the_message;
//
//    // Voer een redirect uit naar dezelfde pagina (zonder POST-data)
//    header("Location: " . $_SERVER['PHP_SELF']);
//    exit(); // Stop verdere uitvoering van het script
//}
if(empty($_GET['id'])){
	header("location:users.php");
}
$user = User::find_by_id($_GET['id']);

if(isset($_POST['updateuser'])){
	if($user){
		$user->username = $_POST['username'];
		$user->first_name = $_POST['first_name'];
		$user->last_name = $_POST['last_name'];
		$user->save();
		header("location:users.php");
	}
}
?>

<?php //if (!empty($the_message)): ?>
<!--	<div class="alert alert-success alert-dismissible show fade">-->
<!--        --><?php //echo $the_message; ?>
<!--		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>-->
<!--	</div>-->
<?php //endif; ?>
<div class="card">
	<div class="card-header d-flex justify-content-between">
		<h4 class="card-title">Edit User</h4>
		<a href="users.php">
			<i class="bi bi-house text-primary display-6"></i>
		</a>

	</div>
	<div class="card-content">
		<div class="card-body">
			<form class="form form-vertical" method="post">
				<div class="form-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group has-icon-left">
								<label for="first-name-icon">Username</label>
								<div class="position-relative">
									<input type="text" class="form-control"
									       placeholder="Username input" id="first-name-icon" name="username" value="<?php echo $user->username; ?>">
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
									       id="email-id-icon" name="first_name" value="<?php echo $user->first_name; ?>">
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
									       id="email-id-icon" name="last_name" value="<?php echo $user->last_name; ?>">
									<div class="form-control-icon">
										<i class="bi bi-people"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 d-flex justify-content-end">
							<input type="submit" name="updateuser" class="btn btn-primary me-1 mb-1" value="Update">
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
?>
