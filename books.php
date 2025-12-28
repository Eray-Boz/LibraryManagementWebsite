<?php
include 'includes/header.php';


$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$whereClause = "";
$paramTypes = "";
$params = [];

if ($search) {
    $whereClause = "WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
    $searchWildcard = "%$search%";
    $params = [$searchWildcard, $searchWildcard, $searchWildcard];
    $paramTypes = "sss"; 
}


$sqlCount = "SELECT COUNT(*) as count FROM books $whereClause";
$stmt = $conn->prepare($sqlCount);

if ($search) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$total_records = $result->fetch_assoc()['count'];
$total_pages = ceil($total_records / $limit);


$sql = "SELECT * FROM books $whereClause ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if ($search) {
    
    $paramTypes .= "ii"; 
    $params[] = $start;
    $params[] = $limit;
    $stmt->bind_param($paramTypes, ...$params);
} else {
    $stmt->bind_param("ii", $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();


$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
?>

<div class="glass fade-in" style="padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Book Management</h2>
        <a href="add_book.php" class="btn-primary" style="padding: 10px 20px;">+ Add New Book</a>
    </div>

    <form method="GET" action="books.php" style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Search by title, author, or genre..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn-primary" style="width: auto;">Search</button>
            <?php if($search): ?>
                <a href="books.php" class="btn-primary" style="background: var(--text-muted); width: auto; text-align: center; line-height: 20px;">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Published</th>
                    <th>ISBN</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><span style="background: rgba(99, 102, 241, 0.1); color: var(--primary-color); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;"><?php echo htmlspecialchars($book['genre']); ?></span></td>
                            <td><?php echo htmlspecialchars($book['published_year']); ?></td>
                            <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                            <td>
                                <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="action-btn btn-edit">Edit</a>
                                <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 10px;">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>" class="btn-primary" style="width: auto;">&laquo; Prev</a>
            <?php endif; ?>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="btn-primary" style="width: auto; <?php echo $i == $page ? 'background: var(--primary-hover);' : 'background: rgba(255,255,255,0.1);'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>" class="btn-primary" style="width: auto;">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
