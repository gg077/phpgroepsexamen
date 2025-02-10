<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top.php");

if (empty($_GET['id'])) {
    header("location: blogs.php"); // Redirect als er geen blog ID is opgegeven
    exit();
}

$_SESSION['creating_blog']=true;

// Haal het blogbericht op
$blog = Blog::find_by_id($_GET['id']);
$photos = Photo::find_photos_by_blog($blog->id);

if (!$blog) {
    header("location: blogs.php"); // Redirect als het blog niet bestaat
    exit();
}

// Haal de huidige fotos op die aan deze blog gekoppeld zijn
$current_photos = Blog::get_photos($blog->id);
var_dump($current_photos);

// Haal de huidige categorieën op die aan deze blog gekoppeld zijn
$current_categories = Blog::get_categories($blog->id) ?? [];

// Haal alle beschikbare categorieën op
$all_categories = Category::find_all();

// Zet de huidige categorie-ID's in een array (voorkom fouten als er geen categorieën zijn)
$selected_category_ids = !empty($current_categories) ? array_column($current_categories, 'id') : [];


//// Controleer of er een melding in de sessie staat
$the_message = "";
if (isset($_SESSION['the_message'])) {
    $the_message = $_SESSION['the_message'];
    unset($_SESSION['the_message']); // Verwijder de melding na ophalen
}

if (isset($_POST['updateblog'])) {
    if ($blog) {
        $blog->title = trim($_POST['title']);
        $blog->description = trim($_POST['description']);

        // Controleer of er een nieuwe foto is geüpload
        if (!empty($_FILES['photo']['name'])) {
            $photo = $blog->photo_id ? Photo::find_by_id($blog->photo_id) : null;

            // Verwijder de oude afbeelding
            if ($photo) {
                $photo->update_photo();
            }

            // Voeg een nieuwe afbeelding toe
            if ($photo) {
                $photo->title = trim($_POST['title']);
                $photo->description = trim($_POST['description']);
                $photo->set_file($_FILES['photo']);
                $photo->save();
            } else {
                $photo = new Photo();
                $photo->title = trim($_POST['title']);
                $photo->description = trim($_POST['description']);
                $photo->set_file($_FILES['photo']);
                $photo->save();
            }

            // Update de foto ID in de blog
            global $database;
            $blog->photo_id = $database->get_last_insert_id();
        }

        // **Update de blogpost**
        if ($blog->save() == false) {
            // **Update de categorieën in de tussentabel**
            if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
                $blog->save_categories($_POST['categories']); // Oproepen van de verbeterde functie
            }
            if(!empty($_SESSION['photo_ids']) && is_array($_SESSION['photo_ids'])){
                foreach ($photos as $photo) {
                    array_push($_SESSION['photo_ids'],$photo->id);
                }
                $blog->save_photos($_SESSION['photo_ids']);
                unset($_SESSION['photo_ids']);
            }

            $_SESSION['the_message'] = "Blogpost en categorieën succesvol bijgewerkt.";
        } else {
            $_SESSION['the_message'] = "Er is een fout opgetreden bij het updaten.";
        }

        unset($_SESSION['creating_blog']);

        header("location: blogs.php");
        exit();
    }
}
?>

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
                                           value="<?php echo htmlspecialchars($blog->title); ?>">
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
                                           value="<?php echo htmlspecialchars($blog->description); ?>">
                                    <div class="form-control-icon">
                                        <i class="bi bi-card-text"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="categories">Categorieën:</label>
                                <select multiple name="categories[]" id="categories" class="form-control">
                                    <option value="" disabled>Selecteer categorieën (Ctrl+click)</option>

                                    <?php foreach ($all_categories as $category) : ?>
                                        <option value="<?= $category->id; ?>" <?= in_array($category->id, $selected_category_ids) ? 'selected' : ''; ?>>
                                            <?= $category->name; ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <label>Current Photo:</label>
<!--                            <div>-->
<!--                                --><?php //if ($photo): ?>
<!--                                    <img src="--><?php //echo $photo->picture_path(); ?><!--" alt="Blog Image" width="150">-->
<!--                                --><?php //else: ?>
<!--                                    <p>No photo uploaded.</p>-->
<!--                                --><?php //endif; ?>
<!--                            </div>-->
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <?php if (!empty($current_photos)): ?>
                                    <?php foreach ($current_photos as $photo): ?>
                                        <div class="position-relative">
                                            <img src="assets/images/photos/<?php echo $photo['filename']; ?>" alt="Blog
                                            Image" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                            <a href="delete_blogphoto.php?photo_id=<?php echo $photo['id'];
                                            ?>&blog_id=<?php echo $blog->id; ?>"
                                               class="position-absolute top-0 end-0 bg-danger text-white rounded-circle p-1"
                                               onclick="return confirm('Are you sure you want to delete this photo?');">
                                                <i class="bi bi-x"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No photos uploaded.</p>
                                <?php endif; ?>
                            </div>
                        </div>
<!--                        <div class="col-12">-->
<!--                            <div class="form-group has-icon-left">-->
<!--                                <label for="photo">Upload New Photo (optional)</label>-->
<!--                                <div class="position-relative">-->
<!--                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">-->
<!--                                    <div class="form-control-icon">-->
<!--                                        <i class="bi bi-cloud-upload"></i>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="col-12 d-flex justify-content-end">
                            <input type="submit" name="updateblog" class="btn btn-primary me-1 mb-1" value="Update">
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-12">
                <div class="form-group">
                    <label>Upload New Photos:</label>
                    <form action="includes/upload_handler.php" class="dropzone rounded border-light-subtle" id="photoUpload">
                        <div class="dz-message"><i class="bi bi-upload me-3"></i>Drag and drop files here or click to upload</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    Dropzone.options.photoUpload = {
        paramName: "file",
        maxFilesize: 2,
        acceptedFiles: "image/jpeg,image/png,image/gif",
        dictDefaultMessage: "Drop files here or click to upload",
        init: function() {
            this.on("sending", function(file, xhr, formData) {
            });

            this.on("success", function(file, response) {
                console.log("File uploaded successfully:", response);
                // alert("File uploaded successfully!");
            });

            this.on("error", function(file, response) {
                console.log("Upload error:", response);
                alert("Upload failed: " + response);
            });
        }
    };
</script>

<?php
require_once("includes/widget.php");
require_once("includes/footer.php");
?>
