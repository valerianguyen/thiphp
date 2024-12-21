<?php

require_once '../controllers/paginationAdmin.php';

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['add_product'])) {

        if (!isset($_FILES['images']) || count($_FILES['images']['name']) < 1 || count($_FILES['images']['name']) > 7) {
            echo "You must upload between 1 and 7 images.";
            exit;
        }

        $product_name = $_POST['name'];
        $shortdesc = $_POST['shortdesc'];
        $longdesc = $_POST['longdesc'];
        $price = $_POST['price'];
        $Inprice = $_POST['Inprice'];
        $category_id = $_POST['category_id'];
        $quantity = $_POST['quantity'];

        // Begin a transaction
        $pdo->beginTransaction();

        try {
            // Insert product
            $sql_add_product = "INSERT INTO product (name, shortdesc, longdesc, price, category_id, Inprice,quantity) 
                                VALUES (:name, :shortdesc, :longdesc, :price, :category_id, :Inprice,:quantity)";
            $stmt_add_product = $pdo->prepare($sql_add_product);
            $stmt_add_product->execute([
                'name' => $product_name,
                'shortdesc' => $shortdesc,
                'longdesc' => $longdesc,
                'price' => $price,
                'category_id' => $category_id,
                'Inprice' => $Inprice,
                'quantity' => $quantity
            ]);

            // Get the ID of the newly inserted product
            $product_id = $pdo->lastInsertId();

            // Process image uploads
            $upload_dir = '../assets/uploads/';
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

            // Prepare image upload statement
            $sql_add_image = "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (:product_id, :image_path, :is_primary)";
            $stmt_add_image = $pdo->prepare($sql_add_image);

            // Process each uploaded image
            foreach ($_FILES['images']['error'] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['images']['tmp_name'][$key];
                    $file_type = $_FILES['images']['type'][$key];
                    $file_name = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                    $image_path = $upload_dir . $file_name;

                    // Validate file type
                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception("Invalid file type for image " . ($key + 1));
                    }

                    // Move uploaded file
                    if (!move_uploaded_file($file_tmp, $image_path)) {
                        throw new Exception("Failed to move uploaded file");
                    }

                    // Determine if this is the primary image (first image)
                    $is_primary = ($key === 0) ? 1 : 0;

                    // Insert image path into product_images
                    $stmt_add_image->execute([
                        'product_id' => $product_id,
                        'image_path' => str_replace('../', '', $image_path),
                        'is_primary' => $is_primary
                    ]);
                }
            }

            // Commit the transaction
            $pdo->commit();

            $_SESSION['message'] = "Thêm Sản Phẩm Thành Công !";

            echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=products&page=' . $current_page . '";</script>';
            exit;


        } catch (Exception $e) {
            // Rollback the transaction on error
            $pdo->rollBack();
            $_SESSION['error'] = "Error: " . $e->getMessage();
            echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=products";</script>';
            exit;



        }
    }

    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];

        try {

            $pdo->beginTransaction();


            $sql_delete_cart = "DELETE FROM cart WHERE product_id = :product_id";
            $stmt_delete_cart = $pdo->prepare($sql_delete_cart);
            $stmt_delete_cart->execute(['product_id' => $product_id]);


            $sql_deactivate_product = "UPDATE product SET is_active = 0 WHERE id_product = :id_product";
            $stmt_deactivate_product = $pdo->prepare($sql_deactivate_product);
            $stmt_deactivate_product->execute(['id_product' => $product_id]);


            $pdo->commit();

            $_SESSION['message'] = "Xóa thành công!";
            echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=products&page=' . $current_page . '";</script>';
            exit;


        } catch (PDOException $e) {

            $pdo->rollBack();
            echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=products";</script>';
            exit;


        }
    }
}



$pagination_result = fetchPaginatedProducts($pdo, $current_page, $items_per_page);
$products = $pagination_result['products'];
$total_pages = $pagination_result['total_pages'];
$total_products = $pagination_result['total_products'];
?>


