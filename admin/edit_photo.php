<?php
// De benodigde bestanden worden ingeladen, zoals de header, sidebar en content-top.
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");

// Er wordt gecontroleerd of de gebruiker is ingelogd. Als dit niet het geval is, wordt de gebruiker doorgestuurd naar de loginpagina.
if (!$session->is_signed_in()) {
    header("location:login.php");
}
// Er wordt gecontroleerd of er een ID is meegegeven in de URL. Als dit ontbreekt, wordt de gebruiker doorgestuurd naar de overzichtspagina.
if (empty($_GET['id'])) {
    header("Location:photos.php");
} else {
    // De foto wordt opgehaald uit de database op basis van het ID dat in de URL is meegegeven.
    $photo = Photo::find_by_id($_GET['id']);

    // Als het formulier is verzonden, wordt de onderstaande logica uitgevoerd om de foto bij te werken.
    if (isset($_POST['update'])) {
        // Er wordt gecontroleerd of het foto-object bestaat.
        if ($photo) {
            // De titel, beschrijving en alternatieve tekst worden bijgewerkt met de waarden uit het formulier.
            $photo->title = $_POST['title'];
            $photo->description = $_POST['description'];
            $photo->alternate_text = $_POST['alternate_text'];

            // Er wordt gecontroleerd of er een nieuw bestand is geüpload via het formulier.
            if (!empty($_FILES['file']['name'])) {
                // De oude afbeelding wordt fysiek verwijderd van de server.
                $photo->update_photo();

                // Het nieuwe bestand wordt verwerkt en gekoppeld aan het foto-object.
                if ($photo->set_file($_FILES['file'])) {
                    // Het nieuwe bestand wordt opgeslagen op de server en de database wordt bijgewerkt.
                    $photo->save();
                } else {
                    // Eventuele foutmeldingen worden toegevoegd als er iets misgaat.
                    $photo->errors = array_merge($photo->errors, $photo->errors);
                }
            } else {
                // Als er geen nieuw bestand is geüpload, worden alleen de tekstvelden in de database bijgewerkt.
                $photo->save();
            }
        }
    }
}
?>
	<div class="card">
	<div class="card-header">
		<h4 class="card-title">Edit photo</h4>
	</div>
	<div class="card-content">
		<div class="card-body row">

			<form class="form form-vertical col-6" action="edit_photo.php?id=<?= $photo->id; ?>" method="post" enctype="multipart/form-data">
				<div class="form-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="title">Title</label>
								<input type="text" id="title" class="form-control"
								       name="title" placeholder="title" value="<?= $photo->title; ?>">
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea  style="text-align:left;" class="form-control" name="description" id="description" rows="5" cols="100%" placeholder="description"><?= trim($photo->description) ?></textarea>
								</textarea>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="alternate_text">Alternate Text</label>
								<input type="text" class="form-control" name="alternate_text" id="alternate_text" rows="5" cols="100%" placeholder="alternate text" value="<?= $photo->alternate_text; ?>"></input>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="file" class="form-label">Choose new photo</label>
								<input class="form-control" type="file" id="file" name="file">
							</div>
						</div>
						<div class="col-12 d-flex justify-content-end">
							<button name="update" type="submit" class="btn btn-primary me-1 mb-1">Upload</button>
						</div>
					</div>
				</div>
			</form>
			<div class="col-6">
				<div class="shadow-sm">
					<img src="<?php echo $photo->picture_path(); ?>" alt="<?php echo $photo->title; ?>" class="card-img-top img-fluid img-thumbnail">
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
								<i class="bi bi-hdd"></i> <strong>File size:</strong> <?php echo ($photo->size) / 1024; ?> Kb
							</li>
						</ul>
					</div>

			</div>

		</div>
	</div>
</div>

<?php
require_once("includes/footer.php");
?>