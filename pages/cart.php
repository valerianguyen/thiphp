<?php

include '../partials/header.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ' . BASE_URL . 'pages/auth.php');
    exit;
}

$user_username = $_SESSION['username'];
$id_user = $_SESSION['id_user'];

$sql_user = "SELECT address FROM user WHERE username = :username";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([':username' => $user_username]);
$user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
$user_address = $user_data['address'];

if (isset($_POST['update_address'])) {
    $new_address = $_POST['address'];
    $sql_update_address = "UPDATE user SET address = :address WHERE username = :username";
    $stmt_update_address = $pdo->prepare($sql_update_address);
    $stmt_update_address->execute([':address' => $new_address, ':username' => $user_username]);
}

if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $sql_delete = "DELETE FROM cart WHERE user_username = :user_username AND product_id = :product_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([':user_username' => $user_username, ':product_id' => $product_id]);
    echo "<script type='text/javascript'>window.location.href = 'cart.php';</script>";
    exit();
}

$sql_cart = "
    SELECT cart.*, 
           product.name AS product_name, 
           product.price AS current_price, 
           category.name AS category_name, 
           product_images.image_path AS image
    FROM cart
    INNER JOIN product ON cart.product_id = product.id_product
    INNER JOIN category ON product.category_id = category.id_category
    LEFT JOIN product_images ON product.id_product = product_images.product_id AND product_images.is_primary = 1
    WHERE cart.user_username = :user_username
";
$stmt_cart = $pdo->prepare($sql_cart);
$stmt_cart->execute([':user_username' => $user_username]);
$cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);






$sql_stock = "SELECT id_product, quantity FROM product";
$stmt_stock = $pdo->prepare($sql_stock);
$stmt_stock->execute();
$stock_data = $stmt_stock->fetchAll(PDO::FETCH_KEY_PAIR); 

if (!isset($_POST['checkout'])) {
foreach ($cart_items as $key => $item) {
    if (isset($stock_data[$item['product_id']])) {
        if ($item['quantity'] > $stock_data[$item['product_id']]) {
        
            $cart_items[$key]['quantity'] = $stock_data[$item['product_id']];
        }

      
        if ($stock_data[$item['product_id']] <= 0) {
            unset($cart_items[$key]);

            
            $sql_delete = "DELETE FROM cart WHERE user_username = :user_username AND product_id = :product_id";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->execute([':user_username' => $user_username, ':product_id' => $item['product_id']]);
        } else {
           
            $sql_update = "UPDATE cart SET quantity = :quantity WHERE user_username = :user_username AND product_id = :product_id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':quantity' => $cart_items[$key]['quantity'],
                ':user_username' => $user_username,
                ':product_id' => $item['product_id'],
            ]);
        }
    }
}
}


$total_quantity = 0;
$total_price = 0;
$total_products = count($cart_items);

foreach ($cart_items as $item) {
    $total_quantity += $item['quantity'];
    $total_price += $item['price_at_cart'] * $item['quantity'];
}











$voucher_message = isset($_SESSION['voucher_message']) ? $_SESSION['voucher_message'] : '';
$voucher_discount = isset($_SESSION['voucher_discount']) ? $_SESSION['voucher_discount'] : 0;
$applied_voucher = isset($_SESSION['applied_voucher']) ? $_SESSION['applied_voucher'] : null;




