<?php
/**
 * Elementor Dynamic Content Widget
 *
 * A universal Elementor widget for displaying dynamic content groups with various layouts.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/widgets
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Elementor_Widget class
 *
 * Extends Elementor's Widget_Base to create a dynamic content widget.
 */
class DCF_Elementor_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve the widget name used to identify the widget.
     *
     * @since  1.0.0
     * @access public
     * @return string Widget name.
     */
    public function get_name(): string {
        return 'dcf-dynamic-content';
    }

    /**
     * Get widget title.
     *
     * Retrieve the widget title displayed in the Elementor editor.
     *
     * @since  1.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title(): string {
        return __('Dynamic Content', 'elementor-dynamic-content-framework');
    }

    /**
     * Get widget icon.
     *
     * Retrieve the widget icon displayed in the Elementor editor.
     *
     * @since  1.0.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon(): string {
        return 'eicon-database';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since  1.0.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories(): array {
        return ['general'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since  1.0.0
     * @access public
     * @return array Widget keywords.
     */
    public function get_keywords(): array {
        return ['dynamic', 'content', 'data', 'dcf', 'framework'];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function register_controls(): void {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'elementor-dynamic-content-framework'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Content Group Selection
        $this->add_control(
            'content_group',
            [
                'label'       => __('Content Group', 'elementor-dynamic-content-framework'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'options'     => $this->get_content_groups(),
                'default'     => '',
                'label_block' => true,
                'description' => __('Select a content group to display', 'elementor-dynamic-content-framework'),
            ]
        );

        // Layout Selection
        $this->add_control(
            'layout',
            [
                'label'       => __('Layout', 'elementor-dynamic-content-framework'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'options'     => $this->get_layouts(),
                'default'     => 'grid',
                'label_block' => true,
                'description' => __('Select a layout to render the content', 'elementor-dynamic-content-framework'),
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render(): void {
        // Mark that widget is present on the page for asset loading
        if (class_exists('DCF_Assets')) {
            DCF_Assets::mark_widget_present();
        }

        $settings = $this->get_settings_for_display();

        // Check if content group is selected
        if (empty($settings['content_group'])) {
            $this->render_placeholder();
            return;
        }

        // Check if layout is selected
        if (empty($settings['layout'])) {
            echo '<div class="dcf-widget-error">';
            echo esc_html__('Please select a layout.', 'elementor-dynamic-content-framework');
            echo '</div>';
            return;
        }

        // Get the layout engine
        $layout_engine = new DCF_Layout_Engine();

        // Render the content using the layout engine
        try {
            $group_id = absint($settings['content_group']);
            $layout_slug = sanitize_key($settings['layout']);
            
            // Get layout-specific settings (will be implemented in task 8.2)
            $layout_settings = [];

            echo $layout_engine->render($group_id, $layout_slug, $layout_settings);
        } catch (Exception $e) {
            echo '<div class="dcf-widget-error">';
            echo esc_html__('Error rendering content: ', 'elementor-dynamic-content-framework');
            echo esc_html($e->getMessage());
            echo '</div>';
        }
    }

    /**
     * Render widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function content_template(): void {
        ?>
        <# if (!settings.content_group) { #>
            <div class="dcf-widget-placeholder">
                <i class="eicon-database"></i>
                <h3><?php echo esc_html__('Dynamic Content Widget', 'elementor-dynamic-content-framework'); ?></h3>
                <p><?php echo esc_html__('Select a content group to display', 'elementor-dynamic-content-framework'); ?></p>
            </div>
        <# } else { #>
            <div class="dcf-widget-preview">
                <p><?php echo esc_html__('Content Group:', 'elementor-dynamic-content-framework'); ?> {{ settings.content_group }}</p>
                <p><?php echo esc_html__('Layout:', 'elementor-dynamic-content-framework'); ?> {{ settings.layout }}</p>
                <p><em><?php echo esc_html__('Preview will be rendered on the frontend', 'elementor-dynamic-content-framework'); ?></em></p>
            </div>
        <# } #>
        <?php
    }

    /**
     * Render placeholder message when no content group is selected.
     *
     * @since  1.0.0
     * @access private
     */
    private function render_placeholder(): void {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<div class="dcf-widget-placeholder">';
            echo '<i class="eicon-database"></i>';
            echo '<h3>' . esc_html__('Dynamic Content Widget', 'elementor-dynamic-content-framework') . '</h3>';
            echo '<p>' . esc_html__('Select a content group to display', 'elementor-dynamic-content-framework') . '</p>';
            echo '</div>';
        }
    }

    /**
     * Get available content groups for the dropdown.
     *
     * @since  1.0.0
     * @access private
     * @return array Content groups as [id => title]
     */
    private function get_content_groups(): array {
        $groups = [];
        
        // Add empty option
        $groups[''] = __('-- Select Content Group --', 'elementor-dynamic-content-framework');

        // Get all active content groups
        try {
            $all_groups = DCF_Group::get_all(['status' => 'active']);
            
            foreach ($all_groups as $group) {
                $groups[$group['id']] = $group['title'];
            }
        } catch (Exception $e) {
            // If there's an error, just return the empty option
            error_log('DCF Widget: Error loading content groups - ' . $e->getMessage());
        }

        return $groups;
    }

    /**
     * Get available layouts for the dropdown.
     *
     * @since  1.0.0
     * @access private
     * @return array Layouts as [slug => name]
     */
    private function get_layouts(): array {
        $layouts = [];

        // Get all registered layouts
        $all_layouts = dcf_get_layouts();

        foreach ($all_layouts as $slug => $config) {
            $layouts[$slug] = $config['name'];
        }

        // If no layouts are registered, provide a default option
        if (empty($layouts)) {
            $layouts['grid'] = __('Grid', 'elementor-dynamic-content-framework');
        }

        return $layouts;
    }
}
