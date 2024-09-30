<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bk') {
    header("Location: login.php");
    exit();
}
?>