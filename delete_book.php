<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

redirect('books.php');
?>
