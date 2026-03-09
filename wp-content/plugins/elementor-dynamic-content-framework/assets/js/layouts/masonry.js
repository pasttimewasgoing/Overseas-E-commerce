/**
 * Masonry Layout JavaScript
 *
 * Handles masonry/waterfall layout functionality
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/assets/js/layouts
 */

(function($) {
	'use strict';

	/**
	 * Initialize masonry layout
	 */
	function initMasonry($scope) {
		var $masonry = $scope.find('.dcf-masonry-layout');
		
		if ($masonry.length === 0) {
			return;
		}

		// Simple CSS-based masonry using column-count
		// No additional JavaScript needed as CSS handles the layout
		// This is a placeholder for future enhancements
		
		// Trigger layout recalculation after images load
		$masonry.find('img').on('load', function() {
			// Force reflow
			$masonry.hide().show(0);
		});
	}

	// Initialize on document ready
	$(document).ready(function() {
		$('.dcf-masonry-layout').each(function() {
			initMasonry($(this).closest('.elementor-widget-dcf-dynamic-content'));
		});
	});

	// Initialize in Elementor editor
	if (window.elementorFrontend) {
		$(window).on('elementor/frontend/init', function() {
			elementorFrontend.hooks.addAction('frontend/element_ready/dcf-dynamic-content.default', function($scope) {
				var settings = $scope.find('.dcf-masonry-layout').data('settings');
				if (settings && settings.layout === 'masonry') {
					initMasonry($scope);
				}
			});
		});
	}

})(jQuery);
