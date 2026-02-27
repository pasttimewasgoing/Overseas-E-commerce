// 页面加载完成后初始化所有功能
document.addEventListener('DOMContentLoaded', function() {
    // 下拉菜单延迟折叠功能
    const navDropdown = document.querySelector('.nav-dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    let hideTimeout;
    
    if (navDropdown && dropdownMenu) {
        navDropdown.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            dropdownMenu.style.display = 'block';
        });
        
        navDropdown.addEventListener('mouseleave', function() {
            hideTimeout = setTimeout(function() {
                dropdownMenu.style.display = 'none';
            }, 500);
        });
        
        dropdownMenu.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
        });
        
        dropdownMenu.addEventListener('mouseleave', function() {
            hideTimeout = setTimeout(function() {
                dropdownMenu.style.display = 'none';
            }, 500);
        });
    }
    
    // 表单提交处理
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 获取表单数据
            const formData = new FormData(contactForm);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // 简单验证
            if (!data.name || !data.phone || !data.email || !data.subject || !data.message) {
                alert('请填写所有必填项！');
                return;
            }
            
            // 验证邮箱格式
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(data.email)) {
                alert('请输入有效的电子邮箱地址！');
                return;
            }
            
            // 验证电话格式
            const phoneRegex = /^1[3-9]\d{9}$/;
            if (!phoneRegex.test(data.phone)) {
                alert('请输入有效的手机号码！');
                return;
            }
            
            // 模拟提交
            const submitBtn = contactForm.querySelector('.submit-btn');
            submitBtn.textContent = '提交中...';
            submitBtn.disabled = true;
            
            setTimeout(function() {
                alert('留言提交成功！我们会尽快与您联系。');
                contactForm.reset();
                submitBtn.textContent = '提交留言';
                submitBtn.disabled = false;
            }, 1500);
        });
    }
    
    // 表单输入验证提示
    const inputs = document.querySelectorAll('.contact-form input, .contact-form textarea, .contact-form select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.style.borderColor = '#ff4757';
            } else {
                this.style.borderColor = '#ddd';
            }
        });
        
        input.addEventListener('focus', function() {
            this.style.borderColor = '#2c5aa0';
        });
    });
    
    // 平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
