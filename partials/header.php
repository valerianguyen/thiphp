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

$sqlFile = __DIR__ . '/../controllers/';


if (!file_exists($sqlFile . 'verified.txt')) {
    echo '<script>window.location.href = "/sieuthi/controllers/initial.php";</script>';
    exit;
} else {

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error: Could not connect. " . $e->getMessage());
    }

    $sql_categories = "SELECT * FROM category WHERE is_active = 1";
    $stmt_categories = $pdo->prepare($sql_categories);
    $stmt_categories->execute();
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

    $total_quantity = 0;
    if (isset($_SESSION['id_user'])) {
        $sql_cart = "SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_username = :username";
        $stmt_cart = $pdo->prepare($sql_cart);
        $stmt_cart->execute(['username' => $_SESSION['username']]);
        $cart_data = $stmt_cart->fetch(PDO::FETCH_ASSOC);
        $total_quantity = $cart_data['total_quantity'] ?? 0;
    }

    function isAdmin($pdo, $user_id)
    {
        $sql = "SELECT isAdmin FROM user WHERE id_user = :id_user";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_user' => $user_id]);
        return (bool) $stmt->fetchColumn();
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siêu Thị Mini Market</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <style>
        .top-banner {
            background-color: #28a745;
            color: white;
            font-size: 14px;
            padding: 5px;
            display: flex;
            align-items: center;
        }

        .top-banner .container {

            display: flex;
            align-items: center;
        }

        .banner-slide {
            height: 500px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .banner-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            width: 80%;
        }

        .category-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .category-card:hover {
            transform: translateY(-10px);
        }

        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            height: 200px;
            object-fit: cover;
        }

        .flash-sale {
            background: linear-gradient(45deg, #ff6b6b, #ff8787);
            color: white;
            padding: 20px 0;
        }

        .timer {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .benefits-section {
            background-color: #f8f9fa;
            padding: 40px 0;
        }

        .benefit-item {
            text-align: center;
            padding: 20px;
        }

        .benefit-icon {
            font-size: 2.5rem;
            color: #28a745;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <div class="container text-center">
            <marquee>
                Chào mừng bạn đến với Mini Market, nơi cung cấp những thứ bạn cần.
            </marquee>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
                <i class="fas fa-leaf me-2"></i>Mini Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>index.php">Trang Chủ</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Danh Mục
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($categories as $category): ?>
                                <li><a class="dropdown-item"
                                        href="<?php echo BASE_URL; ?>pages/shop.php?category_id=<?= $category['id_category']; ?>"><?= $category['name']; ?></a></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>pages/shop.php">Cửa Hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>pages/aboutus.php">Về Chúng Tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>pages/contact.php">Liên Hệ</a>
                    </li>

                </ul>

                <div class="d-flex">


                    <?php if (isset($_SESSION['id_user'])): ?>
                        <a href="<?php echo BASE_URL; ?>pages/account.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-user"></i> Xin chào <?php echo $_SESSION['username']; ?>
                        </a>

                        <a href="<?php echo BASE_URL; ?>controllers/logout.php" class="btn btn-outline-light me-2">
                            Đăng xuất
                        </a>
                        <?php if (!isAdmin($pdo, $_SESSION['id_user'])): ?>
                            <a href="<?php echo BASE_URL; ?>pages/cart.php" class="btn btn-outline-light position-relative">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $total_quantity ?>
                                </span>
                            </a>

                        <?php endif; ?>
                    <?php else: ?>


                        <a href="<?php echo BASE_URL; ?>pages/auth.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-user"></i> Đăng Nhập
                        </a>

                        <a href="<?php echo BASE_URL; ?>pages/auth.php" class="btn btn-outline-light position-relative">
                            <i class="fas fa-shopping-cart"></i>

                        </a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </nav>