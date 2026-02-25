/**
 * TechVision Theme JavaScript
 */

(function() {
    'use strict';
    
    // 轮播图功能
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-item');
    const dots = document.querySelectorAll('.carousel-dots .dot');
    
    // 切换幻灯片
    window.changeSlide = function(direction) {
        if (slides.length === 0) return;
        
        slides[currentSlide].classList.remove('active');
        if (dots.length > 0) dots[currentSlide].classList.remove('active');
        
        currentSlide += direction;
        
        if (currentSlide >= slides.length) {
            currentSlide = 0;
        } else if (currentSlide < 0) {
            currentSlide = slides.length - 1;
        }
        
        slides[currentSlide].classList.add('active');
        if (dots.length > 0) dots[currentSlide].classList.add('active');
    };
    
    // 直接跳转到指定幻灯片
    window.currentSlide = function(index) {
        if (slides.length === 0) return;
        
        slides[currentSlide].classList.remove('active');
        if (dots.length > 0) dots[currentSlide].classList.remove('active');
        
        currentSlide = index;
        
        slides[currentSlide].classList.add('active');
        if (dots.length > 0) dots[currentSlide].classList.add('active');
    };
    
    // 自动播放轮播图
    if (slides.length > 0) {
        setInterval(function() {
            changeSlide(1);
        }, 5000);
    }
    
    // 标签切换功能
    window.switchTab = function(tabIndex) {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        if (tabButtons.length === 0 || tabContents.length === 0) return;
        
        // 移除所有活动状态
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // 添加活动状态到选中的标签
        if (tabButtons[tabIndex]) tabButtons[tabIndex].classList.add('active');
        if (tabContents[tabIndex]) tabContents[tabIndex].classList.add('active');
    };
    
    // 平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '#0') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // 移动端菜单切换
    const createMobileMenu = function() {
        const nav = document.querySelector('.nav');
        if (!nav) return;
        
        const menuToggle = document.createElement('button');
        menuToggle.className = 'mobile-menu-toggle';
        menuToggle.innerHTML = '☰';
        menuToggle.setAttribute('aria-label', '菜单');
        
        const navMenu = document.querySelector('.nav-menu');
        if (!navMenu) return;
        
        // 在小屏幕上添加菜单按钮
        if (window.innerWidth <= 768) {
            if (!document.querySelector('.mobile-menu-toggle')) {
                nav.insertBefore(menuToggle, navMenu);
            }
            
            menuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                this.classList.toggle('active');
            });
        }
    };
    
    // 页面加载完成后初始化
    document.addEventListener('DOMContentLoaded', function() {
        createMobileMenu();
        
        // 监听窗口大小变化
        window.addEventListener('resize', function() {
            createMobileMenu();
        });
    });
    
    // 添加到购物车动画
    document.querySelectorAll('.buy-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            // 如果按钮有onclick属性，不执行动画
            if (this.hasAttribute('onclick')) return;
            
            e.preventDefault();
            
            // 添加点击动画
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
            
            // 这里可以添加AJAX请求到购物车
            console.log('添加到购物车');
        });
    });
    
    // 产品数量选择器
    window.updateQuantity = function(change) {
        const input = document.getElementById('product-quantity');
        if (!input) return;
        
        let currentValue = parseInt(input.value) || 1;
        let newValue = currentValue + change;
        
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 999;
        
        if (newValue >= min && newValue <= max) {
            input.value = newValue;
        }
    };
    
    // 加入购物车
    window.addToCart = function(productId) {
        const quantity = document.getElementById('product-quantity');
        const qty = quantity ? parseInt(quantity.value) : 1;
        
        // 这里添加AJAX请求
        console.log('添加产品到购物车:', productId, '数量:', qty);
        
        // 显示提示
        alert('产品已添加到购物车！');
    };
    
    // 立即购买
    window.buyNow = function(productId) {
        const quantity = document.getElementById('product-quantity');
        const qty = quantity ? parseInt(quantity.value) : 1;
        
        console.log('立即购买:', productId, '数量:', qty);
        
        // 跳转到结账页面
        // window.location.href = '/checkout?product=' + productId + '&qty=' + qty;
    };
    
    // 产品详情标签切换
    window.showProductTab = function(tabId) {
        const buttons = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('.tab-panel');
        
        buttons.forEach(btn => btn.classList.remove('active'));
        panels.forEach(panel => panel.classList.remove('active'));
        
        const activeButton = document.querySelector(`.tab-button[onclick*="${tabId}"]`);
        const activePanel = document.getElementById(tabId);
        
        if (activeButton) activeButton.classList.add('active');
        if (activePanel) activePanel.classList.add('active');
    };
    
    // 懒加载图片
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
})();
