<?php


function getDateRange($period, $customStart = null, $customEnd = null) {
    $end = date('Y-m-d 23:59:59');
    
    if ($customStart && $customEnd) {
        return [
            date('Y-m-d 00:00:00', strtotime($customStart)),
            date('Y-m-d 23:59:59', strtotime($customEnd))
        ];
    }
    
    switch($period) {
        case 'day':
            $start = date('Y-m-d 00:00:00');
            break;
        case 'week':
            $start = date('Y-m-d 00:00:00', strtotime('-7 days'));
            break;
        case 'month':
            $start = date('Y-m-d 00:00:00', strtotime('-30 days'));
            break;
        case 'year':
            $start = date('Y-m-d 00:00:00', strtotime('-365 days'));
            break;
        default:
            $start = date('Y-m-d 00:00:00');
    }
    return [$start, $end];
}

$period = isset($_GET['period']) ? $_GET['period'] : 'day';
$customStart = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$customEnd = isset($_GET['end_date']) ? $_GET['end_date'] : null;

list($startDate, $endDate) = getDateRange($period, $customStart, $customEnd);

try {
    // Modified query to group by order ID and aggregate product details
    $query = "
        SELECT 
            o.id_order,
            o.order_date,
            o.user_username,
            o.total_price,
            GROUP_CONCAT(
                CONCAT(od.product_name, ': ', 
                      od.price_at_purchase, ' VNĐ x ', 
                      od.quantity, ' = ', 
                      od.price_at_purchase * od.quantity, ' VNĐ')
                SEPARATOR ', '
            ) as product_list,
            SUM(od.quantity * od.price_at_purchase) as total_revenue,
            SUM((od.price_at_purchase - p.Inprice) * od.quantity) as total_profit
        FROM orders o
        JOIN order_detail od ON o.id_order = od.order_id
        JOIN product p ON od.product_id = p.id_product
        WHERE o.status = 'Hoàn thành'
        AND o.order_date BETWEEN :start_date AND :end_date
        GROUP BY o.id_order, o.order_date, o.user_username, o.total_price
        ORDER BY o.order_date DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':start_date' => $startDate,
        ':end_date' => $endDate
    ]);
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate overall totals
    $totalRevenue = array_sum(array_column($orders, 'total_revenue'));
    $totalProfit = array_sum(array_column($orders, 'total_profit'));
    
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error = "Đã có lỗi xảy ra khi truy xuất dữ liệu. Vui lòng thử lại sau.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Doanh Thu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .summary-card {
            background: linear-gradient(to right, #4e73df, #224abe);
            color: white;
        }
        .period-active {
            background-color: #4e73df;
            color: white;
        }
        .modal-body ul {
            list-style-type: none;
            padding: 0;
        }
        .modal-body li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .modal-body li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">Thống Kê Doanh Thu</h2>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="section" value="profit">
                <div class="col-md-3">
                    <label class="form-label">Thời gian</label>
                    <select name="period" class="form-select" id="period-select">
                        <option value="day" <?= $period == 'day' ? 'selected' : '' ?>>Hôm nay</option>
                        <option value="week" <?= $period == 'week' ? 'selected' : '' ?>>7 ngày qua</option>
                        <option value="month" <?= $period == 'month' ? 'selected' : '' ?>>30 ngày qua</option>
                        <option value="year" <?= $period == 'year' ? 'selected' : '' ?>>365 ngày qua</option>
                        <option value="custom" <?= $customStart ? 'selected' : '' ?>>Tùy chỉnh</option>
                    </select>
                </div>
                
                <div class="col-md-3 custom-date" style="display: <?= $customStart ? 'block' : 'none' ?>;">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $customStart ?>">
                </div>
                
                <div class="col-md-3 custom-date" style="display: <?= $customStart ? 'block' : 'none' ?>;">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $customEnd ?>">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Xem thống kê</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card summary-card">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Tổng Doanh Thu</h5>
                    <p class="card-text display-6"><?= number_format($totalRevenue) ?> VNĐ</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card summary-card">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Tổng Lợi Nhuận</h5>
                    <p class="card-text display-6"><?= number_format($totalProfit) ?> VNĐ</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Chi tiết sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="productList">
                    <!-- Product list will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Chi tiết đơn hàng hoàn thành</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Khách hàng</th>
                            <th>Sản phẩm</th>
                            <th>Doanh thu</th>
                            <th>Lợi nhuận</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có dữ liệu trong khoảng thời gian này</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id_order']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                <td><?= htmlspecialchars($order['user_username']) ?></td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-info text-white" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#productModal"
                                            data-products="<?= htmlspecialchars($order['product_list']) ?>">
                                        Xem sản phẩm
                                    </button>
                                </td>
                                <td><?= number_format($order['total_revenue']) ?> VNĐ</td>
                                <td><?= number_format($order['total_profit']) ?> VNĐ</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('period-select').addEventListener('change', function() {
    const customDateInputs = document.querySelectorAll('.custom-date');
    if (this.value === 'custom') {
        customDateInputs.forEach(input => input.style.display = 'block');
    } else {
        customDateInputs.forEach(input => input.style.display = 'none');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const productModal = document.getElementById('productModal');
    const productList = document.getElementById('productList');

    productModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const products = button.getAttribute('data-products');
        const productArray = products.split(', ');
        
        let productHtml = '<ul>';
        productArray.forEach(product => {
            productHtml += `<li>${product}</li>`;
        });
        productHtml += '</ul>';
        
        productList.innerHTML = productHtml;
    });
});
</script>

</body>
</html>