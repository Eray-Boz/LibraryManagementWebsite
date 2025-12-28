<?php
include 'includes/header.php';

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$whereClause = "";
$params = [];
$paramTypes = "";

$baseQuery = "SELECT loans.id, loans.borrow_date, loans.return_date, loans.status, 
              members.full_name as member_name, 
              books.title as book_title 
              FROM loans 
              JOIN members ON loans.member_id = members.id 
              JOIN books ON loans.book_id = books.id";

if ($search) {
    $whereClause = " WHERE members.full_name LIKE ? OR books.title LIKE ?";
    $searchWildcard = "%$search%";
    $params = [$searchWildcard, $searchWildcard];
    $paramTypes = "ss";
}

$countQuery = "SELECT COUNT(*) as count FROM loans 
               JOIN members ON loans.member_id = members.id 
               JOIN books ON loans.book_id = books.id" . $whereClause;
$stmt = $conn->prepare($countQuery);
if ($search) $stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['count'];
$total_pages = ceil($total_records / $limit);

$query = $baseQuery . $whereClause . " ORDER BY loans.status ASC, loans.borrow_date DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
if ($search) {
    $paramTypes .= "ii";
    $params[] = $start;
    $params[] = $limit;
    $stmt->bind_param($paramTypes, ...$params);
} else {
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$loans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="glass fade-in p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Loan Management</h2>
        <a href="borrow_book.php" class="btn btn-primary">+ Borrow Book</a>
    </div>

    <form method="GET" action="loans.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by member or book..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
            <?php if($search): ?>
                <a href="loans.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($loans) > 0): ?>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($loan['member_name']); ?></td>
                            <td><?php echo htmlspecialchars($loan['book_title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($loan['borrow_date'])); ?></td>
                            <td>
                                <?php echo $loan['return_date'] ? date('M d, Y', strtotime($loan['return_date'])) : '-'; ?>
                            </td>
                            <td>
                                <?php if ($loan['status'] == 'borrowed'): ?>
                                    <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">Borrowed</span>
                                <?php else: ?>
                                    <span style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">Returned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($loan['status'] == 'borrowed'): ?>
                                    <a href="return_book.php?id=<?php echo $loan['id']; ?>" class="action-btn btn-edit" onclick="return confirm('Mark this book as returned?');">Return</a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-3">No loans found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
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
