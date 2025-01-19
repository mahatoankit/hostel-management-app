const userLoginBtn = document.getElementById('user-login-btn');
const adminLoginBtn = document.getElementById('admin-login-btn');
const loginButton = document.getElementById('login-button');
const userTypeInput = document.getElementById('user_type');

userLoginBtn.addEventListener('click', () => {
    userLoginBtn.classList.add('active');
    adminLoginBtn.classList.remove('active');
    loginButton.textContent = 'Sign in as Hosteller';
    userTypeInput.value = 'hosteller';
});

adminLoginBtn.addEventListener('click', () => {
    adminLoginBtn.classList.add('active');
    userLoginBtn.classList.remove('active');
    loginButton.textContent = 'Sign in as Admin';
    userTypeInput.value = 'admin';
});