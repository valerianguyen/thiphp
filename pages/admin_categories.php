<?php

require_once '../controllers/paginationAdmin.php';
$upload_dir = '../assets/uploads/';
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 5;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $category_name = $_POST['category_name'];
        $image = null;

        // Handle image upload for new category
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            $file = $_FILES['category_image'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $image = $new_filename;
                }
            }
        }

        $sql_add_category = "INSERT INTO category (name, image) VALUES (:name, :image)";
        $stmt_add_category = $pdo->prepare($sql_add_category);
        $stmt_add_category->execute(['name' => $category_name, 'image' => $image]);
        $_SESSION['message'] = "Thêm thành công!";
        echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=category&page=' . $current_page . '";</script>';
        exit;
    } elseif (isset($_POST['edit_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];

        // Get current image path
        $sql_get_image = "SELECT image FROM category WHERE id_category = :id";
        $stmt_get_image = $pdo->prepare($sql_get_image);
        $stmt_get_image->execute(['id' => $category_id]);
        $current_image = $stmt_get_image->fetch(PDO::FETCH_ASSOC)['image'];

        // Handle new image upload
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            $file = $_FILES['category_image'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Delete old image if it exists
                    if ($current_image && file_exists($upload_dir . $current_image)) {
                        unlink($upload_dir . $current_image);
                    }
                    $current_image = $new_filename;
                }
            }
        }

        $sql_edit_category = "UPDATE category SET name = :name, image = :image WHERE id_category = :id";
        $stmt_edit_category = $pdo->prepare($sql_edit_category);
        $stmt_edit_category->execute([
            'name' => $category_name,
            'image' => $current_image,
            'id' => $category_id
        ]);

        $_SESSION['message'] = "Sửa thành công!";
        echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=category&page=' . $current_page . '";</script>';
        exit;
    } elseif (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];

        $sql_check_products = "SELECT COUNT(*) AS active_products 
                               FROM product 
                               WHERE category_id = :category_id AND is_active = 1";
        $stmt_check_products = $pdo->prepare($sql_check_products);
        $stmt_check_products->execute(['category_id' => $category_id]);
        $active_products = $stmt_check_products->fetch(PDO::FETCH_ASSOC)['active_products'];

        if ($active_products == 0) {
            // Get image path before deleting category
            $sql_get_image = "SELECT image FROM category WHERE id_category = :id";
            $stmt_get_image = $pdo->prepare($sql_get_image);
            $stmt_get_image->execute(['id' => $category_id]);
            $image = $stmt_get_image->fetch(PDO::FETCH_ASSOC)['image'];

            // Delete the category
            $sql_delete_category = "UPDATE category SET is_active = 0 WHERE id_category = :id";
            $stmt_delete_category = $pdo->prepare($sql_delete_category);
            $stmt_delete_category->execute(['id' => $category_id]);

            // Delete the image file if it exists
            if ($image && file_exists($upload_dir . $image)) {
                unlink($upload_dir . $image);
            }

            $_SESSION['message'] = "Xóa thành công!";
            echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=category";</script>';
            exit;
        } else {
            $_SESSION['message'] = "Lỗi khi xóa!";
            echo '<script>window.location.href = "/sieuthi/pages/admin.php?section=category";</script>';
            exit;
        }
    }
}



$pagination_result = fetchPaginatedCategories($pdo, $current_page, $items_per_page);
$categories = $pagination_result['categories'];
$total_pages = $pagination_result['total_pages'];
$total_categories = $pagination_result['total_categories'];
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

    .input-group .form-control {
        border-radius: 20px 0 0 20px;
    }

    .input-group .btn-primary {
        border-radius: 0 20px 20px 0;
    }

    .form-control {
        min-width: 100%;
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


<h2 class="mb-4">Quản lý danh mục</h2>
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

<form method="post" action="" class="mb-4" enctype="multipart/form-data">
    <div class="input-group mb-3" style="max-width: 600px;">
        <input type="text" name="category_name" class="form-control" placeholder="Thêm danh mục mới" required>
        <input type="file" name="category_image" class="form-control" accept="image/*" required>
        <button type="submit" name="add_category" class="btn btn-primary" style="border-radius: 0 20px 20px 0;">Thêm
            danh mục</button>
    </div>
</form>

<table class="table table-hover table-bordered mt-3">
    <thead class="table-light">
        <tr>
            <th>Tên danh mục</th>
            <th>Hình ảnh</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody id="categories-container">
        <?php foreach ($categories as $category): ?>
            <?php
            $category_id = $category['id_category'];
            $sql_check_products = "SELECT COUNT(*) AS active_products 
                           FROM product 
                           WHERE category_id = :category_id AND is_active = 1";
            $stmt_check_products = $pdo->prepare($sql_check_products);
            $stmt_check_products->execute(['category_id' => $category_id]);
            $active_products = $stmt_check_products->fetch(PDO::FETCH_ASSOC)['active_products'];
            ?>

            <tr>
                <td>
                    <form method="post" enctype="multipart/form-data" style="display:flex; align-items: center;">
                        <input type="hidden" name="category_id" value="<?= $category['id_category']; ?>">
                        <input type="text" name="category_name" class="form-control me-2" value="<?= $category['name']; ?>"
                            required>
                </td>
                <td>
                    <?php if ($category['image']): ?>
                        <img src="<?= $upload_dir . $category['image']; ?>" alt="Category Image"
                            style="max-width: 50px; max-height: 50px;">
                    <?php endif; ?>
                    <input type="file" name="category_image" class="form-control mt-2" accept="image/*">
                </td>
                <td>
                    <button type="submit" name="edit_category" class="btn btn-custom me-2">Sửa</button>
                    </form>
                    <form method="post" style="display:inline;" onsubmit="return confirmDelete(<?= $category['id_category']; ?>)">
                        <input type="hidden" name="category_id" value="<?= $category['id_category']; ?>">
                        <button type="submit" name="delete_category" class="btn btn-danger" <?= ($active_products > 0) ? 'disabled' : ''; ?>>Xóa</button>
                    </form>
                    <script>
                        function confirmDelete(categoryId) {
                            return confirm("Bạn có chắc chắn muốn xóa danh mục này không?");
                        }
                    </script>

                    <?php if ($active_products > 0): ?>
                        <small class="text-muted">Không thể xóa: <?= $active_products; ?> sản phẩm đang được liên kết</small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php if ($total_pages > 1): ?>
    <nav aria-label="Category pagination" class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?section=category&page=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>