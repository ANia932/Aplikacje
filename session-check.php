<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}
?>