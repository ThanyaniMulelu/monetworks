// Enhanced JavaScript for contact form with PHP backend integration

// Update the contact form handling in script.js
document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...

    // Enhanced contact form handling with PHP backend
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm(this)) {
                showNotification('Please fill in all required fields correctly.', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            submitBtn.disabled = true;

            // Prepare form data
            const formData = new FormData(this);
            
            // Send to PHP backend
            fetch('contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    this.reset();
                    
                    // Show success message
                    showNotification(data.message, 'success');
                } else {
                    // Show error message
                    showNotification(data.message || 'An error occurred. Please try again.', 'error');
                    
                    // Show specific validation errors if available
                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(error => {
                            showNotification(error, 'error');
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Network error. Please check your connection and try again.', 'error');
            })
            .finally(() => {
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// Enhanced form validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        // Remove existing validation classes
        input.classList.remove('is-invalid', 'is-valid');
        
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.add('is-valid');
        }
        
        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                isValid = false;
            }
        }
        
        // Phone validation (optional but if provided, should be valid)
        if (input.name === 'phone' && input.value) {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
            if (!phoneRegex.test(input.value)) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

// Enhanced notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 
                 'fa-info-circle';
    
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${icon} me-2"></i>
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;

    // Add styles
    const bgColor = type === 'success' ? '#28a745' : 
                    type === 'error' ? '#dc3545' : 
                    '#007bff';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 350px;
        margin-bottom: 10px;
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Close button functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        margin-left: 10px;
        padding: 0;
    `;
    
    closeBtn.addEventListener('click', () => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    });

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }
    }, 5000);
}

