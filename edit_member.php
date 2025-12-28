<?php
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$member = null;
$error = '';

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();
}

if (!$member) {
    redirect('members.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);

    if (empty($full_name)) {
        $error = "Full Name is required.";
    } else {
        $stmt = $conn->prepare("UPDATE members SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $email, $phone, $id);
        
        if ($stmt->execute()) {
            redirect('members.php');
        } else {
            $error = "Error updating member: " . $stmt->error;
        }
    }
}
?>

<div class="glass fade-in p-5 mx-auto" style="max-width: 600px;">
    <h2 class="text-center mb-4">Edit Member</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($member['full_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($member['email']); ?>">
        </div>
        <div class="mb-4">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($member['phone']); ?>">
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Update Member</button>
            <a href="members.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
