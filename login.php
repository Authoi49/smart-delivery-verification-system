<?php
session_start();
require_once 'includes/db_connect.php';
$base = '';
$pageTitle = 'Login';
$error = '';
$successMsg = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($user['role'] === 'rider') {
                    header("Location: rider/dashboard.php");
                } else {
                    header("Location: customer/dashboard.php");
                }
                exit();
            } else {
                $error = 'Incorrect password.';
            }
        } else {
            $error = 'No account found with that email.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="form-box">
        <h2>Login</h2>

        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p class="form-footer-text">Don't have an account? <a href="register.php">Register here</a></p>

        <div class="alert alert-info mt-24" style="font-size:0.82rem;">
            <b>Demo accounts</b> (after running seed.php):<br>
            Admin: admin@delivery.com / admin123<br>
            Rider: rider@delivery.com / rider123<br>
            Customer: customer@delivery.com / cust123
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
