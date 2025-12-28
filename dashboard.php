<?php
include 'includes/header.php';


$result = $conn->query("SELECT COUNT(*) as count FROM books");
$totalBooks = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(DISTINCT author) as count FROM books");
$totalAuthors = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(DISTINCT genre) as count FROM books");
$totalGenres = $result->fetch_assoc()['count'];

// New Stats
$result = $conn->query("SELECT COUNT(*) as count FROM members");
$totalMembers = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM loans WHERE status = 'borrowed'");
$activeLoans = $result->fetch_assoc()['count'];
?>

<?php include 'includes/banner.php'; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

    <div class="glass fade-in" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; text-transform: uppercase;">Total Books</h3>
        <p style="font-size: 2.5rem; font-weight: 700; color: var(--success);"><?php echo $totalBooks; ?></p>
    </div>
    
    <div class="glass fade-in" style="padding: 1.5rem; text-align: center; animation-delay: 0.1s;">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; text-transform: uppercase;">Members</h3>
        <p style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color);"><?php echo $totalMembers; ?></p>
    </div>

    <div class="glass fade-in" style="padding: 1.5rem; text-align: center; animation-delay: 0.2s;">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; text-transform: uppercase;">Active Loans</h3>
        <p style="font-size: 2.5rem; font-weight: 700; color: var(--secondary-color);"><?php echo $activeLoans; ?></p>
    </div>

    <div class="glass fade-in" style="padding: 1.5rem; text-align: center; animation-delay: 0.3s;">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; text-transform: uppercase;">Authors</h3>
        <p style="font-size: 2.5rem; font-weight: 700; color: var(--text-color);"><?php echo $totalAuthors; ?></p>
    </div>
    
    <div class="glass fade-in" style="padding: 1.5rem; text-align: center; animation-delay: 0.4s;">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; text-transform: uppercase;">Genres</h3>
        <p style="font-size: 2.5rem; font-weight: 700; color: var(--text-muted);"><?php echo $totalGenres; ?></p>
    </div>
</div>

<?php include 'includes/slider.php'; ?>

<?php include 'includes/footer.php'; ?>
