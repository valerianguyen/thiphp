<?php
require_once '../controllers/paginationAdmin.php';
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
// Kiểm tra xem phương thức yêu cầu là POST không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra xem có gửi form để chỉnh sửa người dùng không
    if (isset($_POST['edit_user'])) {
        // Lấy các giá trị từ form
        $user_id = $_POST['user_id'];  // ID của người dùng
        $is_admin = $_POST['isAdmin'];  // Trạng thái quản trị viên (có hoặc không)
        $is_disabled = $_POST['isDisabled'];  // Trạng thái kích hoạt (kích hoạt hay vô hiệu hóa)

        // Câu truy vấn SQL để cập nhật thông tin người dùng
        $sql_update_user = "UPDATE user SET isAdmin = :isAdmin, isDisabled = :isDisabled WHERE id_user = :id_user";
        // Chuẩn bị câu truy vấn và thực thi
        $stmt_update_user = $pdo->prepare($sql_update_user);
        $stmt_update_user->execute(['isAdmin' => $is_admin, 'isDisabled' => $is_disabled, 'id_user' => $user_id]);
    }
}







$pagination_result = fetchPaginatedUsers($pdo, $current_page, $items_per_page);
$users = $pagination_result['users'];
$total_pages = $pagination_result['total_pages'];
$total_users = $pagination_result['total_users'];
?>
<style>
    .btn-primary {
        background-color: rgb(102, 196, 206);
        border-color: rgb(102, 196, 206);
    }

    .btn-primary:hover {
        background-color: rgba(48, 120, 156, 0.9);
        border-color: rgba(48, 120, 156, 0.9);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(48, 120, 156, 0.05);
    }

    .badge {
        padding: 0.5em 1em;
        font-size: 0.9em;
    }

    .table td,
    .table th {
        vertical-align: middle;
        text-align: center;
    }

    .form-select {
        min-width: 140px;
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

<div class="container mt-5">
    <h2 class="mb-4">Quản Lý Người Dùng</h2>
    <table class="table table-hover table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>Tên đầy đủ</th>
                <th>Địa chỉ</th>
                <th>Quyền</th>
                <th>Tình trạng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <!-- Duyệt qua từng người dùng trong mảng $users -->
            <?php foreach ($users as $user): ?>
                <tr>
                    <!-- Hiển thị tên đầy đủ của người dùng, sử dụng htmlspecialchars để bảo vệ khỏi XSS -->
                    <td><?= htmlspecialchars($user['fullname']); ?></td>
                    <!-- Hiển thị địa chỉ của người dùng, cũng sử dụng htmlspecialchars -->
                    <td><?= htmlspecialchars($user['address'] ?? '(Chưa cập nhật)', ENT_QUOTES, 'UTF-8'); ?></td>

                    <td>
                        <!-- Hiển thị trạng thái quản trị viên với badge tương ứng -->
                        <span class="badge <?= $user['isAdmin'] == 1 ? 'bg-primary' : 'bg-secondary'; ?>">
                            <?= $user['isAdmin'] == 1 ? 'Admin' : 'Khách Hàng'; ?>
                        </span>
                    </td>
                    <td>
                        <!-- Hiển thị trạng thái kích hoạt với badge tương ứng -->
                        <span class="badge <?= $user['isDisabled'] == 1 ? 'bg-danger' : 'bg-success'; ?>">
                            <?= $user['isDisabled'] == 1 ? 'Vô Hiệu Hóa' : 'Hoạt Động'; ?>
                        </span>
                    </td>
                    <td>
                        <!-- Form để chỉnh sửa thông tin người dùng -->
                        <form method="post" class="d-flex align-items-center">
                            <!-- Ẩn input chứa ID người dùng để gửi đi -->
                            <input type="hidden" name="user_id" value="<?= $user['id_user']; ?>">

                            <!-- Dropdown để chọn trạng thái quản trị viên -->
                            <select name="isAdmin" class="form-select me-2">
                                <option value="0" <?= $user['isAdmin'] == 0 ? 'selected' : ''; ?>>Khách hàng</option>
                                <option value="1" <?= $user['isAdmin'] == 1 ? 'selected' : ''; ?>>Admin</option>
                            </select>

                            <!-- Dropdown để chọn trạng thái kích hoạt -->
                            <select name="isDisabled" class="form-select me-2">
                                <option value="0" <?= $user['isDisabled'] == 0 ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="1" <?= $user['isDisabled'] == 1 ? 'selected' : ''; ?>>Vô hiệu hóa</option>
                            </select>

                            <!-- Nút để gửi form -->
                            <button type="submit" name="edit_user" class="btn btn-primary">Sửa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>


</div>
<?php if ($total_pages > 1): ?>
    <nav aria-label="Category pagination" class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?section=users&page=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

