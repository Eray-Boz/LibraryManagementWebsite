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
    $whereClause = "WHERE full_name LIKE ? OR email LIKE ?";
    $searchWildcard = "%$search%";
    $params = [$searchWildcard, $searchWildcard];
    $paramTypes = "ss";
}

$sqlCount = "SELECT COUNT(*) as count FROM members $whereClause";
$stmt = $conn->prepare($sqlCount);
if ($search) $stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['count'];
$total_pages = ceil($total_records / $limit);

$sql = "SELECT * FROM members $whereClause ORDER BY joined_at DESC LIMIT ?, ?";
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
$members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="glass fade-in p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Member Management</h2>
        <a href="add_member.php" class="btn btn-primary">+ Register Member</a>
    </div>

    <form method="GET" action="members.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
            <?php if($search): ?>
                <a href="members.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Joined At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($members) > 0): ?>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td><?php echo htmlspecialchars($member['phone']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($member['joined_at'])); ?></td>
                            <td>
                                <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="action-btn btn-edit">Edit</a>
                                <a href="delete_member.php?id=<?php echo $member['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this member?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-3">No members found.</td></tr>
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
