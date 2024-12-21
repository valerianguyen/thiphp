<?php
// Hàm thêm sản phẩm vào giỏ hàng
function addToCart($pdo, $user_username, $product_id, $price, $quantity) {

    // Câu lệnh SQL để kiểm tra xem sản phẩm đã có trong giỏ hàng của người dùng chưa
    $sql_check = "SELECT * FROM cart WHERE user_username = :user_username AND product_id = :product_id";
    $stmt_check = $pdo->prepare($sql_check);
    // Thực hiện câu lệnh kiểm tra
    $stmt_check->execute([':user_username' => $user_username, ':product_id' => $product_id]);
    // Lấy kết quả kiểm tra
    $cart_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // Nếu sản phẩm đã có trong giỏ hàng
    if ($cart_item) {
        // Tính toán số lượng mới
        $new_quantity = $cart_item['quantity'] + $quantity;
        // Câu lệnh SQL để cập nhật số lượng sản phẩm trong giỏ hàng
        $sql_update = "UPDATE cart SET quantity = :quantity WHERE user_username = :user_username AND product_id = :product_id";
        $stmt_update = $pdo->prepare($sql_update);
        // Thực hiện cập nhật số lượng
        $stmt_update->execute([':quantity' => $new_quantity, ':user_username' => $user_username, ':product_id' => $product_id]);
    } else {
        // Nếu sản phẩm chưa có trong giỏ hàng, thêm sản phẩm mới vào giỏ hàng
        $sql_insert = "INSERT INTO cart (user_username, product_id, price_at_cart, quantity) VALUES (:user_username, :product_id, :price_at_cart, :quantity)";
        $stmt_insert = $pdo->prepare($sql_insert);
        // Thực hiện câu lệnh chèn
        $stmt_insert->execute([':user_username' => $user_username, ':product_id' => $product_id, ':price_at_cart' => $price, ':quantity' => $quantity]);
    }
}
?>
