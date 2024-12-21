<?php

function isLoggedIn()
{
    return isset($_SESSION['id_user']);
}

function addToCart($pdo, $user_username, $product_id, $price, $quantity)
{
    $sql_check = "SELECT * FROM cart WHERE user_username = :user_username AND product_id = :product_id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([':user_username' => $user_username, ':product_id' => $product_id]);
    $cart_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        $new_quantity = $cart_item['quantity'] + $quantity;
        $sql_update = "UPDATE cart SET quantity = :quantity WHERE user_username = :user_username AND product_id = :product_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([':quantity' => $new_quantity, ':user_username' => $user_username, ':product_id' => $product_id]);
    } else {
        $sql_insert = "INSERT INTO cart (user_username, product_id, price_at_cart, quantity) VALUES (:user_username, :product_id, :price_at_cart, :quantity)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([':user_username' => $user_username, ':product_id' => $product_id, ':price_at_cart' => $price, ':quantity' => $quantity]);
    }
}
?>

