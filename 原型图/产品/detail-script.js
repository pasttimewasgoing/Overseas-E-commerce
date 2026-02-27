// 导航栏下拉菜单
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.nav-dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdown && dropdownMenu) {
        let timeout;
        
        dropdown.addEventListener('mouseenter', function() {
            clearTimeout(timeout);
            dropdownMenu.style.display = 'block';
        });
        
        dropdown.addEventListener('mouseleave', function() {
            timeout = setTimeout(function() {
                dropdownMenu.style.display = 'none';
            }, 300);
        });
    }
    
    // 缩略图切换
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.main-image .image-placeholder');
    
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', function() {
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            // 这里可以添加实际的图片切换逻辑
        });
    });
    
    // 颜色选择
    const colorBtns = document.querySelectorAll('.color-btn');
    colorBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            colorBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // 尺寸选择
    const sizeBtns = document.querySelectorAll('.size-btn');
    sizeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            sizeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // 数量控制
    const minusBtn = document.querySelector('.qty-btn.minus');
    const plusBtn = document.querySelector('.qty-btn.plus');
    const qtyInput = document.querySelector('.qty-input');
    
    if (minusBtn && plusBtn && qtyInput) {
        minusBtn.addEventListener('click', function() {
            let value = parseInt(qtyInput.value);
            if (value > 1) {
                qtyInput.value = value - 1;
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(qtyInput.value);
            qtyInput.value = value + 1;
        });
        
        qtyInput.addEventListener('change', function() {
            if (parseInt(this.value) < 1) {
                this.value = 1;
            }
        });
    }
    
    // 加入购物车
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const originalText = this.textContent;
            this.textContent = '已添加到购物车 ✓';
            this.style.background = '#28a745';
            this.style.color = '#fff';
            this.style.borderColor = '#28a745';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.background = '';
                this.style.color = '';
                this.style.borderColor = '';
            }, 2000);
        });
    }
    
    // 立即购买
    const buyNowBtn = document.querySelector('.buy-now-btn');
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function() {
            alert('正在跳转到结账页面...');
            // 这里可以跳转到结账页面
            // window.location.href = '../购物车/index.html';
        });
    }
    
    // 收藏按钮
    const wishlistBtn = document.querySelector('.wishlist-btn');
    if (wishlistBtn) {
        let isFavorite = false;
        wishlistBtn.addEventListener('click', function() {
            isFavorite = !isFavorite;
            if (isFavorite) {
                this.style.color = '#ff4757';
                this.style.borderColor = '#ff4757';
                this.textContent = '❤️';
            } else {
                this.style.color = '';
                this.style.borderColor = '';
                this.textContent = '❤';
            }
        });
    }
    
    // 标签页切换
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // 移除所有活动状态
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            
            // 添加活动状态
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // 快速添加按钮
    const quickAddBtns = document.querySelectorAll('.quick-add-btn');
    quickAddBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const originalText = this.textContent;
            this.textContent = '已添加 ✓';
            this.style.background = '#28a745';
            this.style.color = '#fff';
            this.style.borderColor = '#28a745';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.background = '';
                this.style.color = '';
                this.style.borderColor = '';
            }, 2000);
        });
    });
});
