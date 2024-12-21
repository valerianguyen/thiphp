<?php


require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];


try {
    // Tạo kết nối PDO tới cơ sở dữ liệu MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // Thiết lập chế độ báo lỗi cho PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Nếu xảy ra lỗi trong quá trình kết nối, dừng chương trình và hiển thị thông báo lỗi
    die("Lỗi: Không thể kết nối. " . $e->getMessage());
}
?>
