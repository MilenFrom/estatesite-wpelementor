<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EstateSite_Property_Stats extends Widget_Base {

    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

    public function get_name() {
        return 'estatesite_property_stats';
    }

    public function get_title() {
        return esc_html__( 'EstateSite Property Stats', 'estatesite-houzez' );
    }

    public function get_icon() {
        return 'estatesite-element-icon eicon-pie-chart';
    }

    public function get_categories() {
        return [ 'estatesite-elements' ];
    }

    public function get_badge() {
        return 'Estate Site';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'estatesite-houzez' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
    
        $this->add_control(
            'show_property_types',
            [
                'label' => esc_html__( 'Show Property Types', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
    
        $this->add_control(
            'show_listing_types',
            [
                'label' => esc_html__( 'Show Listing Types', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
    
        $this->add_control(
            'show_cities',
            [
                'label' => esc_html__( 'Show Cities', 'estatesite-houzez' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
    
        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'estatesite-houzez'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
    
        $this->add_control(
            'background_color',
            [
                'label' => esc_html__('Background Color', 'estatesite-houzez'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .estatesite-property-stats' => 'background-color: {{VALUE}};',
                ],
            ]
        );
    
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => esc_html__('Background', 'estatesite-houzez'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .estatesite-property-stats',
            ]
        );
    
        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-property-stats' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__('Border', 'estatesite-houzez'),
                'selector' => '{{WRAPPER}} .estatesite-property-stats',
            ]
        );
    
        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'estatesite-houzez'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .estatesite-property-stats' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section();
    }
    
    protected function render() {

        $this->single_property_preview_query(); // Only for preview

        $settings = $this->get_settings_for_display();

        $has_data = false;
        $output = '';

        ob_start();

        if ('yes' === $settings['show_property_types']) {
            $has_data |= $this->render_chart('property_type', esc_html__('Видове Имоти', 'estatesite-houzez'));
        }
        if ('yes' === $settings['show_listing_types']) {
            $has_data |= $this->render_chart('property_status', esc_html__('Видове Обяви', 'estatesite-houzez'));
        }
        if ('yes' === $settings['show_cities']) {
            $has_data |= $this->render_chart('property_city', esc_html__('Обяви в Градове', 'estatesite-houzez'));
        }

        $output = ob_get_clean();

        if ($has_data) {
            $this->add_render_attribute('wrapper', 'class', 'estatesite-property-stats');
            $this->add_render_attribute('wrapper', 'style', 'display: flex; justify-content: space-between; flex-wrap: wrap;');

            echo '<div ' . $this->get_render_attribute_string('wrapper') . '>';
            echo $output;
            echo '</div>';
        }

        $this->reset_preview_query(); // Only for preview
    }
        
    private function render_chart( $taxonomy, $title ) {
        if ( ! function_exists( 'houzez_get_term_slugs_for_stats' ) || ! function_exists( 'houzez_realtor_stats' ) ) {
            return;
        }
    
        $term_data = houzez_get_term_slugs_for_stats( $taxonomy );
        $data = array();
    
        for ( $i = 0; $i < count( $term_data['slug'] ); $i++ ) {
            $count = houzez_realtor_stats( $taxonomy, 'fave_agents', get_the_ID(), $term_data['slug'][$i] );
            if ( $count > 0 ) {
                $data[ $term_data['name'][$i] ] = $count;
            }
        }
    
        if ( empty( $data ) ) {
            return;
        }
    
        $total_count = array_sum( $data );
        foreach ( $data as $key => $value ) {
            $data[$key] = round(($value / $total_count) * 100);
        }
    
        arsort( $data );
        $data = array_slice( $data, 0, 3, true );
        echo '<style>
            .estatesite-property-stats canvas{
                max-width: 100px;
                width: 100px;
                height: 100px;
            }
            .chart-content{
                display: flex;
                margin-top: 10px;
            }
            .estatesite-property-stats{
                display: flex;
                justify-content: space-between;
            }
            .property-stat-chart ul.chart-legend{
                margin-bottom: 0;
                padding-left: 10px;
                display: flex;
                flex-direction: column;
                align-self: center;
            }
        </style>';
        echo '<div class="property-stat-chart">';
        echo '<h5>' . $title . '</h5>';
        echo '<div class="chart-content">';
        echo '<canvas id="' . esc_attr( $taxonomy ) . '-chart"></canvas>';
            echo '<ul class="chart-legend" style="list-style-type: none;">';
                foreach ( $data as $label => $value ) {
                    echo '<li><span class="color-box"></span>' . esc_html( $label ) . ': ' . round( $value, 2 ) . '%</li>';
                }
            echo '</ul>';
        echo '</div>';
        echo '</div>';
    
        $this->render_chart_script( $taxonomy, $data );
        return true;
    }
    
    private function render_chart_script( $taxonomy, $data ) {
        $chart_data = json_encode( array_values( $data ) );
        $chart_labels = json_encode( array_keys( $data ) );
    
        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('{$taxonomy}-chart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: {$chart_data},
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                    }],
                    labels: {$chart_labels}
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        enabled: false
                    }
                }
            });
        });
        </script>";
    }    

}

Plugin::instance()->widgets_manager->register( new EstateSite_Property_Stats() );