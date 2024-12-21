<?php

include '../partials/headerAdmin.php';

$product_id = $_GET['id'];


$sql_fetch_product = "SELECT * FROM product WHERE id_product = :id_product";
$stmt_product = $pdo->prepare($sql_fetch_product);
$stmt_product->execute(['id_product' => $product_id]);
$product = $stmt_product->fetch(PDO::FETCH_ASSOC);


$sql_fetch_categories = "SELECT * FROM category";
$stmt_categories = $pdo->prepare($sql_fetch_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);


$sql_fetch_product_images = "SELECT * FROM product_images WHERE product_id = :product_id";
$stmt_product_images = $pdo->prepare($sql_fetch_product_images);
$stmt_product_images->execute(['product_id' => $product_id]);
$product_images = $stmt_product_images->fetchAll(PDO::FETCH_ASSOC);



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['name'];
    $shortdesc = $_POST['shortdesc'];
    $longdesc = $_POST['longdesc'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $image_path = $product['image'];
    $Inprice = $_POST['Inprice'];
    $quantity = $_POST['quantity'];


    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['image']['type'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $upload_dir = 'assets/uploads/';
        $new_image_path = $upload_dir . basename($file_name);


        if (in_array($file_type, $allowed_types)) {
            move_uploaded_file($file_tmp, $new_image_path);
            $image_path = $new_image_path;
        } else {
            echo "Only JPG, JPEG, and PNG formats are allowed.";
            exit;
        }
    }



    if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
        $delete_ids = $_POST['delete_images'];
        $sql_delete_images = "DELETE FROM product_images WHERE id_product_image IN (" .
            implode(',', array_fill(0, count($delete_ids), '?')) . ")";
        $stmt_delete = $pdo->prepare($sql_delete_images);
        $stmt_delete->execute($delete_ids);
    }


    $sql_reset_primary = "UPDATE product_images SET is_primary = 0 WHERE product_id = :product_id";
    $stmt_reset = $pdo->prepare($sql_reset_primary);
    $stmt_reset->execute(['product_id' => $product_id]);


    if (isset($_POST['primary_image'])) {
        $sql_set_primary = "UPDATE product_images SET is_primary = 1 WHERE id_product_image = :image_id";
        $stmt_primary = $pdo->prepare($sql_set_primary);
        $stmt_primary->execute(['image_id' => $_POST['primary_image']]);
    }


    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = '../assets/uploads/';
        $upload_dir_2 = 'assets/uploads/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];


        $current_image_count = count($product_images);
        $new_image_count = count($_FILES['images']['name']);

        if ($current_image_count + $new_image_count > 7) {
            echo "Maximum 7 images allowed.";
            exit;
        }


        for ($i = 0; $i < $new_image_count; $i++) {
            // Add this debug check
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                echo "File upload error: " . $_FILES['images']['error'][$i];
                continue;
            }

            $file_type = $_FILES['images']['type'][$i];
            $file_tmp = $_FILES['images']['tmp_name'][$i];
            $file_name = $_FILES['images']['name'][$i];


            if ($_FILES['images']['size'][$i] > 5 * 1024 * 1024) {
                echo "File " . $file_name . " is too large. Maximum 5MB allowed.";
                continue;
            }

            if (in_array($file_type, $allowed_types)) {
                $uniqueID = uniqid();

                $new_image_path = $upload_dir . $uniqueID . '_' . basename($file_name);
                $new_image_path_2 = $upload_dir_2 . $uniqueID . '_' . basename($file_name);

                if (move_uploaded_file($file_tmp, $new_image_path)) {

                    $sql_insert_image = "INSERT INTO product_images (product_id, image_path) VALUES (:product_id, :image_path)";
                    $stmt_insert = $pdo->prepare($sql_insert_image);
                    $stmt_insert->execute([
                        'product_id' => $product_id,
                        'image_path' => $new_image_path_2
                    ]);
                } else {
                    echo "Failed to move uploaded file: " . $file_name;
                }
            } else {
                echo "Invalid file type for: " . $file_name;
            }
        }

        $sql_check_primary = "SELECT COUNT(*) FROM product_images WHERE product_id = :product_id AND is_primary = 1";
        $stmt_check_primary = $pdo->prepare($sql_check_primary);
        $stmt_check_primary->execute(['product_id' => $product_id]);
        $primary_count = $stmt_check_primary->fetchColumn();

        if ($primary_count == 0) {
            // Get the first uploaded image's ID
            $sql_get_first_image = "SELECT id_product_image FROM product_images WHERE product_id = :product_id ORDER BY id_product_image ASC LIMIT 1";
            $stmt_get_first_image = $pdo->prepare($sql_get_first_image);
            $stmt_get_first_image->execute(['product_id' => $product_id]);
            $first_image_id = $stmt_get_first_image->fetchColumn();

            if ($first_image_id) {
                $sql_set_primary = "UPDATE product_images SET is_primary = 1 WHERE id_product_image = :image_id";
                $stmt_set_primary = $pdo->prepare($sql_set_primary);
                $stmt_set_primary->execute(['image_id' => $first_image_id]);
            }
        }
    }

    
    
    $sql_update_product = "UPDATE product SET name = :name, shortdesc = :shortdesc, longdesc = :longdesc, 
                           price = :price, category_id = :category_id, Inprice = :Inprice, quantity =:quantity" .
        ($image_path ? ", image = :image" : "") .
        " WHERE id_product = :id_product";

    $stmt_update_product = $pdo->prepare($sql_update_product);


    $params = [
        'name' => $product_name,
        'shortdesc' => $shortdesc,
        'longdesc' => $longdesc,
        'price' => $price,
        'category_id' => $category_id,
        'id_product' => $product_id,
        'Inprice' => $Inprice,
        'quantity' =>$quantity
    ];
    if ($image_path) {
        $params['image'] = $image_path;
    }

    $stmt_update_product->execute($params);


    $sql_update_cart = "UPDATE cart SET price_at_cart = :price WHERE product_id = :product_id";
    $stmt_update_cart = $pdo->prepare($sql_update_cart);
    $stmt_update_cart->execute([
        'price' => $price,
        'product_id' => $product_id
    ]);

    if ($stmt_update_product->execute($params)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cập nhật sản phẩm thành công',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'http://localhost/sieuthi/pages/admin.php?section=products';
        });
    </script>";
        exit;
    } else {
        print_r($stmt_update_product->errorInfo());
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit;
    
    
}
?>




