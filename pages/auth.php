<?php

include '../partials/header.php';

include '../includes/func.php';

if (isLoggedIn() == true) {
    header('Location: ' . BASE_URL . 'index.php');
}

?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
<div class="auth-wrapper" style="background-color: #F2F6FA; padding: 50px 0;">
    <div class="container">
        <div class="form-card shadow-lg rounded" style="background-color: white; padding: 30px; max-width: 900px; margin: auto;">

            <div class="toggle-buttons text-center mb-5">
                <button id="loginBtn" class="btn me-2" style="background-color: rgb(25,135,84); color: white; padding: 10px 30px; border-radius: 30px;">Đăng nhập</button>
                <button id="registerBtn" class="btn ms-2" style="background-color: #f8f9fa; color: rgb(25,135,84); padding: 10px 30px; border-radius: 30px;">Đăng ký</button>
            </div>

            <form id="loginForm" class="form active" action="<?php echo BASE_URL; ?>controllers/auth.php" method="POST" style="display: block;">
                <h3 class="text-center mb-4" style="color: rgb(25,135,84);">Đăng nhập vào tài khoản của bạn</h3>
                <div class="mb-3">
                    <input type="text" name="emailOrPhone" class="form-control form-control-lg" placeholder="Email, Tên đăng nhập hoặc Số điện thoại" required style="border-radius: 20px;">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Mật khẩu" required style="border-radius: 20px;">
                </div>
                <input type="hidden" name="action" value="login">
                <button type="submit" class="btn w-100" style="background-color: rgb(25,135,84); color: white; padding: 12px; border-radius: 30px;">Đăng nhập</button>
            </form>

            <form id="registerForm" class="form" action="<?php echo BASE_URL; ?>controllers/register.php" method="POST" style="display: none;" onsubmit="return validateForm()">
                <h3 class="text-center mb-4" style="color: rgb(25,135,84);">Tạo tài khoản của bạn</h3>
                <div class="mb-3">
                    <input type="text" name="username" class="form-control form-control-lg" placeholder="Tên đăng nhập" required style="border-radius: 20px;">
                </div>
                <div class="mb-3">
                    <input type="text" name="fullname" class="form-control form-control-lg" placeholder="Họ và tên" required style="border-radius: 20px;">
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" required style="border-radius: 20px;">
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control form-control-lg" required style="border-radius: 20px;" name="phone" id="phone" value="<?= $user_data['phone'] ?>" maxlength="10" oninput="validatePhoneNumber(this)"
                        placeholder="Số điện thoại phải bắt đầu bằng số 0, tối đa 10 chữ số, không chứa hơn 2 số 0 liên tiếp.">
                </div>
                <div class="mb-3">
                    <input type="text" name="address" class="form-control form-control-lg" placeholder="Địa Chỉ" maxlength="90" required style="border-radius: 20px;">
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Mật khẩu" required style="border-radius: 20px;">
                </div>
                <button type="submit" class="btn w-100" style="background-color: rgb(25,135,84); color: white; padding: 12px; border-radius: 30px;">Đăng ký</button>
            </form>
        </div>
    </div>
</div>


<?php

include '../partials/footer.php';

?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/auth.js"></script>
<script>
    window.onload = function() {
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');


        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        loginBtn.style.backgroundColor = 'rgb(25,135,84)';
        loginBtn.style.color = 'white';
        registerBtn.style.backgroundColor = '#f8f9fa';
        registerBtn.style.color = 'rgb(25,135,84)';


        loginBtn.addEventListener('click', () => {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            loginBtn.style.backgroundColor = 'rgb(25,135,84)';
            loginBtn.style.color = 'white';
            registerBtn.style.backgroundColor = '#f8f9fa';
            registerBtn.style.color = 'rgb(25,135,84)';
        });

        registerBtn.addEventListener('click', () => {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            registerBtn.style.backgroundColor = 'rgb(25,135,84)';
            registerBtn.style.color = 'white';
            loginBtn.style.backgroundColor = '#f8f9fa';
            loginBtn.style.color = 'rgb(25,135,84)';
        });
    };
</script>


<script>
    function validatePhoneNumber(input) {
        // Lấy giá trị của input
        let phone = input.value;

        // Kiểm tra xem số đầu tiên có phải là "0" không
        if (phone.length > 0 && phone[0] !== '0') {
            input.value = '0' + phone.slice(1);
        }

        // Giới hạn số ký tự tối đa là 10
        if (phone.length > 10) {
            input.value = phone.slice(0, 10);
        }

        // Kiểm tra điều kiện không có quá hai số "0" liên tiếp
        if (/000/.test(phone)) {
            input.value = phone.slice(0, -1); // Xóa ký tự cuối cùng nếu vi phạm
            alert("Số điện thoại không được chứa quá hai số 0 liên tiếp.");
        }
    }

    function validateForm() {
        const phoneInput = document.getElementById('phone').value;

        // Kiểm tra đúng 10 chữ số
        if (phoneInput.length !== 10) {
            alert("Số điện thoại phải chứa đúng 10 chữ số.");
            return false;
        }

        // Kiểm tra bắt đầu bằng số "0"
        if (phoneInput[0] !== '0') {
            alert("Số điện thoại phải bắt đầu bằng số 0.");
            return false;
        }

        // Kiểm tra không có hơn 2 số "0" liên tiếp
        if (/000/.test(phoneInput)) {
            alert("Số điện thoại không được chứa quá hai số 0 liên tiếp.");
            return false;
        }

        // Nếu hợp lệ
        return true;
    }
</script>


</body>

</html>