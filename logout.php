<?php
    require_once("includes/header.php");
    $session->logout();
    header("location:index.php");
?>