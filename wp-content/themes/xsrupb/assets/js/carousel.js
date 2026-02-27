/**
 * Carousel JavaScript
 *
 * @package XSRUPB
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * 轮播图管理对象
     */
    var XSRUPBCarousel = {
        
        /**
         * 初始化
         */
        init: function() {
            this.initSwiper();
        },
        
        /**
         * 初始化 Swiper 轮播图
         */
        initSwiper: function() {
            if (typeof Swiper === 'undefined') {
                return;
            }
            
            // 主轮播图
            if ($('.main-carousel').length) {
                new Swiper('.main-carousel', {
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });
            }
            
            // 产品轮播图
            if ($('.product-carousel').length) {
                new Swiper('.product-carousel', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: false,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 3,
                        },
                        1024: {
                            slidesPerView: 4,
                        },
                    },
                });
            }
        }
    };
    
    // 文档就绪时初始化
    $(document).ready(function() {
        XSRUPBCarousel.init();
    });
    
})(jQuery);
