/* ============================================
   Zargar Accounting - Animations & Interactions
   Gold Jewelry Theme
   ============================================ */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        
        // ==========================================
        // Smooth Scroll for Internal Links
        // ==========================================
        const links = document.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href !== '') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
        
        // ==========================================
        // Active Menu Item Highlight
        // ==========================================
        const currentPage = new URLSearchParams(window.location.search).get('page');
        const menuLinks = document.querySelectorAll('.sidebar-menu-link');
        
        menuLinks.forEach(link => {
            const linkPage = new URL(link.href).searchParams.get('page');
            const parentItem = link.closest('.sidebar-menu-item');
            
            if (linkPage === currentPage) {
                parentItem?.classList.add('active');
            } else {
                parentItem?.classList.remove('active');
            }
        });
        
        // ==========================================
        // Form Field Animations
        // ==========================================
        const formControls = document.querySelectorAll('.form-control');
        
        formControls.forEach(input => {
            // Floating label effect
            input.addEventListener('focus', function() {
                const label = this.previousElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.style.transform = 'translateY(-5px)';
                    label.style.fontSize = '12px';
                }
            });
            
            input.addEventListener('blur', function() {
                const label = this.previousElementSibling;
                if (label && label.classList.contains('form-label') && !this.value) {
                    label.style.transform = '';
                    label.style.fontSize = '';
                }
            });
            
            // Ripple effect on focus
            input.addEventListener('focus', function() {
                this.style.animation = 'inputFocus 0.3s ease-out';
            });
        });
        
        // ==========================================
        // Button Ripple Effect
        // ==========================================
        const buttons = document.querySelectorAll('.btn');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                const existingRipple = this.querySelector('.ripple');
                if (existingRipple) {
                    existingRipple.remove();
                }
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // ==========================================
        // Widget Hover Effects
        // ==========================================
        const widgets = document.querySelectorAll('.widget');
        
        widgets.forEach((widget, index) => {
            widget.style.animationDelay = (index * 0.1) + 's';
            
            widget.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            widget.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
        
        // ==========================================
        // Parallax Background Effect
        // ==========================================
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const st = window.pageYOffset || document.documentElement.scrollTop;
            const header = document.querySelector('.zargar-header');
            
            if (header) {
                const scrolled = st * 0.3;
                header.style.backgroundPositionY = scrolled + 'px';
            }
            
            lastScrollTop = st <= 0 ? 0 : st;
        }, false);
        
        // ==========================================
        // Gold Particle Effect (Subtle)
        // ==========================================
        function createGoldParticle() {
            const particle = document.createElement('div');
            particle.className = 'gold-particle';
            particle.style.cssText = `
                position: fixed;
                width: 4px;
                height: 4px;
                background: radial-gradient(circle, var(--gold-500) 0%, transparent 70%);
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                opacity: 0;
                animation: particleFloat 3s ease-out forwards;
            `;
            
            particle.style.left = Math.random() * window.innerWidth + 'px';
            particle.style.top = window.innerHeight + 'px';
            
            document.body.appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 3000);
        }
        
        // Create particles periodically
        setInterval(() => {
            if (Math.random() > 0.7) {
                createGoldParticle();
            }
        }, 2000);
        
        // ==========================================
        // Loading Animation for Forms
        // ==========================================
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-primary');
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="loading-spinner"></span> در حال ذخیره...';
                    submitBtn.disabled = true;
                }
            });
        });
        
        // ==========================================
        // Auto-refresh for Logs (if on logs page)
        // ==========================================
        if (currentPage === 'zargar-accounting-logs') {
            const autoRefresh = setInterval(function() {
                // You can implement AJAX refresh here
                console.log('Auto-refresh logs...');
            }, 30000); // Every 30 seconds
        }
        
        // ==========================================
        // Tooltip Initialization
        // ==========================================
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = this.getAttribute('data-tooltip');
                tooltip.style.cssText = `
                    position: absolute;
                    background: rgba(0, 0, 0, 0.9);
                    color: var(--gold-300);
                    padding: 8px 12px;
                    border-radius: 6px;
                    font-size: 12px;
                    pointer-events: none;
                    z-index: 10000;
                    white-space: nowrap;
                    border: 1px solid var(--gold-600);
                `;
                
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
                
                this._tooltip = tooltip;
            });
            
            element.addEventListener('mouseleave', function() {
                if (this._tooltip) {
                    this._tooltip.remove();
                    delete this._tooltip;
                }
            });
        });
        
        console.log('✨ Zargar Accounting - Gold Theme Initialized');
    });
    
    // ==========================================
    // Add Custom CSS Animations
    // ==========================================
    const style = document.createElement('style');
    style.textContent = `
        @keyframes particleFloat {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.8;
            }
            100% {
                transform: translateY(-100vh) translateX(${Math.random() * 100 - 50}px);
                opacity: 0;
            }
        }
        
        @keyframes inputFocus {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.4);
            }
            100% {
                box-shadow: 0 0 0 8px rgba(255, 215, 0, 0);
            }
        }
        
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: rippleEffect 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes rippleEffect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .loading-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    
})();
