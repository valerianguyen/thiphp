<?php

include '../partials/headerAdmin.php'; // Nhúng phần đầu của trang, có thể bao gồm các thẻ HTML, CSS, và JS

if (isset($_SESSION['id_user'])) { // Kiểm tra xem người dùng đã đăng nhập hay chưa
    $user_id = $_SESSION['id_user']; // Lấy ID người dùng từ session

    if (!isAdmin($pdo, $user_id)) { 
        header('Location: ' . BASE_URL . 'index.php'); 
        exit; // Dừng thực thi mã
    }
}

$user_id = $_SESSION['id_user']; // Lấy ID người dùng từ session
$success_msg = ''; // Biến để lưu thông điệp thành công
$error_msg = ''; // Biến để lưu thông điệp lỗi

$active_section = 'info'; // Mặc định đặt phần đang hoạt động là thông tin

// Kiểm tra xem có yêu cầu phần nào được kích hoạt không
if (isset($_POST['section'])) {
    $active_section = $_POST['section']; // Nếu có yêu cầu từ form, đặt phần hoạt động theo yêu cầu
} elseif (isset($_GET['section'])) {
    $active_section = $_GET['section']; // Nếu có yêu cầu từ URL, đặt phần hoạt động theo yêu cầu
}

// Câu lệnh SQL để lấy thông tin người dùng dựa trên ID
$sql = "SELECT username, fullname, email, phone, address, password FROM user WHERE id_user = :user_id";
$stmt = $pdo->prepare($sql); // Chuẩn bị câu lệnh SQL
$stmt->execute(['user_id' => $user_id]); // Thực thi câu lệnh
$user_data = $stmt->fetch(PDO::FETCH_ASSOC); // Lấy dữ liệu người dùng

// Kiểm tra xem phương thức yêu cầu có phải là POST không và có yêu cầu cập nhật thông tin không
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $fullname = $_POST['fullname']; // Lấy tên đầy đủ từ yêu cầu
    $email = $_POST['email']; // Lấy email từ yêu cầu
    $phone = $_POST['phone']; // Lấy số điện thoại từ yêu cầu
    $address = $_POST['address']; // Lấy địa chỉ từ yêu cầu

    // Câu lệnh SQL để kiểm tra xem email hoặc số điện thoại đã tồn tại chưa
    $sql_check = "SELECT * FROM user WHERE (email = :email OR phone = :phone) AND id_user != :user_id";
    $stmt_check = $pdo->prepare($sql_check); // Chuẩn bị câu lệnh SQL
    $stmt_check->execute(['email' => $email, 'phone' => $phone, 'user_id' => $user_id]); // Thực thi câu lệnh kiểm tra

    // Nếu email hoặc số điện thoại đã tồn tại cho người dùng khác
    if ($stmt_check->rowCount() > 0) {
        $error_msg = "Email hoặc số điện thoại đã được sử dụng."; // Thông báo lỗi
        $active_section = 'info'; // Đặt lại phần hoạt động thành thông tin
    } else {
        // Câu lệnh SQL để cập nhật thông tin người dùng
        $sql_update = "UPDATE user SET fullname = :fullname, email = :email, phone = :phone, address = :address WHERE id_user = :user_id";
        $stmt_update = $pdo->prepare($sql_update); // Chuẩn bị câu lệnh SQL
        $stmt_update->execute(['fullname' => $fullname, 'email' => $email, 'phone' => $phone, 'address' => $address, 'user_id' => $user_id]); // Thực thi câu lệnh với thông tin mới

        $success_msg = "Thông tin cập nhật thành công!"; // Thông báo thành công
        echo "<script>
            alert('$success_msg'); 
            window.location.href = '?section=info'; 
        </script>";
        exit; // Dừng thực thi mã
    }
}

// Kiểm tra xem có yêu cầu thay đổi mật khẩu không
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password']; // Lấy mật khẩu cũ từ yêu cầu
    $new_password = $_POST['new_password']; // Lấy mật khẩu mới từ yêu cầu
    $confirm_password = $_POST['confirm_password']; // Lấy xác nhận mật khẩu mới từ yêu cầu

    // Kiểm tra mật khẩu cũ
    if ($old_password !== $user_data['password']) {
        $error_msg = "Mật khẩu cũ không đúng!"; // Thông báo lỗi mật khẩu cũ không đúng
        $active_section = 'password'; // Đặt lại phần hoạt động thành mật khẩu
    } elseif (strlen($new_password) < 5) { // Kiểm tra độ dài mật khẩu mới
        $error_msg = "Mật khẩu mới phải ít nhất 5 ký tự"; // Thông báo lỗi mật khẩu mới quá ngắn
        $active_section = 'password'; // Đặt lại phần hoạt động thành mật khẩu
    } elseif ($new_password !== $confirm_password) { // Kiểm tra xem mật khẩu mới và xác nhận có khớp nhau không
        $error_msg = "Mật khẩu mới không khớp"; // Thông báo lỗi mật khẩu mới không khớp
        $active_section = 'password'; // Đặt lại phần hoạt động thành mật khẩu
    } else {
        // Câu lệnh SQL để cập nhật mật khẩu
        $sql_password = "UPDATE user SET password = :password WHERE id_user = :user_id";
        $stmt_password = $pdo->prepare($sql_password); // Chuẩn bị câu lệnh SQL
        $stmt_password->execute(['password' => $_new_password, 'user_id' => $user_id]); // Thực thi câu lệnh với mật khẩu mới

        $success_msg = "Mật khẩu thay đổi thành công!"; // Thông báo thành công
        echo "<script>
            alert('$success_msg'); 
            window.location.href = '?section=password'; 
        </script>";
        exit; // Dừng thực thi mã
    }
}