<div class="container mt-5">
    <h2 class="text-center mb-4">Sửa sản phẩm</h2>
    <form method="post" action="" enctype="multipart/form-data" class="shadow-lg p-4 rounded"
        style="background-color: #f9f9f9;">

        <div class="mb-3">
            <label for="productName" class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" id="productName" value="<?= $product['name']; ?>"
                required>
        </div>


        <div class="mb-3">
            <label for="shortdesc" class="form-label">Nội dung ngắn</label>
            <input type="text" name="shortdesc" class="form-control" id="shortdesc"
                value="<?= $product['shortdesc']; ?>" required>
        </div>


        <div class="mb-3">
            <label for="longdesc" class="form-label">Nội dung dài</label>
            <textarea name="longdesc" class="form-control" id="longdesc" rows="4"
                required><?= $product['longdesc']; ?></textarea>
        </div>


        <div class="mb-3">
            <label for="price" class="form-label">Giá bán</label>
            <input type="number" name="price" class="form-control" id="price" value="<?= $product['price']; ?>"
                required>
        </div>

        <div class="mb-3">
            <label for="Inprice" class="form-label">Giá nhập</label>
            <input type="number" name="Inprice" class="form-control" id="Inprice" value="<?= $product['Inprice']; ?>"
                required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" name="quantity" class="form-control" id="quantity" value="<?= $product['quantity']; ?>"
                required>
        </div>


        <div class="mb-3">
            <label for="category" class="form-label">Danh mục</label>
            <select name="category_id" class="form-select" id="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id_category']; ?>" <?= $product['category_id'] == $category['id_category'] ? 'selected' : ''; ?>>
                        <?= $category['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>



        <div class="mb-3 image-upload-section">
            <label class="form-label">Hình ảnh (tối đa 7)</label>

            <?php if (!empty($product_images)): ?>
                <div class="mb-3">
                    <span>Để cập nhật hoặc thêm hình ảnh, hãy xóa tất cả hình ảnh: </span>
                    <button type="button" class="btn btn-danger" id="delete-all-images">Xóa tất cả ảnh</button>
                </div>
            <?php endif; ?>
            <div class="row g-3">
                <?php
                $existing_image_paths = array_column($product_images, 'image_path');

                for ($i = 0; $i < 7; $i++):
                    $current_image = isset($existing_image_paths[$i]) ? "/sieuthi/" . $existing_image_paths[$i] : '';
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="image-upload-card">
                            <div class="image-upload-wrapper">
                                <input type="file" name="images[]" multiple class="image-input"
                                    accept="image/jpeg, image/png, image/jpg" data-preview="preview-<?= $i ?>"
                                    <?= $current_image ? 'data-existing-image="' . $current_image . '"' : '' ?>>
                                <div class="image-preview-overlay">
                                    <img src="<?= $current_image ?: '/path/to/placeholder.png' ?>" class="image-preview"
                                        id="preview-<?= $i ?>" style="display: <?= $current_image ? 'block' : 'none' ?>;">
                                    <div class="upload-icon">
                                        <i class="upload-symbol">+</i>
                                    </div>
                                </div>
                            </div>

                            <?php if ($current_image): ?>
                                <div class="image-actions">
                                    <label class="primary-image-label">
                                        <input type="radio" name="primary_image"
                                            value="<?= $product_images[$i]['id_product_image'] ?>"
                                            <?= $product_images[$i]['is_primary'] ? 'checked' : '' ?>>
                                        Ảnh chính
                                    </label>
                                    <button type="button" class="btn-delete-image"
                                        data-image-id="<?= $product_images[$i]['id_product_image'] ?>">
                                        Xóa
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>



        <button type="submit" class="btn btn-secondary w-100">Cập nhật sản phẩm</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Remove upload button and empty slots when images exist
        function cleanupImageUpload() {
            const imageUploadCards = document.querySelectorAll('.image-upload-card');
            const existingImages = Array.from(imageUploadCards)
                .filter(card => {
                    const previewImg = card.querySelector('.image-preview');
                    return previewImg.src && !previewImg.src.includes('placeholder');
                });

            // If there are no existing images, show all slots
            if (existingImages.length === 0) {
                imageUploadCards.forEach(card => {
                    card.style.display = 'block';
                });
            } else {
                // Hide empty slots
                imageUploadCards.forEach(card => {
                    const previewImg = card.querySelector('.image-preview');
                    if (!previewImg.src || previewImg.src.includes('placeholder')) {
                        card.style.display = 'none';
                    }
                });
            }
        }

        // Call cleanup function on page load
        cleanupImageUpload();

        const imageInputs = document.querySelectorAll('.image-input');

        imageInputs.forEach(input => {
            const previewId = input.getAttribute('data-preview');
            const previewImg = document.getElementById(previewId);
            const existingImage = input.getAttribute('data-existing-image');

            input.addEventListener('change', function (e) {


                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Chỉ cho phép định dạng JPG, JPEG và PNG');
                        input.value = ''; // Clear the input
                        return;
                    }

                    // Validate file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Kích thước file không được vượt quá 5MB');
                        input.value = ''; // Clear the input
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        previewImg.src = event.target.result;
                        previewImg.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        document.querySelector('form').addEventListener('submit', function (event) {
            // Count total images (existing + new)
            const imageInputs = document.querySelectorAll('.image-input');
            let totalImages = 0;

            imageInputs.forEach(input => {
                if (input.files.length > 0 || input.getAttribute('data-existing-image')) {
                    totalImages++;
                }
            });

            if (totalImages > 7) {
                event.preventDefault();
                alert('Tối đa 7 ảnh được cho phép');
                return;
            }
        });
        const deleteAllImagesBtn = document.getElementById('delete-all-images');
        if (deleteAllImagesBtn) {
            deleteAllImagesBtn.addEventListener('click', function () {
                // Confirm deletion
                if (confirm('Bạn có chắc chắn muốn xóa tất cả ảnh? Thao tác này không thể hoàn tác.')) {
                    // Find all existing image IDs
                    const existingImageIds = Array.from(document.querySelectorAll('.btn-delete-image'))
                        .map(button => button.getAttribute('data-image-id'));

                    // Create hidden inputs for deletion
                    existingImageIds.forEach(imageId => {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_images[]';
                        hiddenInput.value = imageId;
                        this.closest('form').appendChild(hiddenInput);
                    });

                    // Reset all image upload cards
                    const imageUploadCards = document.querySelectorAll('.image-upload-card');
                    imageUploadCards.forEach((card, index) => {
                        const previewImg = card.querySelector('.image-preview');
                        const fileInput = card.querySelector('.image-input');
                        const imageActions = card.querySelector('.image-actions');
                        const primaryRadio = card.querySelector('input[type="radio"]');

                        // Reset image preview
                        previewImg.src = '/sieuthi/assets/placeholder.png';
                        previewImg.style.display = 'none';
                        fileInput.value = '';

                        // Remove primary image selection
                        if (primaryRadio) {
                            primaryRadio.checked = false;
                        }

                        // Hide image actions
                        if (imageActions) {
                            imageActions.style.display = 'none';
                        }
                    });

                    // Auto-select first image as primary when new images are uploaded
                    function autoSelectPrimaryOnUpload() {
                        const imageInputs = document.querySelectorAll('.image-input');
                        imageInputs.forEach(input => {
                            input.addEventListener('change', function (e) {
                                const file = e.target.files[0];
                                if (file) {
                                    // Find the first radio button and check it
                                    const firstPrimaryRadio = document.querySelector('input[name="primary_image"]');
                                    if (firstPrimaryRadio) {
                                        firstPrimaryRadio.checked = true;
                                    }
                                }
                            });
                        });
                    }

                    // Call the auto-select function
                    autoSelectPrimaryOnUpload();

                    // Cleanup image upload
                    cleanupImageUpload();
                    this.closest('form').submit();

                }
            });
        }

        // Delete image functionality
        const deleteButtons = document.querySelectorAll('.btn-delete-image');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const imageId = this.getAttribute('data-image-id');
                const imageUploadCard = this.closest('.image-upload-card');

                // Create a hidden input to send the image ID to be deleted
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'delete_images[]';
                hiddenInput.value = imageId;

                // Reset the image upload card
                const previewImg = imageUploadCard.querySelector('.image-preview');
                const fileInput = imageUploadCard.querySelector('.image-input');

                // Use a more reliable placeholder image path
                previewImg.src = '/sieuthi/assets/placeholder.png';
                previewImg.style.display = 'none';
                fileInput.value = '';

                // Remove primary image selection
                const primaryRadio = imageUploadCard.querySelector('input[type="radio"]');
                if (primaryRadio) {
                    primaryRadio.checked = false;
                }

                // Append the hidden input to the form
                this.closest('form').appendChild(hiddenInput);

                // Hide the entire image actions section
                const imageActions = imageUploadCard.querySelector('.image-actions');
                if (imageActions) {
                    imageActions.style.display = 'none';
                }

                // Cleanup and hide empty slots after deletion
                cleanupImageUpload();
            });
        });
    });
</script>

<style>
    .image-upload-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }

    .image-upload-card {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .image-upload-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }

    .image-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }



    .image-preview-overlay {
        position: relative;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f1f3f5;
    }

    .image-preview {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .upload-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 60px;
        height: 60px;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-upload-wrapper:hover .upload-icon {
        opacity: 1;
    }

    .upload-symbol {
        font-size: 30px;
        color: #6c757d;
    }

    .image-actions {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        background-color: #f1f3f5;
        border-top: 1px solid #dee2e6;
    }

    .primary-image-label {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
    }

    .btn-delete-image {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        transition: background-color 0.3s ease;
    }

    .btn-delete-image:hover {
        background-color: #c82333;
    }
</style>