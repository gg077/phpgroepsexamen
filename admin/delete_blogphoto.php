<?php
require_once("includes/header.php");
if(!$session->is_signed_in()){
    header("location:login.php");
}
if (!empty($_GET['photo_id']) && !empty($_GET['blog_id'])) {
    global $database;

    $photo_id = $_GET['photo_id'];
    $blog_id = $_GET['blog_id'];

    // First remove the association in blogs_photos
    $sql = "DELETE FROM blogs_photos WHERE blog_id = ? AND photo_id = ?";
    $database->query($sql, [$blog_id, $photo_id]);

    // Then delete the photo if it's not used by other blogs
    $sql = "SELECT COUNT(*) as count FROM blogs_photos WHERE photo_id = ?";
    $result = $database->query($sql, [$photo_id]);
    $count = $result->fetch_assoc()['count'];

    if ($count == 0) {
        // Photo is not used anywhere else, safe to delete
        $photo = Photo::find_by_id($photo_id);
        if ($photo) {
            $photo->delete();
        }
    }
}

// Redirect back to edit page
header("Location: edit_blog.php?id=" . $_GET['blog_id']);
exit();