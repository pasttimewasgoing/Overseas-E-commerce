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
    
    // 分类筛选功能
    const categoryTabs = document.querySelectorAll('.category-tab');
    const blogCards = document.querySelectorAll('.blog-card');
    
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // 更新激活状态
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // 筛选博客卡片
            blogCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                
                if (category === 'all' || cardCategory === category) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
    
    // 为博客卡片添加过渡效果
    blogCards.forEach(card => {
        card.style.transition = 'opacity 0.3s, transform 0.3s';
    });
    
    // 分页按钮功能
    const pageButtons = document.querySelectorAll('.page-btn');
    pageButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.textContent === '下一页' || this.textContent === '上一页') {
                return;
            }
            
            pageButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // 滚动到顶部
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
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
