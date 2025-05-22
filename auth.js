document.addEventListener('DOMContentLoaded', () => {
    // Tab Switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    const forms = document.querySelectorAll('.auth-form');
    const switchTabs = document.querySelectorAll('.switch-tab');
    const validateField = (field, type) => {
        const value = field.value.trim();
    
        if (!value) {
            return { valid: false, message: 'This field is required' };
        }
    
        switch (type) {
            case 'name':
                if (/[0-9]/.test(value)) {
                    return { valid: false, message: 'Name should not contain numbers' };
                }
                if (value.length < 2) {
                    return { valid: false, message: 'Name should be at least 2 characters' };
                }
                break;
    
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        return { valid: false, message: 'Please enter a valid email address' };
                    }
                    break;
    
            case 'phone':
                const phoneRegex = /^\d{10}$/;
                if (!phoneRegex.test(value)) {
                    return { valid: false, message: 'Phone number must be exactly 10 digits' };
                }
                break;
    
            case 'password':
                if (value.length < 8) {
                    return { valid: false, message: 'Password must be at least 8 characters' };
                }
                if (!/[A-Z]/.test(value) || !/[a-z]/.test(value) || !/[0-9]/.test(value)) {
                    return { valid: false, message: 'Password must contain uppercase, lowercase, and numbers' };
                }
                break;
    
            case 'username':
                const usernameRegex = /^[A-Za-z]{4,}$/;
                if (!usernameRegex.test(value)) {
                    return { valid: false, message: 'Username must be at least 4 letters and contain only letters' };
                }
                break;
        }
    
        return { valid: true };
    };
    
        

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab;

            // Update active tab
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Show corresponding form
            forms.forEach(form => form.classList.remove('active'));
            document.querySelector(`[data-form="${tab}"]`).classList.add('active');
        });
    });

    switchTabs.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const tab = link.dataset.tab;

            // Update active tab
            tabButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[data-tab="${tab}"]`).classList.add('active');

            // Show corresponding form
            forms.forEach(form => form.classList.remove('active'));
            document.querySelector(`[data-form="${tab}"]`).classList.add('active');
        });
    });

    // Password Visibility Toggle
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            const input = icon.previousElementSibling;
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // Password Strength Indicator
    const passwordInput = document.querySelector('#regPassword');
    const strengthBars = document.querySelectorAll('.strength-bar');
    const strengthText = document.querySelector('.strength-text');

    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;
        let strength = 0;

        if (value.length > 0) strength++;
        if (value.length >= 8) strength++;
        if (/[A-Z]/.test(value) && /[0-9]/.test(value)) strength++;

        strengthBars.forEach((bar, index) => {
            bar.classList.toggle('active', index < strength);
        });

        strengthText.textContent = strength === 1 ? 'Weak' : strength === 2 ? 'Moderate' : 'Strong';
    });

    // Form Submission (Login)
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(loginForm);
        
        // Validate fields
        const emailValidation = validateField(loginForm.querySelector('#loginEmail'), 'email');
        const passwordValidation = validateField(loginForm.querySelector('#loginPassword'), 'password');
        
        if (!emailValidation.valid) {
            alert(emailValidation.message);
            return;
        }
        
        if (!passwordValidation.valid) {
            alert(passwordValidation.message);
            return;
        }
        
        const data = {
            email: formData.get('email'),
            password: formData.get('password')
        };
        
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        })
        .then(response => response.text())
        .then(text => {
            if (text.includes('Invalid email or password')) {
                alert('Invalid email or password');
            } else {
                window.location.href = 'index.php';
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Form Submission (Register)
    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(registerForm);
        
        // Validate all fields
        const validations = [
            validateField(registerForm.querySelector('#regName'), 'name'),
            validateField(registerForm.querySelector('#regEmail'), 'email'),
            validateField(registerForm.querySelector('#regPhone'), 'phone'),
            validateField(registerForm.querySelector('#regPassword'), 'password'),
            validateField(registerForm.querySelector('#regConfirmPassword'), 'password')
        ];
        
        // Check for validation errors
        const errors = validations.filter(v => !v.valid);
        if (errors.length > 0) {
            alert(errors[0].message);
            return;
        }
        
        // Password match validation
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');
        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }
        
        if (!formData.get('terms')) {
            alert('You must agree to the Terms of Service and Privacy Policy');
            return;
        }
        
        // Prepare data for backend
        const data = {
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            password: password,
            confirm_password: confirmPassword
        };
        
        // Send registration request to backend
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        })
        .then(response => response.text())
        .then(text => {
            if (text.includes('Passwords do not match') || text.includes('Registration failed')) {
                alert(text);
            } else {
                window.location.href = 'index.php';
            }
        })
        .catch(error => console.error('Error:', error));
    });
});