<?php

session_start();

define('BASE_URL', value: '/sieuthi/');

include '../includes/db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {


    $emailOrPhone = $_POST['emailOrPhone'];
    $password = $_POST['password'];


    $stmt = $pdo->prepare("SELECT * FROM user WHERE (email = :emailOrPhone OR phone = :emailOrPhone OR username = :emailOrPhone)");

    $stmt->bindParam(':emailOrPhone', $emailOrPhone, PDO::PARAM_STR);

    $stmt->execute();


    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user) {

        if ($user['isDisabled']) {

            echo "<script>
                alert('Tài khoản đã bị vô hiệu hóa.');
                window.location.href = '" . BASE_URL . "pages/auth.php';
              </script>";
            exit();
        }

        if ($password === $user['password']) {

            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];

            header('Location: ' . BASE_URL . 'index.php');
            exit();
        } else {

            echo "<script>
                    alert('Email/số điện thoại/tên người dùng hoặc mật khẩu không đúng.');
                    window.location.href = '" . BASE_URL . "pages/auth.php';
                  </script>";
            exit();
        }
    } else {

        echo "<script>
                alert('Người dùng không tồn tại.');
                window.location.href = '" . BASE_URL . "pages/auth.php';
              </script>";
        exit();
    }
}
?>