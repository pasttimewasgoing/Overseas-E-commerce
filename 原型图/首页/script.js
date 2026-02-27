// 轮播图功能
let currentSlideIndex = 0;
const slides = document.querySelectorAll('.carousel-item');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
    if (n >= slides.length) currentSlideIndex = 0;
    if (n < 0) currentSlideIndex = slides.length - 1;
    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    slides[currentSlideIndex].classList.add('active');
    dots[currentSlideIndex].classList.add('active');
}

function changeSlide(n) {
    currentSlideIndex += n;
    showSlide(currentSlideIndex);
}

function currentSlide(n) {
    currentSlideIndex = n;
    showSlide(currentSlideIndex);
}

// 自动轮播
setInterval(() => {
    currentSlideIndex++;
    showSlide(currentSlideIndex);
}, 5000);

// Tab切换功能
let currentTab = 0;
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

function switchTab(n) {
    currentTab = n;
    
    tabBtns.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));
    
    tabBtns[currentTab].classList.add('active');
    
    // 查找对应的tab内容
    const targetContent = document.querySelector(`.tab-content[data-tab="${n}"]`);
    if (targetContent) {
        targetContent.classList.add('active');
    }
}

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
    // 检查并显示空状态占位符
    checkEmptyProductSections();
    
    // 购买按钮点击事件
    const buyBtns = document.querySelectorAll('.buy-btn');
    buyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            alert('商品已添加到购物车！');
        });
    });
    
    // 视频播放按钮
    const videoPlaceholders = document.querySelectorAll('.video-placeholder');
    videoPlaceholders.forEach(placeholder => {
        placeholder.addEventListener('click', function() {
            alert('视频播放功能待实现');
        });
    });
    
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
    
    // 产品搜索功能（带防抖）
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
    
    // 平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

// 检查产品区域是否为空并显示占位符
function checkEmptyProductSections() {
    // 检查所有产品区域
    const sections = [
        { selector: '.new-products', title: '新品上线' },
        { selector: '.hot-products', title: '热门推荐' },
        { selector: '.more-products', title: '更多产品' }
    ];
    
    sections.forEach(section => {
        const sectionElement = document.querySelector(section.selector);
        if (!sectionElement) return;
        
        // 查找该区域的产品网格
        const productGrid = sectionElement.querySelector('.product-grid');
        if (!productGrid) return;
        
        // 检查是否有产品卡片
        const productCards = productGrid.querySelectorAll('.product-card');
        
        // 如果没有产品，显示占位符
        if (productCards.length === 0) {
            productGrid.innerHTML = `
                <div class="empty-placeholder" style="grid-column: 1 / -1;">
                    <div class="empty-icon">📦</div>
                    <h3>暂无商品</h3>
                    <p>该分类下暂时没有商品，敬请期待</p>
                </div>
            `;
        }
    });
    
    // 检查tab内容区域
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tabContent => {
        const productGrid = tabContent.querySelector('.product-grid');
        if (!productGrid) return;
        
        const productCards = productGrid.querySelectorAll('.product-card');
        
        // 如果没有产品，显示占位符
        if (productCards.length === 0) {
            productGrid.innerHTML = `
                <div class="empty-placeholder" style="grid-column: 1 / -1;">
                    <div class="empty-icon">📦</div>
                    <h3>暂无商品</h3>
                    <p>该分类下暂时没有商品，敬请期待</p>
                </div>
            `;
        }
    });
}
