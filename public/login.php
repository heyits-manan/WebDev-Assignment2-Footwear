<?php
include '../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('index.php');
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2 class="section-title">Login</h2>
        <?php if ($error): ?>
            <p style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn" style="width: 100%; border: none; cursor: pointer;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">Don't have an account? <a href="register.php" style="color: var(--accent);">Register here</a></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
