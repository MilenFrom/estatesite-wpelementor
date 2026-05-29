<?php
/**
 * Property Sub-Units Widget
 * 
 * Displays sub-units from eas_multi_units or fave_multi_units postmeta fields
 */

// Namespace to avoid conflicts
namespace EstateSite\Elementor\Widgets;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Use necessary Elementor classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;

class Property_Sub_Units extends Widget_Base {

    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

    public function get_name() {

        return 'estatesite_property_sub_units';

    }

    public function get_title() {

        return esc_html__('Property Sub-Units', 'estatesite-houzez');
    
    }

    public function get_icon() {

        return 'estatesite-element-icon eicon-table';
    
    }

    public function get_categories() {

        return ['estatesite-elements'];

    }

    public function get_badge() {
        return 'Estate Site';
    }

    public function get_keywords() {

        return ['property', 'apartment', 'sub-units', 'units', 'multi-units', 'table', 'estate'];
    
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'property_id',
            [
                'label' => esc_html__('Property ID', 'estatesite-houzez'),
                'type' => Controls_Manager::NUMBER,
                'description' => esc_html__('Leave empty to use current property ID', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'table_headers',
            [
                'label' => esc_html__('Show Table Headers', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_inquiry_button',
            [
                'label' => esc_html__('Show Inquiry Button', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_gallery_button',
            [
                'label' => esc_html__('Show Gallery Button', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Show a button to view apartment photos', 'estatesite-houzez'),
            ]
        );

        $this->end_controls_section();

        // Columns Visibility section
        $this->start_controls_section(
            'columns_visibility_section',
            [
                'label' => esc_html__('Columns Visibility', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title_column',
            [
                'label' => esc_html__('Show Title Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price_column',
            [
                'label' => esc_html__('Show Price Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price_postfix_column',
            [
                'label' => esc_html__('Show Price Postfix Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Display the price postfix (e.g., /month, /year) in a separate column', 'estatesite-houzez'),
            ]
        );

        // Removed beds and baths column controls as they don't exist in the API data
        
        $this->add_control(
            'show_apno_column',
            [
                'label' => esc_html__('Show Apartment Number Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'apno_header_text',
            [
                'label' => esc_html__('Apartment No. Header Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Apartment No.', 'estatesite-houzez'),
                'condition' => [
                    'show_apno_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_size_column',
            [
                'label' => esc_html__('Show Size Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_type_column',
            [
                'label' => esc_html__('Show Type Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_availability_column',
            [
                'label' => esc_html__('Show Availability Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        // Enhanced apartment data fields (from EstateAssistant)
        $this->add_control(
            'enhanced_fields_heading',
            [
                'label' => esc_html__('Enhanced Fields', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        
        $this->add_control(
            'show_building_column',
            [
                'label' => esc_html__('Show Building Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'show_entrance_column',
            [
                'label' => esc_html__('Show Entrance Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'show_floor_column',
            [
                'label' => esc_html__('Show Floor Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'show_direction_column',
            [
                'label' => esc_html__('Show Direction/Aspect Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'show_description_column',
            [
                'label' => esc_html__('Show Description Column', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        // Header Text section
        $this->start_controls_section(
            'header_text_section',
            [
                'label' => esc_html__('Header Text', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'table_headers' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_header_text',
            [
                'label' => esc_html__('Title Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Title', 'estatesite-houzez'),
                'condition' => [
                    'show_title_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'price_header_text',
            [
                'label' => esc_html__('Price Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Price', 'estatesite-houzez'),
                'condition' => [
                    'show_price_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'price_postfix_header_text',
            [
                'label' => esc_html__('Price Postfix Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Price Term', 'estatesite-houzez'),
                'condition' => [
                    'show_price_postfix_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'beds_header_text',
            [
                'label' => esc_html__('Beds Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Beds', 'estatesite-houzez'),
                'condition' => [
                    'show_beds_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'baths_header_text',
            [
                'label' => esc_html__('Baths Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Baths', 'estatesite-houzez'),
                'condition' => [
                    'show_baths_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'size_header_text',
            [
                'label' => esc_html__('Size Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Size', 'estatesite-houzez'),
                'condition' => [
                    'show_size_column' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'size_suffix',
            [
                'label' => esc_html__('Area Suffix', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'sq.m',
                'description' => esc_html__('Custom suffix to display after the area size (e.g., sq.m, m², ft²)', 'estatesite-houzez'),
                'condition' => [
                    'show_size_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'type_header_text',
            [
                'label' => esc_html__('Type Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Type', 'estatesite-houzez'),
                'condition' => [
                    'show_type_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'availability_header_text',
            [
                'label' => esc_html__('Availability Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Availability', 'estatesite-houzez'),
                'condition' => [
                    'show_availability_column' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'building_column_text',
            [
                'label' => esc_html__('Building Column Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Building', 'estatesite-houzez'),
                'condition' => [
                    'show_building_column' => 'yes',
                    'table_headers' => 'yes'
                ],
            ]
        );
        
        $this->add_control(
            'entrance_column_text',
            [
                'label' => esc_html__('Entrance Column Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Entrance', 'estatesite-houzez'),
                'condition' => [
                    'show_entrance_column' => 'yes',
                    'table_headers' => 'yes'
                ],
            ]
        );
        
        $this->add_control(
            'floor_column_text',
            [
                'label' => esc_html__('Floor Column Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Floor', 'estatesite-houzez'),
                'condition' => [
                    'show_floor_column' => 'yes',
                    'table_headers' => 'yes'
                ],
            ]
        );
        
        $this->add_control(
            'direction_column_text',
            [
                'label' => esc_html__('Direction Column Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Direction', 'estatesite-houzez'),
                'condition' => [
                    'show_direction_column' => 'yes',
                    'table_headers' => 'yes'
                ],
            ]
        );
        
        $this->add_control(
            'description_column_text',
            [
                'label' => esc_html__('Description Column Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Description', 'estatesite-houzez'),
                'condition' => [
                    'show_description_column' => 'yes',
                    'table_headers' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'action_header_text',
            [
                'label' => esc_html__('Action Header', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Action', 'estatesite-houzez'),
                'condition' => [
                    'show_inquiry_button' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
        
        // Direction Labels Section
        $this->start_controls_section(
            'direction_labels_section',
            [
                'label' => esc_html__('Direction Labels', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'show_direction_column' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'direction_n_label',
            [
                'label' => esc_html__('North (N) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'N',
                'description' => esc_html__('Custom label for North direction (e.g., Северно)', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'direction_ne_label',
            [
                'label' => esc_html__('North East (NE) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'NE',
                'description' => esc_html__('Custom label for North East direction (e.g., Североизточно)', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'direction_e_label',
            [
                'label' => esc_html__('East (E) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'E',
                'description' => esc_html__('Custom label for East direction (e.g., Източно)', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'direction_se_label',
            [
                'label' => esc_html__('South East (SE) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'SE',
                'description' => esc_html__('Custom label for South East direction (e.g., Югоизточно)', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'direction_s_label',
            [
                'label' => esc_html__('South (S) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'S',
                'description' => esc_html__('Custom label for South direction (e.g., Южно)', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'direction_sw_label',
            [
                'label' => esc_html__('South West (SW) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'SW',
                'description' => esc_html__('Custom label for South West direction (e.g., Югозападно)', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'direction_w_label',
            [
                'label' => esc_html__('West (W) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'W',
                'description' => esc_html__('Custom label for West direction (e.g., Западно)', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'direction_nw_label',
            [
                'label' => esc_html__('North West (NW) Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => 'NW',
                'description' => esc_html__('Custom label for North West direction (e.g., Северозападно)', 'estatesite-houzez'),
            ]
        );
        
        $this->end_controls_section();
        
        // Button section for both inquiry and gallery buttons
        $this->start_controls_section(
            'button_section',
            [
                'label' => esc_html__('Action buttons info', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_inquiry_button',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_gallery_button',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__('Inquiry Button Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Inquire', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'gallery_button_text',
            [
                'label' => esc_html__('Gallery Button Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Gallery', 'estatesite-houzez'),
                'condition' => [
                    'show_gallery_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'hide_button_text',
            [
                'label' => esc_html__('Hide Button Text', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'estatesite-houzez'),
                'label_off' => esc_html__('No', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Show only icons without text', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'button_order',
            [
                'label' => esc_html__('Buttons Order', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'inquiry_first',
                'options' => [
                    'inquiry_first' => esc_html__('Inquiry | Gallery', 'estatesite-houzez'),
                    'gallery_first' => esc_html__('Gallery | Inquiry', 'estatesite-houzez'),
                ],
                'description' => esc_html__('Select the order in which buttons should appear', 'estatesite-houzez'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'show_inquiry_button',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_gallery_button',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'popup_type',
            [
                'label' => esc_html__('Inquiry Action Type', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'elementor',
                'options' => [
                    'elementor' => esc_html__('Elementor Popup', 'estatesite-houzez'),
                    'custom' => esc_html__('Custom Action', 'estatesite-houzez'),
                ],
                'condition' => [
                    'show_inquiry_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'elementor_popup_id',
            [
                'label' => esc_html__('Elementor Popup ID', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'popup_type' => 'elementor',
                    'show_inquiry_button' => 'yes',
                ],
                'description' => esc_html__('Enter the Elementor Popup ID. Create a popup in Templates > Popups and enter the template ID here.', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'custom_action',
            [
                'label' => esc_html__('Custom Action', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'popup_type' => 'custom',
                ],
                'description' => esc_html__('Enter a custom action for the button, e.g., "mailto:info@example.com" or "#contact-form"', 'estatesite-houzez'),
            ]
        );

        $this->end_controls_section();
        
        // Inquiry Button Style section
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => esc_html__('Inquiry Button Style', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_inquiry_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => 'unset',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => esc_html__('Hover Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_hover_text_color',
            [
                'label' => esc_html__('Hover Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__('Hover Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 8,
                    'right' => 16,
                    'bottom' => 8,
                    'left' => 16,
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Gallery Button Style section
        $this->start_controls_section(
            'gallery_button_style_section',
            [
                'label' => esc_html__('Gallery Button Style', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_gallery_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_bg_color',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => 'unset',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_text_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_hover_bg_color',
            [
                'label' => esc_html__('Hover Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_hover_text_color',
            [
                'label' => esc_html__('Hover Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_hover_border_color',
            [
                'label' => esc_html__('Hover Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_button_padding',
            [
                'label' => esc_html__('Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 8,
                    'right' => 16,
                    'bottom' => 8,
                    'left' => 16,
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-gallery-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Table Style section
        $this->start_controls_section(
            'table_style_section',
            [
                'label' => esc_html__('Table Style', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'table_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .property-sub-units-table th, {{WRAPPER}} .property-sub-units-table td' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'header_bg_color',
            [
                'label' => esc_html__('Header Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .property-sub-units-table thead th' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'header_text_color',
            [
                'label' => esc_html__('Header Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .property-sub-units-table thead th' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'row_hover_color',
            [
                'label' => esc_html__('Row Hover Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f9f9f9',
                'selectors' => [
                    '{{WRAPPER}} .property-sub-units-table tbody tr:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'cell_padding',
            [
                'label' => esc_html__('Cell Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-sub-units-table th, {{WRAPPER}} .property-sub-units-table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Icon settings
        $this->start_controls_section(
            'icon_section',
            [
                'label' => esc_html__('Buttons', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'show_inquiry_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'show_inquiry_icon',
            [
                'label' => esc_html__('Show Inquiry Button Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_inquiry_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'show_gallery_icon',
            [
                'label' => esc_html__('Show Gallery Button Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'estatesite-houzez'),
                'label_off' => esc_html__('Hide', 'estatesite-houzez'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_gallery_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'inquiry_icon',
            [
                'label' => esc_html__('Inquiry Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-envelope',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_inquiry_icon' => 'yes',
                    'show_inquiry_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'gallery_icon',
            [
                'label' => esc_html__('Gallery Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-images',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_gallery_icon' => 'yes',
                    'show_gallery_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'icon_position',
            [
                'label' => esc_html__('Icon Position', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'before',
                'options' => [
                    'before' => esc_html__('Before', 'estatesite-houzez'),
                    'after' => esc_html__('After', 'estatesite-houzez'),
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_inquiry_icon',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_gallery_icon',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'icon_spacing',
            [
                'label' => esc_html__('Icon Spacing', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-button-icon-before' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-button-icon-after' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_inquiry_icon',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_gallery_icon',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'button_size',
            [
                'label' => esc_html__('Button Size', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'btn-sm',
                'options' => [
                    'btn-xs' => esc_html__('Extra Small', 'estatesite-houzez'),
                    'btn-sm' => esc_html__('Small', 'estatesite-houzez'),
                    'btn-md' => esc_html__('Medium', 'estatesite-houzez'),
                    'btn-lg' => esc_html__('Large', 'estatesite-houzez'),
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_inquiry_button',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_gallery_button',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'button_icon_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-inquiry-button .estatesite-button-icon-before i' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-inquiry-button .estatesite-button-icon-after i' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-inquiry-button .estatesite-button-icon-before svg' => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-inquiry-button .estatesite-button-icon-after svg' => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-gallery-button .estatesite-button-icon-before i' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-gallery-button .estatesite-button-icon-after i' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-gallery-button .estatesite-button-icon-before svg' => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-gallery-button .estatesite-button-icon-after svg' => 'fill: {{VALUE}} !important;',
                    // For Elementor icon widget compatibility
                    '{{WRAPPER}} .estatesite-inquiry-button .elementor-icon' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .estatesite-gallery-button .elementor-icon' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_inquiry_icon',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_gallery_icon',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );
        
        $this->end_controls_section();

        // Responsive Card Style section
        $this->start_controls_section(
            'responsive_card_style_section',
            [
                'label' => esc_html__('Responsive Card Style', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'card_title_prefix',
            [
                'label' => esc_html__('Responsive Title Prefix', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => esc_html__('e.g. "Apartment"', 'estatesite-houzez'),
                'description' => esc_html__('Text to display before the apartment title in card view', 'estatesite-houzez'),
            ]
        );
        
        $this->add_control(
            'card_actions_bg_color',
            [
                'label' => esc_html__('Actions Area Background', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f9f9f9',
                'selectors' => [
                    '{{WRAPPER}} .property-unit-card-actions' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'card_inquiry_button_bg_color',
            [
                'label' => esc_html__('Card Inquiry Button Background', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#28a745',
                'selectors' => [
                    '{{WRAPPER}} .property-unit-card-actions .estatesite-inquiry-button' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );
        
        $this->add_control(
            'card_inquiry_button_text_color',
            [
                'label' => esc_html__('Card Inquiry Button Text', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .property-unit-card-actions .estatesite-inquiry-button' => 'color: {{VALUE}} !important;',
                ],
            ]
        );
        
        $this->add_control(
            'card_gallery_button_bg_color',
            [
                'label' => esc_html__('Card Gallery Button Background', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-unit-card-actions .estatesite-gallery-button' => 'background-color: {{VALUE}} !important;',
                ],
                'condition' => [
                    'show_gallery_button' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'card_gallery_button_text_color',
            [
                'label' => esc_html__('Card Gallery Button Text', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .property-unit-card-actions .estatesite-gallery-button' => 'color: {{VALUE}} !important;',
                ],
                'condition' => [
                    'show_gallery_button' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'card_buttons_gap',
            [
                'label' => esc_html__('Buttons Gap', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-unit-card-actions .estatesite-inquiry-button' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'card_actions_padding',
            [
                'label' => esc_html__('Actions Area Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-unit-card-actions' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }

    /**
     * Format price with currency
     */
    protected function format_price($price, $price_postfix = '') {
        // Format price using Houzez function if available, otherwise use basic formatting
        if (function_exists('houzez_get_property_price')) {
            return houzez_get_property_price($price, $price_postfix);
        } else {
            return '$' . number_format($price) . ($price_postfix ? ' ' . $price_postfix : '');
        }
    }

    /**
     * Enqueue required scripts and styles
     */
    protected function enqueue_scripts_styles() {
        // Make sure we're handling the popup functionality correctly
        wp_enqueue_script('elementor-frontend');
        
        if (defined('ELEMENTOR_PRO_VERSION')) {
            wp_enqueue_script('elementor-pro-frontend');
        }
        
        // Register our script with all necessary dependencies
        wp_register_script(
            'estatesite-apartment-popup', 
            false, 
            ['jquery', 'elementor-frontend'], 
            '1.0.0', 
            true
        );
        
        // Create improved script for handling the elementor popup triggers
        $script = "(function($) {\n";
        
        // Wait for Elementor Pro to be fully loaded if it exists
        $script .= "  var initElementorPopups = function() {\n";
        $script .= "    function openElementorPopup(popupId, unitData) {\n";
        $script .= "      // Different versions of Elementor use different APIs\n";
        $script .= "      if (typeof elementorProFrontend !== 'undefined' && elementorProFrontend.modules && elementorProFrontend.modules.popup) {\n";
        $script .= "        elementorProFrontend.modules.popup.showPopup({id: popupId});\n";
        $script .= "      } else if (typeof elementorFrontend !== 'undefined' && elementorFrontend.utils && elementorFrontend.utils.popup) {\n";
        $script .= "        elementorFrontend.utils.popup.showPopup({id: popupId});\n";
        $script .= "      } else {\n";
        $script .= "        console.error('Elementor popup utilities not available');\n";
        $script .= "        alert('Unable to open the popup. Please refresh the page and try again.');\n";
        $script .= "        return false;\n";
        $script .= "      }\n";
        
        // Set form data with retry to ensure popup is rendered
        $script .= "      var maxAttempts = 5;\n";
        $script .= "      var attempt = 0;\n";
        $script .= "      var formFillInterval = setInterval(function() {\n";
        $script .= "        var popup = $('#elementor-popup-modal-' + popupId);\n";
        $script .= "        if (popup.length > 0) {\n";
        $script .= "          // Populate standard fields\n";
        $script .= "          popup.find('[name=\"property_title\"]').val(unitData.title);\n";
        $script .= "          popup.find('[name=\"estate_type\"]').val(unitData.type);\n";
        $script .= "          popup.find('[name=\"apartment_no\"]').val(unitData.id);\n";
        $script .= "          popup.find('[name=\"price\"]').val(unitData.price);\n";
        $script .= "          \n";
        $script .= "          // Populate additional fields based on API data\n";
        $script .= "          popup.find('[name=\"size\"]').val(unitData.size);\n";
        $script .= "          popup.find('[name=\"size_postfix\"]').val(unitData.sizePostfix);\n";
        $script .= "          popup.find('[name=\"building\"]').val(unitData.building);\n";
        $script .= "          popup.find('[name=\"floor\"]').val(unitData.floor);\n";
        $script .= "          popup.find('[name=\"entrance\"]').val(unitData.entrance);\n";
        $script .= "          popup.find('[name=\"apno\"]').val(unitData.apno);\n";
        $script .= "          popup.find('[name=\"description\"]').val(unitData.description);\n";
        $script .= "          popup.find('[name=\"surface\"]').val(unitData.surface);\n";
        $script .= "          popup.find('[name=\"status\"]').val(unitData.status);\n";
        $script .= "          \n";
        $script .= "          // Try to populate any custom fields with matching names\n";
        $script .= "          for (var key in unitData) {\n";
        $script .= "            popup.find('[name=\"' + key + '\"]').val(unitData[key]);\n";
        $script .= "          }\n";
        
        // Add templating support for text content
        $script .= "          // Format the price for display with proper delimiters and currency\n";
        $script .= "          function formatPrice(price) {\n";
        $script .= "            if (!price) return '';\n";
        $script .= "            // Convert to number if it's a string\n";
        $script .= "            price = parseFloat(price.toString().replace(/[^0-9.]/g, ''));\n";
        $script .= "            // Format with thousand separators and currency symbol\n";
        $script .= "            return price.toLocaleString('de-DE', { style: 'currency', currency: 'EUR' });\n";
        $script .= "          }\n";
        $script .= "          \n";
        $script .= "          // Create a formatted version of the data for templating\n";
        $script .= "          var formattedData = {};\n";
        $script .= "          for (var key in unitData) {\n";
        $script .= "            formattedData[key] = unitData[key];\n";
        $script .= "          }\n";
        $script .= "          // Use pre-formatted price if available, otherwise format it\n";
        $script .= "          if (unitData.priceFormatted) {\n";
        $script .= "            formattedData.price = unitData.priceFormatted;\n";
        $script .= "          } else if (unitData.price) {\n";
        $script .= "            formattedData.price = formatPrice(unitData.price);\n";
        $script .= "          }\n";
        $script .= "          \n";
        $script .= "          // Process all text elements for template variables {{variable}}\n";
        $script .= "          popup.find('.elementor-widget-container, .elementor-text-editor, .elementor-heading-title').each(function() {\n";
        $script .= "            var element = jQuery(this);\n";
        $script .= "            var originalContent = element.attr('data-original-content');\n";
        $script .= "            \n";
        $script .= "            // Store original content if not already stored\n";
        $script .= "            if (!originalContent) {\n";
        $script .= "              originalContent = element.html();\n";
        $script .= "              element.attr('data-original-content', originalContent);\n";
        $script .= "            } else {\n";
        $script .= "              // Reset to original content before replacing variables again\n";
        $script .= "              element.html(originalContent);\n";
        $script .= "            }\n";
        $script .= "            \n";
        $script .= "            var content = element.html();\n";
        $script .= "            var hasReplacement = false;\n";
        $script .= "            \n";
        $script .= "            // Replace all template variables {{var}} with actual data\n";
        $script .= "            for (var key in formattedData) {\n";
        $script .= "              var template = '{{' + key + '}}';\n";
        $script .= "              if (content.indexOf(template) !== -1) {\n";
        $script .= "                content = content.split(template).join(formattedData[key] || '');\n";
        $script .= "                hasReplacement = true;\n";
        $script .= "              }\n";
        $script .= "            }\n";
        $script .= "            \n";
        $script .= "            // Update content only if replacements were made\n";
        $script .= "            if (hasReplacement) {\n";
        $script .= "              element.html(content);\n";
        $script .= "            }\n";
        $script .= "          });\n";
        
        $script .= "          clearInterval(formFillInterval);\n";
        $script .= "          console.log('Popup form populated successfully');\n";
        $script .= "        } else if (attempt >= maxAttempts) {\n";
        $script .= "          clearInterval(formFillInterval);\n";
        $script .= "          console.warn('Could not find popup form to populate');\n";
        $script .= "        }\n";
        $script .= "        attempt++;\n";
        $script .= "      }, 300);\n";
        
        $script .= "      return true;\n";
        $script .= "    }\n";
        
        // Handle click events
        $script .= "    function initPopupTriggers() {\n";
        $script .= "      $('.elementor-popup-trigger').off('click').on('click', function(e) {\n";
        $script .= "        e.preventDefault();\n";
        $script .= "        var popupId = $(this).data('popup-id');\n";
        $script .= "        if (!popupId) {\n";
        $script .= "          console.error('Popup ID not set');\n";
        $script .= "          return;\n";
        $script .= "        }\n";
        
        $script .= "          // Get popup ID and unit data from the button\n";
        $script .= "          var popupId = $(this).data('popup-id');\n";
        $script .= "          var unitData = {\n";
        $script .= "            title: $(this).data('unit-title'),\n";
        $script .= "            type: $(this).data('unit-type'),\n";
        $script .= "            price: $(this).data('unit-price'),\n";
        $script .= "            priceFormatted: $(this).data('unit-price-formatted'),\n";
        $script .= "            id: $(this).data('unit-id'),\n";
        $script .= "            size: $(this).data('unit-size'),\n";
        $script .= "            sizePostfix: $(this).data('unit-size-postfix'),\n";
        $script .= "            building: $(this).data('unit-building'),\n";
        $script .= "            floor: $(this).data('unit-floor'),\n";
        $script .= "            entrance: $(this).data('unit-entrance'),\n";
        $script .= "            apno: $(this).data('unit-apno'),\n";
        $script .= "            description: $(this).data('unit-description'),\n";
        $script .= "            status: $(this).data('unit-status'),\n";
        $script .= "            surface: $(this).data('unit-surface')\n";
        $script .= "          };\n";
        
        $script .= "        openElementorPopup(popupId, unitData);\n";
        $script .= "      });\n";
        $script .= "    }\n";
        
        // Initialize when document is ready
        $script .= "    $(document).ready(function() {\n";
        $script .= "      initPopupTriggers();\n";
        $script .= "    });\n";
        
        // Also initialize after Elementor frontend init
        $script .= "    $(document).on('elementor/frontend/init', function() {\n";
        $script .= "      // Wait a moment for Elementor Pro to initialize\n";
        $script .= "      setTimeout(function() {\n";
        $script .= "        initPopupTriggers();\n";
        $script .= "      }, 200);\n";
        $script .= "      \n";
        $script .= "      // Also initialize when widgets are ready\n";
        $script .= "      if (elementorFrontend && elementorFrontend.hooks) {\n";
        $script .= "        elementorFrontend.hooks.addAction('frontend/element_ready/global', function() {\n";
        $script .= "          initPopupTriggers();\n";
        $script .= "        });\n";
        $script .= "      }\n";
        $script .= "    });\n";
        $script .= "  };\n";
        
        // Run initialization function
        $script .= "  if (document.readyState === 'loading') {\n";
        $script .= "    document.addEventListener('DOMContentLoaded', initElementorPopups);\n";
        $script .= "  } else {\n";
        $script .= "    initElementorPopups();\n";
        $script .= "  }\n";
        $script .= "})(jQuery);\n";
        
        // Add the inline script to our registered script and enqueue it
        wp_add_inline_script('estatesite-apartment-popup', $script);
        wp_enqueue_script('estatesite-apartment-popup');
    }

    /**
     * Render the widget output on the frontend.
     */
    protected function render() {

        $this->single_property_preview_query(); // Only for preview

        $settings = $this->get_settings_for_display();

        // Resolve property ID:
        //   1. Explicit `property_id` in widget settings always wins.
        //   2. Otherwise use current loop post — which is the user-picked
        //      preview target in the editor (set by the trait above) or the
        //      real property post on the live frontend.
        $property_id = ! empty( $settings['property_id'] )
            ? intval( $settings['property_id'] )
            : get_the_ID();
        
        // Get enhanced sub-units data
        $sub_units = get_post_meta($property_id, 'eas_multi_units', true);
        
        // Check if the data might be serialized but not automatically unserialized
        if (is_string($sub_units) && !empty($sub_units)) {
            $maybe_unserialized = maybe_unserialize($sub_units);
            if (is_array($maybe_unserialized)) {
                $sub_units = $maybe_unserialized;
            }
        }
        
        if (empty($sub_units) || !is_array($sub_units)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode() || current_user_can('administrator')) {
                // Show debugging info for admins or in edit mode
                echo '<br><small>(Property ID: ' . $property_id . ')</small>';
            }
            $this->reset_preview_query(); // Only for preview
            return;
        }
        
        // Enqueue necessary scripts and styles
        $this->enqueue_scripts_styles();
        
        // Add JavaScript for gallery functionality
        if ($settings['show_gallery_button'] === 'yes') {
            // Include lightbox script (if not already loaded by theme)
            wp_enqueue_script('jquery');
            
            if (!wp_script_is('fancybox')) {
                wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array('jquery'), '5.0', true);
                wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), '5.0');
            }
            
            // Add inline JavaScript for apartment gallery
            ?>
            <script>
            function openApartmentGallery(e, element) {
                e.preventDefault();
                
                const unitId = element.getAttribute('data-unit-id');
                const propertyId = element.getAttribute('data-property-id');
                const galleryItems = [];
                
                // Get photos from the unit data
                const unitData = window.estateSiteApartmentData?.[propertyId]?.[unitId];
                
                if (unitData && unitData.photos && unitData.photos.length) {
                    unitData.photos.forEach(photo => {
                        galleryItems.push({
                            src: photo.url,
                            caption: unitData.title || ''
                        });
                    });
                    
                    Fancybox.show(galleryItems, {
                        infinite: true
                    });
                } else {
                    console.error('No photos found for this apartment');
                }
            }
            </script>
            <?php
            
            // Prepare apartment data for JavaScript
            echo "<script>\n";
            echo "window.estateSiteApartmentData = window.estateSiteApartmentData || {};\n";
            echo "window.estateSiteApartmentData[{$property_id}] = {};\n";
            
            foreach ($sub_units as $unit_id => $unit) {
                $photos = array();
                if (!empty($unit['eas_mu_photos'])) {
                    foreach ($unit['eas_mu_photos'] as $photo) {
                        if (!empty($photo['attachment_id'])) {
                            $image_url = wp_get_attachment_image_url($photo['attachment_id'], 'full');
                            if ($image_url) {
                                $photos[] = array('url' => $image_url);
                            }
                        }
                    }
                }
                
                $unit_title = !empty($unit['eas_mu_title']) ? $unit['eas_mu_title'] : __('Unit', 'estatesite-houzez') . ' ' . ($unit_id + 1);
                
                echo "window.estateSiteApartmentData[{$property_id}][{$unit_id}] = {";
                echo "title: " . json_encode($unit_title) . ",";
                echo "photos: " . json_encode($photos);
                echo "};\n";
            }
            echo "</script>\n";
        }
        
        // Add inline styles for the table
        ?>
        <style>
            /* Traditional table layout */
            .property-sub-units-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                display: none; /* Hidden by default, shown on larger screens */
            }
            .property-sub-units-table th {
                background-color: #f7f7f7;
                text-align: left;
                padding: 10px;
                font-weight: 700;
                border-bottom: 1px solid #ddd;
            }
            .property-sub-units-table td {
                padding: 10px;
                border-bottom: 1px solid #ddd;
                vertical-align: middle;
            }
            .property-sub-units-table tr:hover {
                background-color: #f5f5f5;
            }
            .property-sub-units-table .unit-price {
                font-weight: bold;
            }
            
            /* Responsive card-based layout */
            .property-sub-units-cards {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                margin-bottom: 20px;
            }
            .property-unit-card {
                background-color: #f8f8f8;
                border-radius: 6px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                overflow: hidden;
                width: 100%;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .property-unit-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            .property-unit-card-header {
                padding: 15px;
                background-color: #fff;
                border-bottom: 1px solid #eee;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .property-unit-card-body {
                padding: 15px;
            }
            .property-unit-card-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
                padding-bottom: 8px;
                border-bottom: 1px solid #eee;
            }
            .property-unit-card-row:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            .property-unit-card-label {
                color: #555;
                font-weight: 500;
            }
            .property-unit-card-value {
                text-align: right;
                font-weight: 500;
            }
            .property-unit-card-price {
                /* color: #00aeff; */
                font-size: 1.25rem;
                font-weight: bold;
            }
            .property-unit-card-status {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 4px;
                text-transform: uppercase;
                font-size: 0.8rem;
                font-weight: bold;
            }
            .property-unit-card-status.available {
                background-color: #a3f0c8;
                color: #16633f;
            }
            .property-unit-card-status.sold {
                background-color: #ffd2d2;
                color: #9e3a3a;
            }
            .property-unit-card-status.reserved {
                background-color: #ffe8b3;
                color: #8a5a00;
            }
            /* Status text colors */
            .text-available {
                color: #16633f;
                font-weight: bold;
            }
            .text-sold {
                color: #9e3a3a;
                font-weight: bold;
            }
            .text-reserved {
                color: #8a5a00;
                font-weight: bold;
            }
            .property-unit-card-actions {
                padding: 15px;
                background-color: #fff;
                border-top: 1px solid #eee;
                display: flex;
                justify-content: space-around;
                gap: 10px;
            }
            
            /* Buttons */            
            .estatesite-inquiry-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid;
                border-radius: 4px;
                padding: 8px 16px;
                font-size: 14px;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.3s ease;
                flex: 1;
            }
            .estatesite-inquiry-button:hover {
                text-decoration: none;
            }
            .estatesite-gallery-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid;
                border-radius: 4px;
                padding: 8px 16px;
                font-size: 14px;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.3s ease;
                flex: 1;
            }
            .estatesite-gallery-button:hover {
                text-decoration: none;
            }
            .action-bnts-cont {
                display: flex;
                gap: 10px;
            }
            
            /* Responsive design */
            @media (min-width: 768px) {
                .property-sub-units-cards {
                    display: flex;
                }
                .property-unit-card {
                    width: calc(50% - 10px);
                }
                .property-sub-units-table {
                    display: none;
                }
            }
            @media (min-width: 1200px) {
                .property-sub-units-cards {
                    display: none;
                }
                .property-sub-units-table {
                    display: table;
                }
            }
            /* Display option - can be toggled by controls */
            .display-mode-cards .property-sub-units-cards {
                display: flex !important;
            }
            .display-mode-cards .property-sub-units-table {
                display: none !important;
            }
            .display-mode-table .property-sub-units-cards {
                display: none !important;
            }
            .display-mode-table .property-sub-units-table {
                display: table !important;
            }
        </style>
        <?php
        
        // Render container
        echo '<div class="estatesite-property-sub-units">';
        
        // Start table view
        echo '<table class="property-sub-units-table">';
        
        // Table headers
        if ($settings['table_headers'] === 'yes') {
            echo '<thead><tr>';
            
            if ($settings['show_title_column'] === 'yes') {
                echo '<th>' . esc_html($settings['title_header_text']) . '</th>';
            }
            
            if ($settings['show_price_column'] === 'yes') {
                echo '<th>' . esc_html($settings['price_header_text']) . '</th>';
            }

            if ($settings['show_price_postfix_column'] === 'yes') {
                echo '<th>' . esc_html($settings['price_postfix_header_text']) . '</th>';
            }

            // Beds and baths columns removed as they're not in the API data

            // Apartment Number column
            if ($settings['show_apno_column'] === 'yes') {
                echo '<th>' . esc_html($settings['apno_header_text']) . '</th>';
            }
            
            if ($settings['show_size_column'] === 'yes') {
                echo '<th>' . esc_html($settings['size_header_text']) . '</th>';
            }
            
            if ($settings['show_type_column'] === 'yes') {
                echo '<th>' . esc_html($settings['type_header_text']) . '</th>';
            }
            
            if ($settings['show_availability_column'] === 'yes') {
                echo '<th>' . esc_html($settings['availability_header_text']) . '</th>';
            }
            
            // Enhanced fields headers
            if ($settings['show_building_column'] === 'yes') {
                echo '<th>' . esc_html($settings['building_column_text']) . '</th>';
            }
            
            if ($settings['show_entrance_column'] === 'yes') {
                echo '<th>' . esc_html($settings['entrance_column_text']) . '</th>';
            }
            
            if ($settings['show_floor_column'] === 'yes') {
                echo '<th>' . esc_html($settings['floor_column_text']) . '</th>';
            }
            
            if ($settings['show_direction_column'] === 'yes') {
                echo '<th>' . esc_html($settings['direction_column_text']) . '</th>';
            }
            
            if ($settings['show_description_column'] === 'yes') {
                echo '<th>' . esc_html($settings['description_column_text']) . '</th>';
            }
            
            if ($settings['show_inquiry_button'] === 'yes') {
                echo '<th>' . esc_html($settings['action_header_text']) . '</th>';
            }
            
            echo '</tr></thead>';
        }
        
        // Table body
        echo '<tbody>';
        
        // Loop through sub-units
        foreach ($sub_units as $unit_id => $unit) {
            echo '<tr>';
            
            // Title column
            if ($settings['show_title_column'] === 'yes') {
                // Use enhanced apartment data (eas_mu_ prefix)
                echo '<td>' . esc_html(!empty($unit['eas_mu_title']) ? $unit['eas_mu_title'] : 
                    (__('Unit', 'estatesite-houzez') . ' ' . ($unit_id + 1))) . '</td>';
            }
            
            // Price column
            if ($settings['show_price_column'] === 'yes') {
                $price = !empty($unit['eas_mu_price']) ? $unit['eas_mu_price'] : '';
                $price_postfix = !empty($unit['eas_mu_price_postfix']) ? $unit['eas_mu_price_postfix'] : '';
                echo '<td>' . $this->format_price($price, $price_postfix) . '</td>';
            }

            // Price Postfix column
            if ($settings['show_price_postfix_column'] === 'yes') {
                $price_postfix = !empty($unit['eas_mu_price_postfix']) ? $unit['eas_mu_price_postfix'] : '-';
                echo '<td>' . esc_html($price_postfix) . '</td>';
            }

            // Beds and baths columns removed as they're not in the API data

            // Apartment Number column
            if ($settings['show_apno_column'] === 'yes') {
                $apno = !empty($unit['eas_mu_apno']) ? $unit['eas_mu_apno'] : '-';
                echo '<td>' . esc_html($apno) . '</td>';
            }
            
            // Size column
            if ($settings['show_size_column'] === 'yes') {
                $size = !empty($unit['eas_mu_size']) ? $unit['eas_mu_size'] : '-';
                
                // Use custom suffix from settings if specified, otherwise use the value from data
                $size_postfix = !empty($settings['size_suffix']) ? $settings['size_suffix'] : 
                               (!empty($unit['eas_mu_size_postfix']) ? $unit['eas_mu_size_postfix'] : '');
                
                echo '<td>' . esc_html($size) . ($size_postfix ? ' ' . esc_html($size_postfix) : '') . '</td>';
            }
            
            // Type column
            if ($settings['show_type_column'] === 'yes') {
                $type = !empty($unit['eas_mu_type']) ? $unit['eas_mu_type'] : '-';
                echo '<td>' . esc_html($type) . '</td>';
            }
            
            // Availability column
            if ($settings['show_availability_column'] === 'yes') {
                $availability = !empty($unit['eas_mu_availability_date']) ? $unit['eas_mu_availability_date'] : 
                    (!empty($unit['eas_mu_status']) ? $unit['eas_mu_status'] : '-');
                echo '<td>' . esc_html($availability) . '</td>';
            }
            
            // Enhanced fields columns
            // Building column
            if ($settings['show_building_column'] === 'yes') {
                $building = !empty($unit['eas_mu_building_no']) ? $unit['eas_mu_building_no'] : '-';
                echo '<td>' . esc_html($building) . '</td>';
            }
            
            // Entrance column
            if ($settings['show_entrance_column'] === 'yes') {
                $entrance = !empty($unit['eas_mu_entrance']) ? $unit['eas_mu_entrance'] : '-';
                echo '<td>' . esc_html($entrance) . '</td>';
            }
            
            // Floor column
            if ($settings['show_floor_column'] === 'yes') {
                $floor = !empty($unit['eas_mu_floor']) ? $unit['eas_mu_floor'] : '-';
                echo '<td>' . esc_html($floor) . '</td>';
            }
            
            // Direction/Aspect column
            if ($settings['show_direction_column'] === 'yes') {
                $aspects = [];
                
                // Get custom direction labels from settings
                $direction_labels = [
                    'N' => !empty($settings['direction_n_label']) ? $settings['direction_n_label'] : 'N',
                    'NE' => !empty($settings['direction_ne_label']) ? $settings['direction_ne_label'] : 'NE',
                    'E' => !empty($settings['direction_e_label']) ? $settings['direction_e_label'] : 'E',
                    'SE' => !empty($settings['direction_se_label']) ? $settings['direction_se_label'] : 'SE',
                    'S' => !empty($settings['direction_s_label']) ? $settings['direction_s_label'] : 'S',
                    'SW' => !empty($settings['direction_sw_label']) ? $settings['direction_sw_label'] : 'SW',
                    'W' => !empty($settings['direction_w_label']) ? $settings['direction_w_label'] : 'W',
                    'NW' => !empty($settings['direction_nw_label']) ? $settings['direction_nw_label'] : 'NW',
                ];
                
                // Check all possible directions
                if (!empty($unit['eas_mu_aspect_n']) && $unit['eas_mu_aspect_n']) $aspects[] = $direction_labels['N'];
                if (!empty($unit['eas_mu_aspect_ne']) && $unit['eas_mu_aspect_ne']) $aspects[] = $direction_labels['NE'];
                if (!empty($unit['eas_mu_aspect_e']) && $unit['eas_mu_aspect_e']) $aspects[] = $direction_labels['E'];
                if (!empty($unit['eas_mu_aspect_se']) && $unit['eas_mu_aspect_se']) $aspects[] = $direction_labels['SE'];
                if (!empty($unit['eas_mu_aspect_s']) && $unit['eas_mu_aspect_s']) $aspects[] = $direction_labels['S'];
                if (!empty($unit['eas_mu_aspect_sw']) && $unit['eas_mu_aspect_sw']) $aspects[] = $direction_labels['SW'];
                if (!empty($unit['eas_mu_aspect_w']) && $unit['eas_mu_aspect_w']) $aspects[] = $direction_labels['W'];
                if (!empty($unit['eas_mu_aspect_nw']) && $unit['eas_mu_aspect_nw']) $aspects[] = $direction_labels['NW'];
                
                $direction = !empty($aspects) ? implode(', ', $aspects) : '-';
                echo '<td>' . esc_html($direction) . '</td>';
            }
            
            // Description column
            if ($settings['show_description_column'] === 'yes') {
                $description = !empty($unit['eas_mu_description']) ? $unit['eas_mu_description'] : '-';
                echo '<td>' . esc_html($description) . '</td>';
            }
            
            // Inquiry button column
            if ($settings['show_inquiry_button'] === 'yes') {
                echo '<td class="action-bnts-cont">';
                
                // Button with icon
                $button_text = !empty($settings['button_text']) ? $settings['button_text'] : __('Inquire', 'estatesite-houzez');
                
                // Generate button based on popup type
                $popup_type = !empty($settings['popup_type']) ? $settings['popup_type'] : 'elementor';
                $unit_title = !empty($unit['eas_mu_title']) ? $unit['eas_mu_title'] : __('Unit', 'estatesite-houzez') . ' ' . ($unit_id + 1);
                
                $button_classes = 'btn ' . (!empty($settings['button_size']) ? $settings['button_size'] : 'btn-sm') . ' estatesite-inquiry-button';
                $button_attrs = '';
                $button_url = '#';
                
                switch ($popup_type) {
                    case 'elementor':
                        $popup_id = !empty($settings['elementor_popup_id']) ? $settings['elementor_popup_id'] : '';
                        $button_classes .= ' elementor-popup-trigger';
                        
                        // Build data attributes for the button
                        $button_attrs .= ' data-popup-id="' . esc_attr($popup_id) . '"';
                        $button_attrs .= ' data-unit-title="' . esc_attr($unit_title) . '"';
                        $button_attrs .= ' data-unit-type="' . esc_attr(!empty($unit['eas_mu_type']) ? $unit['eas_mu_type'] : '') . '"';
                        
                        // Price formatting
                        $price = !empty($unit['eas_mu_price']) ? $unit['eas_mu_price'] : '';
                        $price_formatted = !empty($unit['eas_mu_price']) ? $this->format_price($unit['eas_mu_price'], '') : '';
                        $button_attrs .= ' data-unit-price="' . esc_attr($price) . '"';
                        $button_attrs .= ' data-unit-price-formatted="' . esc_attr($price_formatted) . '"';
                        $button_attrs .= ' data-unit-id="' . esc_attr($unit_id) . '"';
                    
                        // Add additional apartment data for the popup based on API fields
                        $button_attrs .= ' data-unit-size="' . esc_attr(!empty($unit['eas_mu_size']) ? $unit['eas_mu_size'] : '') . '"';
                        $button_attrs .= ' data-unit-size-postfix="' . esc_attr(!empty($unit['eas_mu_size_postfix']) ? $unit['eas_mu_size_postfix'] : '') . '"';
                        $button_attrs .= ' data-unit-building="' . esc_attr(!empty($unit['eas_mu_building_no']) ? $unit['eas_mu_building_no'] : '') . '"';
                        $button_attrs .= ' data-unit-floor="' . esc_attr(!empty($unit['eas_mu_floor']) ? $unit['eas_mu_floor'] : '') . '"';
                        $button_attrs .= ' data-unit-entrance="' . esc_attr(!empty($unit['eas_mu_entrance']) ? $unit['eas_mu_entrance'] : '') . '"';
                        $button_attrs .= ' data-unit-apno="' . esc_attr(!empty($unit['eas_mu_apno']) ? $unit['eas_mu_apno'] : '') . '"';
                        $button_attrs .= ' data-unit-description="' . esc_attr(!empty($unit['eas_mu_description']) ? $unit['eas_mu_description'] : '') . '"';
                        $button_attrs .= ' data-unit-status="' . esc_attr(!empty($unit['eas_mu_status']) ? $unit['eas_mu_status'] : '') . '"';
                        $button_attrs .= ' data-unit-surface="' . esc_attr(!empty($unit['eas_mu_surface_all']) ? $unit['eas_mu_surface_all'] : '') . '"';
                        break;
                    
                    case 'custom':
                        $button_url = !empty($settings['custom_action']) ? $settings['custom_action'] : '#';
                        break;
                }
                
                // Prepare gallery button data if enabled
                $gallery_button_html = '';
                if ($settings['show_gallery_button'] === 'yes' && !empty($unit['eas_mu_photos'])) {
                    $gallery_button_text = !empty($settings['gallery_button_text']) ? $settings['gallery_button_text'] : esc_html__('Gallery', 'estatesite-houzez');
                    $gallery_button_classes = 'btn btn-sm estatesite-gallery-button ' . esc_attr($settings['button_size']);
                    
                    // Gallery button will launch a lightbox with apartment photos
                    $gallery_button_attrs = ' data-unit-id="' . esc_attr($unit_id) . '"';
                    $gallery_button_attrs .= ' data-property-id="' . esc_attr($property_id) . '"';
                    
                    $gallery_button_html = '<a href="#" class="' . esc_attr($gallery_button_classes) . '"' . $gallery_button_attrs . ' onclick="openApartmentGallery(event, this)">';
                    
                    if ($settings['show_gallery_icon'] === 'yes' && $settings['icon_position'] === 'before') {
                        $gallery_button_html .= '<span class="estatesite-button-icon-before">';
                        // Add custom style attribute for the icon
                        $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                        $gallery_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                        ob_start();
                        Icons_Manager::render_icon($settings['gallery_icon'], ['aria-hidden' => 'true']);
                        $gallery_button_html .= ob_get_clean();
                        $gallery_button_html .= '</span>';
                        $gallery_button_html .= '</span>';
                    }
                    
                    if ($settings['hide_button_text'] !== 'yes') {
                        $gallery_button_html .= esc_html($gallery_button_text);
                    }
                    
                    if ($settings['show_gallery_icon'] === 'yes' && $settings['icon_position'] === 'after') {
                        $gallery_button_html .= '<span class="estatesite-button-icon-after">';
                        // Add custom style attribute for the icon
                        $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                        $gallery_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                        ob_start();
                        Icons_Manager::render_icon($settings['gallery_icon'], ['aria-hidden' => 'true']);
                        $gallery_button_html .= ob_get_clean();
                        $gallery_button_html .= '</span>';
                        $gallery_button_html .= '</span>';
                    }
                    
                    $gallery_button_html .= '</a>';
                }
                
                // Generate inquiry button HTML with icon
                $inquiry_button_html = '<a href="' . esc_url($button_url) . '" class="' . esc_attr($button_classes) . '"' . $button_attrs . '>';
                
                if ($settings['show_inquiry_icon'] === 'yes' && $settings['icon_position'] === 'before') {
                    $inquiry_button_html .= '<span class="estatesite-button-icon-before">';
                    // Add custom style attribute for the icon
                    $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                    $inquiry_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                    ob_start();
                    Icons_Manager::render_icon($settings['inquiry_icon'], ['aria-hidden' => 'true']);
                    $inquiry_button_html .= ob_get_clean();
                    $inquiry_button_html .= '</span>';
                    $inquiry_button_html .= '</span>';
                }
                
                if ($settings['hide_button_text'] !== 'yes') {
                    $inquiry_button_html .= esc_html($button_text);
                }
                
                if ($settings['show_inquiry_icon'] === 'yes' && $settings['icon_position'] === 'after') {
                    $inquiry_button_html .= '<span class="estatesite-button-icon-after">';
                    // Add custom style attribute for the icon
                    $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                    $inquiry_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                    ob_start();
                    Icons_Manager::render_icon($settings['inquiry_icon'], ['aria-hidden' => 'true']);
                    $inquiry_button_html .= ob_get_clean();
                    $inquiry_button_html .= '</span>';
                    $inquiry_button_html .= '</span>';
                }
                
                $inquiry_button_html .= '</a>';
                
                // Display buttons in the selected order
                if ($settings['button_order'] === 'gallery_first' && !empty($gallery_button_html)) {
                    echo $gallery_button_html . ' ' . $inquiry_button_html;
                } else {
                    echo $inquiry_button_html;
                    if (!empty($gallery_button_html)) {
                        echo ' ' . $gallery_button_html;
                    }
                }
                
                // No Bootstrap modal HTML needed anymore - using only Elementor popups or custom actions
                
                echo '</td>';
            }
            
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        
        // Card-based layout for responsive design
        echo '<div class="property-sub-units-cards">';
        
        // Loop through sub-units for card view
        foreach ($sub_units as $unit_id => $unit) {
            // Get common data once
            $unit_title = !empty($unit['eas_mu_title']) ? $unit['eas_mu_title'] : __('Unit', 'estatesite-houzez') . ' ' . ($unit_id + 1);
            $price = !empty($unit['eas_mu_price']) ? $unit['eas_mu_price'] : '';
            $price_postfix = !empty($unit['eas_mu_price_postfix']) ? $unit['eas_mu_price_postfix'] : '';
            $price_formatted = $this->format_price($price, $price_postfix);
            $size = !empty($unit['eas_mu_size']) ? $unit['eas_mu_size'] : '-';
            $size_postfix = !empty($settings['size_suffix']) ? $settings['size_suffix'] : (!empty($unit['eas_mu_size_postfix']) ? $unit['eas_mu_size_postfix'] : '');
            $type = !empty($unit['eas_mu_type']) ? $unit['eas_mu_type'] : '-';
            // Use the same logic for availability/status as in the table view
            $status = !empty($unit['eas_mu_availability_date']) ? $unit['eas_mu_availability_date'] : 
                (!empty($unit['eas_mu_status']) ? $unit['eas_mu_status'] : '-');
            $apno = !empty($unit['eas_mu_apno']) ? $unit['eas_mu_apno'] : '-';
            $building = !empty($unit['eas_mu_building_no']) ? $unit['eas_mu_building_no'] : '-';
            $entrance = !empty($unit['eas_mu_entrance']) ? $unit['eas_mu_entrance'] : '-';
            $floor = !empty($unit['eas_mu_floor']) ? $unit['eas_mu_floor'] : '-';
            $description = !empty($unit['eas_mu_description']) ? $unit['eas_mu_description'] : '-';
            
            // Start card
            echo '<div class="property-unit-card">';
            
            // Card header with title and status
            echo '<div class="property-unit-card-header">';
            
            // Display title with optional prefix
            $title_prefix = !empty($settings['card_title_prefix']) ? esc_html($settings['card_title_prefix']) . ' ' : '';
            echo '<h3>' . $title_prefix . esc_html($unit_title) . '</h3>';
            
            // Status badge with color coding
            $status_class = 'available';
            
            // Set color class based on status
            if (strtolower($status) == 'продаден' || strtolower($status) == 'sold') {
                $status_class = 'sold';
            } elseif (strtolower($status) == 'reserved') {
                $status_class = 'reserved';
            }
            
            echo '<span class="property-unit-card-status ' . esc_attr($status_class) . '">' . esc_html($status) . '</span>';
            echo '</div>'; // End header
            
            // Card body with property details
            echo '<div class="property-unit-card-body">';
            
            // Status row (always shown)
            if ($settings['show_availability_column'] === 'yes') {
                echo '<div class="property-unit-card-row">';
                echo '<div class="property-unit-card-label">' . esc_html($settings['availability_header_text']) . '</div>';
                echo '<div class="property-unit-card-value">';
                // Use same status class coloring from header but with text only
                $status_class = 'available';
                
                // Set color class based on status
                if (strtolower($status) == 'продаден' || strtolower($status) == 'sold') {
                    $status_class = 'sold';
                } elseif (strtolower($status) == 'reserved') {
                    $status_class = 'reserved';
                }
                
                echo '<span class="text-' . esc_attr($status_class) . '">' . esc_html($status) . '</span>';
                echo '</div>';
                echo '</div>';
            }
            
            // Type
            if ($settings['show_type_column'] === 'yes') {
                echo '<div class="property-unit-card-row">';
                echo '<div class="property-unit-card-label">' . esc_html($settings['type_header_text']) . '</div>';
                echo '<div class="property-unit-card-value">' . esc_html($type) . '</div>';
                echo '</div>';
            }
            
            // Floor
            if ($settings['show_floor_column'] === 'yes') {
                echo '<div class="property-unit-card-row">';
                echo '<div class="property-unit-card-label">' . esc_html($settings['floor_column_text']) . '</div>';
                echo '<div class="property-unit-card-value">' . esc_html($floor) . '</div>';
                echo '</div>';
            }
            
            // Size
            if ($settings['show_size_column'] === 'yes') {
                echo '<div class="property-unit-card-row">';
                echo '<div class="property-unit-card-label">' . esc_html($settings['size_header_text']) . '</div>';
                echo '<div class="property-unit-card-value">' . esc_html($size) . ($size_postfix ? ' ' . esc_html($size_postfix) : '') . '</div>';
                echo '</div>';
            }
            
            // Direction/Aspect
            if ($settings['show_direction_column'] === 'yes') {
                $aspects = [];
                
                // Get custom direction labels from settings
                $direction_labels = [
                    'N' => !empty($settings['direction_n_label']) ? $settings['direction_n_label'] : 'N',
                    'NE' => !empty($settings['direction_ne_label']) ? $settings['direction_ne_label'] : 'NE',
                    'E' => !empty($settings['direction_e_label']) ? $settings['direction_e_label'] : 'E',
                    'SE' => !empty($settings['direction_se_label']) ? $settings['direction_se_label'] : 'SE',
                    'S' => !empty($settings['direction_s_label']) ? $settings['direction_s_label'] : 'S',
                    'SW' => !empty($settings['direction_sw_label']) ? $settings['direction_sw_label'] : 'SW',
                    'W' => !empty($settings['direction_w_label']) ? $settings['direction_w_label'] : 'W',
                    'NW' => !empty($settings['direction_nw_label']) ? $settings['direction_nw_label'] : 'NW',
                ];
                
                // Check all possible directions
                if (!empty($unit['eas_mu_aspect_n']) && $unit['eas_mu_aspect_n']) $aspects[] = $direction_labels['N'];
                if (!empty($unit['eas_mu_aspect_ne']) && $unit['eas_mu_aspect_ne']) $aspects[] = $direction_labels['NE'];
                if (!empty($unit['eas_mu_aspect_e']) && $unit['eas_mu_aspect_e']) $aspects[] = $direction_labels['E'];
                if (!empty($unit['eas_mu_aspect_se']) && $unit['eas_mu_aspect_se']) $aspects[] = $direction_labels['SE'];
                if (!empty($unit['eas_mu_aspect_s']) && $unit['eas_mu_aspect_s']) $aspects[] = $direction_labels['S'];
                if (!empty($unit['eas_mu_aspect_sw']) && $unit['eas_mu_aspect_sw']) $aspects[] = $direction_labels['SW'];
                if (!empty($unit['eas_mu_aspect_w']) && $unit['eas_mu_aspect_w']) $aspects[] = $direction_labels['W'];
                if (!empty($unit['eas_mu_aspect_nw']) && $unit['eas_mu_aspect_nw']) $aspects[] = $direction_labels['NW'];
                
                $direction = !empty($aspects) ? implode(', ', $aspects) : '-';
                
                echo '<div class="property-unit-card-row">';
                echo '<div class="property-unit-card-label">' . esc_html($settings['direction_column_text']) . '</div>';
                echo '<div class="property-unit-card-value">' . esc_html($direction) . '</div>';
                echo '</div>';
            }
            
            // Price - displayed prominently
            if ($settings['show_price_column'] === 'yes') {
                echo '<div class="property-unit-card-row">';
                echo '<div class="property-unit-card-label">' . esc_html($settings['price_header_text']) . '</div>';
                echo '<div class="property-unit-card-value property-unit-card-price">' . $price_formatted . '</div>';
                echo '</div>';
            }

            // Price Postfix
            if ($settings['show_price_postfix_column'] === 'yes') {
                echo '<div class="property-unit-card-row">';
                echo '<div class="property-unit-card-label">' . esc_html($settings['price_postfix_header_text']) . '</div>';
                echo '<div class="property-unit-card-value">' . esc_html($price_postfix) . '</div>';
                echo '</div>';
            }

            echo '</div>'; // End card body
            
            // Card footer with action buttons
            if ($settings['show_inquiry_button'] === 'yes') {
                echo '<div class="property-unit-card-actions">';
                
                // Prepare button attributes (same as in table view)
                $unit_title = !empty($unit['eas_mu_title']) ? $unit['eas_mu_title'] : __('Unit', 'estatesite-houzez') . ' ' . ($unit_id + 1);
                
                $button_classes = 'btn ' . (!empty($settings['button_size']) ? $settings['button_size'] : 'btn-sm') . ' estatesite-inquiry-button';
                $button_attrs = '';
                $button_url = '#';
                
                switch ($popup_type) {
                    case 'elementor':
                        $popup_id = !empty($settings['elementor_popup_id']) ? $settings['elementor_popup_id'] : '';
                        $button_classes .= ' elementor-popup-trigger';
                        
                        // Build data attributes for the button
                        $button_attrs .= ' data-popup-id="' . esc_attr($popup_id) . '"';
                        $button_attrs .= ' data-unit-title="' . esc_attr($unit_title) . '"';
                        $button_attrs .= ' data-unit-type="' . esc_attr(!empty($unit['eas_mu_type']) ? $unit['eas_mu_type'] : '') . '"';
                        
                        // Price formatting
                        $price = !empty($unit['eas_mu_price']) ? $unit['eas_mu_price'] : '';
                        $price_formatted = !empty($unit['eas_mu_price']) ? $this->format_price($unit['eas_mu_price'], '') : '';
                        $button_attrs .= ' data-unit-price="' . esc_attr($price) . '"';
                        $button_attrs .= ' data-unit-price-formatted="' . esc_attr($price_formatted) . '"';
                        $button_attrs .= ' data-unit-id="' . esc_attr($unit_id) . '"';
                    
                        // Add additional apartment data for the popup based on API fields
                        $button_attrs .= ' data-unit-size="' . esc_attr(!empty($unit['eas_mu_size']) ? $unit['eas_mu_size'] : '') . '"';
                        $button_attrs .= ' data-unit-size-postfix="' . esc_attr(!empty($unit['eas_mu_size_postfix']) ? $unit['eas_mu_size_postfix'] : '') . '"';
                        $button_attrs .= ' data-unit-building="' . esc_attr(!empty($unit['eas_mu_building_no']) ? $unit['eas_mu_building_no'] : '') . '"';
                        $button_attrs .= ' data-unit-floor="' . esc_attr(!empty($unit['eas_mu_floor']) ? $unit['eas_mu_floor'] : '') . '"';
                        $button_attrs .= ' data-unit-entrance="' . esc_attr(!empty($unit['eas_mu_entrance']) ? $unit['eas_mu_entrance'] : '') . '"';
                        $button_attrs .= ' data-unit-apno="' . esc_attr(!empty($unit['eas_mu_apno']) ? $unit['eas_mu_apno'] : '') . '"';
                        $button_attrs .= ' data-unit-description="' . esc_attr(!empty($unit['eas_mu_description']) ? $unit['eas_mu_description'] : '') . '"';
                        $button_attrs .= ' data-unit-status="' . esc_attr(!empty($unit['eas_mu_status']) ? $unit['eas_mu_status'] : '') . '"';
                        $button_attrs .= ' data-unit-surface="' . esc_attr(!empty($unit['eas_mu_surface_all']) ? $unit['eas_mu_surface_all'] : '') . '"';
                        break;
                    
                    case 'custom':
                        $button_url = !empty($settings['custom_popup_url']) ? $settings['custom_popup_url'] : '#';
                        break;
                    
                    default:
                        // Default no-popup behavior
                        break;
                }
                
                // Button text with optional icon
                $button_text = !empty($settings['button_text']) ? $settings['button_text'] : __('Inquire', 'estatesite-houzez');
                
                // Prepare inquiry button HTML
                $inquiry_button_html = '<a href="' . esc_url($button_url) . '" class="' . esc_attr($button_classes) . '"' . $button_attrs . '>';
                
                if ($settings['show_inquiry_icon'] === 'yes' && $settings['icon_position'] === 'before') {
                    $inquiry_button_html .= '<span class="estatesite-button-icon-before">';
                    // Add custom style attribute for the icon
                    $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                    $inquiry_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                    ob_start();
                    Icons_Manager::render_icon($settings['inquiry_icon'], ['aria-hidden' => 'true']);
                    $inquiry_button_html .= ob_get_clean();
                    $inquiry_button_html .= '</span>';
                    $inquiry_button_html .= '</span>';
                }
                
                if ($settings['hide_button_text'] !== 'yes') {
                    $inquiry_button_html .= esc_html($button_text);
                }
                
                if ($settings['show_inquiry_icon'] === 'yes' && $settings['icon_position'] === 'after') {
                    $inquiry_button_html .= '<span class="estatesite-button-icon-after">';
                    // Add custom style attribute for the icon
                    $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                    $inquiry_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                    ob_start();
                    Icons_Manager::render_icon($settings['inquiry_icon'], ['aria-hidden' => 'true']);
                    $inquiry_button_html .= ob_get_clean();
                    $inquiry_button_html .= '</span>';
                    $inquiry_button_html .= '</span>';
                }
                
                // Add icon after text if set
                if (!empty($settings['button_icon_after']['value'])) {
                    $inquiry_button_html .= '<span class="estatesite-button-icon-after">';
                    ob_start();
                    Icons_Manager::render_icon($settings['button_icon_after']);
                    $inquiry_button_html .= ob_get_clean();
                    $inquiry_button_html .= '</span>';
                }
                
                $inquiry_button_html .= '</a>';
                
                // Prepare gallery button HTML
                $gallery_button_html = '';
                if ($settings['show_gallery_button'] === 'yes' && !empty($unit['eas_mu_photos'])) {
                    $gallery_button_text = !empty($settings['gallery_button_text']) ? $settings['gallery_button_text'] : esc_html__('Gallery', 'estatesite-houzez');
                    $gallery_button_classes = 'btn btn-sm estatesite-gallery-button ' . esc_attr($settings['button_size']);
                    
                    // Gallery button will launch a lightbox with apartment photos
                    $gallery_button_attrs = ' data-unit-id="' . esc_attr($unit_id) . '"';
                    $gallery_button_attrs .= ' data-property-id="' . esc_attr($property_id) . '"';
                    
                    $gallery_button_html = '<a href="#" class="' . esc_attr($gallery_button_classes) . '"' . $gallery_button_attrs . ' onclick="openApartmentGallery(event, this)">';
                    
                    if ($settings['show_gallery_icon'] === 'yes' && $settings['icon_position'] === 'before') {
                        $gallery_button_html .= '<span class="estatesite-button-icon-before">';
                        // Add custom style attribute for the icon
                        $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                        $gallery_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                        ob_start();
                        Icons_Manager::render_icon($settings['gallery_icon'], ['aria-hidden' => 'true']);
                        $gallery_button_html .= ob_get_clean();
                        $gallery_button_html .= '</span>';
                        $gallery_button_html .= '</span>';
                    }
                    
                    if ($settings['hide_button_text'] !== 'yes') {
                        $gallery_button_html .= esc_html($gallery_button_text);
                    }
                    
                    if ($settings['show_gallery_icon'] === 'yes' && $settings['icon_position'] === 'after') {
                        $gallery_button_html .= '<span class="estatesite-button-icon-after">';
                        // Add custom style attribute for the icon
                        $icon_style = !empty($settings['button_icon_color']) ? ' style="color:' . esc_attr($settings['button_icon_color']) . ';"' : '';
                        $gallery_button_html .= '<span class="elementor-button-icon"' . $icon_style . '>';
                        ob_start();
                        Icons_Manager::render_icon($settings['gallery_icon'], ['aria-hidden' => 'true']);
                        $gallery_button_html .= ob_get_clean();
                        $gallery_button_html .= '</span>';
                        $gallery_button_html .= '</span>';
                    }
                    
                    $gallery_button_html .= '</a>';
                }
                
                // Display buttons in the selected order
                if ($settings['button_order'] === 'gallery_first' && !empty($gallery_button_html)) {
                    echo $gallery_button_html . ' ' . $inquiry_button_html;
                } else {
                    echo $inquiry_button_html;
                    if (!empty($gallery_button_html)) {
                        echo ' ' . $gallery_button_html;
                    }
                }
                
                echo '</div>'; // End card actions
            }
            
            echo '</div>'; // End card
        }
        
        echo '</div>'; // End cards container
        echo '</div>'; // End main container

        $this->reset_preview_query(); // Only for preview
    }
}