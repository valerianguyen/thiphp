<?php
require_once '../controllers/paginationAdmin.php';
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10; 





if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];
        
        $sql_update_order_status = "UPDATE orders SET status = :status WHERE id_order = :order_id";
        $stmt_update_order_status = $pdo->prepare($sql_update_order_status);
        $stmt_update_order_status->execute(['status' => $status, 'order_id' => $order_id]);
        
        echo "<script type='text/javascript'>window.location.href = 'admin.php?section=orders&page=$current_page';</script>";
        exit();
    }
}

$pagination_result = fetchPaginatedOrders($pdo, $current_page, $items_per_page);
$orders = $pagination_result['orders'];
$total_pages = $pagination_result['total_pages'];
$total_orders = $pagination_result['total_orders'];
?>

<style>
    .btn-custom {
        background-color: rgb(25,135,84);
        border-color: rgb(25,135,84);
        color: white;
    }

    .btn-custom:hover {
        background-color: rgba(48,120,156, 0.9);
        border-color: rgba(48,120,156, 0.9);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(48,120,156, 0.05);
    }

    .table td, .table th {
        vertical-align: middle;
    }

    .form-select {
        min-width: 150px;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
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

<h2 class="mb-4">Quản Lý Đơn Hàng</h2>
<!-- Add this div for the popup -->
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

<table class="table table-hover table-bordered mt-3">
    <thead class="table-light">
        <tr>
            <th>ID Đơn hàng</th>
            <th>Khách hàng</th>
            <th>Ngày</th>
            <th>Sản phẩm</th>
            <th>Tiền</th>
            <th class="text-success">Lãi thu</th>
            <th>Hình thức thanh toán</th>
            <th>Tình trạng</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): 
            $products = explode(', ', $order['product_list']);
            $isMultipleProducts = count($products) > 1;
            $isCompleted = $order['status'] === 'Hoàn thành';
        ?>
            <tr>
                <td><?= htmlspecialchars($order['id_order']); ?></td>
                <td><?= htmlspecialchars($order['user_username']); ?></td>
                <td><?= htmlspecialchars($order['order_date']); ?></td>
                <td>
             
                        <button type="button" 
                                class="btn btn-sm btn-info text-white" 
                                data-bs-toggle="modal" 
                                data-bs-target="#productModal"
                                data-products="<?= htmlspecialchars($order['product_list']); ?>">
                            Xem sản phẩm
                        </button>
      
                   
                </td>
                <td><?= number_format($order['total_price'], 0, ',', '.') . ' ₫'; ?></td>
                <td class="text-success"><?= number_format($order['total_profit'], 0, ',', '.') . ' ₫'; ?></td>
                <td class="text-center"><?= htmlspecialchars($order['payment_method']); ?></td>
                <td>
                    <form method="post" class="d-flex align-items-center">
                        <input type="hidden" name="order_id" value="<?= $order['id_order']; ?>">
                        <select name="status" class="form-select me-2" <?= $isCompleted ? 'disabled' : ''; ?>>
                            <option value="Đang duyệt" <?= $order['status'] == 'Đang duyệt' ? 'selected' : ''; ?>>Đang duyệt</option>
                            <option value="Đang giao" <?= $order['status'] == 'Đang giao' ? 'selected' : ''; ?>>Đang giao</option>
                            <option value="Hoàn thành" <?= $order['status'] == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                        </select>
                </td>
                <td>
                    <?php if (!$isCompleted): ?>
                        <button type="submit" name="update_order_status" class="btn btn-custom">
                            Cập nhật
                        </button>
                    <?php endif; ?>
                </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if ($total_pages > 1): ?>
<nav aria-label="Category pagination" class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $current_page) ? 'active' : ''; ?>">
                <a class="page-link" href="?section=orders&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productModal = document.getElementById('productModal');
    const productList = document.getElementById('productList');

    productModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const products = button.getAttribute('data-products');
        const productArray = products.split(', ');
        
        // Create an unordered list of products
        let productHtml = '<ul>';
        productArray.forEach(product => {
            productHtml += `<li>${product}</li>`;
        });
        productHtml += '</ul>';
        
        productList.innerHTML = productHtml;
    });
});
</script>

