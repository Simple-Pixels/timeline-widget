<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class SP_Timeline_Widget extends Widget_Base {

    public function get_name() {
        return 'sp_timeline';
    }

    public function get_title() {
        return __( 'Simple Pixels Timeline', 'sp-tl' );
    }

    public function get_icon() {
        return 'eicon-time-line';
    }

    public function get_categories() {
        return [ 'simple-pixels-category' ];
    }

    public function get_style_depends() {
        return [ 'sp-timeline-style' ];
    }

    public function get_script_depends() {
        return [ 'gsap', 'gsap-scrolltrigger', 'sp-timeline-init' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_items',
            [ 'label' => __( 'Timeline Items', 'sp-tl' ) ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'item_date',
            [
                'label' => __( 'Date/Year', 'sp-tl' ),
                'type' => Controls_Manager::TEXT,
                'default' => '2025',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'item_title',
            [
                'label' => __( 'Title', 'sp-tl' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Milestone Title', 'sp-tl' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'item_desc',
            [
                'label' => __( 'Description', 'sp-tl' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Short description for this milestone.', 'sp-tl' ),
                'rows' => 4,
            ]
        );

        $this->add_control(
            'items',
            [
                'label' => __( 'Items', 'sp-tl' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'item_date' => '2006',
                        'item_title' => 'Founded',
                        'item_desc' => 'Founded as an electrical contracting business delivering for commercial, government, and infrastructure clients.',
                    ],
                    [
                        'item_date' => '2010',
                        'item_title' => 'Diversifies',
                        'item_desc' => 'Into multi-trade and subcontractor management for infrastructure and energy programs.',
                    ],
                ],
                'title_field' => '{{{ item_date }}} — {{{ item_title }}}',
            ]
        );

        $this->end_controls_section();

        // Styles
        $this->start_controls_section(
            'section_style',
            [ 'label' => __( 'Styles', 'sp-tl' ), 'tab' => Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'circle_color',
            [
                'label' => __( 'Circle (Ball) Color', 'sp-tl' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#006CFF',
            ]
        );

        $this->add_control(
            'line_color',
            [
                'label' => __( 'Progress Line Color', 'sp-tl' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#006CFF',
            ]
        );

        $this->add_control(
            'spine_color',
            [
                'label' => __( 'Default Spine Color', 'sp-tl' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#bdc3c7',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'year_typo',
                'label' => __( 'Year Typography', 'sp-tl' ),
                'selector' => '{{WRAPPER}} .sp-timeline .year',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typo',
                'label' => __( 'Title Typography', 'sp-tl' ),
                'selector' => '{{WRAPPER}} .sp-timeline .title-row h3',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typo',
                'label' => __( 'Description Typography', 'sp-tl' ),
                'selector' => '{{WRAPPER}} .sp-timeline .content p',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $items = $settings['items'] ?? [];
        $widget_id = esc_attr( $this->get_id() );

        $circle = $settings['circle_color'] ?: '#006CFF';
        $line   = $settings['line_color']   ?: '#006CFF';
        $spine  = $settings['spine_color']  ?: '#bdc3c7';
        ?>
        <div class="sp-timeline-wrap" id="sp-timeline-<?php echo $widget_id; ?>"
             style="--sp-circle: <?php echo esc_attr($circle); ?>; --sp-line: <?php echo esc_attr($line); ?>; --sp-spine: <?php echo esc_attr($spine); ?>;">
            <div class="timeline sp-timeline">
                <ul>
                    <span class="default-line"></span>
                    <span class="draw-line"></span>

                    <?php foreach ( $items as $index => $item ) :
                        $side = ($index % 2 === 0) ? 'left' : 'right';
                    ?>
                        <li class="<?php echo esc_attr( $side ); ?>">
                            <div class="circle"></div>
                            <div class="year"><?php echo esc_html( $item['item_date'] ); ?></div>
                            <div class="content">
                                <div class="title-row"><h3><?php echo esc_html( $item['item_title'] ); ?></h3></div>
                                <p><?php echo esc_html( $item['item_desc'] ); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
        <?php
        $cfg = [ 'selector' => '#sp-timeline-' . $widget_id ];
        wp_add_inline_script(
            'sp-timeline-init',
            'window.SP_TIMELINES = window.SP_TIMELINES || []; window.SP_TIMELINES.push(' . wp_json_encode( $cfg ) . ');',
            'after'
        );
    }
}