<style>
    .btn-custom {
        background-color: rgb(102, 196, 206);
        border-color: rgb(102, 196, 206);
        color: white;
    }

    .btn-custom:hover {
        background-color: rgba(48, 120, 156, 0.9);
        border-color: rgba(48, 120, 156, 0.9);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(48, 120, 156, 0.05);
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .form-control,
    .form-select {
        min-width: 100%;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }


    .low-stock {
        color: red;
        font-weight: bold;
    }

    .out-of-stock {
        color: darkred;
        font-weight: bold;
    }

    .warning-text {
        color: red;
        font-style: italic;
    
    }
    .pagination {
        justify-content: center;
        margin-top: 20px;
    }

    .page-item.active .page-link {
        background-color: rgb(25,135,84);
        border-color: rgb(25,135,84);
        color: white;
    }

    .page-item .page-link {
        color: rgb(25,135,84);
        border: 1px solid rgb(25,135,84);
    }

    .page-item .page-link:hover {
        background-color: rgb(38, 100, 136);
        color: white;
    }

    .list-group-item:hover {
        background-color: rgba(25, 135, 84, 0.1);
    }
</style>
<h2 class="mb-4">Quản lý sản phẩm</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['message']; ?>
        <?php unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
<form method="post" action="" enctype="multipart/form-data" class="mb-4 bg-light p-4 shadow-sm rounded">
    <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Tên sản phẩm" required>
    </div>
    <div class="mb-3">
        <input type="text" name="shortdesc" class="form-control" placeholder="Nội dung ngắn" required>
    </div>
    <div class="mb-3">
        <textarea name="longdesc" class="form-control" placeholder="Nội dung dài" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <input type="number" name="price" class="form-control" placeholder="Giá bán" required>
        <input type="number" name="Inprice" class="form-control" placeholder="Giá nhập" required>
        <input type="number" name="quantity" class="form-control" placeholder="Số lượng" required>

    </div>
    <div class="mb-3">
        <select name="category_id" class="form-select" required>
            <option value="">Chọn danh mục</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id_category']; ?>">
                    <?= htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

    </div>
    <div class="mb-3">
        <label class="form-label">Ảnh sản phẩm (1-7 Hình ảnh, tối thiểu là 1)</label>
        <div class="row">
            <?php for ($i = 1; $i <= 7; $i++): ?>
                <div class="col-md-4 mb-2">
                    <div class="border p-2 rounded">
                        <label for="image<?= $i ?>" class="form-label">
                            Hình ảnh <?= $i ?>
                            <?= ($i === 1) ? '<span class="text-danger">(Bắt buộc)</span>' : '' ?>
                        </label>
                        <input type="file" name="images[]" id="image<?= $i ?>" class="form-control"
                            accept="image/jpeg, image/png, image/jpg" <?= ($i === 1) ? 'required' : '' ?>>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <button type="submit" name="add_product" class="btn btn-custom">Thêm sản phẩm</button>
</form>



<?php if (count($products) > 0): ?>
    <table class="table table-hover table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Danh mục</th>
                <th>Giá bán</th>
                <th>Giá nhập</th>
                <th>Số Lượng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php if ($product['primary_image']): ?>
                            <img src="<?php echo BASE_URL . $product['primary_image']; ?>"
                                alt="<?= htmlspecialchars($product['name']); ?>" style="width: 50px; height: auto;">
                        <?php else: ?>
                            <img src="<?php echo BASE_URL; ?>assets/uploads/default.jpg" alt="No Image"
                                style="width: 50px; height: auto;">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']); ?></td>
                    <td><?= htmlspecialchars($product['category_name']) ?? 'No Category'; ?></td>
                    <td><?= number_format($product['price'], 0, ',', '.') . ' ₫'; ?></td>
                    <td><?= number_format($product['Inprice'], 0, ',', '.') . ' ₫'; ?></td>
                    <td
                        class="<?= $product['quantity'] == 0 ? 'out-of-stock' : ($product['quantity'] < 10 ? 'low-stock' : ''); ?>">
                        <?= htmlspecialchars($product['quantity']); ?>
                        <?php if ($product['quantity'] == 0): ?>
                            <span class="warning-text"> - Sản phẩm đã hết hàng</span>
                        <?php elseif ($product['quantity'] < 10): ?>
                            <span class="warning-text"> - Sản phẩm gần hết hàng</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id_product']; ?>" class="btn btn-warning btn-sm"
                            onclick="return confirm('Bạn có chắc chắn muốn sửa sản phẩm này không?');">Sửa</a>

                        <form method="post" style="display:inline;"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                            <input type="hidden" name="product_id" value="<?= $product['id_product']; ?>">
                            <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Không có sản phẩm nào.</p>
<?php endif; ?>

<?php if ($total_pages > 1): ?>
<nav aria-label="Category pagination" class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $current_page) ? 'active' : ''; ?>">
                <a class="page-link" href="?section=products&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

