<?php
require_once("includes/header.php");
if(!$session->is_signed_in()){
    header("location:login.php");
}
if(empty($_GET['id'])){
    header("location:login.php");
}else{
    $photo = Photo::find_by_id($_GET['id']);
    $photo->soft_delete();
    header("location:photos.php");
}
require_once("includes/footer.php");
?>