document.addEventListener('DOMContentLoaded', function() {
    // 标签切换
    const authTabs = document.querySelectorAll('.auth-tab');
    const authForms = document.querySelectorAll('.auth-form-container');
    
    authTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // 移除所有活动状态
            authTabs.forEach(t => t.classList.remove('active'));
            authForms.forEach(f => f.classList.remove('active'));
            
            // 添加活动状态
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // 密码强度检测
    const registerPassword = document.getElementById('registerPassword');
    const strengthBar = document.querySelector('.strength-bar');
    
    if (registerPassword && strengthBar) {
        registerPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthBar.className = 'strength-bar';
            
            if (strength === 0) {
                strengthBar.style.width = '0';
            } else if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength === 3) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });
    }
    
    // 登录注册表单提交
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const remember = document.querySelector('input[name="remember"]').checked;
            
            // 表单验证
            if (!email || !password) {
                alert('请填写所有必填字段');
                return;
            }
            
            // 邮箱格式验证
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('请输入有效的邮箱地址');
                return;
            }
            
            // 显示加载状态
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = '登录注册中...';
            submitBtn.disabled = true;
            
            // 模拟API调用
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // 这里应该发送到后端API
                console.log('登录注册信息:', { email, password, remember });
                
                // 模拟登录注册成功
                alert('登录注册成功！欢迎回来');
                
                // 如果选择了记住我，保存到localStorage
                if (remember) {
                    localStorage.setItem('rememberedEmail', email);
                }
                
                // 跳转到首页
                window.location.href = '../首页/index.html';
            }, 1500);
        });
        
        // 自动填充记住的邮箱
        const rememberedEmail = localStorage.getItem('rememberedEmail');
        if (rememberedEmail) {
            document.getElementById('loginEmail').value = rememberedEmail;
            document.querySelector('input[name="remember"]').checked = true;
        }
    }
    
    // 注册表单提交
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.querySelector('input[name="terms"]').checked;
            const newsletter = document.querySelector('input[name="newsletter"]').checked;
            
            // 表单验证
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                alert('请填写所有必填字段');
                return;
            }
            
            // 邮箱格式验证
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('请输入有效的邮箱地址');
                return;
            }
            
            if (password !== confirmPassword) {
                alert('两次输入的密码不一致');
                return;
            }
            
            if (password.length < 8) {
                alert('密码长度至少为8个字符');
                return;
            }
            
            if (!terms) {
                alert('请同意服务条款和隐私政策');
                return;
            }
            
            // 显示加载状态
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = '创建中...';
            submitBtn.disabled = true;
            
            // 模拟API调用
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // 这里应该发送到后端API
                console.log('注册信息:', { 
                    firstName, 
                    lastName, 
                    email, 
                    password,
                    newsletter 
                });
                
                // 模拟注册成功
                alert('注册成功！欢迎加入我们\n\n验证邮件已发送到您的邮箱，请查收。');
                
                // 跳转到首页
                window.location.href = '../首页/index.html';
            }, 1500);
        });
    }
    
    // 社交登录注册按钮
    const socialBtns = document.querySelectorAll('.social-btn');
    socialBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const provider = this.getAttribute('data-provider');
            const providerName = this.textContent.trim();
            
            // 显示加载状态
            const originalText = this.innerHTML;
            this.innerHTML = '<span>⏳</span> 连接中...';
            this.disabled = true;
            
            // 模拟社交登录注册流程
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
                
                // 根据不同的提供商处理登录注册
                switch(provider) {
                    case 'google':
                        alert('正在使用 Google 账户登录注册...\n\n在实际应用中，这里会打开 Google OAuth 授权窗口。');
                        // 实际应用中应该调用：
                        // window.location.href = '/auth/google';
                        break;
                    case 'facebook':
                        alert('正在使用 Facebook 账户登录注册...\n\n在实际应用中，这里会打开 Facebook OAuth 授权窗口。');
                        // 实际应用中应该调用：
                        // window.location.href = '/auth/facebook';
                        break;
                    case 'twitter':
                        alert('正在使用 X (Twitter) 账户登录注册...\n\n在实际应用中，这里会打开 X OAuth 授权窗口。');
                        // 实际应用中应该调用：
                        // window.location.href = '/auth/twitter';
                        break;
                }
            }, 1000);
        });
    });
    
    // 忘记密码
    const forgotLink = document.querySelector('.forgot-link');
    if (forgotLink) {
        forgotLink.addEventListener('click', function(e) {
            e.preventDefault();
            const email = prompt('请输入您的注册邮箱地址：');
            if (email) {
                alert('密码重置链接已发送到您的邮箱，请查收');
                // 这里应该调用密码重置API
            }
        });
    }
});
