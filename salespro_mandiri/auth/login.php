<?php 

    session_start();

    $pesan_error = null;
    if ( isset($_SESSION['pesan_error'])) {
        $pesan_error = $_SESSION['pesan_error'];
        unset($_SESSION['pesan_error']);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<body>

    <div>

        <div>
            <h2>Login Warung ABC</h2>
            <p>Masuk untuk mengakses sistem</p>
        </div>

        <?php if ($pesan_error) { ?>
            <div><?= $pesan_error; ?></div>
        <?php } ?>

        <form action="proses_login.php" method="POST">

            <div >
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login">Login</button>

        </form>

    </div>

</body>
</html>