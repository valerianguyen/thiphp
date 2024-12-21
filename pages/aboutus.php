<?php

// Bao gồm file header.php để sử dụng các phần chung trên trang
include '../partials/header.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa bằng cách kiểm tra biến session 'id_user'
if (isset($_SESSION['id_user'])) {
    // Lấy ID của người dùng từ session
    $user_id = $_SESSION['id_user'];

    // Kiểm tra xem người dùng có phải là quản trị viên không bằng cách gọi hàm isAdmin
    if (isAdmin($pdo, $user_id)) {
        // Nếu là quản trị viên, chuyển hướng đến trang quản trị admin.php
        header('Location: ' . BASE_URL . 'pages/admin.php');
        // Ngừng thực hiện các đoạn code sau
        exit;
    }
}

?>


<div class="container mt-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6 order-md-2 text-center">
            <img src="https://i.pinimg.com/originals/f3/e5/70/f3e570ab505ac1ba962e6004a0c36e5d.jpg" class="img-fluid rounded" alt="Siêu Thị Nhỏ">
        </div>
        <div class="col-md-6 order-md-1 text-center text-md-start">
            <h1 class="display-4 section-title">Chào Mừng Đến Với Siêu Thị Của Chúng Tôi</h1>
            <p class="lead about-text">
                Khám phá các sản phẩm chất lượng cao với giá cả phải chăng. Chúng tôi cung cấp đa dạng thực phẩm tươi sống, hàng tiêu dùng và nhu yếu phẩm hàng ngày cho gia đình bạn.
            </p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <h2 class="section-title">Câu Chuyện Của Chúng Tôi</h2>
        </div>
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <p class="about-text">
                        Tại <strong>Siêu Thị Nhỏ</strong>, chúng tôi tin vào việc mang đến những sản phẩm an toàn, tươi ngon và đảm bảo chất lượng cho khách hàng. Từ những ngày đầu, chúng tôi đã phục vụ cộng đồng với niềm đam mê và sự tận tâm.
                    </p>
                    <p class="about-text">
                        Với sự phát triển của mình, chúng tôi đã trở thành một địa điểm tin cậy, cung cấp đầy đủ mọi sản phẩm gia đình bạn cần từ rau củ, thịt cá đến các nhu yếu phẩm hàng ngày.
                        Sứ mệnh của chúng tôi là cung cấp những sản phẩm chất lượng, đáp ứng nhu cầu thiết yếu hàng ngày của mọi gia đình.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5 align-items-center">
        <div class="col-md-6 text-center">
            <img src="https://th.bing.com/th/id/R.7ac9c9a30557369479700e7769dc0ce2?rik=ausa7rLNY8aHqw&pid=ImgRaw&r=0" class="img-fluid rounded" alt="Sản Phẩm Tươi Ngon">
        </div>
        <div class="col-md-6">
            <h2 class="section-title text-center text-md-start">Chúng Tôi Cung Cấp Gì</h2>
            <ul class="about-text">
                <li>Thực phẩm tươi sống: Rau củ, trái cây, thịt và hải sản</li>
                <li>Đồ gia dụng và nhu yếu phẩm hàng ngày</li>
                <li>Thực phẩm đóng hộp và các món ăn nhẹ</li>
                <li>Sản phẩm dành cho trẻ em và đồ dùng vệ sinh</li>
            </ul>
            <p class="about-text">
                Tất cả sản phẩm đều được chọn lọc kỹ lưỡng để đảm bảo bạn nhận được sự hài lòng cao nhất. Chúng tôi cam kết cung cấp sản phẩm chất lượng với giá cả hợp lý.
            </p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <h2 class="section-title">Giá Trị Của Chúng Tôi</h2>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h4 class="signature-color-text" style="color: rgb(25,135,84);">Chất Lượng</h4>
                    <p class="about-text">
                        Chúng tôi cam kết mang đến những sản phẩm tốt nhất cho khách hàng, từ thực phẩm tươi sạch đến hàng tiêu dùng chất lượng cao.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h4 class="signature-color-text" style="color: rgb(25,135,84);">Giá Trị</h4>
                    <p class="about-text">
                        Chúng tôi tin vào việc mang đến sản phẩm với mức giá hợp lý, đảm bảo khách hàng luôn nhận được giá trị tốt nhất cho từng đồng chi tiêu.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h4 class="signature-color-text" style="color: rgb(25,135,84);">Cộng Đồng</h4>
                    <p class="about-text">
                        Chúng tôi xem khách hàng như những người bạn đồng hành, cùng nhau xây dựng cộng đồng phát triển và bền vững.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5 text-center">
        <div class="col-md-12">
            <h2 class="section-title">Tham Gia Cùng Chúng Tôi</h2>
            <p class="about-text">
                Hãy đến và trải nghiệm mua sắm tại siêu thị của chúng tôi. Chúng tôi luôn sẵn sàng phục vụ bạn với những sản phẩm tươi ngon và dịch vụ tận tâm.
            </p>
            <a href="<?php echo BASE_URL; ?>pages/shop.php" class="btn btn-primary mt-3" style="background-color: rgb(25,135,84); border-color: rgb(25,135,84);">Mua Ngay</a>
        </div>
    </div>
</div>



<?php

include '../partials/footer.php';

?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>