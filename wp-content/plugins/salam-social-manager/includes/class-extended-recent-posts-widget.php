<?php
/**
 * Custom Widget for Latest Salam Social Posts.
 * Based on the default WordPress Recent Posts Widget.
 */
class SSM_Latest_Posts_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'ssm_latest_posts_widget', // Base ID
            __( 'Latest Salam Social Posts', 'salam-social-manager' ), // Name
            array( 
                'classname' => 'widget_salam_social_posts', 
                'description' => __( 'A list of your most recent Salam Social Posts.', 'salam-social-manager' ),
            ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Salam Social Updates', 'salam-social-manager' );

        /**
         * Filter the widget title.
         *
         * @param string $title    The widget title.
         * @param array  $instance The settings for the particular instance of the widget.
         * @param string $id_base  The widget base ID.
         */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number ) {
            $number = 5;
        }
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        /**
         * Arguments for WP_Query. Crucially sets 'post_type' to 'salam_social'.
         */
        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'post_type'           => 'salam_social', // <--- TARGETS YOUR CPT
        ), $instance ) );

        if ( ! $r->have_posts() ) {
            return;
        }
        ?>

        <?php echo $args['before_widget']; ?>
        <?php if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        } ?>
        <ul>
            <?php while ( $r->have_posts() ) : $r->the_post(); ?>
                <!-- <li>
                    <a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
                    <?php if ( $show_date ) : ?>
                        <span class="post-date"><?php echo get_the_date(); ?></span>
                    <?php endif; ?>
                </li> -->


                <li class="product-category product first">
	<a aria-label="Visit product category Accessories" href="<?php the_permalink(); ?>">
        
    
    
    <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-card-thumbnail">
                                <?php the_post_thumbnail( 'medium' ); // Use 'medium', 'large', or a custom size ?>
                            </div>
                        <?php endif; ?>
    
    
    <h2 class="woocommerce-loop-category__title">
    <?php get_the_title() ? the_title() : the_ID(); ?></h2>
		</a></li>
            <?php endwhile; ?>
        </ul>
        <?php echo $args['after_widget']; ?>
        <?php
        // Restore original Post Data
        wp_reset_postdata();
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'salam-social-manager' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'salam-social-manager' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>

        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?', 'salam-social-manager' ); ?></label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;

        return $instance;
    }
}