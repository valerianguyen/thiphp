<?php

session_start();

define('BASE_URL', '/sieuthi/');

include '../includes/db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $address = $_POST['address'];

   
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email OR phone = :phone");
    $stmt->execute(['email' => $email, 'phone' => $phone]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
    
        echo "<script>
        alert('Email, username hoặc số điện thoại đã được đăng ký.');
        window.location.href = '" . BASE_URL . "pages/auth.php'; 
        </script>";
        exit();
    } else {
      
        $stmt = $pdo->prepare("INSERT INTO user (username, fullname, email, phone, password,address) VALUES (:username, :fullname, :email, :phone, :password,:address)");
        $stmt->execute([
            'username' => $username,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'address' => $address
        ]);

        // Lấy ID của người dùng vừa được chèn
        $user_id = $pdo->lastInsertId();

        // Thiết lập biến session cho người dùng đã đăng nhập
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;

        // Chuyển hướng đến trang xác thực hoặc bước tiếp theo
        echo "<script>
        alert('Bạn đã đăng ký thành công!');
        window.location.href = '" . BASE_URL . "pages/auth.php'; 
        </script>";
  
        exit();
    }
}
?>