<?php
/**
 * Define the internationalization functionality.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class DCF_i18n {

	/**
	 * Translations array
	 *
	 * @var array
	 */
	private static $translations = null;

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		// Load standard WordPress translations
		load_plugin_textdomain(
			'elementor-dynamic-content-framework',
			false,
			dirname( DCF_PLUGIN_BASENAME ) . '/languages/'
		);
		
		// Load PHP array translations for Chinese
		$locale = get_locale();
		if ( $locale === 'zh_CN' || $locale === 'zh_Hans' || $locale === 'zh_Hans_CN' ) {
			$translations_file = DCF_PLUGIN_DIR . 'languages/translations-zh_CN.php';
			if ( file_exists( $translations_file ) ) {
				self::$translations = include $translations_file;
				
				// Add translation filter
				add_filter( 'gettext', array( $this, 'translate_text' ), 10, 3 );
				add_filter( 'gettext_with_context', array( $this, 'translate_text_with_context' ), 10, 4 );
			}
		}
	}

	/**
	 * Translate text using PHP array
	 *
	 * @param string $translation Translated text
	 * @param string $text Original text
	 * @param string $domain Text domain
	 * @return string
	 */
	public function translate_text( $translation, $text, $domain ) {
		if ( $domain !== 'elementor-dynamic-content-framework' ) {
			return $translation;
		}
		
		if ( self::$translations && isset( self::$translations[ $text ] ) ) {
			return self::$translations[ $text ];
		}
		
		return $translation;
	}

	/**
	 * Translate text with context using PHP array
	 *
	 * @param string $translation Translated text
	 * @param string $text Original text
	 * @param string $context Context
	 * @param string $domain Text domain
	 * @return string
	 */
	public function translate_text_with_context( $translation, $text, $context, $domain ) {
		if ( $domain !== 'elementor-dynamic-content-framework' ) {
			return $translation;
		}
		
		if ( self::$translations && isset( self::$translations[ $text ] ) ) {
			return self::$translations[ $text ];
		}
		
		return $translation;
	}
}
