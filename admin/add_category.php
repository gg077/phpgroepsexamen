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
    $category = new Category();
    $category->name = trim($_POST['name']);
    $category->save();

    if (!empty($category)) {
        $the_message = "New category: " . $category->name . " was added to the Database!";
    } else {
        $the_message = "Adding the new category FAILED!";
    }

    // Zet de boodschap in de sessie voor gebruik na redirect
    $_SESSION['the_message'] = $the_message;
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
            <h4 class="card-title">Add Category</h4>
        </div>
        <div class="card-body">
            <form class="form form-vertical" method="post">
                <div class="form-group has-icon-left">
                    <label for="name-id-icon">Name</label>
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Name input" id="name-id-icon" name="name">
                        <div class="form-control-icon">
                            <i class="bi bi-tag"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <input type="submit" name="submit" class="btn btn-primary me-1 mb-1" value="Submit">
                </div>
            </form>
        </div>
    </div>

<?php
require_once("includes/widget.php");
require_once("includes/footer.php");
ob_end_flush();
?>

