<?php

class SSM_Settings_Page {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add the top level menu page.
     */
    public function add_plugin_page() {
        add_menu_page(
            'Salam Social Settings',
            'SSM Settings',
            'manage_options',
            'salam-social-settings',
            array( $this, 'create_admin_page' ),
            'dashicons-share',
            6
        );
    }

    /**
     * Options page callback.
     */
    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h2>Salam Social Manager Settings</h2>
            <p>Configure the global settings for the Salam Social plugin, including the media watermark text.</p>
            <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'ssm_option_group' );
                    do_settings_sections( 'salam-social-settings' );
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings.
     */
    public function page_init() {        
        register_setting(
            'ssm_option_group', // Option group
            'ssm_watermark_text', // Option name
            array( $this, 'sanitize_watermark_text' ) // Sanitize callback
        );

        add_settings_section(
            'watermark_settings', // ID
            'Watermark Customization', // Title
            array( $this, 'print_section_info' ), // Callback
            'salam-social-settings' // Page
        );  

        add_settings_field(
            'watermark_text', // ID
            'Watermark Text', // Title 
            array( $this, 'watermark_text_callback' ), // Callback
            'salam-social-settings', // Page
            'watermark_settings' // Section           
        );      
    }

    /**
     * Sanitize the watermark text.
     */
    public function sanitize_watermark_text( $input ) {
        return sanitize_text_field( $input );
    }

    /** * Print the Section text.
     */
    public function print_section_info() {
        print 'Enter your desired watermark text below:';
    }

    /** * Get the settings option array and print one of its values
     */
    public function watermark_text_callback() {
        $text = get_option( 'ssm_watermark_text', 'Salam Social' );
        printf(
            '<input type="text" id="ssm_watermark_text" name="ssm_watermark_text" value="%s" />',
            esc_attr( $text )
        );
    }
}
