/**
 * 首页专用 JavaScript
 * 包含轮播图、标签切换等功能
 */

(function() {
    'use strict';
    
    // 轮播图功能
    const Carousel = {
        currentIndex: 0,
        interval: null,
        slides: [],
        dots: [],
        
        init: function() {
            this.slides = document.querySelectorAll('.carousel-item');
            this.dots = document.querySelectorAll('.dot');
            
            if (this.slides.length === 0) return;
            
            this.startAutoPlay();
            this.bindEvents();
        },
        
        showSlide: function(n) {
            if (n >= this.slides.length) this.currentIndex = 0;
            if (n < 0) this.currentIndex = this.slides.length - 1;
            
            this.slides.forEach(slide => slide.classList.remove('active'));
            this.dots.forEach(dot => dot.classList.remove('active'));
            
            this.slides[this.currentIndex].classList.add('active');
            if (this.dots[this.currentIndex]) {
                this.dots[this.currentIndex].classList.add('active');
            }
        },
        
        changeSlide: function(n) {
            this.currentIndex += n;
            this.showSlide(this.currentIndex);
            this.resetAutoPlay();
        },
        
        goToSlide: function(n) {
            this.currentIndex = n;
            this.showSlide(this.currentIndex);
            this.resetAutoPlay();
        },
        
        startAutoPlay: function() {
            this.interval = setInterval(() => {
                this.currentIndex++;
                this.showSlide(this.currentIndex);
            }, 5000);
        },
        
        resetAutoPlay: function() {
            clearInterval(this.interval);
            this.startAutoPlay();
        },
        
        bindEvents: function() {
            // 绑定前后按钮
            const prevBtn = document.querySelector('.carousel-btn.prev');
            const nextBtn = document.querySelector('.carousel-btn.next');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.changeSlide(-1));
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.changeSlide(1));
            }
            
            // 绑定指示点
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goToSlide(index));
            });
        }
    };
    
    // Tab 切换功能
    const Tabs = {
        currentTab: 0,
        tabBtns: [],
        tabContents: [],
        
        init: function() {
            this.tabBtns = document.querySelectorAll('.tab-btn');
            this.tabContents = document.querySelectorAll('.tab-content');
            
            if (this.tabBtns.length === 0) return;
            
            this.bindEvents();
        },
        
        switchTab: function(n) {
            this.currentTab = n;
            
            this.tabBtns.forEach(btn => btn.classList.remove('active'));
            this.tabContents.forEach(content => content.classList.remove('active'));
            
            if (this.tabBtns[this.currentTab]) {
                this.tabBtns[this.currentTab].classList.add('active');
            }
            
            const targetContent = document.querySelector(`.tab-content[data-tab="${n}"]`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        },
        
        bindEvents: function() {
            this.tabBtns.forEach((btn, index) => {
                btn.addEventListener('click', () => this.switchTab(index));
            });
        }
    };
    
    // 视频播放功能
    const VideoPlayer = {
        init: function() {
            const videoPlaceholders = document.querySelectorAll('.video-placeholder');
            
            videoPlaceholders.forEach(placeholder => {
                placeholder.addEventListener('click', function() {
                    // 这里可以实现实际的视频播放功能
                    // 例如打开模态框或跳转到视频页面
                    console.log('视频播放功能待实现');
                });
            });
        }
    };
    
    // 产品卡片交互
    const ProductCards = {
        init: function() {
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                // 添加鼠标悬停效果
                card.addEventListener('mouseenter', function() {
                    this.style.zIndex = '10';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.zIndex = '1';
                });
            });
        }
    };
    
    // 页面加载完成后初始化
    document.addEventListener('DOMContentLoaded', function() {
        Carousel.init();
        Tabs.init();
        VideoPlayer.init();
        ProductCards.init();
    });
    
    // 暴露全局函数供内联事件使用
    window.changeSlide = function(n) {
        Carousel.changeSlide(n);
    };
    
    window.currentSlide = function(n) {
        Carousel.goToSlide(n);
    };
    
    window.switchTab = function(n) {
        Tabs.switchTab(n);
    };
    
})();
