/**
 * Frontend JavaScript
 * 
 * Handles frontend interactions for DCF widgets
 */

(function($) {
	'use strict';

	/**
	 * DCF Frontend Handler
	 */
	const DCF_Frontend = {
		/**
		 * Initialize
		 */
		init: function() {
			this.initPopups();
			this.initLazyLoad();
		},

		/**
		 * Initialize popup functionality
		 */
		initPopups: function() {
			$(document).on('click', '.dcf-popup-trigger', function(e) {
				e.preventDefault();
				const targetId = $(this).data('popup-target');
				const $popup = $('#' + targetId);
				
				if ($popup.length) {
					$popup.addClass('dcf-popup-active');
					$('body').addClass('dcf-popup-open');
				}
			});

			$(document).on('click', '.dcf-popup-close, .dcf-popup-overlay', function(e) {
				e.preventDefault();
				$(this).closest('.dcf-popup').removeClass('dcf-popup-active');
				$('body').removeClass('dcf-popup-open');
			});

			// Close on ESC key
			$(document).on('keydown', function(e) {
				if (e.key === 'Escape' && $('.dcf-popup-active').length) {
					$('.dcf-popup-active').removeClass('dcf-popup-active');
					$('body').removeClass('dcf-popup-open');
				}
			});
		},

		/**
		 * Initialize lazy loading for images
		 */
		initLazyLoad: function() {
			if ('loading' in HTMLImageElement.prototype) {
				// Browser supports native lazy loading
				return;
			}

			// Fallback for browsers that don't support native lazy loading
			const lazyImages = document.querySelectorAll('img[loading="lazy"]');
			
			if ('IntersectionObserver' in window) {
				const imageObserver = new IntersectionObserver(function(entries, observer) {
					entries.forEach(function(entry) {
						if (entry.isIntersecting) {
							const img = entry.target;
							img.src = img.dataset.src || img.src;
							img.classList.add('dcf-loaded');
							imageObserver.unobserve(img);
						}
					});
				});

				lazyImages.forEach(function(img) {
					imageObserver.observe(img);
				});
			} else {
				// Fallback for older browsers
				lazyImages.forEach(function(img) {
					img.src = img.dataset.src || img.src;
				});
			}
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		DCF_Frontend.init();
	});

	/**
	 * Reinitialize on Elementor preview
	 */
	$(window).on('elementor/frontend/init', function() {
		DCF_Frontend.init();
	});

})(jQuery);
