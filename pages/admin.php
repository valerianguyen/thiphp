<?php

include '../partials/headerAdmin.php';




$sql_stats = "
    SELECT 
        SUM(CASE 
            WHEN status = 'Hoàn thành' 
            THEN (SELECT SUM((od.price_at_purchase - p.Inprice) * od.quantity) 
                  FROM order_detail od 
                  JOIN product p ON od.product_id = p.id_product 
                  WHERE od.order_id = o.id_order) 
            ELSE 0 
        END) AS total_profit,

        SUM(CASE 
            WHEN status = 'Đang duyệt' or status = 'Đang giao'
            THEN (SELECT SUM((od.price_at_purchase- p.Inprice) * od.quantity) 
                  FROM order_detail od 
                  JOIN product p ON od.product_id = p.id_product 
                  WHERE od.order_id = o.id_order) 
            ELSE 0 
        END) AS pending_profit, 

        (SELECT COUNT(*) FROM product) AS total_products, 
        (SELECT COUNT(*) FROM user) AS total_users 
    FROM orders o";



$stmt_stats = $pdo->prepare($sql_stats);
$stmt_stats->execute();

$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
?>


<div class="container-fluid">
    <div class="row">

        <div class="col-lg-2 text-white p-4" style="background-color: rgb(25,135,84); min-height: 1000px; width: 250px">
            
            <ul class="nav flex-column">

                <li class="nav-item mb-2">
                    <a href="?section=profit" class="nav-link text-white">💵 Quản lý Doanh Thu</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="?section=category" class="nav-link text-white">📁 Quản lý danh mục</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="?section=products" class="nav-link text-white">🛍️ Quản lý sản phẩm</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="?section=users" class="nav-link text-white">👥 Quản lý người dùng</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="?section=orders" class="nav-link text-white">🧾 Quản lý đơn hàng</a>
                </li>
            
            </ul>
        </div>


        <div class="col-lg-10 p-5">
            <div class="row g-4 mb-5">
          
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary">💵 Tổng thu nhập</h5>
                           
                            <p class="card-text"><?= number_format($stats['total_profit'], 0, ',', '.') . ' ₫'; ?></p>
                        </div>
                    </div>
                </div>

             
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-warning">⏳ Thu nhập đang chờ</h5>
                          
                            <p class="card-text"><?= number_format($stats['pending_profit'], 0, ',', '.') . ' ₫'; ?></p>
                        </div>
                    </div>
                </div>

              
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-success">📦 Tổng sản phẩm</h5>
                          
                            <p class="card-text"><?= $stats['total_products']; ?></p>
                        </div>
                    </div>
                </div>

              
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-info">👥 Tổng người dùng</h5>
                          
                            <p class="card-text"><?= $stats['total_users']; ?></p>
                        </div>
                    </div>
                </div>
            </div>



            <?php
         
            $active_section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

            // Kiểm tra giá trị của biến $active_section để quyết định hiển thị nội dung nào
            if ($active_section == 'category') {
                // Nếu giá trị là 'category', bao gồm file admin_categories.php
                include 'admin_categories.php';
            } elseif ($active_section == 'products') {
                // Nếu giá trị là 'products', bao gồm file admin_products.php
                include 'admin_products.php';
            } elseif ($active_section == 'users') {
                // Nếu giá trị là 'users', bao gồm file admin_users.php
                include 'admin_users.php';
            } elseif ($active_section == 'orders') {
                // Nếu giá trị là 'orders', bao gồm file admin_orders.php
                include 'admin_orders.php';
            } elseif ($active_section == 'vouchers') {
                // Nếu giá trị là 'orders', bao gồm file admin_vouchers.php
                include 'admin_vouchers.php';
            } elseif ($active_section == 'profit') {
                // Nếu giá trị là 'orders', bao gồm file admin_vouchers.php
                include 'admin_profit.php';
            } else {
                // Nếu không khớp với bất kỳ giá trị nào ở trên, hiển thị thông báo chào mừng
                echo '<div class="welcome-message">
            <h3 class="text-center">Chào mừng bạn đến với hệ thống quản lý siêu thị mini</h3>
                        <p class="lead text-center">Chúc bạn một ngày làm việc thật vui vẻ ❤️</p>

        </div>';
            }
            ?>

        </div>
    </div>
</div>

<style>
    .welcome-message {
        background-color: #f8f9fa;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .welcome-message h3 {
        color: #333;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .welcome-message p {
        color: #666;
        font-size: 1.1em;
    }
</style>

</body>

</html>