if (isset($_POST['apply_voucher'])) {
    $voucher_code = $_POST['voucher_code'];

    $sql_voucher = "SELECT * FROM voucher WHERE voucher_code = :voucher_code";
    $stmt_voucher = $pdo->prepare($sql_voucher);
    $stmt_voucher->execute([':voucher_code' => $voucher_code]);
    $voucher = $stmt_voucher->fetch(PDO::FETCH_ASSOC);

    if ($voucher) {
        $current_date = date('Y-m-d');

        if ($total_price < $voucher['condition_total_price']) {
            $voucher_message = "Chưa đủ điều kiện";
        } elseif (
            $voucher['usage_limit'] == 0 ||
            ($voucher['valid_from'] !== null && $current_date < $voucher['valid_from']) ||
            ($voucher['valid_to'] !== null && $current_date > $voucher['valid_to'])
        ) {
            $voucher_message = "Mã giảm giá không thể sử dụng được";
        } else {
            $sql_usage = "SELECT usage_count FROM user_voucher_usage WHERE id_user = :id_user AND voucher_id = :voucher_id";
            $stmt_usage = $pdo->prepare($sql_usage);
            $stmt_usage->execute([':id_user' => $id_user, ':voucher_id' => $voucher['voucher_id']]);
            $usage = $stmt_usage->fetch(PDO::FETCH_ASSOC);

            if ($usage && $usage['usage_count'] >= $voucher['user_usage_limit']) {
                $voucher_message = "Bạn đã sử dụng mã giảm giá này";
            } else {
                if ($voucher['discount_type'] == 'amount') {
                    $voucher_discount = $voucher['discount_value'];
                } else {
                    $voucher_discount = $total_price * ($voucher['discount_value'] / 100);
                }
                $voucher_message = "Mã giảm giá đã được áp dụng";
                $applied_voucher = $voucher;

         
                $_SESSION['voucher_message'] = $voucher_message;
                $_SESSION['voucher_discount'] = $voucher_discount;
                $_SESSION['applied_voucher'] = $applied_voucher;

            }
        }
    } else {
        $voucher_message = "Mã giảm giá không hợp lệ";
    }
}
if (isset($_POST['update_address'])) {
    $new_address = $_POST['address'];
    $sql_update_address = "UPDATE user SET address = :address WHERE username = :username";
    $stmt_update_address = $pdo->prepare($sql_update_address);
    $stmt_update_address->execute([':address' => $new_address, ':username' => $user_username]);
    $user_address = $new_address; 
}


if (isset($_POST['checkout'])) {
    if (empty($user_address)) {
        echo "<script>
            alert('Vui lòng cập nhật địa chỉ giao hàng trước khi thanh toán!');
            window.location.href = '#address-section';
        </script>";
        exit;
    }

    foreach ($cart_items as $item) {
        $sql_stock_check = "SELECT quantity, is_active, name FROM product WHERE id_product = :product_id";
        $stmt_stock_check = $pdo->prepare($sql_stock_check);
        $stmt_stock_check->execute([':product_id' => $item['product_id']]);
        $product = $stmt_stock_check->fetch(PDO::FETCH_ASSOC);

       
        if (
            !$product ||
            $product['quantity'] < $item['quantity'] ||
            $product['quantity'] <= 0 ||
            $product['is_active'] != 1 ||
            $product['deleted_at'] !== null
        ) {

            $error_message = "Một số sản phẩm đã được cập nhật, vui lòng thanh toán lại.";
            echo "<script>
                alert('" . htmlspecialchars($error_message) . "');
                window.location.href = 'cart.php';
            </script>";
            exit;
        }
    }

    $payment_method = $_POST['payment_method'];
    $final_total = $total_price - $voucher_discount; 

    $sql_order = "INSERT INTO orders (user_username, total_price, status, payment_method) 
                  VALUES (:user_username, :total_price, 'Đang duyệt', :payment_method)";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([':user_username' => $user_username, ':total_price' => $final_total, ':payment_method' => $payment_method]);

    $order_id = $pdo->lastInsertId();

    foreach ($cart_items as $item) {
   
        $item_discount = 0;
        if ($applied_voucher) {
            if ($applied_voucher['discount_type'] == 'amount') {
              
                $item_discount = ($voucher_discount / $total_quantity) * $item['quantity'];
            } else {
              
                $item_discount = $item['price_at_cart'] * $item['quantity'] * ($applied_voucher['discount_value'] / 100);
            }
        }
        $discounted_price = max(($item['price_at_cart'] * $item['quantity'] - $item_discount) / $item['quantity'], 0);

        $sql_order_detail = "INSERT INTO order_detail (order_id, product_id, product_name, price_at_purchase, quantity)
                             VALUES (:order_id, :product_id, :product_name, :price_at_purchase, :quantity)";
        $stmt_order_detail = $pdo->prepare($sql_order_detail);
        $stmt_order_detail->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':product_name' => $item['product_name'],
            ':price_at_purchase' => $discounted_price,
            ':quantity' => $item['quantity']

        ]);
    }


    if ($applied_voucher) {

        $sql_update_voucher = "UPDATE voucher SET usage_limit = usage_limit - 1 WHERE voucher_id = :voucher_id";
        $stmt_update_voucher = $pdo->prepare($sql_update_voucher);
        $stmt_update_voucher->execute([':voucher_id' => $applied_voucher['voucher_id']]);

        $sql_update_usage = "INSERT INTO user_voucher_usage (id_user, voucher_id, usage_count) 
                             VALUES (:id_user, :voucher_id, 1)
                             ON DUPLICATE KEY UPDATE usage_count = usage_count + 1";
        $stmt_update_usage = $pdo->prepare($sql_update_usage);
        $stmt_update_usage->execute([':id_user' => $id_user, ':voucher_id' => $applied_voucher['voucher_id']]);
    }


    foreach ($cart_items as $item) {
      
        $sql_update_stock = "UPDATE product SET quantity = quantity - :quantity WHERE id_product = :product_id";
        $stmt_update_stock = $pdo->prepare($sql_update_stock);
        $stmt_update_stock->execute([':quantity' => $item['quantity'], ':product_id' => $item['product_id']]);
    }

    $sql_clear_cart = "DELETE FROM cart WHERE user_username = :user_username";
    $stmt_clear_cart = $pdo->prepare($sql_clear_cart);
    $stmt_clear_cart->execute([':user_username' => $user_username]);

    unset($_SESSION['voucher_message']);
    unset($_SESSION['voucher_discount']);
    unset($_SESSION['applied_voucher']);
    echo "<script>
        alert('Bạn đã đặt hàng thành công!');
        window.location.href = '" . BASE_URL . "index.php';
    </script>";
    exit;
}
?>

