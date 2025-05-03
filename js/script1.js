document.addEventListener('DOMContentLoaded', () => {
    // Password toggle functionality
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');
    
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
    
    // Ripple effect for the submit button
    const submitBtn = document.querySelector('.btn-ripple');
    
    submitBtn.addEventListener('click', function(e) {
        // Only create ripple if it's a valid form submission
        if (document.querySelector('form').checkValidity()) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('btn-ripple-effect');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }
    });
    
    // Form validation feedback
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        input.addEventListener('invalid', function(e) {
            e.preventDefault();
            this.classList.add('border-red-500');
            
            const errorMsg = document.createElement('p');
            errorMsg.className = 'text-red-500 text-xs mt-1';
            errorMsg.textContent = 'Ce champ est requis';
            
            // Only add error message if not already present
            if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('text-red-500')) {
                this.parentNode.insertBefore(errorMsg, this.nextSibling);
            }
        });
        
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('border-red-500');
                if (this.nextElementSibling && this.nextElementSibling.classList.contains('text-red-500')) {
                    this.nextElementSibling.remove();
                }
            }
        });
    });
    
    // Add subtle animation to form elements
    const formElements = form.querySelectorAll('div.space-y-2');
    formElements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
        el.classList.add('animate-fade-in');
    });
});