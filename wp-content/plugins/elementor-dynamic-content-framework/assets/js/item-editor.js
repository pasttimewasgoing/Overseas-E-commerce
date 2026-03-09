/**
 * Item Editor for Elementor Dynamic Content Framework
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/assets/js
 */

(function($) {
	'use strict';

	/**
	 * Item Editor functionality
	 */
	const DCFItemEditor = {
		/**
		 * Initialize item editor
		 */
		init: function() {
			this.bindEvents();
			this.initSortable();
		},

		/**
		 * Bind event handlers
		 */
		bindEvents: function() {
			// Add item button - redirect to add new item
			$('#dcf-add-item').on('click', function() {
				const groupId = $('#dcf-group-id').val();
				window.location.href = dcfAdmin.ajaxUrl.replace('admin-ajax.php', 'admin.php') + '?page=dcf-items&group_id=' + groupId + '&action=new';
			});

			// Remove item buttons
			$(document).on('click', '.dcf-remove-item', function() {
				if (confirm(dcfAdmin.i18n.confirmDelete)) {
					$(this).closest('.dcf-item').remove();
				}
			});

			// Duplicate item buttons
			$(document).on('click', '.dcf-duplicate-item', function() {
				const $button = $(this);
				const $item = $button.closest('.dcf-item');
				const itemId = $item.data('item-id');

				if (!itemId) {
					alert('Invalid item ID');
					return;
				}

				// Disable button during request
				$button.prop('disabled', true).text('Duplicating...');

				// Send AJAX request
				$.ajax({
					url: dcfAdmin.ajaxUrl,
					type: 'POST',
					data: {
						action: 'dcf_duplicate_item',
						nonce: dcfAdmin.nonce,
						item_id: itemId
					},
					success: function(response) {
						if (response.success) {
							// Reload the page to show the new item
							location.reload();
						} else {
							alert(response.data.message || 'Failed to duplicate item');
							$button.prop('disabled', false).text('Duplicate');
						}
					},
					error: function() {
						alert('An error occurred while duplicating the item');
						$button.prop('disabled', false).text('Duplicate');
					}
				});
			});

			// Toggle item content
			$(document).on('click', '.dcf-item-toggle', function() {
				$(this).closest('.dcf-item').find('.dcf-item-content').slideToggle();
			});

			// Repeater field actions
			$(document).on('click', '.dcf-add-repeater-item', function() {
				const repeater = $(this).closest('.dcf-repeater-field');
				DCFItemEditor.addRepeaterItem(repeater);
			});

			$(document).on('click', '.dcf-remove-repeater-item', function() {
				$(this).closest('.dcf-repeater-item').remove();
			});

			// Media upload for item fields
			$(document).on('click', '.dcf-item-media-upload', function(e) {
				e.preventDefault();
				DCFItemEditor.handleItemMediaUpload($(this));
			});

			// Save items order
			$('#dcf-save-items-order').on('click', () => this.saveItemsOrder());
		},

		/**
		 * Initialize sortable items
		 */
		initSortable: function() {
			if ($.fn.sortable) {
				$('.dcf-items-list').sortable({
					handle: '.dcf-item-handle',
					placeholder: 'dcf-item-placeholder',
					update: function(event, ui) {
						DCFItemEditor.updateItemNumbers();
					}
				});

				// Sortable for repeater items
				$(document).on('mouseenter', '.dcf-repeater-items', function() {
					if (!$(this).hasClass('ui-sortable')) {
						$(this).sortable({
							handle: '.dcf-repeater-handle',
							placeholder: 'dcf-repeater-placeholder'
						});
					}
				});
			}
		},

		/**
		 * Add new item (deprecated - now redirects to new page)
		 */
		addItem: function() {
			// This method is deprecated
			// Redirect is handled in bindEvents
			return;
		},

		/**
		 * Get item template HTML
		 */
		getItemTemplate: function() {
			const groupTypeId = $('#dcf-group-type-id').val();
			
			// For now, we'll create a basic template
			// In a real implementation, this would fetch the schema via AJAX
			// and generate fields dynamically
			
			let html = '<div class="dcf-item" data-item-id="">';
			html += '<div class="dcf-item-header">';
			html += '<span class="dcf-item-handle">☰</span>';
			html += '<span class="dcf-item-number">#1</span>';
			html += '<button type="button" class="dcf-item-toggle button button-small">Toggle</button>';
			html += '<div class="dcf-item-actions">';
			html += '<button type="button" class="dcf-duplicate-item button button-small">Duplicate</button>';
			html += '<button type="button" class="dcf-remove-item button button-small dcf-button-danger">Remove</button>';
			html += '</div>';
			html += '</div>';
			html += '<div class="dcf-item-content" style="display:block;">';
			html += '<form method="post" class="dcf-item-form" enctype="multipart/form-data">';
			html += '<input type="hidden" name="dcf_item_nonce" value="' + dcfAdmin.nonce + '">';
			html += '<input type="hidden" name="item_id" value="">';
			html += '<input type="hidden" name="group_id" value="' + $('#dcf-group-id').val() + '">';
			html += '<p class="description">Please add fields based on your group type schema. Save the form to create this item.</p>';
			html += '<div class="dcf-item-form-actions">';
			html += '<button type="submit" class="button button-primary">Save Item</button>';
			html += '</div>';
			html += '</form>';
			html += '</div>';
			html += '</div>';
			return html;
		},

		/**
		 * Update item numbers
		 */
		updateItemNumbers: function() {
			$('.dcf-items-list .dcf-item').each(function(index) {
				$(this).find('.dcf-item-number').text('#' + (index + 1));
			});
		},

		/**
		 * Add repeater item
		 */
		addRepeaterItem: function(repeater) {
			const template = repeater.find('.dcf-repeater-template').html();
			const items = repeater.find('.dcf-repeater-items');
			
			if (template) {
				items.append(template);
			} else {
				// Default repeater item template
				let html = '<div class="dcf-repeater-item">';
				html += '<div class="dcf-repeater-item-header">';
				html += '<span class="dcf-repeater-handle">☰</span>';
				html += '<button type="button" class="dcf-remove-repeater-item button">Remove</button>';
				html += '</div>';
				html += '<div class="dcf-repeater-item-content">';
				html += '<!-- Repeater sub-fields will be rendered here -->';
				html += '</div>';
				html += '</div>';
				items.append(html);
			}
		},

		/**
		 * Handle media upload for item fields
		 */
		handleItemMediaUpload: function(button) {
			const fieldId = button.data('field-id');
			const mediaType = button.data('media-type') || 'image';
			const multiple = button.data('multiple') || false;

			// Create media frame
			const frame = wp.media({
				title: button.data('title') || dcfAdmin.i18n.selectImage,
				button: {
					text: button.data('button-text') || dcfAdmin.i18n.selectFile
				},
				multiple: multiple,
				library: {
					type: mediaType
				}
			});

			// Handle media selection
			frame.on('select', function() {
				if (multiple) {
					// Handle multiple selection (gallery)
					const attachments = frame.state().get('selection').toJSON();
					const ids = attachments.map(att => att.id).join(',');
					$('#' + fieldId).val(ids);
					
					// Update preview
					const preview = button.siblings('.dcf-media-preview');
					if (preview.length) {
						preview.empty();
						attachments.forEach(att => {
							preview.append('<img src="' + att.url + '" alt="">');
						});
					}
				} else {
					// Handle single selection
					const attachment = frame.state().get('selection').first().toJSON();
					$('#' + fieldId).val(attachment.id);
					
					// Update preview
					const preview = button.siblings('.dcf-media-preview');
					if (preview.length) {
						if (mediaType === 'image') {
							preview.html('<img src="' + attachment.url + '" alt="">');
						} else if (mediaType === 'video') {
							preview.html('<video src="' + attachment.url + '" controls></video>');
						} else {
							preview.html('<span>' + attachment.filename + '</span>');
						}
					}
				}
				
				// Show remove button
				button.siblings('.dcf-media-remove-button').show();
			});

			frame.open();
		},

		/**
		 * Save items order via AJAX
		 */
		saveItemsOrder: function() {
			const groupId = $('#dcf-group-id').val();
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
					action: 'dcf_save_items_order',
					nonce: dcfAdmin.nonce,
					group_id: groupId,
					order: order
				},
				beforeSend: function() {
					$('#dcf-save-items-order').prop('disabled', true).text('Saving...');
				},
				success: function(response) {
					if (response.success) {
						alert('Order saved successfully!');
					} else {
						alert('Error saving order: ' + response.data.message);
					}
				},
				error: function() {
					alert('Error saving order. Please try again.');
				},
				complete: function() {
					$('#dcf-save-items-order').prop('disabled', false).text('Save Order');
				}
			});
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		if ($('.dcf-item-editor').length) {
			DCFItemEditor.init();
		}
	});

})(jQuery);
