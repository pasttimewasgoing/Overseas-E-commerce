/**
 * Schema Builder for Elementor Dynamic Content Framework
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/assets/js
 */

(function($) {
	'use strict';

	/**
	 * Schema Builder functionality
	 */
	const DCFSchemaBuilder = {
		/**
		 * Field type templates
		 */
		fieldTypes: {
			text: { label: 'Text', properties: ['label', 'default_value', 'placeholder', 'max_length'] },
			textarea: { label: 'Textarea', properties: ['label', 'default_value', 'placeholder', 'rows'] },
			image: { label: 'Image', properties: ['label', 'allowed_formats', 'max_size_mb'] },
			video: { label: 'Video', properties: ['label', 'allowed_formats', 'max_size_mb', 'allow_url'] },
			url: { label: 'URL', properties: ['label', 'placeholder', 'validation_pattern'] },
			icon: { label: 'Icon', properties: ['label', 'icon_library'] },
			gallery: { label: 'Gallery', properties: ['label', 'max_images', 'allowed_formats'] },
			repeater: { label: 'Repeater', properties: ['label', 'min_items', 'max_items', 'sub_fields'] }
		},

		/**
		 * Initialize schema builder
		 */
		init: function() {
			this.bindEvents();
			this.initSortable();
		},

		/**
		 * Bind event handlers
		 */
		bindEvents: function() {
			// Add field button
			$('#dcf-add-field').on('click', () => this.addField());

			// Remove field buttons
			$(document).on('click', '.dcf-remove-field', function() {
				$(this).closest('.dcf-schema-field').remove();
			});

			// Field type change
			$(document).on('change', '.dcf-field-type', function() {
				const fieldType = $(this).val();
				const field = $(this).closest('.dcf-schema-field');
				DCFSchemaBuilder.updateFieldProperties(field, fieldType);
			});

			// Generate slug from name
			$('#dcf-group-type-name').on('input', function() {
				const name = $(this).val();
				const slug = DCFSchemaBuilder.generateSlug(name);
				$('#dcf-group-type-slug').val(slug);
			});

			// Form submission validation - remove incomplete fields
			$('.dcf-group-type-form').on('submit', function(e) {
				console.log('Form submitting, checking for incomplete fields...');
				
				// Remove any empty or incomplete fields before submission
				$('.dcf-schema-field').each(function() {
					const fieldType = $(this).find('.dcf-field-type').val();
					const fieldName = $(this).find('input[name="schema[fields][name][]"]').val();
					const fieldLabel = $(this).find('input[name="schema[fields][label][]"]').val();
					
					console.log('Checking field:', { fieldType, fieldName, fieldLabel });
					
					// Remove field if any required value is empty
					if (!fieldType || !fieldName || !fieldLabel) {
						console.log('Removing incomplete field');
						$(this).remove();
					}
				});
				
				console.log('Remaining fields:', $('.dcf-schema-field').length);
			});
		},

		/**
		 * Initialize sortable fields
		 */
		initSortable: function() {
			if ($.fn.sortable) {
				$('.dcf-schema-fields').sortable({
					handle: '.dcf-field-handle',
					placeholder: 'dcf-field-placeholder'
				});
			}
		},

		/**
		 * Add new field to schema
		 */
		addField: function() {
			const fieldHtml = this.getFieldTemplate();
			$('.dcf-schema-fields').append(fieldHtml);
		},

		/**
		 * Get field template HTML
		 */
		getFieldTemplate: function(fieldData = {}) {
			const fieldType = fieldData.type || 'text';
			const fieldName = fieldData.name || '';
			const fieldLabel = fieldData.label || '';

			let html = '<div class="dcf-schema-field">';
			html += '<div class="dcf-schema-field-header">';
			html += '<span class="dcf-field-handle">☰</span>';
			html += '<div class="dcf-schema-field-actions">';
			html += '<button type="button" class="dcf-remove-field button">Remove</button>';
			html += '</div>';
			html += '</div>';
			
			html += '<div class="dcf-form-group">';
			html += '<label>Field Type</label>';
			html += '<select class="dcf-field-type" name="schema[fields][type][]">';
			
			for (const [type, config] of Object.entries(this.fieldTypes)) {
				const selected = type === fieldType ? ' selected' : '';
				html += `<option value="${type}"${selected}>${config.label}</option>`;
			}
			
			html += '</select>';
			html += '</div>';
			
			html += '<div class="dcf-form-group">';
			html += '<label>Field Name (slug)</label>';
			html += `<input type="text" name="schema[fields][name][]" value="${fieldName}" required>`;
			html += '</div>';
			
			html += '<div class="dcf-form-group">';
			html += '<label>Field Label</label>';
			html += `<input type="text" name="schema[fields][label][]" value="${fieldLabel}" required>`;
			html += '</div>';
			
			html += '<div class="dcf-field-properties">';
			html += this.getFieldPropertiesHtml(fieldType, fieldData);
			html += '</div>';
			
			html += '</div>';

			return html;
		},

		/**
		 * Get field properties HTML based on type
		 */
		getFieldPropertiesHtml: function(fieldType, fieldData = {}) {
			const properties = this.fieldTypes[fieldType]?.properties || [];
			let html = '';

			properties.forEach(prop => {
				const value = fieldData[prop] || '';
				
				switch(prop) {
					case 'label':
					case 'default_value':
					case 'placeholder':
					case 'validation_pattern':
						html += `<div class="dcf-form-group">`;
						html += `<label>${this.formatLabel(prop)}</label>`;
						html += `<input type="text" name="schema[fields][${prop}][]" value="${value}">`;
						html += `</div>`;
						break;
					
					case 'max_length':
					case 'rows':
					case 'max_size_mb':
					case 'max_images':
					case 'min_items':
					case 'max_items':
						html += `<div class="dcf-form-group">`;
						html += `<label>${this.formatLabel(prop)}</label>`;
						html += `<input type="number" name="schema[fields][${prop}][]" value="${value}">`;
						html += `</div>`;
						break;
					
					case 'allowed_formats':
						html += `<div class="dcf-form-group">`;
						html += `<label>${this.formatLabel(prop)}</label>`;
						html += `<input type="text" name="schema[fields][${prop}][]" value="${value}" placeholder="jpg,png,webp">`;
						html += `</div>`;
						break;
					
					case 'allow_url':
						html += `<div class="dcf-form-group">`;
						html += `<label><input type="checkbox" name="schema[fields][${prop}][]" ${value ? 'checked' : ''}> ${this.formatLabel(prop)}</label>`;
						html += `</div>`;
						break;
					
					case 'icon_library':
						html += `<div class="dcf-form-group">`;
						html += `<label>${this.formatLabel(prop)}</label>`;
						html += `<select name="schema[fields][${prop}][]">`;
						html += `<option value="fontawesome" ${value === 'fontawesome' ? 'selected' : ''}>Font Awesome</option>`;
						html += `<option value="custom" ${value === 'custom' ? 'selected' : ''}>Custom</option>`;
						html += `</select>`;
						html += `</div>`;
						break;
				}
			});

			return html;
		},

		/**
		 * Update field properties when type changes
		 */
		updateFieldProperties: function(field, fieldType) {
			const propertiesContainer = field.find('.dcf-field-properties');
			const html = this.getFieldPropertiesHtml(fieldType);
			propertiesContainer.html(html);
		},

		/**
		 * Format property label
		 */
		formatLabel: function(prop) {
			return prop.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
		},

		/**
		 * Generate slug from name
		 */
		generateSlug: function(name) {
			return name
				.toLowerCase()
				.replace(/[^a-z0-9]+/g, '-')
				.replace(/^-+|-+$/g, '');
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		if ($('.dcf-schema-builder').length) {
			DCFSchemaBuilder.init();
		}
	});

})(jQuery);
