<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top.php");

if (empty($_GET['id'])) {
    header("location: blogs.php"); // Redirect als er geen blog ID is opgegeven
    exit();
}

// Haal het blogbericht op
$blog = Blog::find_by_id($_GET['id']);

if (!$blog) {
    header("location: blogs.php"); // Redirect als het blog niet bestaat
    exit();
}

// Haal de huidige categorieën op die aan deze blog gekoppeld zijn
$current_categories = Blog::get_categories($blog->id) ?? [];

// Haal alle beschikbare categorieën op
$all_categories = Category::find_all();

// Zet de huidige categorie-ID's in een array (voorkom fouten als er geen categorieën zijn)
$selected_category_ids = !empty($current_categories) ? array_column($current_categories, 'id') : [];

// Controleer of er een melding in de sessie staat
$the_message = "";
if (isset($_SESSION['the_message'])) {
    $the_message = $_SESSION['the_message'];
    unset($_SESSION['the_message']); // Verwijder de melding na ophalen
}

$photo = $blog->photo_id ? Photo::find_by_id($blog->photo_id) : null;

if (isset($_POST['updateblog'])) {
    if ($blog) {
        $blog->title = trim($_POST['title']);
        $blog->description = trim($_POST['description']);

        // Controleer of er een nieuwe foto is geüpload
        if (!empty($_FILES['photo']['name'])) {
            // Haal de bestaande foto op
            $photo = $blog->photo_id ? Photo::find_by_id($blog->photo_id) : null;

            // Verwijder de bestaande foto uit de images directory
            if ($photo) {
                $photo->update_photo();
            }

            // Werk de bestaande foto bij met de nieuwe foto
            if ($photo) {
                $photo->title = trim($_POST['title']);
                $photo->description = trim($_POST['description']);
                $photo->set_file($_FILES['photo']);
                $photo->save();
            } else {
                // Maak een nieuwe foto aan als er geen bestaande foto is
                $photo = new Photo();
                $photo->title = trim($_POST['title']);
                $photo->description = trim($_POST['description']);
                $photo->set_file($_FILES['photo']);
                $photo->save();
            }
            // Bijwerken van de $blog->photo_id eigenschap
            global $database;
            $blog->photo_id = $database->get_last_insert_id();
        }

        // **Update de blogpost**
        if ($blog->save() == false) {
            // **Categorieën updaten in de blogs_categories tussentabel**
            if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
                $blog->save_categories($_POST['categories']);
            }

            // Zet succesmelding en redirect
            $_SESSION['the_message'] = "Blogpost en categorieën succesvol bijgewerkt.";
        } else {
            $_SESSION['the_message'] = "Er is een fout opgetreden bij het updaten.";
        }

        header("location: blogs.php");
        exit();
    }
}
?>

<!-- Weergave van melding -->
<?php if (!empty($the_message)) : ?>
    <div class="alert alert-success alert-dismissible show fade">
        <?= $the_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Formulier voor het bewerken van een blogpost -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4 class="card-title">Edit Blog</h4>
        <a href="blog.php">
            <i class="bi bi-house text-primary display-6"></i>
        </a>
    </div>
    <div class="card-content">
        <div class="card-body">
            <form class="form form-vertical" method="post" enctype="multipart/form-data">
                <div class="form-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group has-icon-left">
                                <label for="title-icon">Title</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="title-icon" name="title" required
                                           value="<?= $blog->title; ?>">
                                    <div class="form-control-icon">
                                        <i class="bi bi-type"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group has-icon-left">
                                <label for="description-icon">Description</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="description-icon" name="description" required
                                           value="<?= $blog->description; ?>">
                                    <div class="form-control-icon">
                                        <i class="bi bi-card-text"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categorieën selectie -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="categories">Categorieën:</label>
                                <select multiple name="categories[]" id="categories" class="form-control">
                                    <option value="" disabled>Selecteer categorieën (Ctrl+click)</option>
                                    <?php foreach ($all_categories as $category) : ?>
                                        <option value="<?= $category->id; ?>"
                                            <?= in_array($category->id, $selected_category_ids) ? 'selected' : ''; ?>>
                                            <?= $category->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <label>Current Photo:</label>
                            <div>
                                <?php if ($photo) : ?>
                                    <img src="<?= $photo->picture_path(); ?>" alt="Blog Image" width="150">
                                <?php else : ?>
                                    <p>No photo uploaded.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group has-icon-left">
                                <label for="photo">Upload New Photo (optional)</label>
                                <div class="position-relative">
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    <div class="form-control-icon">
                                        <i class="bi bi-cloud-upload"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <input type="submit" name="updateblog" class="btn btn-primary me-1 mb-1" value="Update">
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
