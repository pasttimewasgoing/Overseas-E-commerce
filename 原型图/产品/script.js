// 防抖函数
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

// 页面加载完成后初始化所有功能
document.addEventListener('DOMContentLoaded', function() {
    // 分类栏折叠/展开功能
    const categoryTitles = document.querySelectorAll('.category-title');
    categoryTitles.forEach(title => {
        title.addEventListener('click', function() {
            const categoryGroup = this.parentElement;
            const categoryList = categoryGroup.querySelector('.category-list');
            
            if (categoryList) {
                // 切换显示/隐藏
                if (categoryList.style.display === 'none' || !categoryList.style.display) {
                    categoryList.style.display = 'block';
                    this.classList.add('active');
                } else {
                    categoryList.style.display = 'none';
                    this.classList.remove('active');
                }
            }
        });
    });
    
    // 分类链接点击事件
    const categoryLinks = document.querySelectorAll('.category-list a');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            // 移除所有active类
            categoryLinks.forEach(l => l.classList.remove('active'));
            // 添加当前active类
            this.classList.add('active');
        });
    });
    
    // 购买按钮点击事件
    const buyBtns = document.querySelectorAll('.buy-btn');
    buyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            alert('商品已添加到购物车！');
        });
    });
    
    // 更多按钮点击事件
    const moreBtn = document.querySelector('.more-btn');
    if (moreBtn) {
        moreBtn.addEventListener('click', function() {
            alert('加载更多产品...');
        });
    }
    
    // 主搜索功能
    const mainSearchInput = document.getElementById('mainSearch');
    const searchBtn = document.querySelector('.search-btn');
    const productCards = document.querySelectorAll('.product-card');
    
    function performSearch() {
        const searchTerm = mainSearchInput.value.toLowerCase();
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const desc = card.querySelector('.product-desc').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || desc.includes(searchTerm)) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // 如果没有搜索词，显示所有产品
        if (searchTerm === '') {
            productCards.forEach(card => {
                card.style.display = 'block';
            });
        }
    }
    
    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }
    
    if (mainSearchInput) {
        mainSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    // 下拉菜单延迟折叠功能
    const navDropdown = document.querySelector('.nav-dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    let hideTimeout;
    
    if (navDropdown && dropdownMenu) {
        // 鼠标进入下拉区域时，清除隐藏定时器
        navDropdown.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            dropdownMenu.style.display = 'block';
        });
        
        // 鼠标离开下拉区域时，延迟500毫秒后隐藏
        navDropdown.addEventListener('mouseleave', function() {
            hideTimeout = setTimeout(function() {
                dropdownMenu.style.display = 'none';
            }, 500);
        });
        
        // 鼠标进入下拉菜单时，清除隐藏定时器
        dropdownMenu.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
        });
        
        // 鼠标离开下拉菜单时，延迟500毫秒后隐藏
        dropdownMenu.addEventListener('mouseleave', function() {
            hideTimeout = setTimeout(function() {
                dropdownMenu.style.display = 'none';
            }, 500);
        });
    }
    
    // 下拉菜单产品搜索功能（带防抖）
    const searchInput = document.getElementById('productSearch');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const noResults = document.querySelector('.no-results');
    
    if (searchInput) {
        // 使用防抖，延迟300毫秒执行搜索
        const debouncedSearch = debounce(function(searchTerm) {
            let hasResults = false;
            
            dropdownItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                const matches = text.includes(searchTerm.toLowerCase());
                
                if (matches) {
                    item.classList.remove('hidden');
                    hasResults = true;
                } else {
                    item.classList.add('hidden');
                }
            });
            
            // 显示或隐藏"未找到"提示
            if (noResults) {
                noResults.style.display = hasResults ? 'none' : 'block';
            }
        }, 300);
        
        searchInput.addEventListener('input', function(e) {
            debouncedSearch(e.target.value);
        });
        
        // 阻止搜索框点击时关闭下拉菜单
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
