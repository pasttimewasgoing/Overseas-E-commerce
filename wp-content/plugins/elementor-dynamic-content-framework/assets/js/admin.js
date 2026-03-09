/**
 * Admin JavaScript for Elementor Dynamic Content Framework
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/assets/js
 */

(function($) {
	'use strict';

	/**
	 * Admin functionality
	 */
	const DCFAdmin = {
		/**
		 * Initialize admin functionality
		 */
		init: function() {
			this.bindEvents();
			this.initSortable();
		},

		/**
		 * Bind event handlers
		 */
		bindEvents: function() {
			// Confirm delete actions
			$('.dcf-delete-action').on('click', function(e) {
				if (!confirm(dcfAdmin.i18n.confirmDelete)) {
					e.preventDefault();
					return false;
				}
			});

			// Media upload buttons
			$('.dcf-media-upload-button').on('click', this.handleMediaUpload);

			// Remove media buttons
			$('.dcf-media-remove-button').on('click', this.handleMediaRemove);

			// Form validation
			$('.dcf-form').on('submit', this.validateForm);
		},

		/**
		 * Initialize sortable lists
		 */
		initSortable: function() {
			if ($.fn.sortable) {
				$('.dcf-items-list').sortable({
					handle: '.dcf-item-handle',
					placeholder: 'dcf-item-placeholder',
					update: function(event, ui) {
						DCFAdmin.updateItemOrder();
					}
				});

				$('.dcf-repeater-items').sortable({
					handle: '.dcf-repeater-handle',
					placeholder: 'dcf-repeater-placeholder'
				});
			}
		},

		/**
		 * Handle media upload
		 */
		handleMediaUpload: function(e) {
			e.preventDefault();

			const button = $(this);
			const fieldId = button.data('field-id');
			const mediaType = button.data('media-type') || 'image';

			// Create media frame
			const frame = wp.media({
				title: button.data('title') || dcfAdmin.i18n.selectImage,
				button: {
					text: button.data('button-text') || dcfAdmin.i18n.selectFile
				},
				multiple: false,
				library: {
					type: mediaType
				}
			});

			// Handle media selection
			frame.on('select', function() {
				const attachment = frame.state().get('selection').first().toJSON();
				
				// Update hidden input
				$('#' + fieldId).val(attachment.id);
				
				// Update preview
				const preview = button.siblings('.dcf-media-preview');
				if (preview.length) {
					if (mediaType === 'image') {
						preview.html('<img src="' + attachment.url + '" alt="">');
					} else {
						preview.html('<span>' + attachment.filename + '</span>');
					}
				}
				
				// Show remove button
				button.siblings('.dcf-media-remove-button').show();
			});

			frame.open();
		},

		/**
		 * Handle media removal
		 */
		handleMediaRemove: function(e) {
			e.preventDefault();

			const button = $(this);
			const fieldId = button.data('field-id');

			// Clear hidden input
			$('#' + fieldId).val('');

			// Clear preview
			button.siblings('.dcf-media-preview').empty();

			// Hide remove button
			button.hide();
		},

		/**
		 * Update item order via AJAX
		 */
		updateItemOrder: function() {
			const order = [];
			$('.dcf-items-list .dcf-item').each(function(index) {
				order.push({
					id: $(this).data('item-id'),
					order: index
				});
			});

			$.ajax({
				url: dcfAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'dcf_update_item_order',
					nonce: dcfAdmin.nonce,
					order: order
				},
				success: function(response) {
					if (response.success) {
						// Show success message
						DCFAdmin.showNotice('success', 'Order updated successfully');
					}
				}
			});
		},

		/**
		 * Validate form before submission
		 */
		validateForm: function(e) {
			const form = $(this);
			let isValid = true;

			// Check required fields
			form.find('[required]').each(function() {
				if (!$(this).val()) {
					isValid = false;
					$(this).addClass('dcf-error');
				} else {
					$(this).removeClass('dcf-error');
				}
			});

			if (!isValid) {
				e.preventDefault();
				DCFAdmin.showNotice('error', 'Please fill in all required fields');
				return false;
			}

			return true;
		},

		/**
		 * Show admin notice
		 */
		showNotice: function(type, message) {
			const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
			$('.dcf-admin-page').prepend(notice);
			
			setTimeout(function() {
				notice.fadeOut(function() {
					$(this).remove();
				});
			}, 3000);
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		DCFAdmin.init();
	});

})(jQuery);

