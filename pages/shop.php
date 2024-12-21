<?php

include '../partials/header.php';

if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];

    if (isAdmin($pdo, $user_id)) {
        echo "<script>
            window.location.href = '" . BASE_URL . "pages/admin.php';
        </script>";
        exit;
    }
}

$category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';

// Base SQL query
$sql_products = "SELECT product.*, category.name AS category_name, 
                 (SELECT image_path FROM product_images 
                  WHERE product_id = product.id_product AND is_primary = 1 
                  LIMIT 1) AS primary_image
                 FROM product
                 INNER JOIN category ON product.category_id = category.id_category
                 WHERE product.is_active = 1";

$params = [];

// Add category filter
if ($category_id > 0) {
    $sql_products .= " AND product.category_id = :category_id";
    $params['category_id'] = $category_id;
}

// Add search filter
if (!empty($search_query)) {
    $sql_products .= " AND (product.name LIKE :search OR category.name LIKE :search)";
    $params['search'] = "%$search_query%";
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $sql_products .= " ORDER BY product.price ASC";
        break;
    case 'price_desc':
        $sql_products .= " ORDER BY product.price DESC";
        break;
    case 'name_asc':
        $sql_products .= " ORDER BY product.name ASC";
        break;
    case 'name_desc':
        $sql_products .= " ORDER BY product.name DESC";
        break;
}

// Pagination setup
$products_per_page = 9;
$current_page = isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
$offset = ($current_page - 1) * $products_per_page;

// Get total products count
$total_products_sql = "SELECT COUNT(*) FROM product WHERE is_active = 1";
if ($category_id > 0) {
    $total_products_sql .= " AND category_id = :category_id";
}
if (!empty($search_query)) {
    $total_products_sql .= " AND (name LIKE :search OR category_id IN (SELECT id_category FROM category WHERE name LIKE :search))";
}
$total_products_stmt = $pdo->prepare($total_products_sql);
if ($category_id > 0) {
    $total_products_stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
}
if (!empty($search_query)) {
    $total_products_stmt->bindValue(':search', "%$search_query%", PDO::PARAM_STR);
}
$total_products_stmt->execute();
$total_products_count = $total_products_stmt->fetchColumn();
$total_pages = ceil($total_products_count / $products_per_page);

// Add pagination to main query
$sql_products .= " LIMIT :limit OFFSET :offset";

// Prepare and execute main products query
$stmt_products = $pdo->prepare($sql_products);