<section class="h-100 h-custom" style="background-color: #d2c9ff;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12">
                <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                    <div class="card-body p-0">
                        <div class="row g-0 p-4">
                            <?php if ($total_products == 0): ?>
                                <div class="text-center">
                                    <h1 class="fw-bold mb-0">Giỏ hàng trống</h1>
                                    <a href="<?= BASE_URL; ?>pages/shop.php" class="btn btn-secondary mt-4">Tới cửa hàng</a>
                                </div>
                            <?php else: ?>
                                <div class="col-lg-8">
                                    <div class="p-5">
                                        <div class="d-flex justify-content-between align-items-center mb-5">
                                            <h1 class="fw-bold mb-0">Giỏ hàng</h1>
                                            <h6 class="mb-0 text-muted"><?= $total_products ?> món đồ</h6>
                                        </div>
                                        <hr class="my-4">

                                        <?php foreach ($cart_items as $item): ?>
                                            <div class="row mb-4 d-flex justify-content-between align-items-center">
                                                <div class="col-md-2 col-lg-2 col-xl-2">
                                                    <img src="../<?= $item['image']; ?>" class="img-fluid rounded-3"
                                                        alt="<?= $item['product_name']; ?>" onerror="this.src='https://placehold.co/102x64'">
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-3">
                                                    <h6 class="text-muted"><?= $item['category_name']; ?></h6>
                                                    <h6 class="mb-0"><?= $item['product_name']; ?></h6>
                                                    <p class="mb-0">
                                                        <?= number_format($item['price_at_cart'], 0, ',', '.') . ' ₫'; ?>
                                                    </p>

                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                                                    <input id="form1" name="quantity" value="<?= $item['quantity']; ?>"
                                                        type="number" class="form-control form-control-sm text-center" disabled />
                                                </div>
                                                <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                                    <h6 class="mb-0">
                                                        <?= number_format($item['price_at_cart'] * $item['quantity'], 0, ',', '.') . ' ₫'; ?>
                                                    </h6>
                                                </div>
                                                <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                                    <form method="post" action="">
                                                        <input type="hidden" name="product_id"
                                                            value="<?= $item['product_id']; ?>">
                                                        <button type="submit" name="delete_product" class="text-muted btn"><i
                                                                class="fas fa-times"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <hr class="my-4">
                                        <?php endforeach; ?>

                                        <div class="pt-5">
                                            <h6 class="mb-0"><a href="<?= BASE_URL; ?>pages/shop.php" class="text-body"><i
                                                        class="fas fa-long-arrow-alt-left me-2"></i>Quay lại cửa hàng</a>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 bg-body-tertiary">
                                    <div class="p-5">
                                        <h3 class="fw-bold mb-5 mt-2 pt-1">Thống kê</h3>

                                        <h5 class="text-uppercase mb-3">Hình thức giao hàng</h5>

                                        <div class="mb-4 pb-2">
                                            <select name="shipping_method" class="form-select">
                                                <option value="Express" selected>Giao hàng hỏa tốc - Free</option>
                                            </select>
                                        </div>

                                        <div id="address-section" class="mb-4">
                                            <h5 class="text-uppercase mb-3">Địa chỉ giao hàng</h5>
                                            <form method="post" action="" class="mb-3">
                                                <div class="form-outline">
                                                    <textarea name="address" class="form-control" rows="3"
                                                        required><?= htmlspecialchars($user_address) ?></textarea>
                                                </div>
                                                <button type="submit" name="update_address" class="btn btn-secondary mt-2">
                                                    Cập nhật địa chỉ
                                                </button>
                                            </form>
                                            <?php if (empty($user_address)): ?>
                                                <div class="alert alert-warning" role="alert">
                                                    Vui lòng nhập địa chỉ giao hàng
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        
                                        <hr class="my-4">

                                        <div class="d-flex justify-content-between">
                                            <h5 class="text-uppercase">Sản phẩm: <?= $total_products ?></h5>
                                            <h5><?= number_format($total_price, 0, ',', '.') . ' ₫'; ?></h5>
                                        </div>

                                        
                                        <div class="d-flex justify-content-between mb-4">
                                            <h5 class="">Phí vận chuyển:</h5>
                                            <h5>Miễn phí</h5>
                                        </div>

                                        <div class="d-flex justify-content-between mb-4">
                                            <h5 class="">Tổng:</h5>
                                            <h5><?= number_format($total_price - $voucher_discount, 0, ',', '.') . ' ₫'; ?>
                                            </h5>
                                        </div>

                                        <hr class="my-4">

                                        <form method="post" action="">
                                            <h5 class="text-uppercase mb-3">Hình thức thanh toán</h5>
                                            <div>
                                                <label>
                                                    <input type="radio" name="payment_method" value="COD" checked
                                                        onclick="toggleCardDetails(false)">
                                                    Thanh toán COD
                                                </label>
                                                <br>
                                                <label>
                                                    <input type="radio" name="payment_method" value="Online"
                                                        onclick="toggleCardDetails(true)">
                                                    Thanh toán qua Visa / Mastercard
                                                </label>
                                            </div>

                                           
                                            <div id="card-details" style="display: none; margin-top: 20px;">
                                                <h5 class="text-uppercase mb-3">Thông tin thẻ</h5>
                                                <div class="mb-3">
                                                    <label for="cardholder">Tên chủ thẻ</label>
                                                    <input type="text" class="form-control" id="cardholder"
                                                        placeholder="Nhập tên chủ thẻ">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="cardnumber">Số thẻ</label>
                                                    <input type="text" class="form-control" id="cardnumber"
                                                        placeholder="Nhập số thẻ">
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="expiry">Ngày hết hạn</label>
                                                        <input type="text" class="form-control" id="expiry"
                                                            placeholder="MM/YY">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="cvv">CVV</label>
                                                        <input type="text" class="form-control" id="cvv" placeholder="CVV">
                                                    </div>
                                                </div>
                                            </div>


                                            <hr class="my-4">
                                            <button type="submit" name="checkout"
                                                class="btn btn-dark btn-block btn-lg">Thanh toán ngay</button>
                                        </form>

                                    </div>
                                <?php endif;  ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function toggleCardDetails(show) {
        document.getElementById('card-details').style.display = show ? 'block' : 'none';
    }
</script>