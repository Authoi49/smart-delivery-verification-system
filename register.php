<?php
session_start();
require_once 'includes/db_connect.php';
$base = '';
$pageTitle = 'Register';
$errors = [];
$old = ['full_name' => '', 'email' => '', 'phone' => '', 'role' => 'customer'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['full_name'] = trim($_POST['full_name'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['phone'] = trim($_POST['phone'] ?? '');
    $old['role'] = $_POST['role'] ?? 'customer';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // ---- Server-side validation ----
    if ($old['full_name'] === '') {
        $errors['full_name'] = 'Full name is required.';
    }
    if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'A valid email is required.';
    }
    if ($old['phone'] === '' || !preg_match('/^[0-9+\-\s]{7,20}$/', $old['phone'])) {
        $errors['phone'] = 'A valid phone number is required.';
    }
    if (!in_array($old['role'], ['customer', 'rider', 'admin'])) {
        $errors['role'] = 'Invalid role selected.';
    }
    if (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check duplicate email
    if (empty($errors['email'])) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $old['email']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = 'This email is already registered.';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $old['full_name'], $old['email'], $old['phone'], $hashed, $old['role']);
        if ($stmt->execute()) {
            $_SESSION['register_success'] = 'Account created successfully! Please log in.';
            header("Location: login.php");
            exit();
        } else {
            $errors['general'] = 'Something went wrong. Please try again.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="form-box">
        <h2>Create an Account</h2>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($errors['general']); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" id="registerForm" novalidate>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($old['full_name']); ?>">
                <?php if (!empty($errors['full_name'])): ?><div class="form-error"><?php echo $errors['full_name']; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old['email']); ?>">
                <?php if (!empty($errors['email'])): ?><div class="form-error"><?php echo $errors['email']; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($old['phone']); ?>" placeholder="e.g. 01XXXXXXXXX">
                <?php if (!empty($errors['phone'])): ?><div class="form-error"><?php echo $errors['phone']; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role">Register as</label>
                <select id="role" name="role">
                    <option value="customer" <?php echo $old['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="rider" <?php echo $old['role'] === 'rider' ? 'selected' : ''; ?>>Rider</option>
                    <option value="admin" <?php echo $old['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6">
                <?php if (!empty($errors['password'])): ?><div class="form-error"><?php echo $errors['password']; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="6">
                <?php if (!empty($errors['confirm_password'])): ?><div class="form-error"><?php echo $errors['confirm_password']; ?></div><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <p class="form-footer-text">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<script src="js/validate.js"></script>
<?php include 'includes/footer.php'; ?>
