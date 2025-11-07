<?php
/**
 * Plugin Name: Simple Pixels Timeline Widget
 * Description: Elementor timeline widget with repeater items + color controls.
 * Version: 1.0.0
 * Author: Simple Pixels
 * Text Domain: sp-tl
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SP_TL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SP_TL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

add_action( 'wp_enqueue_scripts', function () {
    wp_register_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
        [],
        '3.12.5',
        true
    );

    wp_register_script(
        'gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
        ['gsap'],
        '3.12.5',
        true
    );

    wp_register_style(
        'sp-timeline-style',
        SP_TL_PLUGIN_URL . 'assets/sp-timeline.css',
        [],
        '1.0.0'
    );

    wp_register_script(
        'sp-timeline-init',
        SP_TL_PLUGIN_URL . 'assets/sp-timeline-init.js',
        ['jquery','gsap','gsap-scrolltrigger'],
        '1.0.0',
        true
    );
});

add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
    require_once SP_TL_PLUGIN_PATH . 'includes/class-sp-timeline-widget.php';
    $widgets_manager->register( new \SP_Timeline_Widget() );
} );

add_action( 'elementor/elements/categories_registered', function ( $elements_manager ) {
    $elements_manager->add_category(
        'simple-pixels-category',
        [ 'title' => __( 'Simple Pixels Widgets', 'sp-tl' ), 'icon' => 'eicon-folder' ]
    );
} );
