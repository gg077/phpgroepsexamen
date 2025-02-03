<?php
// Inclusie van de header, sidebar en content-top2 bestanden
// Deze bestanden bevatten de algemene layout en navigatie van de pagina
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");


//Controleer of er een 'delete' parameter in de URL is opgegeven
if (isset($_GET['delete'])) {
    // Haal het blog op basis van het opgegeven ID
    $blog = Blog::find_by_id($_GET['delete']);

    // Controleer of het blog bestaat
    if ($blog) {
        // Voer een soft delete uit (verander de 'deleted_at' waarde in de database)
        $blog->soft_delete();

        // Stuur de gebruiker terug naar de blogs pagina
        header("location:blogs.php");
        exit;
    } else {
        // Toon een waarschuwing als het blog niet gevonden is
        echo "<script>alert('Blog niet gevonden')</script>";
    }
}
?>

<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                Blogs
            </h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-tab" data-bs-toggle="tab"
                            data-bs-target="#active-tab-pane" type="button" role="tab" aria-controls="active-tab-pane"
                            aria-selected="true">Active Blogs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive-tab-pane"
                            type="button" role="tab" aria-controls="inactive-tab-pane" aria-selected="false">Inactive
                        Blogs
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="active-tab-pane" role="tabpanel"
                     aria-labelledby="active-tab">
                    <table class="table table-striped" id="table1">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>author_id</th>
                            <th>title</th>
                            <th>description</th>
                            <th>Categories</th>
                            <th>Created_at</th>
                            <th>Deleted at</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Haal alle blogs op uit de database
                        $active_blogs = Blog::find_all();
                        ?>
                        <?php foreach ($active_blogs as $blog): ?>
                            <?php

                            // Haal de bijbehorende auteur en foto op
                            $author = User::find_by_id($blog->author_id);
                            $photo = Photo::find_by_id($blog->photo_id);

                            // Controleer of de blog actief is (niet verwijderd)
                            if ($blog->deleted_at == '0000-00-00 00:00:00'):
                                ?>
                                <tr>
                                    <td><?= $blog->id; ?></td>
                                    <td><span>
        <img height="40" width="40" class="avatar me-3"
             src="<?php echo $blog->photo_id != 0 ? $photo->picture_path() : 'https://placehold.co/40x40'; ?>"
             alt="">
    </span><?= $author->username; ?></td>
                                    <td><?= $blog->title; ?></td>
                                    <td><?= $blog->description; ?></td>
                                    <td>
                                        <?php $categories = Blog::get_categories($blog->id); ?>

                                        <?php if (!empty($categories)) : ?>
                                            <?php foreach ($categories as $category) : ?>
                                                <span class="badge bg-primary me-1">
                <i class="bi bi-tag-fill"></i> <?= $category['name']; ?>
            </span><br>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <span class="text-muted">Geen categorieën</span>
                                        <?php endif; ?>
                                    </td>


                                    <td><?= $blog->created_at; ?></td>
                                    <td><?= $blog->deleted_at; ?></td>
                                    <td class="d-flex justify-content-around">
                                        <!-- Verwijderknop -->
                                        <a href="blogs.php?delete=<?= $blog->id; ?>"
                                           onclick="return confirm('Weet je zeker dat je deze gebruiker wil verwijderen?')">
                                            <i class="bi bi-trash text-danger"></i>
                                        </a>
                                        <!-- Bewerken knop -->
                                        <a href="edit_blog.php?id=<?= $blog->id; ?>">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="inactive-tab-pane" role="tabpanel" aria-labelledby="inactive-tab">
                    <table class="table table-striped" id="table2">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>author_id</th>
                            <th>title</th>
                            <th>description</th>
                            <th>Created_at</th>
                            <th>Deleted at</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Haal opnieuw alle blogs op
                        $inactive_blogs = Blog::find_all();
                        ?>
                        <?php foreach ($inactive_blogs as $blog): ?>
                            <?php

                            // Haal auteur en foto op
                            $author = User::find_by_id($blog->author_id);
                            $photo = Photo::find_by_id($blog->photo_id);

                            // Controleer of het blog verwijderd is
                            if ($blog->deleted_at !== '0000-00-00 00:00:00'):
                                ?>
                                <tr>
                                    <td><?= $blog->id; ?></td>
                                    <td> <span>
        <img height="40" width="40" class="avatar me-3"
             src="<?php echo $blog->photo_id != 0 ? $photo->picture_path() : 'https://placehold.co/40x40'; ?>"
             alt="">
    </span><?= $author->username; ?></td>
                                    <td><?= $blog->title; ?></td>
                                    <td><?= $blog->description; ?></td>
                                    <td><?= $blog->created_at; ?></td>
                                    <td><?= $blog->deleted_at; ?></td>
                                    <td class="d-flex justify-content-around">
                                        <!-- Herstelknop -->
                                        <a href="blogs.php?restore=<?= $blog->id; ?>"
                                           onclick="return confirm('Weet je zeker dat je deze gebruiker wil herstellen?')">
                                            <i class="bi bi-bootstrap-reboot text-warning"></i>
                                        </a>
                                        <!-- Bewerken knop -->
                                        <a href="edit_blog.php?id=<?= $blog->id; ?>">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
require_once("includes/footer.php"); ?>

