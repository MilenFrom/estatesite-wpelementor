<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor EAS Team Widget.
 * @since 1.0.0
 */
class EstateSite_Team extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'estatesite_team';
    }

    /**
     * Get widget title.
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'EAS Team', 'estatesite-houzez' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'estatesite-element-icon eicon-person';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'estatesite-elements' ];
    }

    /**
     * Get widget badge.
     */
    public function get_badge() {
        return 'Estate Site';
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Content', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'team_image',
            [
                'label'     => esc_html__( 'Image', 'estatesite-houzez' ),
                'type'      => Controls_Manager::MEDIA,
                'description'   => '370 x 550 pixels',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'team_name',
            [
                'label'     => esc_html__( 'Name', 'estatesite-houzez' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'team_position',
            [
                'label'     => esc_html__( 'Position', 'estatesite-houzez' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'team_description',
            [
                'label'     => esc_html__( 'Description', 'estatesite-houzez' ),
                'type'      => Controls_Manager::WYSIWYG,
                'description'   => '',
            ]
        );
        $this->add_control(
            'team_link',
            [
                'label'     => esc_html__( 'Custom Link', 'estatesite-houzez' ),
                'type'      => Controls_Manager::TEXT,
                'placeholder' => 'https://your-link.com',
                'description'   => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'team_social_facebook',
            [
                'label'     => esc_html__( 'Facebook Profile Link', 'estatesite-houzez' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'team_social_facebook_target',
            [
                'label'     => esc_html__( 'Facebook Target', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    "_self" => "Self",
                    "_blank" => "Blank",
                    "_parent" => "Parent"
                ],
                'default' => '',
            ]
        );
        $this->add_control(
            'team_social_twitter',
            [
                'label'     => esc_html__( 'X Profile Link', 'estatesite-houzez' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'team_social_twitter_target',
            [
                'label'     => esc_html__( 'X Target', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    "_self" => "Self",
                    "_blank" => "Blank",
                    "_parent" => "Parent"
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'team_social_linkedin',
            [
                'label'     => esc_html__( 'LinkedIn Profile Link', 'estatesite-houzez' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'team_social_linkedin_target',
            [
                'label'     => esc_html__( 'LinkedIn Target', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    "_self" => "Self",
                    "_blank" => "Blank",
                    "_parent" => "Parent"
                ],
                'default' => '',
            ]
        );
        $this->add_control(
            'team_social_pinterest',
            [
                'label'     => esc_html__( 'Pinterest Profile Link', 'estatesite-houzez' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => '',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'team_social_pinterest_target',
            [
                'label'     => esc_html__( 'Pinterest Target', 'estatesite-houzez' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    "_self" => "Self",
                    "_blank" => "Blank",
                    "_parent" => "Parent"
                ],
                'default' => '',
            ]
        );
        
        $this->end_controls_section();

        /*----------------------------------------------------------
        * Styling
        **---------------------------------------------------------*/
        $this->start_controls_section(
            'styling_section',
            [
                'label'     => esc_html__( 'Box', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'agent_box_border_radius',
            [
                'label' => esc_html__( 'Radius', 'estatesite-houzez' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .team-module' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'agent_box_shadow',
                'selector' => '{{WRAPPER}} .team-module',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'typo_section',
            [
                'label'     => esc_html__( 'Typography', 'estatesite-houzez' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'team_tname',
                'label'    => esc_html__( 'Name', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .team-name strong',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'team-title',
                'label'    => esc_html__( 'Position', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .team-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'team_content',
                'label'    => esc_html__( 'Content', 'estatesite-houzez' ),
                'selector' => '{{WRAPPER}} .team-description',
            ]
        );

        $this->end_controls_section(); 

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();

        $args['team_image']    =  $settings['team_image']['id'];
        $args['team_name']     =  $settings['team_name'];
        $args['team_position']  =  $settings['team_position'];
        $args['team_description']  =  $settings['team_description'];
        $args['team_link']  =  $settings['team_link'];

        $args['team_social_facebook']  =  $settings['team_social_facebook'];
        $args['team_social_twitter']  =  $settings['team_social_twitter'];
        $args['team_social_linkedin']  =  $settings['team_social_linkedin'];
        $args['team_social_pinterest']  =  $settings['team_social_pinterest'];

        $args['team_social_facebook_target']  =  $settings['team_social_facebook_target'];
        $args['team_social_twitter_target']  =  $settings['team_social_twitter_target'];
        $args['team_social_linkedin_target']  =  $settings['team_social_linkedin_target'];
        $args['team_social_pinterest_target']  =  $settings['team_social_pinterest_target'];
       
        if( function_exists( 'houzez_team' ) ) {
            echo houzez_team( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register( new EstateSite_Team ); 