<?php
/**
 * WC Category Slider Metabox Template
 *
 * This file is used to display the metabox for the WC Category Slider plugin.
 *
 * @sicne 1.0.0
 * @package WooCommerceCategorySlider
 * @var \WP_Post $post The current post object.
 */

defined( 'ABSPATH' ) || exit;

$navs = array(
	'Categories',
	'Display Settings',
	'Slider Settings',
	'Font Settings',
);
?>

<div class="ever-row">
	<div class="ever-col-12">
		<div class="ever-tabs">
			<?php
			$active = 'active';
			foreach ( $navs as $nav ) {
				$icon = ''; // Tab menu icons.
				switch ( $nav ) {
					case 'Categories':
						$icon = 'align-justify';
						break;
					case 'Display Settings':
						$icon = 'tv';
						break;
					case 'Slider Settings':
						$icon = 'sliders';
						break;
					case 'Font Settings':
						$icon = 'font';
						break;
				}

				// === tab nav label ===
				$label    = $nav;
				$template = sanitize_title( $nav );

				// === tab nav item ===
				printf(
					'<a href="#" class="tab-item %1$s" data-target="%2$s"><span class="fa fa-%3$s"></span> %4$s</a>',
					esc_attr( $active ),
					esc_attr( $template ),
					esc_attr( $icon ),
					esc_attr( $label )
				);

				$active = '';
			}
			?>
		</div>
		<div class="tab-content">
			<?php
			$active = 'active';
			foreach ( $navs as $nav ) {
				$template = sanitize_title( $nav );
				// === tab content item ===
				printf(
					'<div class="tab-content-item %1$s" id="%2$s">',
					esc_attr( $active ),
					esc_attr( $template )
				);

				switch ( $nav ) {
					case 'Categories':
						include __DIR__ . '/metabox-categories.php';
						break;
					case 'Display Settings':
						include __DIR__ . '/metabox-display-settings.php';
						break;
					case 'Slider Settings':
						include __DIR__ . '/metabox-slider-settings.php';
						break;
					case 'Font Settings':
						include __DIR__ . '/metabox-font-settings.php';
						break;
				}
				echo '</div>';
				$active = '';
			}
			?>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function ($) {
		//===  handle active tab ===
		function CategorySliderSetActiveTab($target) {
			$('.tab-item, .tab-content-item').removeClass('active');
			$('.tab-item[data-target="' + $target + '"]').addClass('active');
			$('.tab-content-item[id="' + $target + '"]').addClass('active');
			if (typeof(localStorage) !== 'undefined') {
				localStorage.setItem("wc_category_slider_active_tab", $target);
			}
		}

		var activeTab = 'categories';
		if (typeof(localStorage) !== 'undefined') {
			activeTab = localStorage.getItem('wc_category_slider_active_tab') || 'categories';
		}

		CategorySliderSetActiveTab(activeTab);
		$('.tab-item').on('click', function (e) {
			e.preventDefault();
			var $target = $(this).data('target');
			CategorySliderSetActiveTab($target);
		});

		//=== Custom css editor ===
		wp.codeEditor.initialize($('#custom_css'), WCS.codeEditor);
	});
</script>


