
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

      
        function switchForm(showForm, hideForm) {
            hideForm.style.opacity = 0;  
            setTimeout(() => {
                hideForm.classList.remove('active');  
                showForm.classList.add('active');  
                setTimeout(() => {
                    showForm.style.opacity = 1; 
                }, 50);  
            }, 500);  
        }

      
        loginBtn.addEventListener('click', () => {
            if (!loginForm.classList.contains('active')) {
                switchForm(loginForm, registerForm);
                loginBtn.classList.add('active');
                registerBtn.classList.remove('active');
            }
        });

        registerBtn.addEventListener('click', () => {
            if (!registerForm.classList.contains('active')) {
                switchForm(registerForm, loginForm);
                registerBtn.classList.add('active');
                loginBtn.classList.remove('active');
            }
        });