// Bind parameters
foreach ($params as $key => $value) {
    $stmt_products->bindValue(":$key", $value);
}
$stmt_products->bindValue(':limit', $products_per_page, PDO::PARAM_INT);
$stmt_products->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_products->execute();
$products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories
$sql_categories = "SELECT * FROM category WHERE is_active = 1";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .category-list {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .category-link {
        color: rgb(25, 135, 84);
        text-decoration: none;
    }



    .product-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-img {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        height: 200px;
        object-fit: cover;
    }

    .product-title {
        color: rgb(25, 135, 84);
        font-weight: bold;
    }

    .btn-primary {
        background-color: rgb(25, 135, 84);
        border-color: rgb(25, 135, 84);
    }

    .btn-primary:hover {
        background-color: rgb(38, 100, 136);
        border-color: rgb(38, 100, 136);
    }

    .hero-banner {
        background-image: url('../assets/images/shop-banner.jpg');
        background-size: cover;
        background-position: center;
        color: #fff;
        text-align: center;
        padding: 100px 0;
        border-radius: 20px;
        margin-bottom: 40px;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: bold;
        color: rgb(25, 135, 84);
    }

    .pagination {
        justify-content: center;
        margin-top: 20px;
    }

    .page-item.active .page-link {
        background-color: rgb(25, 135, 84);
        border-color: rgb(25, 135, 84);
        color: white;
    }

    .page-item .page-link {
        color: rgb(25, 135, 84);
        border: 1px solid rgb(25, 135, 84);
    }

    .page-item .page-link:hover {
        background-color: rgb(38, 100, 136);
        color: white;
    }

    .list-group-item:hover {
        background-color: rgba(25, 135, 84, 0.1);
    }
</style>

<div class="container mt-5">
    <div class="hero-banner p-1">
        <h1 class="hero-title">Chào Mừng Bạn Tới Mini Market</h1>
        <p class="lead" style="color:black">Hãy lựa chọn những thứ bạn cần trong đây.</p>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-4 col-12">
            <div class="category-list">
                <h3 class="mb-4">Danh mục</h3>
                <ul class="list-group" id="category-list">
                    <li class="list-group-item d-flex align-items-center <?= !$category_id ? 'bg-success text-light' : ''; ?>"
                        onclick="window.location.href='<?= BASE_URL; ?>pages/shop.php';" style="cursor: pointer;">
                        <a class="<?= !$category_id ? 'text-light' : ''; ?> category-link">Tất cả</a>
                    </li>
                    <?php
                    $categories_per_page = 5;
                    $total_categories = count($categories);
                    $shown_categories = array_slice($categories, 0, $categories_per_page);
                    foreach ($shown_categories as $category): ?>
                        <li class="list-group-item d-flex align-items-center <?= $category_id == $category['id_category'] ? 'bg-success' : ''; ?>"
                            onclick="window.location.href='<?= BASE_URL; ?>pages/shop.php?category_id=<?= $category['id_category']; ?>';"
                            style="cursor: pointer;">
                            <img src="../assets/uploads/<?= $category['image']; ?>" alt="All" class="me-2"
                                onerror="this.src='https://placehold.co/30x30'" style="width: 30px; height: 30px;">
                            <a class="<?= $category_id == $category['id_category'] ? 'text-light' : ''; ?> category-link"
                                <?= $category_id == $category['id_category'] ? 'style="color: white;"' : ''; ?>><?= $category['name']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($total_categories > $categories_per_page): ?>
                    <div class="text-center mt-3">
                        <button class="btn btn-primary" id="load-more-categories">Xem thêm</button>
                    </div>
                <?php endif; ?>
            </div>
 

        </div>


        <div class="col-lg-9 col-md-8 col-12">

            <form action="<?php echo BASE_URL; ?>pages/shop.php" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm" name="search"
                        value="<?= htmlspecialchars($search_query) ?>">
                    <select name="sort" class="form-select">
                        <option value="">Sắp xếp theo</option>
                        <option value="price_asc" <?= isset($_GET['sort']) && $_GET['sort'] == 'price_asc' ? 'selected' : ''; ?>>Giá: Thấp đến Cao</option>
                        <option value="price_desc" <?= isset($_GET['sort']) && $_GET['sort'] == 'price_desc' ? 'selected' : ''; ?>>Giá: Cao đến Thấp</option>
                        <option value="name_asc" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_asc' ? 'selected' : ''; ?>>Tên: A đến Z</option>
                        <option value="name_desc" <?= isset($_GET['sort']) && $_GET['sort'] == 'name_desc' ? 'selected' : ''; ?>>Tên: Z đến A</option>
                        <option value="best_sellers" <?= isset($_GET['sort']) && $_GET['sort'] == 'best_sellers' ? 'selected' : ''; ?>>Sản phẩm mua nhiều nhất</option>
                    </select>
                    <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                </div>
            </form>

            <div id="products" class="row">
                <?php if (empty($products)): ?>
                    <div class="col-12">
                        <p>Không tìm thấy sản phẩm nào.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex">
                            <div class="product-card w-100 d-flex flex-column">
                                <img src="../<?= !empty($product['primary_image']) ? $product['primary_image'] : $product['image']; ?>"
                                    class="card-img-top product-img" alt="Product Image"
                                    style="width: 100%; height: 280px; object-fit: cover;"
                                    onerror="this.src='https://via.placeholder.com/150'">
                                <div class="card-body d-flex flex-column p-4 flex-grow-1">
                                    <p class="text-muted mb-1"><?= $product['category_name']; ?></p>
                                    <h5 class="product-title mb-3"><?= $product['name']; ?></h5>
                                    <p class="card-text fw-bold mb-3" style="font-size: 1.2rem;">
                                        <?= number_format($product['price'], 0, ',', '.') . ' ₫'; ?>
                                    </p>
                                    <div class="mt-auto">
                                        <button
                                            class="btn <?= $product['quantity'] <= 0 ? 'btn-outline-secondary' : 'btn-primary'; ?> w-100"
                                            <?= $product['quantity'] <= 0 ? 'disabled' : ''; ?>
                                            onclick="window.location.href='details.php?id=<?= $product['id_product']; ?>'">
                                            <?= $product['quantity'] <= 0 ? 'Hết hàng' : 'Xem ngay'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?=
                        (isset($_GET['category_id']) ? '&category_id=' . urlencode($category_id) : '') .
                        ($i === $current_page) ? 'active' : ''; ?>">
                        <a class="page-link"                                                   
                        href="<?php echo BASE_URL; ?>pages/shop.php?page=<?= $i; ?>&category_id=<?= urlencode($category_id); ?>
                        &search=<?= urlencode($search_query); ?>&sort=<?= urlencode($_GET['sort'] ?? ''); ?>">
                        <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

</div>





<?php include '../partials/footer.php'; ?>
<script>
                document.getElementById('load-more-categories').addEventListener('click', function () {
                    const allCategories = <?= json_encode($categories); ?>;
                    const categoriesPerPage = <?= $categories_per_page; ?>;
                    const categoryList = document.getElementById('category-list');
                    let currentCount = categoryList.childElementCount - 1; // Exclude the "Tất cả" item

                    if (currentCount < allCategories.length) {
                        const nextCategories = allCategories.slice(currentCount, currentCount + categoriesPerPage);
                        nextCategories.forEach(category => {
                            const listItem = document.createElement('li');
                            listItem.className = 'list-group-item d-flex align-items-center ' + (category.id_category === <?=$category_id?> ? 'bg-success' : '');
                            listItem.onclick = function () {
                                window.location.href = '<?= BASE_URL; ?>pages/shop.php?category_id=' + category.id_category;
                            };
                            listItem.style.cursor = 'pointer';
                            listItem.innerHTML = `
                            <img src="../assets/uploads/${category.image}" alt="All" class="me-2"
                                onerror="this.src='https://placehold.co/30x30'" style="width: 30px; height: 30px;">
                            <a href="" class="category-link" style="${category.id_category === <?=$category_id?> ? 'color: white;' : ''}">${category.name}</a>
                        `;
                            categoryList.appendChild(listItem);
                        });

                      
                        if (currentCount + categoriesPerPage >= allCategories.length) {
                            this.style.display = 'none';
                        }
                    }
                });
            </script>

</body>

</html>