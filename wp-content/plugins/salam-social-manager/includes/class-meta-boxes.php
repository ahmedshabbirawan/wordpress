<?php

class SSM_Meta_Boxes {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_source_info_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_source_info_meta_box' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * Enqueue a simple script for the Chapter field's dependency (simulated AJAX).
     */
    public function enqueue_admin_scripts( $hook ) {
        global $post;
        if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            if ( 'salam_social' === $post->post_type || (isset($_GET['post_type']) && 'salam_social' === $_GET['post_type']) ) {
                wp_enqueue_script( 'ssm-meta-script', SSM_PLUGIN_URL . 'js/meta-script.js', array( 'jquery' ), '1.0', true );
                wp_localize_script( 'ssm-meta-script', 'ssm_data', array(
                    // Simulate Chapter data based on Book selection
                    'chapters' => array(
                        'book1' => array( '1-1', '1-2', '1-3' ),
                        'book2' => array( '2-1', '2-2', '2-3' ),
                        'book3' => array( '3-1', '3-2', '3-3' ),
                    ),
                ) );
            }
        }
    }

    /**
     * Add the custom meta box.
     */
    public function add_source_info_meta_box() {
        add_meta_box(
            'ssm_source_info',                 // Unique ID
            __( 'Source Information', SSM_DOMAIN ), // Title
            array( $this, 'render_source_info_meta_box' ), // Callback function
            'salam_social',                    // Post type
            'side',                            // Context: side is better for short fields
            'default'                          // Priority
        );
    }

    /**
     * Render the custom meta box fields.
     */
    public function render_source_info_meta_box( $post ) {
        // Add a nonce field for security
        wp_nonce_field( 'ssm_source_info_save', 'ssm_source_info_nonce' );

        // Get current values
        $book    = get_post_meta( $post->ID, '_salam_book', true );
        $chapter = get_post_meta( $post->ID, '_salam_chapter', true );
        $line    = get_post_meta( $post->ID, '_salam_line', true );

        // Simulated Book options (in a real app, this would be dynamic)
        $books = array(
            ''      => __( 'Select a Book', SSM_DOMAIN ),
            'book1' => __( 'The Great Book of Wisdom', SSM_DOMAIN ),
            'book2' => __( 'Ancient Teachings', SSM_DOMAIN ),
            'book3' => __( 'Modern Philosophy', SSM_DOMAIN ),
        );

        ?>
        <p>
            <label for="_salam_book"><?php _e( 'Book:', SSM_DOMAIN ); ?></label>
            <select name="_salam_book" id="_salam_book" class="widefat">
                <?php foreach ( $books as $key => $name ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $book ); ?>>
                        <?php echo esc_html( $name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="_salam_chapter"><?php _e( 'Chapter:', SSM_DOMAIN ); ?></label>
            <select name="_salam_chapter" id="_salam_chapter" class="widefat" data-current-chapter="<?php echo esc_attr( $chapter ); ?>">
                <option value=""><?php _e( 'Select Chapter', SSM_DOMAIN ); ?></option>
                </select>
        </p>

        <p>
            <label for="_salam_line"><?php _e( 'Line:', SSM_DOMAIN ); ?></label>
            <input type="text" id="_salam_line" name="_salam_line" value="<?php echo esc_attr( $line ); ?>" class="widefat" />
        </p>
        <script>
            // Note: This script is for demonstration. The actual implementation is in meta-script.js
            // But for the sake of a self-contained example, a helper function is included here.
            jQuery(document).ready(function($) {
                function updateChapterSelect(selectedBook) {
                    var $chapterSelect = $('#_salam_chapter');
                    var currentChapter = $chapterSelect.data('current-chapter');
                    $chapterSelect.empty().append('<option value="">Select Chapter</option>');

                    if (ssm_data.chapters[selectedBook]) {
                        $.each(ssm_data.chapters[selectedBook], function(index, chapter) {
                            $chapterSelect.append($('<option>', {
                                value: chapter,
                                text: 'Chapter ' + chapter,
                                selected: chapter === currentChapter
                            }));
                        });
                    }
                }

                // Initial load
                updateChapterSelect($('#_salam_book').val());

                // On Book change
                $('#_salam_book').on('change', function() {
                    $('#_salam_chapter').data('current-chapter', ''); // Clear current chapter when book changes
                    updateChapterSelect($(this).val());
                });
            });
        </script>
        <?php
    }

    /**
     * Save the custom meta box data.
     */
    public function save_source_info_meta_box( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['ssm_source_info_nonce'] ) ) {
            return $post_id;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['ssm_source_info_nonce'], 'ssm_source_info_save' ) ) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        // Sanitize and save the data.
        $fields = array(
            '_salam_book'    => 'book',
            '_salam_chapter' => 'chapter',
            '_salam_line'    => 'line',
        );

        foreach ( $fields as $meta_key => $field_name ) {
            if ( isset( $_POST[ $meta_key ] ) ) {
                $value = sanitize_text_field( $_POST[ $meta_key ] );
                update_post_meta( $post_id, $meta_key, $value );
            }
        }

        return $post_id;
    }
}
