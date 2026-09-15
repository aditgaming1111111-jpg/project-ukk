<?php
require_once __DIR__ . '/../config/database.php';

if (!empty($_SESSION['user_id'])) {
    redirect('/dashboard.php');
}

$pageTitle = 'Login - SALESPRO';
$error = flash('error');
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>SALESPRO</h1>
                <p>Sales Management System</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="proses_login.php" class="login-form">
                <div class="form-group">
                    <label for="email">Email / Username</label>
                    <input type="text" id="email" name="email" placeholder="Email / Username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">LOGIN</button>
            </form>
        </div>
    </div>
</body>
</html>
