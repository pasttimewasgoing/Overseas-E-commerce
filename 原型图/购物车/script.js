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
    
    // 优惠券折叠功能
    const couponToggle = document.querySelector('.coupon-toggle');
    const couponContent = document.querySelector('.coupon-content');
    
    if (couponToggle && couponContent) {
        // 默认折叠
        couponContent.classList.add('hidden');
        couponToggle.classList.add('collapsed');
        
        couponToggle.addEventListener('click', function() {
            couponContent.classList.toggle('hidden');
            couponToggle.classList.toggle('collapsed');
        });
    }
    
    // 数量控制
    const cartItems = document.querySelectorAll('.cart-item');
    
    cartItems.forEach(item => {
        const minusBtn = item.querySelector('.minus');
        const plusBtn = item.querySelector('.plus');
        const qtyInput = item.querySelector('.qty-input');
        const itemPrice = parseFloat(item.querySelector('.item-price').textContent.replace('$', ''));
        const totalPriceEl = item.querySelector('.total-price');
        
        function updateItemTotal() {
            const quantity = parseInt(qtyInput.value);
            const total = (itemPrice * quantity).toFixed(2);
            totalPriceEl.textContent = '$' + total;
            updateOrderSummary();
        }
        
        minusBtn.addEventListener('click', function() {
            let value = parseInt(qtyInput.value);
            if (value > 1) {
                qtyInput.value = value - 1;
                updateItemTotal();
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(qtyInput.value);
            qtyInput.value = value + 1;
            updateItemTotal();
        });
        
        qtyInput.addEventListener('change', function() {
            if (parseInt(this.value) < 1) {
                this.value = 1;
            }
            updateItemTotal();
        });
        
        // 删除项目
        const removeBtn = item.querySelector('.remove-btn');
        removeBtn.addEventListener('click', function() {
            if (confirm('确定要删除此商品吗？')) {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    item.remove();
                    updateOrderSummary();
                    checkEmptyCart();
                }, 300);
            }
        });
    });
    
    // 更新订单摘要
    function updateOrderSummary() {
        const items = document.querySelectorAll('.cart-item');
        let subtotal = 0;
        
        items.forEach(item => {
            const totalPrice = parseFloat(item.querySelector('.total-price').textContent.replace('$', ''));
            subtotal += totalPrice;
        });
        
        document.querySelector('.subtotal').textContent = '$' + subtotal.toFixed(2);
        document.querySelector('.total-amount').textContent = '$' + subtotal.toFixed(2);
    }
    
    // 检查购物车是否为空
    function checkEmptyCart() {
        const items = document.querySelectorAll('.cart-item');
        if (items.length === 0) {
            const cartItems = document.querySelector('.cart-items');
            cartItems.innerHTML = `
                <div style="text-align: center; padding: 60px 20px;">
                    <p style="font-size: 48px; margin-bottom: 20px;">🛒</p>
                    <h3 style="font-size: 24px; margin-bottom: 15px; color: #1a1a1a;">购物车是空的</h3>
                    <p style="color: #666; margin-bottom: 30px;">快去选购您喜欢的产品吧！</p>
                    <a href="../产品/index.html" style="display: inline-block; padding: 12px 30px; background: #2c5aa0; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600;">浏览产品</a>
                </div>
            `;
        }
    }
    
    // 优惠码应用
    const applyBtn = document.querySelector('.apply-btn');
    const couponInput = document.querySelector('.coupon-input');
    
    if (applyBtn && couponInput) {
        applyBtn.addEventListener('click', function() {
            const code = couponInput.value.trim().toUpperCase();
            
            if (code === '') {
                alert('请输入优惠码');
                return;
            }
            
            // 模拟优惠码验证
            if (code === 'SAVE10') {
                alert('优惠码已应用！您获得了10%的折扣');
                couponInput.value = '';
                // 这里可以添加实际的折扣计算逻辑
            } else {
                alert('无效的优惠码');
            }
        });
    }
    
    // 结账按钮
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            const items = document.querySelectorAll('.cart-item');
            if (items.length === 0) {
                alert('购物车是空的，请先添加商品');
                return;
            }
            alert('正在跳转到结账页面...');
            // 这里可以跳转到实际的结账页面
        });
    }
    
    // 推荐产品加入购物车
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            const productName = card.querySelector('h3').textContent;
            
            this.textContent = '已添加 ✓';
            this.style.background = '#28a745';
            this.style.color = '#fff';
            this.style.borderColor = '#28a745';
            
            setTimeout(() => {
                this.textContent = '加入购物车';
                this.style.background = '';
                this.style.color = '';
                this.style.borderColor = '';
            }, 2000);
        });
    });
});
