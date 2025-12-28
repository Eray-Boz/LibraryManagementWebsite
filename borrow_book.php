<?php
include 'includes/header.php';

$error = '';

$membersResult = $conn->query("SELECT id, full_name, email FROM members ORDER BY full_name ASC");
$members = $membersResult->fetch_all(MYSQLI_ASSOC);

$booksResult = $conn->query("
    SELECT id, title, author 
    FROM books 
    WHERE id NOT IN (
        SELECT book_id FROM loans WHERE status = 'borrowed'
    )
    ORDER BY title ASC
");
$books = $booksResult->fetch_all(MYSQLI_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)$_POST['member_id'];
    $book_id = (int)$_POST['book_id'];
    $borrow_date = date('Y-m-d H:i:s');

    if (!$member_id || !$book_id) {
        $error = "Please select both a member and a book.";
    } else {
        $check = $conn->query("SELECT id FROM loans WHERE book_id = $book_id AND status = 'borrowed'");
        if ($check->num_rows > 0) {
            $error = "This book is currently unavailable.";
        } else {
            $stmt = $conn->prepare("INSERT INTO loans (member_id, book_id, borrow_date, status) VALUES (?, ?, ?, 'borrowed')");
            $stmt->bind_param("iis", $member_id, $book_id, $borrow_date);
            
            if ($stmt->execute()) {
                redirect('loans.php');
            } else {
                $error = "Error creating loan: " . $stmt->error;
            }
        }
    }
}
?>

<div class="glass fade-in p-5 mx-auto" style="max-width: 600px;">
    <h2 class="text-center mb-4">Borrow a Book</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Select Member</label>
            <select name="member_id" class="form-select" required>
                <option value="">-- Choose Member --</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?php echo $m['id']; ?>">
                        <?php echo htmlspecialchars($m['full_name']) . " (" . htmlspecialchars($m['email']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Select Book</label>
            <select name="book_id" class="form-select" required>
                <option value="">-- Choose Book --</option>
                <?php foreach ($books as $b): ?>
                    <option value="<?php echo $b['id']; ?>">
                        <?php echo htmlspecialchars($b['title']) . " by " . htmlspecialchars($b['author']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (count($books) == 0): ?>
                <div class="form-text text-danger">No books available for borrowing.</div>
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1" <?php echo count($books) == 0 ? 'disabled' : ''; ?>>Confirm Loan</button>
            <a href="loans.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
