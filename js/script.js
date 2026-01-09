document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const validateForm = (form) => {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            if(!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#e74c3c';
                const errorMsg = document.createElement('small');
                errorMsg.className = 'error-msg';
                errorMsg.textContent = 'This field is required';
                errorMsg.style.color = '#e74c3c';
                errorMsg.style.display = 'block';
                errorMsg.style.marginTop = '5px';
                input.parentNode.appendChild(errorMsg);
            } else {
                input.style.borderColor = '#ddd';
                const existingError = input.parentNode.querySelector('.error-msg');
                if(existingError) {
                    existingError.remove();
                }
            }
        });
        
        return isValid;
    };

    // Login form validation
    const loginForm = document.querySelector('form[action="index.php"]');
    if(loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if(!validateForm(this)) {
                e.preventDefault();
            }
        });
    }

    // Registration form validation
    const registerForm = document.querySelector('form[action="register.php"]');
    if(registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = this.querySelector('input[name="password"]');
            
            if(!validateForm(this)) {
                e.preventDefault();
                return;
            }
            
            if(password.value.length < 6) {
                e.preventDefault();
                const errorMsg = document.createElement('small');
                errorMsg.className = 'error-msg';
                errorMsg.textContent = 'Password must be at least 6 characters';
                errorMsg.style.color = '#e74c3c';
                errorMsg.style.display = 'block';
                errorMsg.style.marginTop = '5px';
                password.parentNode.appendChild(errorMsg);
                password.style.borderColor = '#e74c3c';
            }
        });
    }

    // Confirm before leaving a group
    document.querySelectorAll('.leave-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            if(!confirm('Are you sure you want to leave this study group?')) {
                e.preventDefault();
            }
        });
    });

    // Fade in animations
    const animateElements = document.querySelectorAll('.group-card, .form-group, .hero');
    animateElements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
});