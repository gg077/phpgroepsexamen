<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");

if (!$session->is_signed_in()) {
    header("location:login.php");
    exit();
}

if (empty($_GET['id'])) {
    header("Location: photos.php");
    exit();
}

$photo = Photo::find_by_id($_GET['id']);
$message = "";

// Verwerk update van tekstvelden en originele afbeelding
if (isset($_POST['update'])) {
    $photo->title = $_POST['title'];
    $photo->description = $_POST['description'];
    $photo->alternate_text = $_POST['alternate_text'];

    if (!empty($_FILES['file']['name'])) {
        if ($photo->set_file($_FILES['file'])) {
            $photo->save();
        } else {
            $message = "Fout bij uploaden bestand!";
        }
    } else {
        $photo->save();
    }

    $message = "Foto succesvol bijgewerkt!";
}

// Verwerk gecropte afbeelding en sla deze als aparte afbeelding op
if (!empty($_POST['cropped_image'])) {
    $cropped_image = $_POST['cropped_image'];
    list(, $cropped_image) = explode(',', $cropped_image);
    $cropped_image = base64_decode($cropped_image);

    // Juiste opslagmap
    $uploadDir = realpath(__DIR__ . '/../admin/assets/images/photos/') . DIRECTORY_SEPARATOR;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!is_writable($uploadDir)) {
        die("Kan niet schrijven naar map: " . $uploadDir);
    }

    // Genereer een nieuwe naam voor de gecropte afbeelding
    $croppedFileName = 'cropped_' . time() . '.png';
    $croppedFilePath = $uploadDir . $croppedFileName;

    // Sla gecropte afbeelding op de server op
    if (file_put_contents($croppedFilePath, $cropped_image)) {
        // Sla gecropte afbeelding op in de database als een aparte record
        $cropped_photo = new Photo();
        $cropped_photo->title = $photo->title . " (Cropped)";
        $cropped_photo->description = $photo->description;
        $cropped_photo->alternate_text = $photo->alternate_text;
        $cropped_photo->filename = $croppedFileName;
        $cropped_photo->size = filesize($croppedFilePath);
        $cropped_photo->type = mime_content_type($croppedFilePath);
        $cropped_photo->save();

        // Toon de gecropte afbeelding in plaats van de originele
        $photo->filename = $croppedFileName;
        $message = "Gecropte afbeelding succesvol opgeslagen en weergegeven!";
    } else {
        die("Fout bij opslaan bestand: " . error_get_last()['message']);
    }
}
?>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Edit Photo</h4>
        </div>
        <div class="card-content">
            <div class="card-body row">
                <!-- Linkerkant: Formulier -->
                <form class="form form-vertical col-6" action="edit_photo.php?id=<?= $photo->id; ?>" method="post" enctype="multipart/form-data" id="editForm">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-info"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <div class="form-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" id="title" class="form-control" name="title" value="<?= $photo->title; ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="5" required><?= trim($photo->description) ?></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="alternate_text">Alternate Text</label>
                                    <input type="text" class="form-control" name="alternate_text" id="alternate_text" value="<?= $photo->alternate_text; ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="file" class="form-label">Choose new photo</label>
                                    <input class="form-control" type="file" id="file" name="file" accept="image/*">
                                </div>
                            </div>



                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" id="cropButton" class="btn btn-warning me-1 mb-1">Crop Image</button>
                                <button name="update" type="submit" class="btn btn-primary me-1 mb-1">Update</button>
                            </div>

                            <input type="hidden" id="croppedImageData" name="cropped_image">
                        </div>
                    </div>
                </form>

                <!-- Rechterkant: Huidige foto en details -->
                <div class="col-6">
                    <div class="shadow-sm">
                        <!-- Cropper.js Preview -->
                        <div class="col-12 text-center">
                            <img id="imagePreview" src="<?php echo $photo->picture_path(); ?>" style="max-width: 100%;">
                        </div>
                    </div>
                    <div class="mt-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-calendar"></i> <strong>Uploaded on:</strong> <?php echo $photo->created_at; ?>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-file"></i> <strong>Filename:</strong> <?php echo $photo->filename; ?>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-file-image"></i> <strong>File type:</strong> <?php echo $photo->type; ?>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-hdd"></i> <strong>File size:</strong> <?php echo round(($photo->size) / 1024, 2); ?> Kb
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        let cropper;
        document.getElementById('cropButton').addEventListener('click', function () {
            const image = document.getElementById('imagePreview');
            if (cropper) cropper.destroy();
            cropper = new Cropper(image, { aspectRatio: 1, viewMode: 2, autoCropArea: 0.8 });

            alert('Je kan nu de afbeelding croppen. Klik op "Update" om op te slaan.');
        });

        document.getElementById('editForm').addEventListener('submit', function () {
            if (cropper) {
                document.getElementById('croppedImageData').value = cropper.getCroppedCanvas().toDataURL('image/png');
            }
        });
    </script>

<?php
require_once("includes/footer.php");
?>