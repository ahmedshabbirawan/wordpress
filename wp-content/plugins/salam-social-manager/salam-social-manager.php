<?php
/**
 * Plugin Name: Salam Social Manager
 * Description: Manages the 'Salam Social' custom post type, taxonomy, and handles automatic media watermarking.
 * Version: 1.0
 * Author: AI Assistant
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define( 'SSM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SSM_DOMAIN', 'salam-social-manager' );

// Include necessary files (These classes don't depend on WP_Widget)
require_once SSM_PLUGIN_PATH . 'includes/class-post-type.php';
require_once SSM_PLUGIN_PATH . 'includes/class-meta-boxes.php';
require_once SSM_PLUGIN_PATH . 'includes/class-media-watermark.php';
require_once SSM_PLUGIN_PATH . 'includes/class-settings-page.php';

require_once SSM_PLUGIN_PATH . 'includes/class-extended-recent-posts-widget.php';

// require_once SSM_PLUGIN_PATH . 'includes/class-extended-recent-posts-widget.php';

/**
 * Main Plugin Class
 */
class Salam_Social_Manager {
    public function __construct() {
        // Initialize all non-widget components
        new SSM_Post_Type();
        new SSM_Meta_Boxes();
        new SSM_Media_Watermark();
        new SSM_Settings_Page();
    }
}



function ssm_register_widgets() {
    register_widget( 'SSM_Latest_Posts_Widget' );
}
add_action( 'widgets_init', 'ssm_register_widgets' );

// Instantiate the plugin
new Salam_Social_Manager();

