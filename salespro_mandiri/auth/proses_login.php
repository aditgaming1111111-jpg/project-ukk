<?php 

    session_start();

    require_once __DIR__ . '/../config/database.php';

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM pengguna WHERE email = '$email' ";
    $hasil = mysqli_query($conn, $sql);

    if (mysqli_num_rows($hasil) == 1) {
        $data = mysqli_fetch_assoc($hasil);

        if (password_verify($password, $data['password'])) {
            // password cocok buat session
            $_SESSION['login'] = true;
            $_SESSION['pengguna_id'] = $data['pengguna_id'];
            $_SESSION['nama_pengguna'] = $data['nama_pengguna'];
            $_SESSION['peran'] = $data['peran'];

            header("Location: ../dashboard.php");
            exit;
        } else {
            $_SESSION['pesan_error'] = 'password salah!';
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['pesan_error'] = 'Email tidak ditemukan!';
        header("Location: login.php");
        exit;
    }

?>