<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();


$current_page = basename($_SERVER['PHP_SELF'], ".php");
$page_titles = [
    'dashboard' => 'Dashboard',
    'books' => 'Books',
    'add_book' => 'Add New Book',
    'edit_book' => 'Edit Book',
    'members' => 'Members',
    'add_member' => 'Register Member',
    'edit_member' => 'Edit Member',
    'loans' => 'Loans',
    'borrow_book' => 'Borrow Book'
];
$title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'LibraryPro';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - LibraryPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark glass mb-4">
        <div class="container-fluid">
            <a href="dashboard.php" class="navbar-brand fw-bold d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-book me-2" viewBox="0 0 16 16">
                    <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                </svg>
                LibraryPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                    <a href="books.php" class="nav-link <?php echo $current_page == 'books' ? 'active' : ''; ?>">Books</a>
                    <a href="members.php" class="nav-link <?php echo $current_page == 'members' ? 'active' : ''; ?>">Members</a>
                    <a href="loans.php" class="nav-link <?php echo $current_page == 'loans' ? 'active' : ''; ?>">Loans</a>
                    <a href="logout.php" class="nav-link text-danger">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container main-content">
        <div class="breadcrumbs">
            <a href="dashboard.php">Home</a> 
            <?php if ($current_page != 'dashboard'): ?>
                <span>/</span> 
                <span style="color: var(--text-color);"><?php echo $title; ?></span>
            <?php endif; ?>
        </div>
