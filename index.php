<?php


include 'partials/header.php';


if (isset($_SESSION['id_user'])) {

    $user_id = $_SESSION['id_user'];


    if (isAdmin($pdo, $user_id)) {

        echo "<script>
            window.location.href = '" . BASE_URL . "pages/admin.php';
        </script>";
        exit;
    }

}

$stmt_new_arrivals = $pdo->prepare("
    SELECT product.*, category.name AS category_name, 
           (SELECT image_path FROM product_images 
            WHERE product_id = product.id_product AND is_primary = 1 
            LIMIT 1) AS primary_image
    FROM product
    INNER JOIN category ON product.category_id = category.id_category
    WHERE product.is_active = 1
      And product.quantity >=1
    ORDER BY product.created_at DESC 
    LIMIT 8
");
$stmt_new_arrivals->execute();
$new_arrivals = $stmt_new_arrivals->fetchAll(PDO::FETCH_ASSOC);

// Fetch hot products
$stmt_hot_products = $pdo->prepare("
    SELECT product.*, category.name AS category_name, 
           (SELECT image_path FROM product_images 
            WHERE product_id = product.id_product AND is_primary = 1 
            LIMIT 1) AS primary_image
    FROM product
    INNER JOIN category ON product.category_id = category.id_category
    WHERE product.is_active = 1
      And product.quantity >=1
    LIMIT 8
");
$stmt_hot_products->execute();
$hot_products = $stmt_hot_products->fetchAll(PDO::FETCH_ASSOC);

// Fetch special products
$stmt_sp_products = $pdo->prepare("
    SELECT product.*, category.name AS category_name, 
           (SELECT image_path FROM product_images 
            WHERE product_id = product.id_product AND is_primary = 1 
            LIMIT 1) AS primary_image
    FROM product
    INNER JOIN category ON product.category_id = category.id_category
    WHERE product.is_active = 1
      And product.quantity >=1
    LIMIT 4
");
$stmt_sp_products->execute();
$sp_products = $stmt_sp_products->fetchAll(PDO::FETCH_ASSOC);

?>



<div id="mainBanner" class="carousel slide flip-carousel" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="banner-slide" style="background-image: url('assets/images/img1.jpeg')">
            </div>
        </div>
        <div class="carousel-item">
            <div class="banner-slide" style="background-image: url('assets/images/img2.jpeg')">
            </div>
        </div>
        <div class="carousel-item">
            <div class="banner-slide" style="background-image: url('assets/images/img3.jpeg')">
            </div>
        </div>
        <div class="carousel-item">
            <div class="banner-slide" style="background-image: url('assets/images/img4.jpeg')">
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainBanner" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainBanner" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>



<!-- Benefits Section -->
<section class="benefits-section mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h5>Giao Hàng Miễn Phí</h5>
                    <p>Cho đơn hàng từ 500.000đ</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h5>Cam Kết Chất Lượng</h5>
                    <p>Hoàn tiền nếu không hài lòng</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h5>Hỗ Trợ 24/7</h5>
                    <p>Tư vấn miễn phí</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-sync"></i>
                    </div>
                    <h5>Đổi Trả Dễ Dàng</h5>
                    <p>Trong vòng 24h</p>
                </div>
            </div>
        </div>
    </div>
</section>







<section class="container my-5">
    <h2 class="text-center mb-4">Danh Mục Sản Phẩm</h2>
    <div class="row g-4">
        <?php
        $maxCategories = 8;
        $categoryCount = count($categories);
        $shownCategories = min($categoryCount, $maxCategories);
        foreach (array_slice($categories, 0, $shownCategories) as $category): ?>

            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>pages/shop.php?category_id=<?= $category['id_category']; ?>">
                    <div class="card h-100 shadow-sm border rounded overflow-hidden">

                        <div style="position: relative; height: 200px;">
                            <img src="assets/uploads/<?= $category['image']; ?>" class="card-img-top"
                                style="height: 100%; object-fit: cover;" alt="<?= $category['name']; ?>" onerror="this.src='https://placehold.co/150x150'">
                            <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold text-center"
                                style="background-color: rgba(0, 0, 0, 0.5); width: 100%; padding: 10px 0;">
                                <?= $category['name']; ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>


        <?php if ($categoryCount > $maxCategories): ?>
            <div class="col-12 text-center mt-4">
                <a href="#" class="btn btn-success">Thêm danh mục</a>
            </div>
        <?php endif; ?>
    </div>
</section>




<section class="container mb-5">
    <h2 class="text-center mb-4">Sản Phẩm Nổi Bật</h2>
    <div class="row g-4">
        <?php
        $maxProducts = 8;
        $productCount = count($hot_products);
        $shownProducts = min($productCount, $maxProducts);
        $defaultImage = 'https://via.placeholder.com/150';

        foreach (array_slice($hot_products, 0, $shownProducts) as $product):
            $image = !empty($product['primary_image']) ? $product['primary_image'] : $product['image'];
            $image = @getimagesize($image) ? $image : $defaultImage;
            ?>

            <div class="col-md-3">
                <div class="card product-card">
                    <img src="<?= $image; ?>" class="card-img-top" alt="Product">
                    <div class="card-body">
                        <h5 class="card-title"><?= $product['name']; ?></h5>
                        <p class="card-text text-success fw-bold">
                            <?= number_format($product['price'], 0, ',', '.') . ' ₫'; ?></p>
                        <button class="btn btn-success w-100"
                            onclick="window.location.href='pages/details.php?id=<?= $product['id_product']; ?>'">Chi
                            Tiết</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($productCount > $maxProducts): ?>
        <div class="text-center mt-4">
            <a href="pages/all_products.php" class="btn btn-primary">Xem Thêm</a>
        </div>
    <?php endif; ?>
</section>







<?php include 'partials/footer.php'; ?>


</body>

</html>