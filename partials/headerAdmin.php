<?php
session_start();  
define('BASE_URL', '/sieuthi/');  

require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];


try {
    // Tạo đối tượng PDO để kết nối với cơ sở dữ liệu MySQL và thiết lập mã hóa UTF-8.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Thiết lập chế độ báo lỗi.
} catch (PDOException $e) {
    // Nếu kết nối thất bại, dừng script và in ra thông báo lỗi.
    die("Error: Could not connect. " . $e->getMessage());
}

// Truy vấn lấy danh sách các danh mục (categories) đang hoạt động (is_active = 1)
$sql_categories = "SELECT * FROM category WHERE is_active = 1";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);  // Lưu kết quả truy vấn vào mảng $categories.

$total_quantity = 0;  // Biến lưu trữ tổng số lượng sản phẩm trong giỏ hàng.

// Nếu người dùng đã đăng nhập (session id_user tồn tại)
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];  // Lấy id người dùng từ session.

    // Truy vấn để lấy tổng số lượng sản phẩm trong giỏ hàng của người dùng.
    $sql_cart = "SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_username = :username";
    $stmt_cart = $pdo->prepare($sql_cart);
    $stmt_cart->execute(['username' => $_SESSION['username']]);  // Thực thi truy vấn với tên người dùng từ session.
    $cart_data = $stmt_cart->fetch(PDO::FETCH_ASSOC);  // Lấy dữ liệu tổng số lượng.

    // Nếu có dữ liệu giỏ hàng, cập nhật biến $total_quantity.
    if ($cart_data['total_quantity']) {
        $total_quantity = $cart_data['total_quantity'];
    }
}

// Hàm kiểm tra người dùng có phải là admin không.
function isAdmin($pdo, $user_id)
{
    // Truy vấn kiểm tra cột isAdmin của người dùng theo id.
    $sql = "SELECT isAdmin FROM user WHERE id_user = :id_user";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_user' => $user_id]);  // Thực thi truy vấn với id người dùng.
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);  // Lấy kết quả truy vấn.

    // Nếu tồn tại dữ liệu người dùng và isAdmin = 1 thì trả về true (là admin).
    return $user_data && $user_data['isAdmin'] == 1;
}



if (isset($_SESSION['id_user'])) {
   
    $user_id = $_SESSION['id_user'];

   
    if (!isAdmin($pdo, $user_id)) {
     
        header('Location: ' . BASE_URL . 'index.php');
       
        exit;
    }
}
else{
    header('Location: ' . BASE_URL . 'index.php');
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Market</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/index.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/header.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark"
            style="background-color: rgb(25,135,84); padding: 20px 40px; position: relative;">
            <div class="container-fluid">

                <a class="navbar-brand text-white fw-bold me-5" style="font-size: 2rem;"
                    href="<?php echo BASE_URL; ?>index.php">
                    Mini Market
                </a>


                <?php
                if (isset($_SESSION['id_user'])) {  // Kiểm tra nếu người dùng đã đăng nhập
                    $user_id = $_SESSION['id_user'];  // Lấy ID người dùng từ session

                    // Kiểm tra xem người dùng có phải là admin hay không
                    if (!isAdmin($pdo, $user_id)) {  // Nếu không phải admin thì hiển thị dropdown danh mục
                ?>
                        <div class="dropdown me-5">
                            <a class="btn btn-outline-light dropdown-toggle text-white fw-semibold rounded-pill" href="#"
                                role="button" id="categoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Danh mục
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                                <?php foreach ($categories as $category): ?> <!-- Lặp qua các danh mục -->
                                    <li>
                                        <a class="dropdown-item"
                                            href="<?php echo BASE_URL; ?>pages/shop.php?category_id=<?= $category['id_category']; ?>"> <!-- Link đến shop.php theo từng id_category -->
                                            <?= $category['name']; ?> <!-- Hiển thị tên danh mục -->
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php
                    }
                } else {  // Nếu người dùng chưa đăng nhập
                    ?>
                    <div class="dropdown me-5">
                        <a class="btn btn-outline-light dropdown-toggle text-white fw-semibold rounded-pill" href="#"
                            role="button" id="categoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Danh mục
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            <?php foreach ($categories as $category): ?> <!-- Lặp qua các danh mục -->
                                <li>
                                    <a class="dropdown-item"
                                        href="<?php echo BASE_URL; ?>pages/shop.php?category_id=<?= $category['id_category']; ?>"> <!-- Link đến shop.php theo từng id_category -->
                                        <?= $category['name']; ?> <!-- Hiển thị tên danh mục -->
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php
                }
                ?>




                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>


                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">

                    <!-- Danh sách các mục trong navbar -->
                    <ul class="navbar-nav">
                        <?php
                        // Nếu người dùng đã đăng nhập và không phải là admin hoặc chưa đăng nhập
                        if ((isset($_SESSION['id_user']) && !isAdmin($pdo, $_SESSION['id_user'])) || (!isset($_SESSION['id_user']))):
                        ?>
                            <li class="nav-item mx-1">
                                <a class="nav-link text-white fw-semibold" href="<?php echo BASE_URL; ?>pages/shop.php">Cửa hàng</a>
                            </li>
                            <li class="nav-item mx-1">
                                <a class="nav-link text-white fw-semibold" href="<?php echo BASE_URL; ?>pages/aboutus.php">Về chúng tôi</a>
                            </li>
                            <li class="nav-item mx-1">
                                <a class="nav-link text-white fw-semibold" href="<?php echo BASE_URL; ?>pages/contact.php">Liên hệ</a>
                            </li>
                        <?php endif; ?>
                    </ul>


                    </ul>
                    <!-- Search Bar -->
                    <!-- <form class="d-flex ms-auto" role="search" action="<?php echo BASE_URL; ?>pages/shop.php" method="GET">
                        <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..." name="search" aria-label="Search" required>
                        <button class="btn btn-outline-light" type="submit">Tìm kiếm</button>
                    </form> -->



                    <!-- Các nút liên quan đến tài khoản người dùng -->
                    <div class="d-flex align-items-center">
                        <?php if (isset($_SESSION['id_user'])): ?>
                            <!-- Hiển thị thông báo chào mừng người dùng nếu họ đã đăng nhập -->
                            <a class="text-white me-3 fw-bold" href="<?php echo BASE_URL; ?>pages/admin_profile.php">Xin chào
                                <?php echo $_SESSION['username']; ?>! </a>

                            <!-- Nút đăng xuất -->
                            <a href="<?php echo BASE_URL; ?>controllers/logout.php" class="btn btn-light rounded-pill me-4">Đăng xuất</a>

                            <!-- Hiển thị nút giỏ hàng cho người dùng không phải admin -->
                            <?php if (!isAdmin($pdo, $_SESSION['id_user'])): ?>
                                <a href="<?php echo BASE_URL; ?>pages/cart.php"
                                    class="btn btn-outline-light rounded-pill position-relative" style="font-size:15px">
                                    🛒
                                    <!-- Hiển thị số lượng sản phẩm trong giỏ hàng -->
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?= $total_quantity ?>
                                    </span>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Nếu chưa đăng nhập, hiển thị nút đăng nhập -->
                            <a href="<?php echo BASE_URL; ?>pages/auth.php" class="btn btn-light rounded-pill me-4"
                                style="font-size:15px">Đăng nhập</a>
                        <?php endif; ?>
                    </div>


                    

                </div>

            </div>
        </nav>
    </header>




