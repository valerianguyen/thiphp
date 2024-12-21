<?php
ob_start();
include '../partials/header.php';
include '../includes/func.php';

$product_id = (int) $_GET['id'] ?? 0;

if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];
    if (isAdmin($pdo, $user_id)) {
        header('Location: ' . BASE_URL . 'pages/admin.php');
        exit;
    }

    $user_username = $_SESSION['username'] ?? null;
    $order_check_sql = "SELECT COUNT(*) FROM orders o INNER JOIN order_detail od ON o.id_order = od.order_id WHERE o.user_username = :username AND od.product_id = :product_id AND o.status = 'Hoàn thành'";
    $order_check_stmt = $pdo->prepare($order_check_sql);
    $order_check_stmt->bindParam(':username', $user_username);
    $order_check_stmt->bindParam(':product_id', $product_id);
    $order_check_stmt->execute();
    $has_ordered = $order_check_stmt->fetchColumn() > 0;

    if ($has_ordered) {
        $order_list_sql = "SELECT o.id_order FROM orders o INNER JOIN order_detail od ON o.id_order = od.order_id WHERE o.user_username = :username AND od.product_id = :product_id AND o.status = 'Hoàn thành'";
        $order_list_stmt = $pdo->prepare($order_list_sql);
        $order_list_stmt->bindParam(':username', $user_username);
        $order_list_stmt->bindParam(':product_id', $product_id);
        $order_list_stmt->execute();
        $order_ids = $order_list_stmt->fetchAll(PDO::FETCH_COLUMN);

        $reviewed_order_sql = "SELECT order_id FROM product_reviews WHERE user_username = :username AND product_id = :product_id";
        $reviewed_order_stmt = $pdo->prepare($reviewed_order_sql);
        $reviewed_order_stmt->bindParam(':username', $user_username);
        $reviewed_order_stmt->bindParam(':product_id', $product_id);
        $reviewed_order_stmt->execute();
        $reviewed_order_ids = $reviewed_order_stmt->fetchAll(PDO::FETCH_COLUMN);

        $available_order_ids = array_diff($order_ids, $reviewed_order_ids);

        if (isset($_POST['submit_review'])) {

            $rating = (int) $_POST['rating'];
            $review_text = trim($_POST['review_text']);

            if (empty($available_order_ids)) {
                echo "<script>alert('Bạn đã đánh giá tất cả các đơn hàng của sản phẩm này rồi.');</script>;
                window.location.href = '" . $_SERVER['REQUEST_URI'] . "';";

                exit;
            }

            $order_id = reset($available_order_ids);

            $insert_review_sql = "INSERT INTO product_reviews (product_id, user_username, order_id, rating, review_text) VALUES (:product_id, :user_username, :order_id, :rating, :review_text)";
            $insert_review_stmt = $pdo->prepare($insert_review_sql);
            $insert_review_stmt->bindParam(':product_id', $product_id);
            $insert_review_stmt->bindParam(':user_username', $user_username);
            $insert_review_stmt->bindParam(':order_id', $order_id);
            $insert_review_stmt->bindParam(':rating', $rating);
            $insert_review_stmt->bindParam(':review_text', $review_text);

            if ($insert_review_stmt->execute()) {
                echo "<script>alert('Đánh giá của bạn đã được gửi thành công!');
                window.location.href = '" . $_SERVER['REQUEST_URI'] . "';</script>";

                exit;
            } else {
                echo "<script>alert('Có lỗi xảy ra khi gửi đánh giá.');</script>";
            }
        }
    }
}

$sql = "SELECT product.*, category.name AS category_name FROM product INNER JOIN category ON product.category_id = category.id_category WHERE product.id_product = :id AND product.is_active = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo '
            <h3 class="text-center">Đang trong quá trình phát triển.</h3>
                        <p class="lead text-center">Vui lòng quay lại sau! ❤️</p>';
    exit;
}

if ($product['quantity'] <= 0) {
    echo "<script>alert('Sản phẩm đã hết hàng, vui lòng thử lại sau !'); window.location.href = 'shop.php';</script>";
    exit;
}

