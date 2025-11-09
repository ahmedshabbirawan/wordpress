<?php

class SSM_Media_Watermark {

    // Define the custom media category term
    const MEDIA_CATEGORY_SLUG = 'salam-social-media';
    const MEDIA_CATEGORY_NAME = 'Salam Social Media';

    public function __construct() {
        // Register the media category on plugin activation (best practice)
        register_activation_hook( SSM_PLUGIN_PATH . 'salam-social-manager.php', array( $this, 'create_media_category' ) );

        // Hook to automatically assign category on featured image set
        add_action( 'set_post_thumbnail', array( $this, 'assign_media_category_on_set' ), 10, 2 );
        
        // Hook to apply the watermark after the image is fully generated
        add_filter( 'wp_generate_attachment_metadata', array( $this, 'apply_watermark' ), 10, 2 );


    }

    /**
     * Create the "Salam Social Media" term in the 'category' taxonomy for media.
     * We'll use the default category taxonomy for simplicity, or a custom media one if registered.
     */
    public function create_media_category() {
        if ( ! term_exists( self::MEDIA_CATEGORY_SLUG, 'category' ) ) {
            wp_insert_term(
                self::MEDIA_CATEGORY_NAME,
                'category', // Using the default category taxonomy for simplicity in media handling
                array( 'slug' => self::MEDIA_CATEGORY_SLUG )
            );
        }
    }

    /**
     * Check if the image attachment is used as a featured image on a 'salam_social' post,
     * and if so, assign it to the 'Salam Social Media' category.
     */
    public function assign_media_category_on_set( $attachment_id, $post_id ) {
        if ( 'salam_social' === get_post_type( $post_id ) ) {
            $term = get_term_by( 'slug', self::MEDIA_CATEGORY_SLUG, 'category' );
            if ( $term && $term->term_id ) {
                // Set the category for the attachment
                wp_set_object_terms( $attachment_id, array( $term->term_id ), 'category', true );
            }
        }
    }

    /**
     * Apply the watermark to the image attachment if it's in the correct category.
     */
    public function apply_watermark( $metadata, $attachment_id ) {
        // 1. Check if the attachment is in the 'Salam Social Media' category
        // if ( ! has_term( self::MEDIA_CATEGORY_SLUG, 'category', $attachment_id ) ) {
        //     return $metadata;
        // }



        $upload_dir  = wp_upload_dir();
        $full_image_path = $upload_dir['basedir'] . '/' . $metadata['file'];

        // Get watermark settings (from the stretch goal settings page or defaults)
        $watermark_text = get_option( 'ssm_watermark_text', 'Salam Social' );
        
        // Get the dynamically generated, filterable text
        $watermark_text = apply_filters( 'ssm_watermark_text', $watermark_text );

        // 2. Check if the file exists and is writable
        if ( ! file_exists( $full_image_path ) || ! is_writable( $full_image_path ) ) {
          //   return $metadata;
        }

        // 3. Get image details
        $image_info = getimagesize( $full_image_path );
        $mime_type  = $image_info['mime'];
        $image_width = $image_info[0];
        $image_height = $image_info[1];

        // 4. Create image resource based on MIME type
        $image = false;
        if ( 'image/jpeg' === $mime_type || 'image/jpg' === $mime_type ) {
            $image = imagecreatefromjpeg( $full_image_path );
        } elseif ( 'image/png' === $mime_type ) {
            $image = imagecreatefrompng( $full_image_path );
        } elseif ( 'image/gif' === $mime_type ) {
            $image = imagecreatefromgif( $full_image_path );
        }

        if ( ! $image ) {
           // return $metadata; // Could not create image resource
        }
        
        // --- Watermark Application (using GD library) ---

        // Allocate color (e.g., White for visibility)
        $color = imagecolorallocate( $image, 255, 255, 255 ); 
        
        // Settings (Simplified for a standard font/size if GD is used)
        $font_size = 12; // Default font size in pixels
        $padding = 10;   // Padding from the bottom/right edge
        $font_file = SSM_PLUGIN_PATH . 'assets/arial.ttf'; // Use a standard font if available, or a default GD font
        
        if (!file_exists($font_file)) {
             // Fallback to simpler GD font if TTF isn't available
             $font_size = 12; // GD Font size (1-5)
             $text_width = strlen($watermark_text) * imagefontwidth($font_size);
             $text_height = imagefontheight($font_size);
             
             // Calculate position (Bottom-Right)
             $x = $image_width - $text_width - $padding;
             $y = $image_height - $text_height - $padding;
             
             imagestring($image, $font_size, $x, $y, $watermark_text, $color);
        } else {
             // Use TTF font (better quality)
             // Calculate text bounding box to find size
             $bbox = imagettfbbox($font_size, 0, $font_file, $watermark_text);
             $text_width = $bbox[2] - $bbox[0];
             $text_height = $bbox[7] - $bbox[1]; // Height can be calculated differently, but this is a rough estimate
             
             // Calculate position (Bottom-Right with padding)
             $x = $image_width - $text_width - $padding;
             $y = $image_height - $padding; // Y is the baseline for TTF
             
             // Apply the watermark
             imagettftext($image, $font_size, 0, $x, $y, $color, $font_file, $watermark_text);
        }

        // echo $mime_type; exit;


        // 5. Save the watermarked image
        if ( 'image/jpeg' === $mime_type || 'image/jpg' === $mime_type ) {
            imagejpeg( $image, $full_image_path);
        } elseif ( 'image/png' === $mime_type ) {
            imagepng( $image, $full_image_path );
        } elseif ( 'image/gif' === $mime_type ) {
            imagegif( $image, $full_image_path );
        }
        
        imagedestroy( $image );

        // Re-generate metadata for all image sizes to also be watermarked
        // This is a simplified approach. A more robust solution would iterate through $metadata['sizes']
        // and apply the watermark to each resized image. For this example, we only watermark the full size.
        
        return $metadata;
    }
}
