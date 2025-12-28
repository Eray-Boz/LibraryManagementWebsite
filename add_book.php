<?php
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $genre = sanitize($_POST['genre']);
    $isbn = sanitize($_POST['isbn']);
    $published_year = (int) $_POST['published_year'];

    if (empty($title) || empty($author)) {
        $error = "Title and Author are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO books (title, author, genre, isbn, published_year) VALUES (?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssi", $title, $author, $genre, $isbn, $published_year);
        
        if ($stmt->execute()) {
            redirect('books.php');
        } else {
            $error = "Error adding book: " . $stmt->error;
        }
    }
}
?>

<div class="glass fade-in" style="padding: 3rem; max-width: 600px; margin: 0 auto;">
    <h2 style="margin-bottom: 2rem; text-align: center;">Add New Book</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Book Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Author</label>
            <input type="text" name="author" class="form-control" required>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" class="form-control">
            </div>
            <div class="form-group">
                <label>Published Year</label>
                <input type="number" name="published_year" class="form-control" min="1000" max="<?php echo date('Y'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label>ISBN</label>
            <input type="text" name="isbn" class="form-control">
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 2rem;">
            <button type="submit" class="btn-primary">Save Book</button>
            <a href="books.php" class="btn-primary" style="background: var(--text-muted); text-align: center;">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
