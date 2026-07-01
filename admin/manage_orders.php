<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('admin');
$pageTitle = 'Manage Orders';

$successMsg = $_SESSION['order_assign_success'] ?? '';
unset($_SESSION['order_assign_success']);

// Fetch all riders for the assignment dropdown
$riders = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'rider' ORDER BY full_name");
$ridersList = [];
while ($r = $riders->fetch_assoc()) { $ridersList[] = $r; }

// Fetch all orders with customer + rider names
$orders = $conn->query("
    SELECT o.*, c.full_name AS customer_name, r.full_name AS rider_name
    FROM orders o
    JOIN users c ON o.customer_id = c.user_id
    LEFT JOIN users r ON o.rider_id = r.user_id
    ORDER BY
        CASE o.status WHEN 'Pending' THEN 0 WHEN 'Assigned' THEN 1 WHEN 'Delivered' THEN 2 END,
        o.created_at DESC
");

include '../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Manage Orders</h1>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Rider</th>
                    <th>Proof</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($o = $orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $o['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                        <td>৳<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td><span class="status status-<?php echo $o['status']; ?>"><?php echo htmlspecialchars($o['status']); ?></span></td>
                        <td><?php echo $o['rider_name'] ? htmlspecialchars($o['rider_name']) : '<span class="text-muted">—</span>'; ?></td>
                        <td>
                            <?php if ($o['proof_photo']): ?>
                                <a href="../uploads/proofs/<?php echo htmlspecialchars($o['proof_photo']); ?>" target="_blank">View</a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($o['status'] === 'Pending'): ?>
                                <form action="assign_rider.php" method="POST" class="flex" style="gap:6px;">
                                    <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                                    <select name="rider_id" required>
                                        <option value="">Select Rider</option>
                                        <?php foreach ($ridersList as $r): ?>
                                            <option value="<?php echo $r['user_id']; ?>"><?php echo htmlspecialchars($r['full_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">No action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
