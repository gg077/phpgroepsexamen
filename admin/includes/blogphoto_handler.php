<?php
require_once "../includes/init.php";
require_once ("../includes/Photo.php");

if (!$session->is_signed_in()) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0 && isset($_POST['blog_id'])) {
        $photo = new Photo();
        $photo->user_id = $session->user_id;

        if ($photo->set_file($_FILES['file'])) {
            if ($photo->save()) {
                // After saving the photo, create the blog-photo relationship
                global $database;
                $blog_id = $_POST['blog_id'];

                // Insert into blogs_photos table
                if ($photo->associate_with_blog($_POST['blog_id'])) {
                    echo json_encode([
                        "success" => "File uploaded successfully!",
                        "photo_id" => $photo->id,
                        "photo_path" => $photo->picture_path()
                    ]);
                } else {
                    // If association fails, delete the photo
                    $photo->delete();
                    echo json_encode([
                        "error" => "Failed to associate photo with blog"
                    ]);
                }
            } else {
                echo json_encode([
                    "error" => "Failed to save photo to database. Errors: " . implode("<br>", $photo->errors)
                ]);
            }
        } else {
            echo json_encode([
                "error" => "File validation failed: " . implode("<br>", $photo->errors)
            ]);
        }
    } else {
        echo json_encode([
            "error" => "No file uploaded or missing blog ID"
        ]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>