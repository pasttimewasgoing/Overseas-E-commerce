(function ($) {
	var WidgetElements_ViewsHandler = function ($scope, $) {
		var id_scope = $scope.attr("data-id");
		var elementSettings = dceGetElementSettings($scope);
		
		// Early return if not slideshow or table with DataTables
		if (elementSettings.dce_views_style_format !== "slideshow" && 
			!(elementSettings.dce_views_style_format === "table" && elementSettings.dce_views_style_table_data)) {
			return;
		}
		
		let swiper_class = dceIsSwiperLatest()
			? ".swiper"
			: ".swiper-container";
		var elementSwiper = $scope.find(swiper_class);
		var speed = elementSettings.transition_speed;
		var disableOnInteraction =
			Boolean(elementSettings.pause_on_interaction) || false;
		var loop = false;
		// Handle slideshow format with Swiper
		if (elementSettings.dce_views_style_format === "slideshow") {
			if ("yes" === elementSettings.infinite) {
				loop = true;
			}

			var id_post = $scope.attr("data-post-id");

			var viewsSwiperOptions = {
				autoHeight: true,
				speed: speed,
				loop: loop,
			};

			// Responsive Parameters
			viewsSwiperOptions.breakpoints = dynamicooo.makeSwiperBreakpoints(
				{
					slidesPerView: {
						elementor_key: "slides_to_show",
						default_value: "auto",
					},
					slidesPerGroup: {
						elementor_key: "slides_to_scroll",
						default_value: 1,
					},
					spaceBetween: {
						elementor_key: "spaceBetween",
						default_value: 0,
					},
				},
				elementSettings,
			);

			// Navigation
			if (elementSettings.navigation != "none") {
				if (
					elementSettings.navigation == "both" ||
					elementSettings.navigation == "arrows"
				) {
					viewsSwiperOptions = $.extend(viewsSwiperOptions, {
						navigation: {
							nextEl: id_post
								? ".elementor-element-" +
									id_scope +
									'[data-post-id="' +
									id_post +
									'"] .elementor-swiper-button-next'
								: ".elementor-swiper-button-next",
							prevEl: id_post
								? ".elementor-element-" +
									id_scope +
									'[data-post-id="' +
									id_post +
									'"] .elementor-swiper-button-prev'
								: ".elementor-swiper-button-prev",
						},
					});
				}

				if (
					elementSettings.navigation == "both" ||
					elementSettings.navigation == "dots"
				) {
					viewsSwiperOptions = $.extend(viewsSwiperOptions, {
						pagination: {
							el: id_post
								? ".elementor-element-" +
									id_scope +
									'[data-post-id="' +
									id_post +
									'"] .swiper-pagination'
								: ".swiper-pagination",
							type: "bullets",
							clickable: true,
						},
					});
				}
			}

			// Autoplay
			if (elementSettings.autoplay) {
				viewsSwiperOptions = $.extend(viewsSwiperOptions, {
					autoplay: {
						autoplay: true,
						delay: elementSettings.autoplay_speed,
						disableOnInteraction: disableOnInteraction,
					},
				});
			}

			const asyncSwiper = elementorFrontend.utils.swiper;

			new asyncSwiper(elementSwiper, viewsSwiperOptions)
				.then((newSwiperInstance) => {
					viewsSwiper = newSwiperInstance;
				})
				.catch((error) => console.log(error));

			// Pause on hover
			if (elementSettings.autoplay && elementSettings.pause_on_hover) {
				$(elementSwiper).on({
					mouseenter: function () {
						viewsSwiper.autoplay.stop();
					},
					mouseleave: function () {
						viewsSwiper.autoplay.start();
					},
				});
			}
			
			return; // Exit early for slideshow format
		}
		
		// Handle table format with DataTables
		if (elementSettings.dce_views_style_format === "table" && elementSettings.dce_views_style_table_data) {
			var $table = $scope.find('table.dce-datatable');
			
			var options = {
				order: [],
				ordering: true
			};
			
			// Add language URL from separate i18n files
			if (typeof window.getDataTablesLanguageUrl === 'function') {
				var language = $('html').attr('lang') || 'en';
				var languageUrl = window.getDataTablesLanguageUrl(language);
				
				if (languageUrl) {
					options.language = {
						url: languageUrl
					};
				}
			}
			
			if (elementSettings.dce_views_style_table_data_autofill) {
				options.autoFill = true;
			}
			
			if (elementSettings.dce_views_style_table_data_buttons) {
				options.dom = 'Bfrtip';
				options.buttons = [
					'copyHtml5',
					'excelHtml5',
					'csvHtml5',
					'pdfHtml5'
				];
			}
			
			if (elementSettings.dce_views_style_table_data_colreorder) {
				options.colReorder = true;
			}
			
			if (elementSettings.dce_views_style_table_data_fixedcolumns) {
				options.fixedColumns = true;
			}
			
			if (elementSettings.dce_views_style_table_data_fixedheader) {
				options.fixedHeader = true;
			}
			
			if (elementSettings.dce_views_style_table_data_keytable) {
				options.keys = true;
			}
			
			if (elementSettings.dce_views_style_table_data_responsive) {
				options.responsive = true;
			}
			
			if (elementSettings.dce_views_style_table_data_rowgroup) {
				options.rowGroup = {
					dataSrc: 'group'
				};
			}
			
			if (elementSettings.dce_views_style_table_data_rowreorder) {
				options.rowReorder = true;
			}
			
			if (elementSettings.dce_views_style_table_data_scroller) {
				options.scroller = true;
				options.scrollX = true;
				if (elementSettings.dce_views_style_table_data_scroller_y) {
					options.scrollY = 200;
				}
				options.paging = true;
				options.deferRender = true;
			} else {
				options.paging = false;
			}
			
			if (elementSettings.dce_views_style_table_data_select) {
				options.select = true;
			}
			
			$table.DataTable(options);
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dce-views.default",
			WidgetElements_ViewsHandler,
		);
	});
})(jQuery);
