<?php
session_start();

// If user is logged in, redirect to forum
if (isset($_SESSION['user_id'])) {
    header("Location: forum.php");
    exit();
}

// Else, show the welcome page
include 'welcome.php';
