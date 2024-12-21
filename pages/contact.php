<?php
include '../partials/header.php'; // Bao gồm header từ phần phụ

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user']; // Lấy ID người dùng từ phiên

    // Kiểm tra xem người dùng có phải là quản trị viên không
    if (isAdmin($pdo, $user_id)) {
        // Nếu là admin, chuyển hướng người dùng đến trang admin bằng JavaScript
        echo "<script>
            window.location.href = '" . BASE_URL . "pages/admin.php';
        </script>";
        exit; // Dừng chạy tiếp các mã lệnh dưới
    }
}

$success = false; // Khởi tạo biến $success để theo dõi trạng thái thành công
// Kiểm tra phương thức yêu cầu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success = true; // Nếu yêu cầu là POST, cập nhật biến $success thành true
}
?>



<style>
    .signature-color {
        background-color: rgb(25,135,84);
        color: #fff;
    }

    .btn-signature {
        background-color: rgb(25,135,84);
        border-color: rgb(25,135,84);
    }

    .btn-signature:hover {
        background-color: rgb(38, 100, 136);
        border-color: rgb(38, 100, 136);
    }

    .contact-heading {
        color: rgb(25,135,84);
        font-weight: bold;
    }

    .contact-info-icon {
        font-size: 2.5rem;
        color: rgb(25,135,84);
    }

    .alert-success {
        margin-top: 20px;
    }

    .contact-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .contact-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #f0f4f7, #e8eff1);
    }

    .find-us-map {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
    }
</style>

<?php if ($success): ?>
    <div class="alert alert-success text-center" role="alert">
        Tin nhắn của bạn đã được gửi thành công! <!-- Thông báo thành công cho người dùng -->
    </div>
<?php endif; ?>


<section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-5">
                <div class="contact-card text-center">
                    <i class="contact-info-icon bi bi-geo-alt"></i>
                    <h5 class="contact-heading mt-3">Địa Chỉ Của Chúng Tôi</h5>
                    <p>365 Phố Mini Market, Việt Nam</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-5">
                <div class="contact-card text-center">
                    <i class="contact-info-icon bi bi-telephone"></i>
                    <h5 class="contact-heading mt-3">Điện Thoại</h5>
                    <p>+84 123 456 789</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 mb-5">
                <div class="contact-card text-center">
                    <i class="contact-info-icon bi bi-envelope"></i>
                    <h5 class="contact-heading mt-3">Email</h5>
                    <p>contact@minimarket.com</p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-10 offset-md-1">
                <h2 class="text-center mb-4 contact-heading">Gửi Tin Nhắn Cho Chúng Tôi</h2>
                <div class="contact-card p-4">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Họ và Tên</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên của bạn" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Địa Chỉ Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Tin Nhắn</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Viết tin nhắn của bạn ở đây" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-signature">Gửi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>



<?php include '../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>

</html>