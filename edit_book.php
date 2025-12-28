<?php
include 'includes/header.php';

if (!isset($_GET['id'])) {
    redirect('books.php');
}

$id = (int) $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    redirect('books.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $genre = sanitize($_POST['genre']);
    $isbn = sanitize($_POST['isbn']);
    $published_year = (int) $_POST['published_year'];

    if (empty($title) || empty($author)) {
        $error = "Title and Author are required.";
    } else {
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, genre = ?, isbn = ?, published_year = ? WHERE id = ?");
        
        $stmt->bind_param("ssssii", $title, $author, $genre, $isbn, $published_year, $id);
        
        if ($stmt->execute()) {
            redirect('books.php');
        } else {
            $error = "Error updating book: " . $stmt->error;
        }
    }
}
?>

<div class="glass fade-in" style="padding: 3rem; max-width: 600px; margin: 0 auto;">
    <h2 style="margin-bottom: 2rem; text-align: center;">Edit Book</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Book Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>Author</label>
            <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" class="form-control" value="<?php echo htmlspecialchars($book['genre']); ?>">
            </div>
            <div class="form-group">
                <label>Published Year</label>
                <input type="number" name="published_year" class="form-control" min="1000" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($book['published_year']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label>ISBN</label>
            <input type="text" name="isbn" class="form-control" value="<?php echo htmlspecialchars($book['isbn']); ?>">
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 2rem;">
            <button type="submit" class="btn-primary">Update Book</button>
            <a href="books.php" class="btn-primary" style="background: var(--text-muted); text-align: center;">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
