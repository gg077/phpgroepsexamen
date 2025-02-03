<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");
if(!$session->is_signed_in()){
	header("location:login.php");
}
$message = "";
$photo = new Photo();

//hier gebeurd het opladen fysisch van de photo naar de server
if(isset($_POST['submit'])){
	$photo->title = $_POST['title'];
	$photo->description = $_POST['description'];
    $photo->alternate_text = $_POST['alternate_text'];
	$photo->set_file($_FILES['file']);
}
//hier gebeurd het wegschrijven van de data, (link) naar de database
if($photo->save()){
	$message ="Foto succesvol opgeladen!";
}else{
	$message = join("<br>",$photo->errors);
}

?>
<div class=col-md-6 col-12">
<div class="card">
	<div class="card-header">
		<h4 class="card-title">Upload photo</h4>
	</div>
	<div class="card-content">
		<div class="card-body">
			<form class="form form-vertical" action="upload.php" method="post" enctype="multipart/form-data">
				<div class="form-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label for="title">Title</label>
								<input type="text" id="title" class="form-control"
								       name="title" placeholder="title">
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" id="description" rows="5" cols="100%" placeholder="description"></textarea>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="alternate_text">Alternate Text</label>
								<input type="text" class="form-control" name="alternate_text" id="alternate_text" rows="5" cols="100%" placeholder="alternate text"></input>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<label for="file" class="form-label">Choose photo</label>
								<input class="form-control" type="file" id="file" name="file">
							</div>
						</div>
						<div class="col-12 d-flex justify-content-end">
							<button name="submit" type="submit" class="btn btn-primary me-1 mb-1">Upload</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</div>


<?php
require_once("includes/footer.php");
?>
