// public/js/app.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Toggle comment reply forms
    const replyButtons = document.querySelectorAll('.reply-btn');
    const cancelReplyButtons = document.querySelectorAll('.cancel-reply-btn');
    
    replyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            document.getElementById(`reply-form-${commentId}`).style.display = 'block';
        });
    });
    
    cancelReplyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            document.getElementById(`reply-form-${commentId}`).style.display = 'none';
        });
    });
    
    // Store comment form data in localStorage if checkbox is checked
    const commentForm = document.querySelector('form[action*="comentarios"]');
    if (commentForm) {
        const saveInfoCheckbox = document.getElementById('saveInfo');
        const guestNameInput = document.getElementById('guest_name');
        const guestEmailInput = document.getElementById('guest_email');
        
        // Load saved data if available
        if (localStorage.getItem('commentName')) {
            guestNameInput.value = localStorage.getItem('commentName');
        }
        if (localStorage.getItem('commentEmail')) {
            guestEmailInput.value = localStorage.getItem('commentEmail');
        }
        if (localStorage.getItem('saveCommentInfo') === 'true') {
            saveInfoCheckbox.checked = true;
        }
        
        // Save data when form is submitted
        commentForm.addEventListener('submit', function() {
            if (saveInfoCheckbox && saveInfoCheckbox.checked) {
                localStorage.setItem('commentName', guestNameInput.value);
                localStorage.setItem('commentEmail', guestEmailInput.value);
                localStorage.setItem('saveCommentInfo', 'true');
            } else {
                localStorage.removeItem('commentName');
                localStorage.removeItem('commentEmail');
                localStorage.removeItem('saveCommentInfo');
            }
        });
    }
    
    // Add active class to current link
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.navbar-nav a.nav-link');
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (linkPath && currentLocation.startsWith(linkPath) && linkPath !== '/') {
            link.classList.add('active');
        } else if (linkPath === '/' && currentLocation === '/') {
            link.classList.add('active');
        }
    });
    
    // Back to top button
    const backToTopButton = document.getElementById('back-to-top');
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Lazy load images
    const lazyImages = document.querySelectorAll('img.lazy');
    if (lazyImages.length > 0) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const image = entry.target;
                    image.src = image.dataset.src;
                    if (image.dataset.srcset) {
                        image.srcset = image.dataset.srcset;
                    }
                    image.classList.remove('lazy');
                    observer.unobserve(image);
                }
            });
        });
        
        lazyImages.forEach(image => {
            imageObserver.observe(image);
        });
    }
    
    // Initialize code highlighting if highlight.js is loaded
    if (typeof hljs !== 'undefined') {
        document.querySelectorAll('pre code').forEach((el) => {
            hljs.highlightElement(el);
        });
    }
    
    // Newsletter signup form validation and submission
    const newsletterForm = document.querySelector('.newsletter form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            // Simple email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Por favor, ingresa un correo electrónico válido.');
                return;
            }
            
            // Here you would typically submit the form via AJAX
            // For now, we'll just show a success message
            const formContainer = this.parentElement;
            formContainer.innerHTML = '<div class="alert alert-success">¡Gracias por suscribirte! Te has registrado correctamente.</div>';
        });
    }
});