// Nếu phần đang hoạt động là đơn hàng
if ($active_section === 'orders') {
    // Câu lệnh SQL để lấy lịch sử đơn hàng của người dùng
    $sql_orders = "
        SELECT orders.id_order, orders.order_date, orders.total_price, orders.status, order_detail.product_name, 
               order_detail.price_at_purchase, order_detail.quantity
        FROM orders
        JOIN order_detail ON orders.id_order = order_detail.order_id
        WHERE orders.user_username = :user_username
        ORDER BY orders.order_date DESC";

    $stmt_orders = $pdo->prepare($sql_orders); // Chuẩn bị câu lệnh SQL
    $stmt_orders->execute(['user_username' => $user_data['username']]); // Thực thi câu lệnh với tên người dùng
    $order_history = $stmt_orders->fetchAll(PDO::FETCH_ASSOC); // Lấy tất cả đơn hàng và lưu vào mảng
}

?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/account.css">
<style>
    /* Global Styles */
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa;
    }

    .containerr {
        padding-top: 50px;
        padding-bottom: 50px;
        padding-left: 100px;
        padding-right: 100px ;
    }

    .tab-navigation {
        display: flex;
        justify-content: space-around;
        margin-bottom: 20px;
    }

    .tab-navigation a {
        width: 30%;
        padding: 15px;
        text-align: center;
        text-decoration: none;
        font-size: 16px;
        font-weight: bold;
        border-radius: 50px;
        border: 2px solid transparent;
        transition: 0.3s ease;
        color: rgb(25,135,84);
    }

    .tab-navigation a.active {
        background-color: rgb(25,135,84);
        color: white;
        border-color: rgb(25,135,84);
    }

    .tab-navigation a:hover {
        background-color: rgba(48, 120, 156, 0.8);
        color: white;
        border-color: rgba(48, 120, 156, 0.8);
    }

    /* Card Layout */
    .card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 40px;
        margin-bottom: 30px;
    }

    h2 {
        color: rgb(25,135,84);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-control {
        border-radius: 10px;
        padding: 10px 15px;
        font-size: 16px;
    }

    .btn-custom {
        background-color: rgb(25,135,84);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 16px;
        transition: 0.3s ease;
        display: block;
        width: 100%;
        margin-top: 20px;
    }

    .btn-custom:hover {
        background-color: rgba(48, 120, 156, 0.8);
        color: white;
    }

    /* Spacing and Layout Enhancements */
    .form-group {
        margin-bottom: 20px;
    }

    .table {
        margin-top: 30px;
    }

    /* Icons for a modern touch */
    .form-icon {
        color: rgb(25,135,84);
        margin-right: 10px;
    }

    /* Style alerts */
    .alert {
        padding: 15px;
        margin-bottom: 30px;
        border-radius: 8px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

<div class="containerr">

    <div class="tab-navigation">
        <!-- Tạo các liên kết cho các phần thông tin tài khoản, mật khẩu và lịch sử đơn hàng -->
        <a href="?section=info" class="<?= ($active_section == 'info') ? 'active' : '' ?>">Thông tin tài khoản</a>
        <a href="?section=password" class="<?= ($active_section == 'password') ? 'active' : '' ?>">Mật khẩu</a>
    
    </div>

    <div class="card">

        <div id="account-info" style="<?= ($active_section == 'info') ? '' : 'display: none;' ?>">
            <h2><i class="fas fa-user-circle form-icon"></i> Thông tin tài khoản</h2>
            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if ($error_msg && !isset($_POST['change_password'])): ?>
                <div class="alert alert-danger"><?= $error_msg ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="section" value="info">

                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <!-- Hiển thị tên đăng nhập, không cho phép chỉnh sửa -->
                    <input type="text" class="form-control" id="username" value="<?= $user_data['username'] ?>"
                        readonly>
                </div>

                <div class="form-group">
                    <label for="fullname">Tên đầy đủ</label>
                    <!-- Trường nhập tên đầy đủ, cho phép chỉnh sửa -->
                    <input type="text" class="form-control" name="fullname" id="fullname"
                        value="<?= $user_data['fullname'] ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <!-- Trường nhập email, cho phép chỉnh sửa -->
                    <input type="email" class="form-control" name="email" id="email" value="<?= $user_data['email'] ?>">
                </div>

              

            

                <!-- Nút để cập nhật thông tin -->
                <button type="submit" name="update_info" class="btn btn-custom">Cập nhật thông tin</button>
            </form>
        </div>

        <div id="password-section" style="<?= ($active_section == 'password') ? '' : 'display: none;' ?>">
            <h2><i class="fas fa-key form-icon"></i> Thay đổi mật khẩu</h2>
            <!-- Hiển thị thông báo lỗi nếu có khi thay đổi mật khẩu -->
            <?php if ($error_msg && isset($_POST['change_password'])): ?>
                <div class="alert alert-danger"><?= $error_msg ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="section" value="password">

                <div class="form-group">
                    <label for="oldPassword">Mật khẩu cũ</label>
                    <!-- Trường nhập mật khẩu cũ -->
                    <input type="password" class="form-control" name="old_password" id="oldPassword">
                </div>

                <div class="form-group">
                    <label for="newPassword">Mật khẩu mới</label>
                    <!-- Trường nhập mật khẩu mới -->
                    <input type="password" class="form-control" name="new_password" id="newPassword">
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Xác nhận mật khẩu mới</label>
                    <!-- Trường nhập xác nhận mật khẩu mới -->
                    <input type="password" class="form-control" name="confirm_password" id="confirmPassword">
                </div>

                <!-- Nút để đổi mật khẩu -->
                <button type="submit" name="change_password" class="btn btn-custom">Đổi mật khẩu</button>
            </form>
        </div>

       
    </div>
</div>


<?php include '../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>