/**
 * Slider Layout JavaScript
 *
 * Initializes Swiper sliders for DCF Slider layout
 *
 * @package Elementor_Dynamic_Content_Framework
 */

(function($) {
    'use strict';

    /**
     * Initialize DCF Sliders
     */
    function initDCFSliders() {
        // Check if Swiper is available
        if (typeof Swiper === 'undefined') {
            console.error('DCF Slider: Swiper library is not loaded');
            return;
        }

        // Find all slider wrappers
        const sliderWrappers = document.querySelectorAll('.dcf-slider-wrapper');

        sliderWrappers.forEach(function(wrapper) {
            // Skip if already initialized
            if (wrapper.classList.contains('dcf-slider-initialized')) {
                return;
            }

            // Get Swiper configuration from data attribute
            const configAttr = wrapper.getAttribute('data-swiper-config');
            let config = {};

            if (configAttr) {
                try {
                    config = JSON.parse(configAttr);
                } catch (e) {
                    console.error('DCF Slider: Invalid Swiper configuration', e);
                    config = {};
                }
            }

            // Default configuration
            const defaultConfig = {
                loop: true,
                speed: 500,
                autoplay: false,
                navigation: false,
                pagination: false
            };

            // Merge configurations
            const finalConfig = Object.assign({}, defaultConfig, config);

            // Find the swiper container within this wrapper
            const swiperContainer = wrapper.querySelector('.swiper');

            if (!swiperContainer) {
                console.error('DCF Slider: Swiper container not found');
                return;
            }

            // Initialize Swiper
            try {
                const swiper = new Swiper(swiperContainer, finalConfig);

                // Mark as initialized
                wrapper.classList.add('dcf-slider-initialized');

                // Remove loading class if present
                wrapper.classList.remove('loading');

                // Store swiper instance on the wrapper for potential future access
                wrapper.dcfSwiper = swiper;

                // Trigger custom event
                const event = new CustomEvent('dcf-slider-initialized', {
                    detail: { swiper: swiper, wrapper: wrapper }
                });
                wrapper.dispatchEvent(event);

            } catch (e) {
                console.error('DCF Slider: Failed to initialize Swiper', e);
            }
        });
    }

    /**
     * Initialize on DOM ready
     */
    function onReady() {
        initDCFSliders();
    }

    /**
     * Initialize on Elementor frontend
     */
    function onElementorFrontendInit() {
        // Initialize sliders when Elementor widgets are loaded
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
                // Check if this widget contains sliders
                const sliders = $scope.find('.dcf-slider-wrapper');
                if (sliders.length > 0) {
                    initDCFSliders();
                }
            });
        }
    }

    /**
     * Reinitialize sliders (useful for AJAX loaded content)
     */
    window.dcfReinitSliders = function() {
        initDCFSliders();
    };

    // Initialize on different events
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
    } else {
        onReady();
    }

    // Initialize for Elementor frontend
    if (typeof elementorFrontend !== 'undefined') {
        elementorFrontend.hooks.addAction('frontend/element_ready/global', onElementorFrontendInit);
    } else {
        // Fallback if Elementor is not available
        window.addEventListener('elementor/frontend/init', onElementorFrontendInit);
    }

    // Reinitialize on window load (for late-loaded content)
    window.addEventListener('load', function() {
        setTimeout(initDCFSliders, 100);
    });

})(jQuery);
