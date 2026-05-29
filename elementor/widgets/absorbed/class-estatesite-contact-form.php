<?php
/**
 * Enhanced Contact Form Elementor Widget
 *
 * @package EstateSite\Elementor\Widgets
 * @since 1.5.0
 */

namespace EstateSite\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Contact_Form extends Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'estatesite-contact-form';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return esc_html__('Enhanced Contact Form', 'estatesite-houzez');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['estatesite-elements'];
    }

    /**
     * Get Estate Site badge
     */
    public function get_badge() {
        return 'Estate Site';
    }

    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['contact', 'form', 'meeting', 'inquiry', 'schedule', 'appointment'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        // Content Tab
        $this->register_meeting_types_controls();
        $this->register_form_fields_controls();
        $this->register_form_settings_controls();

        // Style Tab
        $this->register_meeting_cards_style_controls();
        $this->register_form_fields_style_controls();
        $this->register_button_style_controls();
        $this->register_message_style_controls();
    }

    /**
     * Advanced Choices Section
     */
    private function register_meeting_types_controls() {
        $this->start_controls_section(
            'section_meeting_types',
            [
                'label' => esc_html__('Advanced Choices', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_meeting_types',
            [
                'label' => esc_html__('Show Advanced Choices', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'meeting_types_label',
            [
                'label' => esc_html__('Section Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Select an Option', 'estatesite-houzez'),
                'condition' => ['show_meeting_types' => 'yes'],
            ]
        );

        $this->add_control(
            'choices_selection_mode',
            [
                'label' => esc_html__('Selection Mode', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'single',
                'options' => [
                    'single' => esc_html__('Single Choice', 'estatesite-houzez'),
                    'multiple' => esc_html__('Multiple Choices', 'estatesite-houzez'),
                ],
                'condition' => ['show_meeting_types' => 'yes'],
            ]
        );

        $this->add_control(
            'choices_layout',
            [
                'label' => esc_html__('Layout', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'estatesite-houzez'),
                    'list' => esc_html__('List', 'estatesite-houzez'),
                ],
                'condition' => ['show_meeting_types' => 'yes'],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'meeting_title',
            [
                'label' => esc_html__('Title', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Option', 'estatesite-houzez'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'meeting_description',
            [
                'label' => esc_html__('Description', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'rows' => 2,
            ]
        );

        $repeater->add_control(
            'meeting_icon',
            [
                'label' => esc_html__('Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-check-circle',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'meeting_types',
            [
                'label' => esc_html__('Choices', 'estatesite-houzez'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'meeting_title' => esc_html__('Property Viewing', 'estatesite-houzez'),
                        'meeting_description' => esc_html__('Visit a property with our experts', 'estatesite-houzez'),
                        'meeting_icon' => ['value' => 'fas fa-home', 'library' => 'fa-solid'],
                    ],
                    [
                        'meeting_title' => esc_html__('Free Consultation', 'estatesite-houzez'),
                        'meeting_description' => esc_html__('Discuss your property needs', 'estatesite-houzez'),
                        'meeting_icon' => ['value' => 'fas fa-comments', 'library' => 'fa-solid'],
                    ],
                    [
                        'meeting_title' => esc_html__('Investment Advice', 'estatesite-houzez'),
                        'meeting_description' => esc_html__('Strategies for profitable investments', 'estatesite-houzez'),
                        'meeting_icon' => ['value' => 'fas fa-chart-line', 'library' => 'fa-solid'],
                    ],
                ],
                'title_field' => '{{{ meeting_title }}}',
                'condition' => ['show_meeting_types' => 'yes'],
            ]
        );

        $this->add_control(
            'meeting_animation',
            [
                'label' => esc_html__('Card Animation', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fadeInUp',
                'options' => [
                    '' => esc_html__('None', 'estatesite-houzez'),
                    'fadeIn' => esc_html__('Fade In', 'estatesite-houzez'),
                    'fadeInUp' => esc_html__('Fade In Up', 'estatesite-houzez'),
                    'fadeInDown' => esc_html__('Fade In Down', 'estatesite-houzez'),
                    'fadeInLeft' => esc_html__('Fade In Left', 'estatesite-houzez'),
                    'fadeInRight' => esc_html__('Fade In Right', 'estatesite-houzez'),
                    'zoomIn' => esc_html__('Zoom In', 'estatesite-houzez'),
                    'bounceIn' => esc_html__('Bounce In', 'estatesite-houzez'),
                    'slideInUp' => esc_html__('Slide In Up', 'estatesite-houzez'),
                    'pulse' => esc_html__('Pulse', 'estatesite-houzez'),
                ],
                'condition' => ['show_meeting_types' => 'yes'],
            ]
        );

        $this->add_control(
            'animation_delay',
            [
                'label' => esc_html__('Staggered Delay (ms)', 'estatesite-houzez'),
                'type' => Controls_Manager::NUMBER,
                'default' => 100,
                'min' => 0,
                'max' => 500,
                'step' => 50,
                'condition' => [
                    'show_meeting_types' => 'yes',
                    'meeting_animation!' => '',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Form Fields Section (Repeater)
     */
    private function register_form_fields_controls() {
        $this->start_controls_section(
            'section_form_fields',
            [
                'label' => esc_html__('Form Fields', 'estatesite-houzez'),
            ]
        );

        $repeater = new Repeater();

        // Field Type
        $repeater->add_control(
            'field_type',
            [
                'label' => esc_html__('Field Type', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'name',
                'options' => [
                    'name' => esc_html__('Name', 'estatesite-houzez'),
                    'phone' => esc_html__('Phone', 'estatesite-houzez'),
                    'email' => esc_html__('Email', 'estatesite-houzez'),
                    'location' => esc_html__('Location', 'estatesite-houzez'),
                    'datetime' => esc_html__('Date & Time', 'estatesite-houzez'),
                    'message' => esc_html__('Message', 'estatesite-houzez'),
                ],
            ]
        );

        // Shared: Placeholder / Label
        $repeater->add_control(
            'field_label',
            [
                'label' => esc_html__('Placeholder', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'condition' => [
                    'field_type' => ['name', 'phone', 'email'],
                ],
            ]
        );

        // Shared: Icon (for simple fields)
        $repeater->add_control(
            'field_icon',
            [
                'label' => esc_html__('Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::ICONS,
                'condition' => [
                    'field_type' => ['name', 'phone', 'email'],
                ],
            ]
        );

        // Shared: Required (for simple fields)
        $repeater->add_control(
            'required',
            [
                'label' => esc_html__('Required', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'field_type' => ['name', 'phone', 'email'],
                ],
            ]
        );

        // Shared: Width
        $repeater->add_control(
            'width',
            [
                'label' => esc_html__('Width', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => '100',
                'options' => [
                    '100' => '100%',
                    '50' => '50%',
                    '33' => '33%',
                    '25' => '25%',
                ],
            ]
        );

        // ── Location-specific controls ──

        $repeater->add_control(
            'show_country',
            [
                'label' => esc_html__('Show Country', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => ['field_type' => 'location'],
            ]
        );

        $repeater->add_control(
            'country_placeholder',
            [
                'label' => esc_html__('Country Placeholder', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Select Country', 'estatesite-houzez'),
                'condition' => ['field_type' => 'location', 'show_country' => 'yes'],
            ]
        );

        $repeater->add_control(
            'country_selection_mode',
            [
                'label' => esc_html__('Country Selection Mode', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'single',
                'options' => [
                    'single' => esc_html__('Single Select', 'estatesite-houzez'),
                    'multiple' => esc_html__('Multi Select', 'estatesite-houzez'),
                ],
                'condition' => ['field_type' => 'location', 'show_country' => 'yes'],
            ]
        );

        $repeater->add_control(
            'country_search_enabled',
            [
                'label' => esc_html__('Country Search', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => ['field_type' => 'location', 'show_country' => 'yes'],
            ]
        );

        $repeater->add_control(
            'country_required',
            [
                'label' => esc_html__('Country Required', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => ['field_type' => 'location', 'show_country' => 'yes'],
            ]
        );

        $repeater->add_control(
            'show_city',
            [
                'label' => esc_html__('Show City', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => ['field_type' => 'location'],
            ]
        );

        $repeater->add_control(
            'city_placeholder',
            [
                'label' => esc_html__('City Placeholder', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Select City', 'estatesite-houzez'),
                'condition' => ['field_type' => 'location', 'show_city' => 'yes'],
            ]
        );

        $repeater->add_control(
            'city_selection_mode',
            [
                'label' => esc_html__('City Selection Mode', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'single',
                'options' => [
                    'single' => esc_html__('Single Select', 'estatesite-houzez'),
                    'multiple' => esc_html__('Multi Select', 'estatesite-houzez'),
                ],
                'condition' => ['field_type' => 'location', 'show_city' => 'yes'],
            ]
        );

        $repeater->add_control(
            'city_search_enabled',
            [
                'label' => esc_html__('City Search', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => ['field_type' => 'location', 'show_city' => 'yes'],
            ]
        );

        $repeater->add_control(
            'city_required',
            [
                'label' => esc_html__('City Required', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => ['field_type' => 'location', 'show_city' => 'yes'],
            ]
        );

        $repeater->add_control(
            'show_district',
            [
                'label' => esc_html__('Show District', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => ['field_type' => 'location'],
            ]
        );

        $repeater->add_control(
            'district_placeholder',
            [
                'label' => esc_html__('District Placeholder', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Select District', 'estatesite-houzez'),
                'condition' => ['field_type' => 'location', 'show_district' => 'yes'],
            ]
        );

        $repeater->add_control(
            'district_selection_mode',
            [
                'label' => esc_html__('District Selection Mode', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'single',
                'options' => [
                    'single' => esc_html__('Single Select', 'estatesite-houzez'),
                    'multiple' => esc_html__('Multi Select', 'estatesite-houzez'),
                ],
                'condition' => ['field_type' => 'location', 'show_district' => 'yes'],
            ]
        );

        $repeater->add_control(
            'district_search_enabled',
            [
                'label' => esc_html__('District Search', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => ['field_type' => 'location', 'show_district' => 'yes'],
            ]
        );

        $repeater->add_control(
            'district_required',
            [
                'label' => esc_html__('District Required', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => ['field_type' => 'location', 'show_district' => 'yes'],
            ]
        );

        // ── DateTime-specific controls ──

        $repeater->add_control(
            'show_date',
            [
                'label' => esc_html__('Show Date', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => ['field_type' => 'datetime'],
            ]
        );

        $repeater->add_control(
            'date_placeholder',
            [
                'label' => esc_html__('Date Placeholder', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Preferred Date', 'estatesite-houzez'),
                'condition' => ['field_type' => 'datetime', 'show_date' => 'yes'],
            ]
        );

        $repeater->add_control(
            'date_icon',
            [
                'label' => esc_html__('Date Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::ICONS,
                'default' => ['value' => 'fas fa-calendar', 'library' => 'fa-solid'],
                'condition' => ['field_type' => 'datetime', 'show_date' => 'yes'],
            ]
        );

        $repeater->add_control(
            'date_required',
            [
                'label' => esc_html__('Date Required', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => ['field_type' => 'datetime', 'show_date' => 'yes'],
            ]
        );

        $repeater->add_control(
            'date_allowed_days',
            [
                'label' => esc_html__('Allowed Days', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => esc_html__('All Days', 'estatesite-houzez'),
                    'weekdays' => esc_html__('Weekdays Only (Mon-Fri)', 'estatesite-houzez'),
                ],
                'condition' => ['field_type' => 'datetime', 'show_date' => 'yes'],
            ]
        );

        $repeater->add_control(
            'show_time',
            [
                'label' => esc_html__('Show Time', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => ['field_type' => 'datetime'],
            ]
        );

        $repeater->add_control(
            'time_placeholder',
            [
                'label' => esc_html__('Time Placeholder', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Preferred Time', 'estatesite-houzez'),
                'condition' => ['field_type' => 'datetime', 'show_time' => 'yes'],
            ]
        );

        $repeater->add_control(
            'time_icon',
            [
                'label' => esc_html__('Time Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::ICONS,
                'default' => ['value' => 'fas fa-clock', 'library' => 'fa-solid'],
                'condition' => ['field_type' => 'datetime', 'show_time' => 'yes'],
            ]
        );

        $repeater->add_control(
            'time_required',
            [
                'label' => esc_html__('Time Required', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => ['field_type' => 'datetime', 'show_time' => 'yes'],
            ]
        );

        // Generate hour options for dropdowns
        $hour_options = [];
        for ($h = 0; $h < 24; $h++) {
            $hour_formatted = sprintf('%02d:00', $h);
            $hour_options[$hour_formatted] = $hour_formatted;
        }

        $repeater->add_control(
            'time_min_hour',
            [
                'label' => esc_html__('Start Hour', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => '09:00',
                'options' => $hour_options,
                'condition' => ['field_type' => 'datetime', 'show_time' => 'yes'],
            ]
        );

        $repeater->add_control(
            'time_max_hour',
            [
                'label' => esc_html__('End Hour', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => '18:00',
                'options' => $hour_options,
                'condition' => ['field_type' => 'datetime', 'show_time' => 'yes'],
            ]
        );

        $repeater->add_control(
            'time_increment',
            [
                'label' => esc_html__('Time Increment', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => '30',
                'options' => [
                    '15' => esc_html__('15 minutes', 'estatesite-houzez'),
                    '30' => esc_html__('30 minutes', 'estatesite-houzez'),
                    '60' => esc_html__('1 hour', 'estatesite-houzez'),
                ],
                'condition' => ['field_type' => 'datetime', 'show_time' => 'yes'],
            ]
        );

        // ── Message-specific controls ──

        $repeater->add_control(
            'message_label',
            [
                'label' => esc_html__('Label', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Additional Information', 'estatesite-houzez'),
                'condition' => ['field_type' => 'message'],
            ]
        );

        $repeater->add_control(
            'message_label_note',
            [
                'label' => esc_html__('Label Note', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('(optional)', 'estatesite-houzez'),
                'condition' => ['field_type' => 'message'],
            ]
        );

        $repeater->add_control(
            'message_placeholder',
            [
                'label' => esc_html__('Placeholder', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Tell us more about your preferences...', 'estatesite-houzez'),
                'condition' => ['field_type' => 'message'],
            ]
        );

        $repeater->add_control(
            'message_rows',
            [
                'label' => esc_html__('Rows', 'estatesite-houzez'),
                'type' => Controls_Manager::NUMBER,
                'default' => 4,
                'min' => 2,
                'max' => 10,
                'condition' => ['field_type' => 'message'],
            ]
        );

        // Add the repeater control
        $this->add_control(
            'form_fields',
            [
                'label' => esc_html__('Fields', 'estatesite-houzez'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'field_type' => 'name',
                        'field_label' => esc_html__('Your Name', 'estatesite-houzez'),
                        'field_icon' => ['value' => 'fas fa-user', 'library' => 'fa-solid'],
                        'width' => '50',
                        'required' => 'yes',
                    ],
                    [
                        'field_type' => 'phone',
                        'field_label' => esc_html__('+359 88 123 4567', 'estatesite-houzez'),
                        'field_icon' => ['value' => 'fas fa-phone', 'library' => 'fa-solid'],
                        'width' => '50',
                        'required' => 'yes',
                    ],
                    [
                        'field_type' => 'email',
                        'field_label' => esc_html__('your@email.com', 'estatesite-houzez'),
                        'field_icon' => ['value' => 'fas fa-envelope', 'library' => 'fa-solid'],
                        'width' => '100',
                        'required' => 'yes',
                    ],
                    [
                        'field_type' => 'location',
                        'field_label' => esc_html__('Location', 'estatesite-houzez'),
                        'show_city' => 'yes',
                        'show_district' => 'yes',
                        'width' => '100',
                    ],
                    [
                        'field_type' => 'datetime',
                        'field_label' => esc_html__('Date & Time', 'estatesite-houzez'),
                        'show_date' => 'yes',
                        'show_time' => 'yes',
                        'width' => '100',
                    ],
                    [
                        'field_type' => 'message',
                        'field_label' => esc_html__('Message', 'estatesite-houzez'),
                        'width' => '100',
                    ],
                ],
                'title_field' => '{{{ field_type.charAt(0).toUpperCase() + field_type.slice(1) }}}',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Form Settings Section
     */
    private function register_form_settings_controls() {
        $this->start_controls_section(
            'section_form_settings',
            [
                'label' => esc_html__('Form Settings', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'recipient_email',
            [
                'label' => esc_html__('Recipient Email', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => get_option('admin_email'),
                'placeholder' => get_option('admin_email'),
                'description' => esc_html__('Email address to receive form submissions', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'email_subject',
            [
                'label' => esc_html__('Email Subject', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('New Meeting Request', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'success_message',
            [
                'label' => esc_html__('Success Message', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Thank you! We will contact you within 24 hours to confirm.', 'estatesite-houzez'),
                'rows' => 2,
            ]
        );

        $this->add_control(
            'hide_form_on_success',
            [
                'label' => esc_html__('Hide Form After Success', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'description' => esc_html__('Hide the form fields and show only the success message after successful submission.', 'estatesite-houzez'),
            ]
        );

        // Error Messages Section
        $this->add_control(
            'heading_error_messages',
            [
                'label' => esc_html__('Error Messages', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'error_required',
            [
                'label' => esc_html__('Required Field', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('This field is required', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'error_invalid_email',
            [
                'label' => esc_html__('Invalid Email', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Please enter a valid email address', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'error_invalid_phone',
            [
                'label' => esc_html__('Invalid Phone', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Please enter a valid phone number', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'error_select_option',
            [
                'label' => esc_html__('Select Option (Single)', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Please select an option', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'error_select_at_least_one',
            [
                'label' => esc_html__('Select Option (Multiple)', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Please select at least one option', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'error_general',
            [
                'label' => esc_html__('General Error', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('An error occurred. Please try again.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'heading_button_settings',
            [
                'label' => esc_html__('Button Settings', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'submit_button_text',
            [
                'label' => esc_html__('Submit Button Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Request Meeting', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'footer_text',
            [
                'label' => esc_html__('Footer Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('We will contact you within 24 hours to confirm', 'estatesite-houzez'),
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Advanced Choices Cards Style Section
     */
    private function register_meeting_cards_style_controls() {
        $this->start_controls_section(
            'section_style_meeting_cards',
            [
                'label' => esc_html__('Advanced Choices Cards', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => ['show_meeting_types' => 'yes'],
            ]
        );

        $this->add_responsive_control(
            'cards_per_row',
            [
                'label' => esc_html__('Cards Per Row', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-types.layout-grid .estatesite-meeting-card' => 'flex: 0 0 calc((100% - ({{VALUE}} - 1) * var(--card-gap, 15px)) / {{VALUE}}); max-width: calc((100% - ({{VALUE}} - 1) * var(--card-gap, 15px)) / {{VALUE}});',
                ],
                'condition' => ['choices_layout' => 'grid'],
            ]
        );

        $this->add_responsive_control(
            'card_gap',
            [
                'label' => esc_html__('Card Gap', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 50]],
                'default' => ['size' => 15, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-types' => '--card-gap: {{SIZE}}{{UNIT}}; gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => esc_html__('Card Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => 25,
                    'right' => 20,
                    'bottom' => 25,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => 12,
                    'right' => 12,
                    'bottom' => 12,
                    'left' => 12,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Normal/Selected tabs
        $this->start_controls_tabs('card_style_tabs');

        // Normal tab
        $this->start_controls_tab(
            'card_normal_tab',
            ['label' => esc_html__('Normal', 'estatesite-houzez')]
        );

        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card' => 'border: 2px solid {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_icon_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-meeting-card-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_title_color',
            [
                'label' => esc_html__('Title Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_description_color',
            [
                'label' => esc_html__('Description Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Selected tab
        $this->start_controls_tab(
            'card_selected_tab',
            ['label' => esc_html__('Selected', 'estatesite-houzez')]
        );

        $this->add_control(
            'card_selected_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F5F0E8',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card.selected' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_selected_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#c4b99a',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card.selected' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_selected_icon_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#2C3E50',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card.selected .estatesite-meeting-card-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-meeting-card.selected .estatesite-meeting-card-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_selected_icon_bg',
            [
                'label' => esc_html__('Icon Background', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e8e0d0',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card.selected .estatesite-meeting-card-icon-wrap' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // Icon styling
        $this->add_control(
            'heading_card_icon',
            [
                'label' => esc_html__('Icon', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'card_icon_size',
            [
                'label' => esc_html__('Icon Size', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 16, 'max' => 60]],
                'default' => ['size' => 24, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-meeting-card-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_icon_wrap_size',
            [
                'label' => esc_html__('Icon Container Size', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 40, 'max' => 100]],
                'default' => ['size' => 50, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card-icon-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_icon_bg',
            [
                'label' => esc_html__('Icon Background', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card-icon-wrap' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_icon_border_radius',
            [
                'label' => esc_html__('Icon Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                    '%' => ['min' => 0, 'max' => 50],
                ],
                'default' => ['size' => 8, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-meeting-card-icon-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Typography
        $this->add_control(
            'heading_card_typography',
            [
                'label' => esc_html__('Typography', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'card_title_typography',
                'label' => esc_html__('Title Typography', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-meeting-card-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'card_description_typography',
                'label' => esc_html__('Description Typography', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-meeting-card-description',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Form Fields Style Section
     */
    private function register_form_fields_style_controls() {
        $this->start_controls_section(
            'section_style_fields',
            [
                'label' => esc_html__('Form Fields', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'field_gap',
            [
                'label' => esc_html__('Field Gap', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 40]],
                'default' => ['size' => 20, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-fields' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'field_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field' => 'border: 1px solid {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_focus_border_color',
            [
                'label' => esc_html__('Focus Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#2C3E50',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field:focus' => 'border-color: {{VALUE}}; box-shadow: 0 0 0 3px {{VALUE}}1a;',
                ],
            ]
        );

        $this->add_control(
            'field_text_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_placeholder_color',
            [
                'label' => esc_html__('Placeholder Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_icon_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-cf-field-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_padding',
            [
                'label' => esc_html__('Field Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => 15,
                    'right' => 20,
                    'bottom' => 15,
                    'left' => 45,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field-group.has-icon .estatesite-cf-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-cf-field-group:not(.has-icon) .estatesite-cf-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'field_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'field_typography',
                'label' => esc_html__('Typography', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-cf-field',
            ]
        );

        // Labels
        $this->add_control(
            'heading_labels',
            [
                'label' => esc_html__('Labels', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => esc_html__('Label Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'label_note_color',
            [
                'label' => esc_html__('Label Note Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-label-note' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => esc_html__('Label Typography', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-cf-label',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Button Style Section
     */
    private function register_button_style_controls() {
        $this->start_controls_section(
            'section_style_button',
            [
                'label' => esc_html__('Submit Button', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_width',
            [
                'label' => esc_html__('Width', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => ['min' => 10, 'max' => 100],
                    'px' => ['min' => 100, 'max' => 600],
                ],
                'default' => ['size' => 100, 'unit' => '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-submit' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => 15,
                    'right' => 30,
                    'bottom' => 15,
                    'left' => 30,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Normal/Hover tabs
        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            ['label' => esc_html__('Normal', 'estatesite-houzez')]
        );

        $this->add_control(
            'button_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#2C3E50',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-submit' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .estatesite-cf-submit' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            ['label' => esc_html__('Hover', 'estatesite-houzez')]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#1a252f',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-submit:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-submit:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => esc_html__('Typography', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-cf-submit',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .estatesite-cf-submit',
            ]
        );

        // Footer text
        $this->add_control(
            'heading_footer_text',
            [
                'label' => esc_html__('Footer Text', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'footer_text_color',
            [
                'label' => esc_html__('Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-footer' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'footer_text_typography',
                'label' => esc_html__('Typography', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-cf-footer',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Message Style Section
     */
    private function register_message_style_controls() {
        $this->start_controls_section(
            'section_style_message',
            [
                'label' => esc_html__('Messages', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'success_bg_color',
            [
                'label' => esc_html__('Success Background', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#d4edda',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-message.success' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'success_text_color',
            [
                'label' => esc_html__('Success Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#155724',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-message.success' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'error_bg_color',
            [
                'label' => esc_html__('Error Background', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f8d7da',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-message.error' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'error_text_color',
            [
                'label' => esc_html__('Error Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#721c24',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-message.error' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'message_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 20]],
                'default' => ['size' => 8, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-cf-message' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output
     */
    /**
     * Extract settings from repeater items for a specific field type
     */
    private function get_field_item($form_fields, $type) {
        foreach ($form_fields as $item) {
            if (($item['field_type'] ?? '') === $type) {
                return $item;
            }
        }
        return null;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $form_fields = $settings['form_fields'] ?? [];

        // Extract location and datetime items for JS settings
        $loc = $this->get_field_item($form_fields, 'location');
        $dt  = $this->get_field_item($form_fields, 'datetime');

        // Extract required flags from simple fields
        $name_item  = $this->get_field_item($form_fields, 'name');
        $phone_item = $this->get_field_item($form_fields, 'phone');
        $email_item = $this->get_field_item($form_fields, 'email');

        // Prepare settings for AJAX
        $ajax_settings = [
            'recipient_email' => $settings['recipient_email'],
            'email_subject' => $settings['email_subject'],
            'success_message' => $settings['success_message'],
            'hide_form_on_success' => $settings['hide_form_on_success'] === 'yes',
            'name_required' => $name_item ? ($name_item['required'] ?? '') : '',
            'email_required' => $email_item ? ($email_item['required'] ?? '') : '',
            'phone_required' => $phone_item ? ($phone_item['required'] ?? '') : '',
            'date_required' => $dt ? ($dt['date_required'] ?? '') : '',
            'date_allowed_days' => $dt ? ($dt['date_allowed_days'] ?? 'all') : 'all',
            'time_required' => $dt ? ($dt['time_required'] ?? '') : '',
            // Location field settings
            'country_required' => $loc ? ($loc['country_required'] ?? '') : '',
            'city_required' => $loc ? ($loc['city_required'] ?? '') : '',
            'district_required' => $loc ? ($loc['district_required'] ?? '') : '',
            'country_selection_mode' => $loc ? ($loc['country_selection_mode'] ?? 'single') : 'single',
            'city_selection_mode' => $loc ? ($loc['city_selection_mode'] ?? 'single') : 'single',
            'district_selection_mode' => $loc ? ($loc['district_selection_mode'] ?? 'single') : 'single',
            'choices_selection_mode' => $settings['choices_selection_mode'],
            // Time picker settings
            'time_min_hour' => $dt ? ($dt['time_min_hour'] ?? '09:00') : '09:00',
            'time_max_hour' => $dt ? ($dt['time_max_hour'] ?? '18:00') : '18:00',
            'time_increment' => $dt ? intval($dt['time_increment'] ?? 30) : 30,
            // Error messages
            'translations' => [
                'required' => $settings['error_required'],
                'invalidEmail' => $settings['error_invalid_email'],
                'invalidPhone' => $settings['error_invalid_phone'],
                'selectMeetingType' => $settings['error_select_option'],
                'selectAtLeastOne' => $settings['error_select_at_least_one'],
                'errorOccurred' => $settings['error_general'],
            ],
        ];
        ?>
        <div class="estatesite-contact-form-wrapper">
            <form class="estatesite-contact-form" data-settings="<?php echo esc_attr(json_encode($ajax_settings)); ?>">

                <?php if ($settings['show_meeting_types'] === 'yes' && !empty($settings['meeting_types'])) : ?>
                    <?php if (!empty($settings['meeting_types_label'])) : ?>
                        <div class="estatesite-cf-section-label"><?php echo esc_html($settings['meeting_types_label']); ?></div>
                    <?php endif; ?>

                    <?php
                    $layout_class = 'layout-' . ($settings['choices_layout'] ?: 'grid');
                    $selection_mode = $settings['choices_selection_mode'] ?: 'single';
                    ?>
                    <div class="estatesite-meeting-types <?php echo esc_attr($layout_class); ?>"
                         data-selection-mode="<?php echo esc_attr($selection_mode); ?>">
                        <?php
                        $animation_class = !empty($settings['meeting_animation']) ? 'animate__animated animate__' . $settings['meeting_animation'] : '';
                        $delay = 0;

                        foreach ($settings['meeting_types'] as $index => $item) :
                            $delay_style = '';
                            if (!empty($settings['meeting_animation']) && $settings['animation_delay'] > 0) {
                                $delay_style = 'animation-delay: ' . ($delay * $settings['animation_delay']) . 'ms;';
                                $delay++;
                            }
                            ?>
                            <div class="estatesite-meeting-card <?php echo esc_attr($animation_class); ?>"
                                 data-value="<?php echo esc_attr($item['meeting_title']); ?>"
                                 style="<?php echo esc_attr($delay_style); ?>">
                                <div class="estatesite-meeting-card-icon-wrap">
                                    <span class="estatesite-meeting-card-icon">
                                        <?php Icons_Manager::render_icon($item['meeting_icon'], ['aria-hidden' => 'true']); ?>
                                    </span>
                                </div>
                                <div class="estatesite-meeting-card-content">
                                    <h4 class="estatesite-meeting-card-title"><?php echo esc_html($item['meeting_title']); ?></h4>
                                    <?php if (!empty($item['meeting_description'])) : ?>
                                        <p class="estatesite-meeting-card-description"><?php echo esc_html($item['meeting_description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="meeting_type" value="">
                <?php endif; ?>

                <div class="estatesite-cf-fields">
                    <?php foreach ($form_fields as $index => $item) :
                        $field_type = $item['field_type'] ?? '';
                        $width = $item['width'] ?? '100';
                        $width_class = 'estatesite-cf-col-' . $width;

                        switch ($field_type) :
                            case 'name':
                                $placeholder = !empty($item['field_label']) ? $item['field_label'] : esc_html__('Your Name', 'estatesite-houzez');
                                $icon = $item['field_icon'] ?? [];
                                $is_required = ($item['required'] ?? '') === 'yes';
                                ?>
                                <div class="estatesite-cf-field-group <?php echo $width_class; ?> <?php echo !empty($icon['value']) ? 'has-icon' : ''; ?>">
                                    <?php if (!empty($icon['value'])) : ?>
                                        <span class="estatesite-cf-field-icon">
                                            <?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <input type="text"
                                           name="name"
                                           class="estatesite-cf-field estatesite-cf-name"
                                           placeholder="<?php echo esc_attr($placeholder); ?>"
                                           <?php echo $is_required ? 'required' : ''; ?>>
                                    <span class="estatesite-cf-field-error"></span>
                                </div>
                                <?php break;

                            case 'phone':
                                $placeholder = !empty($item['field_label']) ? $item['field_label'] : esc_html__('+359 88 123 4567', 'estatesite-houzez');
                                $icon = $item['field_icon'] ?? [];
                                $is_required = ($item['required'] ?? '') === 'yes';
                                ?>
                                <div class="estatesite-cf-field-group <?php echo $width_class; ?> <?php echo !empty($icon['value']) ? 'has-icon' : ''; ?>">
                                    <?php if (!empty($icon['value'])) : ?>
                                        <span class="estatesite-cf-field-icon">
                                            <?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <input type="tel"
                                           name="phone"
                                           class="estatesite-cf-field estatesite-cf-phone"
                                           placeholder="<?php echo esc_attr($placeholder); ?>"
                                           <?php echo $is_required ? 'required' : ''; ?>>
                                    <span class="estatesite-cf-field-error"></span>
                                </div>
                                <?php break;

                            case 'email':
                                $placeholder = !empty($item['field_label']) ? $item['field_label'] : esc_html__('your@email.com', 'estatesite-houzez');
                                $icon = $item['field_icon'] ?? [];
                                $is_required = ($item['required'] ?? '') === 'yes';
                                ?>
                                <div class="estatesite-cf-field-group <?php echo $width_class; ?> <?php echo !empty($icon['value']) ? 'has-icon' : ''; ?>">
                                    <?php if (!empty($icon['value'])) : ?>
                                        <span class="estatesite-cf-field-icon">
                                            <?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <input type="email"
                                           name="email"
                                           class="estatesite-cf-field estatesite-cf-email"
                                           placeholder="<?php echo esc_attr($placeholder); ?>"
                                           <?php echo $is_required ? 'required' : ''; ?>>
                                    <span class="estatesite-cf-field-error"></span>
                                </div>
                                <?php break;

                            case 'location':
                                $show_country = ($item['show_country'] ?? '') === 'yes';
                                $show_city = ($item['show_city'] ?? '') === 'yes';
                                $show_district = ($item['show_district'] ?? '') === 'yes';

                                if ($show_country || $show_city || $show_district) :
                                    $countries = estatesite_nom_get_countries();
                                    $country_mode = $item['country_selection_mode'] ?? 'single';
                                    $city_mode = $item['city_selection_mode'] ?? 'single';
                                    $district_mode = $item['district_selection_mode'] ?? 'single';
                                    $is_multi_country = ($country_mode === 'multiple');
                                    $is_multi_city = ($city_mode === 'multiple');
                                    $is_multi_district = ($district_mode === 'multiple');
                                    $country_ph = $item['country_placeholder'] ?? esc_html__('Select Country', 'estatesite-houzez');
                                    $city_ph = $item['city_placeholder'] ?? esc_html__('Select City', 'estatesite-houzez');
                                    $district_ph = $item['district_placeholder'] ?? esc_html__('Select District', 'estatesite-houzez');
                                    ?>
                                    <div class="estatesite-cf-row estatesite-cf-location-row <?php echo $width_class; ?>">
                                        <?php if ($show_country) : ?>
                                        <div class="estatesite-cf-field-group">
                                            <select name="country<?php echo $is_multi_country ? '[]' : ''; ?>"
                                                    class="estatesite-cf-field estatesite-cf-country"
                                                    data-placeholder="<?php echo esc_attr($country_ph); ?>"
                                                    data-mode="<?php echo esc_attr($country_mode); ?>"
                                                    data-search="<?php echo ($item['country_search_enabled'] ?? 'yes') !== 'yes' ? 'false' : 'true'; ?>"
                                                    <?php echo $is_multi_country ? 'multiple' : ''; ?>
                                                    <?php echo ($item['country_required'] ?? '') === 'yes' ? 'required' : ''; ?>>
                                                <?php if (!$is_multi_country) : ?>
                                                    <option value="" placeholder><?php echo esc_html($country_ph); ?></option>
                                                <?php endif; ?>
                                                <?php foreach ($countries as $country) : ?>
                                                    <option value="<?php echo esc_attr($country['id']); ?>"
                                                            data-name="<?php echo esc_attr($country['name']); ?>">
                                                        <?php echo esc_html($country['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="estatesite-cf-field-error"></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($show_city) : ?>
                                            <div class="estatesite-cf-field-group">
                                                <select name="city<?php echo $is_multi_city ? '[]' : ''; ?>"
                                                        class="estatesite-cf-field estatesite-cf-city"
                                                        data-placeholder="<?php echo esc_attr($city_ph); ?>"
                                                        data-mode="<?php echo esc_attr($city_mode); ?>"
                                                        data-search="<?php echo ($item['city_search_enabled'] ?? 'yes') !== 'yes' ? 'false' : 'true'; ?>"
                                                        <?php echo $is_multi_city ? 'multiple' : ''; ?>
                                                        <?php echo ($item['city_required'] ?? '') === 'yes' ? 'required' : ''; ?>
                                                        <?php if (!$show_country) : ?>data-default-country="37"<?php else : ?>disabled<?php endif; ?>>
                                                    <?php if (!$is_multi_city) : ?>
                                                        <option value="" placeholder><?php echo esc_html($city_ph); ?></option>
                                                    <?php endif; ?>
                                                </select>
                                                <span class="estatesite-cf-field-error"></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($show_district) : ?>
                                            <div class="estatesite-cf-field-group">
                                                <select name="district<?php echo $is_multi_district ? '[]' : ''; ?>"
                                                        class="estatesite-cf-field estatesite-cf-district"
                                                        data-placeholder="<?php echo esc_attr($district_ph); ?>"
                                                        data-mode="<?php echo esc_attr($district_mode); ?>"
                                                        data-search="<?php echo ($item['district_search_enabled'] ?? 'yes') !== 'yes' ? 'false' : 'true'; ?>"
                                                        <?php echo $is_multi_district ? 'multiple' : ''; ?>
                                                        <?php echo ($item['district_required'] ?? '') === 'yes' ? 'required' : ''; ?>
                                                        <?php echo $show_city ? 'disabled' : ''; ?>>
                                                    <?php if (!$is_multi_district) : ?>
                                                        <option value="" placeholder><?php echo esc_html($district_ph); ?></option>
                                                    <?php endif; ?>
                                                </select>
                                                <span class="estatesite-cf-field-error"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif;
                                break;

                            case 'datetime':
                                $show_date = ($item['show_date'] ?? 'yes') === 'yes';
                                $show_time = ($item['show_time'] ?? 'yes') === 'yes';

                                if ($show_date || $show_time) :
                                    ?>
                                    <div class="estatesite-cf-row <?php echo $width_class; ?>">
                                        <?php if ($show_date) :
                                            $date_ph = $item['date_placeholder'] ?? esc_html__('Preferred Date', 'estatesite-houzez');
                                            $date_icon = $item['date_icon'] ?? [];
                                            ?>
                                            <div class="estatesite-cf-field-group <?php echo !empty($date_icon['value']) ? 'has-icon' : ''; ?>">
                                                <?php if (!empty($date_icon['value'])) : ?>
                                                    <span class="estatesite-cf-field-icon">
                                                        <?php Icons_Manager::render_icon($date_icon, ['aria-hidden' => 'true']); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <input type="text"
                                                       name="preferred_date"
                                                       class="estatesite-cf-field estatesite-cf-date"
                                                       placeholder="<?php echo esc_attr($date_ph); ?>"
                                                       readonly
                                                       <?php echo ($item['date_required'] ?? '') === 'yes' ? 'required' : ''; ?>>
                                                <span class="estatesite-cf-field-error"></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($show_time) :
                                            $time_ph = $item['time_placeholder'] ?? esc_html__('Preferred Time', 'estatesite-houzez');
                                            $time_icon = $item['time_icon'] ?? [];
                                            $min_hour = intval(substr($item['time_min_hour'] ?? '09:00', 0, 2));
                                            $max_hour = intval(substr($item['time_max_hour'] ?? '18:00', 0, 2));
                                            $increment = intval($item['time_increment'] ?? 30);
                                            ?>
                                            <div class="estatesite-cf-field-group <?php echo !empty($time_icon['value']) ? 'has-icon' : ''; ?>">
                                                <?php if (!empty($time_icon['value'])) : ?>
                                                    <span class="estatesite-cf-field-icon">
                                                        <?php Icons_Manager::render_icon($time_icon, ['aria-hidden' => 'true']); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <select name="preferred_time"
                                                        class="estatesite-cf-field estatesite-cf-time"
                                                        <?php echo ($item['time_required'] ?? '') === 'yes' ? 'required' : ''; ?>>
                                                    <option value=""><?php echo esc_html($time_ph); ?></option>
                                                    <?php
                                                    for ($hour = $min_hour; $hour <= $max_hour; $hour++) {
                                                        for ($min = 0; $min < 60; $min += $increment) {
                                                            if ($hour == $max_hour && $min > 0) break;
                                                            $time_value = sprintf('%02d:%02d', $hour, $min);
                                                            echo '<option value="' . esc_attr($time_value) . '">' . esc_html($time_value) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <span class="estatesite-cf-field-error"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif;
                                break;

                            case 'message':
                                $msg_label = $item['message_label'] ?? esc_html__('Additional Information', 'estatesite-houzez');
                                $msg_note = $item['message_label_note'] ?? esc_html__('(optional)', 'estatesite-houzez');
                                $msg_ph = $item['message_placeholder'] ?? esc_html__('Tell us more about your preferences...', 'estatesite-houzez');
                                $msg_rows = $item['message_rows'] ?? 4;
                                ?>
                                <div class="estatesite-cf-field-group <?php echo $width_class; ?>">
                                    <?php if (!empty($msg_label)) : ?>
                                        <label class="estatesite-cf-label">
                                            <?php echo esc_html($msg_label); ?>
                                            <?php if (!empty($msg_note)) : ?>
                                                <span class="estatesite-cf-label-note"><?php echo esc_html($msg_note); ?></span>
                                            <?php endif; ?>
                                        </label>
                                    <?php endif; ?>
                                    <textarea name="message"
                                              class="estatesite-cf-field estatesite-cf-textarea"
                                              placeholder="<?php echo esc_attr($msg_ph); ?>"
                                              rows="<?php echo esc_attr($msg_rows); ?>"></textarea>
                                </div>
                                <?php break;

                        endswitch;
                    endforeach; ?>
                </div>

                <button type="submit" class="estatesite-cf-submit">
                    <span class="btn-text"><?php echo esc_html($settings['submit_button_text']); ?></span>
                    <span class="btn-loading">
                        <span class="spinner"></span>
                        <?php esc_html_e('Sending...', 'estatesite-houzez'); ?>
                    </span>
                </button>

                <?php if (!empty($settings['footer_text'])) : ?>
                    <p class="estatesite-cf-footer"><?php echo esc_html($settings['footer_text']); ?></p>
                <?php endif; ?>

                <div class="estatesite-cf-message"></div>
            </form>
        </div>
        <?php
    }
}