$image_sql = "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC";
$image_stmt = $pdo->prepare($image_sql);
$image_stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$image_stmt->execute();
$product_images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

$review_sql = "SELECT r.rating, r.review_text, u.username, o.id_order, r.created_at FROM product_reviews r INNER JOIN user u ON r.user_username = u.username INNER JOIN orders o ON r.order_id = o.id_order WHERE r.product_id = :product_id ORDER BY r.created_at DESC";
$review_stmt = $pdo->prepare($review_sql);
$review_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$review_stmt->execute();
$reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

$average_rating = 0;
$total_reviews = count($reviews);
if ($total_reviews > 0) {
    $sum_rating = array_sum(array_column($reviews, 'rating'));
    $average_rating = round($sum_rating / $total_reviews, 1);
}

if (!$product) {
    echo "<h2>Product not found</h2>";
    exit;
}

if (isset($_POST['buy_now']) || isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['id_user'])) {
        header('Location: ' . BASE_URL . 'pages/auth.php');
        exit;
    } else {
        $user_username = $_SESSION['username'] ?? null;
        $quantity = (int) $_POST['quantity'] ?? 0;

        if ($quantity > $product['quantity']) {
            echo "<script>alert('Số lượng sản phẩm không đủ. !'); window.location.href = '" . $_SERVER['REQUEST_URI'] . "';</script>";
            exit;
        }

        addToCart($pdo, $user_username, $product_id, $product['price'], quantity: $quantity);

        if (isset($_POST['buy_now'])) {
            echo "
            <script type='text/javascript'>
            alert('Mua sản phẩm thành công!');
            window.location.href = 'cart.php';</script>";
            exit();
        } else if (isset($_POST['add_to_cart'])) {
            echo '<script>alert("Thêm vô giỏ hàng thành công!");</script>';
        }
    }
}

?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/details.css">

<div class="container mt-5">
    <div class="row">
        <div class="col-md-7">
            <div class="product-image-container">

                <?php if (!empty($product_images)): ?>
                    <div class="product-main-image shadow rounded overflow-hidden d-flex justify-content-center">
                        <img src="../<?= $product_images[0]['image_path']; ?>" alt="Primary Product Image"
                            class="img-fluid main-product-image" onerror="this.src='https://placehold.co/746x500'">
                    </div>


                    <?php if (count($product_images) > 1): ?>
                        <div class="product-image-gallery mt-3 d-flex justify-content-start">
                            <?php foreach ($product_images as $image): ?>
                                <div class="gallery-image-wrapper me-2">
                                    <img src="../<?= $image['image_path']; ?>" alt="Product Image"
                                        class="img-thumbnail gallery-image" data-full-image="../<?= $image['image_path']; ?> "
                                        onerror="this.src='https://placehold.co/69x64'">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>

                    <div class="product-image-wrapper shadow rounded overflow-hidden d-flex justify-content-center">
                        <img src="../<?= $product['image']; ?>" alt="Hình ảnh sản phẩm" class="product-img img-fluid">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-5 ps-">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>

                        <li class="breadcrumb-item active" aria-current="page"><?= $product['category_name']; ?></li>

                    </ol>
                </nav>
            </div>
            <h2 class="product-title" style="color: rgb(25,135,84); font-weight: bold;">
                <?= $product['name']; ?>
            </h2>
            <p>
                Số lượng còn lại:
                <input type="text" value="<?= $product['quantity']; ?>" readonly
                    style="border: none; background: transparent; font-size: 16px;">
            </p>

            <?php if ($product['quantity'] < 10): ?>
                <p style="color: red; font-size: 18px;">Sản phẩm sắp hết hàng</p>
            <?php endif; ?>
            <p class="text-muted" style="font-style: italic;color:black">
                <?= $product['category_name']; ?>
            </p>
            <p class="price fw-bold" style="font-size: 1.5rem; color: rgb(25,135,84);">
                <?= number_format($product['price'], 0, ',', '.') . ' ₫'; ?>

            </p>
            <p class="product-description text-muted">
                <?= $product['shortdesc']; ?>
            </p>

            <div class="d-flex align-items-center mb-3">
                <form method="post" action="" id="productForm">



                    <div class="input-group mb-3">
                        <input type="number" name="quantity" class="form-control text-center" value="1" min="1"
                            style="max-width: 80px;">
                        <button type="submit" name="add_to_cart" class="btn btn-outline-success btn-signature ms-2">Thêm
                            vào giỏ hàng</button>
                        <button type="submit" name="buy_now" class="btn btn-primary ms-2"
                            style="background-color: rgb(25,135,84); border-color: rgb(25,135,84);">Mua ngay</button>

                    </div>
                </form>
            </div>

            <div id="successAlert" class="alert alert-success mt-3" style="display:none;">

                Sản phẩm đã được thêm vào giỏ hàng thành công!
            </div>
        </div>
    </div>

    <div class="product-info mt-5 p-4 bg-light rounded shadow">
        <h4 class="text-primary" style="color: rgb(25,135,84); font-weight: bold;">Thông tin</h4>
        <p><?= $product['longdesc']; ?></p>
    </div>
