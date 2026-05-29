<?php
/**
 * EstateSite Blog Posts Widget
 *
 * Displays latest blog posts with various layout options
 *
 * @since 1.4.0
 */

namespace EstateSite\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Blog_Posts extends Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'estatesite-blog-posts';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return esc_html__('Blog Posts', 'estatesite-houzez');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'estatesite-element-icon eicon-posts-grid';
    }

    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['estatesite-elements'];
    }

    /**
     * Show in panel
     */
    public function show_in_panel() {
        return true;
    }

    /**
     * Get custom help URL
     */
    public function get_custom_help_url() {
        return 'https://estatesite.eu';
    }

    /**
     * Get widget badge
     */
    public function get_badge() {
        return 'Estate Site';
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {

        // Content Section
        // Query Section
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'query_type',
            [
                'label' => esc_html__('Query Type', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => esc_html__('All Posts', 'estatesite-houzez'),
                    'archive' => esc_html__('Archive Posts (Current Archive)', 'estatesite-houzez'),
                ],
            ]
        );

        $this->add_control(
            'filter_by',
            [
                'label' => esc_html__('Filter By', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'estatesite-houzez'),
                    'category' => esc_html__('Category', 'estatesite-houzez'),
                    'tag' => esc_html__('Tag', 'estatesite-houzez'),
                ],
                'condition' => [
                    'query_type' => 'all',
                ],
            ]
        );

        $this->add_control(
            'categories',
            [
                'label' => esc_html__('Categories', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_categories_list(),
                'condition' => [
                    'query_type' => 'all',
                    'filter_by' => 'category',
                ],
            ]
        );

        $this->add_control(
            'tags',
            [
                'label' => esc_html__('Tags', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_tags_list(),
                'condition' => [
                    'query_type' => 'all',
                    'filter_by' => 'tag',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'estatesite-houzez'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => esc_html__('Order By', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'estatesite-houzez'),
                    'title' => esc_html__('Title', 'estatesite-houzez'),
                    'rand' => esc_html__('Random', 'estatesite-houzez'),
                    'comment_count' => esc_html__('Comment Count', 'estatesite-houzez'),
                    'modified' => esc_html__('Modified Date', 'estatesite-houzez'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => esc_html__('Ascending', 'estatesite-houzez'),
                    'DESC' => esc_html__('Descending', 'estatesite-houzez'),
                ],
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => esc_html__('Pagination Type', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'estatesite-houzez'),
                    'numbers' => esc_html__('Numbers', 'estatesite-houzez'),
                    'load_more' => esc_html__('Load More Button', 'estatesite-houzez'),
                    'infinite_scroll' => esc_html__('Infinite Scroll', 'estatesite-houzez'),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'load_more_text',
            [
                'label' => esc_html__('Load More Button Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Load More', 'estatesite-houzez'),
                'condition' => [
                    'pagination_type' => 'load_more',
                ],
            ]
        );

        $this->end_controls_section();

        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Content', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_layout_toggle',
            [
                'label' => esc_html__('Show Layout Toggle', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => esc_html__('Display grid/list toggle buttons on frontend.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_filter',
            [
                'label' => esc_html__('Show Filter', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => esc_html__('Display category/tag filter dropdown on frontend.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'filter_style',
            [
                'label' => esc_html__('Filter Style', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'buttons',
                'options' => [
                    'buttons' => esc_html__('Buttons', 'estatesite-houzez'),
                    'dropdown' => esc_html__('Dropdown', 'estatesite-houzez'),
                    'carousel' => esc_html__('Carousel', 'estatesite-houzez'),
                ],
                'condition' => [
                    'show_filter' => 'yes',
                ],
                'description' => esc_html__('Carousel option adds horizontal scrolling with navigation arrows.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'filter_type',
            [
                'label' => esc_html__('Filter Type', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'category',
                'options' => [
                    'category' => esc_html__('Category', 'estatesite-houzez'),
                    'tag' => esc_html__('Tag', 'estatesite-houzez'),
                ],
                'condition' => [
                    'show_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'filter_all_text',
            [
                'label' => esc_html__('All Items Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('All', 'estatesite-houzez'),
                'condition' => [
                    'show_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'card_style',
            [
                'label' => esc_html__('Card Style', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'style-1',
                'options' => [
                    'style-1' => esc_html__('Style 1 - Classic', 'estatesite-houzez'),
                    'style-2' => esc_html__('Style 2 - Modern', 'estatesite-houzez'),
                    'style-3' => esc_html__('Style 3 - Minimal', 'estatesite-houzez'),
                    'style-4' => esc_html__('Style 4 - Magazine', 'estatesite-houzez'),
                    'style-5' => esc_html__('Style 5 - Bold Overlay', 'estatesite-houzez'),
                    'style-6' => esc_html__('Style 6 - Side Border Accent', 'estatesite-houzez'),
                ],
                'description' => esc_html__('Choose a predefined card design.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Default Layout', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'estatesite-houzez'),
                    'list' => esc_html__('List', 'estatesite-houzez'),
                    'masonry' => esc_html__('Masonry', 'estatesite-houzez'),
                ],
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => esc_html__('2 Columns', 'estatesite-houzez'),
                    '3' => esc_html__('3 Columns', 'estatesite-houzez'),
                    '4' => esc_html__('4 Columns', 'estatesite-houzez'),
                ],
                'condition' => [
                    'layout' => ['grid', 'masonry'],
                ],
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => esc_html__('Order By', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'estatesite-houzez'),
                    'title' => esc_html__('Title', 'estatesite-houzez'),
                    'modified' => esc_html__('Modified', 'estatesite-houzez'),
                    'comment_count' => esc_html__('Comment Count', 'estatesite-houzez'),
                    'rand' => esc_html__('Random', 'estatesite-houzez'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => esc_html__('Ascending', 'estatesite-houzez'),
                    'DESC' => esc_html__('Descending', 'estatesite-houzez'),
                ],
            ]
        );

        $this->add_control(
            'category',
            [
                'label' => esc_html__('Category', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_categories_list(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'exclude_current',
            [
                'label' => esc_html__('Exclude Current Post', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Display Options Section
        $this->start_controls_section(
            'section_display',
            [
                'label' => esc_html__('Display Options', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => esc_html__('Show Featured Image', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image',
                'default' => 'medium_large',
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'fallback_image',
            [
                'label' => esc_html__('Fallback Image', 'estatesite-houzez'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
                'condition' => [
                    'show_image' => 'yes',
                ],
                'description' => esc_html__('This image will be shown when a post has no featured image.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_length',
            [
                'label' => esc_html__('Title Character Limit', 'estatesite-houzez'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 500,
                'condition' => [
                    'show_title' => 'yes',
                ],
                'description' => esc_html__('Set to 0 for unlimited. Counts characters including spaces.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'title_truncate_text',
            [
                'label' => esc_html__('Title Truncation Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => '...',
                'condition' => [
                    'show_title' => 'yes',
                    'title_length!' => 0,
                ],
                'description' => esc_html__('Text to append when title is truncated.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => esc_html__('Show Excerpt', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length_type',
            [
                'label' => esc_html__('Excerpt Length Type', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'characters',
                'options' => [
                    'characters' => esc_html__('Characters', 'estatesite-houzez'),
                    'words' => esc_html__('Words', 'estatesite-houzez'),
                ],
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label' => esc_html__('Excerpt Length', 'estatesite-houzez'),
                'type' => Controls_Manager::NUMBER,
                'default' => 120,
                'min' => 0,
                'max' => 1000,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
                'description' => esc_html__('Set to 0 for unlimited.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'excerpt_truncate_text',
            [
                'label' => esc_html__('Excerpt Truncation Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => '...',
                'condition' => [
                    'show_excerpt' => 'yes',
                    'excerpt_length!' => 0,
                ],
                'description' => esc_html__('Text to append when excerpt is truncated.', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => esc_html__('Show Date', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label' => esc_html__('Show Author', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_category',
            [
                'label' => esc_html__('Show Category', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'category_link',
            [
                'label' => esc_html__('Category Link', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => esc_html__('Make category clickable (link to category archive)', 'estatesite-houzez'),
                'condition' => [
                    'show_category' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_comments',
            [
                'label' => esc_html__('Show Comments Count', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_read_more',
            [
                'label' => esc_html__('Show Read More Button', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => esc_html__('Read More Text', 'estatesite-houzez'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Read More', 'estatesite-houzez'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'card_link',
            [
                'label' => esc_html__('Full Card Link', 'estatesite-houzez'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'separator' => 'before',
                'description' => esc_html__('Make the entire card clickable. This will disable individual links inside the card for better UX.', 'estatesite-houzez'),
            ]
        );

        $this->end_controls_section();

        // Style Section - Card
        $this->start_controls_section(
            'section_style_card',
            [
                'label' => esc_html__('Card', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Columns Gap', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-posts' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'layout!' => 'list',
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Rows Gap', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-posts' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_backdrop_filter_heading',
            [
                'label' => esc_html__('Backdrop Filter', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'card_backdrop_blur',
            [
                'label' => esc_html__('Blur', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post' => 'backdrop-filter: blur({{SIZE}}{{UNIT}}) brightness({{card_backdrop_brightness.SIZE}}%) saturate({{card_backdrop_saturate.SIZE}}%); -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}) brightness({{card_backdrop_brightness.SIZE}}%) saturate({{card_backdrop_saturate.SIZE}}%);',
                ],
            ]
        );

        $this->add_control(
            'card_backdrop_brightness',
            [
                'label' => esc_html__('Brightness', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post' => 'backdrop-filter: blur({{card_backdrop_blur.SIZE}}px) brightness({{SIZE}}%) saturate({{card_backdrop_saturate.SIZE}}%); -webkit-backdrop-filter: blur({{card_backdrop_blur.SIZE}}px) brightness({{SIZE}}%) saturate({{card_backdrop_saturate.SIZE}}%);',
                ],
            ]
        );

        $this->add_control(
            'card_backdrop_saturate',
            [
                'label' => esc_html__('Saturation', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post' => 'backdrop-filter: blur({{card_backdrop_blur.SIZE}}px) brightness({{card_backdrop_brightness.SIZE}}%) saturate({{SIZE}}%); -webkit-backdrop-filter: blur({{card_backdrop_blur.SIZE}}px) brightness({{card_backdrop_brightness.SIZE}}%) saturate({{SIZE}}%);',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .estatesite-blog-post',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .estatesite-blog-post',
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => esc_html__('Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_margin',
            [
                'label' => esc_html__('Margin', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Layout Toggle
        $this->start_controls_section(
            'section_style_toggle',
            [
                'label' => esc_html__('Layout Toggle', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_layout_toggle' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_alignment',
            [
                'label' => esc_html__('Alignment', 'estatesite-houzez'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'estatesite-houzez'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'estatesite-houzez'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'estatesite-houzez'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'flex-end',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-layout-toggle' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_spacing',
            [
                'label' => esc_html__('Bottom Spacing', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-layout-toggle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_button_size',
            [
                'label' => esc_html__('Button Size', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 80,
                    ],
                ],
                'default' => [
                    'size' => 44,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggle_icon_size',
            [
                'label' => esc_html__('Icon Size', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 40,
                    ],
                ],
                'default' => [
                    'size' => 18,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_normal_heading',
            [
                'label' => esc_html__('Normal State', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'toggle_normal_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_normal_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'toggle_normal_border',
                'selector' => '{{WRAPPER}} .estatesite-toggle-btn',
            ]
        );

        $this->add_control(
            'toggle_normal_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'toggle_normal_shadow',
                'selector' => '{{WRAPPER}} .estatesite-toggle-btn',
            ]
        );

        $this->add_control(
            'toggle_inactive_heading',
            [
                'label' => esc_html__('Inactive State', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'toggle_inactive_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#555555',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn:not(.active)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_inactive_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn:not(.active)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_inactive_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn:not(.active)' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'toggle_inactive_shadow',
                'selector' => '{{WRAPPER}} .estatesite-toggle-btn:not(.active)',
            ]
        );

        $this->add_control(
            'toggle_hover_heading',
            [
                'label' => esc_html__('Hover State', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'toggle_hover_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_hover_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F5F5F5',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_active_heading',
            [
                'label' => esc_html__('Active State', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'toggle_active_color',
            [
                'label' => esc_html__('Icon Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_active_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073AA',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'toggle_active_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073AA',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-toggle-btn.active' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Entrance Animation
        $this->start_controls_section(
            'section_style_animation',
            [
                'label' => esc_html__('Entrance Animation', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_animation',
            [
                'label' => esc_html__('Animation', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fadeInUp',
                'options' => [
                    '' => esc_html__('None', 'estatesite-houzez'),
                    'fadeIn' => esc_html__('Fade In', 'estatesite-houzez'),
                    'fadeInDown' => esc_html__('Fade In Down', 'estatesite-houzez'),
                    'fadeInLeft' => esc_html__('Fade In Left', 'estatesite-houzez'),
                    'fadeInRight' => esc_html__('Fade In Right', 'estatesite-houzez'),
                    'fadeInUp' => esc_html__('Fade In Up', 'estatesite-houzez'),
                    'slideInDown' => esc_html__('Slide In Down', 'estatesite-houzez'),
                    'slideInLeft' => esc_html__('Slide In Left', 'estatesite-houzez'),
                    'slideInRight' => esc_html__('Slide In Right', 'estatesite-houzez'),
                    'slideInUp' => esc_html__('Slide In Up', 'estatesite-houzez'),
                    'zoomIn' => esc_html__('Zoom In', 'estatesite-houzez'),
                    'zoomInDown' => esc_html__('Zoom In Down', 'estatesite-houzez'),
                    'zoomInLeft' => esc_html__('Zoom In Left', 'estatesite-houzez'),
                    'zoomInRight' => esc_html__('Zoom In Right', 'estatesite-houzez'),
                    'zoomInUp' => esc_html__('Zoom In Up', 'estatesite-houzez'),
                    'bounceIn' => esc_html__('Bounce In', 'estatesite-houzez'),
                    'bounceInDown' => esc_html__('Bounce In Down', 'estatesite-houzez'),
                    'bounceInLeft' => esc_html__('Bounce In Left', 'estatesite-houzez'),
                    'bounceInRight' => esc_html__('Bounce In Right', 'estatesite-houzez'),
                    'bounceInUp' => esc_html__('Bounce In Up', 'estatesite-houzez'),
                    'flipInX' => esc_html__('Flip In X', 'estatesite-houzez'),
                    'flipInY' => esc_html__('Flip In Y', 'estatesite-houzez'),
                    'rotateIn' => esc_html__('Rotate In', 'estatesite-houzez'),
                    'rollIn' => esc_html__('Roll In', 'estatesite-houzez'),
                ],
            ]
        );

        $this->add_control(
            'item_animation_duration',
            [
                'label' => esc_html__('Animation Duration', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'slow' => esc_html__('Slow', 'estatesite-houzez'),
                    'normal' => esc_html__('Normal', 'estatesite-houzez'),
                    'fast' => esc_html__('Fast', 'estatesite-houzez'),
                ],
                'condition' => [
                    'item_animation!' => '',
                ],
            ]
        );

        $this->add_control(
            'item_animation_delay',
            [
                'label' => esc_html__('Delay Between Items (ms)', 'estatesite-houzez'),
                'type' => Controls_Manager::NUMBER,
                'default' => 100,
                'min' => 0,
                'max' => 1000,
                'step' => 50,
                'condition' => [
                    'item_animation!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Typography
        $this->start_controls_section(
            'section_style_typography',
            [
                'label' => esc_html__('Typography', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => esc_html__('Title', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Title Hover Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'excerpt_heading',
            [
                'label' => esc_html__('Excerpt', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-excerpt',
            ]
        );

        $this->add_control(
            'excerpt_color',
            [
                'label' => esc_html__('Excerpt Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'meta_heading',
            [
                'label' => esc_html__('Meta', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-meta',
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => esc_html__('Meta Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-meta' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-blog-post-meta a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Category
        $this->start_controls_section(
            'section_style_category',
            [
                'label' => esc_html__('Category', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_category' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-category a, {{WRAPPER}} .estatesite-blog-post-category span',
            ]
        );

        $this->start_controls_tabs('category_style_tabs');

        // Normal State
        $this->start_controls_tab(
            'category_style_normal',
            [
                'label' => esc_html__('Normal', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'category_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-blog-post-category span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_bg_color',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category a' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-blog-post-category span' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'category_border',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-category a, {{WRAPPER}} .estatesite-blog-post-category span',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'category_box_shadow',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-category a, {{WRAPPER}} .estatesite-blog-post-category span',
            ]
        );

        $this->end_controls_tab();

        // Hover State
        $this->start_controls_tab(
            'category_style_hover',
            [
                'label' => esc_html__('Hover', 'estatesite-houzez'),
                'condition' => [
                    'category_link' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_hover_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'category_hover_box_shadow',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-category a:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-blog-post-category span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label' => esc_html__('Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-blog-post-category span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_margin',
            [
                'label' => esc_html__('Margin', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Featured Image
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => esc_html__('Featured Image', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_aspect_ratio',
            [
                'label' => esc_html__('Aspect Ratio', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('Default', 'estatesite-houzez'),
                    '1/1' => '1:1 (Square)',
                    '4/3' => '4:3 (Standard)',
                    '3/2' => '3:2',
                    '16/9' => '16:9 (Widescreen)',
                    '21/9' => '21:9 (Ultrawide)',
                    '3/4' => '3:4 (Portrait)',
                    '2/3' => '2:3 (Portrait)',
                    '9/16' => '9:16 (Portrait)',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image' => 'aspect-ratio: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => esc_html__('Height', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 50,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'image_aspect_ratio' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_object_fit',
            [
                'label' => esc_html__('Object Fit', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'fill' => esc_html__('Fill', 'estatesite-houzez'),
                    'contain' => esc_html__('Contain', 'estatesite-houzez'),
                    'cover' => esc_html__('Cover', 'estatesite-houzez'),
                    'none' => esc_html__('None', 'estatesite-houzez'),
                    'scale-down' => esc_html__('Scale Down', 'estatesite-houzez'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_object_position',
            [
                'label' => esc_html__('Object Position', 'estatesite-houzez'),
                'type' => Controls_Manager::SELECT,
                'default' => 'center center',
                'options' => [
                    'center center' => esc_html__('Center Center', 'estatesite-houzez'),
                    'center top' => esc_html__('Center Top', 'estatesite-houzez'),
                    'center bottom' => esc_html__('Center Bottom', 'estatesite-houzez'),
                    'left top' => esc_html__('Left Top', 'estatesite-houzez'),
                    'left center' => esc_html__('Left Center', 'estatesite-houzez'),
                    'left bottom' => esc_html__('Left Bottom', 'estatesite-houzez'),
                    'right top' => esc_html__('Right Top', 'estatesite-houzez'),
                    'right center' => esc_html__('Right Center', 'estatesite-houzez'),
                    'right bottom' => esc_html__('Right Bottom', 'estatesite-houzez'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image img' => 'object-position: {{VALUE}};',
                ],
                'condition' => [
                    'image_object_fit!' => ['fill', 'none'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-image img',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-blog-post-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .estatesite-blog-post-image img',
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => esc_html__('Opacity', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'image_hover_heading',
            [
                'label' => esc_html__('Hover Effects', 'estatesite-houzez'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'image_hover_opacity',
            [
                'label' => esc_html__('Hover Opacity', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post:hover .estatesite-blog-post-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'image_hover_scale',
            [
                'label' => esc_html__('Hover Scale', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.5,
                        'max' => 2,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post:hover .estatesite-blog-post-image img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

        $this->add_control(
            'image_transition_duration',
            [
                'label' => esc_html__('Transition Duration (ms)', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image img' => 'transition: all {{SIZE}}ms ease;',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label' => esc_html__('Margin', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-blog-post-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Filter Style Section
        $this->start_controls_section(
            'section_filter_style',
            [
                'label' => esc_html__('Filter', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_filter' => 'yes',
                ],
            ]
        );

        // Filter Alignment
        $this->add_responsive_control(
            'filter_alignment',
            [
                'label' => esc_html__('Alignment', 'estatesite-houzez'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'estatesite-houzez'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'estatesite-houzez'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'estatesite-houzez'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'flex-end',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-top-bar' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        // Filter Buttons Typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'filter_typography',
                'label' => esc_html__('Typography', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-filter-btn, {{WRAPPER}} .estatesite-filter-dropdown',
            ]
        );

        // Filter Button/Dropdown Spacing
        $this->add_responsive_control(
            'filter_spacing',
            [
                'label' => esc_html__('Spacing', 'estatesite-houzez'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-buttons' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'filter_style' => 'buttons',
                ],
            ]
        );

        // Tabs for Normal and Active states
        $this->start_controls_tabs('filter_style_tabs');

        // Normal State Tab
        $this->start_controls_tab(
            'filter_normal_tab',
            [
                'label' => esc_html__('Normal', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'filter_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-filter-dropdown' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-filter-dropdown' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'filter_border',
                'label' => esc_html__('Border', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-filter-btn, {{WRAPPER}} .estatesite-filter-dropdown',
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'filter_hover_tab',
            [
                'label' => esc_html__('Hover', 'estatesite-houzez'),
            ]
        );

        $this->add_control(
            'filter_hover_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073AA',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-filter-dropdown:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_hover_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f5f9fc',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-filter-dropdown:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073AA',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn:hover' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .estatesite-filter-dropdown:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Active State Tab (only for buttons)
        $this->start_controls_tab(
            'filter_active_tab',
            [
                'label' => esc_html__('Active', 'estatesite-houzez'),
                'condition' => [
                    'filter_style' => 'buttons',
                ],
            ]
        );

        $this->add_control(
            'filter_active_color',
            [
                'label' => esc_html__('Text Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_background',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073AA',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_border_color',
            [
                'label' => esc_html__('Border Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073AA',
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn.active' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // Border Radius
        $this->add_responsive_control(
            'filter_border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 4,
                    'right' => 4,
                    'bottom' => 4,
                    'left' => 4,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-filter-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        // Padding
        $this->add_responsive_control(
            'filter_padding',
            [
                'label' => esc_html__('Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => 10,
                    'right' => 20,
                    'bottom' => 10,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .estatesite-filter-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Box Shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'filter_box_shadow',
                'label' => esc_html__('Box Shadow', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-filter-btn, {{WRAPPER}} .estatesite-filter-dropdown',
            ]
        );

        // Filter Container Padding
        $this->add_responsive_control(
            'filter_container_padding',
            [
                'label' => esc_html__('Container Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                'description' => esc_html__('Add padding to prevent box shadow from being clipped.', 'estatesite-houzez'),
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get categories list for dropdown
     */
    protected function get_categories_list() {
        $categories = get_categories([
            'hide_empty' => false,
        ]);

        $options = [];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }

        return $options;
    }

    /**
     * Render single blog post
     */
    public static function render_post($settings, $column_class, $post_index = 0) {
        // Use shared template function for consistent rendering across widget and AJAX
        return estatesite_render_blog_post_card($settings, $post_index);
    }

    /**
     * Get tags list for dropdown
     */
    protected function get_tags_list() {
        $tags = get_tags([
            'hide_empty' => false,
        ]);

        $options = [];
        foreach ($tags as $tag) {
            $options[$tag->term_id] = $tag->name;
        }

        return $options;
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get current page from custom query var
        $current_page = max(1, get_query_var('espaged', 1));

        // Build query args
        $args = [
            'post_type' => 'post',
            'posts_per_page' => $settings['posts_per_page'],
            'paged' => $current_page,
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
            'post_status' => 'publish',
        ];

        // Check query type
        if ($settings['query_type'] === 'archive') {
            // Use current archive query parameters
            if (is_category()) {
                $args['cat'] = get_queried_object_id();
            } elseif (is_tag()) {
                $args['tag_id'] = get_queried_object_id();
            } elseif (is_author()) {
                $args['author'] = get_queried_object_id();
            } elseif (is_date()) {
                if (is_year()) {
                    $args['year'] = get_query_var('year');
                } elseif (is_month()) {
                    $args['year'] = get_query_var('year');
                    $args['monthnum'] = get_query_var('monthnum');
                } elseif (is_day()) {
                    $args['year'] = get_query_var('year');
                    $args['monthnum'] = get_query_var('monthnum');
                    $args['day'] = get_query_var('day');
                }
            }
        } else {
            // All Posts mode with filters

            // Add category filter
            if ($settings['filter_by'] === 'category' && !empty($settings['categories'])) {
                $args['category__in'] = $settings['categories'];
            }

            // Add tag filter
            if ($settings['filter_by'] === 'tag' && !empty($settings['tags'])) {
                $args['tag__in'] = $settings['tags'];
            }
        }

        // Execute query
        $query = new \WP_Query($args);

        if (!$query->have_posts()) {
            echo '<p>' . esc_html__('No posts found.', 'estatesite-houzez') . '</p>';
            return;
        }

        // Determine column class
        $column_class = '';
        if ($settings['layout'] !== 'list') {
            $columns = $settings['columns'];
            $column_class = 'estatesite-col-' . $columns;
        }

        // Container classes
        $container_classes = [
            'estatesite-blog-posts',
            'estatesite-layout-' . $settings['layout'],
            'estatesite-' . $settings['card_style'],
        ];

        // Add wrapper for toggle buttons or filter
        if ($settings['show_layout_toggle'] === 'yes' || $settings['show_filter'] === 'yes') {
            echo '<div class="estatesite-blog-posts-wrapper">';

            // Top bar with toggle and filter
            if ($settings['show_layout_toggle'] === 'yes' || $settings['show_filter'] === 'yes') {
                echo '<div class="estatesite-top-bar">';

                // Layout toggle buttons
                if ($settings['show_layout_toggle'] === 'yes') {
                    echo '<div class="estatesite-layout-toggle">';
                    echo '<button class="estatesite-toggle-btn estatesite-toggle-grid' . ($settings['layout'] === 'grid' ? ' active' : '') . '" data-layout="grid" aria-label="' . esc_attr__('Grid View', 'estatesite-houzez') . '">';
                    echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><rect x="2" y="2" width="7" height="7"/><rect x="11" y="2" width="7" height="7"/><rect x="2" y="11" width="7" height="7"/><rect x="11" y="11" width="7" height="7"/></svg>';
                    echo '</button>';
                    echo '<button class="estatesite-toggle-btn estatesite-toggle-list' . ($settings['layout'] === 'list' ? ' active' : '') . '" data-layout="list" aria-label="' . esc_attr__('List View', 'estatesite-houzez') . '">';
                    echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><rect x="2" y="3" width="16" height="2"/><rect x="2" y="9" width="16" height="2"/><rect x="2" y="15" width="16" height="2"/></svg>';
                    echo '</button>';
                    echo '</div>';
                }

                // Filter
                if ($settings['show_filter'] === 'yes') {
                    $filter_type = $settings['filter_type'];
                    $filter_style = !empty($settings['filter_style']) ? $settings['filter_style'] : 'buttons';
                    $all_text = !empty($settings['filter_all_text']) ? $settings['filter_all_text'] : esc_html__('All', 'estatesite-houzez');

                    if ($filter_style === 'carousel') {
                        // Carousel filter with navigation
                        echo '<div class="estatesite-filter estatesite-filter-carousel">';
                        echo '<button class="estatesite-carousel-nav estatesite-carousel-prev" aria-label="Previous"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg></button>';
                        echo '<div class="estatesite-carousel-wrapper">';
                        echo '<div class="estatesite-filter-buttons estatesite-carousel-track" data-filter-type="' . esc_attr($filter_type) . '">';
                        echo '<button class="estatesite-filter-btn active" data-filter-value="">' . esc_html($all_text) . '</button>';

                        if ($filter_type === 'category') {
                            $categories = get_categories(['hide_empty' => true]);
                            foreach ($categories as $category) {
                                echo '<button class="estatesite-filter-btn" data-filter-value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</button>';
                            }
                        } else {
                            $tags = get_tags(['hide_empty' => true]);
                            foreach ($tags as $tag) {
                                echo '<button class="estatesite-filter-btn" data-filter-value="' . esc_attr($tag->term_id) . '">' . esc_html($tag->name) . '</button>';
                            }
                        }

                        echo '</div>'; // Close .estatesite-carousel-track
                        echo '</div>'; // Close .estatesite-carousel-wrapper
                        echo '<button class="estatesite-carousel-nav estatesite-carousel-next" aria-label="Next"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg></button>';
                        echo '</div>'; // Close .estatesite-filter-carousel
                    } elseif ($filter_style === 'buttons') {
                        // Regular button filter
                        echo '<div class="estatesite-filter estatesite-filter-buttons-wrap">';
                        echo '<div class="estatesite-filter-buttons" data-filter-type="' . esc_attr($filter_type) . '">';
                        echo '<button class="estatesite-filter-btn active" data-filter-value="">' . esc_html($all_text) . '</button>';

                        if ($filter_type === 'category') {
                            $categories = get_categories(['hide_empty' => true]);
                            foreach ($categories as $category) {
                                echo '<button class="estatesite-filter-btn" data-filter-value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</button>';
                            }
                        } else {
                            $tags = get_tags(['hide_empty' => true]);
                            foreach ($tags as $tag) {
                                echo '<button class="estatesite-filter-btn" data-filter-value="' . esc_attr($tag->term_id) . '">' . esc_html($tag->name) . '</button>';
                            }
                        }

                        echo '</div>';
                        echo '</div>';
                    } else {
                        // Dropdown filter
                        echo '<div class="estatesite-filter estatesite-filter-dropdown-wrap">';
                        echo '<select class="estatesite-filter-dropdown" data-filter-type="' . esc_attr($filter_type) . '">';
                        echo '<option value="">' . esc_html($all_text) . '</option>';

                        if ($filter_type === 'category') {
                            $categories = get_categories(['hide_empty' => true]);
                            foreach ($categories as $category) {
                                echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                            }
                        } else {
                            $tags = get_tags(['hide_empty' => true]);
                            foreach ($tags as $tag) {
                                echo '<option value="' . esc_attr($tag->term_id) . '">' . esc_html($tag->name) . '</option>';
                            }
                        }

                        echo '</select>';
                        echo '</div>';
                    }
                }

                echo '</div>'; // Close .estatesite-top-bar
            }
        }

        // Prepare widget settings for AJAX filtering
        $ajax_settings = json_encode([
            'posts_per_page' => $settings['posts_per_page'],
            'order_by' => $settings['order_by'],
            'order' => $settings['order'],
            'columns' => $settings['columns'],
            'layout' => $settings['layout'],
            'card_style' => $settings['card_style'],
            'show_image' => $settings['show_image'],
            'image_size' => $settings['image_size'],
            'fallback_image' => $settings['fallback_image'],
            'show_category' => $settings['show_category'],
            'category_link' => $settings['category_link'],
            'card_link' => $settings['card_link'],
            'show_title' => $settings['show_title'],
            'title_length' => $settings['title_length'],
            'title_truncate_text' => $settings['title_truncate_text'],
            'show_date' => $settings['show_date'],
            'show_author' => $settings['show_author'],
            'show_comments' => $settings['show_comments'],
            'show_excerpt' => $settings['show_excerpt'],
            'excerpt_length' => $settings['excerpt_length'],
            'excerpt_length_type' => $settings['excerpt_length_type'],
            'excerpt_truncate_text' => $settings['excerpt_truncate_text'],
            'show_read_more' => $settings['show_read_more'],
            'read_more_text' => $settings['read_more_text'],
            'item_animation' => $settings['item_animation'],
            'item_animation_duration' => $settings['item_animation_duration'],
            'item_animation_delay' => $settings['item_animation_delay'],
        ]);

        echo '<div class="' . esc_attr(implode(' ', $container_classes)) . '" data-columns="' . esc_attr($settings['columns']) . '" data-settings="' . esc_attr($ajax_settings) . '">';

        $post_index = 0;
        while ($query->have_posts()) {
            $query->the_post();
            echo self::render_post($settings, $column_class, $post_index);
            $post_index++;
        }

        echo '</div>'; // Close .estatesite-blog-posts

        // Pagination
        if ($settings['pagination_type'] !== 'none' && $query->max_num_pages > 1) {
            echo '<div class="estatesite-pagination">';

            if ($settings['pagination_type'] === 'numbers') {
                // Custom pagination that works independently of WordPress query
                $current_page = max(1, get_query_var('espaged', 1));
                $total_pages = $query->max_num_pages;

                if ($total_pages > 1) {
                    echo '<ul class="page-numbers">';

                    // Previous button
                    if ($current_page > 1) {
                        $prev_url = add_query_arg('espaged', $current_page - 1);
                        echo '<li><a class="prev page-numbers" href="' . esc_url($prev_url) . '">';
                        echo '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M10 2L5 8l5 6V2z"/></svg>';
                        echo '</a></li>';
                    }

                    // Page numbers
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $current_page) {
                            echo '<li><span aria-current="page" class="page-numbers current">' . $i . '</span></li>';
                        } else {
                            $page_url = ($i == 1) ? remove_query_arg('espaged') : add_query_arg('espaged', $i);
                            echo '<li><a class="page-numbers" href="' . esc_url($page_url) . '">' . $i . '</a></li>';
                        }
                    }

                    // Next button
                    if ($current_page < $total_pages) {
                        $next_url = add_query_arg('espaged', $current_page + 1);
                        echo '<li><a class="next page-numbers" href="' . esc_url($next_url) . '">';
                        echo '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M6 2l5 6-5 6V2z"/></svg>';
                        echo '</a></li>';
                    }

                    echo '</ul>';
                }
            } elseif ($settings['pagination_type'] === 'load_more') {
                // Load More button with widget settings
                $load_more_text = !empty($settings['load_more_text']) ? $settings['load_more_text'] : esc_html__('Load More', 'estatesite-houzez');

                $widget_settings = json_encode([
                    'query_type' => $settings['query_type'],
                    'filter_by' => $settings['filter_by'],
                    'categories' => !empty($settings['categories']) ? $settings['categories'] : [],
                    'tags' => !empty($settings['tags']) ? $settings['tags'] : [],
                    'posts_per_page' => $settings['posts_per_page'],
                    'order_by' => $settings['order_by'],
                    'order' => $settings['order'],
                    'columns' => $settings['columns'],
                    'layout' => $settings['layout'],
                    'card_style' => $settings['card_style'],
                    // Display settings
                    'show_image' => $settings['show_image'],
                    'image_size' => $settings['image_size'],
                    'fallback_image' => $settings['fallback_image'],
                    'show_category' => $settings['show_category'],
                    'category_link' => $settings['category_link'],
                    'card_link' => $settings['card_link'],
                    'show_title' => $settings['show_title'],
                    'title_length' => $settings['title_length'],
                    'title_truncate_text' => $settings['title_truncate_text'],
                    'show_date' => $settings['show_date'],
                    'show_author' => $settings['show_author'],
                    'show_comments' => $settings['show_comments'],
                    'show_excerpt' => $settings['show_excerpt'],
                    'excerpt_length' => $settings['excerpt_length'],
                    'excerpt_length_type' => $settings['excerpt_length_type'],
                    'excerpt_truncate_text' => $settings['excerpt_truncate_text'],
                    'show_read_more' => $settings['show_read_more'],
                    'read_more_text' => $settings['read_more_text'],
                    // Animation settings
                    'item_animation' => $settings['item_animation'],
                    'item_animation_duration' => $settings['item_animation_duration'],
                    'item_animation_delay' => $settings['item_animation_delay'],
                ]);

                echo '<button class="estatesite-load-more-btn" ';
                echo 'data-page="1" ';
                echo 'data-max-pages="' . esc_attr($query->max_num_pages) . '" ';
                echo 'data-settings="' . esc_attr($widget_settings) . '">';
                echo '<span class="load-more-text">' . esc_html($load_more_text) . '</span>';
                echo '<span class="load-more-spinner" style="display:none;">Loading...</span>';
                echo '</button>';
            } elseif ($settings['pagination_type'] === 'infinite_scroll') {
                // Infinite scroll trigger with widget settings
                $widget_settings = json_encode([
                    'query_type' => $settings['query_type'],
                    'filter_by' => $settings['filter_by'],
                    'categories' => !empty($settings['categories']) ? $settings['categories'] : [],
                    'tags' => !empty($settings['tags']) ? $settings['tags'] : [],
                    'posts_per_page' => $settings['posts_per_page'],
                    'order_by' => $settings['order_by'],
                    'order' => $settings['order'],
                    'columns' => $settings['columns'],
                    'layout' => $settings['layout'],
                    'card_style' => $settings['card_style'],
                    // Display settings
                    'show_image' => $settings['show_image'],
                    'image_size' => $settings['image_size'],
                    'fallback_image' => $settings['fallback_image'],
                    'show_category' => $settings['show_category'],
                    'category_link' => $settings['category_link'],
                    'card_link' => $settings['card_link'],
                    'show_title' => $settings['show_title'],
                    'title_length' => $settings['title_length'],
                    'title_truncate_text' => $settings['title_truncate_text'],
                    'show_date' => $settings['show_date'],
                    'show_author' => $settings['show_author'],
                    'show_comments' => $settings['show_comments'],
                    'show_excerpt' => $settings['show_excerpt'],
                    'excerpt_length' => $settings['excerpt_length'],
                    'excerpt_length_type' => $settings['excerpt_length_type'],
                    'excerpt_truncate_text' => $settings['excerpt_truncate_text'],
                    'show_read_more' => $settings['show_read_more'],
                    'read_more_text' => $settings['read_more_text'],
                    // Animation settings
                    'item_animation' => $settings['item_animation'],
                    'item_animation_duration' => $settings['item_animation_duration'],
                    'item_animation_delay' => $settings['item_animation_delay'],
                ]);

                echo '<div class="estatesite-infinite-scroll-trigger" ';
                echo 'data-page="1" ';
                echo 'data-max-pages="' . esc_attr($query->max_num_pages) . '" ';
                echo 'data-settings="' . esc_attr($widget_settings) . '"></div>';
                echo '<div class="estatesite-infinite-scroll-loading" style="display:none;">Loading...</div>';
            }

            echo '</div>';
        }

        // Close wrapper if toggle or filter is enabled
        if ($settings['show_layout_toggle'] === 'yes' || $settings['show_filter'] === 'yes') {
            echo '</div>'; // Close .estatesite-blog-posts-wrapper
        }

        wp_reset_postdata();
    }
}
