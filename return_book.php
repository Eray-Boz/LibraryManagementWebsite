<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $return_date = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("UPDATE loans SET status = 'returned', return_date = ? WHERE id = ?");
    $stmt->bind_param("si", $return_date, $id);
    $stmt->execute();
}

redirect('loans.php');
?>
