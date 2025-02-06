<?php
require_once "../includes/init.php";
require_once ("../includes/Photo.php");

if (!$session->is_signed_in()) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $photo = new Photo();
        $photo->set_file($_FILES['file']);

        if ($photo->save()) {
            echo json_encode(["success" => "File uploaded successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to save photo to database. Errors: " . implode("<br>", $photo->errors)]);
        }
    } else {
        echo json_encode(["error" => "No file uploaded"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>