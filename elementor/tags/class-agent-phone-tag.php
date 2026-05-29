<?php
/**
 * Elementor Dynamic Tag - Agent Phone
 *
 * Adds "Agent Phone" to Elementor's Dynamic Tags menu under Houzez group.
 * Retrieves agent mobile phone from the related houzez_agent post when
 * viewing a property post.
 *
 * @package EstateSiteHouzez
 * @since 1.3.13
 */

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class EstateSite_Agent_Phone_Tag extends Tag {

    /**
     * Get tag name (unique identifier)
     *
     * @return string
     */
    public function get_name() {
        return 'estatesite-agent-phone';
    }

    /**
     * Get tag title (displayed in Elementor)
     *
     * @return string
     */
    public function get_title() {
        return __('Agent Phone', 'estatesite-houzez');
    }

    /**
     * Get tag group
     * Uses the Houzez group constant if available, falls back to 'houzez'
     *
     * @return string
     */
    public function get_group() {
        // Use Houzez's group constant if class exists
        if (class_exists('Houzez_Elementor_Extensions')) {
            return Houzez_Elementor_Extensions::HOUZEZ_GROUP;
        }
        return 'houzez';
    }

    /**
     * Get tag categories
     * TEXT_CATEGORY allows use in text fields
     * URL_CATEGORY allows use in link fields (for tel: links)
     *
     * @return array
     */
    public function get_categories() {
        return [
            TagsModule::TEXT_CATEGORY,
            TagsModule::URL_CATEGORY,
        ];
    }

    /**
     * Register tag controls
     * These appear when clicking the wrench icon in Elementor
     */
    protected function register_controls() {
        $this->add_control(
            'agent_index',
            [
                'label'       => __('Agent Number', 'estatesite-houzez'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 0,
                'min'         => 0,
                'description' => __('Which agent to show (0 = first agent, 1 = second, etc.)', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'format',
            [
                'label'   => __('Format', 'estatesite-houzez'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'raw'       => __('Raw Number', 'estatesite-houzez'),
                    'formatted' => __('Formatted (0888 123 456)', 'estatesite-houzez'),
                    'tel'       => __('Tel URL (tel:0888123456)', 'estatesite-houzez'),
                ],
                'default' => 'raw',
            ]
        );

        $this->add_control(
            'prefix',
            [
                'label'       => __('Prefix', 'estatesite-houzez'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __('Text before phone', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'suffix',
            [
                'label'       => __('Suffix', 'estatesite-houzez'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __('Text after phone', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'fallback',
            [
                'label'       => __('Fallback Text', 'estatesite-houzez'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __('Text if no phone found', 'estatesite-houzez'),
            ]
        );
    }

    /**
     * Render the dynamic tag output
     */
    public function render() {
        $settings = $this->get_settings();
        $property_id = get_the_ID();

        // Validate we're on a property
        if (!$property_id || get_post_type($property_id) !== 'property') {
            echo esc_html($settings['fallback']);
            return;
        }

        // Get agent phone using the helper function from shortcode file
        if (function_exists('estatesite_get_property_agent_phone')) {
            $phone = estatesite_get_property_agent_phone($property_id, intval($settings['agent_index']));
        } else {
            $phone = $this->get_agent_phone($property_id, intval($settings['agent_index']));
        }

        if (empty($phone)) {
            echo esc_html($settings['fallback']);
            return;
        }

        // Format and output
        $output = esc_html($settings['prefix']) . $this->format_phone($phone, $settings['format']) . esc_html($settings['suffix']);

        echo $output;
    }

    /**
     * Get agent phone number for a property (fallback if shortcode not loaded)
     *
     * @param int $property_id Property post ID
     * @param int $agent_index Which agent to get
     * @return string Agent phone number or empty string
     */
    private function get_agent_phone($property_id, $agent_index = 0) {
        $agent_ids = get_post_meta($property_id, 'fave_agents', true);

        if (empty($agent_ids)) {
            return '';
        }

        if (!is_array($agent_ids)) {
            $agent_ids = array($agent_ids);
        }

        if (!isset($agent_ids[$agent_index])) {
            return '';
        }

        $agent_id = intval($agent_ids[$agent_index]);

        if (!$agent_id || get_post_type($agent_id) !== 'houzez_agent') {
            return '';
        }

        $agent_mobile = get_post_meta($agent_id, 'fave_agent_mobile', true);

        return sanitize_text_field($agent_mobile);
    }

    /**
     * Format phone number
     *
     * @param string $phone Phone number
     * @param string $format Format type
     * @return string Formatted phone
     */
    private function format_phone($phone, $format) {
        switch ($format) {
            case 'tel':
                // Return clean tel: URL (for use in link fields)
                $clean_phone = preg_replace('/[^0-9+]/', '', $phone);
                return 'tel:' . esc_attr($clean_phone);

            case 'formatted':
                // Format phone nicely (Bulgarian format: 0888 123 456)
                $formatted = preg_replace('/(\d{4})(\d{3})(\d{3})/', '$1 $2 $3', $phone);
                return esc_html($formatted);

            case 'raw':
            default:
                return esc_html($phone);
        }
    }
}