</div>

<?php if (isset($_SESSION['id_user']) && $has_ordered && $available_order_ids): ?>
    <div class="review-form mt-2 p-5 bg-light rounded shadow" style="
        width: 1300px;
        margin: 0 auto;
        max-width: 100%;
    ">
        <h4 class="text-primary" style="font-weight: bold; text-align:center;">Hãy Để Lại Đánh Giá</h4>
        <form method="post" action="">
            <div class="rating">
                <input type="radio" name="rating" id="star5" value="5" required>
                <label for="star5">★</label>
                <input type="radio" name="rating" id="star4" value="4" required>
                <label for="star4">★</label>
                <input type="radio" name="rating" id="star3" value="3" required>
                <label for="star3">★</label>
                <input type="radio" name="rating" id="star2" value="2" required>
                <label for="star2">★</label>
                <input type="radio" name="rating" id="star1" value="1" required>
                <label for="star1">★</label>
            </div>
            <div class="mb-3">
                <textarea name="review_text" id="review_text" class="form-control" rows="3"
                    placeholder="Nhập đánh giá của bạn tại đây" required style="resize: none;" maxlength="200"></textarea>

            </div>
            <button type="submit" name="submit_review" class="btn btn-success"
                style="background-color: rgb(25,135,84); border-color: rgb(25,135,84);margin: auto; display: block; width: fit-content;">Gửi
                Đánh Giá</button>
        </form>
    </div>
<?php endif; ?>

<div class="reviews mt-5">
    <h4>Các Đánh Giá (<?= $total_reviews; ?>)</h4>
    <p>Đánh: <?= number_format($average_rating, 1); ?> / 5</p>

    <?php if ($total_reviews > 0): ?>
        <ul class="list-unstyled">
            <?php foreach ($reviews as $review): ?>
                <li class="review-item">
                    <strong><?= htmlspecialchars($review['username']); ?></strong>
                    <div class=""><?= str_repeat('⭐', $review['rating']); ?></div>
                    <p class="mt-2"><?= htmlspecialchars($review['review_text']); ?></p>
                    <p class="mt-2 text-muted" style="font-size:15px"><?= date('d-m-Y', strtotime($review['created_at'])); ?>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="no-reviews">Không Có Đánh Giá Nào.</p>
    <?php endif; ?>
</div>


<?php include '../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainImage = document.querySelector('.main-product-image');
        const galleryImages = document.querySelectorAll('.gallery-image');

        galleryImages.forEach(image => {
            image.addEventListener('click', function () {

                galleryImages.forEach(img => img.classList.remove('active'));


                this.classList.add('active');


                const fullImageSrc = this.getAttribute('data-full-image');
                mainImage.src = fullImageSrc;
            });


            if (galleryImages[0] === image) {
                image.classList.add('active');
            }
        });
    });
</script>
<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
    }

    .rating input {
        display: none;
    }

    .rating label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.3s;
    }

    .rating label:hover,
    .rating label:hover~label,
    .rating input:checked~label {
        color: #ffc107;
    }
</style>

</body>

</html>