<?php
function fetchPaginatedCategories($pdo, $page = 1, $items_per_page = 5)
{

    $offset = ($page - 1) * $items_per_page;


    $sql_total_categories = "SELECT COUNT(*) AS total FROM category WHERE is_active = 1";
    $stmt_total = $pdo->prepare($sql_total_categories);
    $stmt_total->execute();
    $total_categories = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];


    $sql_fetch_categories = "SELECT * FROM category 
                             WHERE is_active = 1 
                             LIMIT :items_per_page OFFSET :offset";
    $stmt_categories = $pdo->prepare($sql_fetch_categories);
    $stmt_categories->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt_categories->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_categories->execute();
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);


    $total_pages = ceil($total_categories / $items_per_page);

    return [
        'categories' => $categories,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_categories' => $total_categories
    ];
}


function fetchPaginatedUsers($pdo, $page = 1, $items_per_page = 10)
{

    $offset = ($page - 1) * $items_per_page;


    $sql_total_users = "SELECT COUNT(*) AS total FROM user";
    $stmt_total = $pdo->prepare($sql_total_users);
    $stmt_total->execute();
    $total_users = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];


    $sql_fetch_users = "SELECT * FROM user
                             LIMIT :items_per_page OFFSET :offset";
    $stmt_users = $pdo->prepare($sql_fetch_users);
    $stmt_users->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt_users->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_users->execute();
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    $total_pages = ceil($total_users / $items_per_page);

    return [
        'users' => $users,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_users' => $total_users
    ];
}



function fetchPaginatedProducts($pdo, $page = 1, $items_per_page = 10)
{
    // Calculate offset for pagination
    $offset = ($page - 1) * $items_per_page;

    // Count total active products
    $sql_total_products = "SELECT COUNT(*) AS total 
                           FROM product 
                           WHERE is_active = 1";
    $stmt_total = $pdo->prepare($sql_total_products);
    $stmt_total->execute();
    $total_products = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch paginated products with additional details
    $sql_fetch_products = "SELECT p.*, 
                            pi.image_path AS primary_image,
                            c.name AS category_name 
                          FROM product p
                          LEFT JOIN product_images pi ON p.id_product = pi.product_id AND pi.is_primary = 1
                          LEFT JOIN category c ON p.category_id = c.id_category 
                          WHERE p.is_active = 1
                          LIMIT :items_per_page OFFSET :offset";

    $stmt_products = $pdo->prepare($sql_fetch_products);
    $stmt_products->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt_products->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_products->execute();
    $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total pages
    $total_pages = ceil($total_products / $items_per_page);

    return [
        'products' => $products,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_products' => $total_products
    ];
}


function fetchPaginatedOrders($pdo, $page = 1, $items_per_page = 10) {
   
    $offset = ($page - 1) * $items_per_page;

   
    $sql_total_orders = "SELECT COUNT(DISTINCT o.id_order) AS total FROM orders o";
    $stmt_total = $pdo->prepare($sql_total_orders);
    $stmt_total->execute();
    $total_orders = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

    
    $sql_fetch_orders = "
    SELECT 
        o.id_order,
        o.user_username,
        o.order_date,
        o.total_price,
        o.payment_method,
        o.status,
        GROUP_CONCAT(
            CONCAT(od.product_name, ': ', od.price_at_purchase, ' đ x ', od.quantity, ' = ', od.price_at_purchase * od.quantity, ' đ')
            SEPARATOR ', '
        ) as product_list,
        SUM((od.price_at_purchase - p.Inprice) * od.quantity) AS total_profit
    FROM orders o
    JOIN order_detail od ON o.id_order = od.order_id
    JOIN product p ON od.product_id = p.id_product
    GROUP BY o.id_order, o.user_username, o.order_date, o.total_price, o.payment_method, o.status
    ORDER BY 
        CASE o.status 
            WHEN 'Đang giao' THEN 1
            WHEN 'Đang duyệt' THEN 2
            WHEN 'Hoàn thành' THEN 3
            ELSE 4
        END,
        o.order_date DESC
    LIMIT :items_per_page OFFSET :offset
";
    $stmt_orders = $pdo->prepare($sql_fetch_orders);
    $stmt_orders->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt_orders->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_orders->execute();
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

 
    $total_pages = ceil($total_orders / $items_per_page);

    return [
        'orders' => $orders,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_orders' => $total_orders
    ];
}


function fetchPaginatedVouchers($pdo, $page = 1, $items_per_page = 10) {
    
    $offset = ($page - 1) * $items_per_page;

    $sql_total_vouchers = "SELECT COUNT(*) AS total FROM voucher";
    $stmt_total = $pdo->prepare($sql_total_vouchers);
    $stmt_total->execute();
    $total_vouchers = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

   
    $sql_fetch_vouchers = "SELECT * FROM voucher 
                           ORDER BY created_at DESC 
                           LIMIT :items_per_page OFFSET :offset";
    
    $stmt_vouchers = $pdo->prepare($sql_fetch_vouchers);
    $stmt_vouchers->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt_vouchers->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_vouchers->execute();
    $vouchers = $stmt_vouchers->fetchAll(PDO::FETCH_ASSOC);

   
    $total_pages = ceil($total_vouchers / $items_per_page);

    return [
        'vouchers' => $vouchers,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_vouchers' => $total_vouchers
    ];
}
?>