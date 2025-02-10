<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");
require_once('includes/image_helpers.php');
?>
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Our Gallery</h5>
                </div>
                <div class="card-body">
                    <div class="row gallery">
	                    <?php $photos = Photo::find_all(); ?>
                        <?php foreach ($photos as $photo): ?>
		                    <div class="col-6 col-sm-6 col-lg-3 p-2">
			                    <div class="position-relative" style="padding-top: 100%;">
                                    <a href="<?= $photo->picture_path(/*'large'*/); ?>" data-lightbox="gallery">
<!--                                        <picture>-->
<!--                                            <source media="(min-width: 1200px)" srcset="--><?php //= $photo->picture_path('medium'); ?><!--">-->
<!--                                            <source media="(min-width: 768px)" srcset="--><?php //= $photo->picture_path('medium'); ?><!--">-->
<!--                                            <source media="(min-width: 480px)" srcset="--><?php //= $photo->picture_path('thumbnail'); ?><!--">-->
                                            <img alt="<?= $photo->alternate_text; ?>"
                                                 class="position-absolute top-0 start-0 w-100 h-100 img-thumbnail"
                                                 style="object-fit: cover;"
                                                 src="<?= $photo->picture_path('thumbnail'); ?>">
<!--                                        </picture>-->
                                    </a>
				                    <div class="d-flex position-absolute top-0 end-0 mt-2 me-2">
					                    <a class="btn btn-danger btn-sm me-1" href="delete_photo.php?id=<?= $photo->id; ?>">
						                    <i class="bi bi-trash"></i>
					                    </a>
					                    <a class="btn btn-primary btn-sm" href="edit_photo.php?id=<?= $photo->id; ?>">
						                    <i class="bi bi-eye"></i>
					                    </a>
				                    </div>
			                    </div>
		                    </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
require_once("includes/footer.php");
?>